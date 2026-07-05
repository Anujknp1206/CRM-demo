<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartItem extends Model
{
    protected $fillable = [
        'part_id',
        'item_id',
        'quantity',
        'notes',
        'hi_notes'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function part()
    {
        return $this->belongsTo(Part::class);
    }
}
