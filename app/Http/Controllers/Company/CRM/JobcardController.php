<?php

namespace App\Http\Controllers\Company\CRM;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Planning;
use App\Models\PlanningItem;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
class JobcardController extends Controller
{
    public function index(Request $request, Company $company)
    {
        return view('company.crm.jobcard.index', [
            'company' => $company,
            'title' => auth()->user()->name . ":: Job Card Management",
            'label' => "Job Card List"
        ]);
    }
    public function data(Request $request, Company $company)
    {
        $query = Planning::with(['order', 'incharge', 'department'])
            ->where('company_id', $company->id);

        $search = $request->search ?: null;
        $from = $request->from_date ?: null;
        $to = $request->to_date ?: null;

        // 🔍 SEARCH
        if ($search !== null && $from === null && $to === null) {

            if (is_numeric($search)) {

                $query->where('id', $search);

            } else {

                $query->where(function ($q) use ($search) {

                    $q->where('po_number', 'LIKE', "%{$search}%")

                        ->orWhereHas('order', function ($qo) use ($search) {
                            $qo->where('order_number', 'LIKE', "%{$search}%")
                                ->orWhere('customer_name', 'LIKE', "%{$search}%");
                        })

                        // ✅ FIX: employee name (not user)
                        ->orWhereHas('incharge', function ($qi) use ($search) {
                            $qi->where('first_name', 'LIKE', "%{$search}%")
                                ->orWhere('last_name', 'LIKE', "%{$search}%");
                        })

                        // ✅ department search
                        ->orWhereHas('department', function ($qd) use ($search) {
                            $qd->where('name', 'LIKE', "%{$search}%");
                        });

                });
            }
        }

        // 📅 DATE FILTER
        if ($search === null && ($from !== null || $to !== null)) {
            if ($from) {
                $query->whereDate('date', '>=', $from);
            }
            if ($to) {
                $query->whereDate('date', '<=', $to);
            }
        }

        // 🟢 DEFAULT TODAY
        if ($search === null && $from === null && $to === null) {
            $query->whereDate('created_at', today());
        }

        $plannings = $query->latest()->get();

        return view('company.crm.jobcard.partials.jobcard_rows', [
            'plannings' => $plannings,
            'company' => $company
        ])->render();
    }
    public function ajaxDetails(Request $request, Company $company)
    {
        $planning = Planning::with([
            'order',
            'department',
            'incharge',
            'checkedBy',
            'items.orderItem.machine',
            'items.orderItem.component'
        ])->findOrFail($request->id);

        return response()->json($planning);
    }
    public function ajaxSearch(Request $request, Company $company)
    {
        $search = $request->search;

        $plannings = Planning::with(['order'])
            ->where('company_id', $company->id)
            ->where(function ($q) use ($search) {

                $q->where('po_number', 'LIKE', "%{$search}%")

                    ->orWhereHas('order', function ($qo) use ($search) {
                        $qo->where('customer_name', 'LIKE', "%{$search}%");
                    });

            })
            ->limit(20)
            ->get();

        return $plannings->map(function ($p) {
            return [
                'id' => $p->id,
                'text' => $p->po_number . ' - ' . ($p->order->customer_name ?? '')
            ];
        });
    }
    public function create(Company $company)
    {
        $orders = Order::where('company_id', $company->id)->get();
        $employees = Employee::where('company_id', $company->id)->get();
        $departments = Department::where('company_id', $company->id)->get();

        $selectedOrder = request()->order;

        // ✅ Get selected order
        $order = $selectedOrder
            ? Order::with('quotation.lead.customer')->find($selectedOrder)
            : null;

        // ✅ Get customer name safely
        $customerName = optional($order?->quotation?->lead?->customer)->name ?? 'GENERAL';

        // ✅ Generate PO
        $poNumber = $this->generatePoNumber($company, $customerName);

        return view('company.crm.jobcard.create', compact(
            'company',
            'orders',
            'employees',
            'departments',
            'selectedOrder'
        ) + [
            'title' => auth()->user()->name . ":: Job Card Management",
            'label' => "Job Card List",
            'poNumber' => $poNumber,
        ]);
    }
    public function store(Request $request, Company $company)
    {
        DB::transaction(function () use ($request, $company) {

            // ✅ CREATE PLANNING
            $planning = Planning::create([
                'company_id' => $company->id,
                'order_id' => $request->order_id,
                'department_id' => $request->department_id,
                'planning_incharge_id' => $request->planning_incharge_id,
                'checked_by' => $request->checked_by,
                'po_number' => $request->po_number,
                'priority' => $request->priority,
                'shift' => $request->shift,
                'date' => now(),

                // ✅ FIX DATE FORMAT
                'delivery_date' => $request->delivery_date
                    ? Carbon::createFromFormat('d/m/Y', $request->delivery_date)->format('Y-m-d')
                    : null,
                'term' => $request->term,
                'remark' => $request->remark,
                'status' => 'pending'
            ]);

            // ✅ SAVE ITEMS FROM REQUEST (IMPORTANT)
            foreach ($request->order_item_id as $index => $itemId) {

                PlanningItem::create([
                    'planning_id' => $planning->id,
                    'order_item_id' => $itemId,
                    'employee_id' => $request->item_employee_id[$index] ?? null,
                    'description' => $request->description[$index] ?? null,
                    'qty' => $request->qty[$index] ?? 0,
                    'specs' => $request->specs[$index] ?? null,
                    'status' => 'pending',
                    'remarks' => $request->item_remarks[$index] ?? null

                ]);
            }
        });

        return redirect()->route('jobcard.index', $company->id)
            ->with('success', 'Job Card Created Successfully');
    }
    private function generatePoNumber($company, $customerName)
    {
        $initials = $company->initials(); // e.g. SK

        // Date
        $date = now()->format('dmy'); // 200326

        // Clean customer name (only first 2 words)
        $words = explode(' ', trim($customerName));
        $firstTwoWords = array_slice($words, 0, 2);

        $cleanCustomer = preg_replace('/[^A-Za-z0-9]/', '', implode('', $firstTwoWords));
        $cleanCustomer = strtoupper($cleanCustomer); // optional

        // Get last PO for today
        $last = Planning::whereDate('created_at', today())
            ->latest()
            ->first();

        $count = $last ? (int) substr($last->po_number, -3) + 1 : 1;

        $running = str_pad($count, 3, '0', STR_PAD_LEFT);

        return "{$initials}-JC-{$date}-{$running}-{$cleanCustomer}";
    }
    public function generatePoAjax(Request $request, Company $company)
    {
        $order = Order::with('quotation.lead.customer')
            ->find($request->order_id);

        $customerName = optional($order?->quotation?->lead?->customer)->name ?? 'GENERAL';

        $poNumber = $this->generatePoNumber($company, $customerName);

        return response()->json([
            'po_number' => $poNumber
        ]);
    }
    public function edit(Company $company, $id)
    {
        $planning = Planning::with([
            'order',
            'items.orderItem.machine',
            'items.orderItem.component'
        ])->findOrFail($id);

        $employees = Employee::where('company_id', $company->id)->get();
        $departments = Department::where('company_id', $company->id)->get();

        return view('company.crm.jobcard.edit', compact(
            'company',
            'planning',
            'employees',
            'departments'
        ) + [
            'title' => auth()->user()->name . ":: Job Card Management",
            'label' => "Edit Job Card"
        ]);
    }
    public function preview(Company $company, $id)
    {
        $planning = Planning::with([
            'order',
            'order.quotation.lead.customer',
            'department',
            'incharge',
            'checkedBy',
            'items.employee',
            'items.orderItem.machine',
            'items.orderItem.component'
        ])->findOrFail($id);

        $settings = Setting::first();
        $title = auth()->user()->name . " Job Card Preview";
        $label = 'Preview Job Card';

        return view('company.crm.jobcard.preview', compact(
            'planning',
            'company',
            'settings',
            'title',
            'label'
        ));
    }
    public function pdf($id)
    {
        $planning = Planning::with([
            'order',
            'department',
            'incharge',
            'checkedBy',
            'items.employee',
            'items.orderItem.machine',
            'items.orderItem.component'
        ])->findOrFail($id);

        // ✅ GET COMPANY
        $company = $planning->order->company ?? Auth::user()->company;

        // ✅ FILE NAME
        $fileName = $this->generateFileName(
            $company,
            'jc',
            $planning->po_number,
            $planning->order->quotation->lead->customer->name ?? 'jobcard'
        );

        // ✅ LOAD PDF FIRST (IMPORTANT)
        $pdf = Pdf::loadView('company.crm.jobcard.pdf', compact('planning', 'company'))
            ->setPaper('a4', 'portrait');

        // ✅ ACCESS DOMPDF
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();
        $canvas = $dompdf->getCanvas();

        // ✅ PAGE NUMBER (BOTTOM RIGHT)
        $canvas->page_text(
            520,   // X position
            825,   // Y position
            "Page {PAGE_NUM} of {PAGE_COUNT}",
            null,
            7,
            [255, 255, 255] // white color for blue footer
        );

        return $pdf->download($fileName);
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
            'jc' => 'JC', // ✅ ADD THIS
            'payment' => 'PAY'
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
    public function update(Request $request, Company $company, $id)
    {
        DB::transaction(function () use ($request, $company, $id) {

            $planning = Planning::findOrFail($id);

            $planning->update([
                'department_id' => $request->department_id,
                'planning_incharge_id' => $request->planning_incharge_id,
                'checked_by' => $request->checked_by,
                'priority' => $request->priority,
                'shift' => $request->shift,
                'delivery_date' => $request->delivery_date
                    ? Carbon::createFromFormat('d/m/Y', $request->delivery_date)->format('Y-m-d')
                    : null,
                'remark' => $request->remark,
                'term' => $request->term
            ]);

            // 🔥 Update Items
            foreach ($request->item_id as $index => $itemId) {

                PlanningItem::where('id', $itemId)->update([
                    'description' => $request->description[$index],
                    'specs' => $request->specs[$index],
                    'qty' => $request->qty[$index],
                    'status' => $request->status[$index],
                    'employee_id' => $request->item_employee_id[$index],
                    'remarks' => $request->item_remarks[$index],
                ]);
            }
            $planning->load('items');
            $hasWorking = $planning->items->where('status', 'working')->count();
            $doneItems = $planning->items->where('status', 'done')->count();
            $totalItems = $planning->items->count();

            if ($doneItems == $totalItems && $totalItems > 0) {
                $planning->status = 'completed';
            } elseif ($hasWorking > 0 || $doneItems > 0) {
                $planning->status = 'in_progress';
            } else {
                $planning->status = 'pending';
            }

            $planning->save();
        });

        return redirect()->route('jobcard.index', $company->id)
            ->with('success', 'Job Card Updated Successfully');
    }
    public function destroy(Company $company, $id)
    {
        $planning = Planning::where('company_id', $company->id)->findOrFail($id);

        // 🔥 SAFETY CHECK
        if ($planning->items()->whereIn('status', ['working', 'done'])->exists()) {
            return back()->with('error', 'Cannot delete! Some items are already in progress or completed.');
        }

        $planning->delete();

        return redirect()->route('jobcard.index', $company->id)
            ->with('success', 'Job Card deleted successfully');
    }
}
