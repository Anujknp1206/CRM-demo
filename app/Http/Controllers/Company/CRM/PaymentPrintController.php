<?php

namespace App\Http\Controllers\Company\CRM;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentPrintController extends Controller
{
    public function single(Company $company, Order $order, Payment $payment)
    {
        $payment = $order->payments()
            ->where('id', $payment->id)
            ->firstOrFail();

        return view(
            'company.crm.payments.print.print-single',
            compact('company', 'order', 'payment')
        );
    }


    public function full(Company $company, Order $order)
    {
        // Ensure order belongs to this company
        $order = $company->orders()
            ->where('id', $order->id)
            ->firstOrFail();

        // Only allow full print if fully paid
        abort_if($order->payment_status !== 'paid', 403);

        $payments = $order->payments()
            ->orderBy('payment_date')
            ->get();

        return view(
            'company.crm.payments.print.print-full',
            compact('company', 'order', 'payments')
        );
    }
}


