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
            $routeName = Route::currentRouteName();
            // dd($routeName);
            $user = Auth::user();

            $role = Role::where('id', $user->role_id)->first();
            // dd($routeNameMatches);
            if ($routeNameMatches = $this->matchRouteWithPermissionName($routeName)) {
                $perms = Permission::where('role_id', $role->id)->where('name', $routeNameMatches['permissionName'])->first();
                // dd($perms);
                // $hasPermission = $perms->index == 1;
                // dd($hasPermission);
                // dd($routeNameMatches['operation']);

                // if(isset($perms)){
                switch ($routeNameMatches['operation']) {
                    case "index":
                        $hasPermission = $perms->view == 1;
                        break;
                    case 'create':
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
                        // dd($hasPermission);
                        break;
                    default:
                        $hasPermission = false;
                        break;
                }

                // dd($hasPermission);
                if ($hasPermission) {
                    return $next($request);
                } else {
                    Alert::error('Opps',"You can't Perform this Operation");
                    return redirect()->route('home');
                }
            // }
            // else{
            //     Alert::error('Opps',"You can't Perform this Operation");
            //     return redirect()->route('home');
            // }
            } else {
                return $next($request);
            }
        }

        private function matchRouteWithPermissionName($routeName)
        {
            // dd($routeName);
            $matches = [];

            preg_match('/^(?P<entity>[a-z]+)\.(?P<operation>index|view|create|edit|update|delete|conf-delete )$/i', $routeName, $matches);
            // dd($matches);
            if (count($matches) > 2) {
                // dd($matches);
                return [
                    'permissionName' => Str::title($matches['entity']),
                    'operation' => $matches['operation']
                ];
            }
            return false;
        }
}

