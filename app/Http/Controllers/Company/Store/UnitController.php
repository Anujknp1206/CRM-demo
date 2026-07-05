<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Company;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Company $company)
    {
        $title = $company->company_name . " - Unit Management";
        return view('company.store.units.index', [
            'units' => Unit::latest()->get(),
            'company' => $company,
            'label' => 'Unit List',
            'title' => $title,
        ]);
    }


    public function store(Request $request, Company $company)
    {
        $request->merge([
            'name' => trim(strtolower($request->name))
        ]);

        $request->validate([
            'name' => 'required|max:50|unique:units,name,NULL,id,company_id,' . $company->id
        ]);

        $unit = Unit::create([
            'company_id' => $company->id,
            'name' => ucfirst($request->name)
        ]);


        return response()->json([
            'success' => true,
            'unit' => $unit
        ]);
    }
    public function update(Request $request, Company $company, Unit $unit)
    {
        $request->merge([
            'name' => trim(strtolower($request->name))
        ]);

        $request->validate([
            'name' => 'required|max:50|unique:units,name,' . $unit->id
        ]);

        $unit->update([
            'name' => ucfirst($request->name)
        ]);

        return response()->json([
            'success' => true,
            'unit' => $unit
        ]);
    }

    public function destroy(Company $company, Unit $unit)
    {
        $unit->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
