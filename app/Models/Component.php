<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Component extends Model
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

        static::creating(function ($component) {
            if (empty($component->code)) {
                $component->code = self::generateCode($component->name, $component->size);
            }
        });
    }
    public function quotationItems()
    {
        return $this->hasMany(QuotationItem::class, 'component_id');
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
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'component_id');
    }
    public static function generateCode($name, $size)
    {
        $prefix = strtoupper(substr($name, 0, 3));
        $cleanSize = str_replace(' ', '', $size);

        return $prefix . '-' . $cleanSize;
    }
}
