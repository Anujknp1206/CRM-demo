<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'company_id',
        'employee_id',
        'date',
        'is_present'
    ];

    protected $casts = [
        'is_present' => 'boolean',
        'date' => 'date'
    ];

    /* ================= RELATIONSHIPS ================= */

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}