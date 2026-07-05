<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Http\Controllers\Controller;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        
        return view('roles.index', compact('roles', 'permissions'));
    }

    public function assignPermission(Request $request)
    {
        $role = Role::find($request->role_id);
        $role->syncPermissions($request->permissions);
        return redirect()->back()->with('success', 'Permissions updated successfully');
    }

    public function assignRole(Request $request)
    {
        $user = User::find($request->user_id);
        $user->syncRoles($request->role);
        return redirect()->back()->with('success', 'Role assigned successfully');
    }
}

