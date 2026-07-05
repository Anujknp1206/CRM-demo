<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_number',
        'company_id',
        'lead_id',
        'user_id',
        'assigned_user_id',
        'quote_date',
        'pi_number',
        'pi_date',
        'contact_person',
        'office_address',
        'delivery_address',
        'special_clause',
        'hi_special_clause',
        'terms_conditions',
        'hi_terms_conditions',
        'total_amount',
        'discount',
        'tax',
        'tax_amount',
        'final_amount',
        'currency',
        'conversion_rate',
        'status'
    ];
    protected $casts = [
        'quote_date' => 'date',
        'pi_date' => 'date',
    ];
    protected $appends = [
        'customer_name',
        'mobile',
        'email'
    ];

    public function getCustomerNameAttribute()
    {
        return optional($this->lead?->customer)->name;
    }
    public function getCurrencySymbolAttribute()
    {
        return match ($this->currency) {
            'INR' => '₹',
            'USD' => '$',
            'EUR', 'EURO' => '€',
            default => $this->currency,
        };
    }

    public function getEmailAttribute()
    {
        return optional($this->lead?->customer)->email;
    }

    public function getMobileAttribute()
    {
        return optional(
            $this->lead?->customer?->primaryPhone
        )->phone;
    }
    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function files()
    {
        return $this->hasMany(QuotationFile::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
