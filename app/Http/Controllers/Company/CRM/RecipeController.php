<?php

namespace App\Http\Controllers\Company\CRM;


use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\OrderItem;
use App\Models\Part;
use App\Models\Recipe;
use App\Models\Machine;
use App\Models\Component;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{

    public function index(Request $request, Company $company)
    {

        $machines = Machine::all();


        $components = Component::all();


        return view(
            'company.crm.recipes.index',
            [
                'company' => $company,
                'title' => Auth::user()->name . " :: Recipe Management",
                'label' => "Recipe List",
                'machines' => $machines,

                'components' => $components
            ]
        );

    }


    public function search(
        Request $request,
        Company $company
    ) {

        $q = $request->q;


        $recipes = Recipe::with(
            'recipeable'
        )

            ->where(function ($query) use ($q) {

                $query->where(
                    'name',
                    'like',
                    '%' . $q . '%'
                );


                $query->orWhereHas(
                    'recipeable',
                    function ($sub) use ($q) {

                        $sub->where(
                            'name',
                            'like',
                            '%' . $q . '%'
                        );

                    }

                );

            })

            ->limit(20)

            ->get();



        return response()->json(

            $recipes->map(
                function ($r) {

                    return [

                        'id' => $r->id,

                        'text' =>
                            $r->name
                            . ' ('
                            . $r->recipeable?->name
                            . ')'

                    ];

                }

            )

        );

    }
    public function ajaxList(Request $request, Company $company)
    {
        $query = Recipe::with([
            'recipeable',
            'parts.items.item'
        ]);

        if ($request->filled('search')) {
            $query->where('id', (int) $request->search);
        }

        $recipes = $query->latest()->get();

        return view(
            'company.crm.recipes.partials.rows',
            compact('recipes', 'company')
        )->render();
    }
    public function create(Request $request, Company $company)
    {
        $machines = Machine::all();

        $components = Component::all();

        $items = Item::where('company_id', $company->id)
            ->with('unit')
            ->get();

        // 🔥 LOAD PARTS WITH ITEMS
        $parts = Part::with([
            'items.item'
        ])->latest()->get();

        $orderItem = null;

        if ($request->has('order_item_id')) {

            $orderItem = OrderItem::with([
                'machine',
                'component',
                'item'
            ])->find($request->order_item_id);
        }

        return view('company.crm.recipes.create', [

            'title' => Auth::user()->name . " :: Recipe Management",

            'label' => "Recipe List",

            'company' => $company,

            'machines' => $machines,

            'components' => $components,

            'items' => $items,

            'parts' => $parts, // 🔥 IMPORTANT

            'orderItem' => $orderItem

        ]);
    }

    public function store(Request $request, Company $company)
    {
        $request->validate([

            'name' => 'required|string|max:255',

            'type' => 'required|in:machine,component',

            'recipeable_id' => 'required',

            'parts' => 'required|array|min:1',

            // PARTS
            'parts.*.part_id' => 'required|exists:parts,id',

            'parts.*.weightage' => 'nullable|numeric|min:0|max:10',

        ]);

        $type = $request->type == 'machine'
            ? Machine::class
            : Component::class;

        DB::transaction(function () use ($request, $type, &$recipe) {

            /*
            =====================================
            CHECK EXISTING RECIPES
            =====================================
            */
            $existingCount = Recipe::where('recipeable_type', $type)
                ->where('recipeable_id', $request->recipeable_id)
                ->count();

            /*
            =====================================
            FIRST RECIPE AUTO DEFAULT
            =====================================
            */
            $makeDefault = $existingCount == 0
                ? true
                : $request->has('is_default');

            /*
            =====================================
            REMOVE OLD DEFAULTS
            =====================================
            */
            if ($makeDefault) {

                Recipe::where('recipeable_type', $type)
                    ->where('recipeable_id', $request->recipeable_id)
                    ->update([
                        'is_default' => 0
                    ]);
            }

            /*
            =====================================
            CREATE RECIPE
            =====================================
            */
            $recipe = Recipe::create([

                'name' => $request->name,
                'hi_name' => $request->hi_name,

                'notes' => $request->notes,
                'hi_notes' => $request->hi_notes,

                'is_default' => $makeDefault,

                'recipeable_type' => $type,

                'recipeable_id' => $request->recipeable_id

            ]);

            /*
            =====================================
            ATTACH PARTS
            =====================================
            */
            foreach ($request->parts as $partData) {

                // skip empty rows
                if (empty($partData['part_id'])) {
                    continue;
                }

                $recipe->parts()->attach(

                    $partData['part_id'],

                    [
                        'weightage' => $partData['weightage'] ?? 0
                    ]
                );
            }

            /*
            =====================================
            DEFAULT SAFETY CHECK
            =====================================
            */

            $defaults = Recipe::where('recipeable_type', $type)
                ->where('recipeable_id', $request->recipeable_id)
                ->where('is_default', 1)
                ->orderByDesc('id')
                ->pluck('id');

            // KEEP ONLY ONE DEFAULT
            if ($defaults->count() > 1) {

                Recipe::whereIn('id', $defaults->slice(1))
                    ->update([
                        'is_default' => 0
                    ]);
            }

            // ENSURE AT LEAST ONE DEFAULT
            $hasDefault = Recipe::where('recipeable_type', $type)
                ->where('recipeable_id', $request->recipeable_id)
                ->where('is_default', 1)
                ->exists();

            if (!$hasDefault) {

                Recipe::where('id', $recipe->id)
                    ->update([
                        'is_default' => 1
                    ]);
            }

        });

        return response()->json([

            'success' => true,

            'message' => 'Recipe created successfully'

        ]);
    }
    public function edit(Company $company, Recipe $recipe)
    {
        $machines = Machine::all();

        $components = Component::all();

        $items = Item::where('company_id', $company->id)->get();

        $allParts = Part::all();

        $recipe->load([

            'parts' => function ($q) {

                $q->withPivot('weightage');

            },

            'parts.items.item'

        ]);

        return view('company.crm.recipes.edit', [

            'company' => $company,

            'recipe' => $recipe,

            'machines' => $machines,

            'components' => $components,

            'items' => $items,

            'allParts' => $allParts,

            'title' => 'Edit Recipe',

            'label' => 'Edit Recipe'

        ]);
    }

    public function update(Request $request, Company $company, Recipe $recipe)
    {
        $request->validate([

            'name' => 'required|string|max:255',

            'parts' => 'required|array|min:1',

            'parts.*.part_id' => 'required|exists:parts,id',

            'parts.*.weightage' => 'nullable|numeric|min:0|max:10',

            'parts.*.items' => 'required|array|min:1',

            'parts.*.items.*.item_id' => 'required|exists:items,id',

            'parts.*.items.*.quantity' => 'required|numeric|min:0.01',

        ]);

        DB::transaction(function () use ($request, $recipe) {

            /*
            =========================================
            DEFAULT HANDLING
            =========================================
            */
            if ($request->has('is_default')) {

                Recipe::where('recipeable_type', $recipe->recipeable_type)
                    ->where('recipeable_id', $recipe->recipeable_id)
                    ->where('id', '!=', $recipe->id)
                    ->update([
                        'is_default' => 0
                    ]);
            }

            /*
            =========================================
            UPDATE RECIPE
            =========================================
            */
            $recipe->update([

                'name' => $request->name,
                'hi_name' => $request->hi_name,

                'notes' => $request->notes,
                'hi_notes' => $request->hi_notes,

                'is_default' => $request->has('is_default')

            ]);

            /*
            =========================================
            REMOVE OLD PART LINKS
            =========================================
            */
            $recipe->parts()->detach();

            /*
            =========================================
            ATTACH NEW PARTS
            =========================================
            */
            foreach ($request->parts as $partData) {

                if (empty($partData['part_id'])) {
                    continue;
                }

                /*
                =========================================
                ATTACH PART WITH WEIGHTAGE
                =========================================
                */
                $recipe->parts()->attach(

                    $partData['part_id'],

                    [
                        'weightage' => $partData['weightage'] ?? 0
                    ]
                );

                /*
                =========================================
                UPDATE PART ITEM NOTES/QTY
                =========================================
                */
                $part = Part::find($partData['part_id']);

                if (!$part) {
                    continue;
                }

                foreach ($partData['items'] as $itemData) {

                    $partItem = $part->items()
                        ->where('item_id', $itemData['item_id'])
                        ->first();

                    if ($partItem) {

                        $partItem->update([

                            'quantity' => $itemData['quantity'],

                            'notes' => $itemData['notes'] ?? null,

                            'hi_notes' => $itemData['hi_notes'] ?? null

                        ]);
                    }
                }
            }

            /*
            =========================================
            ENSURE SINGLE DEFAULT
            =========================================
            */
            $hasDefault = Recipe::where('recipeable_type', $recipe->recipeable_type)
                ->where('recipeable_id', $recipe->recipeable_id)
                ->where('is_default', 1)
                ->exists();

            if (!$hasDefault) {

                $recipe->update([
                    'is_default' => 1
                ]);
            }

            /*
            =========================================
            REMOVE DUPLICATE DEFAULTS
            =========================================
            */
            $defaults = Recipe::where('recipeable_type', $recipe->recipeable_type)
                ->where('recipeable_id', $recipe->recipeable_id)
                ->where('is_default', 1)
                ->orderByDesc('id')
                ->pluck('id');

            if ($defaults->count() > 1) {

                Recipe::whereIn('id', $defaults->slice(1))
                    ->update([
                        'is_default' => 0
                    ]);
            }

        });

        return response()->json([

            'success' => true,

            'message' => 'Recipe updated successfully'

        ]);
    }
    public function destroy(Company $company, Recipe $recipe)
    {

        $recipe->delete();

        return response()->json([
            'success' => true
        ]);

    }
    public function recipesForOrderItem(Request $request, Company $company)
    {
        $orderItem = OrderItem::findOrFail($request->order_item_id);

        /*
        Detect type
        */
        if ($orderItem->machine_id) {

            $type = Machine::class;
            $ownerId = $orderItem->machine_id;

        } elseif ($orderItem->component_id) {

            $type = Component::class;
            $ownerId = $orderItem->component_id;

        } else {

            return response()->json([
                'recipes' => []
            ]);
        }

        /*
        🔥 LOAD PARTS + ITEMS (UPDATED)
        */
        $recipes = Recipe::with([
            'parts' => function ($q) {
                $q->select(
                    'parts.id',
                    'parts.name',
                    'parts.hi_name',
                    'parts.code',
                    'parts.notes',
                    'parts.hi_notes'
                );
            },
           'parts.items.item:id,name,hi_name,code'
        ])
            ->where('recipeable_type', $type)
            ->where('recipeable_id', $ownerId)
            ->get();


        return response()->json([
            'recipes' => $recipes
        ]);
    }
}