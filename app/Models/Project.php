<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'start_date',
        'end_date',
    ];
}
