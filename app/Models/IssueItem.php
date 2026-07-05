<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssueItem extends Model
{
    protected $fillable = [
        'issue_id',
        'item_id',
        'brand_id',
        'bom_item_id',
        'condition_id',
        'location_id',
        'unit_id',
        'requested_qty',
        'issued_qty',
        'pending_qty'
    ];

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}

