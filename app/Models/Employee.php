<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
// app/Models/Employee.php
class Employee extends Model
{
    protected $fillable = [

        // relations
        'company_id',
        'department_id',
        'country_id',
        'state_id',
        'city_id',

        // personal
        'first_name',
        'middle_name',
        'last_name',
        'father_name',
        'address',
        'pincode',
        'email',
        'mobile',
        'previous_company',
        'experience_years',
        'reference_name',

        // office
        'joining_date',
        'user_id',
        'password',
        'pan',
        'status',

        // bank
        'bank_name',
        'account_no',
        'account_holder',
        'branch_name',
        'ifsc_code',
    ];
    protected $casts = [
        'status' => 'boolean',
    ];
    /* ================= RELATIONSHIPS ================= */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopePresentToday($query)
    {
        $today = Carbon::today();

        return $query->whereHas('attendances', function ($q) use ($today) {
            $q->whereDate('date', $today)
                ->where('is_present', 1);
        });
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
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
}
