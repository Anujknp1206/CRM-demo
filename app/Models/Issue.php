<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $fillable = [
        'company_id',
        'department_id',
        'bom_id',
        'employee_id',
        'issue_no',
        'issue_date',
        'issue_time',
        'status',
        'remark'
    ];

    public function items()
    {
        return $this->hasMany(IssueItem::class);
    }
    public function bom()
    {
        return $this->belongsTo(Bom::class);
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function hasAvailableStock()
    {
        foreach ($this->items as $item) {
            if ($item->pending_qty > 0) {
                $available = \App\Models\Stock::where([
                    'company_id' => $this->company_id,
                    'item_id' => $item->item_id,
                    'brand_id' => $item->brand_id,
                    'condition_id' => $item->condition_id,
                    'location_id' => $item->location_id,
                ])->sum('quantity');

                if ($available > 0) {
                    return true;
                }
            }
        }
        return false;
    }
    public function getDynamicStatusAttribute()
    {
        $totalItems = $this->items->count();

        if ($totalItems == 0)
            return 'pending';

        $issuedCount = $this->items->where('issued_qty', '>', 0)->count();
        $fullyIssuedCount = $this->items->where('pending_qty', 0)->count();

        if ($issuedCount == 0) {
            return 'pending'; // 🔴 nothing issued
        }

        if ($fullyIssuedCount == $totalItems) {
            return 'issued'; // 🟢 all done
        }

        return 'partial'; // 🟡 mixed
    }
    public function isBomFullyCompleted()
    {
        if (!$this->bom_id)
            return false;

        $bomItems = \App\Models\BomItem::where('bom_id', $this->bom_id)->get();

        foreach ($bomItems as $bomItem) {

            $issued = IssueItem::whereHas('issue', function ($q) {
                $q->where('bom_id', $this->bom_id);
            })
                ->where('bom_item_id', $bomItem->id)
                ->sum('issued_qty');

            if ($issued < $bomItem->quantity) {
                return false; // still pending
            }
        }

        return true;
    }
    public function isFullyReturned()
    {
        foreach ($this->items as $item) {

            $returned = IssueReturn::where([
                'issue_id' => $this->id,
                'item_id' => $item->item_id,
                'brand_id' => $item->brand_id,
                'condition_id' => $item->condition_id,
                'location_id' => $item->location_id,
            ])->sum('return_qty');

            if ($returned < $item->issued_qty) {
                return false;
            }
        }
        return true;
    }
}
