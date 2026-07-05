<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Issue;

class IssueReturn extends Model
{
    protected $fillable = [
        'issue_id',
        'remark',
        'return_date'
    ];

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    public function items()
    {
        return $this->hasMany(IssueReturnItem::class, 'return_id');
    }
}