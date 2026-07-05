<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'country_id',
        'state_id',
        'city_id',
        'pincode',
        'email',
        'website',
        'alternate_email',
        'mobile',
        'alternate_mobile',
        'gstin_no',
        'rex_registration_no',
        'iec_code',
        'pan_no',
        'address'
    ];

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'company_id');
    }

    public function followups()
    {
        return $this->hasMany(Followup::class);
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
    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user');
    }
    public function initials()
    {
        return collect(explode(' ', $this->company_name))
            ->map(fn($word) => strtoupper($word[0]))
            ->join('');
    }
    public function getFullMobileAttribute()
    {
        $code = $this->country->phonecode ?? '';
        return $code ? '+' . $code . ' ' . $this->mobile : $this->mobile;
    }
}
