<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'address',
        'gst',
        'country_id',
        'state_id',
        'city_id',
    ];

    public function phones()
    {
        return $this->hasMany(CustomerPhone::class);
    }
    public function primaryPhone()
    {
        return $this->hasOne(CustomerPhone::class)->where('is_primary', true);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
    public function getFullPrimaryMobileAttribute()
    {
        $phone = optional($this->primaryPhone)->phone;

        if (!$phone) {
            return '—';
        }

        $code = optional($this->country)->phonecode;

        return $code ? '+' . $code . ' ' . $phone : $phone;
    }
}
