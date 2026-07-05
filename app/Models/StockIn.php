<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    protected $fillable = [
        'company_id',
        'doc_no',
        'doc_date',
        'grn_date',
        'po_date',
        'supplier_date',
        'supplier_document',
        'supplier_id',
        'purchase_order_id',
        'sup_doc_num',
        'remark'
    ];

    public function items()
    {
        return $this->hasMany(StockInItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
