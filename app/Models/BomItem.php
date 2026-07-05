<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BomItem extends Model
{
    protected $fillable = [
        'bom_id',
        'item_id',
        'department_id',
        'recipe_id',
        'bom_part_id',
        'employee_id',
        'shift_id',
        'order_item_id',
        'quantity',
        'unit_id',
        'status',
        'remarks',
        'notes',
        'hi_notes'
    ];

    public function bom()
    {
        return $this->belongsTo(Bom::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function part()
    {
        return $this->belongsTo(BomPart::class, 'bom_part_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function orderItem()
    {
        return $this->belongsTo(
            OrderItem::class,
            'order_item_id'
        );
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

}