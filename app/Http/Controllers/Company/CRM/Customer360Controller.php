<?php

namespace App\Http\Controllers\Company\CRM;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Quotation;
use Illuminate\Http\Request;

class Customer360Controller extends Controller
{
    public function load(Company $company, $customerId, $type)
    {
        // Base Lead (Customer)
        $lead = Lead::where('id', $customerId)
            ->where('company_id', $company->id)
            ->firstOrFail();

        switch ($type) {

            case 'leads':
                $data = collect([$lead]); // single lead wrapped as collection
                return view('company.crm.customer360.leads', compact(
                    'company',
                    'data',
                    'lead'
                ));

            case 'quotations':
                $data = Quotation::where('lead_id', $lead->id)->get();

                return view('company.crm.customer360.quotations', compact(
                    'company',
                    'data',
                    'lead'
                ));

            case 'orders':

                $orders = Order::with(['quotation'])
                    ->whereHas('quotation', function ($q) use ($lead) {
                        $q->where('lead_id', $lead->id);
                    })->get();

                $quotation = Quotation::where('lead_id', $lead->id)->latest()->first();

                $hasOrder = $orders->isNotEmpty();

                return view('company.crm.customer360.orders', [
                    'company' => $company,
                    'data' => $orders,
                    'lead' => $lead,
                    'quotation' => $quotation,
                    'hasOrder' => $hasOrder,
                ]);



            case 'payments':

                $payments = Payment::with(['order.quotation'])
                    ->whereHas('order.quotation', function ($q) use ($lead) {
                        $q->where('lead_id', $lead->id);
                    })->get();

                $order = Order::whereHas('quotation', function ($q) use ($lead) {
                    $q->where('lead_id', $lead->id);
                })->latest()->first();

                return view('company.crm.customer360.payments', [
                    'company' => $company,
                    'data' => $payments,
                    'lead' => $lead,
                    'order' => $order,
                ]);

            default:
                abort(404);
        }
    }
}
