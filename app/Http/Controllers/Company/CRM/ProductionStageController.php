<?php

namespace App\Http\Controllers\Company\CRM;

use App\Http\Controllers\Controller;
use App\Models\ProductionStage;
use Illuminate\Http\Request;


class ProductionStageController extends Controller
{
    /**
     * Store new stage
     */
    public function store(Request $request, $companyId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
            'apply_type' => 'required|in:global,order',
            'order_id' => 'nullable|exists:orders,id'
        ]);

        /*
        🔥 CREATE STAGE
        */
        $stage = ProductionStage::create([
            'company_id' => $companyId,
            'order_id' => $request->apply_type === 'order'
                ? $request->order_id
                : null,
            'name' => $request->name,
            'code' => $request->code,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        /*
        🔥 GET PARTS BASED ON APPLY TYPE
        */
        $partsQuery = \App\Models\BomPart::whereHas('bom', function ($q) use ($request) {

            if ($request->apply_type === 'order') {
                $q->where('order_id', $request->order_id);
            }

        });

        $parts = $partsQuery->get();

        /*
        🔥 DEFAULT STATUS
        */
        $defaultStatus = \App\Models\ProductionStatus::orderBy('sort_order')->first();

        /*
        🔥 CREATE STAGE PROGRESS (IMPORTANT FIX)
        */
        foreach ($parts as $part) {

            // get bom_items for this part
            $bomItems = \App\Models\BomItem::where('bom_part_id', $part->id)->get();

            foreach ($bomItems as $bomItem) {

                \App\Models\BomPartStageProgress::firstOrCreate(
                    [
                        'bom_part_id' => $part->id,
                        'stage_id' => $stage->id,
                        'order_item_id' => $bomItem->order_item_id, // ✅ REQUIRED
                    ],
                    [
                        'status_id' => $defaultStatus?->id,
                        'progress_percent' => $defaultStatus?->default_progress ?? 0,
                    ]
                );
            }
        }

        return back()->with('success', 'Stage created successfully');
    }

    /**
     * Update stage
     */
    public function update(Request $request, $companyId, $id)
    {
        $stage = ProductionStage::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer'
        ]);

        $stage->update([
            'name' => $request->name,
            'code' => $request->code,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return back()->with('success', 'Stage updated successfully');
    }


    /**
     * Delete stage
     */
    public function destroy($companyId, $id)
    {
        $stage = ProductionStage::findOrFail($id);

        // 🔥 Get all progress records of this stage
        $progressRecords = \App\Models\BomPartStageProgress::where('stage_id', $stage->id)->get();

        if ($progressRecords->isNotEmpty()) {

            // Check if any record is NOT pending
            $hasStarted = $progressRecords->contains(function ($progress) {

                return optional($progress->status)->name !== 'Pending';
            });

            if ($hasStarted) {
                return response()->json([
                    'message' => 'Cannot delete. Stage already started or completed.'
                ], 422);
            }

            // ✅ All are pending → safe to delete
            \App\Models\BomPartStageProgress::where('stage_id', $stage->id)->delete();
        }

        // Delete stage
        $stage->delete();

        return response()->json(['success' => true]);
    }
}