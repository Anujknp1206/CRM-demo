<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Company;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Company $company)
    {
        return view('company.store.locations.index', [
            'company' => $company,
            'title' => $company->company_name . ' - Location Management',
            'label' => 'Location List',
            'locations' => Location::with('parent')
                ->where('company_id', $company->id)
                ->latest()
                ->get(),
            'parentLocations' => Location::where('company_id', $company->id)->get()
        ]);
    }

    public function store(Request $request, Company $company)
    {
        $location = Location::create([
            'company_id' => $company->id,
            'name' => strtoupper($request->name),
            'parent_id' => $request->parent_id
        ]);

        return response()->json([
            'location' => $location->load('parent')
        ]);
    }
    public function show(Company $company, Location $location)
    {
        return response()->json($location);
    }
    public function update(Request $request, Company $company, Location $location)
    {
        $location->update([
            'name' => strtoupper($request->name),
            'parent_id' => $request->parent_id
        ]);

        return response()->json([
            'location' => $location->load('parent')
        ]);
    }

    public function destroy(Company $company, Location $location)
    {
        
        // ✅ delete all children first
        Location::where('parent_id', $location->id)->delete();

        // ✅ then delete parent
        $location->delete();

        return response()->json(['success' => true]);
    }


    public function search(Request $request, Company $company)
    {
        return Location::with('parent')
            ->where('company_id', $company->id)
            ->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->q}%")
                    ->orWhereHas('parent', fn($p) =>
                        $p->where('name', 'LIKE', "%{$request->q}%"));
            })
            ->get();
    }
}

