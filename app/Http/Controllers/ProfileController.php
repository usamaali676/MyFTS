<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AvatarService;
use App\Services\ProfileStatsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
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
            if (!$viewer->canViewProfileOf($target)) {
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

    /**
     * Users change only their own photo. The upload is auto-cropped and
     * compressed to <= 100 KB server-side, so any photo size is accepted.
     */
    public function updateAvatar(Request $request, AvatarService $avatars)
    {
        $request->validate([
            'photo' => ['required', 'file', 'max:20480'],
        ], [
            'photo.required' => 'Please choose a photo first.',
            'photo.max' => 'That file is too large (20 MB max).',
        ]);

        $user = Auth::user();

        try {
            $avatars->store($request->file('photo'), $user);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong while saving the photo. Please try a different image.',
            ], 500);
        }

        return response()->json([
            'message' => 'Profile photo updated.',
            'url' => $user->fresh()->avatar_url,
        ]);
    }

    public function removeAvatar(AvatarService $avatars)
    {
        $avatars->remove(Auth::user());

        return response()->json(['message' => 'Profile photo removed.']);
    }
}
