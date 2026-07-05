<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'company_id',
        'quotation_id',
        'lead_id',
        'user_id',
        'assigned_user_id',
        'order_date',
        'po_number',
        'po_date',
        'pi_number',
        'pi_date',
        'delivery_date',
        'contact_person',
        'delivery_address',
        'remark',
        'hi_remark',
        'terms_conditions',
        'hi_terms_conditions',
        'total_amount',
        'discount',
        'tax',
        'tax_amount',
        'final_amount',
        'status',
        'paid_amount',
        'due_amount',
        'currency',
        'conversion_rate',
        'payment_status'
    ];

    protected $casts = [
        'order_date' => 'date',
        'po_date' => 'date',
        'pi_date' => 'date',
        'delivery_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class)
            ->orderBy('sort_order');
    }
    public function getCalculatedPaidAmountAttribute()
    {
        return $this->payments()
            ->whereIn('status', [
                'completed',
                'partial'
            ])
            ->sum('amount');
    }
    public function getCalculatedPaymentStatusAttribute()
    {
        $paid = $this->calculated_paid_amount;

        if ($paid <= 0) {

            return 'unpaid';
        }

        if ($paid >= $this->final_amount) {

            return 'paid';
        }

        return 'partial';
    }
    public function getCalculatedDueAmountAttribute()
    {
        return max(
            $this->final_amount -
            $this->calculated_paid_amount,
            0
        );
    }

    public function files()
    {
        return $this->hasMany(OrderFile::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function boms()
    {
        return $this->hasMany(Bom::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    protected $appends = [
        'customer_name',
        'email',
        'mobile'
    ];
    public function getCustomerNameAttribute()
    {
        return optional($this->quotation?->lead?->customer)->name;
    }
    public function getCurrencySymbolAttribute()
    {
        return match ($this->currency) {
            'INR' => '₹',
            'USD' => '$',
            'EURO', 'EUR' => '€',
            'GBP' => '£',
            default => $this->currency, // fallback
        };
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function getEmailAttribute()
    {
        return optional($this->quotation?->lead?->customer)->email;
    }

    public function getMobileAttribute()
    {
        return optional(
            $this->quotation?->lead?->customer?->primaryPhone
        )->phone;
    }
    public function getProgressPercentAttribute()
    {
        /*
        -----------------------------------------
        IF ORDER DISPATCHED
        -----------------------------------------
        */
        if ($this->status === 'dispatched') {
            return 100;
        }

        /*
        -----------------------------------------
        PART 1
        BOM + ISSUE READINESS
        TOTAL = 30%
        -----------------------------------------
        */

        $readinessProgress = 0;

        $boms = $this->boms;

        /*
        -----------------------------------------
        NO BOM
        -----------------------------------------
        */
        if ($boms->isEmpty()) {
            return 0;
        }

        /*
        -----------------------------------------
        BOM CREATED = 10%
        -----------------------------------------
        */
        $readinessProgress += 10;

        /*
        -----------------------------------------
        TOTAL BOM ITEM QTY
        -----------------------------------------
        */
        $totalBomItems = $boms->flatMap
            ->items
            ->sum('quantity');

        /*
        -----------------------------------------
        BOM ITEMS ADDED = 10%
        -----------------------------------------
        */
        if ($totalBomItems > 0) {
            $readinessProgress += 10;
        }

        /*
        -----------------------------------------
        ISSUED QTY
        -----------------------------------------
        */
        $issuedQty = \App\Models\IssueItem::whereHas(
            'issue',
            function ($q) {

                $q->whereIn(
                    'bom_id',
                    $this->boms->pluck('id')
                );

            }
        )->sum('issued_qty');

        /*
        -----------------------------------------
        ISSUE PROGRESS = 10%
        -----------------------------------------
        */
        if ($totalBomItems > 0) {

            $issuePart =
                ($issuedQty / $totalBomItems) * 10;

            $readinessProgress += $issuePart;
        }

        /*
        -----------------------------------------
        CAP READINESS TO 30
        -----------------------------------------
        */
        $readinessProgress = min(
            round($readinessProgress, 2),
            30
        );

        /*
        -----------------------------------------
        PART 2
        PRODUCTION PROGRESS
        TOTAL = 70%
        -----------------------------------------
        */

        $items = $this->items;

        if ($items->isEmpty()) {
            return $readinessProgress;
        }

        $totalItemProgress = 0;

        /*
        -----------------------------------------
        LOOP ORDER ITEMS
        -----------------------------------------
        */
        foreach ($items as $item) {

            /*
            -----------------------------------------
            GET UNIQUE PARTS
            -----------------------------------------
            */
            $parts = $item->bomItems
                ->pluck('part')
                ->filter()
                ->unique('id');

            /*
            -----------------------------------------
            TOTAL PART WEIGHT
            -----------------------------------------
            */
            $totalWeight = $parts->sum('weightage');

            if ($totalWeight <= 0) {
                continue;
            }

            $itemProgress = 0;

            /*
            -----------------------------------------
            CALCULATE WEIGHTED PART PROGRESS
            IMPORTANT FIX 🔥
            -----------------------------------------
            */
            foreach ($parts as $part) {

                /*
                -----------------------------------------
                ITEM SPECIFIC PART PROGRESS
                -----------------------------------------
                */
                $partProgress =
                    $part->getProgressForOrderItem(
                        $item->id
                    );

                /*
                -----------------------------------------
                WEIGHTED CALCULATION
                -----------------------------------------
                */
                $itemProgress += (
                    $partProgress
                    *
                    ($part->weightage ?? 0)
                );
            }

            /*
            -----------------------------------------
            NORMALIZE ITEM PROGRESS
            -----------------------------------------
            */
            $itemProgress =
                $itemProgress / $totalWeight;

            /*
            -----------------------------------------
            ADD TO TOTAL
            -----------------------------------------
            */
            $totalItemProgress += $itemProgress;
        }

        /*
        -----------------------------------------
        AVERAGE ALL ORDER ITEMS
        -----------------------------------------
        */
        $averageItemProgress =
            $totalItemProgress / max(
                $items->count(),
                1
            );

        /*
        -----------------------------------------
        CONVERT TO 70%
        -----------------------------------------
        */
        $stageProgress =
            ($averageItemProgress / 100) * 70;

        /*
        -----------------------------------------
        FINAL ORDER PROGRESS
        -----------------------------------------
        */
        $progress =
            $readinessProgress + $stageProgress;

        /*
        -----------------------------------------
        FINAL CAP
        -----------------------------------------
        */
        $progress = round($progress, 2);

        if (
            $readinessProgress >= 30 &&
            $averageItemProgress >= 100
        ) {
            return 100;
        }

        return min($progress, 99);
    }
    public function getProgressDetailsAttribute()
    {
        $boms = $this->boms;

        /*
        |--------------------------------------------------------------------------
        | NO BOM
        |--------------------------------------------------------------------------
        */
        if ($boms->isEmpty()) {

            return collect([
                "BOM Items : 0",
                "Issued Qty : 0",
                "Production : 0%",
                "Status : No BOM Created",
            ])->implode(PHP_EOL);
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL BOM ITEMS
        |--------------------------------------------------------------------------
        */
        $totalItems = $boms
            ->flatMap(fn($bom) => $bom->items)
            ->sum('quantity');

        /*
        |--------------------------------------------------------------------------
        | BOM IDS
        |--------------------------------------------------------------------------
        */
        $bomIds = $boms->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | TOTAL ISSUED QTY
        |--------------------------------------------------------------------------
        */
        $issuedQty = \App\Models\IssueItem::query()
            ->whereHas('issue', function ($q) use ($bomIds) {

                $q->whereIn('bom_id', $bomIds);

            })
            ->sum('issued_qty');

        /*
        |--------------------------------------------------------------------------
        | PRODUCTION PROGRESS
        | SAME LOGIC AS MAIN PROGRESS
        |--------------------------------------------------------------------------
        */
        $items = $this->items;

        $totalItemProgress = 0;

        $processedItems = 0;

        foreach ($items as $item) {

            /*
            |--------------------------------------------------------------------------
            | UNIQUE PARTS
            |--------------------------------------------------------------------------
            */
            $parts = $item->bomItems
                ->pluck('part')
                ->filter()
                ->unique('id');

            /*
            |--------------------------------------------------------------------------
            | TOTAL PART WEIGHT
            |--------------------------------------------------------------------------
            */
            $totalWeight = $parts->sum('weightage');

            if ($totalWeight <= 0) {
                continue;
            }

            $itemProgress = 0;

            /*
            |--------------------------------------------------------------------------
            | CALCULATE PART PROGRESS
            |--------------------------------------------------------------------------
            */
            foreach ($parts as $part) {

                $partProgress = $part->getProgressForOrderItem(
                    $item->id
                );

                $itemProgress += (
                    $partProgress *
                    ($part->weightage ?? 0)
                );
            }

            /*
            |--------------------------------------------------------------------------
            | NORMALIZE
            |--------------------------------------------------------------------------
            */
            $itemProgress = $itemProgress / $totalWeight;

            /*
            |--------------------------------------------------------------------------
            | ADD TO TOTAL
            |--------------------------------------------------------------------------
            */
            $totalItemProgress += $itemProgress;

            $processedItems++;
        }

        /*
        |--------------------------------------------------------------------------
        | FINAL STAGE AVG
        |--------------------------------------------------------------------------
        */
        $stageAvg = $processedItems > 0
            ? round($totalItemProgress / $processedItems, 2)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */
        if ($this->status === 'dispatched') {

            $status = 'Dispatched';

        } elseif ($stageAvg <= 0) {

            $status = 'Not Started';

        } elseif ($stageAvg < 100) {

            $status = 'In Progress';

        } else {

            $status = 'Completed';
        }

        /*
        |--------------------------------------------------------------------------
        | FINAL OUTPUT
        |--------------------------------------------------------------------------
        */
        return collect([

            "BOM Items : {$totalItems}",

            "Issued Qty : {$issuedQty}",

            "Production : {$stageAvg}%",

            "Status : {$status}",

        ])->implode(PHP_EOL);
    }
    const STATUSES = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'planning' => 'Planning',
        'in_production' => 'In Production',
        'on_hold' => 'On Hold',
        'delayed' => 'Delayed',
        'ready' => 'Ready',
        'dispatched' => 'Dispatched',
        'cancelled' => 'Cancelled',
    ];
}
