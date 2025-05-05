<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaleReportController extends Controller
{
   public function index() {
        $role_id = Role::whereIn('name', ['Closer', 'TSR'])->get();
        $agent = User::whereIn('role_id', $role_id->pluck('id'))->get();
    //    $role_id = Role::where('name', "TSR","Closer")->first();
    //    $agent = User::where('role_id' , $role_id->id)->get();
       $closer_id = Role::where('name', "Closer")->first();
       $closer = User::where('role_id', $closer_id->id)->get();
     return view('pages.report.index', compact('agent', 'closer'));

   }
   public function show() {
    $invoices = Invoice::whereHas('payments')->get();
    $sr = 1;

    // dd($invoices);
    $data = $invoices->map(function ($item, $index) {

        $sale = Sale::where('id', $item->sale_id )->first();
        $types = $item->servicecharges;
        $payments = $item->payments;
        // $date = Carbon::parse($item->payments->created_at)->format('Y-m-d');
        // $closer = $sale->lead->closers
        return [

            'id' => $sale->id,
            'sr_no' => $index + 1,
            'date' =>  $payments->map(function ($date) {
                return Carbon::parse($date->created_at)->format('Y-m-d');
            }),
            'agent' => explode(' -',  $sale->lead->saler->name  )[0] ?? 'N/A',
            // 'closer' => $sale->lead->closers ?? 'N/A',
            'closer' => $sale->lead->closers->map(function ($closer) {
                return explode(' -', $closer->user->name )[0] ?? 'N/A';  // Assuming 'closer' model has 'name' field
            }) ?? 'N/A',
            'type' => $types->map(function ($items) {
                return $items->service_name->name ?? 'N/A';  // Assuming 'closer' model has 'name' field
            }) ?? 'N/A',
            'comment' => $sale->comment ?? '',
            'price' => $item->total_amount
        ];
    });

    return response()->json(['data' => $data]);
}

public function filterData(Request $request)
{

    // dd($request->all());
    $query = Invoice::whereHas('payments');


    // Filter by date range
    if ($request->has('date') && !empty($request->date)) {
        [$start, $end] = explode(' to ', $request->date);
        $query->whereHas('payments', function ($q) use ($start, $end) {
            $q->whereBetween('created_at', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
        });
    }

    $invoices = $query->get();
    $typeCategories = [
        'Development' => ['Website Development'],
        'Marketing' => ['LandingPage', 'GMB', 'SMM', 'Google Reviews', 'Google Ads Campaign', 'SEO']
    ];

    $filtered = $invoices->filter(function ($invoice) use ($request) {
        $sale = $invoice->sale;

        // Filter by agent
        if ($request->agent && $sale->lead->saler->id != $request->agent) {
            return false;
        }

        // Filter by closer
        if ($request->closer) {
            $closerIds = $sale->lead->closers->pluck('closer_id')->toArray();
            if (!in_array($request->closer, $closerIds)) {
                return false;
            }
        }
        $typeCategories = [
            'Development' => ['Website Development'],
            'Marketing' => ['LandingPage', 'GMB', 'SMM', 'Google Reviews', 'Google Ads Campaign', 'SEO']
        ];

        if ($request->type) {
            $types = $invoice->servicecharges->pluck('service_name.name')->toArray();

            // Match current types against defined category
            $categoryServices = $typeCategories[$request->type] ?? [];

            // If none of the invoice's types match the selected category, skip it
            $hasMatch = false;
            foreach ($types as $typeName) {
                if (in_array($typeName, $categoryServices)) {
                    $hasMatch = true;
                    break;
                }
            }

            if (!$hasMatch) {
                return false; // Exclude this invoice from the results
            }
        }




        return true;
    });

    $data = $filtered->values()->map(function ($item, $index) {
        $sale = $item->sale;
        $types = $item->servicecharges;
        $payments = $item->payments;

        return [
            'id' => $sale->id,
            'sr_no' => $index + 1,
            'date' => $payments->map(function ($date) {
                return Carbon::parse($date->created_at)->format('Y-m-d');
            }),
            'agent' => explode(' -', $sale->lead->saler->name )[0] ?? 'N/A',
            'closer' => $sale->lead->closers->map(function ($closer) {
                return explode(' -', $closer->user->name )[0] ?? 'N/A';
            }) ?? 'N/A',
            'type' => $types->map(function ($items) {
                return $items->service_name->name ?? 'N/A';
            }) ?? 'N/A',
            'comment' => $sale->comment ?? '',
            'price' => $item->total_amount
        ];
    });

    // Revenue calculations
    $devRevenue = 0;
    $marketingRevenue = 0;
    $totalRevenue = 0;
    $chargeBackAmount = 0;

    // foreach ($filtered as $invoice) {
    //     $types = $invoice->servicecharges->pluck('service_name.name')->toArray();
    //     $totalAmount = $invoice->total_amount;
    //     $serviceCount = count($types);

    //     if ($serviceCount === 0) continue; // Avoid division by zero

    //     // Per service revenue allocation
    //     $amountPerService = $totalAmount / $serviceCount;

    //     // foreach ($types as $typeName) {
    //     //     if (in_array($typeName, $typeCategories['Development'])) {
    //     //         $devRevenue += $amountPerService;
    //     //     }
    //     //     if (in_array($typeName, $typeCategories['Marketing'])) {
    //     //         $marketingRevenue += $amountPerService;
    //     //     }
    //     // }
    //     $isDevelopment = collect($types)->every(fn($type) => in_array($type, $typeCategories['Development']));
    //     $isMarketing = collect($types)->every(fn($type) => in_array($type, $typeCategories['Marketing']));

    //     if ($isDevelopment) {
    //         $devRevenue += $totalAmount;
    //     }

    //     if ($isMarketing) {
    //         $marketingRevenue += $totalAmount;
    //     }

    //     $totalRevenue += $totalAmount;

    //     if ($invoice->chargeback) {
    //         $chargeBackAmount += $totalAmount;
    //     }
    // }

    foreach ($filtered as $invoice) {
        $types = $invoice->servicecharges->pluck('service_name.name')->toArray();
        $amount = $invoice->total_amount;
        // $amount = 0;

        if (array_intersect($types, $typeCategories['Development'])) {
            $devRevenue += $amount;
        }

        if (array_intersect($types, $typeCategories['Marketing'])) {
            $marketingRevenue += $amount;
            // dd($marketingRevenue);
        }


        $totalRevenue += $amount;

        if ($invoice->chargeback) {
            $chargeBackAmount += $amount;
        }
                // dd($devRevenue);

    }
    // dd($devRevenue);



//     $devRevenue = 0;
// $marketingRevenue = 0;
// $totalRevenue = 0;
// $chargeBackAmount = 0;

// foreach ($filtered as $invoice) {
//     $types = $invoice->servicecharges->pluck('service_name.name')->toArray();
//     $totalAmount = $invoice->total_amount;

//     $devTypes = array_intersect($types, $typeCategories['Development']);
//     $mktgTypes = array_intersect($types, $typeCategories['Marketing']);

//     $devCount = count($devTypes);
//     $mktgCount = count($mktgTypes);
//     $totalTypeCount = $devCount + $mktgCount;

//     if ($totalTypeCount > 0) {
//         $amountPerType = $totalAmount / $totalTypeCount;

//         $devRevenue += $amountPerType * $devCount;
//         $marketingRevenue += $amountPerType * $mktgCount;
//     }

//     $totalRevenue += $totalAmount;

//     if ($invoice->chargeback) {
//         $chargeBackAmount += $totalAmount;
//     }
// }


    return response()->json([
        'data' => $data,
        'summary' => [
            'development' => $devRevenue,
            'marketing' => $marketingRevenue,
            'chargeback' => $chargeBackAmount,
            'total' => $totalRevenue - $chargeBackAmount
        ]
    ]);

    // return response()->json(['data' => $data]);
}

}
