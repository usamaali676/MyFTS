<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use App\Models\Role;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;
use RealRashid\SweetAlert\Facades\Alert;
use Tests\TestCase;
use Illuminate\Support\Str;



class PermissionMiddelware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
        //     dd('AJAX request detected');
        // }

        $routeName = Route::currentRouteName();
        // dd($routeName);
        $user = Auth::user();
        // dd($user);
        // Ensure a user is authenticated before checking their permissions
        if ($user) {
            $role = Role::where('id', $user->role_id)->first();
            //  dd($role);
            // If route matches permission pattern
            if ($routeNameMatches = $this->matchRouteWithPermissionName($routeName)) {
                // Fetch permission for the role
                $perms = Permission::where('role_id', $role->id)
                                   ->where('name', $routeNameMatches['permissionName'])
                                   ->first();
                // dd($perms);

                if ($perms) {
                    // Check permission based on operation
                    switch ($routeNameMatches['operation']) {
                        case "index":
                            $hasPermission = $perms->view == 1;
                            break;
                        case 'create':
                            $hasPermission = $perms->create == 1;
                            break;
                        case 'store':
                            $hasPermission = $perms->create == 1;
                            break;
                        case 'edit':
                            $hasPermission = $perms->edit == 1;
                            break;
                        case 'update':
                            $hasPermission = $perms->edit == 1;
                            break;
                        case 'conf-delete':
                            $hasPermission = $perms->delete == 1;
                            break;
                        case 'delete':
                            $hasPermission = $perms->delete == 1;
                            break;
                        default:
                            $hasPermission = false;
                            break;
                    }

                    // If the user has permission, proceed to the next request
                    if ($hasPermission) {
                        return $next($request);
                    } else {
                        // Handle error for unauthorized request
                        if ($request->ajax()) {
                            return response()->json(['error' => "You can't perform this operation"], 403);
                        } else {
                            Alert::error('Opps', "You can't perform this operation");
                            return redirect()->route('home');
                        }
                    }
                }
                else{
                    if ($request->ajax()) {
                        return response()->json(['error' => "You can't perform this operation"], 422);
                    } else {
                        Alert::error('Opps', "You can't perform this operation");
                        return redirect()->route('home');
                    }
                }
            }
        }

        // If no matching route or user does not have necessary permissions, continue the request
        return $next($request);
    }

    private function matchRouteWithPermissionName($routeName)
    {
        $matches = [];
        // dd($matches);
       preg_match('/^(?P<entity>[a-z]+)\.(?P<operation>index|view|create|store|edit|update|delete|conf-delete)$/i', $routeName, $matches);


        if (count($matches) > 2) {
            return [
                'permissionName' => Str::title($matches['entity']),
                'operation' => $matches['operation']
            ];
        }

        return false;
    }

}

