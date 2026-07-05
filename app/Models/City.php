<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'state_id',
        'name',
        'postal_code',
    ];

    /**
     * A city belongs to a state.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
