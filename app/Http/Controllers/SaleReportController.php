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
        $role_id = Role::whereIn('name', ['Closer', 'TSR', 'Customer Support'])->get();
        $agent = User::whereIn('role_id', $role_id->pluck('id'))->get();
    //    $role_id = Role::where('name', "TSR","Closer")->first();
    //    $agent = User::where('role_id' , $role_id->id)->get();
       $closer_id = Role::where('name', "Closer")->first();
       $closer = User::where('role_id', $closer_id->id)->get();
     return view('pages.report.index', compact('agent', 'closer'));

   }
   public function show() {
    $invoices = Invoice::whereHas('payments')->whereNotIn('invoice_type', ['Upsell', 'Monthly'])->get();
    // dd($invoices->sale_id);
    $sr = 1;

    // dd($invoices->sale_id);
    $data = $invoices->map(function ($item, $index) {

        $sale = Sale::where('id', $item->sale_id )->first();
        $types = $item->servicecharges;
        $payments = $item->payments;
        // $date = Carbon::parse($item->payments->created_at)->format('Y-m-d');
        // $closer = $sale->lead->closers
        return [

            'id' => $sale->id,
            'sr_no' => $index + 1,
            // 'date' =>  $payments->map(function ($date) {
            //     return Carbon::parse($date->created_at)->format('Y-m-d');
            // }),
            'date' =>   Carbon::parse($item->activation_date)->format('Y-m-d'),
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
    $query = Invoice::whereHas('payments')->whereNotIn('invoice_type', ['Upsell', 'Monthly']);


    // Filter by date range
    if ($request->has('date') && !empty($request->date)) {
        [$start, $end] = explode(' to ', $request->date);
        $query->whereHas('payments', function ($q) use ($start, $end) {
            $q->whereBetween('activation_date', [
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
    // dd($data);

    $data = $filtered->values()->map(function ($item, $index) {
        $sale = $item->sale;
        $types = $item->servicecharges;
        $payments = $item->payments;

        return [
            'id' => $sale->id,
            'sr_no' => $index + 1,
            // 'date' => $payments->map(function ($date) {
            //     return Carbon::parse($date->created_at)->format('Y-m-d');
            // }),
            'date' =>   Carbon::parse($item->activation_date)->format('Y-m-d'),
            'agent' => explode(' -', $sale->lead->saler->name )[0] ?? 'N/A',
            'closer' => $sale->lead->closers->map(function ($closer) {
                return explode(' -', $closer->user->name )[0] ?? 'N/A';
            }) ?? 'N/A',
            'type' => $types->map(function ($items) {
                return $items->service_name->name ?? 'N/A';
            }) ?? 'N/A',
            'comment' => $sale->comment ?? '',
            'price' => $item->total_amount,
            // 'invoice' => $item->invoice_number,
        ];
    });
    // dd($data);

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

public function getstats() {
    $role_id = Role::whereIn('name', ['Closer', 'TSR', 'Customer Support'])->get();
    $agent = User::whereIn('role_id', $role_id->pluck('id'))->get();
//    $role_id = Role::where('name', "TSR","Closer")->first();
//    $agent = User::where('role_id' , $role_id->id)->get();
   $closer_id = Role::where('name', "Closer")->first();
   $closer = User::where('role_id', $closer_id->id)->get();
 return view('pages.report.view', compact('agent', 'closer'));

}

public function stats(Request $request) {

        // dd($request->agent);
        $request->validate([
            'agent' => 'required'
        ]);
        $query = Invoice::whereHas('payments')->whereNotIn('invoice_type', ['Upsell', 'Monthly']);

        // dd($query);
    // Filter by date range
    if ($request->has('date') && !empty($request->date)) {
        [$start, $end] = explode(' to ', $request->date);
        $query->whereHas('payments', function ($q) use ($start, $end) {
            $q->whereBetween('activation_date', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
        });
    }

    $invoices = $query->get();
    // dd($invoices);
    $typeCategories = [
        'Development' => ['Website Development'],
        'Marketing' => ['LandingPage', 'GMB', 'SMM', 'Google Reviews', 'Google Ads Campaign', 'SEO']
    ];

    $filtered = $invoices->filter(function ($invoice) use ($request) {
        $sale = $invoice->sale;

        // dd($sale);
        // Filter by agent


        // dd($sale);
        // Filter by closer
        // if ($request->agent) {
        //     $closerIds = $sale->lead->closers->pluck('closer_id')->toArray();
        //     if (!in_array($request->agent, $closerIds)) {
        //         return false;
        //     }
        // }
        $closerIds = $sale->lead->closers->pluck('closer_id')->toArray();

         if ($request->agent && $sale->lead->saler->id != $request->agent && !in_array($request->agent, $closerIds)) {
            return false;
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
    // dd($filtered);

    $data = $filtered->values()->map(function ($item, $index) {
        $sale = $item->sale;
        $types = $item->servicecharges;
        $payments = $item->payments;

        return [
            'id' => $sale->id,
            'sr_no' => $index + 1,
            // 'date' => $payments->map(function ($date) {
            //     return Carbon::parse($date->created_at)->format('Y-m-d');
            // }),
            'date' =>   Carbon::parse($item->activation_date)->format('Y-m-d'),
            'agent' => explode(' -', $sale->lead->saler->name )[0] ?? 'N/A',
            // 'closer' => $sale->lead->closers->map(function ($closer) {
            //     return explode(' -', $closer->user->name )[0] ?? 'N/A';
            // }) ?? 'N/A',
            'type' => $types->map(function ($items) {
                return $items->service_name->name ?? 'N/A';
            }) ?? 'N/A',
            'comment' => $sale->comment ?? '',
            'price' => $item->total_amount
        ];
    });


    // dd($data);

    // return view('pages.report.report', response()->json([
    //     'labels' => $data->pluck('date')->flatten()->unique()->sort()->values(),
    //     'datasets' => [
    //         'Development' => $data->filter(fn($d) => in_array('Development', $d['type']))
    //                           ->pluck('price'),
    //         'Marketing' => $data->filter(fn($d) => in_array('Marketing', $d['type']))
    //                           ->pluck('price'),
    //     ]
    //     ]));

    // dd($data);

    $developmentServices = ['Website Development'];
    $marketingServices = ['LandingPage', 'GMB', 'SMM', 'Google Reviews', 'Google Ads Campaign', 'SEO'];

    $labels = $data->pluck('date')->flatten()->unique()->sort()->values();

    $developmentItems = $data->filter(function ($d) use ($developmentServices) {
        return collect($d['type'])->intersect($developmentServices)->isNotEmpty();
    });

    $marketingItems = $data->filter(function ($d) use ($marketingServices) {
        return collect($d['type'])->intersect($marketingServices)->isNotEmpty();
    });

    // Get individual price lists
    $development = $developmentItems->pluck('price')->values();
    $marketing = $marketingItems->pluck('price')->values();

    // Calculate combined total
    $total = $development->sum() + $marketing->sum();
    $developmentTotal = $development->sum();
    $marketingTotal = $marketing->sum();


    // Group prices by month (e.g., "2025-05")
$monthlyGroups = $data->flatMap(function ($d) use ($developmentServices, $marketingServices) {
    return collect($d['date'])->map(function ($date) use ($d, $developmentServices, $marketingServices) {
        $month = Carbon::parse($date)->format('Y-m');
        $type = collect($d['type']);

        return [
            'month' => $month,
            'development' => $type->intersect($developmentServices)->isNotEmpty() ? $d['price'] : 0,
            'marketing' => $type->intersect($marketingServices)->isNotEmpty() ? $d['price'] : 0,

        ];
    });
});

// Aggregate sums per month
$monthlySummary = $monthlyGroups->groupBy('month')->map(function ($items) {
    return [
        'development' => $items->sum('development'),
        'marketing' => $items->sum('marketing')
    ];
})->sortKeys();

// Extract labels and data arrays
$monthlyLabels = $monthlySummary->keys()->map(function ($month) {
    return Carbon::parse($month . '-01')->format('M Y'); // e.g., "May 2025"
})->values();

$monthlyDevelopment = $monthlySummary->pluck('development')->values();
$monthlyMarketing = $monthlySummary->pluck('marketing')->values();

    return view('pages.report.report', [
        'labels' => $labels,
        'datasets' => [
            'Development' => $development,
            'Marketing' => $marketing
        ],
        'total' => $total,
        'monthlyLabels' => $monthlyLabels,
        'monthlyDev' => $monthlyDevelopment,
        'monthlyMarketing' => $monthlyMarketing,
        'developmentTotal' => $developmentTotal,
        'marketingTotal' => $marketingTotal
    ]);

    // $development = $data->filter(function ($d) use ($developmentServices) {
    //     return collect($d['type'])->intersect($developmentServices)->isNotEmpty();
    // })->pluck('price')->values();

    // $marketing = $data->filter(function ($d) use ($marketingServices) {
    //     return collect($d['type'])->intersect($marketingServices)->isNotEmpty();
    // })->pluck('price')->values();

    // return view('pages.report.report', [
    //     'labels' => $labels,
    //     'datasets' => [
    //         'Development' => $development,
    //         'Marketing' => $marketing
    //     ]
    // ]);
    // return view('pages.report.report', [
    //     'labels' => $data->pluck('date')->flatten()->unique()->sort()->values(),
    //     'datasets' => [
    //         'Development' => $data->filter(fn($d) => in_array('Development', $d['type']->toArray()))
    //                             ->pluck('price')->values(),
    //         'Marketing' => $data->filter(fn($d) => in_array('Marketing', $d['type']->toArray()))
    //                             ->pluck('price')->values(),
    //     ]

    // ]);

}

public function update(){


    $roleIds = Role::where('name', "Customer Support")->pluck('id');

    $users = User::whereIn('role_id', $roleIds)->orwhere('id', 14)->get();

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
    // dd($salesWithPayments);

//     $roleIds = Role::where('name', "Customer Support")->pluck('id');

// $users = User::whereIn('role_id', $roleIds)->get();

// $data = [];

// foreach ($users as $user) {
//     $sales = Sale::whereHas('Customer_support', function ($query) use ($user) {
//         $query->where('cs_id', $user->id);
//     });

//     $allSale = (clone $sales)->count();
//     $activeSalesCount = (clone $sales)->where('status', 1)->count();
//     $inactiveSalesCount = (clone $sales)->where('status', 0)->count();
//     $currentMonth = Carbon::now()->format('M Y');
//     $pendingPayment = (clone $sales)->whereHas('invoice', function ($query) use ($user){
//         $query->whereIn('month', $currentMonth)
//         ->whereNotHas('payments');
//     })->count();


//     $data[] = [
//         'user_id' => $user->id,
//         'user_name' => $user->name,
//         'total_sale' => $allSale,
//         'active_sales' => $activeSalesCount,
//         'inactive_sales' => $inactiveSalesCount,
//     ];
// }

// Optionally return or dump $data
// dd($data);


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


