<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;
use App\Models\Condition;
use App\Models\Company;
use Illuminate\Http\Request;

class ConditionController extends Controller
{
    public function index(Company $company)
    {
        return view('company.store.conditions.index', [
            'conditions' => Condition::where('company_id', $company->id)->latest()->get(),
            'company' => $company,
            'label' => 'Condition List',
            'title' => $company->company_name . ' - Condition Management',
        ]);
    }

    public function store(Request $request, Company $company)
    {
        $request->merge([
            'name' => trim(strtolower($request->name))
        ]);

        $request->validate([
            'name' => 'required|max:100|unique:conditions,name,NULL,id,company_id,' . $company->id
        ]);

        $condition = Condition::create([
            'company_id' => $company->id,
            'name' => ucfirst($request->name)
        ]);

        return response()->json([
            'success' => true,
            'condition' => $condition
        ]);
    }

    public function update(Request $request, Company $company, Condition $condition)
    {
         $request->merge([
            'name' => trim(strtolower($request->name))
        ]);

        $request->validate([
            'name' => 'required|max:100|unique:conditions,name,' .
                $condition->id . ',id,company_id,' . $company->id
        ]);

        $condition->update([
            'name' => ucfirst($request->name)
        ]);

        return response()->json([
            'success' => true,
            'condition' => $condition
        ]);
    }

    public function destroy(Company $company, Condition $condition)
    {
      
        $condition->delete();

        return response()->json(['success' => true]);
    }
}
