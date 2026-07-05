<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $title = "Company Management";
        $label = "Company List";

        $user = auth()->user();

        // Check if the user is super admin (replace 'super-admin' with your role name)
        if ($user->hasRole('Super Admin')) {
            // Super Admin: see all companies
            $companies = Company::with(['country', 'state', 'city'])->get();
        } else {
            // Normal user: see only companies assigned to them
            $companies = Company::with(['country', 'state', 'city'])
                ->whereHas('users', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                ->get();
        }

        return view('admin.companies.index', compact('companies', 'title', 'label'));
    }


    public function create()
    {
        $title = "Company Management";
        $label = "Add Company";

        $countries = Country::all();

        return view('admin.companies.create', compact('title', 'label', 'countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile' => 'required|string|max:15',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required'
        ]);

        Company::create([
            'company_name' => $request->company_name,
            'country_id' => $request->country,
            'state_id' => $request->state,
            'city_id' => $request->city,
            'email' => $request->email,
            'website' => $request->website,
            'alternate_email' => $request->alternate_email,
            'mobile' => $request->mobile,
            'alternate_mobile' => $request->alternate_mobile,
            'gstin_no' => $request->gstin_no,
            'rex_registration_no' => $request->rex_registration_no,
            'iec_code' => $request->iec_code,
            'pan_no' => $request->pan_no,
            'address' => $request->address,
            'pincode' => $request->pincode,
            'status' => $request->status,
        ]);

        toast('Company Created Successfully', 'success');
        return redirect()->route('companies.index');
    }

    public function edit($id)
    {
        $title = "Company Management";
        $label = "Edit Company";

        $company = Company::findOrFail($id);
        $countries = Country::all();
        $states = State::where('country_id', $company->country_id)->get();
        $cities = City::where('state_id', $company->state_id)->get();

        return view('admin.companies.edit', compact(
            'company',
            'title',
            'label',
            'countries',
            'states',
            'cities'
        ));
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile' => 'required|string|max:15',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required'
        ]);

        $company->update([
            'company_name' => $request->company_name,
            'country_id' => $request->country,
            'state_id' => $request->state,
            'city_id' => $request->city,
            'email' => $request->email,
            'website' => $request->website,
            'alternate_email' => $request->alternate_email,
            'mobile' => $request->mobile,
            'alternate_mobile' => $request->alternate_mobile,
            'gstin_no' => $request->gstin_no,
            'rex_registration_no' => $request->rex_registration_no,
            'iec_code' => $request->iec_code,
            'pan_no' => $request->pan_no,
            'address' => $request->address,
            'pincode' => $request->pincode,
            'status' => $request->status,
        ]);

        toast('Company Updated Successfully', 'success');
        return redirect()->route('companies.index');
    }

    public function destroy($id)
    {
        Company::destroy($id);
        toast('Company Deleted Successfully', 'success');
        return back();
    }
    public function changeStatus(Request $request)
    {
        $company = Company::find($request->company_id);

        $company->status = !$company->status;
        $company->save();

        return response()->json([
            'status' => 'success',
            'newFlag' => $company->status,
            'message' => "Company status updated!"
        ]);
    }

}
