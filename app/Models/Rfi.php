<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rfi extends Model
{
    protected $fillable = [
        'company_id',
        'rfi_code',
        'rfi_date',
        'status',
        'created_by',
        'approved_by',
        'total_amount',
        'remark',
        'notes'
    ];

    public function items()
    {
        return $this->hasMany(RfiItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}