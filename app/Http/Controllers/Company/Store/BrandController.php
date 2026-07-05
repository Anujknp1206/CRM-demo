<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Company;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index($companyId)
    {
        $company = Company::findOrFail($companyId);

        $brands = Brand::where('company_id', $companyId)
            ->orderBy('name')
            ->get();
        $title = $company->company_name . " - Brand Management";

        return view('company.store.brands.index', [
            'company' => $company,
            'brands' => $brands,
            'label' => 'Brands List',
            'title' => $title,
        ]);
    }
    public function store(Request $request, $companyId)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $brand = Brand::create([
            'company_id' => $companyId,
            'name' => $request->name
        ]);

        return response()->json(['brand' => $brand]);
    }

    public function update(Request $request, $companyId, $id)
    {
        $brand = Brand::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $brand->update(['name' => $request->name]);

        return response()->json(['brand' => $brand]);
    }

    public function destroy($companyId, $id)
    {
        Brand::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }


}
