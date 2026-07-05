<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'company_id',
        'rfi_id',
        'supplier_id',
        'po_code',
        'po_date',
        'total_amount',
        'status',
        'created_by',
        'subtotal',
        'discount',
        'tax',
        'tax_amount',
        'final_amount',
        'remark',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
    public function stockIns()
    {
        return $this->hasMany(StockIn::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function rfi()
    {
        return $this->belongsTo(Rfi::class);
    }
}
