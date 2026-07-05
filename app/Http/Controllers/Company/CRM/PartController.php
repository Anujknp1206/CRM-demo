<?php

namespace App\Http\Controllers\Company\CRM;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Part;
use App\Models\Company;
use App\Models\PartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
class PartController extends Controller
{
    public function searchItems(Request $request, Company $company)
    {
        $search = $request->search;
        $items = Item::with([
            'unit',
            'unitConversions.fromUnit'
        ])
            ->where('company_id', $company->id)
            ->when($search, function ($q) use ($search) {
                $keywords = preg_split('/\s+/', trim($search));
                $q->where(function ($query) use ($keywords) {
                    foreach ($keywords as $word) {
                        $query->where(function ($sub) use ($word) {
                            $sub->where('name', 'like', "%{$word}%")
                                ->orWhere('code', 'like', "%{$word}%")
                                ->orWhere('hi_name', 'like', "%{$word}%");
                        });
                    }
                });
            })
            ->get();

        return response()->json(

            $items->map(function ($item) {

                return [

                    'id' => $item->id,

                    'text' =>
                        $item->name .
                        ' (' . $item->code . ')',

                    'hi_name' => $item->hi_name,

                    // BASE UNIT
                    'base_unit_id' => $item->unit_id,

                    'base_unit_name' => optional($item->unit)->name,

                    // CONVERSIONS
                    'conversions' => $item->unitConversions
                        ->map(function ($conversion) {

                            return [

                                'from_unit_id' =>
                                    $conversion->from_unit_id,

                                'from_unit_name' =>
                                    optional($conversion->fromUnit)->name,

                                'factor' =>
                                    $conversion->factor,
                            ];
                        })
                        ->values()

                ];

            })

        );
    }
    public function getPartItems(Company $company, Part $part)
    {
        $part->load('items.item');

        return response()->json([

            'success' => true,

            'part' => [

                'id' => $part->id,

                'name' => $part->name,

                'hi_name' => $part->hi_name,

            ],

            'items' => $part->items->map(function ($row) {

                return [

                    'id' => $row->id,

                    'item_id' => $row->item_id,

                    'quantity' => $row->quantity,

                    'notes' => $row->notes,

                    'hi_notes' => $row->hi_notes,

                    'item' => [

                        'id' => optional($row->item)->id,

                        'name' => optional($row->item)->name,

                        'hi_name' => optional($row->item)->hi_name,

                        'code' => optional($row->item)->code,

                    ]

                ];
            })

        ]);
    }
    public function details(Company $company, Part $part)
    {
        $part->load([
            'items.item',
            'recipes'
        ]);

        return view(
            'company.crm.part.partials.part_details',
            compact('part', 'company')
        );
    }
    public function index(Company $company)
    {
        return view(
            'company.crm.part.index',
            [
                'company' => $company,
                'title' => Auth::user()->name . " :: Part Management",
                'label' => "Part List"
            ]
        );
    }
    public function data(Request $request, Company $company)
    {
        $query = Part::with([
            'items.item',
            'recipes'
        ]);

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */
        $search = $request->search;

        if (!empty($search)) {

            $query->where(function ($q) use ($search) {

                $q->where('id', $search)

                    ->orWhere('name', 'LIKE', "%{$search}%")

                    ->orWhere('code', 'LIKE', "%{$search}%");

            });
        }

        /*
        |--------------------------------------------------------------------------
        | DATE FILTERS
        |--------------------------------------------------------------------------
        */
        $from = null;
        $to = null;

        if ($request->from_date) {

            $from = Carbon::createFromFormat(
                'd/m/Y',
                $request->from_date
            )->format('Y-m-d');

            $query->whereDate('created_at', '>=', $from);
        }

        if ($request->to_date) {

            $to = Carbon::createFromFormat(
                'd/m/Y',
                $request->to_date
            )->format('Y-m-d');

            $query->whereDate('created_at', '<=', $to);
        }

        /*
        |--------------------------------------------------------------------------
        | DEFAULT ORDER
        |--------------------------------------------------------------------------
        */
        $parts = $query
            ->latest()
            ->get();

        return view(
            'company.crm.part.partials.part_rows',
            compact('parts', 'company')
        )->render();
    }
    public function ajaxSearch(Request $request, Company $company)
    {
        $search = $request->search;

        $parts = Part::query()

            ->where(function ($q) use ($search) {

                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%");

            })

            ->limit(20)
            ->get();

        return $parts->map(function ($part) {

            return [

                'id' => $part->id,

                'text' =>
                    $part->name .
                    ' (' . ($part->code ?? 'N/A') . ')'
            ];
        });
    }
    public function create(Company $company)
    {
        $items = Item::all();

        return view(
            'company.crm.part.create',
            [
                'items' => $items,
                'company' => $company,
                'title' => Auth::user()->name . " :: Part Management",
                'label' => "Create Part"
            ]
        );
    }

    public function store(Request $request, Company $company)
    {

        /*
        |--------------------------------------------------------------------------
        | CREATE PART
        |--------------------------------------------------------------------------
        */

        $part = Part::create([

            'name' => $request->name,

            'hi_name' => $request->hi_name,

            'code' => $request->code,

            'notes' => $request->notes,

            'hi_notes' => $request->hi_notes,

        ]);


        /*
        |--------------------------------------------------------------------------
        | SAVE PART ITEMS
        |--------------------------------------------------------------------------
        */

        foreach ($request->item_id as $i => $itemId) {


            /*
            |--------------------------------------------------------------------------
            | UPDATE ITEM HINDI NAME IN ITEMS TABLE
            |--------------------------------------------------------------------------
            */

            if (!empty($request->item_hi_name[$i])) {

                Item::where('id', $itemId)
                    ->update([

                        'hi_name' => $request->item_hi_name[$i]

                    ]);
            }


            /*
            |--------------------------------------------------------------------------
            | CREATE PART ITEM
            |--------------------------------------------------------------------------
            */

            PartItem::create([

                'part_id' => $part->id,

                'item_id' => $itemId,

                'quantity' => $request->quantity[$i],

                'notes' => $request->item_notes[$i] ?? null,

                'hi_notes' => $request->hi_item_notes[$i] ?? null,

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()

            ->route('parts.index', ['company' => $company->id])

            ->with('success', 'Part Created Successfully');
    }
    public function edit(Company $company, Part $part)
    {
        $part->load([
            'items.item'
        ]);

        return view(
            'company.crm.part.edit',
            [
                'company' => $company,
                'part' => $part,
                'title' => auth()->user()->name . " :: Edit Part",
                'label' => 'Edit Part'
            ]
        );
    }
    public function update(Request $request, Company $company, Part $part)
    {
        $request->validate([

            'name' => 'required|string|max:255',

            'hi_name' => 'required|string|max:255',

            'item_id' => 'required|array',

            'item_id.*' => 'required|exists:items,id',

            'quantity.*' => 'required|numeric|min:0.01'

        ]);


        /*
        |--------------------------------------------------------------------------
        | UPDATE PART
        |--------------------------------------------------------------------------
        */

        $part->update([

            'name' => $request->name,

            'hi_name' => $request->hi_name,

            'code' => $request->code,

            'notes' => $request->notes,

            'hi_notes' => $request->hi_notes

        ]);


        /*
        |--------------------------------------------------------------------------
        | DELETE OLD PART ITEMS
        |--------------------------------------------------------------------------
        */

        $part->items()->delete();


        /*
        |--------------------------------------------------------------------------
        | SAVE NEW PART ITEMS
        |--------------------------------------------------------------------------
        */

        foreach ($request->item_id as $i => $itemId) {


            /*
            |--------------------------------------------------------------------------
            | UPDATE ITEM HINDI NAME IN ITEMS TABLE
            |--------------------------------------------------------------------------
            */

            if (!empty($request->item_hi_name[$i])) {

                Item::where('id', $itemId)
                    ->update([

                        'hi_name' => $request->item_hi_name[$i]

                    ]);
            }


            /*
            |--------------------------------------------------------------------------
            | CREATE PART ITEM
            |--------------------------------------------------------------------------
            */

            PartItem::create([

                'part_id' => $part->id,

                'item_id' => $itemId,

                'quantity' => $request->quantity[$i],

                'notes' => $request->item_notes[$i] ?? null,

                'hi_notes' => $request->hi_item_notes[$i] ?? null

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()

            ->route('parts.index', ['company' => $company->id])

            ->with('success', 'Part Updated Successfully');
    }
    public function destroy(Company $company, Part $part)
    {

        /*
        |--------------------------------------------------------------------------
        | CHECK RECIPE LINK
        |--------------------------------------------------------------------------
        */

        if ($part->recipes()->exists()) {

            return redirect()

                ->back()

                ->with('error', 'Part cannot be deleted because it is linked with recipes.');
        }


        /*
        |--------------------------------------------------------------------------
        | DELETE PART ITEMS
        |--------------------------------------------------------------------------
        */

        $part->items()->delete();


        /*
        |--------------------------------------------------------------------------
        | DELETE PART
        |--------------------------------------------------------------------------
        */

        $part->delete();


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()

            ->route('parts.index', ['company' => $company->id])

            ->with('success', 'Part Deleted Successfully');
    }
}