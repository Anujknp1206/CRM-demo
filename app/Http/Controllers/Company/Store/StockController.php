<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\Condition;
use App\Models\Location;

class StockController extends Controller
{
    public function index(Request $request, Company $company)
    {
        $title = $company->company_name . " - Stock Management";

        $label = 'Stock List';

        /*
        |--------------------------------------------------------------------------
        | STOCK QUERY
        |--------------------------------------------------------------------------
        */

        $query = Stock::with([

            'item.unit',
            'brand',
            'condition',
            'location',
        ])
            ->where('company_id', $company->id);

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->search) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                /*
                |--------------------------------------------------------------------------
                | ITEM NAME
                |--------------------------------------------------------------------------
                */

                $q->whereHas('item', function ($item) use ($search) {

                    $item->where(
                        'name',
                        'LIKE',
                        "%{$search}%"
                    );
                })

                    /*
                    |--------------------------------------------------------------------------
                    | BRAND
                    |--------------------------------------------------------------------------
                    */

                    ->orWhereHas('brand', function ($brand) use ($search) {

                        $brand->where(
                            'name',
                            'LIKE',
                            "%{$search}%"
                        );
                    })

                    /*
                    |--------------------------------------------------------------------------
                    | CONDITION
                    |--------------------------------------------------------------------------
                    */

                    ->orWhereHas('condition', function ($condition) use ($search) {

                        $condition->where(
                            'name',
                            'LIKE',
                            "%{$search}%"
                        );
                    })

                    /*
                    |--------------------------------------------------------------------------
                    | LOCATION
                    |--------------------------------------------------------------------------
                    */

                    ->orWhereHas('location', function ($location) use ($search) {

                        $location->where(
                            'name',
                            'LIKE',
                            "%{$search}%"
                        );
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | LOW STOCK
        |--------------------------------------------------------------------------
        */

        if ($request->status === 'low') {

            $query->whereColumn(
                'quantity',
                '<=',
                'min_quantity'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | OK STOCK
        |--------------------------------------------------------------------------
        */

        if ($request->status === 'ok') {

            $query->whereColumn(
                'quantity',
                '>',
                'min_quantity'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FINAL STOCKS
        |--------------------------------------------------------------------------
        */

        $stocks = $query
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | FILTER DATA
        |--------------------------------------------------------------------------
        */

        $items = Item::where(
            'company_id',
            $company->id
        )->pluck('name', 'id');

        $brands = Brand::where(
            'company_id',
            $company->id
        )->pluck('name', 'id');

        $conditions = Condition::where(
            'company_id',
            $company->id
        )->pluck('name', 'id');

        $locations = Location::where(
            'company_id',
            $company->id
        )->pluck('name', 'id');

        /*
        |--------------------------------------------------------------------------
        | RFI PREVIEW
        |--------------------------------------------------------------------------
        */

        $date = now()->format('Ymd');

        $initials = $company->initials();

        $lastId =
            \App\Models\Rfi::max('id') + 1;

        $previewCreatedBy =
            auth()->user()->name;

        $previewCode =
            "RFI-{$initials}-{$date}-{$lastId}";

        return view(
            'company.store.stocks.stock',
            compact(
                'company',
                'stocks',
                'title',
                'label',
                'previewCode',
                'previewCreatedBy',
                'items',
                'brands',
                'conditions',
                'locations'
            )
        );
    }
    public function data(Request $request, Company $company)
    {
        /*
        |--------------------------------------------------------------------------
        | STOCK QUERY
        |--------------------------------------------------------------------------
        */

        $query = Stock::with([

            'item.unit',
            'brand',
            'condition',
            'location',
            'stockInItems'
        ])
            ->where('company_id', $company->id);

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->search) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->whereHas('item', function ($item) use ($search) {

                    $item->where(
                        'name',
                        'LIKE',
                        "%{$search}%"
                    );
                })

                    ->orWhereHas('brand', function ($brand) use ($search) {

                        $brand->where(
                            'name',
                            'LIKE',
                            "%{$search}%"
                        );
                    })

                    ->orWhereHas('condition', function ($condition) use ($search) {

                        $condition->where(
                            'name',
                            'LIKE',
                            "%{$search}%"
                        );
                    })

                    ->orWhereHas('location', function ($location) use ($search) {

                        $location->where(
                            'name',
                            'LIKE',
                            "%{$search}%"
                        );
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | LOW STOCK
        |--------------------------------------------------------------------------
        */

        if ($request->status === 'low') {

            $query->whereColumn(
                'quantity',
                '<=',
                'min_quantity'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | OK STOCK
        |--------------------------------------------------------------------------
        */

        if ($request->status === 'ok') {

            $query->whereColumn(
                'quantity',
                '>',
                'min_quantity'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FINAL STOCKS
        |--------------------------------------------------------------------------
        */

        $stocks = $query
            ->latest()
            ->get();

        return view(
            'company.store.stocks.partials.row',
            compact('stocks')
        )->render();
    }
}
