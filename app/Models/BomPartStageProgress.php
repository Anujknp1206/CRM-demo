<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BomPartStageProgress extends Model
{
    protected $table = 'bom_part_stage_progress';

    protected $fillable = [
        'bom_part_id',
        'order_item_id',
        'stage_id',
        'status_id',
        'progress_percent',
        'started_at',
        'completed_at',
        'remarks'
    ];

    public function part()
    {
        return $this->belongsTo(BomPart::class, 'bom_part_id');
    }
    public function orderItem()
    {
        return $this->belongsTo(
            OrderItem::class,
            'order_item_id'
        );
    }
    public function stage()
    {
        return $this->belongsTo(
            ProductionStage::class,
            'production_stage_id'
        );
    }

    public function status()
    {
        return $this->belongsTo(
            ProductionStatus::class,
            'status_id'
        );
    }
}