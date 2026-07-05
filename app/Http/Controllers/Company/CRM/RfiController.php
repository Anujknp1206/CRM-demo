<?php

namespace App\Http\Controllers\Company\CRM;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Location;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\RfiItem;
use App\Models\Stock;
use App\Models\StockInItem;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Models\Company;
use Carbon\Carbon;
use App\Models\Specification;

use App\Models\Rfi;
use Illuminate\Support\Facades\Auth;
class RfiController extends Controller
{
    public function index(Company $company)
    {

        return view('company.crm.rfi.index', [
            'company' => $company,
            'items' => Item::where('company_id', $company->id)->pluck('name', 'id'),
            'brands' => Brand::where('company_id', $company->id)->pluck('name', 'id'),
            'conditions' => Condition::where('company_id', $company->id)->get(),
            'locations' => Location::where('company_id', $company->id)->pluck('name', 'id'),
            'units' => Unit::where('company_id', $company->id)->pluck('name', 'id'),

            'suppliers' => Supplier::where('company_id', $company->id)->get(), // ✅ ADD THIS
            'title' => Auth::user()->name . " RFI Management",
            'label' => "RFI List",
        ]);
    }
    public function edit($companyId, Rfi $rfi)
    {
        return response()->json([
            'rfi' => $rfi->load('creator'),
            'items' => $rfi->items()->with(['item', 'brand', 'condition', 'location', 'unit'])->get()
        ]);
    }
    public function getLastRate(Request $request, Company $company)
    {
        $rate = StockInItem::where([
            'item_id' => $request->item_id,
            'brand_id' => $request->brand_id,
            'condition_id' => $request->condition_id,
            'location_id' => $request->location_id,
        ])
            ->latest()
            ->value('rate');

        return response()->json([
            'rate' => $rate ?? 0
        ]);
    }
    public function data(Request $request, Company $company)
    {
        $query = Rfi::withCount('items')
            ->where('company_id', $company->id);

        // 🔍 Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('rfi_code')) {
            $query->where('rfi_code', 'like', '%' . $request->rfi_code . '%');
        }

        $rfis = $query->latest()->get();

        return view('company.crm.rfi.partials.rfi_rows', [
            'rfis' => $rfis,
            'company' => $company
        ])->render();
    }
    public function lowStockItems(Company $company)
    {
        $stocks = Stock::with([
            'item',
            'brand',
            'condition',
            'location',
            'unit',
            'item.unitConversions.fromUnit'
        ])
            ->where('company_id', $company->id)
            ->whereColumn('quantity', '<=', 'min_quantity')

            // ✅ EXCLUDE ITEMS ALREADY IN PO
            ->whereNotIn('item_id', function ($q) {
                $q->select('item_id')
                    ->from('purchase_order_items');
            })

            ->get();

        return view('company.crm.rfi.partials.modal_rows', compact('stocks'))->render();
    }
    public function show(Company $company, $id)
    {
        $rfi = Rfi::with([
            'creator',
            'items.item',
            'items.brand',
            'items.condition',
            'items.unit',
        ])
            ->findOrFail($id);
        $nextId = (PurchaseOrder::max('id') ?? 0) + 1;
        $specs = Specification::where('company_id', $company->id)->get();

        $poCode = 'PO-' . $company->initials() . '-' . now()->format('Ymd') . '-' . $nextId;
        return response()->json([
            'rfi' => [
                'id' => $rfi->id,
                'rfi_code' => $rfi->rfi_code,
                'po_code' => $poCode,
                // ✅ PROPER DATE + TIME FORMAT
                'created_at' => $rfi->created_at
                    ? $rfi->created_at->format('d/m/Y h:i A')
                    : null,

                // ✅ CREATED BY
                'created_by' => $rfi->creator?->name ?? 'N/A',

                'approved_by' => $rfi->approver?->name ?? null,
                'notes' => $rfi->notes,
                'remark' => $rfi->remark,
            ],

            // 🔥 THIS WAS MISSING
            'specifications' => $specs,
            'items' => $rfi->items
        ]);
    }
    public function create(Company $company)
    {
        $stocks = Stock::with(['item', 'brand', 'condition', 'location'])
            ->where('company_id', $company->id)
            ->whereColumn('quantity', '<=', 'min_quantity')
            ->get();

        // 🔥 Generate preview code
        $date = now()->format('Ymd');
        $initials = $company->initials();

        $lastId = Rfi::max('id') + 1;

        $previewCode = "RFI-{$initials}-{$date}-{$lastId}";

        return view('company.crm.rfi.create', compact(
            'company',
            'stocks',
            'previewCode'
        ) + [
            'title' => Auth::user()->name . " RFI Management",
            'label' => "Add RFI (Request For Items)",
        ]);
    }
    public function store(Request $request, Company $company)
    {
        $rfi = \DB::transaction(function () use ($request, $company) {

            $total = 0;

            $rfi = Rfi::create([
                'company_id' => $company->id,
                'rfi_date' => Carbon::parse($request->rfi_date)->format('Y-m-d'),
                'remark' => $request->remark,
                'created_by' => auth()->id(),
                'status' => 'pending'
            ]);


            $rfi->update([
                'rfi_code' =>
                    $company->initials()
                    . '#'
                    . Auth::id()
                    . '/RFI/'
                    . now()->format('ymd')
                    . '/'
                    . str_pad($rfi->id, 4, '0', STR_PAD_LEFT)
            ]);

            foreach ($request->items as $item) {

                if (!isset($item['selected']))
                    continue;

                $qty = $item['requested_quantity'] ?? 1;
                $rate = $item['rate'] ?? 0;

                $amount = $qty * $rate;
                $total += $amount;

                RfiItem::create([
                    'rfi_id' => $rfi->id,
                    'item_id' => $item['item_id'],
                    'brand_id' => $item['brand_id'],
                    'condition_id' => $item['condition_id'],
                    'location_id' => $item['location_id'],
                    'unit_id' => $item['unit_id'],
                    'current_quantity' =>0,
                    'min_quantity' => 10,
                    'requested_quantity' => $qty,
                    'rate' => $rate,
                    'amount' => $amount // ✅ NEW
                ]);
            }

            // ✅ UPDATE TOTAL
            $rfi->update([
                'total_amount' => $total
            ]);

            return $rfi;
        });

        return redirect()
            ->route('rfis.index', $company)
            ->with('success', 'RFI Created Successfully');
    }
    public function update(Request $request, Company $company, Rfi $rfi)
    {
        \DB::transaction(function () use ($request, $rfi) {

            if ($rfi->status !== 'pending') {
                abort(403, 'RFI already processed');
            }

            $total = 0;

            // ✅ UPDATE MAIN RFI
            $rfi->update([
                'rfi_date' => Carbon::parse($request->rfi_date)->format('Y-m-d H:i:s'),
                'remark' => $request->remark,
            ]);

            // ❌ DELETE OLD ITEMS
            $rfi->items()->delete();

            // ✅ INSERT NEW ITEMS
            foreach ($request->items as $item) {

                if (!isset($item['selected']))
                    continue;

                $qty = $item['requested_quantity'] ?? 1;
                $rate = $item['rate'] ?? 0;

                $amount = $qty * $rate;
                $total += $amount;

                RfiItem::create([
                    'rfi_id' => $rfi->id,
                    'item_id' => $item['item_id'],
                    'brand_id' => $item['brand_id'],
                    'condition_id' => $item['condition_id'],
                    'location_id' => $item['location_id'],
                    'unit_id' => $item['unit_id'],
                    'current_quantity' => $item['current_quantity'],
                    'min_quantity' => $item['min_quantity'],
                    'requested_quantity' => $qty,
                    'rate' => $rate,
                    'amount' => $amount
                ]);
            }

            // ✅ UPDATE TOTAL
            $rfi->update([
                'total_amount' => $total
            ]);
        });
        return redirect()
            ->route('rfis.index', $company)
            ->with('success', 'RFI Updated Successfully');
    }
    public function approve(Request $request, Company $company)
    {
        \DB::transaction(function () use ($request, $company) {

            $rfi = Rfi::with('items')->findOrFail($request->rfi_id);

            $subtotal = 0;

            // ✅ CREATE PO
            $po = PurchaseOrder::create([
                'company_id' => $company->id,
                'rfi_id' => $rfi->id,
                'remark' => $request->notes,
                'supplier_id' => $request->supplier_id,
                'po_date' => now(),
                'created_by' => auth()->id(),
                'status' => 'pending'
            ]);

            $approvedIds = collect($request->items)->pluck('id')->toArray();

            foreach ($rfi->items as $item) {

                if (in_array($item->id, $approvedIds)) {

                    $row = collect($request->items)->firstWhere('id', $item->id);
                    if (!$row)
                        continue;

                    $qty = (float) $row['qty'];
                    $rate = (float) $row['rate'];

                    $amount = $qty * $rate;
                    $subtotal += $amount;

                    // ✅ UPDATE RFI ITEM
                    $item->update([
                        'approved_quantity' => $qty,
                        'rate' => $rate,
                        'amount' => $amount,
                        'status' => 'approved'
                    ]);

                    // ✅ CREATE PO ITEM
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $po->id,
                        'item_id' => $item->item_id,
                        'brand_id' => $item->brand_id,
                        'condition_id' => $item->condition_id,
                        'location_id' => $item->location_id,
                        'unit_id' => $item->unit_id,
                        'quantity' => $qty,
                        'rate' => $rate,
                        'specification_id' => $row['specification_id'] ?? null,
                        'amount' => $amount
                    ]);
                } else {
                    $item->update([
                        'approved_quantity' => 0,
                        'status' => 'rejected'
                    ]);
                }
            }

            // ================= CALCULATIONS =================

            $discount = (float) $request->discount;
            $tax = (float) $request->tax;

            $afterDiscount = $subtotal - $discount;
            $taxAmount = ($afterDiscount * $tax) / 100;
            $finalAmount = $afterDiscount + $taxAmount;

            // ================= UPDATE PO =================

            $po->update([
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'tax_amount' => $taxAmount,
                'final_amount' => $finalAmount,
                'total_amount' => $finalAmount,
                'po_code' =>
                    $company->initials()
                    . '#'
                    . Auth::id()
                    . '/PO/'
                    . now()->format('Ymd')
                    . '/'
                    . str_pad($po->id, 4, '0', STR_PAD_LEFT)
            ]);

            // ================= UPDATE RFI =================

            $rfi->update([
                'status' => 'approved',
                'notes' => $request->notes,
                'approved_by' => auth()->id()
            ]);
        });

        return response()->json([
            'status' => true,
            'redirect' => route('pos.index', $company->id)
        ]);
    }
    public function reject(Request $request, Company $company)
    {
        $rfi = Rfi::findOrFail($request->rfi_id);

        $rfi->update([
            'status' => 'rejected',
            'notes' => $request->notes,
            'approved_by' => auth()->id()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'RFI Rejected Successfully'
        ]);
    }
}