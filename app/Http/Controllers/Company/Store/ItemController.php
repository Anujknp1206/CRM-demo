<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;

use App\Models\Brand;
use App\Models\Company;
use App\Models\Item;
use App\Models\Location;
use App\Models\Unit;
use Illuminate\Validation\Rule;
use App\Models\Category;
use App\Models\Condition;
use App\Models\ItemUnitConversion;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function search(Request $request, Company $company)
    {
        $q = trim($request->q);

        return Item::with(['category', 'subcategory', 'unit', 'condition'])
            ->where('company_id', $company->id)
            ->where(function ($query) use ($q) {

                // Item fields
                $query->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('code', 'LIKE', "%{$q}%");

                // Category
                $query->orWhereHas('category', function ($q2) use ($q) {
                    $q2->where('name', 'LIKE', "%{$q}%");
                });

                // Subcategory
                $query->orWhereHas('subcategory', function ($q2) use ($q) {
                    $q2->where('name', 'LIKE', "%{$q}%");
                });

                // Unit
                $query->orWhereHas('unit', function ($q2) use ($q) {
                    $q2->where('name', 'LIKE', "%{$q}%");
                });

                // Condition
                $query->orWhereHas('condition', function ($q2) use ($q) {
                    $q2->where('name', 'LIKE', "%{$q}%");
                });

            })
            ->latest()
            ->limit(20)
            ->get();
    }
    public function index(Company $company)
    {
        $items = Item::with([
            'category',
            'subcategory',
            'unit',
            'condition'
        ])
            ->where('company_id', $company->id)
            ->latest()
            ->get();

        $categories = Category::all();
        $subcategories = Subcategory::all();
        $title = $company->company_name . " - Item Management";

        return view('company.store.items.index', [
            'items' => $items,
            'company' => $company,
            'categories' => $categories,
            'label' => 'Item List',
            'title' => $title,
            'subcategories' => $subcategories,
        ]);
    }
    public function data(Request $request, Company $company)
    {
        // dd($request->all());
        $query = Item::with([
            'category',
            'subcategory',
            'unit',
            'condition'
        ])->where('company_id', $company->id);
        $search = trim($request->search);
        $itemId = $request->item_id;
        $category = $request->category_id;
        $subcategory = $request->subcategory_id;
        if (!empty($itemId)) {
            $query->where('id', $itemId);
        }
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%")
                    ->orWhere('hi_name', 'LIKE', "%{$search}%");
                // Category
                $q->orWhereHas('category', function ($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%{$search}%");
                });
                // Subcategory
                $q->orWhereHas('subcategory', function ($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%{$search}%");
                });

                // Unit
                $q->orWhereHas('unit', function ($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%{$search}%");
                });

                // Condition
                $q->orWhereHas('condition', function ($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%{$search}%");
                });

            });
        }

        /*
        |--------------------------------------------------------------------------
        | CATEGORY FILTER
        |--------------------------------------------------------------------------
        */
        if (!empty($category)) {

            $query->where('category_id', $category);
        }

        /*
        |--------------------------------------------------------------------------
        | SUBCATEGORY FILTER
        |--------------------------------------------------------------------------
        */
        if (!empty($subcategory)) {

            $query->where('subcategory_id', $subcategory);
        }

        /*
        |--------------------------------------------------------------------------
        | GET ITEMS
        |--------------------------------------------------------------------------
        */
        $items = $query->latest()
            ->limit(50)
            ->get();

        return view('company.store.items.partials.item_rows', [
            'items' => $items,
            'company' => $company
        ])->render();
    }
    public function create(Company $company)
    {
        $title = $company->company_name . " - Item Management";

        return view('company.store.items.create', [
            'company' => $company,
            'label' => 'Create Item',
            'title' => $title,
            'categories' => Category::where('company_id', $company->id)->get(),
            'units' => Unit::all(),
            'brands' => Brand::where('company_id', $company->id)->get(),
            'locations' => Location::where('company_id', $company->id)->get(),
            'conditions' => Condition::all(),
        ]);
    }
    public function store(Request $request, Company $company)
    {
        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | ITEM VALIDATION
            |--------------------------------------------------------------------------
            */

            'name' => [

                'required',
                'max:150',

                Rule::unique('items')
                    ->where(function ($q) use ($request, $company) {

                        return $q->where('company_id', $company->id)
                            ->where('category_id', $request->category_id)
                            ->where('subcategory_id', $request->subcategory_id);
                    }),
            ],

            'hi_name' => [

                'required',
                'max:150',

                Rule::unique('items')
                    ->where(function ($q) use ($request, $company) {

                        return $q->where('company_id', $company->id)
                            ->where('category_id', $request->category_id)
                            ->where('subcategory_id', $request->subcategory_id);
                    }),
            ],

            'category_id' => 'required|exists:categories,id',

            'subcategory_id' => 'required|exists:subcategories,id',

            /*
            |--------------------------------------------------------------------------
            | BASE UNIT
            |--------------------------------------------------------------------------
            */

            'base_unit_id' => 'required|exists:units,id',

            'min_quantity' => 'nullable|numeric|min:0',

            /*
            |--------------------------------------------------------------------------
            | STOCKS ARRAY
            |--------------------------------------------------------------------------
            */

            'stocks' => 'required|array|min:1',

            /*
            |--------------------------------------------------------------------------
            | ONLY FIRST ROW HAS UNIT & CONVERSION
            |--------------------------------------------------------------------------
            */

            'stocks.0.stock_unit_id' =>
                'required|exists:units,id',

            'stocks.0.conversion_factor' =>
                'nullable|numeric|min:0.000001',

            /*
            |--------------------------------------------------------------------------
            | ALL STOCK ROWS
            |--------------------------------------------------------------------------
            */

            'stocks.*.brand_id' =>
                'nullable|exists:brands,id',

            'stocks.*.location_id' =>
                'nullable|exists:locations,id',

            'stocks.*.condition_id' =>
                'nullable|exists:conditions,id',

            'stocks.*.initial_stock' =>
                'required|numeric|min:0',
        ]);

        /*
        |--------------------------------------------------------------------------
        | GLOBAL STOCK UNIT & CONVERSION
        |--------------------------------------------------------------------------
        */

        $stockUnitId =
            $request->stocks[0]['stock_unit_id'];

        $conversionFactor =
            $request->stocks[0]['conversion_factor'] ?? 1;

        /*
        |--------------------------------------------------------------------------
        | VALIDATE CONVERSION FACTOR
        |--------------------------------------------------------------------------
        */

        if (
            $request->base_unit_id != $stockUnitId
            &&
            empty($conversionFactor)
        ) {

            return back()
                ->withInput()
                ->withErrors([

                    'stocks.0.conversion_factor' =>
                        'Conversion factor is required.'
                ]);
        }

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | CREATE ITEM
            |--------------------------------------------------------------------------
            */

            $item = Item::create([

                'company_id' => $company->id,

                'name' => $validated['name'],

                'hi_name' => $validated['hi_name'],

                'category_id' => $validated['category_id'],

                'subcategory_id' => $validated['subcategory_id'],

                /*
                |--------------------------------------------------------------------------
                | MAIN / BASE UNIT
                |--------------------------------------------------------------------------
                */

                'unit_id' => $validated['base_unit_id'],

                'low_stock_level' =>
                    $validated['min_quantity'] ?? 0,
            ]);

            /*
            |--------------------------------------------------------------------------
            | DETERMINE FACTOR
            |--------------------------------------------------------------------------
            */

            $factor = 1;

            if (
                $request->base_unit_id != $stockUnitId
            ) {

                $factor = $conversionFactor;
            }

            /*
            |--------------------------------------------------------------------------
            | SAVE CONVERSION MASTER
            |--------------------------------------------------------------------------
            */

            ItemUnitConversion::updateOrCreate(

                [
                    'item_id' => $item->id,

                    'from_unit_id' => $stockUnitId,

                    'to_unit_id' => $request->base_unit_id,
                ],

                [
                    'company_id' => $company->id,

                    'factor' => $factor,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | SAVE MULTIPLE STOCK ENTRIES
            |--------------------------------------------------------------------------
            */

            foreach ($request->stocks as $stockData) {

                /*
                |--------------------------------------------------------------------------
                | CONVERT TO BASE QUANTITY
                |--------------------------------------------------------------------------
                */

                $baseQuantity =
                    $stockData['initial_stock'] * $factor;

                /*
                |--------------------------------------------------------------------------
                | CREATE STOCK
                |--------------------------------------------------------------------------
                */

                Stock::create([

                    'company_id' => $company->id,

                    'brand_id' =>
                        $stockData['brand_id'] ?? null,

                    'item_id' => $item->id,

                    'condition_id' =>
                        $stockData['condition_id'] ?? null,

                    'location_id' =>
                        $stockData['location_id'] ?? null,

                    /*
                    |--------------------------------------------------------------------------
                    | ALWAYS STORE BASE UNIT
                    |--------------------------------------------------------------------------
                    */

                    'unit_id' => $request->base_unit_id,

                    /*
                    |--------------------------------------------------------------------------
                    | ALWAYS STORE BASE QUANTITY
                    |--------------------------------------------------------------------------
                    */

                    'quantity' => $baseQuantity,

                    /*
                    |--------------------------------------------------------------------------
                    | OPTIONAL ERP IMPROVEMENT
                    |--------------------------------------------------------------------------
                    */

                    // 'entered_unit_id' => $stockUnitId,

                    // 'entered_quantity' =>
                    //     $stockData['initial_stock'],

                    // 'conversion_factor' => $factor,

                    'min_quantity' =>
                        $validated['min_quantity'] ?? 0,
                ]);
            }

            DB::commit();

            toast(
                'Item & opening stocks added successfully',
                'success'
            );

            return redirect()->route('items.index', [
                'company' => $company->id
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
    public function edit(Company $company, Item $item)
    {
        $title = $company->company_name . " - Item Management";
        $stock = Stock::where('item_id', $item->id)->first();
        $conversion = ItemUnitConversion::where(
            'item_id',
            $item->id
        )->first();
        return view('company.store.items.edit', [
            'company' => $company,
            'item' => $item,
            'label' => 'Edit Item',
            'title' => $title,
            'stock' => $stock,
            'conversion' => $conversion,
            'brands' => Brand::where('company_id', $company->id)->get(),
            'locations' => Location::where('company_id', $company->id)->get(),
            'categories' => Category::where('company_id', $company->id)->get(),
            'subcategories' => Subcategory::where('category_id', $item->category_id)->get(),
            'units' => Unit::all(),
            'conditions' => Condition::all(),
        ]);
    }
    public function update(Request $request,Company $company,Item $item
    ) {
        $hasStock =
            $item->stocks()
                ->where('quantity', '>', 0)
                ->exists();

        $validated = $request->validate([

            'name' => [

                'required',
                'max:150',

                Rule::unique('items')
                    ->ignore($item->id)
                    ->where(function ($q) use ($request, $company) {

                        return $q
                            ->where(
                                'company_id',
                                $company->id
                            )
                            ->where(
                                'category_id',
                                $request->category_id
                            )
                            ->where(
                                'subcategory_id',
                                $request->subcategory_id
                            );
                    }),
            ],

            'hi_name' => [
                'required',
                'max:150',
            ],

            'category_id' =>
                'required|exists:categories,id',

            'subcategory_id' =>
                'required|exists:subcategories,id',

            'base_unit_id' =>
                'required|exists:units,id',

            'min_quantity' =>
                'nullable|numeric|min:0',

            /*
            |--------------------------------------------------------------------------
            | STOCKS
            |--------------------------------------------------------------------------
            */

            'stocks' =>
                'required|array|min:1',

            /*
            |--------------------------------------------------------------------------
            | FIRST ROW ONLY
            |--------------------------------------------------------------------------
            */

            'stocks.0.stock_unit_id' =>
                'required|exists:units,id',

            'stocks.0.conversion_factor' =>
                'nullable|numeric|min:0.000001',

            /*
            |--------------------------------------------------------------------------
            | ALL ROWS
            |--------------------------------------------------------------------------
            */

            'stocks.*.brand_id' =>
                'nullable|exists:brands,id',

            'stocks.*.location_id' =>
                'nullable|exists:locations,id',

            'stocks.*.condition_id' =>
                'nullable|exists:conditions,id',

            'stocks.*.initial_stock' =>
                'required|numeric|min:0',
        ]);

        /*
        |--------------------------------------------------------------------------
        | GLOBAL UNIT & FACTOR
        |--------------------------------------------------------------------------
        */

        $stockUnitId =
            $request->stocks[0]['stock_unit_id'];

        $conversionFactor =
            $request->stocks[0]['conversion_factor'] ?? 1;

        /*
        |--------------------------------------------------------------------------
        | VALIDATE FACTOR
        |--------------------------------------------------------------------------
        */

        if (
            $request->base_unit_id != $stockUnitId
            &&
            empty($conversionFactor)
        ) {

            return back()
                ->withInput()
                ->withErrors([

                    'stocks.0.conversion_factor' =>
                        'Conversion factor is required.'
                ]);
        }

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | PREVENT BASE UNIT CHANGE
            |--------------------------------------------------------------------------
            */

            if (
                $hasStock &&
                $item->unit_id != $request->base_unit_id
            ) {

                return back()
                    ->withInput()
                    ->withErrors([

                        'base_unit_id' =>

                            'Base unit cannot be changed after stock exists.'
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE ITEM
            |--------------------------------------------------------------------------
            */

            $item->update([

                'name' =>
                    $validated['name'],

                'hi_name' =>
                    $validated['hi_name'],

                'category_id' =>
                    $validated['category_id'],

                'subcategory_id' =>
                    $validated['subcategory_id'],

                'unit_id' =>
                    $validated['base_unit_id'],

                'low_stock_level' =>
                    $validated['min_quantity'] ?? 0,
            ]);

            /*
            |--------------------------------------------------------------------------
            | DETERMINE FACTOR
            |--------------------------------------------------------------------------
            */

            $factor = 1;

            if (
                $request->base_unit_id !=
                $stockUnitId
            ) {

                $factor = $conversionFactor;
            }

            /*
            |--------------------------------------------------------------------------
            | CHECK IF VALID STOCK EXISTS
            |--------------------------------------------------------------------------
            */

            $hasValidStock = false;

            foreach ($request->stocks as $stockData) {

                if (
                    isset($stockData['initial_stock']) &&
                    $stockData['initial_stock'] > 0
                ) {

                    $hasValidStock = true;

                    break;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | DELETE OLD STOCKS
            |--------------------------------------------------------------------------
            */

            Stock::where(
                'item_id',
                $item->id
            )->delete();

            /*
            |--------------------------------------------------------------------------
            | DELETE OLD CONVERSIONS
            |--------------------------------------------------------------------------
            */

            ItemUnitConversion::where(
                'item_id',
                $item->id
            )->delete();

            /*
            |--------------------------------------------------------------------------
            | SAVE CONVERSION ONLY IF STOCK EXISTS
            |--------------------------------------------------------------------------
            */

            if ($hasValidStock) {

                ItemUnitConversion::create([

                    'company_id' =>
                        $company->id,

                    'item_id' =>
                        $item->id,

                    'from_unit_id' =>
                        $stockUnitId,

                    'to_unit_id' =>
                        $request->base_unit_id,

                    'factor' =>
                        $factor,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | SAVE STOCKS
            |--------------------------------------------------------------------------
            */

            foreach ($request->stocks as $stockData) {

                /*
                |--------------------------------------------------------------------------
                | BASE QUANTITY
                |--------------------------------------------------------------------------
                */

                $baseQuantity =
                    $stockData['initial_stock']
                    * $factor;

                /*
                |--------------------------------------------------------------------------
                | SKIP EMPTY
                |--------------------------------------------------------------------------
                */

                if ($baseQuantity <= 0) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | CREATE STOCK
                |--------------------------------------------------------------------------
                */

                $stock = Stock::create([

                    'company_id' =>
                        $company->id,

                    'item_id' =>
                        $item->id,

                    'brand_id' =>
                        $stockData['brand_id'] ?? null,

                    'location_id' =>
                        $stockData['location_id'] ?? null,

                    'condition_id' =>
                        $stockData['condition_id'] ?? null,

                    /*
                    |--------------------------------------------------------------------------
                    | ALWAYS STORE BASE UNIT
                    |--------------------------------------------------------------------------
                    */

                    'unit_id' =>
                        $request->base_unit_id,

                    /*
                    |--------------------------------------------------------------------------
                    | ALWAYS STORE BASE QUANTITY
                    |--------------------------------------------------------------------------
                    */

                    'quantity' =>
                        $baseQuantity,

                    'min_quantity' =>
                        $validated['min_quantity'] ?? 0,
                ]);

                /*
                |--------------------------------------------------------------------------
                | LOW STOCK CHECK
                |--------------------------------------------------------------------------
                */

                checkLowStock($stock);
            }

            DB::commit();

            toast(
                'Item updated successfully',
                'success'
            );

            return redirect()->route(
                'items.index',
                [
                    'company' => $company->id
                ]
            );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }
    public function destroy(Company $company, Item $item)
    {
        // 🔥 Check if used in BOM
        if ($item->bomItems()->exists()) {

            toast('Cannot delete item. It is used in BOM.', 'error');

            return redirect()->back();
        }

        // ✅ Safe to delete
        $item->delete();

        toast('Item deleted successfully', 'success');

        return redirect()->route('items.index', $company->id);
    }   
    public function ajaxShow(Company $company, Item $item)
    { 
        return response()->json(['item' => $item->load(['category', 'subcategory', 'unit', 'condition'])]); 
    }
}
