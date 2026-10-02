<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceServiceCharges;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Single source of truth for the revenue figures shown on the Reports
 * dashboard. Replaces SaleReportController's old inline logic, which had
 * two real bugs, not just an outdated UI:
 *
 *  - Development/Marketing revenue was double-counted: an invoice with
 *    both a Development and a Marketing service line got its FULL
 *    total_amount added to both buckets (`array_intersect` against the
 *    invoice's service names).
 *  - "Charged" always means an invoice with at least one payment
 *    (whereHas('payments')) — the same definition SaleVisibilityService
 *    and ProfileStatsService already use, so figures agree across the app.
 *
 * The category split can't just sum each InvoiceServiceCharges line's own
 * charged_price either: those don't always add up to the invoice's real
 * total_amount (a handful of invoices have invoice-level adjustments made
 * after the service lines were entered, so charged_price drifts from what
 * was actually charged). Instead, each invoice's own total_amount is split
 * Development/Marketing in proportion to its lines' category mix — the
 * split always ties out to real revenue, and an invoice with no service
 * lines at all is simply left out of the split (still counted in
 * total/net revenue).
 */
class RevenueCalculatorService
{
    private function chargedInvoices(?Carbon $from, ?Carbon $to): Builder
    {
        $query = Invoice::whereHas('payments');

        if ($from) {
            $query->whereDate('activation_date', '>=', $from->format('Y-m-d'));
        }
        if ($to) {
            $query->whereDate('activation_date', '<=', $to->format('Y-m-d'));
        }

        return $query;
    }

    public function kpis(?Carbon $from, ?Carbon $to): array
    {
        $invoiceTotals = $this->chargedInvoices($from, $to)->pluck('total_amount', 'id');
        $invoiceIds = $invoiceTotals->keys();

        $totalRevenue = (float) $invoiceTotals->sum();

        $chargebackAmount = (float) Invoice::whereIn('id', $invoiceIds)
            ->whereHas('chargeback')
            ->sum('total_amount');

        [$development, $marketing] = $this->categorySplit($invoiceTotals);

        return [
            'total_sales' => $invoiceIds->count(),
            'total_revenue' => $totalRevenue,
            'net_revenue' => $totalRevenue - $chargebackAmount,
            'chargeback_amount' => $chargebackAmount,
            'development_revenue' => $development,
            'marketing_revenue' => $marketing,
        ];
    }

    /**
     * Revenue trend for the chart: bucketed by day when the range is short
     * (<= 45 days) so a "This Month" view still reads as a real trend line,
     * otherwise by month (including for "All Time", where day-buckets
     * would be meaningless).
     */
    public function trend(?Carbon $from, ?Carbon $to): array
    {
        $invoices = $this->chargedInvoices($from, $to)->get(['id', 'activation_date', 'total_amount']);

        if ($invoices->isEmpty()) {
            return ['labels' => [], 'development' => [], 'marketing' => []];
        }

        $useDaily = $from && $to && $from->diffInDays($to) <= 45;
        $bucketFormat = $useDaily ? 'Y-m-d' : 'Y-m';

        $lines = $this->categoryLines($invoices->pluck('id'));
        $buckets = [];

        foreach ($invoices as $invoice) {
            $rows = $lines->get($invoice->id);
            $share = $this->categoryShare($rows, (float) $invoice->total_amount);

            if (!$share) {
                continue;
            }

            $key = Carbon::parse($invoice->activation_date)->format($bucketFormat);
            $buckets[$key]['development'] = ($buckets[$key]['development'] ?? 0) + $share['development'];
            $buckets[$key]['marketing'] = ($buckets[$key]['marketing'] ?? 0) + $share['marketing'];
        }

        ksort($buckets);

        $labels = [];
        $development = [];
        $marketing = [];

        foreach ($buckets as $key => $totals) {
            $labels[] = $useDaily
                ? Carbon::parse($key)->format('M d')
                : Carbon::createFromFormat('Y-m', $key)->format('M Y');
            $development[] = round($totals['development'], 2);
            $marketing[] = round($totals['marketing'], 2);
        }

        return compact('labels', 'development', 'marketing');
    }

    /**
     * The drill-down invoice list, eager-loaded (the legacy code accessed
     * ->sale->lead->saler/closers inside a ->filter()/->map() closure with
     * no eager loading at all, an N+1 query per invoice).
     */
    public function invoiceRows(?Carbon $from, ?Carbon $to): Collection
    {
        return $this->chargedInvoices($from, $to)
            ->with([
                'sale.lead.saler',
                'sale.lead.closers.user',
                'servicecharges.service_name',
            ])
            ->orderByDesc('activation_date')
            ->get()
            ->values()
            ->map(function (Invoice $invoice, int $index) {
                $lead = optional($invoice->sale)->lead;

                return [
                    'sr_no' => $index + 1,
                    'date' => $invoice->activation_date
                        ? Carbon::parse($invoice->activation_date)->format('Y-m-d')
                        : 'N/A',
                    'agent' => $this->firstName(optional($lead?->saler)->name),
                    'agent_id' => $lead?->saler?->id,
                    'closers' => $lead?->closers
                        ->map(fn ($c) => ['id' => $c->user?->id, 'name' => $this->firstName($c->user?->name)])
                        ->values() ?? collect(),
                    'types' => $invoice->servicecharges
                        ->map(fn ($c) => optional($c->service_name)->name ?? 'N/A')
                        ->unique()
                        ->values(),
                    'amount' => (float) $invoice->total_amount,
                ];
            });
    }

    /**
     * User names in this app are stored as "First Last - Employee Name"
     * (e.g. "Michael Sterling - Abu Bakar"); the table only needs the first
     * part — same `explode(' -', ...)[0]` convention SaleReportController
     * already used elsewhere.
     */
    private function firstName(?string $name): string
    {
        if (!$name) {
            return 'N/A';
        }

        return trim(explode(' -', $name)[0]);
    }

    /**
     * @param Collection<int> $invoiceIds
     * @return Collection<int, Collection> service-charge rows grouped by invoice_id
     */
    private function categoryLines(Collection $invoiceIds): Collection
    {
        if ($invoiceIds->isEmpty()) {
            return collect();
        }

        return InvoiceServiceCharges::whereIn('invoice_service_charges.invoice_id', $invoiceIds)
            ->join('company_services', 'company_services.id', '=', 'invoice_service_charges.company_service_id')
            ->select(
                'invoice_service_charges.invoice_id',
                'company_services.category',
                'invoice_service_charges.charged_price'
            )
            ->get()
            ->groupBy('invoice_id');
    }

    /**
     * @return array{development: float, marketing: float}|null null when the
     *         invoice has no service-line data to split by.
     */
    private function categoryShare(?Collection $rows, float $invoiceTotal): ?array
    {
        if (!$rows || $rows->isEmpty()) {
            return null;
        }

        $devRaw = (float) $rows->where('category', 'Development')->sum('charged_price');
        $mktgRaw = (float) $rows->where('category', 'Marketing')->sum('charged_price');
        $rawTotal = $devRaw + $mktgRaw;

        if ($rawTotal <= 0) {
            return null;
        }

        return [
            'development' => $invoiceTotal * ($devRaw / $rawTotal),
            'marketing' => $invoiceTotal * ($mktgRaw / $rawTotal),
        ];
    }

    /**
     * @param Collection<int, float> $invoiceTotals keyed by invoice id
     * @return array{0: float, 1: float} [development, marketing]
     */
    private function categorySplit(Collection $invoiceTotals): array
    {
        if ($invoiceTotals->isEmpty()) {
            return [0.0, 0.0];
        }

        $lines = $this->categoryLines($invoiceTotals->keys());

        $development = 0.0;
        $marketing = 0.0;

        foreach ($invoiceTotals as $invoiceId => $total) {
            $share = $this->categoryShare($lines->get($invoiceId), (float) $total);

            if ($share) {
                $development += $share['development'];
                $marketing += $share['marketing'];
            }
        }

        return [$development, $marketing];
    }
}
