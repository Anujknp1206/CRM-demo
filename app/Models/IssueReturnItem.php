<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssueReturnItem extends Model
{
    protected $fillable = [
        'return_id',
        'item_id',
        'brand_id',
        'condition_id',
        'location_id',
        'unit_id',
        'return_qty'
    ];

    public function return()
    {
        return $this->belongsTo(IssueReturn::class, 'return_id');
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