<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Company $company)
    {
        $title = $company->company_name . " - Supplier Management";
        $label = 'Supplier List';
        $suppliers = Supplier::where('company_id', $company->id)->with('state', 'city')->get();
        return view('company.store.suppliers.index', compact('company', 'suppliers', 'title', 'label'));
    }

    public function store(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'mobile' => 'required|regex:/^[0-9]{10}$/',
            'email' => 'nullable|email',
        ]);

        $supplier = Supplier::create([
            'company_id' => $company->id,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'name' => $request->name,
            'address' => $request->address,
            'tin_no' => $request->tin_no,
            'email' => $request->email,
            'mobile' => $request->mobile,
        ]);

        return response()->json([
            'message' => 'Supplier added successfully',
            'supplier' => $supplier->load('state', 'city')
        ]);
    }

    public function show(Company $company, Supplier $supplier)
    {
        return response()->json(
            $supplier->load('country', 'state', 'city')
        );
    }


    public function update(Request $request, Company $company, Supplier $supplier)
    {
        $supplier->update($request->all());
        $supplier->load('state', 'city');
        return response()->json([
            'message' => 'Supplier updated successfully',
            'supplier' => $supplier
        ]);
    }
    public function search(Request $request, Company $company)
    {
        $q = $request->q;

        $suppliers = Supplier::with('state', 'city')
            ->where('company_id', $company->id)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('mobile', 'like', "%$q%")
                    ->orWhere('tin_no', 'like', "%$q%");
            })
            ->get();

        return response()->json($suppliers);
    }

    public function destroy(Company $company, Supplier $supplier)
    {
        $supplier->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
