<?php

namespace App\Http\Controllers\Company\CRM;

use App\Http\Controllers\Controller;
use App\Models\ProductionStatus;
use App\Models\Company;
use Illuminate\Http\Request;

class ProductionStatusController extends Controller
{

    public function store(Request $request, $companyId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'badge_color' => 'nullable|string|max:50',
            'default_progress' => 'nullable|integer|min:0|max:100',
            'sort_order' => 'nullable|integer'
        ]);

        ProductionStatus::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'badge_color' => $request->badge_color ?? 'secondary',
            'default_progress' => $request->default_progress ?? 0,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return back()->with('success', 'Status created successfully');
    }


    /**
     * Update status
     */
    public function update(Request $request, $companyId, $id)
    {
        $status = ProductionStatus::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'badge_color' => 'nullable|string|max:50',
            'default_progress' => 'nullable|integer|min:0|max:100',
            'sort_order' => 'nullable|integer'
        ]);

        $status->update([
            'name' => $request->name,
            'badge_color' => $request->badge_color,
            'default_progress' => $request->default_progress ?? 0,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return back()->with('success', 'Status updated successfully');
    }


    /**
     * Delete status
     */
    public function destroy($companyId, $id)
    {
        $status = ProductionStatus::findOrFail($id);

        // Prevent delete if used
        if ($status->stageProgress()->exists()) {
            return response()->json([
                'message' => 'Status is used in production'
            ], 422);
        }

        $status->delete();

        return response()->json(['success' => true]);
    }

    public function index(Company $company)
    {
        $statuses = ProductionStatus::where('company_id', $company->id)
            ->withCount('progresses')
            ->orderBy('sort_order')
            ->get();

        return view('company.crm.orders.production_stage', [
            'statuses' => $statuses,
            'company' => $company,
            'label' => 'Production Status',
            'title' => 'Production Status',
        ]);
    }
}