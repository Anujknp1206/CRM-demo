<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BomPart extends Model
{
    protected $fillable = [
        'bom_id',
        'part_name',
        'hi_part_name',
        'progress_percent',
        'spec_id',
        'shift_id',
        'sort_order',
        'weightage'
    ];

    public function bom()
    {
        return $this->belongsTo(Bom::class);
    }

    public function items()
    {
        return $this->hasMany(BomItem::class, 'bom_part_id');
    }

    public function spec()
    {
        return $this->belongsTo(Specification::class, 'spec_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    public function stageProgress()
    {
        return $this->hasMany(BomPartStageProgress::class);
    }
    public function stageProgresses()
    {
        return $this->hasMany(
            BomPartStageProgress::class,
            'bom_part_id'
        );
    }
    public function recalculateProgress($orderItemId = null)
    {
        $progresses = $this->stageProgress;

        if ($orderItemId) {

            $progresses = $progresses
                ->where('order_item_id', $orderItemId);
        }

        if ($progresses->isEmpty()) {

            $this->progress_percent = 0;

        } else {

            $this->progress_percent = round(
                $progresses->avg('progress_percent'),
                2
            );
        }

        $this->save();
    }
    public function getProgressForOrderItem($orderItemId)
    {
        $progresses = BomPartStageProgress::where(
            'bom_part_id',
            $this->id
        )
            ->where(
                'order_item_id',
                $orderItemId
            )
            ->get();

        if ($progresses->isEmpty()) {
            return 0;
        }

        return round(
            $progresses->avg('progress_percent')
        );
    }
}