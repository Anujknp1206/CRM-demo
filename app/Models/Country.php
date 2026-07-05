<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'phonecode',
    ];

    /**
     * A country has many states.
     */
    public function states()
    {
        return $this->hasMany(State::class);
    }
}
