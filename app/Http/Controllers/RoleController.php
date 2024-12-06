<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = GlobalHelper::Permissions();
        // dd($permissions->name);
        $role = Role::all();
        $roles = Role::withCount('users')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
        return view('pages.role.index', compact('permissions', 'role', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'modalRoleName' => 'required|string|max:255|unique:roles,name',
        ]);

        // Create the role
        $role = Role::create([
            'name' => $request->modalRoleName,
            'created_by' => Auth::user()->id,
        ]);

        // Save permissions
        if(isset($request->permissions)){
            foreach ($request->permissions as $permName => $permData) {
                Permission::create([
                    'role_id' => $role->id,
                    'name' => $permName,
                    'create' => isset($permData['create']),
                    'view' => isset($permData['view']),
                    'edit' => isset($permData['edit']),
                    'delete' => isset($permData['delete']),
                ]);
            }
        }
        return redirect()->back();


    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            return redirect()->route('role.index')->with('error', 'Role not found.');
        }

        // Get unique permissions from routes
        $permissions = GlobalHelper::Permissions();

        // Create an array to hold the role's permissions for easy access
        $rolePermissions = [];
        foreach ($role->permissions as $permission) {
            $rolePermissions[$permission->name] = $permission; // Use the permission's name as the key
        }

        return view('pages.role.edit', compact('role', 'permissions', 'rolePermissions'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
            // Validate the incoming request
            // dd($request->all());
            $request->validate([
                'modalRoleName' => 'required|string|max:255',
            ]);

            // Find the role
            $role = Role::findOrFail($id);

            // Update the role name
            $role->name = $request->modalRoleName;
            $role->updated_by = Auth::user()->id;
            $role->save();

            // Update permissions
            $permissions  = GlobalHelper::Permissions();
            foreach ($permissions as $permName) {
                $permData = $request->permissions[$permName] ?? [
                    'create' => 0,
                    'view' => 0,
                    'edit' => 0,
                    'delete' => 0,
                ];
                // dd($permData['edit']);

                // Update or create the permission
                $permission = Permission::where('role_id', $role->id)->where('name', $permName)->first();
                // dd($permission->edit);
                if ($permission) {
                    $permission->create =   (isset($permData['create']) && $permData['create'] == 'on') ? 1 : 0;  // Convert 'on' to 1, otherwise 0
                    $permission->view =     (isset($permData['view']) && $permData['view'] == 'on') ? 1 : 0;    // Convert 'on' to 1, otherwise 0
                    $permission->edit =  (isset($permData['edit']) && $permData['edit'] == 'on') ? 1 : 0;      // Convert 'on' to 1, otherwise 0
                    $permission->delete = (isset($permData['delete']) && $permData['delete'] == 'on') ? 1 : 0;   // Convert 'on' to 1, otherwise 0
                    $permission->save();
                } else {
                    Permission::create([
                        'role_id' => $role->id,
                        'name' => $permName,
                        'create' => $permData['create'] ? 1 : 0,
                        'view' => $permData['view'] ? 1 : 0,
                        'edit' => $permData['edit'] ? 1 : 0,
                        'delete' => $permData['delete'] ? 1 : 0,
                    ]);
                }
            }

            // if(isset($request->permissions)){
            //     foreach ($request->permissions as $permName => $permData) {
            //         $permission = Permission::where('role_id', $role->id)->where('name', $permName)->first();
            //         if ($permission) {
            //             $permission->create = isset($permData['create']);
            //             $permission->view = isset($permData['view']);
            //             $permission->edit = isset($permData['edit']);
            //             $permission->delete = isset($permData['delete']);
            //             $permission->save();
            //         } else {
            //             // If the permission does not exist, you might want to create it (optional)
            //             Permission::create([
            //                 'role_id' => $role->id,
            //                 'name' => $permName,
            //                 'create' => isset($permData['create']),
            //                 'view' => isset($permData['view']),
            //                 'edit' => isset($permData['edit']),
            //                 'delete' => isset($permData['delete']),
            //             ]);
            //         }
            //     }
            // }
            Alert::success('success', "Role Update Successfully");
            return redirect()->route('role.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        // dd($role);
        if ($role->id == 1) {
            Alert::error('error', 'Cannot delete the admin role');
            return redirect()->route('role.index');
        }
        else {
            $users = User::where('role_id', $role->id)->get();
            if($users->count() > 0){
                Alert::error('error', 'Cannot delete this role as it is assigned to '.$users->count().' user(s).');
                return redirect()->route('role.index');
            }
            else{
                $role->deleted_by = Auth::user()->id;
                $role->save();
                $role->delete();
                Alert::success('success', 'Role deleted successfully');
                return redirect()->route('role.index');
            }
        }
    }
}
