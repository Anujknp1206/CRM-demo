<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $fillable = [
        'name',
        'hi_name',
        'moc',
        'size',
        'code',
        'origin',
        'description',
        'hi_description',
        'status'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($machine) {

            // If code is NOT provided manually, auto generate
            if (empty($machine->code)) {
                $machine->code = self::generateCode($machine->name, $machine->size);
            }
        });
    }
    public function recipes()
    {
        return $this->morphMany(Recipe::class, 'recipeable');
    }

    public function recipe()
    {
        return $this->morphOne(Recipe::class, 'recipeable')
            ->where('is_default', 1);
    }
    public function quotationItems()
    {
        return $this->hasMany(QuotationItem::class, 'machine_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'machine_id');
    }
    public static function generateCode($name, $size)
    {
        $prefix = strtoupper(substr($name, 0, 3));   // First 3 letters of name
        $cleanSize = str_replace(' ', '', $size);    // Remove spaces

        return $prefix . '-' . $cleanSize;
    }
}
