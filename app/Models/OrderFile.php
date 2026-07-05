<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderFile extends Model
{
    protected $fillable = [
        'order_id',
        'file_name',
        'file_path',
        'uploaded_by'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
