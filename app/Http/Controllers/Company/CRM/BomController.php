<?php

namespace App\Http\Controllers\Company\CRM;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Bom;
use App\Models\BomItem;
use App\Models\BomPart;
use App\Models\Order;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Item;
use App\Models\OrderItem;
use App\Models\Priority;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\Specification;
use App\Models\Unit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
class BomController extends Controller
{
    public function index(Company $company)
    {
        return view('company.crm.boms.index', [
            'company' => $company,
            'title' => 'BOM Management :: All BOM',
            'label' => 'BOM List'
        ]);
    }
    public function details(Company $company, Bom $bom)
    {
        $bom->load([
            'order.quotation.lead.customer.primaryPhone',
            'order.quotation.lead.customer.country',
            'order.items.item',
            'items.item',

            // 🔥 IMPORTANT FIX
            'parts.items.item',
            'parts.items.department',
            'parts.items.employee',
            'parts.items.shift',

            'parts.spec',
            'parts.shift',
            'priority',
            'supervisor',
            'checker'
        ]);

        // ✅ ADD THIS ONLY
        $lead = $bom->order?->quotation?->lead?->customer;
        $bom->custom_mobile = $lead?->full_primary_mobile;

        return response()->json($bom);
    }
    public function ajaxSearch(Request $request, Company $company)
    {
        $search = $request->search;

        $boms = Bom::with(['order'])
            ->where('company_id', $company->id)
            ->where(function ($q) use ($search) {

                $q->where('bom_number', 'LIKE', "%{$search}%")

                    ->orWhereHas('order', function ($qo) use ($search) {
                        $qo->where('order_number', 'LIKE', "%{$search}%");
                    });
            })
            ->limit(20)
            ->get();
        return $boms->map(function ($b) {
            return [
                'id' => $b->id,
                'bom_number' => $b->bom_number,
                'order_number' => $b->order->order_number ?? '',
                'date' => $b->created_at->format('d/m/Y')
            ];
        });
    }
    public function data(Request $request, Company $company)
    {
        $query = Bom::with(['order', 'items.item'])
            ->where('company_id', $company->id);

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('bom_number', 'LIKE', "%{$search}%")

                    ->orWhereHas('order', function ($order) use ($search) {

                        $order->where('order_number', 'LIKE', "%{$search}%");

                    });

            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Show only latest 10 when no filters are applied
        if (
            !$request->filled('search') &&
            !$request->filled('from_date') &&
            !$request->filled('to_date')
        ) {
            $query->latest()->limit(10);
        }

        $boms = $query->latest()->get();

        return view(
            'company.crm.boms.partials.bom_rows',
            compact('boms', 'company')
        )->render();
    }
    public function create(Company $company)
    {
        $orderItems = OrderItem::with([
            'order',
            'item.recipe',
            'machine.recipe',
            'component.recipe'
        ])->get();
        $items = Item::where('company_id', $company->id)->get();
        $units = Unit::where('company_id', $company->id)->get();
        $departments = Department::where('company_id', $company->id)->get();
        $today = Carbon::today();

        $employees = Employee::where('company_id', $company->id)
            ->where('status', 1)
            ->presentToday()
            ->get();
        $priorities = Priority::orderBy('level', 'asc')->get();
        $shifts = Shift::all();
        $nextBomId = (Bom::where('company_id', $company->id)->max('id') ?? 0) + 1;
        $nextBomId = (Bom::where('company_id', $company->id)->max('id') ?? 0) + 1;
        $specifications = Specification::all();

        return view('company.crm.boms.create', compact(
            'company',
            'orderItems',
            'items',
            'units',
            'departments',
            'employees',
            'priorities',
            'shifts',
            'specifications'
        ))->with([
                    'title' => 'BOM Management :: Add BOM',
                    'label' => 'Create BOM'
                ]);
    }
    public function generateBomNumber(Request $request)
    {
        if (!$request->order_id) {
            return response()->json([
                'bom' => 'Select Order First'
            ]);
        }

        /*
        Next BOM ID
        */
        $nextId = (Bom::max('id') ?? 0) + 1;
        $userId = auth()->id() ?? '0';

        /*
        Date
        */
        $date = now()->format('ymd');

        /*
        Get Lead ID via Order
        */
        $leadCode = 'LEAD';

        $order = Order::with('lead')->find($request->order_id);

        if ($order && $order->lead) {
            $leadCode = $order->lead->id; // or ->lead_code if you want
        }

        /*
        Generate BOM number
        */
        $bomNumber =
            'BOM'
            . '/'
            . $date
            . '/'
            . $leadCode
            . '/'
            . $nextId;

        return response()->json([
            'bom' => $bomNumber,
            'today' => now()->format('d/m/Y')
        ]);
    }
    public function getOrderDeliveryDate(Request $request)
    {
        $order = Order::find($request->order_id);

        if (!$order) {
            return response()->json([
                'success' => false
            ]);
        }

        return response()->json([
            'success' => true,
            'delivery_date' => $order->delivery_date,
            'edit_url' => route('orders.edit', [
                'company' => $request->company,
                'order' => $order->id
            ])
        ]);
    }
    public function store(Request $request, Company $company)
    {
        DB::beginTransaction();
        try {
            $rules = [
                'order_id' => 'required|exists:orders,id',
                'items' => 'required|array|min:1',
                'incharge_department_id' => 'required|exists:departments,id',
                'supervisor_id' => 'required|exists:employees,id',
                'delivery_date' => 'required|date_format:d/m/Y|after_or_equal:today',
                'items.*.recipe_id' => 'required|exists:recipes,id',
                'items.*.parts' => 'required|array|min:1',
                'items.*.parts.*.weightage' => 'required|numeric|min:0.1|max:10',
                'items.*.parts.*.part_name' => 'required|string|max:255',
                // 'items.*.parts.*.spec_id' => 'required|exists:specifications,id',
                'items.*.parts.*.items' => 'required|array|min:1',
                'items.*.parts.*.items.*.item_id' => 'required|exists:items,id',
                'items.*.parts.*.items.*.employee_id' => 'required|exists:employees,id',
                'items.*.parts.*.items.*.department_id' => 'required|exists:departments,id',
                'items.*.parts.*.items.*.quantity' => 'required|numeric|min:0.01',
                // 'items.*.parts.*.items.*.notes' => 'required|string|max:1000',
            ];
            $messages = [
                'order_id.required' => 'Please select an order.',
                'order_id.exists' => 'Selected order is invalid.',
                'incharge_department_id.required' => 'Please select incharge department.',
                'incharge_department_id.exists' => 'Selected incharge department is invalid.',
                'supervisor_id.required' => 'Please select BOM incharge.',
                'supervisor_id.exists' => 'Selected supervisor is invalid.',
                'items.required' => 'Please add BOM items.',
                'items.array' => 'Invalid BOM data format.',
                'items.min' => 'At least one order item is required.',
                'items.*.parts.*.weightage.required' => 'Weightage is required for each part.',
                'items.*.parts.*.weightage.numeric' => 'Weightage must be a number.',
                'items.*.parts.*.weightage.min' => 'Weightage must be greater than 0.',
                'items.*.parts.*.weightage.max' => 'Weightage cannot exceed 10.',
                'items.*.parts.required' => 'Each order item must have parts.',
                'items.*.parts.min' => 'Each order item must contain at least one part.',
                'delivery_date.required' => 'Delivery date is required.',
                'delivery_date.date_format' => 'Invalid delivery date format.',
                'delivery_date.after_or_equal' => 'Delivery date cannot be in the past.',
                'items.*.parts.*.part_name.required' => 'Part name is required.',
                'items.*.parts.*.part_name.string' => 'Part name must be valid text.',
                'items.*.parts.*.spec_id.required' => 'Please select specification.',
                'items.*.parts.*.spec_id.exists' => 'Selected specification is invalid.',
                'items.*.parts.*.shift_id.required' => 'Please select shift.',
                'items.*.parts.*.shift_id.exists' => 'Selected shift is invalid.',
                'items.*.parts.*.items.required' => 'Each part must have items.',
                'items.*.parts.*.items.min' => 'Each part must contain at least one item.',
                'items.*.parts.*.items.*.item_id.required' => 'Please select an item.',
                'items.*.parts.*.items.*.item_id.exists' => 'Selected item is invalid.',
                'items.*.parts.*.items.*.employee_id.required' => 'Please assign an employee.',
                'items.*.parts.*.items.*.employee_id.exists' => 'Selected employee is invalid.',
                'items.*.parts.*.items.*.department_id.required' => 'Please select department.',
                'items.*.parts.*.items.*.department_id.exists' => 'Selected department is invalid.',
                'items.*.parts.*.items.*.quantity.required' => 'Quantity is required.',
                'items.*.parts.*.items.*.quantity.numeric' => 'Quantity must be a number.',
                'items.*.parts.*.items.*.quantity.min' => 'Quantity must be greater than 0.',
                'items.*.parts.*.items.*.notes.required' => 'Notes are required.',
                'items.*.parts.*.items.*.notes.string' => 'Notes must be valid text.',
            ];
            $attributes = [
                'order_id' => 'order',
                'items' => 'BOM items',
                'items.*.recipe_id' => 'recipe',
                'items.*.parts' => 'parts',
                'items.*.parts.*.part_name' => 'part name',
                'items.*.parts.*.spec_id' => 'specification',
                'items.*.parts.*.shift_id' => 'shift',
                'items.*.parts.*.weightage' => 'part weightage',
                'items.*.parts.*.items' => 'items',
                'incharge_department_id' => 'incharge department',
                'supervisor_id' => 'BOM incharge',
                'review_department_id' => 'review department',
                'checked_by' => 'checked by',
                'priority_id' => 'priority',
                'shift_id' => 'shift',
                'delivery_date' => 'delivery date',
                'items.*.parts.*.items.*.item_id' => 'item',
                'items.*.parts.*.items.*.employee_id' => 'employee',
                'items.*.parts.*.items.*.department_id' => 'department',
                'items.*.parts.*.items.*.quantity' => 'quantity',
                'items.*.parts.*.items.*.notes' => 'notes',
            ];
            Validator::make($request->all(), $rules, $messages, $attributes)->validate();
            $existing = Bom::where('company_id', $company->id)->where('order_id', $request->order_id)->first();
            if ($existing) {
                return response()->json([
                    'message' => 'BOM already exists',
                    'bom_id' => $existing->id,
                    'bom_number' => $existing->bom_number
                ], 422);
            }
            $bom = Bom::create([
                'company_id' => $company->id,
                'order_id' => $request->order_id,
                'remarks' => $request->remarks,
                'delivery_date' => $request->delivery_date
                    ? Carbon::createFromFormat('d/m/Y', $request->delivery_date)->format('Y-m-d')
                    : null,

                'created_by' => auth()->id(),

                'incharge_department_id' => $request->incharge_department_id,
                'supervisor_id' => $request->supervisor_id,
                'review_department_id' => $request->review_department_id,
                'checked_by' => $request->checked_by,
                'priority_id' => $request->priority_id,
                'shift_id' => $request->shift_id,
            ]);

            /*
            🔥 LOOP ORDER ITEMS
            */
            foreach ($request->items as $orderItemId => $data) {

                $recipeId = $data['recipe_id'] ?? null;
                $parts = $data['parts'] ?? [];

                /*
                🔥 LOOP PARTS
                */
                foreach ($parts as $index => $partData) {

                    $part = BomPart::create([
                        'bom_id' => $bom->id,
                        'part_name' => $partData['part_name'] ?? null,
                        'hi_part_name' => $partData['hi_part_name'] ?? null,
                        'spec_id' => $partData['spec_id'] ?? null,
                        'shift_id' => $partData['shift_id'] ?? null,
                        'weightage' => $partData['weightage'] ?? 0,
                        'sort_order' => $partData['sort_order'] ?? $index
                    ]);

                    /*
                    🔥 ITEMS INSIDE PART
                    */
                    foreach ($partData['items'] ?? [] as $row) {

                        if (empty($row['item_id']))
                            continue;

                        BomItem::create([
                            'bom_id' => $bom->id,
                            'bom_part_id' => $part->id,

                            'order_item_id' => $orderItemId,
                            'recipe_id' => $recipeId, // 🔥 IMPORTANT

                            'item_id' => $row['item_id'],
                            'department_id' => $row['department_id'] ?? null,
                            'employee_id' => $row['employee_id'] ?? null,

                            'quantity' => $row['quantity'] ?? 1,
                            'notes' => $row['notes'] ?? null,
                            'hi_notes' => $row['hi_notes'] ?? null,

                            'status' => 'assigned'
                        ]);
                    }
                }
            }
            $date = now()->format('ymd');
            $leadCode = $bom->order->quotation->lead->id;
            $user = auth()->id();
            $prefix = $company->initials() . '#' . $user;

            $bom->bom_number = $prefix
                . '/'
                . $date
                . '/'
                . $leadCode
                . '/'
                . $bom->id;
            $bom->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'BOM Created Successfully'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function edit(Company $company, Bom $bom)
    {
        // =========================================
        // 🔥 MASTER DATA
        // =========================================

        $items = Item::where('company_id', $company->id)->get();

        $units = Unit::where('company_id', $company->id)->get();

        $departments = Department::where('company_id', $company->id)->get();

        $employees = Employee::where('company_id', $company->id)
            ->where('status', 1)
            ->presentToday()
            ->get();

        $priorities = Priority::orderBy('level', 'asc')->get();

        $shifts = Shift::all();

        $specifications = Specification::all();


        // =========================================
        // 🔥 LOAD RELATIONS
        // =========================================

        $bom->load([

            'parts' => function ($q) {

                $q->orderBy('sort_order', 'asc');

            },

            'parts.items.item',

            'parts.items.employee',

            'parts.items.department',

            'parts.shift',

            'parts.spec',

            'items'

        ]);


        // =========================================
        // 🔥 KEEP OLD GROUPING STRUCTURE
        // =========================================

        $grouped = $bom->parts
            ->groupBy(function ($part) {

                return optional(
                    $part->items->first()
                )->order_item_id;

            })
            ->map(function ($parts) {

                return [

                    // 🔥 KEEP OLD FEATURE
                    'recipe_id' => optional(
                        optional($parts->first())->items->first()
                    )->recipe_id,

                    'parts' => $parts->map(function ($part) {

                        return [

                            // =====================================
                            // 🔥 PART DATA
                            // =====================================
        
                            'id' => $part->id,

                            'part_name' => $part->part_name,

                            'hi_part_name' => $part->hi_part_name,

                            'spec_id' => $part->spec_id,

                            'shift_id' => $part->shift_id,

                            'weightage' => $part->weightage,

                            'sort_order' => $part->sort_order,

                            // =====================================
                            // 🔥 ITEMS
                            // =====================================
        
                            'items' => $part->items->map(function ($item) {

                                return [

                                    'id' => $item->id,

                                    'item_id' => $item->item_id,

                                    // 🔥 IMPORTANT FOR SELECT2
                                    'item_name' => optional($item->item)->name,

                                    'department_id' => $item->department_id,

                                    'employee_id' => $item->employee_id,

                                    'quantity' => $item->quantity,

                                    'notes' => $item->notes,

                                    'hi_notes' => $item->hi_notes,

                                    'status' => $item->status

                                ];

                            })->values()

                        ];

                    })->values()

                ];

            });


        // =========================================
        // 🔥 RETURN VIEW
        // =========================================

        return view('company.crm.boms.edit', compact(
            'company',
            'items',
            'units',
            'departments',
            'employees',
            'priorities',
            'shifts',
            'specifications',
            'bom',
            'grouped'
        ))->with([

                    'title' => 'BOM Management :: Edit BOM',

                    'label' => 'Edit BOM'

                ]);
    }
    public function update(Request $request, Company $company, Bom $bom)
    {
        try {

            DB::beginTransaction();

            // =========================================
            // ✅ UPDATE MAIN BOM
            // =========================================

            $bom->update([

                'order_id' => $request->order_id,

                'remarks' => $request->remarks,
                'hi_remarks' => $request->hi_remarks,

                'status' => $request->status ?? 'draft',

                'delivery_date' => $request->delivery_date
                    ? Carbon::createFromFormat(
                        'd/m/Y',
                        $request->delivery_date
                    )->format('Y-m-d')
                    : null,

                'incharge_department_id' => $request->incharge_department_id,

                'supervisor_id' => $request->supervisor_id,

                'review_department_id' => $request->review_department_id,

                'checked_by' => $request->checked_by,

                'shift_id' => $request->shift_id,

                'priority_id' => $request->priority_id,

            ]);

            // =========================================
            // 🔥 DELETE OLD ITEMS
            // =========================================

            foreach ($bom->parts as $part) {

                foreach ($part->items as $item) {

                    $item->delete();

                }

                $part->delete();
            }

            // =========================================
            // 🔥 INSERT NEW DATA
            // =========================================

            if (!empty($request->items)) {

                foreach ($request->items as $orderItemId => $data) {

                    $recipeId = $data['recipe_id'] ?? null;

                    foreach ($data['parts'] as $index => $partRow) {

                        // =====================================
                        // 🔥 CREATE PART
                        // =====================================

                        $part = $bom->parts()->create([

                            'bom_id' => $bom->id,

                            'part_name' => $partRow['part_name'] ?? '',

                            // 🔥 IMPORTANT
                            'hi_part_name' => $partRow['hi_part_name'] ?? '',

                            'spec_id' => $partRow['spec_id'] ?? null,

                            'shift_id' => $partRow['shift_id'] ?? null,

                            // 🔥 IMPORTANT
                            'weightage' => $partRow['weightage'] ?? 0,

                            // 🔥 IMPORTANT
                            'sort_order' => $partRow['sort_order'] ?? $index,

                        ]);

                        // =====================================
                        // 🔥 CREATE ITEMS
                        // =====================================

                        if (!empty($partRow['items'])) {

                            foreach ($partRow['items'] as $itemRow) {

                                $part->items()->create([

                                    'bom_id' => $bom->id,

                                    'bom_part_id' => $part->id,

                                    'order_item_id' => $orderItemId,

                                    // 🔥 IMPORTANT
                                    'recipe_id' => $recipeId,

                                    'item_id' => $itemRow['item_id'] ?? null,

                                    'department_id' => $itemRow['department_id'] ?? null,

                                    'employee_id' => $itemRow['employee_id'] ?? null,

                                    'quantity' => $itemRow['quantity'] ?? 1,

                                    // 🔥 IMPORTANT
                                    'status' => $itemRow['status'] ?? 'assigned',

                                    'notes' => $itemRow['notes'] ?? null,

                                    // 🔥 IMPORTANT
                                    'hi_notes' => $itemRow['hi_notes'] ?? null,

                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([

                'status' => true,

                'message' => 'BOM Updated Successfully'

            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([

                'status' => false,

                'message' => $e->getMessage()

            ], 500);
        }
    }
    public function createOrEditBom($company, $orderId)
    {
        $today = Carbon::today()->toDateString();

        $attendancePresent = Attendance::where('company_id', $company)
            ->whereDate('date', $today)
            ->where('is_present', 1)
            ->exists();

        // ❌ Attendance check
        if (!$attendancePresent) {

            return redirect()
                ->route('attendance.index', $company)
                ->with('error', 'Please mark today attendance first.');
        }

        // ✅ Existing BOM check
        $bom = Bom::where('order_id', $orderId)->first();

        if ($bom) {

            return redirect()->route('boms.edit', [
                'company' => $company,
                'bom' => $bom->id,
                'existing' => 1
            ]);
        }

        // ✅ Create new BOM
        return redirect()->route('boms.create', [
            'company' => $company,
            'order_id' => $orderId
        ]);
    }
    public function print(Company $company, Bom $bom)
    {
        $bom->load([
            'order.quotation.lead.customer.primaryPhone',
            'order.quotation.lead.customer.country',

            'order.items.machine',
            'order.items.component',
            'order.items.item',

            // 🔥 NEW PART STRUCTURE (IMPORTANT)
            'parts.spec',
            'parts.shift',
            'parts.items.item',
            'parts.items.department',
            'parts.items.employee',
            'parts.items.shift',

            'priority',
            'supervisor',
            'checker',
            'department'
        ]);
        $user = Auth::user();
        return view('company.crm.boms.print', compact('company', 'bom'))
            ->with([
                'title' => 'BOM Management :: Print BOM',
                'label' => 'BOM Preview',
                'user' => $user
            ]);
    }
    public function destroy(Company $company, Bom $bom)
    {
        try {

            // ✅ CHECK IF USED IN ISSUES
            if ($bom->hasIssues()) {
                return back()->with('error', 'Cannot delete! BOM already used in issues.');
            }

            $bom->delete();

            return back()->with('success', 'BOM deleted successfully');

        } catch (\Exception $e) {

            return back()->with('error', 'This BOM is already linked with other records.');
        }
    }
    public function pdf($companyId, $bomId, Request $request)
    {
        // =====================================
        // 🔥 LANGUAGE
        // =====================================

        $lang = $request->lang ?? 'en';

        // =====================================
        // 🔥 LOAD COMPANY
        // =====================================

        $company = Company::findOrFail($companyId);

        // =====================================
        // 🔥 LOAD BOM
        // =====================================

        $bom = Bom::with([

            'order.quotation.lead.customer.primaryPhone',

            'order.quotation.lead.customer.country',

            'order.items.machine',

            'order.items.component',

            'order.items.item',

            // 🔥 PARTS
            'parts.spec',

            'parts.shift',

            'parts.items.item',

            'parts.items.department',

            'parts.items.employee',

            'parts.items.shift',

            'priority',

            'supervisor',

            'checker',

            'department'

        ])->findOrFail($bomId);

        // =====================================
        // 🔥 APPLY HINDI DATA
        // =====================================

        if ($lang === 'hi') {

            // 🔥 REMARKS
            if (!empty($bom->hi_remarks)) {

                $bom->remarks = $bom->hi_remarks;

            }

            // =====================================
            // 🔥 ORDER ITEMS
            // =====================================

            foreach ($bom->order->items as $orderItem) {

                // MACHINE
                if (
                    $orderItem->machine &&
                    !empty($orderItem->machine->hi_name)
                ) {

                    $orderItem->machine->name =
                        $orderItem->machine->hi_name;

                }

                // COMPONENT
                if (
                    $orderItem->component &&
                    !empty($orderItem->component->hi_name)
                ) {

                    $orderItem->component->name =
                        $orderItem->component->hi_name;

                }

                // ITEM
                if (
                    $orderItem->item &&
                    !empty($orderItem->item->hi_name)
                ) {

                    $orderItem->item->name =
                        $orderItem->item->hi_name;

                }
            }

            // =====================================
            // 🔥 PARTS + ITEMS
            // =====================================

            foreach ($bom->parts as $part) {

                // 🔥 PART NAME
                if (!empty($part->hi_part_name)) {

                    $part->part_name =
                        $part->hi_part_name;

                }

                // 🔥 ITEMS
                foreach ($part->items as $bomItem) {

                    // ITEM NAME
                    if (
                        $bomItem->item &&
                        !empty($bomItem->item->hi_name)
                    ) {

                        $bomItem->item->name =
                            $bomItem->item->hi_name;

                    }

                    // NOTES
                    if (!empty($bomItem->hi_notes)) {

                        $bomItem->notes =
                            $bomItem->hi_notes;

                    }
                }
            }
        }

        // =====================================
        // 🔥 SETTINGS
        // =====================================

        $settings = Setting::first();

        // =====================================
        // 🔥 FILTERS
        // =====================================

        $sections = array_filter(
            explode(',', $request->sections ?? '')
        );

        $columns = array_filter(
            explode(',', $request->columns ?? '')
        );

        $rows = $request->has('rows')
            ? explode(',', $request->rows)
            : [];

        // =====================================
        // 🔥 FILTER BOM ITEMS
        // =====================================

        if (!empty($rows)) {

            $bom->setRelation(

                'items',

                $bom->items->filter(function ($item) use ($rows) {

                    return in_array(
                        (string) $item->id,
                        $rows,
                        true
                    );

                })->values()

            );
        }

        // =====================================
        // 🔥 USER
        // =====================================

        $user = Auth::user();

        // =====================================
        // 🔥 PDF GENERATION
        // =====================================

        $pdf = Pdf::loadView(

            'company.crm.boms.pdf',

            compact(
                'bom',
                'settings',
                'company',
                'sections',
                'columns',
                'user',
                'lang'
            )

        )->setPaper('a4', 'portrait');

        // =====================================
        // 🔥 PAGE NUMBER
        // =====================================

        $dompdf = $pdf->getDomPDF();

        $dompdf->render();

        $canvas = $dompdf->getCanvas();

        $canvas->page_text(

            520,
            803,

            "Page {PAGE_NUM} of {PAGE_COUNT}",

            null,

            7,

            [255, 255, 255]

        );

        // =====================================
        // 🔥 FILE NAME
        // =====================================

        $customerName =
            $bom->order?->customer_name ?? 'Customer';

        $fileName = $this->generateFileName(

            $company,

            'bom',

            $bom->bom_number,

            $customerName

        );

        // =====================================
        // 🔥 DOWNLOAD
        // =====================================

        return response($pdf->output(), 200)

            ->header('Content-Type', 'application/pdf')

            ->header(
                'Content-Disposition',
                'attachment; filename="' . $fileName . '"'
            );
    }
    private function generateFileName($company, $docType, $number, $customerName)
    {
        $initials = $company->initials();

        // Document short codes
        $docMap = [
            'quotation' => 'Q',
            'pi' => 'PI',
            'order' => 'O',
            'po' => 'PO',
            'payment' => 'PAY',
            'bom' => 'BOM'
        ];

        $docInitial = $docMap[$docType] ?? strtoupper(substr($docType, 0, 2));

        // Take only first 2 words of customer name
        $words = explode(' ', trim($customerName));
        $firstTwoWords = array_slice($words, 0, 2);
        $cleanCustomer = preg_replace('/[^A-Za-z0-9]/', '', implode('', $firstTwoWords));

        // Format: DDMMYYHi
        $dateTime = Carbon::now()->format('dmyHi');

        return "{$initials}{$docInitial}{$dateTime}-{$cleanCustomer}.pdf";
    }
    public function saveBomTranslation(Request $request)
    {
        $bomId = $request->bom_id;
        $lang = $request->lang;

        $translations = collect($request->translations)->map(function ($t) {
            $t['text'] = $t['text'] ?? '';

            // 🔥 REMOVE XML
            $t['text'] = preg_replace('/<\?xml.*?\?>/i', '', $t['text']);


            // 🔥 CLEAN EXTRA SPACES
            $t['text'] = preg_replace('/\s+/', ' ', $t['text']);

            // 🔥 FINAL TRIM
            $t['text'] = trim($t['text']);
            return $t;
        })->toArray();

        $key = "bom_pdf_{$bomId}_{$lang}";

        Cache::put($key, $translations, now()->addMinutes(30));

        return response()->json(['status' => 'saved']);
    }
    public function getBomTranslation(Request $request)
    {
        $bomId = $request->bom_id;
        $lang = $request->lang;

        $key = "bom_pdf_{$bomId}_{$lang}";

        return response()->json([
            'translations' => Cache::get($key)
        ]);
    }
}
