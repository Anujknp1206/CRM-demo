<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Http\Request;

// app/Http/Controllers/Company/HR/DepartmentController.php
class DepartmentController extends Controller
{
    public function index(Company $company)
    {
        $title = $company->company_name . " - Department Management";
        return view('company.store.departments.index', [
            'company' => $company,
            'title' => $title,
            'label' => 'Department List',
        ]);
    }
    public function data(Request $request, Company $company)
    {
        $query = Department::where('company_id', $company->id);


        $departments = $query->get();

        return view('company.store.departments.partials.rows', compact('departments'))->render();
    }
    public function store(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|max:100'
        ]);

        $department = Department::create([
            'company_id' => $company->id,
            'name' => strtoupper($request->name)
        ]);

        return response()->json(['department' => $department]);
    }

    public function update(Request $request, Company $company, Department $department)
    {
        $department->update([
            'name' => strtoupper($request->name)
        ]);

        return response()->json(['department' => $department]);
    }

    public function destroy(Company $company, Department $department)
    {
        $department->delete();
        return response()->json(['success' => true]);
    }
}

