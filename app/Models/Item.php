<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'company_id',
        'category_id',
        'subcategory_id',
        'unit_id',
        'condition_id',
        'name',
        'hi_name',
        'code',
        'low_stock_level'
    ];
    protected static function booted()
    {
        static::created(function ($item) {

            $words = explode(' ', $item->name);

            $initials = '';

            foreach ($words as $word) {
                $initials .= strtoupper(substr($word, 0, 1));
            }

            $item->code = $initials . '-' . $item->id;

            $item->saveQuietly();
        });
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function unitConversions()
    {
        return $this->hasMany(ItemUnitConversion::class);
    }
    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }
    public function bomItems()
    {
        return $this->hasMany(BomItem::class);
    }
    public function stocks()
    {
        return $this->hasMany(Stock::class, 'item_id', 'id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function specifications()
    {
        return $this->belongsToMany(Specification::class, 'item_specifications')
            ->withPivot('is_required');
    }
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
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
}
