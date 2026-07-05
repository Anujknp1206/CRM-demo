<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Company;
use App\Models\Machine;
use App\Models\Component;
use App\Models\Lead;
use App\Models\Quotation;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $title = $user->name . " :: Dashboard";

        // Default empty summary
        $summary = [
            'users' => User::count(),
            'companies' => Company::count(),
            'machines' => Machine::count(),
            'components' => Component::count(),
            'leads' => Lead::count(),
            'quotations' => Quotation::count(),
        ];

        $users = User::with('roles:id,name')
            ->latest()
            ->limit(10)
            ->get();

        $companies = Company::with([
            'users:id,name,email',
        ])
            ->withCount(['users', 'leads'])
            ->get();

        return view('admin.dashboard', compact(
            'title',
            'summary',
            'users',
            'companies'
        ));

    }
}