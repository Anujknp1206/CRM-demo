<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'company_id',
        'name'
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
    public function fromConversions()
    {
        return $this->hasMany(ItemUnitConversion::class, 'from_unit_id');
    }

    public function toConversions()
    {
        return $this->hasMany(ItemUnitConversion::class, 'to_unit_id');
    }
}
