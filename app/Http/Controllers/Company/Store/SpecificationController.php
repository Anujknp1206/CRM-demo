<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Specification;
use Illuminate\Http\Request;

class SpecificationController extends Controller
{
    public function getSpecifications($company)
    {
        $specs = Specification::where('company_id', $company)
            ->select('id', 'name')
            ->get();

        return response()->json($specs);
    }
    public function index(Company $company)
    {
        return view('company.production.specifications.index', [
            'specifications' => Specification::where('company_id', $company->id)->get(),
            'company' => $company,
            'label' => 'Specification List',
            'title' => 'Specification Management'
        ]);
    }

    public function store(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|max:100'
        ]);

        $spec = Specification::create([
            'company_id' => $company->id,
            'name' => $request->name
        ]);

        return response()->json(['spec' => $spec]);
    }

    public function update(Request $request, Company $company, Specification $specification)
    {
        $specification->update([
            'name' => $request->name
        ]);

        return response()->json(['spec' => $specification]);
    }

    public function destroy(Company $company, Specification $specification)
    {
        $specification->delete();
        return response()->json(['success' => true]);
    }
}
