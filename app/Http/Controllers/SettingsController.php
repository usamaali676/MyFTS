<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Models\BankAccount;
use App\Models\BusinessCategory;
use App\Models\Cashapp;
use App\Models\CompanyServices;
use App\Models\Holidays;
use App\Models\MerchantAccount;
use App\Models\SubCategory;
use App\Models\ZelleAccount;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class SettingsController extends Controller
{
    /**
     * The hub landing page for all Settings CRUDs. Not part of the
     * Permission-row system (see PermissionMiddelware's ignore list) —
     * each card below is individually gated on its own module's view
     * permission instead of the page needing one of its own.
     */
    public function index()
    {
        $user = Auth::user();

        $modules = [
            'bankaccount' => ['route' => 'bankaccount.index', 'label' => 'Bank Accounts', 'icon' => 'mdi-bank', 'count' => BankAccount::count()],
            'businesscategory' => ['route' => 'businesscategory.index', 'label' => 'Business Categories', 'icon' => 'mdi-shape-outline', 'count' => BusinessCategory::count()],
            'subcategory' => ['route' => 'subcategory.index', 'label' => 'Sub Categories', 'icon' => 'mdi-sitemap-outline', 'count' => SubCategory::count()],
            'cashapp' => ['route' => 'cashapp.index', 'label' => 'CashApp Accounts', 'icon' => 'mdi-cash-fast', 'count' => Cashapp::count()],
            'companyservice' => ['route' => 'companyservice.index', 'label' => 'Company Services', 'icon' => 'mdi-briefcase-variant-outline', 'count' => CompanyServices::count()],
            'holiday' => ['route' => 'holiday.index', 'label' => 'Holidays', 'icon' => 'mdi-calendar-star', 'count' => Holidays::count()],
            'merchantaccount' => ['route' => 'merchantaccount.index', 'label' => 'Merchant Accounts', 'icon' => 'mdi-store-outline', 'count' => MerchantAccount::count()],
            'zelleaccount' => ['route' => 'zelleaccount.index', 'label' => 'Zelle Accounts', 'icon' => 'mdi-bank-transfer', 'count' => ZelleAccount::count()],
        ];

        $cards = [];
        foreach ($modules as $key => $module) {
            $perm = GlobalHelper::modulePermission($user, $key);
            if ($perm->view) {
                $cards[] = $module;
            }
        }

        if (empty($cards)) {
            Alert::error('Oops', "You don't have access to this page");
            return redirect()->route('home');
        }

        return view('pages.settings.index', compact('cards'));
    }
}
