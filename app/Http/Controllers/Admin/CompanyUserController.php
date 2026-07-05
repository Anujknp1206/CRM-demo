<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class CompanyUserController extends Controller
{
    // Show list of assigned users for a company
    public function index($company_id)
    {
        $company = Company::with('users')->findOrFail($company_id);
        $users = User::all();
        $title = 'Assign Users';
        $label = 'Assign Users to ' . $company->company_name;
        return view('admin.companies.assign-users', compact('company', 'users', 'title', 'label'));
    }

    // Assign users to company
    public function store(Request $request, $company_id)
    {
        $company = Company::findOrFail($company_id);

        $newUserIds = $request->user_ids ?? [];

        // Get already assigned users
        $existingUserIds = $company->users()->pluck('users.id')->toArray();

        // Merge old + new
        $finalUserIds = array_unique(array_merge($existingUserIds, $newUserIds));

        // Sync merged list
        $company->users()->sync($finalUserIds);

        $names = $company->users->pluck('name')->implode(', ');

        notifyAdmins(
            'User Assigned to Company',
            "$names assigned to {$company->company_name}",
            route('company.assignUsers', $company->id),
            'warning'
        );

        toast('Users added successfully!', 'success');
        return back();
    }

    // Remove one assigned user
    public function removeUser($company_id, $user_id)
    {
        $company = Company::findOrFail($company_id);
        $company->users()->detach($user_id);
        $user = User::findOrFail($user_id);
        notifyAdmins(
            'User Removed from Company',
            "{$user->name} removed from {$company->company_name}",
            route('company.assignUsers', $company->id),
            'danger'
        );

        toast('User removed successfully!', 'success');
        return back();
    }
}
