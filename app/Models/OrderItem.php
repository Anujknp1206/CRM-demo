<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'machine_id',
        'component_id',
        'description',
        'hi_description',
        'quantity',
        'unit_price',
        'total_price',
        'sort_order',
        'converted_unit_price',
        'converted_total_price',
    ];
    protected $appends = [
        'item_name',
        'progress_percent'
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function bomItems()
    {
        return $this->hasMany(
            BomItem::class,
            'order_item_id'
        );
    }
    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }
    public function getProgressPercentAttribute()
    {
        $parts = $this->bomItems
            ->pluck('part')
            ->filter()
            ->unique('id');

        $totalWeight = $parts->sum('weightage');

        if ($totalWeight <= 0) {
            return 0;
        }

        $itemProgress = 0;

        foreach ($parts as $part) {

            $partProgress = $part->getProgressForOrderItem(
                $this->id
            );

            $itemProgress += (
                $partProgress *
                ($part->weightage ?? 0)
            );
        }

        return round($itemProgress / $totalWeight);
    }
    public function item()
    {
        return $this->belongsTo(\App\Models\Item::class);
    }
    public function component()
    {
        return $this->belongsTo(Component::class, 'component_id');
    }

    public function getItemNameAttribute()
    {
        return $this->machine?->name
            ?? $this->component?->name
            ?? $this->item?->name
            ?? $this->description
            ?? 'Item';
    }
    public function bomParts()
    {
        return $this->hasManyThrough(
            BomPart::class,
            Bom::class,
            'id', // Foreign key on BOM
            'bom_id', // Foreign key on BomPart
            'id', // Local key on OrderItem
            'id'
        );
    }
}
