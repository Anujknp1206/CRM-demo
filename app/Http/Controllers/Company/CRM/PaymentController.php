<?php

namespace App\Http\Controllers\Company\CRM;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    public function index(Company $company)
    {
        return view('company.crm.payments.index', [
            'company' => $company,
            'title' => Auth::user()->name . ':: Payment Management',
            'label' => 'Payment List'
        ]);
    }
    public function details(Request $request)
    {
        $payment = Payment::with([
            'order',
            'order.quotation.lead.customer',
        ])->findOrFail($request->id);

        if ($payment->status === 'completed') {
            // Fetch ALL payments for this order
            $allPayments = Payment::where('order_id', $payment->order_id)
                ->orderBy('payment_date')
                ->get();

            return response()->json([
                'status' => 'completed',
                'order' => $payment->order,
                'currency_symbol' => $payment->order->currency_symbol,
                'payments' => $allPayments,
            ]);
        }

        // Pending → only this payment
        return response()->json([
            'status' => 'pending',
            'payment' => $payment,
            'currency_symbol' => $payment->order->currency_symbol,
        ]);
    }
    public function data(Request $request, Company $company)
    {
        $query = Payment::with([
            'order:id,order_number,quotation_id,final_amount,paid_amount,due_amount,currency',
            'order.quotation:id,lead_id',
            'order.quotation.lead:id,customer_id',
            'order.quotation.lead.customer:id,name,email',
        ])->where('company_id', $company->id);
        $search = $request->search ?: null;
        $from = $request->from_date
            ? Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d')
            : null;

        $to = $request->to_date
            ? Carbon::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d')
            : null;


        if ($search) {

            $query->where(function ($q) use ($search) {

                $q->where('payment_number', 'LIKE', "%{$search}%")

                    ->orWhereHas('order', function ($o) use ($search) {

                        $o->where('order_number', 'LIKE', "%{$search}%")

                            ->orWhereHas('quotation.lead.customer', function ($c) use ($search) {

                                $c->where('name', 'LIKE', "%{$search}%")
                                    ->orWhere('email', 'LIKE', "%{$search}%");

                            })

                            ->orWhereHas('quotation.lead.customer.phones', function ($p) use ($search) {

                                $p->where('phone', 'LIKE', "%{$search}%");

                            });

                    });

            });
        }

        if ($from) {
            $query->whereDate('payment_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('payment_date', '<=', $to);
        }

        if (!$search && !$from && !$to) {
            $query->limit(10);
        }

        $payments = $query->latest()->get();
        $isDefaultToday = !$search && !$from && !$to;
        return view('company.crm.payments.partials.payment_rows', [
            'payments' => $payments,
            'isDefaultToday' => $isDefaultToday,
            'company' => $company
        ])->render();
    }
    public function ajaxSearch(Request $request, Company $company)
    {
        $search = trim($request->search ?? '');

        return Payment::with('order.quotation.lead.customer')
            ->where('company_id', $company->id)
            ->where(function ($q) use ($search) {

                $q->where('payment_number', 'LIKE', "%{$search}%")

                    ->orWhereHas('order', function ($o) use ($search) {

                        $o->where('order_number', 'LIKE', "%{$search}%");

                    })

                    ->orWhereHas('order.quotation.lead.customer', function ($c) use ($search) {

                        $c->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");

                    })

                    ->orWhereHas('order.quotation.lead.customer.phones', function ($p) use ($search) {

                        $p->where('phone', 'LIKE', "%{$search}%");

                    });

            })
            ->limit(20)
            ->get()
            ->map(function ($p) {

                return [
                    'id' => $p->id,
                    'text' => $p->payment_number
                        . ' | ' . $p->order->order_number
                        . ' | ' . optional($p->order->quotation?->lead?->customer)->name
                ];

            });
    }
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_number' => 'required|string|unique:payments,payment_number',
            'amount' => 'required|numeric|min:1',
            'payment_mode' => 'required',
            'transaction_reference' => 'nullable|required_unless:payment_mode,cash',
            'payment_date' => 'required|date_format:d/m/Y',
            'payment_time' => 'required|date_format:H:i',
            'post_date' => 'nullable|required_if:is_post_dated,1|date_format:d/m/Y',
        ]);

        $order = Order::findOrFail($request->order_id);

        $paid = $order->payments()->sum('amount');
        $remaining = $order->final_amount - $paid;

        if ($request->amount > $remaining) {
            return response()->json([
                'message' => 'Amount cannot be greater than remaining due'
            ], 422);
        }

        // Remaining after this payment
        $totalPaid = $paid + $request->amount;
        $newRemaining = $order->final_amount - $totalPaid;

        $paymentStatus = $newRemaining == 0
            ? 'completed'
            : ($totalPaid > 0 ? 'partial' : 'pending');
        $isPostDated = $request->has('is_post_dated');
        // Create payment
        $order->payments()->create([
            'company_id' => $order->company_id,
            'payment_number' => $request->payment_number,
            'amount' => $request->amount,
            'payment_mode' => $request->payment_mode,
            'transaction_reference' => $request->transaction_reference,
            'payment_date' => Carbon::createFromFormat('d/m/Y', $request->payment_date),
            'payment_time' => $request->payment_time,
            'is_post_dated' => $isPostDated,
            'post_date' => $isPostDated
                ? Carbon::createFromFormat('d/m/Y', $request->post_date)
                : null,
            'note' => $request->note,
            'status' => $paymentStatus,
        ]);

        // Update order after payment
        $this->recalculate($order);

        return response()->json(['message' => 'Payment saved successfully']);
    }
    public function edit(Company $company, $payment)
    {
        $payment = Payment::where('company_id', $company->id)
            ->where('id', $payment)
            ->with([
                'order.quotation.lead.customer.primaryPhone',
            ])
            ->firstOrFail();

        return response()->json($payment);
    }
    public function update(Request $request, Company $company, $payment)
    {
        $payment = Payment::where('company_id', $company->id)
            ->findOrFail($payment);
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_mode' => 'required',
            'payment_date' => 'required|date_format:d/m/Y',
            'payment_time' => 'required|date_format:H:i',
            'post_date' => 'nullable|required_if:is_post_dated,1|date_format:d/m/Y',
        ]);

        $order = $payment->order;

        // Sum excluding current payment
        $paidExceptThis = $order->payments()
            ->where('id', '!=', $payment->id)
            ->sum('amount');

        if (($paidExceptThis + $request->amount) > $order->final_amount) {
            return response()->json([
                'message' => 'Payment exceeds order total'
            ], 422);
        }

        $totalPaid = $paidExceptThis + $request->amount;
        $remaining = $order->final_amount - $totalPaid;

        $status = $remaining == 0
            ? 'completed'
            : ($totalPaid > 0 ? 'partial' : 'pending');
        $isPostDated = $request->has('is_post_dated');
        $payment->update([
            'amount' => $request->amount,
            'payment_mode' => $request->payment_mode,
            'transaction_reference' => $request->transaction_reference,
            'payment_date' => Carbon::createFromFormat('d/m/Y', $request->payment_date),
            'is_post_dated' => $isPostDated,
            'post_date' => $isPostDated
                ? Carbon::createFromFormat('d/m/Y', $request->post_date)
                : null,
            'payment_time' => $request->payment_time,
            'note' => $request->note,
            'status' => $status,
        ]);

        // 🔥 Recalculate order
        $this->recalculate($order);
        if ($remaining > 0) {
            Payment::where('order_id', $order->id)
                ->update(['status' => 'pending']);
        }

        return response()->json([
            'message' => 'Payment updated successfully'
        ]);
    }
    public function generateNumber(Company $company)
    {
        $date = now()->format('ymd/Hi');

        // Next payment id (preview only)
        $lastPayment = Payment::latest('id')->first();
        $nextId = $lastPayment ? $lastPayment->id + 1 : 1;

        return response()->json([
            'payment_number' =>
                'PAY-' . $company->initials() . '/' . $date . '/' . $nextId
        ]);
    }
    public function destroy(Company $company, $payment)
    {
        $payment = Payment::where('company_id', $company->id)
            ->findOrFail($payment);

        $order = $payment->order;
        $payment->delete();

        // 🔥 Recalculate order (now handles zero-payments)
        $this->recalculate($order);

        // 🔥 Normalize remaining payment statuses
        if ($order->due_amount > 0) {
            Payment::where('order_id', $order->id)
                ->update(['status' => 'pending']);
        }

        return response()->json(['success' => true]);
    }
    private function recalculate(Order $order)
    {
        $paid = $order->payments()->sum('amount');
        $due = $order->final_amount - $paid;

        $order->update([
            'paid_amount' => $paid,
            'due_amount' => $due,

            // 🔥 Payment status
            'payment_status' => $paid == 0
                ? 'unpaid'
                : ($due == 0 ? 'paid' : 'partial'),

            // 🔥 Order status
            'status' => $paid == 0
                ? 'pending'
                : 'confirmed',
        ]);
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

    public function pdfSingle($companyId, $orderId, $paymentId)
    {
        $company = Company::findOrFail($companyId);
        $order = Order::with('quotation.lead.customer')->findOrFail($orderId);
        $payment = Payment::findOrFail($paymentId);

        $settings = Setting::first();

        $pdf = Pdf::loadView('company.crm.payments.print.pdf-single', compact(
            'company',
            'order',
            'payment',
            'settings'
        ));

        $pdf->setPaper('A4', 'portrait');

        $dompdf = $pdf->getDomPDF();

        /* IMPORTANT: Render first */
        $dompdf->render();

        $canvas = $dompdf->getCanvas();

        $canvas->page_text(
            530,   // X position
            810,   // Y position
            "Page {PAGE_NUM} of {PAGE_COUNT}",
            null,
            7,
            [255, 255, 255] // white color for blue footer
        );


        $number = $payment->payment_number;
        $customerName = $order->quotation->lead->customer->name ?? 'NA';

        $fileName = $this->generateFileName(
            $company,
            'payment', // important
            $number,
            $customerName
        );

        return $pdf->download($fileName);


    }

    public function pdfFull($companyId, $orderId, $paymentId)
    {
        $company = Company::findOrFail($companyId);

        $order = Order::with([
            'quotation.lead.customer',
            'items.machine',
            'items.component',
            'payments'
        ])->findOrFail($orderId);

        $payment = Payment::findOrFail($paymentId);

        $payments = $order->payments->sortBy('payment_date');

        $settings = Setting::first();

        $pdf = Pdf::loadView('company.crm.payments.print.pdf-full', compact(
            'company',
            'order',
            'payments',
            'payment',
            'settings'
        ));

        $pdf->setPaper('A4', 'portrait');

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        $canvas = $dompdf->getCanvas();

        $canvas->page_text(
            520,
            810,
            "Page {PAGE_NUM} of {PAGE_COUNT}",
            null,
            7,
            [255, 255, 255]
        );

        $number = $payment->payment_number;
        $customerName = $order->quotation->lead->customer->name ?? 'NA';

        $fileName = $this->generateFileName(
            $company,
            'payment', // important
            $number,
            $customerName
        );

        return $pdf->download($fileName);
    }
}
