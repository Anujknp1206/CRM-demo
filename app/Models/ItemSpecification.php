<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemSpecification extends Model
{
    protected $fillable = [
        'item_id',
        'specification_id',
        'is_required'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function specification()
    {
        return $this->belongsTo(Specification::class);
    }
}