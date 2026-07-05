<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemUnitConversion extends Model
{
    protected $fillable = [
        'company_id',
        'item_id',
        'from_unit_id',
        'to_unit_id',
        'factor',
    ];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function fromUnit()
    {
        return $this->belongsTo(Unit::class, 'from_unit_id');
    }
    public function toUnit()
    {
        return $this->belongsTo(Unit::class, 'to_unit_id');
    }
}