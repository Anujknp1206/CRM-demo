<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Hash;
use Auth;

// use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::guard('web')->user();
        $title = $user->name . " :: User";
        $label = "User List";

        // Get user's primary role and its ID (used as level)
        $role = $user->roles->first(); // assuming single role per user
        if (!$role) {
            toast('Your role is not assigned.', 'error');
            abort(403, 'Unauthorized access.');
        }

        $currentRoleId = $role->id;

        // Get all role IDs that are equal or higher level (numerically greater ID = lower in hierarchy)
        $allowedRoleIds = Role::where('id', '>=', $currentRoleId)->pluck('id');

        // Get users who have roles equal or below the logged-in user’s role
        $users = User::whereHas('roles', function ($query) use ($allowedRoleIds) {
            $query->whereIn('id', $allowedRoleIds);
        })
            ->with(['roles', 'creator', 'companies.country'])
            ->get();
        return view('admin.users.index', compact('users', 'title', 'label'));
    }


    public function create()
    {
        $user = Auth::guard('web')->user();
        $title = $user->name . " :: User";
        $label = "Add User";

        // Define assignable roles dynamically based on the user's role
        if ($user->hasRole('Super Admin')) {
            $roles = Role::whereNot('name', 'Super Admin')->get(); // Cannot assign Super Admin
        } elseif ($user->hasRole('Admin')) {
            $roles = Role::whereNotIn('name', ['Super Admin', 'Admin'])->get(); // Can only assign Staff
        } elseif ($user->hasRole('Manager')) {
            $roles = Role::whereNotIn('name', ['Super Admin', 'Admin',])->get(); // Can only assign Staff
        } else {
            toast('Unauthorized access.', 'error');
            abort(403, 'Unauthorized access.');
        }

        return view('admin.users.create', compact('roles', 'title', 'label'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();

        // Define allowed roles dynamically
        $allowedRoles = [];
        if ($user->hasRole('Super Admin')) {
            // Get all roles except Super Admin
            $allowedRoles = Role::whereNot('name', 'Super Admin')->pluck('name')->toArray();
        } elseif ($user->hasRole('Admin')) {
            $allowedRoles = ['Staff', 'Manager'];
        } elseif ($user->hasRole('Manager')) {
            $allowedRoles = ['Staff'];
        } else {
            toast('User cannot create another Same User Role', 'error');
            abort(403, 'Unauthorized access.');
        }

        // Fetch roles from the database to ensure they exist
        $allowedRoles = Role::whereIn('name', $allowedRoles)->pluck('name')->toArray();


        // Validate input
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'joining_date' => 'required',
                'mobile' => 'required|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'role' => 'required|in:' . implode(',', $allowedRoles), // Ensure valid roles
            ],
        );

        if ($validator->fails()) {
            $errors = implode('<br>', $validator->messages()->all());
            Alert::html('Validation Error!', $errors, 'error');
            return redirect()->back()->withInput(); // Keep old input
        }
        //path for photo
        $path_load = config('url.public_path');
        if ($request->hasFile('photo')) {
            $photo1 = $request->file('photo');
            $photo = "user" . rand(100, 999) . time() . '.' . $photo1->getClientOriginalExtension();
            $destinationPath = $path_load . 'user/';
            $photo1->move($destinationPath, $photo);
        } else {
            $photo = "";
        }
        // Create User
        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'core_password' => $request->password,
            'joining_date' => \Carbon\Carbon::createFromFormat('d-m-Y', $request->joining_date)
                ->format('Y-m-d'),

            'address' => $request->address,
            'photo' => $photo,
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);

        // ✅ Assign Role
        $newUser->assignRole($request->role);

        // Debugging Role Assignment

        toast('User created successfully.', 'success');
        return redirect()->route('users.index');
    }
    public function edit($id)
    {
        $user = Auth::guard('web')->user();
        $title = $user->name . " :: User";
        $label = "Update User";

        $targetUser = User::findOrFail($id);

        // 🚫 Prevent user from editing their own role
        if ($user->id == $targetUser->id) {
            toast('You cannot edit your own role.', 'error');
            abort(403, 'Unauthorized action.');
        }
        // Define assignable roles dynamically based on the user's role
        if ($user->hasRole('Super Admin')) {
            $roles = Role::whereNot('name', 'Super Admin')->get();
        } elseif ($user->hasRole('Admin')) {
            $roles = Role::whereNotIn('name', ['Super Admin', 'Admin'])->get();
        } elseif ($user->hasRole('Manager')) {
            $roles = Role::whereNotIn('name', ['Super Admin', 'Admin', 'Manager'])->get();
        } else {
            toast('Unauthorized access.', 'error');
            abort(403, 'Unauthorized access.');
        }

        $users = User::findOrFail($id);
        return view('admin.users.edit', compact('roles', 'title', 'label', 'users'));
    }
    public function update(Request $request, $id)
    {
        $user = Auth::guard('web')->user();

        // Prevent editing own role
        if ($user->id == $id) {
            toast('You cannot update your own role.', 'error');
            return redirect()->back();
        }
        // Define allowed roles dynamically
        $allowedRoles = [];
        if ($user->hasRole('Super Admin')) {
            $allowedRoles = Role::whereNot('name', 'Super Admin')->pluck('name')->toArray();
        } elseif ($user->hasRole('Admin')) {
            $allowedRoles = ['Staff', 'Author'];
        } else {
            toast('User cannot create another Same User Role', 'error');
            abort(403, 'Unauthorized access.');
        }

        // Fetch roles from the database to ensure they exist
        $allowedRoles = Role::whereIn('name', $allowedRoles)->pluck('name')->toArray();

        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'joining_date' => 'required|date',
            'mobile' => 'required',
            'email' => 'required|email',
            'password' => 'nullable|min:6',
            'role' => 'required|in:' . implode(',', $allowedRoles),
        ]);

        if ($validator->fails()) {
            $errors = implode('<br>', $validator->messages()->all());
            Alert::html('Validation Error!', $errors, 'error');
            return redirect()->back()->withInput();
        }

        //path for photo
        $path_load = config('url.public_path');
        if ($request->hasFile('photo')) {
            $photo1 = $request->file('photo');
            $photo = "user" . rand(100, 999) . time() . '.' . $photo1->getClientOriginalExtension();
            $destinationPath = $path_load . 'user/';
            $photo1->move($destinationPath, $photo);
        } else {
            $userData = User::findOrFail($id);
            $photo = $userData->photo;
        }

        // Update User
        User::where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => $request->password ? Hash::make($request->password) : User::find($id)->password, // Only update password if provided
            'core_password' => $request->password ?? User::find($id)->core_password,
            'joining_date' => \Carbon\Carbon::createFromFormat('d-m-Y', $request->joining_date)
                ->format('Y-m-d'),
            'address' => $request->address,
            'photo' => $photo,
            'created_by' => Auth::id(),
        ]);

        // ✅ Fetch updated user
        $updatedUser = User::findOrFail($id);

        // ✅ Assign Role
        $updatedUser->roles()->detach();
        $updatedUser->assignRole($request->role);

        toast('User updated successfully.', 'success');
        return redirect()->route('users.index');
    }
    public function managePermissions($id)
    {
        $authUser = Auth::user();
        $user = User::findOrFail($id);

        $title = $authUser->name . " :: User Permission";
        $label = "Update Permission";
        // 🚫 Block Super Admin
        // if ($user->hasRole('Super Admin')) {
        //     toast('Super Admin permissions cannot be modified.', 'error');
        //     return back();
        // }

        /* ==========================
           SUPER ADMIN
        ===========================*/
        if ($authUser->hasRole('Super Admin')) {

            $permissions = Permission::orderBy('group_name')
                ->get()
                ->groupBy('group_name');

        }
        /* ==========================
           ADMIN
        ===========================*/ elseif ($authUser->hasRole('Admin')) {

            // Permissions via roles + direct permissions
            $rolePermissions = $authUser->getPermissionsViaRoles();
            $directPermissions = $authUser->permissions;

            $permissions = $rolePermissions
                ->merge($directPermissions)
                ->unique('id')
                ->sortBy('group_name')
                ->groupBy('group_name');

        }
        /* ==========================
           UNAUTHORIZED
        ===========================*/ else {
            abort(403, 'Unauthorized access.');
        }

        return view('admin.users.permission', compact(
            'title',
            'user',
            'permissions',
            'label'
        ));
    }

    public function updatePermissions(Request $request, $id)
    {
        $authUser = Auth::user();
        $user = User::findOrFail($id);

        /* ==========================
           ALLOWED PERMISSIONS
        ===========================*/
        if ($authUser->hasRole('Super Admin')) {

            // All permissions
            $allowedPermissions = Permission::pluck('name')->toArray();

        } elseif ($authUser->hasRole('Admin')) {

            // Admin → role + direct permissions
            $rolePermissions = $authUser->getPermissionsViaRoles()->pluck('name');
            $directPermissions = $authUser->permissions->pluck('name');

            $allowedPermissions = $rolePermissions
                ->merge($directPermissions)
                ->unique()
                ->toArray();

        } else {
            abort(403, 'Unauthorized access.');
        }

        /* ==========================
           VALIDATE REQUEST
        ===========================*/
        $requestedPermissions = $request->permissions ?? [];

        // Only allow permissions admin is allowed to assign
        $validPermissions = array_intersect(
            $requestedPermissions,
            $allowedPermissions
        );

        /* ==========================
           SYNC PERMISSIONS
        ===========================*/
        $user->syncPermissions($validPermissions);

        toast('Permissions updated successfully.', 'success');
        return redirect()->route('users.permissions', $id);
    }

    public function deletePhoto($id)
    {
        $path_load = config('url.public_path');
        dd('dsaf');
        $user = User::findOrFail($id);

        if ($user->photo && $user->photo !== 'user.jpeg') {

            $filePath = $path_load . 'user/' . $user->photo;

            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            $user->update(['photo' => null]);

            toast('Picture deleted successfully!', 'success');

        } else {
            toast('User picture not found', 'error');
        }

        return back();
    }
    public function destroy($id)
    {
        $authUser = Auth::user();
        $user = User::findOrFail($id);

        // ❌ Prevent deleting self
        if ($authUser->id === $user->id) {
            toast('You cannot delete your own account.', 'error');
            return back();
        }

        // ❌ Prevent deleting Super Admin
        if ($user->hasRole('Super Admin')) {
            toast('Super Admin cannot be deleted.', 'error');
            return back();
        }

        // ❌ Check if user is used anywhere
        if (
            $user->createdLeads()->exists() ||
            $user->assignedLeads()->exists() ||
            $user->createdQuotations()->exists() ||
            $user->assignedQuotations()->exists() ||
            $user->createdOrders()->exists() ||
            $user->assignedOrders()->exists() ||
            $user->followups()->exists()
        ) {
            // 👉 Instead of delete → deactivate
            $user->update(['status' => 'inactive']);

            toast('User is in use. Account deactivated instead of deleted.', 'warning');
            return redirect()->route('users.index');
        }

        // Safe to delete
        $user->roles()->detach();
        $user->permissions()->detach();

        if ($user->photo) {
            $path = public_path('admin/uploads/user/' . $user->photo);
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        $user->delete();

        toast('User deleted successfully.', 'success');
        return redirect()->route('users.index');
    }

}

