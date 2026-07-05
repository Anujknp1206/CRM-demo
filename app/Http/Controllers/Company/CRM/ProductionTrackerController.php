<?php

namespace App\Http\Controllers\Company\CRM;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ProductionStage;
use App\Models\ProductionStatus;
use App\Models\BomPartStageProgress;
use Illuminate\Support\Facades\DB;
class ProductionTrackerController extends Controller
{
    /*
    -----------------------------------------
    SHOW PRODUCTION TRACKER PAGE
    -----------------------------------------
    */
    public function show(Company $company, $orderId)
    {
        $order = Order::with([
            'items.bomItems.part.stageProgress.stage',
            'items.bomItems.part.stageProgress.status'
        ])->findOrFail($orderId);

        /*
        -----------------------------------------
        LOAD STAGES
        -----------------------------------------
        */
        $stages = ProductionStage::where('company_id', $company->id)
            ->where(function ($q) use ($orderId) {

                $q->whereNull('order_id')
                    ->orWhere('order_id', $orderId);

            })
            ->orderBy('sort_order')
            ->get();

        /*
        -----------------------------------------
        LOAD STATUSES
        -----------------------------------------
        */
        $statuses = ProductionStatus::where('company_id', $company->id)
            ->orderBy('sort_order')
            ->get();

        /*
        -----------------------------------------
        DEFAULT STATUS
        -----------------------------------------
        */
        $defaultStatus = $statuses->first();

        /*
        -----------------------------------------
        AUTO CREATE MISSING PROGRESS ROWS
        -----------------------------------------
        */
        foreach ($order->items as $item) {

            $parts = $item->bomItems
                ->pluck('part')
                ->filter()
                ->unique('id');

            foreach ($parts as $part) {

                foreach ($stages as $stage) {

                    BomPartStageProgress::firstOrCreate(

                        [
                            'bom_part_id' => $part->id,

                            'order_item_id' => $item->id,

                            'stage_id' => $stage->id,
                        ],

                        [
                            'status_id' => $defaultStatus?->id,

                            'progress_percent' =>
                                $defaultStatus?->default_progress ?? 0,
                        ]
                    );
                }
            }
        }

        /*
        -----------------------------------------
        RELOAD RELATIONS
        IMPORTANT
        -----------------------------------------
        */
        $order->load([
            'items.bomItems.part.stageProgress.stage',
            'items.bomItems.part.stageProgress.status'
        ]);

        /*
        -----------------------------------------
        VIEW
        -----------------------------------------
        */
        return view('company.crm.orders.production', [

            'order' => $order,

            'company' => $company,

            'stages' => $stages,

            'statuses' => $statuses,

            'title' => 'Production Tracker',

            'label' => 'Production Tracker'
        ]);
    }


    /*
    -----------------------------------------
    INITIALIZE STAGES FOR ORDER
    (Run once after BOM creation)
    -----------------------------------------
    */
    public function initialize($companyId, $orderId)
    {
        $order = Order::with('boms.parts')->findOrFail($orderId);

        $stages = ProductionStage::where('company_id', $companyId)
            ->where(function ($q) use ($orderId) {
                $q->whereNull('order_id')
                    ->orWhere('order_id', $orderId);
            })
            ->orderBy('sort_order')
            ->get();

        $defaultStatus = ProductionStatus::where('company_id', $companyId)
            ->orderBy('sort_order')
            ->first();

        foreach ($order->boms as $bom) {

            foreach ($bom->parts as $part) {

                foreach ($stages as $stage) {

                    BomPartStageProgress::firstOrCreate(
                        [
                            'bom_part_id' => $part->id,
                            'order_item_id' => $bom->order_item_id,
                            'stage_id' => $stage->id,
                        ],
                        [
                            'status_id' => $defaultStatus?->id,
                            'progress_percent' => $defaultStatus?->default_progress ?? 0
                        ]
                    );
                }
            }
        }

        return redirect()
            ->route('orders.production.detail', [$companyId, $orderId])
            ->with('success', 'Production initialized');
    }


    /*
    -----------------------------------------
    UPDATE PART STAGE PROGRESS (AJAX)
    -----------------------------------------
    */
    public function updatePartStage(Request $request, $companyId)
    {
        $request->validate([
            'data' => 'required|array'
        ]);

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | LOAD STATUS PROGRESS MAP
            |--------------------------------------------------------------------------
            */
            $statuses = ProductionStatus::pluck(
                'default_progress',
                'id'
            );

            /*
            |--------------------------------------------------------------------------
            | TRACK AFFECTED ORDERS
            |--------------------------------------------------------------------------
            */
            $affectedOrderIds = [];

            /*
            |--------------------------------------------------------------------------
            | UPDATE STAGES
            |--------------------------------------------------------------------------
            */
            foreach ($request->data as $row) {

                $progress = BomPartStageProgress::with([
                    'part',
                    'orderItem'
                ])->find($row['id']);

                if (!$progress) {
                    continue;
                }

                $statusId = $row['status_id'];

                /*
                |--------------------------------------------------------------------------
                | GET DEFAULT PROGRESS
                |--------------------------------------------------------------------------
                */
                $progressPercent = $statuses[$statusId] ?? 0;

                /*
                |--------------------------------------------------------------------------
                | UPDATE STAGE
                |--------------------------------------------------------------------------
                */
                $progress->status_id = $statusId;

                $progress->progress_percent = $progressPercent;

                /*
                |--------------------------------------------------------------------------
                | HANDLE TIMESTAMPS
                |--------------------------------------------------------------------------
                */

                // completed
                if ($progressPercent >= 100) {

                    $progress->completed_at =
                        $progress->completed_at ?? now();

                    $progress->started_at =
                        $progress->started_at ?? now();
                }

                // in progress
                elseif ($progressPercent > 0) {

                    $progress->started_at =
                        $progress->started_at ?? now();

                    $progress->completed_at = null;
                }

                // pending
                else {

                    $progress->started_at = null;

                    $progress->completed_at = null;
                }

                $progress->save();

                /*
                |--------------------------------------------------------------------------
                | RECALCULATE PART PROGRESS
                |--------------------------------------------------------------------------
                */
                $part = $progress->part;

                if ($part) {

                    $partProgresses = BomPartStageProgress::where(
                        'bom_part_id',
                        $part->id
                    )
                        ->where(
                            'order_item_id',
                            $progress->order_item_id
                        )
                        ->get();

                    /*
                    |--------------------------------------------------------------------------
                    | AVG STAGE PROGRESS
                    |--------------------------------------------------------------------------
                    */
                    $averageProgress = $partProgresses->avg(
                        'progress_percent'
                    ) ?? 0;

                    /*
                    |--------------------------------------------------------------------------
                    | SAVE PART PROGRESS
                    |--------------------------------------------------------------------------
                    */
                    $part->progress_percent = round(
                        $averageProgress,
                        2
                    );

                    $part->save();
                }

                /*
                |--------------------------------------------------------------------------
                | TRACK ORDER ID
                |--------------------------------------------------------------------------
                */
                if (
                    $progress->orderItem &&
                    $progress->orderItem->order_id
                ) {

                    $affectedOrderIds[] =
                        $progress->orderItem->order_id;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | UNIQUE ORDER IDS
            |--------------------------------------------------------------------------
            */
            $affectedOrderIds = array_unique(
                $affectedOrderIds
            );

            /*
            |--------------------------------------------------------------------------
            | AUTO UPDATE ORDER STATUS
            |--------------------------------------------------------------------------
            */
            foreach ($affectedOrderIds as $orderId) {

                /*
                |--------------------------------------------------------------------------
                | CHECK IF ANY STAGE IS PENDING
                |--------------------------------------------------------------------------
                */
                $pendingStages = BomPartStageProgress::whereHas(
                    'orderItem',
                    function ($q) use ($orderId) {

                        $q->where(
                            'order_id',
                            $orderId
                        );
                    }
                )
                    ->where('progress_percent', '<', 100)
                    ->exists();

                /*
                |--------------------------------------------------------------------------
                | UPDATE ORDER STATUS
                |--------------------------------------------------------------------------
                */

                // all completed
                if (!$pendingStages) {

                    Order::where('id', $orderId)
                        ->update([
                            'status' => 'dispatched'
                        ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Production updated successfully'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /*
    -----------------------------------------
    OPTIONAL: UPDATE SINGLE STAGE (INLINE EDIT)
    -----------------------------------------
    */
    public function updateSingleStage(Request $request, $companyId)
    {
        $request->validate([
            'id' => 'required',
            'status_id' => 'required'
        ]);

        $progress = BomPartStageProgress::findOrFail($request->id);

        $status = ProductionStatus::find($request->status_id);

        $progress->status_id = $request->status_id;
        $progress->progress_percent = $status->default_progress ?? 0;

        if ($progress->progress_percent == 100) {
            $progress->completed_at = now();
        } elseif ($progress->progress_percent > 0) {
            $progress->started_at = $progress->started_at ?? now();
        } else {
            $progress->started_at = null;
            $progress->completed_at = null;
        }

        $progress->save();

        return response()->json(['success' => true]);
    }
}