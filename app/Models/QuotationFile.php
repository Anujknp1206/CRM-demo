<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationFile extends Model
{
    protected $fillable = [
        'quotation_id',
        'file_name',
        'file_path',
        'uploaded_by'
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
