<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'machine_id',
        'component_id',
        'description',
        'hi_description',
        'quantity',
        'unit_price',
        'sort_order',
        'total_price',
        'converted_unit_price',
        'converted_total_price',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function component()
    {
        return $this->belongsTo(Component::class);
    }
}
