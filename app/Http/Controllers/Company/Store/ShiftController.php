<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function getShifts($company)
    {
        return Shift::select('id', 'name')->get();
    }
    public function index(Company $company)
    {
        return view('company.production.shifts.index', [
            'shifts' => Shift::latest()->get(),
            'company' => $company,
            'label' => 'Shift List',
            'title' => 'Shift Management'
        ]);
    }

    public function store(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|max:50',
            'start_time' => 'nullable',
            'end_time' => 'nullable'
        ]);

        $shift = Shift::create($request->all());

        return response()->json(['shift' => $shift]);
    }

    public function update(Request $request, Company $company, Shift $shift)
    {
        $shift->update($request->all());

        return response()->json(['shift' => $shift]);
    }

    public function destroy(Company $company, Shift $shift)
    {
        $shift->delete();
        return response()->json(['success' => true]);
    }
}