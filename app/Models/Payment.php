<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'company_id',
        'payment_number',
        'amount',
        'payment_mode',
        'transaction_reference',
        'payment_date',
        'payment_time',
        'status',
        'note',
        'is_post_dated',
        'post_date',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'post_date' => 'date',
        'payment_time' => 'string',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
