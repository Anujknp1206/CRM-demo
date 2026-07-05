<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionStage extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'sort_order',
        'order_id',
        'is_active'
    ];

    public function progresses()
    {
        return $this->hasMany(BomPartStageProgress::class, 'stage_id');
    }
}