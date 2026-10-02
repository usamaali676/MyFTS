<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Models\User;
use App\Services\ProfileStatsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ProfileController extends Controller
{
    /**
     * Not part of the Permission-row system (like sale.index/settings.index)
     * — access is checked here instead of via PermissionMiddelware, so this
     * doesn't show up as a spurious module in Role Permissions.
     */
    public function show(Request $request, ProfileStatsService $stats, ?int $id = null)
    {
        $viewer = Auth::user();
        $target = $id ? User::findOrFail($id) : $viewer;

        if ($id && (int) $id !== (int) $viewer->id) {
            // Admin/Executives can browse any profile; anyone else who can
            // view the Sale Report can drill into an agent/closer named
            // there, since they already see that person's sales and
            // revenue in that table.
            $canViewOthers = (int) $viewer->role_id === 1
                || optional($viewer->role)->name === 'Executives'
                || GlobalHelper::modulePermission($viewer, 'salereport')->view;

            if (!$canViewOthers) {
                Alert::error('Oops', "You don't have access to this page");
                return redirect()->route('home');
            }
        }

        $rangeKey = $request->query('range', ProfileStatsService::RANGE_ALL);
        $from = $request->query('from');
        $to = $request->query('to');

        $data = $stats->build($target, $rangeKey, $from, $to);

        return view('pages.profile.show', array_merge($data, [
            'target' => $target,
            'isSelf' => (int) $target->id === (int) $viewer->id,
            'rangeKey' => $rangeKey,
            'customFrom' => $from,
            'customTo' => $to,
        ]));
    }
}
