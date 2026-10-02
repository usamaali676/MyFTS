<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use App\Services\DateRangeResolver;
use App\Services\RevenueCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaleReportController extends Controller
{
    /**
     * The unified Sale Report dashboard: company-wide KPI tiles, a
     * Development/Marketing revenue trend chart, and a drill-down invoice
     * list — replaces the old separate Sale Report + Yearly Report pages.
     * Per-agent detail lives on the Profile page instead of a filter here.
     */
    public function index(Request $request, RevenueCalculatorService $revenue)
    {
        $rangeKey = $request->query('range', DateRangeResolver::RANGE_ALL);
        $range = DateRangeResolver::resolve($rangeKey, $request->query('from'), $request->query('to'));

        return view('pages.report.index', [
            'range' => $range,
            'rangeKey' => $rangeKey,
            'customFrom' => $request->query('from'),
            'customTo' => $request->query('to'),
            'kpis' => $revenue->kpis($range['from'], $range['to']),
            'trend' => $revenue->trend($range['from'], $range['to']),
            'invoices' => $revenue->invoiceRows($range['from'], $range['to']),
        ]);
    }

    public function update(){


        $roleIds = Role::where('name', "Customer Support")->pluck('id');

        $users = User::whereIn('role_id', $roleIds)->get();

        $data = [];

        $currentMonth = Carbon::now()->format('M Y'); // e.g., "May 2025"
        // $currentMonth = Carbon::now()->subMonth()->format('M Y');

        // dd($previousMonth);

        foreach ($users as $user) {
            $salesQuery = Sale::whereHas('Customer_support', function ($query) use ($user) {
                $query->where('cs_id', $user->id);
            });
            // dd($salesQuery);

            $allSale = (clone $salesQuery)->count();
            $activeSalesCount = (clone $salesQuery)->where('status', 1)->count();
            $inactiveSalesCount = (clone $salesQuery)->where('status', 0)->count();

            $ChargedPayment = (clone $salesQuery)->whereHas('invoice', function ($query) use ($currentMonth) {
                $query->where('month', $currentMonth)
                    ->where('invoice_type', "Monthly")
                      ->whereHas('payments');
            })->count();

            // echo $ChargedPayment;

            $Chargedamount = 0;

            // dd($salesQuery);

            $salesWithPayments = (clone $salesQuery)->whereHas('invoice', function ($query) use ($currentMonth) {
                $query->where('month', $currentMonth)
                ->where('invoice_type', "Monthly")
                      ->whereHas('payments');
            })->with(['invoice.payments'])->get();

            // return $salesWithPayments ;
            // echo $salesWithPayments;

            // dd( $salesWithPayments);

            foreach ($salesWithPayments as $sale) {
                foreach ($sale->invoice as $invoice) {
                    if($invoice->invoice_type == "Monthly" && $invoice->month == $currentMonth){
                        foreach ($invoice->payments as $payment) {
                            $Chargedamount += $payment->amount;
                        }
                    }
                }
            }

            $upsellcount = (clone $salesQuery)->whereHas('invoice', function ($query) use ($currentMonth) {
                $query->where('month', $currentMonth)
                    ->where('invoice_type', "UpSell")
                      ->whereHas('payments');
            })->count();

            // echo $upsellcount;

            $upsellamount = 0;

            // dd($salesQuery);

            $upsellWithPayments = (clone $salesQuery)->whereHas('invoice', function ($query) use ($currentMonth) {
                $query->where('month', $currentMonth)
                ->where('invoice_type', "UpSell")
                      ->whereHas('payments');
            })->with(['invoice.payments'])->get();

            foreach ($upsellWithPayments as $sale) {
                foreach ($sale->invoice as $invoice) {
                    if($invoice->invoice_type == "UpSell" && $invoice->month == $currentMonth){
                        foreach ($invoice->payments as $payment) {
                            $upsellamount += $payment->amount;
                        }
                    }
                }
            }

            $grandtotal = $upsellamount +  $Chargedamount;


            $data[] = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'total_sale' => $allSale,
                'active_sales' => $activeSalesCount,
                'inactive_sales' => $inactiveSalesCount,
                'charged_payment_sales' => $ChargedPayment,
                'pending_payment_sales' => $activeSalesCount - $ChargedPayment,
                'total_revenue' => $Chargedamount,
                'upsellcount' => $upsellcount,
                'upsellamount' => $upsellamount,
                'grandtotal' => $grandtotal,
            ];
        }

        return view('pages.report.support', compact('data'));
    }

    public function reportsupport(Request $request){
        // dd($request->all);
        $request->validate([
            'month' =>  'required',
        ]);
        $roleIds = Role::where('name', "Customer Support")->pluck('id');

        $users = User::whereIn('role_id', $roleIds)->get();

        $data = [];

        if($request->has('month')){
            $currentMonth = $request->month;
        }
        else{

            $currentMonth = Carbon::now()->format('M Y'); // e.g., "May 2025"
        }
        foreach ($users as $user) {
            $salesQuery = Sale::whereHas('Customer_support', function ($query) use ($user) {
                $query->where('cs_id', $user->id);
            });

            $allSale = (clone $salesQuery)->count();
            $activeSalesCount = (clone $salesQuery)->where('status', 1)->count();
            $inactiveSalesCount = (clone $salesQuery)->where('status', 0)->count();

            $ChargedPayment = (clone $salesQuery)->whereHas('invoice', function ($query) use ($currentMonth) {
                $query->where('month', $currentMonth)
                ->where('invoice_type', "Monthly")
                      ->whereHas('payments');
            })->count();
            $Chargedamount = 0;

            $salesWithPayments = (clone $salesQuery)->whereHas('invoice', function ($query) use ($currentMonth) {
                $query->where('month', $currentMonth)
                ->where('invoice_type', "Monthly")
                      ->whereHas('payments');
            })->with(['invoice.payments'])->get();

            // dd($salesWithPayments);

            foreach ($salesWithPayments as $sale) {
                foreach ($sale->invoice as $invoice) {
                    if($invoice->invoice_type == "Monthly" && $invoice->month == $currentMonth){
                        foreach ($invoice->payments as $payment) {
                            $Chargedamount += $payment->amount;
                        }
                    }
                }
            }

            $upsellcount = (clone $salesQuery)->whereHas('invoice', function ($query) use ($currentMonth) {
                $query->where('month', $currentMonth)
                    ->where('invoice_type', "UpSell")
                      ->whereHas('payments');
            })->count();

            // echo $upsellcount;

            $upsellamount = 0;

            // dd($salesQuery);

            $upsellWithPayments = (clone $salesQuery)->whereHas('invoice', function ($query) use ($currentMonth) {
                $query->where('month', $currentMonth)
                ->where('invoice_type', "UpSell")
                      ->whereHas('payments');
            })->with(['invoice.payments'])->get();

            foreach ($upsellWithPayments as $sale) {
                foreach ($sale->invoice as $invoice) {
                    if($invoice->invoice_type == "UpSell" && $invoice->month == $currentMonth){
                        foreach ($invoice->payments as $payment) {
                            $upsellamount += $payment->amount;
                        }
                    }
                }
            }

            $grandtotal = $upsellamount +  $Chargedamount;



            $data[] = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'total_sale' => $allSale,
                'active_sales' => $activeSalesCount,
                'inactive_sales' => $inactiveSalesCount,
                'charged_payment_sales' => $ChargedPayment,
                'pending_payment_sales' => $activeSalesCount - $ChargedPayment,
                'total_revenue' => $Chargedamount,
                'upsellcount' => $upsellcount,
                'upsellamount' => $upsellamount,
                'grandtotal' => $grandtotal,
            ];
        }
        return response()->json([
            'data' => $data,
        ]);
    }

}
