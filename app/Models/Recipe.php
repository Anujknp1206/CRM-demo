<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'recipeable_type',
        'recipeable_id',
        'name',
        'hi_name',
        'notes',
        'hi_notes',
        'is_default'
    ];
    public function recipeable()
    {
        return $this->morphTo();
    }
    public function parts()
    {
        return $this->belongsToMany(
            Part::class,
            'part_recipe',
            'recipe_id',
            'part_id'
        )
            ->select([
                'parts.id',
                'parts.name',
                'parts.hi_name',
                'parts.code',
                'parts.notes',
                'parts.hi_notes'
            ])
            ->withPivot([
                'weightage'
            ])
            ->withTimestamps();
    }
}