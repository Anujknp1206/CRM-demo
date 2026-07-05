<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


// app/Models/Department.php
class Department extends Model
{
    protected $fillable = ['company_id', 'name'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function bomDepartments()
    {
        return $this->hasMany(BomDepartment::class);
    }
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
