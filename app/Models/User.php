<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'core_password',
        'joining_date',
        'address',
        'photo',
        'status',
        'created_by',
    ];

    protected $guard_name = 'web';

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'subscription_started_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationship: User created by another user (Vendor)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_user');
    }
    // Leads
    public function createdLeads()
    {
        return $this->hasMany(Lead::class, 'created_by');
    }

    public function assignedLeads()
    {
        return $this->hasMany(Lead::class, 'assigned_user_id');
    }

    // Quotations
    public function createdQuotations()
    {
        return $this->hasMany(Quotation::class, 'user_id');
    }

    public function assignedQuotations()
    {
        return $this->hasMany(Quotation::class, 'assigned_user_id');
    }

    // Orders
    public function createdOrders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function assignedOrders()
    {
        return $this->hasMany(Order::class, 'assigned_user_id');
    }

    // Followups
    public function followups()
    {
        return $this->hasMany(Followup::class, 'managed_by');
    }
    public function getFullMobileAttribute()
    {
        if (!$this->mobile) {
            return '-----';
        }

        $company = $this->companies->first(); // take first company
        $code = optional(optional($company)->country)->phonecode;

        return $code ? '+' . $code . ' ' . $this->mobile : $this->mobile;
    }
}
