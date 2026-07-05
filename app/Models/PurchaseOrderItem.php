<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'brand_id',
        'condition_id',
        'location_id',
        'unit_id',
        'quantity',
        'rate',
        'amount',
        'specification_id'
    ];

    public function stockInItems()
    {
        return $this->hasMany(StockInItem::class, 'purchase_order_item_id');
    }
    public function po()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    // 📦 Item
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function specification()
    {
        return $this->belongsTo(Specification::class);
    }
    // 🏷 Brand
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // 📊 Condition
    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    // 📍 Location
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}