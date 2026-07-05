<?php

namespace App\Http\Controllers\Company\CRM;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;

class CompanyReportController extends Controller
{
    public function customerSearch(Request $request, Company $company)
    {

        $term = $request->q;

        $results = [];


        /* Customer */
        Customer::with(
            ['city', 'state']
        )
            ->where(
                'company_id',
                $company->id
            )
            ->where(
                'name',
                'like',
                "%{$term}%"
            )
            ->take(5)
            ->get()
            ->each(function ($c) use (&$results) {

                $results[] = [
                    'id' => 'customer-' . $c->id,
                    'text' => $c->name . ' | '
                        . ($c->city->name ?? '')
                        . ', '
                        . ($c->state->name ?? '')
                ];

            });


        /* Country */
        Customer::with('country')
            ->where(
                'company_id',
                $company->id
            )
            ->whereHas(
                'country',
                fn($q) =>
                $q->where(
                    'name',
                    'like',
                    "%{$term}%"
                )
            )
            ->select('country_id')
            ->distinct()
            ->get()
            ->each(function ($r) use (&$results) {

                if ($r->country) {

                    $results[] = [
                        'id' => 'country-' . $r->country_id,
                        'text' => 'Country: ' . $r->country->name
                    ];

                }

            });


        /* State */
        Customer::with('state')
            ->where(
                'company_id',
                $company->id
            )
            ->whereHas(
                'state',
                fn($q) =>
                $q->where(
                    'name',
                    'like',
                    "%{$term}%"
                )
            )
            ->select('state_id')
            ->distinct()
            ->get()
            ->each(function ($r) use (&$results) {

                if ($r->state) {

                    $results[] = [
                        'id' => 'state-' . $r->state_id,
                        'text' => 'State: ' . $r->state->name
                    ];

                }

            });


        /* City */
        Customer::with('city')
            ->where(
                'company_id',
                $company->id
            )
            ->whereHas(
                'city',
                fn($q) =>
                $q->where(
                    'name',
                    'like',
                    "%{$term}%"
                )
            )
            ->select('city_id')
            ->distinct()
            ->get()
            ->each(function ($r) use (&$results) {

                if ($r->city) {

                    $results[] = [
                        'id' => 'city-' . $r->city_id,
                        'text' => 'City: ' . $r->city->name
                    ];

                }

            });



        return response()->json([
            'results' => $results
        ]);

    }
    public function customerReport(Company $company)
    {

        $title = 'Customer Reporting';
        $label = 'Customer Reporting';

        $customers =
            Customer::with([
                'primaryPhone',
                'country',
                'state',
                'city'
            ])
                ->withCount('leads')
                ->where(
                    'company_id',
                    $company->id
                )
                ->whereDate(
                    'created_at',
                    today()
                )
                ->latest()
                ->take(10)
                ->get();

        return view(
            'company.crm.reports.customerReports',
            compact(
                'company',
                'title',
                'label',
                'customers'
            )
        );

    }
    public function customersAjax(Request $request, Company $company)
    {

        $query =
            Customer::with([
                'primaryPhone',
                'country',
                'state',
                'city'
            ])
                ->withCount('leads')
                ->where(
                    'company_id',
                    $company->id
                );



        if ($request->selected) {

            $value =
                $request->selected;

            $type =
                explode(
                    '-',
                    $value
                )[0];

            $id =
                explode(
                    '-',
                    $value
                )[1];



            if ($type == 'customer') {
                $query->where(
                    'id',
                    $id
                );
            }


            if ($type == 'country') {
                $query->where(
                    'country_id',
                    $id
                );
            }


            if ($type == 'state') {
                $query->where(
                    'state_id',
                    $id
                );
            }


            if ($type == 'city') {
                $query->where(
                    'city_id',
                    $id
                );
            }

        } else {

            /*
            Default Today 10
            */

            $query->whereDate(
                'created_at',
                today()
            );

        }


        $customers =
            $query
                ->latest()
                ->take(10)
                ->get();



        return response()->json(
            $customers
        );

    }
    public function orderReport(Company $company)
    {
        $title = 'Order Reporting';

        $label = 'Order Reporting';

        /*
        |--------------------------------------------------------------------------
        | BASE QUERY
        |--------------------------------------------------------------------------
        */

        $baseQuery = Order::query()
            ->where('company_id', $company->id);

        /*
        |--------------------------------------------------------------------------
        | SUMMARY DATA
        |--------------------------------------------------------------------------
        */

        $ordersForSummary = (clone $baseQuery)
            ->with([
                'payments',
                'boms.items',
                'items.bomItems.part'
            ])
            ->get();

        /*
        |--------------------------------------------------------------------------
        | CURRENCY SAFE CALCULATION
        |--------------------------------------------------------------------------
        */

        $totalValue = 0;

        $totalPaid = 0;

        $totalDue = 0;

        foreach ($ordersForSummary as $order) {

            /*
            |--------------------------------------------------------------------------
            | INR NORMALIZATION
            |--------------------------------------------------------------------------
            */

            $rate = (
                $order->currency === 'INR'
                || empty($order->conversion_rate)
                || $order->conversion_rate <= 0
            )
                ? 1
                : $order->conversion_rate;

            /*
            |--------------------------------------------------------------------------
            | CONVERT TO INR
            |--------------------------------------------------------------------------
            */

            $totalValue += (
                $order->final_amount * $rate
            );

            $totalPaid += (
                $order->calculated_paid_amount * $rate
            );

            $totalDue += (
                $order->calculated_due_amount * $rate
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $summary = [

            /*
            |--------------------------------------------------------------------------
            | TOTAL ORDERS
            |--------------------------------------------------------------------------
            */

            'total_orders' => $ordersForSummary->count(),

            /*
            |--------------------------------------------------------------------------
            | FINANCIALS
            |--------------------------------------------------------------------------
            */

            'total_value' => round($totalValue, 2),

            'total_paid' => round($totalPaid, 2),

            'total_due' => round($totalDue, 2),

            /*
            |--------------------------------------------------------------------------
            | AVG PROGRESS
            |--------------------------------------------------------------------------
            */

            'avg_progress' => round(
                $ordersForSummary->avg(
                    fn($order) => $order->progress_percent
                )
            ),

            /*
            |--------------------------------------------------------------------------
            | STATUS COUNTS
            |--------------------------------------------------------------------------
            */

            'confirmed_orders' => $ordersForSummary
                ->where('status', 'confirmed')
                ->count(),

            'production_orders' => $ordersForSummary
                ->where('status', 'in_production')
                ->count(),

            'dispatched_orders' => $ordersForSummary
                ->where('status', 'dispatched')
                ->count(),

            'pending_orders' => $ordersForSummary
                ->where('status', 'pending')
                ->count(),

            'cancelled_orders' => $ordersForSummary
                ->where('status', 'cancelled')
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | PAYMENT COUNTS
            |--------------------------------------------------------------------------
            */

            'paid_orders' => $ordersForSummary
                ->where('payment_status', 'paid')
                ->count(),

            'partial_payments' => $ordersForSummary
                ->where('payment_status', 'partial')
                ->count(),

            'pending_payments' => $ordersForSummary
                ->where('payment_status', 'unpaid')
                ->count(),

        ];

        /*
        |--------------------------------------------------------------------------
        | ORDERS TABLE
        |--------------------------------------------------------------------------
        */

        $orders = Order::with([

            'quotation.lead.customer.primaryPhone',

            'assignedUser',

            'payments',

            'items.bomItems.part',

            'boms.parts',

            'boms.items',

        ])
            ->where('company_id', $company->id)
            ->latest()
            ->paginate(10);

        /*
        |--------------------------------------------------------------------------
        | DEBUG (OPTIONAL)
        |--------------------------------------------------------------------------
        */

        /*
        foreach ($ordersForSummary as $o) {

            logger()->info([

                'order' => $o->order_number,

                'currency' => $o->currency,

                'rate' => $o->conversion_rate,

                'final_amount' => $o->final_amount,

                'paid_amount' => $o->paid_amount,

                'due_amount' => $o->due_amount,

                'converted_total' => (
                    $o->currency === 'INR'
                        ? $o->final_amount
                        : $o->final_amount * $o->conversion_rate
                ),

            ]);

        }
        */

        return view(
            'company.crm.reports.orderReports',
            compact(
                'company',
                'title',
                'label',
                'orders',
                'summary'
            )
        );
    }
    public function orderSearch(Request $request, Company $company)
    {
        $term = $request->q;

        $results = [];

        /*
        |--------------------------------------------------------------------------
        | ORDER SEARCH
        |--------------------------------------------------------------------------
        */

        Order::with([
            'quotation.lead.customer'
        ])
            ->where('company_id', $company->id)

            ->where('order_number', 'like', "%{$term}%")

            ->take(10)

            ->get()

            ->each(function ($o) use (&$results) {

                $results[] = [

                    'id' => 'order-' . $o->id,

                    'text' =>
                        $o->order_number
                        . ' | ' .
                        ($o->customer_name ?? '')

                ];

            });

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER SEARCH
        |--------------------------------------------------------------------------
        */

        Customer::where(
            'company_id',
            $company->id
        )

            ->where(
                'name',
                'like',
                "%{$term}%"
            )

            ->take(10)

            ->get()

            ->each(function ($customer) use (&$results) {

                $results[] = [

                    'id' => 'customer-' . $customer->id,

                    'text' =>
                        'Customer: '
                        . $customer->name

                ];

            });

        return response()->json([
            'results' => $results
        ]);
    }
    public function ordersAjax(Request $request, Company $company)
    {
        $query = Order::with([

            'quotation.lead.customer.primaryPhone',

            'items.machine',
            'items.component',

            'boms.parts',
            'boms.items',

        ])->where(
                'company_id',
                $company->id
            );

        /*
        |--------------------------------------------------------------------------
        | DATE FILTER
        |--------------------------------------------------------------------------
        */
        /*
        |--------------------------------------------------------------------------
        | ORDER STATUS FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );

        }

        /*
        |--------------------------------------------------------------------------
        | PAYMENT STATUS FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('payment_status')) {

            $query->where(
                'payment_status',
                $request->payment_status
            );

        }
        if ($request->from_date) {

            $query->whereDate(
                'created_at',
                '>=',
                $request->from_date
            );

        }

        if ($request->to_date) {

            $query->whereDate(
                'created_at',
                '<=',
                $request->to_date
            );

        }

        /*
        |--------------------------------------------------------------------------
        | SEARCH FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->selected) {

            $value = $request->selected;

            $type = explode('-', $value)[0];

            $id = explode('-', $value)[1];

            /*
            |--------------------------------------------------------------------------
            | ORDER
            |--------------------------------------------------------------------------
            */

            if ($type == 'order') {

                $query->where('id', $id);

            }

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER
            |--------------------------------------------------------------------------
            */

            if ($type == 'customer') {

                $query->whereHas(
                    'quotation.lead.customer',
                    function ($q) use ($id) {

                        $q->where('id', $id);

                    }
                );

            }

        }

        $orders = $query
            ->latest()
            ->get();

        return view(
            'company.crm.reports.partials.order_report_table',
            compact('orders', 'company')
        )->render();
    }

    public function orderDetails(Company $company, Order $order)
    {

        $order->load([

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER
            |--------------------------------------------------------------------------
            */

            'quotation.lead.customer.country',
            'quotation.lead.customer.state',
            'quotation.lead.customer.city',
            'quotation.lead.customer.phones',

            /*
            |--------------------------------------------------------------------------
            | USERS
            |--------------------------------------------------------------------------
            */

            'creator',
            'assignedUser',

            /*
            |--------------------------------------------------------------------------
            | PAYMENTS
            |--------------------------------------------------------------------------
            */

            'payments',

            /*
            |--------------------------------------------------------------------------
            | ORDER ITEMS
            |--------------------------------------------------------------------------
            */

            'items.machine',
            'items.component',
            'items.item',
            'items.bomItems.part',

            /*
            |--------------------------------------------------------------------------
            | BOM
            |--------------------------------------------------------------------------
            */

            'boms.department',
            'boms.supervisor',
            'boms.checker',
            'boms.priority',
            'boms.shift',

            /*
            |--------------------------------------------------------------------------
            | BOM ITEMS
            |--------------------------------------------------------------------------
            */

            'boms.items.item',
            'boms.items.unit',
            'boms.items.employee',
            'boms.items.department',
            'boms.items.shift',

            /*
            |--------------------------------------------------------------------------
            | BOM PARTS
            |--------------------------------------------------------------------------
            */

            'boms.parts.spec',
            'boms.parts.shift',

            /*
            |--------------------------------------------------------------------------
            | STAGE PROGRESS
            |--------------------------------------------------------------------------
            */

            'boms.parts.stageProgresses.stage',
            'boms.parts.stageProgresses.status',

        ]);

        return view(
            'company.crm.reports.orderDetails',
            [
                'company' => $company,
                'order' => $order,
                'title' => 'Order Details - ' . $order->order_number,
                'label' => 'Order Details - ' . $order->order_number,
            ]
        );
    }
}