<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use App\Models\ItemUnitConversion;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\{
    Company,
    StockIn,
    StockInItem,
    Stock,
    Brand,
    Unit,
    Condition,
    IssueItem,
    Item,
    Supplier,
    Location,
    PurchaseOrder,
    PurchaseOrderItem
};
use DB;
use Illuminate\Support\Str;

class StockInController extends Controller
{
    public function index(Request $request, Company $company)
    {
        $title = $company->company_name . " - Stock-In Management";
        return view('company.store.stocks.index', [
            'company' => $company,
            'label' => 'Stock In List',
            'title' => $title,
        ]);
    }
    public function data(Request $request, Company $company)
    {
        $query = StockIn::with(['supplier', 'items'])
            ->where('company_id', $company->id);

        if ($request->from_date || $request->to_date || $request->search) {

            if ($request->from_date) {
                $query->whereDate('doc_date', '>=', $request->from_date);
            }

            if ($request->to_date) {
                $query->whereDate('doc_date', '<=', $request->to_date);
            }

            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('doc_no', 'LIKE', "%{$request->search}%")
                        ->orWhereHas('supplier', function ($s) use ($request) {
                            $s->where('name', 'LIKE', "%{$request->search}%");
                        });
                });
            }

            $stockIns = $query->latest()->get(); // filtered results

        } else {
            // 🔥 DEFAULT: last 10 entries (NOT today)
            $stockIns = $query->latest()->take(10)->get();
        }



        $stockIns = $query->orderByDesc('doc_date')->take(10)->get();
        return view(
            'company.store.stocks.partials.stocks_row',
            compact('stockIns', 'company')
        )->render();
    }
    public function ajaxSearch(Request $request, Company $company)
    {
        $term = $request->search;

        $results = StockIn::with('supplier')
            ->where('company_id', $company->id)
            ->where(function ($q) use ($term) {
                $q->where('doc_no', 'LIKE', "%{$term}%")
                    ->orWhereHas('supplier', function ($s) use ($term) {
                        $s->where('name', 'LIKE', "%{$term}%");
                    });
            })
            ->limit(10)
            ->get();

        return response()->json(
            $results->map(function ($row) {
                return [
                    'id' => $row->doc_no,
                    'text' => $row->doc_no . ' - ' . optional($row->supplier)->name
                ];
            })
        );
    }
    public function createWithPO(Request $request, Company $company)
    {
        $today = now();

        // 🔥 next ID (safe way)
        $nextId = StockIn::max('id') + 1;

        // 🔥 format number (0001)
        $formattedId = str_pad($nextId, 4, '0', STR_PAD_LEFT);

        // 🔥 build doc number
        $docNo = $company->initials()
            . '#'
            . Auth::id()
            . '/GRN/'
            . $today->format('Ymd')
            . '/'
            . $formattedId;
        $poId = $request->po_id;
        $po = null;

        if ($poId) {
            $po = PurchaseOrder::with('supplier')
                ->find($poId);
        }
        $title = $company->company_name . " - Stock In (With PO)";

        return view('company.store.stocks.createwithpo', [
            'company' => $company,
            'docNo' => $docNo,
            'title' => $title,
            'label' => 'Stock Entry (With Purchase Order)',

            'grn_date' => $today->format('d/m/Y'),
            'po_date' => $po ? Carbon::parse($po->po_date)->format('d/m/Y') : null,
            // ✅ REQUIRED DATA (same as normal)
            'parentLocations' => Location::where('company_id', $company->id)->get(),
            'brands' => Brand::where('company_id', $company->id)->get(),
            'conditions' => Condition::where('company_id', $company->id)->get(),
            'units' => Unit::where(
                'company_id',
                $company->id
            )->get(),
            'items' => Item::with('unit')->where('company_id', $company->id)->get(),
            'suppliers' => Supplier::where('company_id', $company->id)->get(),
            'selected_po_id' => $poId,
            'locations' => Location::with('children.children')
                ->where('company_id', $company->id)
                ->whereNull('parent_id')
                ->get(),
        ]);
    }
    public function create(Company $company)
    {
        $today = now();

        // 🔥 next ID (safe way)
        $nextId = StockIn::max('id') + 1;

        // 🔥 format number (0001)
        $formattedId = str_pad($nextId, 4, '0', STR_PAD_LEFT);

        // 🔥 build doc number
        $docNo = $company->initials()
            . '#'
            . Auth::id()
            . '/GRN/'
            . $today->format('Ymd')
            . '/'
            . $formattedId;


        $title = $company->company_name . " - Stockin  Management";

        return view('company.store.stocks.create', [
            'company' => $company,
            'docNo' => $docNo,
            'title' => $title,
            'label' => 'Stock Entry (Without Purchase Order)',
            'parentLocations' => Location::where('company_id', $company->id)->get(),
            'brands' => Brand::where('company_id', $company->id)->get(),
            'conditions' => Condition::where('company_id', $company->id)->get(),
            'items' => Item::with('unit')->where('company_id', $company->id)->get(),
            'suppliers' => Supplier::where('company_id', $company->id)->get(),
            'locations' => Location::with('children.children')
                ->where('company_id', $company->id)
                ->whereNull('parent_id') // Halls
                ->get(),
        ]);
    }
    public function store(Request $request, Company $company)
    {
        // dd($request->all());
        $affectedItems = [];

        DB::transaction(function () use ($request, $company, &$affectedItems) {

            /*
            |--------------------------------------------------------------------------
            | FILE UPLOAD
            |--------------------------------------------------------------------------
            */

            $filePath = null;

            if ($request->hasFile('supplier_document')) {

                $file = $request->file('supplier_document');

                $supplier = Supplier::find($request->supplier_id);

                $supplierName = Str::slug(
                    $supplier?->name ?? 'supplier'
                );

                $cleanDocNo = str_replace(
                    ['#', '/', '\\'],
                    '-',
                    $request->doc_no
                );

                $ext = $file->getClientOriginalExtension();

                $fileName =
                    $supplierName .
                    '_PO_' .
                    $cleanDocNo .
                    '.' .
                    $ext;

                $basePath = rtrim(
                    config('url.public_path'),
                    '/\\'
                );

                $uploadPath =
                    $basePath .
                    DIRECTORY_SEPARATOR .
                    'GRN';

                if (!file_exists($uploadPath)) {

                    mkdir($uploadPath, 0777, true);
                }

                $file->move($uploadPath, $fileName);

                $filePath =
                    'admin/uploads/GRN/' .
                    $fileName;
            }

            /*
            |--------------------------------------------------------------------------
            | DATES
            |--------------------------------------------------------------------------
            */

            $docDate = $this->parseDate(
                $request->doc_date
            );

            $poDate = $this->parseDate(
                $request->po_date
            );

            $supplierDate = $this->parseDate(
                $request->supplier_date
            );

            /*
            |--------------------------------------------------------------------------
            | CREATE STOCK IN
            |--------------------------------------------------------------------------
            */

            $stockIn = StockIn::create([

                'company_id' => $company->id,

                'doc_no' => $request->doc_no,

                'doc_date' => $docDate,

                'grn_date' => now(),

                'po_date' => $poDate,

                'supplier_date' => $supplierDate,

                'sup_doc_num' => $request->sup_doc_num,

                'supplier_document' => $filePath,

                'purchase_order_id' =>
                    $request->purchase_order_id,

                'supplier_id' =>
                    $request->supplier_id,

                'remark' => $request->remark,
            ]);

            /*
            |--------------------------------------------------------------------------
            | ITEMS
            |--------------------------------------------------------------------------
            */

            foreach ($request->items as $row) {

                $data = collect($row);

                /*
                |--------------------------------------------------------------------------
                | ENTRY UNIT
                |--------------------------------------------------------------------------
                |
                | Physical unit entered by user
                | Example:
                | BOX
                |
                */

                $entryUnitId =
                    $data->get('entry_unit_id');

                /*
                |--------------------------------------------------------------------------
                | BASE UNIT
                |--------------------------------------------------------------------------
                |
                | Permanent inventory unit
                | Example:
                | PCS
                |
                */

                $baseUnitId =
                    $data->get('base_unit_id');

                /*
                |--------------------------------------------------------------------------
                | PHYSICAL ENTRY QTY
                |--------------------------------------------------------------------------
                |
                | Example:
                | 2 BOX
                |
                */

                $entryQty =
                    (float) $data->get('entry_quantity');

                /*
                |--------------------------------------------------------------------------
                | CONVERSION FACTOR
                |--------------------------------------------------------------------------
                |
                | Example:
                | 1 BOX = 50 PCS
                |
                */

                $conversionFactor =
                    (float) $data->get('conversion_factor');

                /*
                |--------------------------------------------------------------------------
                | NORMALIZED STOCK QTY
                |--------------------------------------------------------------------------
                |
                | Example:
                | 2 × 50 = 100 PCS
                |
                */

                $stockQty =
                    (float) $data->get('stock_quantity');

                /*
                |--------------------------------------------------------------------------
                | SAFETY VALIDATION
                |--------------------------------------------------------------------------
                */

                if (
                    $stockQty <= 0
                ) {

                    throw ValidationException::withMessages([
                        'items' =>
                            'Invalid stock quantity detected.'
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | SAVE CONVERSION IF NOT EXISTS
                |--------------------------------------------------------------------------
                */

                $exists = ItemUnitConversion::where([

                    'item_id' =>
                        $data->get('item_id'),

                    'from_unit_id' =>
                        $entryUnitId,

                    'to_unit_id' =>
                        $baseUnitId,

                ])->exists();

                /*
                |--------------------------------------------------------------------------
                | STORE NEW CONVERSION
                |--------------------------------------------------------------------------
                */

                if (
                    !$exists &&
                    $entryUnitId != $baseUnitId
                ) {

                    ItemUnitConversion::create([

                        'company_id' => $company->id,

                        'item_id' =>
                            $data->get('item_id'),

                        'from_unit_id' =>
                            $entryUnitId,

                        'to_unit_id' =>
                            $baseUnitId,

                        'factor' =>
                            $conversionFactor,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | STOCK IN ITEM
                |--------------------------------------------------------------------------
                */

                StockInItem::create([

                    'stock_in_id' => $stockIn->id,

                    /*
                    |--------------------------------------------------------------------------
                    | PO SUPPORT
                    |--------------------------------------------------------------------------
                    |
                    | Works for:
                    | - with PO
                    | - without PO
                    |
                    */

                    'purchase_order_item_id' =>
                        $data->get('po_item_id'),

                    'item_id' =>
                        $data->get('item_id'),

                    'brand_id' =>
                        $data->get('brand_id'),

                    'condition_id' =>
                        $data->get('condition_id'),

                    'location_id' =>
                        $data->get('location_id'),

                    /*
                    |--------------------------------------------------------------------------
                    | ENTRY UNIT
                    |--------------------------------------------------------------------------
                    */

                    'unit_id' => $entryUnitId,

                    /*
                    |--------------------------------------------------------------------------
                    | BASE UNIT
                    |--------------------------------------------------------------------------
                    */

                    'stock_unit_id' => $baseUnitId,

                    /*
                    |--------------------------------------------------------------------------
                    | PHYSICAL QTY
                    |--------------------------------------------------------------------------
                    */

                    'quantity' => $entryQty,

                    /*
                    |--------------------------------------------------------------------------
                    | NORMALIZED STOCK
                    |--------------------------------------------------------------------------
                    */

                    'stock_quantity' => $stockQty,

                    'rate' =>
                        $data->get('rate'),

                    'supplier_rate' =>
                        $data->get('supplier_rate'),
                ]);

                /*
                |--------------------------------------------------------------------------
                | STOCK UPDATE
                |--------------------------------------------------------------------------
                |
                | STOCK TABLE ALWAYS STORES:
                | BASE UNIT ONLY
                |
                */

                $stock = Stock::firstOrNew([

                    'company_id' => $company->id,

                    'item_id' =>
                        $data->get('item_id'),

                    'brand_id' =>
                        $data->get('brand_id'),

                    'condition_id' =>
                        $data->get('condition_id'),

                    'location_id' =>
                        $data->get('location_id'),

                    /*
                    |--------------------------------------------------------------------------
                    | BASE UNIT ONLY
                    |--------------------------------------------------------------------------
                    */

                    'unit_id' => $baseUnitId,
                ]);

                /*
                |--------------------------------------------------------------------------
                | ADD NORMALIZED STOCK
                |--------------------------------------------------------------------------
                */

                $stock->quantity =
                    ($stock->quantity ?? 0)
                    + $stockQty;

                $stock->save();

                checkLowStock($stock);

                /*
                |--------------------------------------------------------------------------
                | UPDATE PO RECEIVED QTY
                |--------------------------------------------------------------------------
                */

                if (
                    $data->get('po_item_id')
                ) {

                    PurchaseOrderItem::where(
                        'id',
                        $data->get('po_item_id')
                    )->increment(
                            'received_quantity',
                            $entryQty
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | ISSUE NOTIFICATION TRACKING
                |--------------------------------------------------------------------------
                */

                $affectedItems[] = [

                    'item_id' =>
                        $data->get('item_id'),

                    'location_id' =>
                        $data->get('location_id'),
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE PO STATUS
            |--------------------------------------------------------------------------
            */

            if ($request->purchase_order_id) {

                $po = PurchaseOrder::with('items')
                    ->find($request->purchase_order_id);

                $allReceived =
                    $po->items->every(function ($item) {

                        return
                            $item->received_quantity
                            >=
                            $item->quantity;
                    });

                if ($allReceived) {

                    $po->update([
                        'status' => 'received'
                    ]);

                } else {

                    $po->update([
                        'status' => 'partial'
                    ]);
                }
            }
        });

        /*
        |--------------------------------------------------------------------------
        | NOTIFY PENDING ISSUES
        |--------------------------------------------------------------------------
        */

        $this->notifyPendingIssues(
            $company,
            $affectedItems
        );

        /*
        |--------------------------------------------------------------------------
        | ADMIN NOTIFICATION
        |--------------------------------------------------------------------------
        */

        notifyAdmins(

            'Stock In Added',

            "Stock entry {$request->doc_no} added",

            route(
                'stock-ins.index',
                $company->id
            ),

            'success',
        );

        toast(
            'Stock added successfully',
            'success'
        );

        return redirect()->route(
            'stock-ins.index',
            $company
        );
    }
    public function parseDate($date)
    {
        if (!$date)
            return null;

        try {
            // If already Y-m-d → return directly
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return $date;
            }

            // If d/m/Y → convert
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
                return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            }

            // fallback (just in case)
            return Carbon::parse($date)->format('Y-m-d');

        } catch (\Exception $e) {
            return null;
        }
    }
    public function view(Company $company, $id)
    {
        $stock = StockIn::with([
            'supplier',
            'purchaseOrder',
            'items.item',
            'items.brand',
            'items.condition',
            'items.unit',
            'items.stockUnit',
            'items.location'
        ])->findOrFail($id);

        return response()->json([
            'doc_no' => $stock->doc_no,

            // 🔥 format date for UI
            'doc_date' => Carbon::parse($stock->doc_date)->format('d/m/Y'),

            // 🔥 NEW FIELDS
            'supplier_date' => $stock->supplier_date
                ? Carbon::parse($stock->supplier_date)->format('d/m/Y')
                : null,

            'supplier_document' => $stock->supplier_document,
            'sup_doc_num' => $stock->sup_doc_num,

            'purchase_order_id' => $stock->purchase_order_id,

            // 🔥 PO CODE
            'po_code' => optional($stock->purchaseOrder)->po_code,

            'supplier' => $stock->supplier,

            'remark' => $stock->remark,

            // 🔥 ITEMS WITH supplier_rate
            'items' => $stock->items->map(function ($item) {
                return [
                    'item' => $item->item,
                    'brand' => $item->brand,
                    'condition' => $item->condition,
                    'unit' => $item->unit,
                    'stock_unit' => $item->stockUnit,
                    'location' => $item->location,
                    'quantity' => $item->quantity,
                    'stock_quantity' => $item->stock_quantity,
                    'rate' => $item->rate,
                    'supplier_rate' => $item->supplier_rate,
                ];
            }),
        ]);
    }
    public function edit(Company $company, StockIn $stockIn)
    {
        $title = $company->company_name . " - Department Management";
        return view('company.store.stocks.edit', [
            'company' => $company,
            'title' => $title,
            'stockIn' => $stockIn->load(['items.item', 'items.location']),
            'parentLocations' => Location::where('company_id', $company->id)->get(),
            'items' => Item::where('company_id', $company->id)->get(),
            'brands' => Brand::where('company_id', $company->id)->get(),
            'conditions' => Condition::where('company_id', $company->id)->get(),
            'units' => Unit::where('company_id', $company->id)->get(),
            'suppliers' => Supplier::where('company_id', $company->id)->get(),
            'locations' => Location::with('children.children')
                ->where('company_id', $company->id)
                ->whereNull('parent_id')
                ->get(),
            'label' => 'Edit Stock In'
        ]);
    }
    public function editWithPo(Request $request,Company $company,StockIn $stockIn) {

        $title =
            $company->company_name .
            " - Department Management";

        $stockIn->load([

            'supplier',

            'purchaseOrder.items.item',
            'purchaseOrder.items.brand',
            'purchaseOrder.items.condition',
            'purchaseOrder.items.location',
            'purchaseOrder.items.unit',

            'items.stockUnit'
        ]);

        /*
        |--------------------------------------------------------------------------
        | ONLY PO BASED
        |--------------------------------------------------------------------------
        */

        if (!$stockIn->purchase_order_id) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'This stock in is not PO based.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | BUILD TABLE ROWS
        |--------------------------------------------------------------------------
        */

        $poItems = [];

        foreach (
            $stockIn->purchaseOrder->items
            as $poItem
        ) {

            /*
            |--------------------------------------------------------------------------
            | EXISTING STOCK IN ITEM
            |--------------------------------------------------------------------------
            */

            $existing = $stockIn->items
                ->where(
                    'purchase_order_item_id',
                    $poItem->id
                )
                ->first();

            /*
            |--------------------------------------------------------------------------
            | FALLBACK OLD RECORDS
            |--------------------------------------------------------------------------
            */

            if (!$existing) {

                $existing = $stockIn->items
                    ->where(
                        'item_id',
                        $poItem->item_id
                    )
                    ->first();
            }

            /*
            |--------------------------------------------------------------------------
            | CONVERSION
            |--------------------------------------------------------------------------
            */

            $conversion =
                ItemUnitConversion::with([
                    'fromUnit',
                    'toUnit'
                ])
                    ->where(
                        'item_id',
                        $poItem->item_id
                    )
                    ->where(
                        'from_unit_id',
                        $poItem->unit_id
                    )
                    ->first();

            /*
            |--------------------------------------------------------------------------
            | BASE UNIT
            |--------------------------------------------------------------------------
            */

            $baseUnitId =
                $existing->stock_unit_id
                ??
                $conversion?->to_unit_id
                ??
                $poItem->unit_id;

            /*
            |--------------------------------------------------------------------------
            | BASE UNIT NAME
            |--------------------------------------------------------------------------
            */

            $baseUnitName =
                optional(
                    $existing?->stockUnit
                )->name
                ??
                optional(
                    $conversion?->toUnit
                )->name
                ??
                optional(
                    $poItem->unit
                )->name
                ??
                '';

            /*
            |--------------------------------------------------------------------------
            | FACTOR
            |--------------------------------------------------------------------------
            */

            $conversionFactor =
                $conversion?->factor
                ?? 1;

            /*
            |--------------------------------------------------------------------------
            | ENTRY QTY
            |--------------------------------------------------------------------------
            */

            $entryQty =
                $existing->quantity
                ?? 0;

            /*
            |--------------------------------------------------------------------------
            | STOCK QTY
            |--------------------------------------------------------------------------
            */

            $stockQty =
                $existing->stock_quantity
                ??
                (
                    $entryQty
                    *
                    $conversionFactor
                );

            /*
            |--------------------------------------------------------------------------
            | PUSH
            |--------------------------------------------------------------------------
            */

            $poItems[] = [

                'po_item_id' => $poItem->id,

                /*
                |--------------------------------------------------------------------------
                | ITEM
                |--------------------------------------------------------------------------
                */

                'item_id' =>
                    $poItem->item_id,

                'item_name' =>
                    $poItem->item->name ?? '',

                /*
                |--------------------------------------------------------------------------
                | BRAND
                |--------------------------------------------------------------------------
                */

                'brand_id' =>
                    $poItem->brand_id,

                'brand_name' =>
                    $poItem->brand->name ?? '',

                /*
                |--------------------------------------------------------------------------
                | CONDITION
                |--------------------------------------------------------------------------
                */

                'condition_id' =>
                    $poItem->condition_id,

                'condition_name' =>
                    $poItem->condition->name ?? '',

                /*
                |--------------------------------------------------------------------------
                | LOCATION
                |--------------------------------------------------------------------------
                */

                'location_id' =>
                    $poItem->location_id,

                'location_name' =>
                    $poItem->location->name ?? '',

                /*
                |--------------------------------------------------------------------------
                | ENTRY UNIT
                |--------------------------------------------------------------------------
                */

                'unit_id' =>
                    $poItem->unit_id,

                'unit_name' =>
                    $poItem->unit->name ?? '',

                /*
                |--------------------------------------------------------------------------
                | BASE UNIT
                |--------------------------------------------------------------------------
                */

                'stock_unit_id' =>
                    $baseUnitId,

                'base_unit_id' =>
                    $baseUnitId,

                'base_unit_name' =>
                    $baseUnitName,

                /*
                |--------------------------------------------------------------------------
                | FACTOR
                |--------------------------------------------------------------------------
                */

                'conversion_factor' =>
                    $conversionFactor,

                /*
                |--------------------------------------------------------------------------
                | STOCK QTY
                |--------------------------------------------------------------------------
                */

                'stock_quantity' =>
                    $stockQty,

                /*
                |--------------------------------------------------------------------------
                | PO QTY
                |--------------------------------------------------------------------------
                */

                'po_qty' =>
                    $poItem->quantity,

                /*
                |--------------------------------------------------------------------------
                | CHECKED
                |--------------------------------------------------------------------------
                */

                'checked' =>
                    !is_null($existing),

                /*
                |--------------------------------------------------------------------------
                | RECEIVED QTY
                |--------------------------------------------------------------------------
                */

                'received_qty' =>
                    $entryQty,

                /*
                |--------------------------------------------------------------------------
                | RATE
                |--------------------------------------------------------------------------
                */

                'rate' =>
                    $existing->rate
                    ??
                    $poItem->rate,

                /*
                |--------------------------------------------------------------------------
                | SUPPLIER RATE
                |--------------------------------------------------------------------------
                */

                'supplier_rate' =>
                    $existing->supplier_rate
                    ?? 0,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'company.store.stocks.editwithpo',
            [

                'stockIn' => $stockIn,

                'company' => $company,

                'title' => $title,

                'label' => 'Edit Stock In',

                'poItems' => $poItems
            ]
        );
    }
    public function update(Request $request, Company $company, StockIn $stockIn)
    {
        // dd($request->all());
        $request->validate([
            'items.*.item_id' => 'required|exists:items,id',
        ]);

        $affectedItems = [];

        DB::transaction(function () use ($request, $company, $stockIn, &$affectedItems) {

            /*
            ==========================================
            KEEP OLD PO FOR STATUS RECALCULATION
            ==========================================
            */

            $oldPurchaseOrderId = $stockIn->purchase_order_id;

            /*
            ==========================================
            STEP 1: REVERSE OLD STOCK
            ==========================================
            */

            foreach ($stockIn->items as $old) {

                $stock = Stock::where([

                    'company_id' => $company->id,

                    'item_id' => $old->item_id,

                    'brand_id' => $old->brand_id,

                    'condition_id' => $old->condition_id,

                    'location_id' => $old->location_id,

                    /*
                    |--------------------------------------------------------------------------
                    | STOCK ALWAYS IN BASE UNIT
                    |--------------------------------------------------------------------------
                    */
                    'unit_id' => $old->stock_unit_id ?? $old->unit_id,

                ])->lockForUpdate()->first();

                if ($stock) {

                    /*
                    |--------------------------------------------------------------------------
                    | REVERSE STOCK USING STOCK QUANTITY
                    |--------------------------------------------------------------------------
                    */
                    $stock->quantity -= (
                        $old->stock_quantity ?? $old->quantity
                    );

                    if ($stock->quantity <= 0) {

                        $stock->delete();

                    } else {

                        $stock->save();

                        checkLowStock($stock);
                    }
                }

                /*
                ==================================
                REVERSE PO RECEIVED QTY
                ==================================
                */

                if ($old->purchase_order_item_id) {

                    PurchaseOrderItem::where(
                        'id',
                        $old->purchase_order_item_id
                    )->decrement(
                            'received_quantity',
                            $old->quantity
                        );
                }
            }

            /*
            ==========================================
            STEP 2: DELETE OLD ITEMS
            ==========================================
            */

            $stockIn->items()->delete();

            $filePath = $stockIn->supplier_document;

            /*
            ==========================================
            UPDATE FILE
            ==========================================
            */

            if ($request->hasFile('supplier_document')) {

                /*
                DELETE OLD FILE
                */

                if ($stockIn->supplier_document) {

                    $oldFile = public_path($stockIn->supplier_document);

                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                /*
                UPLOAD NEW FILE
                */

                $file = $request->file('supplier_document');

                $supplier = Supplier::find($request->supplier_id);

                $supplierName = Str::slug(
                    $supplier?->name ?? 'supplier'
                );

                $cleanDocNo = str_replace(
                    ['#', '/', '\\'],
                    '-',
                    $request->doc_no
                );

                $ext = $file->getClientOriginalExtension();

                $fileName =
                    $supplierName .
                    '_PO_' .
                    $cleanDocNo .
                    '.' .
                    $ext;

                $basePath = rtrim(
                    config('url.public_path'),
                    '/\\'
                );

                $uploadPath =
                    $basePath .
                    DIRECTORY_SEPARATOR .
                    'GRN';

                if (!file_exists($uploadPath)) {

                    mkdir($uploadPath, 0777, true);
                }

                $file->move($uploadPath, $fileName);

                $filePath =
                    'admin/uploads/GRN/' .
                    $fileName;
            }

            /*
            ==========================================
            STEP 3: UPDATE HEADER
            ==========================================
            */

            $stockIn->update([

                'purchase_order_id' =>
                    $request->purchase_order_id,

                'supplier_document' =>
                    $filePath,

                'remark' => $request->remark,
            ]);

            /*
            ==========================================
            STEP 4: SAVE NEW ITEMS
            ==========================================
            */

            /*
            ==========================================
            CASE 1: PURCHASE ORDER STOCK IN
            ==========================================
            */

        if ($request->purchase_order_id) {

    foreach ($request->selected_items ?? [] as $itemId) {

        /*
        |--------------------------------------------------------------------------
        | ITEM DATA
        |--------------------------------------------------------------------------
        */

        $itemData =
            $request->items[$itemId] ?? null;

        if (!$itemData) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | ENTRY QTY
        |--------------------------------------------------------------------------
        */

        $qty =
            (float) (
                $itemData['entry_quantity']
                ?? 0
            );

        if ($qty <= 0) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | PO ITEM
        |--------------------------------------------------------------------------
        */

        $poItem =
            PurchaseOrderItem::find($itemId);

        if (!$poItem) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | BASE UNIT
        |--------------------------------------------------------------------------
        */

        $stockUnitId =
            $itemData['base_unit_id']
            ?? $poItem->unit_id;

        /*
        |--------------------------------------------------------------------------
        | CONVERSION FACTOR
        |--------------------------------------------------------------------------
        */

        $conversionFactor =
            (float) (
                $itemData['conversion_factor']
                ?? 1
            );

        /*
        |--------------------------------------------------------------------------
        | FINAL STOCK QTY
        |--------------------------------------------------------------------------
        */

        $stockQty =
            (float) (
                $itemData['stock_quantity']
                ?? ($qty * $conversionFactor)
            );

        /*
        |--------------------------------------------------------------------------
        | RATE
        |--------------------------------------------------------------------------
        */

        $rate =
            (float) (
                $itemData['rate']
                ?? 0
            );

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER RATE
        |--------------------------------------------------------------------------
        */

        $supplierRate =
            (float) (
                $itemData['supplier_rate']
                ?? 0
            );

        /*
        |--------------------------------------------------------------------------
        | SAVE CONVERSION
        |--------------------------------------------------------------------------
        */

        if (
            $poItem->unit_id != $stockUnitId
        ) {

            ItemUnitConversion::updateOrCreate(

                [
                    'company_id' => $company->id,

                    'item_id' =>
                        $poItem->item_id,

                    'from_unit_id' =>
                        $poItem->unit_id,
                ],

                [
                    'to_unit_id' =>
                        $stockUnitId,

                    'factor' =>
                        $conversionFactor,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE STOCK IN ITEM
        |--------------------------------------------------------------------------
        */

        StockInItem::create([

            'stock_in_id' =>
                $stockIn->id,

            'purchase_order_item_id' =>
                $poItem->id,

            'item_id' =>
                $poItem->item_id,

            'brand_id' =>
                $poItem->brand_id,

            'condition_id' =>
                $poItem->condition_id,

            'location_id' =>
                $poItem->location_id,

            /*
            |--------------------------------------------------------------------------
            | ENTRY UNIT
            |--------------------------------------------------------------------------
            */

            'unit_id' =>
                $poItem->unit_id,

            /*
            |--------------------------------------------------------------------------
            | ENTRY QTY
            |--------------------------------------------------------------------------
            */

            'quantity' =>
                $qty,

            /*
            |--------------------------------------------------------------------------
            | BASE UNIT
            |--------------------------------------------------------------------------
            */

            'stock_unit_id' =>
                $stockUnitId,

            /*
            |--------------------------------------------------------------------------
            | CONVERSION FACTOR
            |--------------------------------------------------------------------------
            */

            'conversion_factor' =>
                $conversionFactor,

            /*
            |--------------------------------------------------------------------------
            | FINAL STOCK QTY
            |--------------------------------------------------------------------------
            */

            'stock_quantity' =>
                $stockQty,

            /*
            |--------------------------------------------------------------------------
            | RATES
            |--------------------------------------------------------------------------
            */

            'rate' =>
                $rate,

            'supplier_rate' =>
                $supplierRate,
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE STOCK
        |--------------------------------------------------------------------------
        */

        $stock = Stock::firstOrNew([

            'company_id' =>
                $company->id,

            'item_id' =>
                $poItem->item_id,

            'brand_id' =>
                $poItem->brand_id,

            'condition_id' =>
                $poItem->condition_id,

            'location_id' =>
                $poItem->location_id,

            /*
            |--------------------------------------------------------------------------
            | STOCK ALWAYS IN BASE UNIT
            |--------------------------------------------------------------------------
            */

            'unit_id' =>
                $stockUnitId,
        ]);

        /*
        |--------------------------------------------------------------------------
        | ADD STOCK
        |--------------------------------------------------------------------------
        */

        $stock->quantity =
            ($stock->quantity ?? 0)
            + $stockQty;

        $stock->save();

        checkLowStock($stock);

        /*
        |--------------------------------------------------------------------------
        | UPDATE PO RECEIVED
        |--------------------------------------------------------------------------
        */

        PurchaseOrderItem::where(
            'id',
            $poItem->id
        )->increment(
            'received_quantity',
            $qty
        );

        /*
        |--------------------------------------------------------------------------
        | TRACKING
        |--------------------------------------------------------------------------
        */

        $affectedItems[] = [

            'item_id' =>
                $poItem->item_id,

            'location_id' =>
                $poItem->location_id,
        ];
    }
}else {

                /*
                ==========================================
                CASE 2: MANUAL STOCK IN
                ==========================================
                */

                foreach ($request->items ?? [] as $item) {

                    /*
                    |--------------------------------------------------------------------------
                    | ENTRY UNIT
                    |--------------------------------------------------------------------------
                    */

                    $entryUnitId =
                        $item['entry_unit_id'];

                    /*
                    |--------------------------------------------------------------------------
                    | ENTRY QUANTITY
                    |--------------------------------------------------------------------------
                    */

                    $entryQty =
                        $item['entry_quantity'];

                    /*
                    |--------------------------------------------------------------------------
                    | BASE / STOCK UNIT
                    |--------------------------------------------------------------------------
                    */

                    $baseUnitId =
                        $item['base_unit_id'];

                    /*
                    |--------------------------------------------------------------------------
                    | FINAL STOCK QUANTITY
                    |--------------------------------------------------------------------------
                    */

                    $stockQty =
                        $item['stock_quantity'];

                    /*
                    |--------------------------------------------------------------------------
                    | SAVE STOCK IN ITEM
                    |--------------------------------------------------------------------------
                    */
                    /*
                    |--------------------------------------------------------------------------
                    | SAVE UNIT CONVERSION IF NOT EXISTS
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !empty($item['entry_unit_id']) &&
                        !empty($item['base_unit_id']) &&
                        !empty($item['conversion_factor'])
                    ) {

                        // avoid same-unit conversion
                        if ($item['entry_unit_id'] != $item['base_unit_id']) {

                            ItemUnitConversion::updateOrCreate(
                                [
                                    'company_id' => $company->id,
                                    'item_id' => $item['item_id'],

                                    'from_unit_id' => $item['entry_unit_id'],
                                ],

                                [
                                    'to_unit_id' => $item['base_unit_id'],

                                    'factor' => $item['conversion_factor'],
                                ]
                            );
                        }
                    }
                    StockInItem::create([

                        'stock_in_id' => $stockIn->id,

                        'item_id' => $item['item_id'],

                        'brand_id' =>
                            $item['brand_id'] ?? null,

                        'condition_id' =>
                            $item['condition_id'] ?? null,

                        'location_id' =>
                            $item['location_id'],

                        /*
                        |--------------------------------------------------------------------------
                        | ENTRY / GRN UNIT
                        |--------------------------------------------------------------------------
                        */
                        'unit_id' =>
                            $entryUnitId,

                        /*
                        |--------------------------------------------------------------------------
                        | ENTRY / GRN QUANTITY
                        |--------------------------------------------------------------------------
                        */
                        'quantity' =>
                            $entryQty,

                        /*
                        |--------------------------------------------------------------------------
                        | BASE / STOCK UNIT
                        |--------------------------------------------------------------------------
                        */
                        'stock_unit_id' =>
                            $baseUnitId,

                        /*
                        |--------------------------------------------------------------------------
                        | FINAL CONVERTED STOCK QUANTITY
                        |--------------------------------------------------------------------------
                        */
                        'stock_quantity' =>
                            $stockQty,

                        'rate' =>
                            $item['rate'] ?? 0,

                        'supplier_rate' => null,
                    ]);

                    /*
                    ==========================================
                    UPDATE STOCK
                    ==========================================
                    */

                    $stock = Stock::firstOrNew([

                        'company_id' => $company->id,

                        'item_id' =>
                            $item['item_id'],

                        'brand_id' =>
                            $item['brand_id'] ?? null,

                        'condition_id' =>
                            $item['condition_id'] ?? null,

                        'location_id' =>
                            $item['location_id'],

                        /*
                        |--------------------------------------------------------------------------
                        | STOCK ALWAYS STORED IN BASE UNIT
                        |--------------------------------------------------------------------------
                        */
                        'unit_id' =>
                            $baseUnitId,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | ADD FINAL STOCK QUANTITY
                    |--------------------------------------------------------------------------
                    */

                    $stock->quantity =
                        ($stock->quantity ?? 0)
                        + $stockQty;

                    $stock->save();

                    checkLowStock($stock);

                    /*
                    |--------------------------------------------------------------------------
                    | TRACKING
                    |--------------------------------------------------------------------------
                    */

                    $affectedItems[] = [

                        'item_id' =>
                            $item['item_id'],

                        'location_id' =>
                            $item['location_id'],
                    ];
                }
            }
            /*
            ==========================================
            STEP 5: RECALCULATE OLD PO STATUS
            ==========================================
            */

            if ($oldPurchaseOrderId) {

                $oldPo = PurchaseOrder::with('items')
                    ->find($oldPurchaseOrderId);

                if ($oldPo) {

                    $allReceived =
                        $oldPo->items->every(function ($item) {

                            return
                                $item->received_quantity
                                >= $item->quantity;
                        });

                    $anyReceived =
                        $oldPo->items->some(function ($item) {

                            return
                                $item->received_quantity > 0;
                        });

                    if ($allReceived) {

                        $oldPo->update([
                            'status' => 'received'
                        ]);

                    } elseif ($anyReceived) {

                        $oldPo->update([
                            'status' => 'partial'
                        ]);

                    } else {

                        $oldPo->update([
                            'status' => 'approved'
                        ]);
                    }
                }
            }

            /*
            ==========================================
            STEP 6: RECALCULATE NEW PO STATUS
            ==========================================
            */

            if ($request->purchase_order_id) {

                $po = PurchaseOrder::with('items')
                    ->find($request->purchase_order_id);

                if ($po) {

                    $allReceived =
                        $po->items->every(function ($item) {

                            return
                                $item->received_quantity
                                >= $item->quantity;
                        });

                    $anyReceived =
                        $po->items->some(function ($item) {

                            return
                                $item->received_quantity > 0;
                        });

                    if ($allReceived) {

                        $po->update([
                            'status' => 'received'
                        ]);

                    } elseif ($anyReceived) {

                        $po->update([
                            'status' => 'partial'
                        ]);

                    } else {

                        $po->update([
                            'status' => 'approved'
                        ]);
                    }
                }
            }

        });

        /*
        ==========================================
        NOTIFICATIONS
        ==========================================
        */

        $this->notifyPendingIssues(
            $company,
            $affectedItems
        );

        notifyAdmins(
            'Stock In Updated',
            "Stock entry {$stockIn->doc_no} updated",
            route('stock-ins.index', $company->id),
            'success'
        );

        toast(
            'Stock updated successfully',
            'success'
        );

        return redirect()->route(
            'stock-ins.index',
            $company
        );
    }
    public function destroy(Company $company, StockIn $stockIn)
    {
        // 🔴 CHECK: issued items
        foreach ($stockIn->items as $item) {

            $issued = IssueItem::where('item_id', $item->item_id)
                ->where('location_id', $item->location_id)
                ->where('issued_qty', '>', 0)
                ->exists();

            if ($issued) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete. Some stock from this entry is already issued.'
                ], 422);
            }
        }

        DB::transaction(function () use ($stockIn, $company) {

            foreach ($stockIn->items as $item) {

                /* ===============================
                 | 🔁 REVERSE STOCK
                 =============================== */
                $stock = Stock::where([
                    'company_id' => $company->id,
                    'item_id' => $item->item_id,
                    'brand_id' => $item->brand_id,
                    'condition_id' => $item->condition_id,
                    'location_id' => $item->location_id
                ])->first();

                if ($stock) {
                    $stock->quantity -= $item->quantity;

                    if ($stock->quantity <= 0) {
                        $stock->delete();
                    } else {
                        $stock->save();
                        checkLowStock($stock);
                    }
                }

                /* ===============================
                 | 🔁 REVERSE PO RECEIVED QTY 🔥
                 =============================== */
                if ($item->purchase_order_item_id) {
                    PurchaseOrderItem::where('id', $item->purchase_order_item_id)
                        ->decrement('received_quantity', $item->quantity);
                }
            }

            /* ===============================
             | 🔁 UPDATE PO STATUS
             =============================== */
            if ($stockIn->purchase_order_id) {

                $po = PurchaseOrder::with('items')->find($stockIn->purchase_order_id);

                $allReceived = $po->items->every(function ($i) {
                    return $i->received_quantity >= $i->quantity;
                });

                if ($allReceived) {
                    $po->update(['status' => 'received']);
                } else {
                    $po->update(['status' => 'approved']);
                }
            }

            // delete items + stockIn
            $stockIn->items()->delete();
            $stockIn->delete();
        });

        return response()->json([
            'status' => true,
            'message' => 'Stock entry deleted successfully'
        ]);
    }
    public function ajaxStore(Request $request, Company $company)
    {
        $request->validate([

            'item_id' => 'required|exists:items,id',

            'from_unit_id' => 'required|exists:units,id',

            'to_unit_id' => 'required|exists:units,id',

            'factor' => 'required|numeric|min:0.000001',
        ]);

        $conversion = ItemUnitConversion::updateOrCreate(

            [
                'company_id' => $company->id,

                'item_id' => $request->item_id,

                'from_unit_id' => $request->from_unit_id,

                'to_unit_id' => $request->to_unit_id,
            ],

            [
                'factor' => $request->factor,
            ]
        );

        return response()->json([

            'status' => true,

            'message' => 'Conversion created successfully',

            'conversion' => [

                'id' => $conversion->id,

                'factor' => $conversion->factor,

                'from_unit_id' => $conversion->from_unit_id,

                'to_unit_id' => $conversion->to_unit_id,

                'from_unit_name' => optional($conversion->fromUnit)->name,

                'to_unit_name' => optional($conversion->toUnit)->name,
            ]
        ]);
    }
    private function notifyPendingIssues(Company $company, array $affectedItems)
    {
        foreach ($affectedItems as $row) {

            $pendingItems = IssueItem::where('item_id', $row['item_id'])
                ->where('pending_qty', '>', 0)
                ->whereHas('issue', function ($q) use ($company) {
                    $q->where('company_id', $company->id);
                })
                ->where(function ($q) use ($row) {
                    $q->whereNull('location_id')
                        ->orWhere('location_id', $row['location_id']);
                })
                ->with('issue', 'item')
                ->get();


            foreach ($pendingItems as $pending) {

                notifyAdmins(
                    'Pending Issue Can Be Fulfilled',
                    "Issue #{$pending->issue->issue_no} has pending qty for item {$pending->item->name}",
                    route('issues.index', [$company->id, $pending->issue->id]),
                    'warning'
                );
            }
        }
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

        ];

        $docInitial = $docMap[$docType] ?? strtoupper(substr($docType, 0, 2));

        // Take only first 2 words of customer name
        $words = explode(' ', trim($customerName));
        $firstTwoWords = array_slice($words, 0, 2);
        $cleanCustomer = preg_replace('/[^A-Za-z0-9]/', '', implode('', $firstTwoWords));

        // Format: DDMMYYHi
        $dateTime = Carbon::now()->format('dmyHi');

        return "{$initials}-GRN-{$dateTime}-{$cleanCustomer}.pdf";
    }

    public function print($companyId, $stockInId, Request $request)
    {
        $company = Company::findOrFail($companyId);

        $stockIn = StockIn::with([
            'supplier',
            'purchaseOrder',
            'items.unit',
            'items.stockUnit',
            'items.brand',
            'items.condition',
            'items.location',
            'items.poItem'
        ])->findOrFail($stockInId);

        $settings = Setting::first();
        $rows = $request->has('rows') ? explode(',', $request->rows) : [];

        if (!empty($rows)) {
            $stockIn->setRelation(
                'items',
                $stockIn->items->filter(function ($item, $index) use ($rows) {
                    return in_array((string) $index, $rows, true);
                })->values()
            );
        }

        try {

            $pdf = Pdf::loadView(
                'company.store.stocks.print',
                compact('stockIn', 'company', 'settings')
            )->setPaper('a4', 'portrait');

            $pdf->render();

            $canvas = $pdf->getCanvas();
            $canvas->page_text(
                500,
                810,
                "Page {PAGE_NUM} of {PAGE_COUNT}",
                null,
                8,
                [255, 255, 255]
            );

            $fileName = $this->generateFileName(
                $company,
                'stock-in',
                $stockIn->doc_no,
                optional($stockIn->supplier)->name ?? 'Supplier'
            );

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

}