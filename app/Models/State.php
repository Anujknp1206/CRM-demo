<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class State extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
    ];

    /**
     * A state belongs to a country.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * A state has many cities.
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
