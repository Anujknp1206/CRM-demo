<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'lead_code',
        'company_id',
        'customer_id',
        'purpose',
        'remark',
        'message',
        'reference',
        'status',
        'created_by',
        'created_at'
    ];
    protected $appends = [
        'customer_name',
        'mobile'
    ];

    public function getCustomerNameAttribute()
    {
        return optional($this->customer)->name;
    }

    public function getMobileAttribute()
    {
        return optional(
            $this->customer?->primaryPhone
        )->phone;
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function followups()
    {
        return $this->hasMany(Followup::class);
    }
    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function latestFollowup()
    {
        return $this->hasOne(Followup::class)->latestOfMany();
    }


}
