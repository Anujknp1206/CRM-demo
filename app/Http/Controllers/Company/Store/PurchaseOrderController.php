<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ItemUnitConversion;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Setting;
use Carbon\Carbon;

class PurchaseOrderController extends Controller
{
    public function index(Company $company)
    {
        return view('company.store.po.index', [
            'company' => $company,
            'title' => Auth::user()->name . " PO Management",
            'label' => "Purchase Orders",
        ]);
    }

    public function data(Request $request, Company $company)
    {
        $pos = PurchaseOrder::with(['supplier', 'rfi'])
            ->where('company_id', $company->id)
            ->latest()
            ->get();

        return view('company.store.po.partials.po_rows', compact('pos', 'company'))->render();
    }

    public function view(Company $company, $id)
    {
        $po = PurchaseOrder::with([
            'supplier',
            'items.item',
            'items.brand',
            'items.condition',
            'items.unit',
            'items.location',
            'rfi.items',
            'items.specification'
        ])->findOrFail($id);

        return view('company.store.po.partials.view_po', compact('po'))->render();
    }
    public function searchPO(Request $request, Company $company)
    {
        $search = $request->search;

        $pos = PurchaseOrder::with('supplier')
            ->where('company_id', $company->id)
            ->where(function ($q) use ($search) {
                $q->where('po_code', 'like', "%$search%")
                    ->orWhere('id', $search)
                    ->orWhereHas('supplier', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    });
            })
            ->limit(20)
            ->get();

        return response()->json(
            $pos->map(function ($po) {
                return [
                    'id' => $po->id,
                    'po_code' => $po->po_code,
                    'supplier_name' => optional($po->supplier)->name
                ];
            })
        );
    }
    public function getPOItems(
        Company $company,
        $poId
    ) {
        $po = PurchaseOrder::with([

            'items.item',

            'items.brand',

            'items.condition',

            'items.location',

            'items.unit',

            'supplier'

        ])->findOrFail($poId);

        return response()->json([

            'supplier_id' =>
                $po->supplier_id,

            'po_date' =>
                Carbon::parse(
                    $po->po_date
                )->format('d/m/Y'),

            'supplier_name' =>
                optional($po->supplier)->name,

            'items' => $po->items->map(
                function ($item) use ($company) {

                    /*
                    |--------------------------------------------------------------------------
                    | REMAINING QTY
                    |--------------------------------------------------------------------------
                    */

                    $remainingQty =
                        $item->quantity
                        - $item->received_quantity;

                    /*
                    |--------------------------------------------------------------------------
                    | GET UNIT CONVERSION
                    |--------------------------------------------------------------------------
                    */

                    $conversion =
                        ItemUnitConversion::with(
                            'toUnit'
                        )
                            ->where([

                                'company_id' =>
                                    $company->id,

                                'item_id' =>
                                    $item->item_id,

                                /*
                                |--------------------------------------------------------------------------
                                | ENTRY UNIT
                                |--------------------------------------------------------------------------
                                */
                                'from_unit_id' =>
                                    $item->unit_id,

                            ])
                            ->first();

                    /*
                    |--------------------------------------------------------------------------
                    | BASE UNIT
                    |--------------------------------------------------------------------------
                    */

                    $baseUnitId =
                        $conversion?->to_unit_id
                        ?? $item->unit_id;

                    $baseUnitName =
                        optional(
                            $conversion?->toUnit
                        )->name
                        ?? optional(
                            $item->unit
                        )->name;

                    /*
                    |--------------------------------------------------------------------------
                    | FACTOR
                    |--------------------------------------------------------------------------
                    */

                    $factor =
                        $conversion?->factor
                        ?? 1;

                    /*
                    |--------------------------------------------------------------------------
                    | FINAL STOCK QTY
                    |--------------------------------------------------------------------------
                    */

                    $stockQty =
                        $remainingQty
                        * $factor;

                    return [

                        'po_item_id' =>
                            $item->id,

                        'item_id' =>
                            $item->item_id,

                        'item_name' =>
                            optional(
                                $item->item
                            )->name,

                        /*
                        |--------------------------------------------------------------------------
                        | BRAND
                        |--------------------------------------------------------------------------
                        */

                        'brand_id' =>
                            $item->brand_id,

                        'brand_name' =>
                            optional(
                                $item->brand
                            )->name,

                        /*
                        |--------------------------------------------------------------------------
                        | CONDITION
                        |--------------------------------------------------------------------------
                        */

                        'condition_id' =>
                            $item->condition_id,

                        'condition_name' =>
                            optional(
                                $item->condition
                            )->name,

                        /*
                        |--------------------------------------------------------------------------
                        | LOCATION
                        |--------------------------------------------------------------------------
                        */

                        'location_id' =>
                            $item->location_id,

                        'location_name' =>
                            optional(
                                $item->location
                            )->name,

                        /*
                        |--------------------------------------------------------------------------
                        | ENTRY UNIT
                        |--------------------------------------------------------------------------
                        */

                        'unit_id' =>
                            $item->unit_id,

                        'unit_name' =>
                            optional(
                                $item->unit
                            )->name,

                        /*
                        |--------------------------------------------------------------------------
                        | BASE UNIT
                        |--------------------------------------------------------------------------
                        */

                        'base_unit_id' =>
                            $baseUnitId,

                        'base_unit_name' =>
                            $baseUnitName,

                        /*
                        |--------------------------------------------------------------------------
                        | CONVERSION
                        |--------------------------------------------------------------------------
                        */

                        'conversion_factor' =>
                            $factor,

                        /*
                        |--------------------------------------------------------------------------
                        | FINAL STOCK QTY
                        |--------------------------------------------------------------------------
                        */

                        'stock_quantity' =>
                            $stockQty,

                        /*
                        |--------------------------------------------------------------------------
                        | RATE
                        |--------------------------------------------------------------------------
                        */

                        'rate' =>
                            $item->rate,

                        /*
                        |--------------------------------------------------------------------------
                        | QTY INFO
                        |--------------------------------------------------------------------------
                        */

                        'ordered_qty' =>
                            $item->quantity,

                        'received_qty' =>
                            $item->received_quantity,

                        'remaining_qty' =>
                            $remainingQty,
                    ];
                }
            )
        ]);
    }
    // ✏️ EDIT
    public function edit(Company $company, $id)
    {
        $po = PurchaseOrder::with([
            'supplier',
            'items.item',
            'items.brand',
            'items.condition',
            'items.location',
            'rfi.items'
        ])->findOrFail($id);

        return view('company.store.po.partials.edit_po', compact('po'))->render();
    }

    // 🔥 UPDATE + RFI SYNC
    public function update(Request $request, Company $company, $id)
    {
        $po = PurchaseOrder::with('items', 'rfi.items')->findOrFail($id);

        $subtotal = 0;

        foreach ($request->items as $itemId => $data) {

            $poItem = PurchaseOrderItem::find($itemId);

            $qty = (float) $data['quantity'];
            $rate = (float) $data['rate'];

            $amount = $qty * $rate;

            $subtotal += $amount;

            // ✅ UPDATE PO ITEM
            $poItem->update([
                'quantity' => $qty,
                'rate' => $rate,
                'amount' => $amount
            ]);

            // 🔥 SYNC WITH RFI
            $rfiItem = $po->rfi->items
                ->where('item_id', $poItem->item_id)
                ->first();

            if ($rfiItem) {
                $rfiItem->update([
                    'approved_quantity' => $qty,
                    'rate' => $rate,
                    'amount' => $amount
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

            // 🔥 SUMMERNOTE REMARK SAVE
            'remark' => $request->remark
        ]);

        return response()->json([
            'success' => true,
            'message' => 'PO Updated Successfully'
        ]);
    }

    public function print(Company $company, PurchaseOrder $po)
    {
        $user = Auth::user();

        $title = $user->name . " :: Purchase Orders Print Preview";
        $label = "Preview Purchase Orders";

        $po->load([
            'items.item',
            'items.brand',
            'items.condition',
            'items.location',
            'supplier',
            'creator',
            'rfi'
        ]);

        return view('company.store.po.print', compact(
            'company',
            'po',
            'title',
            'label'
        ));
    }
    public function pdf($companyId, $poId, Request $request)
    {
        $company = Company::findOrFail($companyId);

        $po = PurchaseOrder::with([
            'items.item',
            'items.brand',
            'items.condition',
            'items.location',
            'supplier',
            'creator',
            'rfi'
        ])->findOrFail($poId);

        $settings = Setting::first();

        // ================= FILTER INPUT =================

        $sections = array_filter(explode(',', $request->sections ?? ''));
        $extras = array_filter(explode(',', $request->extras ?? ''));
        $columns = array_filter(explode(',', $request->columns ?? ''));
        $rows = $request->has('rows') ? explode(',', $request->rows) : [];

        // ================= FILTER ROWS =================

        if (!empty($rows)) {
            $po->setRelation(
                'items',
                $po->items->filter(function ($item, $index) use ($rows) {
                    return in_array((string) $index, $rows, true);
                })->values()
            );
        }

        // ================= PDF =================

        $pdf = Pdf::loadView(
            'company.store.po.pdf',
            compact('po', 'company', 'settings', 'sections', 'extras', 'columns')
        )->setPaper('a4', 'portrait');

        // ================= PAGE NUMBER =================

        $pdf->render();
        $canvas = $pdf->getCanvas();

        $canvas->page_text(
            520,
            810,
            "Page {PAGE_NUM} of {PAGE_COUNT}",
            null,
            7,
            [255, 255, 255]
        );

        // ================= FILE NAME =================

        $fileName = $this->generateFileName(
            $company,
            'po',
            $po->po_code,
            $po->supplier->name ?? 'Supplier'
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
}
