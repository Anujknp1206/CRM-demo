<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;

use App\Models\Category;
use App\Models\Company;
use App\Models\Subcategory;
use Illuminate\Http\Request;


class SubCategoryController extends Controller
{
    public function index(Company $company)
    {
        $subcategories = Subcategory::with('category')
            ->whereHas('category', fn($q) => $q->where('company_id', $company->id))
            ->latest()
            ->get();

        return view('company.store.subcategories.index', [
            'subcategories' => $subcategories,
            'company' => $company,
            'label' => 'Sub Category List',
            'title' => $company->company_name . ' - Sub Category Management',
        ]);
    }

    public function store(Request $request, Company $company)
    {
        $request->merge([
            'name' => trim(strtolower($request->name))
        ]);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:100|unique:subcategories,name,NULL,id,category_id,' . $request->category_id
        ]);

        $subcategory = Subcategory::create([
            'category_id' => $request->category_id,
            'name' => ucfirst($request->name)
        ]);

        return response()->json([
            'success' => true,
            'subcategory' => $subcategory->load('category')
        ]);
    }

    public function update(Request $request, Company $company, Subcategory $subcategory)
    {
        $request->merge([
            'name' => trim(strtolower($request->name))
        ]);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:100|unique:subcategories,name,' . $subcategory->id . ',id,category_id,' . $request->category_id
        ]);

        $subcategory->update([
            'category_id' => $request->category_id,
            'name' => ucfirst($request->name)
        ]);

        return response()->json([
            'success' => true,
            'subcategory' => $subcategory->load('category')
        ]);
    }

    public function destroy(Company $company, Subcategory $subcategory)
    {
        $subcategory->delete();
        return response()->json(['success' => true]);
    }
    public function byCategory(Company $company, Category $category)
    {
        return Subcategory::where('category_id', $category->id)->get();
    }

}
