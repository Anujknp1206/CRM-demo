<?php

namespace App\Observers;

use App\Models\OrderItem;
use App\Models\ProductionStage;
use App\Models\ProductionStatus;
use App\Models\OrderItemStageStatus;

class OrderItemObserver
{

    public function created(
        OrderItem $item
    ) {

        $pending =
            ProductionStatus::where(
                'name',
                'Pending'
            )->first();

        if (!$pending) {
            return;
        }


        $stages =
            ProductionStage::where(
                'active',
                1
            )
                ->orderBy(
                    'sequence'
                )
                ->get();  


        foreach (
            $stages as $stage
        ) {

            OrderItemStageStatus::firstOrCreate(

                [
                    'order_item_id'
                    =>
                        $item->id,

                    'production_stage_id'
                    =>
                        $stage->id

                ],

                [
                    'production_status_id'
                    =>
                        $pending->id
                ]

            );

        }

    }

}