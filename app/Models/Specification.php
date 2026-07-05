<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specification extends Model
{
    protected $fillable = [
        'company_id',
        'name'
    ];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_specifications');
    }
}
