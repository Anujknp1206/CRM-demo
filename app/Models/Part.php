<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    protected $fillable = [
        'name',
        'hi_name',
        'code',
        'notes',
        'hi_notes',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($part) {

            // If code already provided → skip
            if (!empty($part->code)) {
                return;
            }

            // Generate code
            $part->code = self::generateCode($part->name);
        });
    }

    public static function generateCode($name)
    {
        // Prefix from name (first 3 letters)
        $prefix = strtoupper(substr(preg_replace('/\s+/', '', $name), 0, 3));

        // Count existing parts with same prefix
        $count = self::where('code', 'like', $prefix . '%')->count() + 1;

        // Format number (001, 002...)
        $number = str_pad($count, 3, '0', STR_PAD_LEFT);

        return $prefix . '-' . $number;
    }
    public function items()
    {
        return $this->hasMany(PartItem::class);
    }
    public function recipes()
    {
        return $this->belongsToMany(
            Recipe::class,
            'part_recipe'
        )->withTimestamps();
    }
}