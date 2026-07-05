<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;
use App\Models\Bom;
use App\Models\BomItem;
use App\Models\Brand;
use App\Models\Company;
use App\Models\Condition;
use App\Models\Department;
use App\Models\Employee;
use App\Models\IssueReturn;
use App\Models\Issue;
use App\Models\IssueReturnItem;
use App\Models\Item;
use App\Models\Location;
use App\Models\Project;
use App\Models\IssueItem;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Setting;
use App\Models\Stock;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class IssueController extends Controller
{

    public function index(Company $company)
    {
        $issues = Issue::with(['project', 'items', 'bom'])
            ->where('company_id', $company->id)
            ->latest()
            ->get();

        $title = $company->company_name . " - Issue Item";
        $label = 'Issue Item';
        $nextIssueId = (Issue::where('company_id', $company->id)->max('id') ?? 0) + 1;

        $nextIssueNumber = $company->initials()
            . '-ISS-'
            . now()->format('Ymd')
            . '-'
            . str_pad($nextIssueId, 3, '0', STR_PAD_LEFT);
        return view('company.store.issues.index', [
            'company' => $company,
            'projects' => Project::where('company_id', $company->id)->get(),
            'departments' => Department::where('company_id', $company->id)->get(),
            'employees' => Employee::where('company_id', $company->id)->get(),
            'items' => Item::where('company_id', $company->id)->get(),
            'locations' => Location::where('company_id', $company->id)->get(),
            'brands' => Brand::where('company_id', $company->id)->get(),
            'boms' => Bom::with('items')->where('company_id', $company->id)->get(),
            'issues' => $issues,
            'title' => $title,
            'label' => $label,
            'nextIssueNumber' => $nextIssueNumber,
        ]);
    }
    public function getBomIssues(Company $company, Bom $bom)
    {
        /*
        |--------------------------------------------------------------------------
        | LOAD RELATIONS
        |--------------------------------------------------------------------------
        */

        $bom->load([
            'items.item',
            'items.employee',
            'items.department'
        ]);

        /*
        |--------------------------------------------------------------------------
        | TOTAL BOM REQUIRED QTY
        |--------------------------------------------------------------------------
        */

        $totalBomItems = (float) $bom->items->sum('quantity');

        /*
        |--------------------------------------------------------------------------
        | GROUP BY EMPLOYEE
        |--------------------------------------------------------------------------
        */

        $employees = $bom->items
            ->filter(fn($item) => $item->employee_id)
            ->groupBy('employee_id');

        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE DATA
        |--------------------------------------------------------------------------
        */

        $employeeData = $employees->map(function ($bomItems) use ($bom) {

            $firstBomItem = $bomItems->first();

            /*
            |--------------------------------------------------------------------------
            | GROUP SAME ITEMS
            |--------------------------------------------------------------------------
            |
            | Same item assigned multiple times
            | to same employee => merge into one row
            |
            */

            $groupedItems = $bomItems->groupBy('item_id');

            $items = [];

            foreach ($groupedItems as $itemId => $sameItems) {

                $firstItem = $sameItems->first();

                /*
                |--------------------------------------------------------------------------
                | REQUESTED QTY
                |--------------------------------------------------------------------------
                */

                $requestedQty = (float) $sameItems->sum('quantity');

                /*
                |--------------------------------------------------------------------------
                | BOM ITEM IDS
                |--------------------------------------------------------------------------
                */

                $bomItemIds = $sameItems->pluck('id');

                /*
                |--------------------------------------------------------------------------
                | ISSUE ITEMS
                |--------------------------------------------------------------------------
                */

                $issueItems = IssueItem::with([
                    'brand',
                    'condition'
                ])
                    ->whereHas('issue', function ($q) use ($bom) {

                        $q->where('bom_id', $bom->id);

                    })
                    ->whereIn('bom_item_id', $bomItemIds)
                    ->get();

                /*
                |--------------------------------------------------------------------------
                | TOTAL ISSUED
                |--------------------------------------------------------------------------
                */

                $issuedQty = (float) $issueItems->sum('issued_qty');

                /*
                |--------------------------------------------------------------------------
                | PENDING
                |--------------------------------------------------------------------------
                */

                $pendingQty = max(
                    0,
                    $requestedQty - $issuedQty
                );

                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */

                if ($issuedQty <= 0) {

                    $status = 'Pending';

                } elseif ($pendingQty <= 0) {

                    $status = 'Issued';

                } else {

                    $status = 'Partial';
                }

                /*
                |--------------------------------------------------------------------------
                | LAST ISSUE ITEM
                |--------------------------------------------------------------------------
                */

                $lastIssueItem = $issueItems->sortByDesc('id')->first();

                /*
                |--------------------------------------------------------------------------
                | ITEM DATA
                |--------------------------------------------------------------------------
                */

                $items[] = [

                    'item_id' => $itemId,

                    'item_name' =>
                        $firstItem->item->name ?? '-',

                    'brand_name' =>
                        $lastIssueItem?->brand?->name ?? '-',

                    'condition_name' =>
                        $lastIssueItem?->condition?->name ?? '-',

                    'requested_qty' =>
                        $requestedQty,

                    'issued_qty' =>
                        $issuedQty,

                    'pending_qty' =>
                        $pendingQty,

                    'status' =>
                        $status
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | SORT ITEMS
            |--------------------------------------------------------------------------
            */

            $items = collect($items)
                ->sortBy('item_name')
                ->values();

            /*
            |--------------------------------------------------------------------------
            | RETURN EMPLOYEE
            |--------------------------------------------------------------------------
            */

            return [

                'employee_id' =>
                    $firstBomItem->employee_id,

                'employee_name' => trim(
                    ($firstBomItem->employee->first_name ?? '') . ' ' .
                    ($firstBomItem->employee->last_name ?? '')
                ),

                'department' =>
                    $firstBomItem->department->name ?? '-',

                'total_assigned_qty' =>
                    collect($items)->sum('requested_qty'),

                'total_issued_qty' =>
                    collect($items)->sum('issued_qty'),

                'total_pending_qty' =>
                    collect($items)->sum('pending_qty'),

                'items' =>
                    $items
            ];

        })->values();

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'has_issues' =>
                $employeeData->count() > 0,

            'total_bom_items' =>
                $totalBomItems,

            'employees' =>
                $employeeData
        ]);
    }
    public function getBomItems(Request $request, Company $company)
    {
        $bom = Bom::with(['items', 'order'])->findOrFail($request->bom_id);

        $items = $bom->items->map(function ($item) use ($company) {

            // 🔥 Total issued for this BOM item
            $issuedQty = IssueItem::whereHas('issue', function ($q) use ($company, $item) {
                $q->where('company_id', $company->id)
                    ->where('bom_id', $item->bom_id);
            })
                ->where('bom_item_id', $item->id)
                ->sum('issued_qty');

            // 🔥 Remaining qty
            $remaining = max($item->quantity - $issuedQty, 0);

            return [
                'bom_item_id' => $item->id,
                'item_id' => $item->item_id,
                'quantity' => $remaining, // for UI input
                'original_qty' => $item->quantity,
                'issued_qty' => $issuedQty,
                'remaining_qty' => $remaining
            ];
        });

        // 🔥 Check if ANY item still pending
        $hasPending = $items->sum('remaining_qty') > 0;

        return response()->json([
            'order_id' => $bom->order_id,
            'order_number' => $bom->order->order_number ?? '-',

            // ✅ Only send pending items to UI
            'items' => $items
                ->filter(fn($i) => $i['remaining_qty'] > 0)
                ->values(),

            // 🔥 IMPORTANT FLAG
            'has_pending' => $hasPending
        ]);
    }
    public function getLocationsByItem(Request $request, Company $company)
    {
        $stocks = Stock::where('company_id', $company->id)
            ->where('item_id', $request->item_id)
            ->where('brand_id', $request->brand_id)
            ->where('condition_id', $request->condition_id)
            ->with('location')
            ->get();

        $stockMap = [];

        foreach ($stocks as $s) {
            $stockMap[$s->location_id] = $s->quantity;
        }

        return response()->json($stockMap);
    }
    public function create($company)
    {
        // 🔹 Company (if using route model binding adjust accordingly)
        $company = Company::findOrFail($company);
        $title = $company->company_name . " - Issue Item";
        $label = 'Create Issue';

        // 🔹 Get next issue number (same logic you used)
        $lastIssue = Issue::where('company_id', $company->id)->latest()->first();

        $nextIssueNumber = $lastIssue
            ? 'ISS-' . str_pad(($lastIssue->id + 1), 5, '0', STR_PAD_LEFT)
            : 'ISS-00001';

        // 🔹 Required dropdown data (same as your modal)
        $departments = Department::where('company_id', $company->id)->get();

        $employees = Employee::where('company_id', $company->id)->get();

        $boms = Bom::where('company_id', $company->id)->get();

        $items = Item::where('company_id', $company->id)->get();

        $brands = Brand::where('company_id', $company->id)->get();
        $units = Unit::where('company_id', $company->id)->get();
        $conditions = Condition::where('company_id', $company->id)->get();

        $locations = Location::where('company_id', $company->id)->get();

        // 🔹 Return create blade
        return view('company.store.issues.create', compact(
            'company',
            'nextIssueNumber',
            'departments',
            'employees',
            'boms',
            'items',
            'brands',
            'conditions',
            'locations',
            'title',
            'units',
            'label'
        ));
    }
    public function store(Request $request, Company $company)
    {
        try {
            $request->validate(
                [

                    'bom_id' => 'required|exists:boms,id',

                    'items.*.item_id' => 'required|exists:items,id',

                    'items.*.brand_id' => 'required|exists:brands,id',

                    'items.*.condition_id' => 'required|exists:conditions,id',

                    'items.*.location_id' => 'required|exists:locations,id',

                    'items.*.unit_id' => 'required|exists:units,id',

                    'items.*.quantity' => 'required|numeric|min:1',

                    'department_id' => 'nullable|exists:departments,id',

                    'employee_id' => 'nullable|exists:employees,id',

                    'issue_time' => 'required',

                ],

                [

                    'items.*.brand_id.required' =>
                        'Please select brand for all items.',

                    'items.*.condition_id.required' =>
                        'Please select condition for all items.',

                    'items.*.location_id.required' =>
                        'Please select location for all items.',

                    'items.*.unit_id.required' =>
                        'Please select unit for all items.',

                    'items.*.quantity.required' =>
                        'Please enter quantity for all items.',

                ]
            );
        } catch (\Illuminate\Validation\ValidationException $e) {

            \Log::error($e->errors()); // ✅ correct

            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        }
        $items = [];

        foreach (($request->bom_item_id ?? []) as $i => $bomItemId) {

            $items[] = [
                'bom_item_id' => $bomItemId,
                'item_id' => $request->item_id[$i] ?? null,
                'quantity' => $request->issue_qty[$i] ?? 0,
                'brand_id' => $request->brand_id[$i] ?? null,
                'condition_id' => $request->condition_id[$i] ?? null,
                'location_id' => $request->location_id[$i] ?? null,
                'unit_id' => $request->unit_id[$i] ?? null,
            ];
        }

        $request->merge([
            'items' => $items
        ]);
        $issue = null;

        DB::transaction(function () use ($request, $company, &$issue) {
            $request->merge([

                'issue_date' => Carbon::createFromFormat(
                    'd/m/Y',
                    $request->issue_date
                )->format('Y-m-d'),

                'issue_time' => Carbon::createFromFormat(
                    'h:i A',
                    $request->issue_time
                )->format('H:i:s')

            ]);
            $companyInitials = $company->initials(); // already exists ✅
            $userId = Auth::id();
            $date = Carbon::parse($request->issue_date)->format('ymd');
            $nextId = (Issue::max('id') ?? 0) + 1;
            $issueNo = $companyInitials . '#' . $userId . '/' . $date . '/' . $nextId;
            $issue = Issue::create([
                'company_id' => $company->id,
                'bom_id' => $request->bom_id,
                'department_id' => $request->department_id,
                'employee_id' => $request->employee_id,
                'issue_date' => $request->issue_date,
                'issue_no' => $issueNo,
                'issue_time' => $request->issue_time,
                'status' => 'draft',
                'remark' => $request->remark,
            ]);

            $finalStatus = 'completed';

            foreach ($request->items as $row) {
                // 🔥 GET BOM ITEM (VERY IMPORTANT)
                $bomItem = BomItem::find($row['bom_item_id']);

                if (!$bomItem) {
                    throw new \Exception("Invalid BOM item mapping");
                }
                $bomQty = BomItem::where(
                    'bom_id',
                    $request->bom_id
                )
                    ->where(
                        'item_id',
                        $row['item_id']
                    )
                    ->sum('quantity');
                $issueQty = $row['quantity'];

                $stock = Stock::where([
                    'company_id' => $company->id,
                    'item_id' => $row['item_id'],
                    'brand_id' => $row['brand_id'],
                    'condition_id' => $row['condition_id'],
                    'location_id' => $row['location_id'],
                ])->lockForUpdate()->first();

                if (!$stock) {

                    IssueItem::create([
                        'issue_id' => $issue->id,
                        'item_id' => $row['item_id'],
                        'brand_id' => $row['brand_id'],
                        'bom_item_id' => $row['bom_item_id'],
                        'condition_id' => $row['condition_id'],
                        'location_id' => $row['location_id'],
                        'unit_id' => $row['unit_id'],
                        'requested_qty' => $bomQty,   // ✅ FIXED
                        'issued_qty' => 0,
                        'pending_qty' => $bomQty,
                    ]);

                    $finalStatus = 'partial';
                    continue;
                }

                $available = $stock->quantity;

                // ✅ ISSUE ONLY WHAT ADMIN ENTERS
                $issued = min($available, $issueQty);

                // ✅ PENDING FROM BOM (NOT ADMIN INPUT)
                // 🔥 TOTAL PREVIOUSLY ISSUED
                $alreadyIssued = IssueItem::whereHas('issue', function ($q) use ($request, $company) {

                    $q->where('company_id', $company->id)
                        ->where('bom_id', $request->bom_id);

                })
                    ->where('item_id', $row['item_id'])
                    ->sum('issued_qty');

                // 🔥 REMAINING BEFORE THIS ISSUE
                $remainingBefore = max($bomQty - $alreadyIssued, 0);

                // 🔥 FINAL ISSUE (cannot exceed remaining)
                $issued = min($available, $issueQty, $remainingBefore);

                // 🔥 FINAL PENDING
                $pending = max($remainingBefore - $issued, 0);

                IssueItem::create([
                    'issue_id' => $issue->id,
                    'item_id' => $row['item_id'],
                    'brand_id' => $row['brand_id'],
                    'condition_id' => $row['condition_id'],
                    'location_id' => $row['location_id'],
                    'unit_id' => $row['unit_id'],
                    'bom_item_id' => $row['bom_item_id'],
                    'requested_qty' => $remainingBefore, // ✅ REAL REQUESTED // 🔥 MAIN FIX
                    'issued_qty' => $issued,
                    'pending_qty' => $pending,
                ]);

                // 🔥 reduce stock
                if ($issued > 0) {
                    $stock->quantity -= $issued;
                    $stock->quantity <= 0 ? $stock->delete() : $stock->save();
                    checkLowStock($stock);
                }

                if ($pending > 0) {
                    $finalStatus = 'partial';
                }
            }

            $issue->update(['status' => $finalStatus]);
        });
        if ($issue->isBomFullyCompleted()) {

            Issue::where('bom_id', $issue->bom_id)
                ->update(['status' => 'completed']);
        }
        if ($issue->bom) {
            $issue->bom->syncStatusFromIssues();
        }
        return redirect()
            ->route('issues.index', $company->id)
            ->with('success', 'Issue created successfully');
    }
    public function edit(Company $company, Issue $issue)
    {
        if ($issue->company_id != $company->id) {
            abort(404);
        }

        $title = $company->company_name . " - Edit Issue";
        $label = 'Edit Issue';

        $departments = Department::where('company_id', $company->id)->get();
        $employees = Employee::where('company_id', $company->id)->get();
        $boms = Bom::where('company_id', $company->id)->get();
        $items = Item::where('company_id', $company->id)->get();
        $brands = Brand::where('company_id', $company->id)->get();
        $conditions = Condition::where('company_id', $company->id)->get();
        $locations = Location::where('company_id', $company->id)->get();

        /*
        Load ALL names needed in edit form
        */
        $issue->load([
            'employee',
            'department',

            'bom',
            'bom.items',

            'items',
            'items.item',
            'items.brand',
            'items.condition',
            'items.location',
            'items.unit'
        ]);
        $issue->items->map(function ($issueItem) use ($company) {

            $stock = \DB::table('stocks')
                ->where('company_id', $company->id)
                ->where('item_id', $issueItem->item_id)
                ->where('brand_id', $issueItem->brand_id)
                ->where('condition_id', $issueItem->condition_id)
                ->sum('quantity');

            // 👇 attach stock value
            $issueItem->available_stock = $stock ?? 0;

            return $issueItem;
        });
        $issue->issue_date = Carbon::parse(
            $issue->issue_date
        )->format('d/m/Y');

        $issue->issue_time = Carbon::parse(
            $issue->issue_time
        )->format('h:i A');

        return view('company.store.issues.edit', compact(
            'company',
            'issue',
            'departments',
            'employees',
            'boms',
            'items',
            'brands',
            'conditions',
            'locations',
            'title',
            'label'
        ));
    }
    public function update(Request $request, Company $company, Issue $issue)
    {
        // dd($request->all());
        /*
        =====================================
        BUILD ITEMS ARRAY FIRST
        =====================================
        */

        $items = [];

        foreach (($request->bom_item_id ?? []) as $i => $bomItemId) {

            $items[] = [

                'bom_item_id' => $bomItemId,

                'item_id' => $request->item_id[$i] ?? null,

                'quantity' => $request->issue_qty[$i] ?? 0,

                'brand_id' => $request->brand_id[$i] ?? null,

                'condition_id' => $request->condition_id[$i] ?? null,

                'location_id' => $request->location_id[$i] ?? null,

                'unit_id' => $request->unit_id[$i] ?? null,
            ];
        }

        /*
 |--------------------------------------------------------------------------
 | REMOVE ZERO QTY ROWS
 |--------------------------------------------------------------------------
 */

        $items = collect($items)

            ->filter(function ($row) {

                return (float) $row['quantity'] > 0;
            })

            ->values()

            ->toArray();

        $request->merge([
            'items' => $items
        ]);

        /*
        =====================================
        VALIDATION
        =====================================
        */

        try {

            $request->validate(

                [

                    'bom_id' => 'required|exists:boms,id',

                    'department_id' => 'nullable|exists:departments,id',

                    'employee_id' => 'nullable|exists:employees,id',

                    'issue_time' => 'required',

                    'items.*.item_id' => 'required|exists:items,id',

                    'items.*.brand_id' => 'required|exists:brands,id',

                    'items.*.condition_id' => 'required|exists:conditions,id',

                    'items.*.location_id' => 'required|exists:locations,id',

                    'items.*.unit_id' => 'required|exists:units,id',

                    'items.*.quantity' => 'required|numeric',
                ],

                [

                    'items.*.brand_id.required' =>
                        'Please select brand for all items.',

                    'items.*.condition_id.required' =>
                        'Please select condition for all items.',

                    'items.*.location_id.required' =>
                        'Please select location for all items.',

                    'items.*.unit_id.required' =>
                        'Please select unit for all items.',

                    'items.*.quantity.required' =>
                        'Please enter quantity for all items.',
                ]
            );

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        }

        /*
        =====================================
        TRANSACTION
        =====================================
        */

        DB::transaction(function () use ($request, $company, $issue) {

            /*
            =====================================
            FORMAT DATE/TIME
            =====================================
            */

            $request->merge([

                'issue_date' => Carbon::createFromFormat(
                    'd/m/Y',
                    $request->issue_date
                )->format('Y-m-d'),

                'issue_time' => Carbon::createFromFormat(
                    'h:i A',
                    $request->issue_time
                )->format('H:i:s')

            ]);

            /*
            =====================================
            STEP 1
            REVERSE OLD ISSUED STOCK
            =====================================
            */

            foreach ($issue->items as $old) {

                if ($old->issued_qty > 0) {

                    $stock = Stock::where([

                        'company_id' => $company->id,

                        'item_id' => $old->item_id,

                        'unit_id' => $old->unit_id,

                        'brand_id' => $old->brand_id,

                        'condition_id' => $old->condition_id,

                        'location_id' => $old->location_id,

                    ])->lockForUpdate()->first();

                    /*
                    =====================================
                    RESTORE STOCK
                    =====================================
                    */

                    if ($stock) {

                        $stock->quantity += $old->issued_qty;

                        $stock->save();

                    } else {

                        Stock::create([

                            'company_id' => $company->id,

                            'item_id' => $old->item_id,

                            'unit_id' => $old->unit_id,

                            'brand_id' => $old->brand_id,

                            'condition_id' => $old->condition_id,

                            'location_id' => $old->location_id,

                            'quantity' => $old->issued_qty,
                        ]);
                    }
                }
            }

            /*
            =====================================
            STEP 2
            DELETE OLD ISSUE ITEMS
            =====================================
            */

            $issue->items()->delete();

            /*
            =====================================
            STEP 3
            APPLY NEW ISSUE
            =====================================
            */

            $finalStatus = 'completed';

            foreach ($request->items as $row) {
                if ((float) $row['quantity'] <= 0) {
                    continue;
                }
                /*
                =====================================
                BOM ITEM
                =====================================
                */

                $bomItem = BomItem::find($row['bom_item_id']);

                if (!$bomItem) {

                    throw new \Exception(
                        "Invalid BOM Item"
                    );
                }

                $bomQty = $bomItem->quantity;

                /*
                =====================================
                GET STOCK
                =====================================
                */

                $stock = Stock::where([

                    'company_id' => $company->id,

                    'item_id' => $row['item_id'],

                    'brand_id' => $row['brand_id'],

                    'unit_id' => $row['unit_id'],

                    'condition_id' => $row['condition_id'],

                    'location_id' => $row['location_id'],

                ])->lockForUpdate()->first();

                /*
                =====================================
                ALREADY ISSUED BY OTHER ISSUES
                =====================================
                */

                $alreadyIssued = IssueItem::whereHas(
                    'issue',
                    function ($q) use ($request, $company, $issue) {

                        $q->where(
                            'company_id',
                            $company->id
                        )
                            ->where(
                                'bom_id',
                                $request->bom_id
                            )
                            ->where(
                                'id',
                                '!=',
                                $issue->id
                            );
                    }
                )
                    ->where(
                        'bom_item_id',
                        $row['bom_item_id']
                    )
                    ->sum('issued_qty');

                /*
                =====================================
                REMAINING ALLOWED
                =====================================
                */

                $remainingBefore = max(
                    $bomQty - $alreadyIssued,
                    0
                );

                /*
                =====================================
                NO STOCK
                =====================================
                */

                if (!$stock) {

                    IssueItem::create([

                        'issue_id' => $issue->id,

                        'item_id' => $row['item_id'],

                        'brand_id' => $row['brand_id'],

                        'condition_id' => $row['condition_id'],

                        'location_id' => $row['location_id'],

                        'unit_id' => $row['unit_id'],

                        'bom_item_id' => $row['bom_item_id'],

                        'requested_qty' => $remainingBefore,

                        'issued_qty' => 0,

                        'pending_qty' => $remainingBefore,
                    ]);

                    $finalStatus = 'partial';

                    continue;
                }

                /*
                =====================================
                AVAILABLE STOCK
                =====================================
                */

                $available = $stock->quantity;

                /*
                =====================================
                ADMIN REQUESTED
                =====================================
                */

                $requestedQty = $row['quantity'];

                /*
                =====================================
                FINAL ISSUE QTY
                =====================================
                */

                $issued = min(
                    $available,
                    $requestedQty,
                    $remainingBefore
                );

                /*
                =====================================
                FINAL PENDING
                =====================================
                */

                $pending = max(
                    $remainingBefore - $issued,
                    0
                );

                /*
                =====================================
                CREATE ISSUE ITEM
                =====================================
                */

                IssueItem::create([

                    'issue_id' => $issue->id,

                    'item_id' => $row['item_id'],

                    'brand_id' => $row['brand_id'],

                    'condition_id' => $row['condition_id'],

                    'location_id' => $row['location_id'],

                    'unit_id' => $row['unit_id'],

                    'bom_item_id' => $row['bom_item_id'],

                    'requested_qty' => $remainingBefore,

                    'issued_qty' => $issued,

                    'pending_qty' => $pending,
                ]);

                /*
                =====================================
                REDUCE STOCK
                =====================================
                */

                if ($issued > 0) {

                    $stock->quantity -= $issued;

                    if ($stock->quantity <= 0) {

                        $stock->delete();

                    } else {

                        $stock->save();

                        checkLowStock($stock);
                    }
                }

                /*
                =====================================
                FINAL STATUS
                =====================================
                */

                if ($pending > 0) {

                    $finalStatus = 'partial';
                }
            }

            /*
            =====================================
            STEP 4
            UPDATE ISSUE HEADER
            =====================================
            */

            $issue->update([

                'bom_id' => $request->bom_id,

                'department_id' => $request->department_id,

                'employee_id' => $request->employee_id,

                'issue_date' => $request->issue_date,

                'issue_time' => $request->issue_time,

                'remark' => $request->remark,

                'status' => $finalStatus,
            ]);

            /*
            =====================================
            STEP 5
            SYNC BOM STATUS
            =====================================
            */

            if ($issue->bom) {

                $issue->bom->syncStatusFromIssues();
            }
        });

        /*
        =====================================
        REDIRECT
        =====================================
        */

        return redirect()
            ->route('issues.index', $company->id)
            ->with(
                'success',
                'Issue updated successfully'
            );
    }
    public function show(Company $company, Issue $issue)
    {
        $issue->load([
            'project',
            'department',
            'employee',
            'items.item',
            'items.brand',
            'items.condition',
            'items.location',
            'items.unit', // ✅ unit relation
        ]);

        // ✅ preload all return items
        $returnItems = IssueReturnItem::whereHas('return', function ($q) use ($issue) {
            $q->where('issue_id', $issue->id);
        })->get();

        return response()->json([
            'id' => $issue->id,
            'issue_no' => $issue->issue_no,
            'issue_date' => \Carbon\Carbon::parse($issue->issue_date)->format('d/m/Y'),
            'issue_time' => \Carbon\Carbon::parse($issue->issue_time)->format('H:i'),

            'status' => $issue->dynamic_status,
            'remark' => $issue->remark,

            'bom_number' => $issue->bom->bom_number ?? '-',
            'department_name' => $issue->department->name ?? '-',
            'employee_name' => ($issue->employee->first_name ?? '') . ' ' . ($issue->employee->last_name ?? ''),

            // ================= ITEMS =================
            'items' => $issue->items->map(function ($row) use ($returnItems) {

                // ✅ include unit in matching (CRITICAL)
                $returnedQty = $returnItems
                    ->where('item_id', $row->item_id)
                    ->where('brand_id', $row->brand_id)
                    ->where('condition_id', $row->condition_id)
                    ->where('location_id', $row->location_id)
                    ->where('unit_id', $row->unit_id) // 🔥 FIX
                    ->sum('return_qty');

                $remainingQty = max($row->issued_qty - $returnedQty, 0);

                return [
                    'item_id' => $row->item_id,
                    'brand_id' => $row->brand_id,
                    'condition_id' => $row->condition_id,
                    'location_id' => $row->location_id,
                    'unit_id' => $row->unit_id, // ✅ send id
    
                    'item_name' => $row->item->name ?? 'N/A',
                    'brand_name' => $row->brand->name ?? '-',
                    'condition_name' => $row->condition->name ?? '-',
                    'location_name' => $row->location->name ?? '-',
                    'unit_name' => $row->unit->name ?? '-', // ✅ send name
    
                    'requested_qty' => $row->requested_qty,
                    'issued_qty' => $row->issued_qty,
                    'pending_qty' => $row->pending_qty,

                    'returned_qty' => $returnedQty,
                    'remaining_qty' => $remainingQty,

                    'return_status' => $remainingQty <= 0
                        ? 'returned'
                        : ($returnedQty > 0 ? 'partial' : 'pending'),
                ];
            }),

            // ================= GLOBAL FLAG =================
            'is_fully_returned' => $issue->items->every(function ($row) use ($returnItems) {

                $returnedQty = $returnItems
                    ->where('item_id', $row->item_id)
                    ->where('brand_id', $row->brand_id)
                    ->where('condition_id', $row->condition_id)
                    ->where('location_id', $row->location_id)
                    ->where('unit_id', $row->unit_id) // 🔥 FIX
                    ->sum('return_qty');

                return $returnedQty >= $row->issued_qty;
            }),
        ]);
    }
    public function checkStock(Request $request, Company $company)
    {
        $query = Stock::where('company_id', $company->id)
            ->where('item_id', $request->item_id)
            ->where('location_id', $request->location_id)
            ->where('unit_id', $request->unit_id); // 🔥 MUST ADD

        // optional filters
        if ($request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->condition_id) {
            $query->where('condition_id', $request->condition_id);
        }

        $available = $query->sum('quantity');

        return response()->json([
            'available' => $available
        ]);
    }
    public function stockDetails(Request $request, Company $company)
    {

        $stocks = Stock::with(
            'unit',
            'location'
        )
            ->where('company_id', $company->id)
            ->where('item_id', $request->item_id)
            ->where('brand_id', $request->brand_id)
            ->where('condition_id', $request->condition_id)
            ->get();

        /* stock exists */
        if ($stocks->count() > 0) {

            return response()->json([

                'units' => $stocks
                    ->pluck('unit')
                    ->unique('id')
                    ->values(),

                'locations' => $stocks
                    ->map(function ($s) {

                        return [
                            'id' => $s->location->id,
                            'name' => $s->location->name,
                            'stock' => $s->quantity
                        ];

                    }),

                'available' => $stocks->sum('quantity'),

                /*
                |--------------------------------------------------------------------------
                | ADD DEFAULT UNIT ID
                |--------------------------------------------------------------------------
                */

                'default_unit_id' =>
                    optional($stocks->first())->unit_id,

                'fallback' => false

            ]);
        }

        /* no stock -> fallback master tables */

        return response()->json([

            'units' => \App\Models\Unit::select(
                'id',
                'name'
            )->get(),

            'locations' => \App\Models\Location
                ::where(
                    'company_id',
                    $company->id
                )
                ->select(
                    'id',
                    'name'
                )
                ->get()
                ->map(function ($l) {

                    return [
                        'id' => $l->id,
                        'name' => $l->name,
                        'stock' => 0
                    ];

                }),

            'available' => 0,

            /*
            |--------------------------------------------------------------------------
            | FALLBACK UNIT
            |--------------------------------------------------------------------------
            */

            'default_unit_id' => null,

            'fallback' => true

        ]);
    }
    public function destroy(Company $company, Issue $issue)
    {
        if ($issue->status === 'completed') {
            return response()->json([
                'status' => false,
                'message' => 'Completed issue cannot be deleted'
            ], 422);
        }
        DB::transaction(function () use ($company, $issue) {

            foreach ($issue->items as $item) {

                // 🔴 Only issued quantity goes back to stock

                if ($item->issued_qty > 0) {

                    $stock = Stock::firstOrNew([
                        'company_id' => $company->id,
                        'item_id' => $item->item_id,
                        'brand_id' => $item->brand_id,
                        'condition_id' => $item->condition_id,
                        'location_id' => $item->location_id,
                    ]);

                    $stock->quantity = ($stock->quantity ?? 0) + $item->issued_qty;
                    $stock->save();
                    checkLowStock($stock);
                }
            }

            // 🔴 Delete issue items
            $issue->items()->delete();

            // 🔴 Delete issue
            $issue->delete();
        });

        return response()->json([
            'status' => true,
            'message' => 'Issue deleted and stock restored successfully'
        ]);
    }
    public function returnItem(Request $request, Company $company)
    {
        DB::transaction(function () use ($request, $company) {

            // ✅ Create ONE return entry (parent)
            $return = IssueReturn::create([
                'issue_id' => $request->issue_id,
                'remark' => $request->remark,
                'return_date' => now(),
            ]);

            foreach ($request->items as $row) {

                // ✅ Skip invalid rows
                if (empty($row['return_qty']) || $row['return_qty'] <= 0) {
                    continue;
                }

                // 🔥 Get issue item (WITH UNIT)
                $issueItem = IssueItem::where([
                    'issue_id' => $request->issue_id,
                    'item_id' => $row['item_id'],
                    'brand_id' => $row['brand_id'],
                    'condition_id' => $row['condition_id'],
                    'location_id' => $row['location_id'],
                    'unit_id' => $row['unit_id'], // ✅ IMPORTANT
                ])->first();

                if (!$issueItem) {
                    continue;
                }

                // 🔥 Already returned qty (WITH UNIT)
                $returnedQty = IssueReturnItem::whereHas('return', function ($q) use ($request) {
                    $q->where('issue_id', $request->issue_id);
                })
                    ->where([
                        'item_id' => $row['item_id'],
                        'brand_id' => $row['brand_id'],
                        'condition_id' => $row['condition_id'],
                        'location_id' => $row['location_id'],
                        'unit_id' => $row['unit_id'], // ✅ IMPORTANT
                    ])
                    ->sum('return_qty');

                $remaining = $issueItem->issued_qty - $returnedQty;

                // ❌ Prevent full duplicate
                if ($remaining <= 0) {
                    throw new \Exception("Item already fully returned");
                }

                // ❌ Prevent over return
                if ($row['return_qty'] > $remaining) {
                    throw new \Exception("Return exceeds remaining qty");
                }

                // ✅ Save RETURN ITEM (WITH UNIT)
                IssueReturnItem::create([
                    'return_id' => $return->id,
                    'item_id' => $row['item_id'],
                    'brand_id' => $row['brand_id'],
                    'condition_id' => $row['condition_id'],
                    'location_id' => $row['location_id'],
                    'unit_id' => $row['unit_id'], // ✅ IMPORTANT
                    'return_qty' => $row['return_qty'],
                ]);

                // ✅ STOCK UPDATE (WITH UNIT)
                $stock = Stock::firstOrNew([
                    'company_id' => $company->id,
                    'item_id' => $row['item_id'],
                    'brand_id' => $row['brand_id'],
                    'condition_id' => $row['condition_id'],
                    'location_id' => $row['location_id'],
                ]);

                $stock->quantity = ($stock->quantity ?? 0) + $row['return_qty'];
                $stock->save();
            }
        });

        return response()->json([
            'success' => true,
            'return_id' => IssueReturn::latest()->first()->id
        ]);
    }
    public function allReturnsSummary(Company $company)
    {
        $returns = IssueReturn::with([
            'items.item',
            'items.brand',
            'items.condition',
            'items.location',
            'items.unit',
            'issue.department',
            'issue.employee'
        ])
            ->whereHas('issue', fn($q) => $q->where('company_id', $company->id))
            ->latest()
            ->get();

        $grouped = $returns->groupBy('issue_id')->map(function ($group) {

            $issue = $group->first()->issue;

            return [
                'issue_id' => $issue->id,
                'issue_no' => $issue->issue_no,
                'return_id' => $group->last()->id,

                // ✅ NEW DATA
                'department' => $issue->department->name ?? '-',
                'employee' => trim(
                    ($issue->employee->first_name ?? '') . ' ' .
                    ($issue->employee->last_name ?? '')
                ),

                // ✅ ITEMS WITH RETURN HISTORY
                'items' => $group->flatMap(function ($return) {

                    return $return->items->map(function ($item) use ($return) {

                        return [
                            'item' => $item->item->name,
                            'brand' => $item->brand->name ?? '-',
                            'condition' => $item->condition->name ?? '-',
                            'location' => $item->location->name ?? '-',
                            'unit' => $item->unit->name ?? '-',

                            'qty' => $item->return_qty,

                            // ✅ RETURN DATE TIME
                            'date' => optional($return->created_at)->format('d/m/Y'),
                            'time' => optional($return->created_at)->format('H:i'),

                            'return_id' => $return->id
                        ];
                    });

                })->values()
            ];

        })->values();

        return response()->json([
            'data' => $grouped
        ]);
    }
    public function printIssue(Company $company, Issue $issue)
    {
        $issue->load([
            'items' => function ($q) {
                $q->where('issued_qty', '>', 0); // 🔥 FILTER HERE
            },
            'items.item',
            'items.brand',
            'items.condition',
            'items.location',
            'items.unit',
            'project',
            'employee',
            'department',
            'bom'
        ]);

        $settings = Setting::first();

        $pdf = Pdf::loadView(
            'company.store.issues.print.issue_slip', // 🔥 new blade
            compact('company', 'issue', 'settings')
        )->setPaper('a4', 'portrait');

        // 🔥 render for footer
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        $canvas = $dompdf->getCanvas();

        // 🔥 page number
        $canvas->page_text(
            520,   // X position
            803,
            "Page {PAGE_NUM} of {PAGE_COUNT}",
            null,
            8,
            [255, 255, 255]
        );

        // 🔥 file name
        $fileName = 'ISSUE-' . $issue->issue_no . '-' . now()->format('Ymd-His') . '.pdf';

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
    public function printReturnSlip(Company $company, $returnId)
    {
        $return = IssueReturn::with([
            'items.item',
            'items.brand',
            'items.condition',
            'items.location',
            'items.unit',
            'issue.project',
            'issue.employee',
            'issue.department',
            'issue.bom'
        ])->findOrFail($returnId);

        $settings = Setting::first();

        $pdf = Pdf::loadView(
            'company.store.issues.print.return_slip', // 🔥 new blade
            compact('return', 'settings', 'company')
        )->setPaper('a4', 'portrait');

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        $canvas = $dompdf->getCanvas();

        $canvas->page_text(
            520,
            808,
            "Page {PAGE_NUM} of {PAGE_COUNT}",
            null,
            7,
            [255, 255, 255]
        );

        $fileName = 'RETURN-' . $return->issue->issue_no . '-' . now()->format('Ymd-His') . '.pdf';

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
}
