<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'company_id',
        'brand_id',
        'item_id',
        'condition_id',
        'location_id',
        'unit_id',
        'quantity',
        'min_quantity'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    public function stockInItems()
    {
        return $this->hasMany(StockInItem::class, 'item_id', 'item_id');
    }
}


