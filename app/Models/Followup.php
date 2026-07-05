<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Followup extends Model
{

    protected $fillable = [
        'company_id',
        'lead_id',
        'nextactionDate',
        'describeAction',
        'action_id',
        'managed_by',
        'reminder_sent'
    ];
    protected $casts = [
        'nextactionDate' => 'date',
    ];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function action()
    {
        return $this->belongsTo(Action::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'managed_by');
    }
}
