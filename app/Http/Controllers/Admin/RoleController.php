<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    
    public function index()
    {
        $user=Auth::user();
        $title=$user->name. " :: Role";
        $label="Role List";
        // Super Admin sees all roles, Vendor sees only vendor roles
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            $roles = Role::all();
        } else {
            $roles = Role::where('name', 'LIKE', 'Vendor Staff%')->get();
        }

        return view('admin.roles.index', compact('roles','label','title'));
    }

    public function create()
    {
        $user=Auth::user();
        $title=$user->name. " :: Role";
        $label="Role Add";
        return view('admin.roles.create',compact('title','label'));
    }

    public function store(Request $request)
    {
        $role = Role::create(['name' => ucfirst($request->name)]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }
        toast('Role created successfully','success');
        return redirect()->route('roles.index');
    }


    public function edit($id)
    {
        $user=Auth::user();
        $title=$user->name. " :: Role";
        $label="Role Update";
        $role = Role::findOrFail($id);
        return view('admin.roles.edit', compact('role','title','label'));
    }

    public function update(Request $request, $id)
    {
 
        $role = Role::findOrFail($id);
        $role->update(['name' => ucfirst($request->name)]);
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }
        toast('Role updated successfully','success');
        return redirect()->route('roles.index');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        toast('Role deleted successfully!','success');
        return redirect()->route('roles.index');
    }
}
