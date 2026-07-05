<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Auth;

class PermissionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        abort_if(!$user, 403);

        $title = $user->name . " :: Permissions";
        $label = "Permission List";

        $permissions = Permission::query()
            ->orderBy('group_name')
            ->orderBy('name')
            ->get()
            ->groupBy('group_name');

        return view('admin.permission.index', compact(
            'permissions',
            'title',
            'label'
        ));
    }


    public function create()
    {
        $user = Auth::user();
        $title = $user->name . " :: Add Permissions";
        $label = "Add Permission";

        $groups = Permission::select('group_name')
            ->whereNotNull('group_name')
            ->distinct()
            ->orderBy('group_name')
            ->pluck('group_name');

        return view('admin.permission.create', compact('title', 'label', 'groups'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $title = $user->name . " :: Update Permission";
        $label = "Update Permission";

        $permission = Permission::findOrFail($id);

        $groups = Permission::select('group_name')
            ->whereNotNull('group_name')
            ->distinct()
            ->orderBy('group_name')
            ->pluck('group_name');

        return view('admin.permission.edit', compact('title', 'label', 'permission', 'groups'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
            'group_name' => 'required|string|max:255',
        ]);

        // Create the permission only once
        Permission::create([
            'name' => $request->name,
            'group_name' => $request->group_name,
            'guard_name' => 'web',
        ]);
        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $id,
            'group_name' => 'required|string|max:255',
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);
        $permission->save();

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }
}

