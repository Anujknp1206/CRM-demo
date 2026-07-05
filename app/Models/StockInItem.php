<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockInItem extends Model
{
    protected $fillable = [
        'stock_in_id',
        'purchase_order_item_id',
        'item_id',
        'brand_id',
        'condition_id',
        'location_id',
        'stock_unit_id',
        'unit_id',
        'stock_quantity',
        'quantity',
        'supplier_rate',
        'rate'
    ];
    public function stockIn()
    {
        return $this->belongsTo(
            StockIn::class
        );
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
    public function stockUnit()
    {
        return $this->belongsTo(Unit::class, 'stock_unit_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    public function poItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id');
    }
}

