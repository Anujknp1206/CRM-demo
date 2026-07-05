<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bom extends Model
{
    protected $fillable = [
        'company_id',
        'order_id',
        'order_item_id',
        'bom_number',
        'remarks',
        'hi_remarks',
        'status',
        'delivery_date',
        'created_by',
        'incharge_department_id',
        'supervisor_id',
        'review_department_id',
        'checked_by',
        'priority_id',
        'shift_id'

    ];
    protected $appends = ['delivery_date_formatted'];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function parts()
    {
        return $this->hasMany(BomPart::class);
    }
    public function getTotalItemsAttribute()
    {
        return (int) $this->items->sum('quantity'); // 🔥 cast to int
    }
    public function syncStatusFromIssues()
    {
        $bomItems = $this->items;

        $totalItems = $bomItems->count();

        $fullyIssuedCount = 0;
        $anyIssued = false;

        foreach ($bomItems as $bomItem) {

            $issuedQty = \App\Models\IssueItem::whereHas('issue', function ($q) {
                $q->where('bom_id', $this->id);
            })
                ->where('bom_item_id', $bomItem->id)
                ->sum('issued_qty');

            if ($issuedQty > 0) {
                $anyIssued = true;
            }

            if ($issuedQty >= $bomItem->quantity) {
                $fullyIssuedCount++;
            }
        }

        if (!$anyIssued) {
            $this->update(['status' => 'draft']);
        } elseif ($fullyIssuedCount < $totalItems) {
            $this->update(['status' => 'in_progress']);
        } else {
            $this->update(['status' => 'completed']);
        }
    }
    public function getIssuedItemsCountAttribute()
    {
        return (int) \App\Models\IssueItem::whereHas('issue', function ($q) {
            $q->where('bom_id', $this->id);
        })->sum('issued_qty'); // 🔥 cast to int
    }
    public function hasIssues()
    {
        return \App\Models\Issue::where('bom_id', $this->id)->exists();
    }

    public function hasIssuedItems()
    {
        return \App\Models\IssueItem::whereHas('issue', function ($q) {
            $q->where('bom_id', $this->id);
        })->sum('issued_qty') > 0;
    }
    public function getProgressPercentAttribute()
    {
        if ($this->total_items == 0)
            return 0;

        return round(($this->issued_items_count / $this->total_items) * 100);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);

    }
    public function getDeliveryDateFormattedAttribute()
    {
        return $this->delivery_date
            ? \Carbon\Carbon::parse($this->delivery_date)->format('d/m/Y')
            : null;
    }
    public function supervisor()
    {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }
    public function checker()
    {
        return $this->belongsTo(Employee::class, 'checked_by');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'incharge_department_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }
    public function items()
    {
        return $this->hasMany(BomItem::class);
    }
    public function getProductionProgressAttribute()
    {
        $order = $this->order;

        if (!$order) {
            return 0;
        }

        $orderItems = $order->items;

        if ($orderItems->isEmpty()) {
            return 0;
        }

        $totalProgress = 0;

        $processedItems = 0;

        /*
        -----------------------------------------
        LOOP ORDER ITEMS
        -----------------------------------------
        */

        foreach ($orderItems as $orderItem) {

            $parts = $orderItem->bomItems
                ->pluck('part')
                ->filter()
                ->unique('id');

            $totalWeight = $parts->sum('weightage');

            if ($totalWeight <= 0) {
                continue;
            }

            $itemProgress = 0;

            /*
            -----------------------------------------
            CALCULATE PART PROGRESS
            -----------------------------------------
            */

            foreach ($parts as $part) {

                $partProgress =
                    $part->getProgressForOrderItem(
                        $orderItem->id
                    );

                $itemProgress += (
                    $partProgress *
                    ($part->weightage ?? 0)
                );
            }

            /*
            -----------------------------------------
            NORMALIZE
            -----------------------------------------
            */

            $itemProgress =
                $itemProgress / $totalWeight;

            $totalProgress += $itemProgress;

            $processedItems++;
        }

        /*
        -----------------------------------------
        FINAL AVG
        -----------------------------------------
        */

        if ($processedItems <= 0) {
            return 0;
        }

        return round(
            $totalProgress / $processedItems
        );
    }
    public function orderItem()
    {
        return $this->belongsTo(
            OrderItem::class,
            'order_item_id'
        );
    }
    public function getProductionDetailsAttribute()
    {
        $details = '';

        /*
        -----------------------------------------
        GET ORDER ITEMS
        -----------------------------------------
        */

        $orderItems = $this->order?->items;

        if (!$orderItems || $orderItems->isEmpty()) {

            return "
            <div>No Order Items Found</div>
        ";
        }

        /*
        -----------------------------------------
        LOOP ALL ORDER ITEMS
        -----------------------------------------
        */

        foreach ($orderItems as $orderItem) {

            $itemProgress = 0;

            /*
            -----------------------------------------
            GET PARTS
            -----------------------------------------
            */

            $parts = $orderItem->bomItems
                ->pluck('part')
                ->filter()
                ->unique('id')
                ->values();

            $totalWeight = $parts->sum('weightage');

            /*
            -----------------------------------------
            CALCULATE PROGRESS
            -----------------------------------------
            */

            if ($totalWeight > 0) {

                foreach ($parts as $part) {

                    $partProgress =
                        $part->getProgressForOrderItem(
                            $orderItem->id
                        );

                    $itemProgress += (
                        $partProgress *
                        ($part->weightage ?? 0)
                    );
                }

                $itemProgress =
                    round($itemProgress / $totalWeight);
            }

            /*
            -----------------------------------------
            ITEM NAME
            -----------------------------------------
            */

            $itemName = trim(
                $orderItem->item_name
                ?? 'Unnamed Item'
            );

            /*
            -----------------------------------------
            ALWAYS APPEND
            -----------------------------------------
            */

            $details .= "
            <div style='
                margin-bottom:8px;
                white-space:nowrap;
            '>

                <span style='font-weight:600;'>
                    {$itemName}
                </span>

                :

                <span style='color:#ffc107;'>
                    {$itemProgress}%
                </span>

            </div>
        ";
        }

        return "
        <div style='
            min-width:260px;
        '>
            {$details}
        </div>
    ";
    }
}