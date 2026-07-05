<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;

use Illuminate\Http\Request;



class CategoryController extends Controller
{
    public function index(Company $company)
    {
        return view('company.store.categories.index', [
            'categories' => Category::where('company_id', $company->id)->latest()->get(),
            'company' => $company,
            'label' => 'Category List',
            'title' => $company->company_name . ' - Category Management',
        ]);
    }

    public function store(Request $request, Company $company)
    {
        $request->merge([
            'name' => trim(strtolower($request->name))
        ]);

        $request->validate([
            'name' => 'required|max:100|unique:categories,name,NULL,id,company_id,' . $company->id
        ]);

        $category = Category::create([
            'company_id' => $company->id,
            'name' => ucfirst($request->name)
        ]);

        return response()->json([
            'success' => true,
            'category' => $category
        ]);
    }

    public function update(Request $request, Company $company, Category $category)
    {
        $request->merge([
            'name' => trim(strtolower($request->name))
        ]);

        $request->validate([
            'name' => 'required|max:100|unique:categories,name,' . $category->id . ',id,company_id,' . $company->id
        ]);

        $category->update([
            'name' => ucfirst($request->name)
        ]);

        return response()->json([
            'success' => true,
            'category' => $category
        ]);
    }

    public function destroy(Company $company, Category $category)
    {
        $category->delete();

        return response()->json(['success' => true]);
    }
}

