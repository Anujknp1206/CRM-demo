@extends('company.layouts.master')
@section('content')
    <div class="main p-5">

        <div class="d-flex justify-content-end mb-3 no-print">
            @can('export order details')
                <button onclick="printOrderPage()" class="btn btn-success rounded-pill px-4">

                    <i class="bi bi-printer me-2"></i>

                    Print Report

                </button>
            @endcan
        </div>
        <div class="container py-2" id="print-area">

            {{-- HEADER --}}
            <div class="order-header d-flex justify-content-between align-items-start mb-2">

                <div>
                    <small class="order-label text-muted">
                        Order report
                    </small>

                    <h1 class="order-id fw-bold mb-1">
                        {{ $order->order_number }}
                    </h1>
                </div>

                <div class="d-flex gap-2">

                    <span class="badge rounded-pill bg-success-subtle px-3 py-2 mx-1 status-badge  
                                                                                                        @if($order->status == 'confirmed')
                                                                                                            bg-success-subtle text-success
                                                                                                        @elseif($order->status == 'pending')
                                                                                                            bg-secondary-subtle text-secondary
                                                                                                        @elseif($order->status == 'cancelled')
                                                                                                            bg-danger-subtle text-danger
                                                                                                        @else
                                                                                                            bg-secondary-subtle text-dark
                                                                                                        @endif
                                                                                                        ">
                        {{ ucfirst($order->status ?? 'Unknown') }}
                    </span>
                    @php
                        $totalPaid = $order->payments->sum('amount');
                        $paymentStatus = 'Unpaid';
                        if ($totalPaid > 0 && $totalPaid < $order->final_amount) {
                            $paymentStatus = 'Partially Paid';
                        }
                        if ($totalPaid >= $order->final_amount) {
                            $paymentStatus = 'Paid';
                        }
                    @endphp
                    <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2 mx-1 status-badge   @if($paymentStatus == 'Paid')
                        bg-success-subtle text-success

                    @elseif($paymentStatus == 'Partially Paid')
                            bg-primary-subtle text-primary

                        @elseif($paymentStatus == 'Unpaid')
                                bg-danger-subtle text-danger

                            @endif">
                        {{ $paymentStatus }}

                    </span>

                </div>

            </div>
            @php
                $totalPaid = $order->payments->sum('amount');
                $finalAmount = $order->final_amount ?? 0;
                $dueAmount = $finalAmount - $totalPaid;
                $paymentCount = $order->payments->count();
                $totalProgress = 0;
                $progressCount = 0;
                foreach ($order->boms as $bom) {
                    foreach ($bom->parts as $part) {
                        $latestProgress = $part->stageProgresses->sortByDesc('id')->first();
                        if ($latestProgress && $latestProgress->progress_percentage) {
                            $totalProgress += $latestProgress->progress_percentage;
                            $progressCount++;
                        }
                    }
                }
            @endphp
            <div class="row g-3 mb-2">
                <div class="col-md-3">
                    <div class="card stat-card total-card border-0 rounded-4 h-100">
                        <div class="card-body">
                            <small class="stat-label">
                                Final amount
                            </small>
                            <h2 class="fw-bold mt-2">
                                {{ $order->currency_symbol }} {{ number_format($finalAmount, 2) }}
                            </h2>
                            <small class="stat-subtitle">
                                After tax & discount
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card paid-card border-0 rounded-4 h-100">
                        <div class="card-body">
                            <small class="stat-label">
                                Paid
                            </small>
                            <h2 class="fw-bold text-success mt-2">
                                {{ $order->currency_symbol }} {{ number_format($totalPaid, 2) }}
                            </h2>
                            <small class="stat-subtitle">
                                {{ $paymentCount }}
                                {{ Str::plural('payment', $paymentCount) }}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card due-card border-0 rounded-4 h-100">
                        <div class="card-body">
                            <small class="stat-label">
                                Due
                            </small>
                            <h2 class="fw-bold text-danger mt-2">
                                {{ $order->currency_symbol }} {{ number_format($dueAmount, 2) }}
                            </h2>
                            <small class="stat-subtitle">
                                Outstanding
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card progress-card border-0 rounded-4 h-100">
                        <div class="card-body">
                            <small class="stat-label">
                                Progress
                            </small>

                            <h2 class="fw-bold mt-2">
                                {{ $order->progress_percent }}%
                            </h2>

                            <div class="progress custom-progress mt-3">

                                <div class="progress-bar bg-success" style="width: {{ $order->progress_percent }}%">
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- MAIN CONTENT --}}
            <div class="row g-4">

                {{-- LEFT CARD --}}
                <div class="col-lg-6">

                    <div class="card customer-info-card shadow-sm border-0 rounded-4 h-100">

                        <div class="card-body p-4">

                            <h6 class="fw-bold text-uppercase text-muted mb-3">
                                Customer & Order Info
                            </h6>

                            <div class="row mb-2">

                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">
                                        Customer
                                    </small>

                                    <strong>
                                        {{ $order->customer_name ?? 'N/A' }}
                                    </strong>
                                </div>

                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">
                                        Contact person
                                    </small>

                                    <strong>
                                        {{ $order->contact_person ?? 'N/A' }}
                                    </strong>
                                </div>

                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">
                                        Email
                                    </small>

                                    <strong>
                                        {{ $order->email ?? 'N/A' }}
                                    </strong>
                                </div>

                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">
                                        Mobile
                                    </small>

                                    <strong>
                                        {{ optional($order->quotation?->lead?->customer)->full_primary_mobile }}
                                    </strong>
                                </div>

                            </div>

                            <div class="row mb-2">

                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">
                                        Order date
                                    </small>

                                    <strong>
                                        {{ optional($order->order_date)->format('d M Y') ?? 'N/A' }}
                                    </strong>
                                </div>

                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">
                                        Delivery date
                                    </small>

                                    <strong>
                                        {{ optional($order->delivery_date)->format('d M Y') ?? 'N/A' }}
                                    </strong>
                                </div>

                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">
                                        PO number
                                    </small>

                                    <strong>
                                        {{ $order->po_number ?? 'N/A' }}
                                    </strong>
                                </div>

                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">
                                        PO date
                                    </small>

                                    <strong>
                                        {{ optional($order->po_date)->format('d M Y') ?? 'N/A' }}
                                    </strong>
                                </div>

                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">
                                        PI number
                                    </small>

                                    <strong>
                                        {{ $order->quotation->pi_number ?? 'N/A' }}
                                    </strong>
                                </div>

                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">
                                        PI date
                                    </small>

                                    <strong>
                                        {{ optional($order->quotation->pi_date)->format('d M Y') ?? 'N/A' }}
                                    </strong>
                                </div>

                            </div>

                            <hr>

                            <div>

                                <small class="text-muted d-block mb-2">
                                    Delivery address
                                </small>

                                <strong>
                                    {!! nl2br(e($order->delivery_address ?? 'N/A')) !!}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- RIGHT CARD --}}
                <div class="col-lg-6">

                    <div class="card financial-card shadow-sm border-0 rounded-4 h-100">

                        <div class="card-body p-4">

                            <h6 class="fw-bold text-uppercase text-muted mb-3">
                                Financial Summary
                            </h6>

                            <div class="d-flex justify-content-between mb-3">

                                <span>
                                    Subtotal
                                </span>

                                <strong>
                                    {{ $order->currency_symbol }}
                                    {{ number_format($order->total_amount ?? 0, 2) }}
                                </strong>

                            </div>

                            <div class="d-flex justify-content-between mb-3">

                                <span>
                                    Discount
                                </span>

                                <strong class="text-danger">

                                    - {{ $order->currency_symbol }}
                                    {{ number_format($order->discount ?? 0, 2) }}

                                </strong>

                            </div>

                            <div class="d-flex justify-content-between mb-3">

                                <span>
                                    Tax ({{ $order->tax ?? 0 }}%)
                                </span>

                                <strong>

                                    {{ $order->currency_symbol }}
                                    {{ number_format($order->tax_amount ?? 0, 2) }}

                                </strong>

                            </div>

                            <hr>

                            <div class="d-flex justify-content-between mb-2">

                                <span class="fw-bold">
                                    Final amount
                                </span>

                                <strong class="fw-bold">

                                    {{ $order->currency_symbol }}
                                    {{ number_format($order->final_amount ?? 0, 2) }}

                                </strong>

                            </div>

                            <hr>

                            <div class="row">

                                <div class="col-6 mb-3">

                                    <small class="text-muted d-block">
                                        Currency
                                    </small>

                                    <strong>
                                        {{ $order->currency }}
                                        ({{ $order->currency_symbol }})
                                    </strong>

                                </div>

                                <div class="col-6 mb-3">

                                    <small class="text-muted d-block">
                                        Conversion rate
                                    </small>

                                    <strong>
                                        {{ $order->conversion_rate ?? 1 }}
                                    </strong>

                                </div>

                                <div class="col-6 mb-3">

                                    <small class="text-muted d-block">
                                        Quotation ref
                                    </small>

                                    <strong class="text-dark">
                                        {{ $order->quotation?->quote_number ?? 'N/A' }}
                                    </strong>

                                </div>

                                <div class="col-6 mb-3">

                                    <small class="text-muted d-block">
                                        Lead ref
                                    </small>

                                    <strong class="text-dark">
                                        {{ $order->lead?->lead_code ?? 'N/A' }}
                                    </strong>

                                </div>

                            </div>

                            <hr>

                            <h6 class="fw-bold text-uppercase text-muted mb-3">
                                Payment Status
                            </h6>

                            @php

                                $paidPercent = 0;

                                if (($order->final_amount ?? 0) > 0) {

                                    $paidPercent =
                                        (
                                            $order->calculated_paid_amount
                                            /
                                            $order->final_amount
                                        ) * 100;
                                }

                                $paidPercent = min(round($paidPercent), 100);

                            @endphp

                            <div class="d-flex align-items-center gap-3">

                                <div class="progress flex-grow-1 custom-progress">

                                    <div class="progress-bar bg-success" style="width: {{ $paidPercent }}%">
                                    </div>

                                </div>

                                <strong>

                                    {{ $order->currency_symbol }}
                                    {{ number_format($order->calculated_paid_amount, 2) }}

                                    /

                                    {{ $order->currency_symbol }}
                                    {{ number_format($order->final_amount ?? 0, 2) }}

                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
            {{-- ORDER ITEMS --}}
            <div class="card order-items-card shadow-sm border-0 rounded-4 mt-4">

                <div class="card-body p-4">

                    <h6 class="fw-bold text-uppercase text-muted mb-2">
                        Order Items
                    </h6>

                    <div class="table-responsive">

                        <table class="table order-items-table align-middle">

                            <thead>

                                <tr>

                                    <th width="60">#</th>

                                    <th width="38%">
                                        Item / Description
                                    </th>

                                    <th width="120">
                                        Type
                                    </th>

                                    <th width="80">
                                        Qty
                                    </th>

                                    <th width="140">
                                        Unit Price
                                    </th>

                                    <th width="140">
                                        Total
                                    </th>

                                    <th width="220">
                                        Production
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($order->items as $index => $item)

                                    @php

                                        $itemName =
                                            $item->machine?->name
                                            ??
                                            $item->component?->name
                                            ??
                                            $item->item?->name
                                            ??
                                            'N/A';

                                        $description =
                                            strip_tags(
                                                $item->description
                                                ??
                                                ''
                                            );

                                        $total =
                                            ($item->quantity ?? 0)
                                            *
                                            ($item->unit_price ?? 0);

                                       $progress = $item->progress_percent;

                                    @endphp

                                    <tr>

                                        {{-- SERIAL --}}
                                        <td class="text-bold">
                                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                        </td>

                                        {{-- ITEM --}}
                                        <td>

                                            <div class="item-title">
                                                {{ $itemName }}
                                            </div>

                                            <div class="item-description">

                                                {{ Str::limit($description, 140) }}

                                            </div>

                                        </td>

                                        {{-- TYPE --}}
                                        <td>

                                            <span class="badge item-badge rounded-pill">

                                                {{ $item->machine_id ? 'Machine' : 'Component' }}

                                            </span>

                                        </td>

                                        {{-- QTY --}}
                                        <td class="fw-semibold">
                                            {{ $item->quantity }}
                                        </td>

                                        {{-- UNIT PRICE --}}
                                        <td class="price-text">

                                            {{ $order->currency_symbol }}
                                            {{ number_format($item->unit_price ?? 0, 2) }}

                                        </td>

                                        {{-- TOTAL --}}
                                        <td class="price-text fw-bold">

                                            {{ $order->currency_symbol }}
                                            {{ number_format($total, 2) }}

                                        </td>

                                        {{-- PROGRESS --}}
                                        <td>

                                            <div class="d-flex align-items-center gap-3">

                                                <span class="progress-text">
                                                    {{ $progress }}%
                                                </span>

                                                <div class="progress custom-progress flex-grow-1">

                                                    <div class="progress-bar bg-success" style="width: {{ $progress }}%">
                                                    </div>

                                                </div>

                                            </div>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            {{-- BOTTOM CARDS --}}
            @php

                /*
                |--------------------------------------------------------------------------
                | BOM TOTAL ITEMS
                |--------------------------------------------------------------------------
                */

                $totalBomItems = $order->boms
                    ->flatMap(fn($bom) => $bom->items)
                    ->sum('quantity');

                /*
                |--------------------------------------------------------------------------
                | ISSUED QTY
                |--------------------------------------------------------------------------
                */

                $bomIds = $order->boms->pluck('id');

                $issuedQty = \App\Models\IssueItem::query()
                    ->whereHas('issue', function ($q) use ($bomIds) {

                        $q->whereIn('bom_id', $bomIds);

                    })
                    ->sum('issued_qty');

                /*
                |--------------------------------------------------------------------------
                | PRODUCTION AVG
                |--------------------------------------------------------------------------
                */

                $productionAvg = round(
                    $order->progress_percent ?? 0
                );

                /*
                |--------------------------------------------------------------------------
                | BOM STATUS
                |--------------------------------------------------------------------------
                */

                if ($productionAvg <= 0) {

                    $bomStatus = 'Not Started';

                    $bomStatusClass =
                        'bg-secondary-subtle text-secondary';

                } elseif ($productionAvg < 100) {

                    $bomStatus = 'In Progress';

                    $bomStatusClass =
                        'bg-primary-subtle text-primary';

                } else {

                    $bomStatus = 'Completed';

                    $bomStatusClass =
                        'bg-success-subtle text-success';
                }

            @endphp
            <div class="card payment-history-card shadow-sm border-0 rounded-4 h-100">

                <div class="card-body p-4">

                    <h6 class="fw-bold text-uppercase text-muted mb-2">
                        Payment History
                    </h6>

                    <div class="table-responsive">

                        <div class="table-responsive">

                            <table class="table payment-table align-middle">

                                <thead>

                                    <tr>

                                        <th>
                                            Payment Date
                                        </th>

                                        <th>
                                            Payment Details
                                        </th>

                                        <th>
                                            Mode
                                        </th>

                                        <th>
                                            Status
                                        </th>

                                        <th class="text-end">
                                            Amount
                                        </th>

                                        <th class="text-end">
                                            Remaining
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @php
                                        $runningPaid = 0;
                                    @endphp

                                    @forelse($order->payments as $payment)

                                        @php

                                            $runningPaid += ($payment->amount ?? 0);

                                            $remainingAmount =
                                                max(
                                                    ($order->final_amount ?? 0)
                                                    -
                                                    $runningPaid,
                                                    0
                                                );

                                        @endphp

                                        <tr>

                                            {{-- DATE --}}
                                            <td class="payment-date-cell">

                                                <div class="payment-date">

                                                    {{ optional($payment->payment_date)->format('d M Y') ?? 'N/A' }}

                                                </div>

                                                @if($payment->payment_time)

                                                    <div class="payment-time">

                                                        {{ \Carbon\Carbon::parse($payment->payment_time)->format('h:i A') }}

                                                    </div>

                                                @endif

                                            </td>

                                            {{-- DETAILS --}}
                                            <td>

                                                {{-- PAYMENT NUMBER --}}
                                                <div class="payment-title">

                                                    {{ $payment->payment_number ?? 'N/A' }}

                                                </div>

                                                {{-- TRANSACTION REF --}}
                                                @if($payment->transaction_reference)

                                                    <div class="payment-meta">

                                                        Ref:
                                                        {{ $payment->transaction_reference }}

                                                    </div>

                                                @endif

                                                {{-- POST DATED --}}
                                                @if($payment->is_post_dated)

                                                    <div class="post-dated-badge">

                                                        Post Dated

                                                        @if($payment->post_date)

                                                            ·
                                                            {{ optional($payment->post_date)->format('d M Y') }}

                                                        @endif

                                                    </div>

                                                @endif
                                            </td>

                                            {{-- MODE --}}
                                            <td>

                                                <span class="payment-mode-badge">

                                                    {{ ucfirst($payment->payment_mode ?? 'N/A') }}

                                                </span>

                                            </td>

                                            {{-- STATUS --}}
                                            <td>

                                                <span
                                                    class="status-pill

                                                                                                                                                                                                                                                                            @if($payment->status == 'completed')

                                                                                                                                                                                                                                                                                status-success

                                                                                                                                                                                                                                                                            @elseif($payment->status == 'partial')

                                                                                                                                                                                                                                                                                status-primary

                                                                                                                                                                                                                                                                            @elseif($payment->status == 'pending')

                                                                                                                                                                                                                                                                                status-warning

                                                                                                                                                                                                                                                                            @elseif($payment->status == 'failed')

                                                                                                                                                                                                                                                                                status-danger

                                                                                                                                                                                                                                                                            @else

                                                                                                                                                                                                                                                                                status-secondary

                                                                                                                                                                                                                                                                            @endif
                                                                                                                                                                                                                                                                        ">

                                                    {{ ucfirst($payment->status ?? 'Pending') }}

                                                </span>

                                            </td>

                                            {{-- AMOUNT --}}
                                            <td class="text-end">

                                                <div class="payment-amount">

                                                    {{ $order->currency_symbol }}
                                                    {{ number_format($payment->amount ?? 0, 2) }}

                                                </div>

                                            </td>

                                            {{-- REMAINING --}}
                                            <td class="text-end">

                                                <div class="remaining-amount">

                                                    {{ $order->currency_symbol }}
                                                    {{ number_format($remainingAmount, 2) }}

                                                </div>

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="6" class="text-center py-5 text-muted">

                                                No payments yet

                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>
            {{-- BOM & PRODUCTION --}}


            @foreach($order->boms as $bom)

                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">

                        {{-- HEADER --}}
                        <div class="bom-header p-2">

                            <div class="d-flex justify-content-between align-items-center flex-wrap">

                                {{-- LEFT --}}
                                <div>

                                    <h4 class="fw-bold mb-1">
                                        BOM #{{ $bom->bom_number }}
                                    </h4>

                                    <div class="small opacity-75">
                                        Delivery:
                                        {{ $bom->delivery_date_formatted }}
                                    </div>

                                </div>


                                {{-- RIGHT --}}
                                <div class="bom-progress-wrapper text-center">

                                    <div class="progress-circle" style="--progress: {{ $bom->production_progress }};">

                                        <span>
                                            {{ $bom->production_progress }}%
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- BODY --}}
                        <div class="card-body p-4">
                            <div class="row g-4 align-items-stretch">

                                {{-- SUPERVISOR --}}
                                <div class="col-xl-3 col-md-6">

                                    <div class="info-card h-100">

                                        <div class="info-label">
                                            Supervisor
                                        </div>

                                        <div class="info-name">

                                            {{ trim(
                    ($bom->supervisor?->first_name ?? '') . ' ' .
                    ($bom->supervisor?->middle_name ?? '') . ' ' .
                    ($bom->supervisor?->last_name ?? '')
                ) ?: '-' }}

                                        </div>

                                        <div class="info-subtitle">

                                            {{ $bom->supervisor?->department?->name ?? 'No Department' }}

                                        </div>

                                    </div>

                                </div>


                                {{-- CHECKED BY --}}
                                <div class="col-xl-3 col-md-6">

                                    <div class="info-card h-100">

                                        <div class="info-label">
                                            Checked By
                                        </div>

                                        <div class="info-name">

                                            {{ trim(
                    ($bom->checker?->first_name ?? '') . ' ' .
                    ($bom->checker?->middle_name ?? '') . ' ' .
                    ($bom->checker?->last_name ?? '')
                ) ?: '-' }}

                                        </div>

                                        <div class="info-subtitle">

                                            {{ $bom->checker?->department?->name ?? 'No Department' }}

                                        </div>

                                    </div>

                                </div>


                                {{-- STATUS + PRIORITY --}}
                                <div class="col-xl-3 col-md-6">

                                    <div class="info-card h-100">

                                        <div class="info-label">
                                            Production Status
                                        </div>

                                        <div class="mt-2">

                                            @php
                                                $statusClass = match ($bom->status) {
                                                    'completed' => 'success',
                                                    'in_progress' => 'primary',
                                                    'pending' => 'warning',
                                                    default => 'secondary'
                                                };
                                            @endphp

                                            <span class="badge bg-{{ $statusClass }} px-3 py-2 rounded-pill">

                                                {{ ucwords(str_replace('_', ' ', $bom->status)) }}

                                            </span>

                                        </div>

                                        <div class="info-subtitle mt-3">

                                            Priority:
                                            <strong>
                                                {{ $bom->priority?->name ?? '-' }}
                                            </strong>

                                        </div>

                                    </div>

                                </div>


                                {{-- SHIFT --}}
                                <div class="col-xl-3 col-md-6">

                                    <div class="info-card h-100">

                                        <div class="info-label">
                                            Shift
                                        </div>

                                        <div class="info-name">

                                            {{ $bom->shift?->name ?? '-' }}

                                        </div>

                                        <div class="info-subtitle">

                                            Production Shift

                                        </div>

                                    </div>

                                </div>

                            </div>

                            {{-- PARTS --}}
                            @foreach($bom->parts as $part)

                                <div class="part-wrapper mb-2">

                                    {{-- PART HEADER --}}
                                    <div class="part-header">

                                        <div>

                                            <h5 class="mb-1 fw-bold">
                                                {{ $part->part_name }}
                                            </h5>

                                            <div class="small text-muted">

                                                Weightage:
                                                {{ $part->weightage }}%

                                            </div>

                                        </div>

                                    </div>


                                    {{-- PART META --}}
                                    <div class="row g-3 mb-4">

                                        <div class="col-md-4">

                                            <div class="meta-card">

                                                <label>Specification</label>

                                                <div>
                                                    {{ $part->spec?->name ?? '-' }}
                                                </div>

                                            </div>

                                        </div>

                                        <div class="col-md-4">

                                            <div class="meta-card">

                                                <label>Items</label>

                                                <div>
                                                    {{ $part->items->count() }}
                                                </div>

                                            </div>

                                        </div>

                                        <div class="col-md-4">

                                            <div class="meta-card">

                                                <label>Progress</label>

                                                <div>
                                                    {{ $part->progress_percent }}%
                                                </div>

                                            </div>

                                        </div>
                                    </div>


                                    {{-- PART PROGRESS --}}
                                    <div class="mb-4">

                                        <div class="d-flex justify-content-between mb-2">

                                            <small>
                                                Part Progress
                                            </small>

                                            <small>
                                                {{ $part->progress_percent }}%
                                            </small>

                                        </div>

                                        <div class="progress" style="height:8px;">

                                            <div class="progress-bar bg-info" style="width:{{ $part->progress_percent }}%">
                                            </div>

                                        </div>

                                    </div>


                                    {{-- ITEMS TABLE --}}
                                    <div class="table-responsive">

                                        <table class="table table-hover align-middle">

                                            <thead class="table-light">

                                                <tr>
                                                    <th>Item</th>
                                                    <th>Qty</th>
                                                    <th>Employee</th>
                                                    <th>Department</th>
                                                    <th>Status</th>
                                                    <th>Notes</th>
                                                </tr>

                                            </thead>

                                            <tbody>

                                                @foreach($part->items as $item)

                                                    <tr>

                                                        <td>

                                                            <div class="fw-semibold">
                                                                {{ $item->item?->name }}
                                                            </div>

                                                            <small class="text-muted">
                                                                {{ $item->unit?->name }}
                                                            </small>

                                                        </td>

                                                        <td>
                                                            {{ $item->quantity }}
                                                        </td>

                                                        <td>

                                                            @if($item->employee)

                                                                <div class="fw-semibold">
                                                                    {{ $item->employee->first_name }}
                                                                    {{ $item->employee->last_name }}
                                                                </div>

                                                            @else

                                                                -

                                                            @endif

                                                        </td>

                                                        <td>
                                                            {{ $item->department?->name }}
                                                        </td>

                                                        <td>

                                                            @php
                                                                $statusClass = match ($item->status) {
                                                                    'completed' => 'success',
                                                                    'pending' => 'warning',
                                                                    'in_progress' => 'primary',
                                                                    default => 'secondary'
                                                                };
                                                            @endphp

                                                            <span class="badge bg-{{ $statusClass }}">
                                                                {{ ucfirst($item->status) }}
                                                            </span>

                                                        </td>

                                                        <td>

                                                            <div>
                                                                {{ $item->notes ?: '-' }}
                                                            </div>

                                                        </td>

                                                    </tr>

                                                @endforeach

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

            @endforeach


        </div>

    </div>
@endsection

@push('styles')
    <style>
        .card-body {
            padding: 10px !important;
        }

        /* Main Container */
        .order-header {
            background: linear-gradient(135deg,
                    #ffffff 0%,
                    #f4f8ff 45%,
                    #eef7ff 100%);

            padding: 15px 15px;
            border-radius: 18px;

            border: 1px solid rgba(226, 232, 240, 0.8);

            box-shadow:
                0 4px 18px rgba(15, 23, 42, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, .6);

            transition: all .3s ease;

            position: relative;
            overflow: hidden;
        }

        .order-header:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(15, 23, 42, 0.08);
        }

        /* Small Heading */
        .order-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 6px;
        }

        /* Order ID */
        .order-id {
            font-size: 32px;
            color: #111827;
            letter-spacing: -0.5px;
        }

        /* Meta Text */
        .order-meta {
            font-size: 14px;
            font-weight: 500;
            color: #6b7280 !important;
        }

        /* Badges */
        .status-badge {
            font-size: 13px;
            font-weight: 600;
            border: 1px solid transparent;
            transition: all .2s ease;
        }

        /* Success Badge */
        .bg-success-subtle {
            background: #eaf8f0;
            border-color: #b7ebc9;
        }

        /* Primary Badge */
        .bg-primary-subtle {
            background: #edf4ff;
            border-color: #cfe0ff;
        }

        /* Hover Effect */
        .status-badge:hover {
            transform: scale(1.04);
        }

        /* Common Card Style */
        .stat-card {
            border-radius: 10%;
            padding: 8px;
            transition: all .3s ease;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.05);
            overflow: hidden;
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
        }

        /* Soft Background Colors */
        .total-card {
            background: linear-gradient(135deg, #f8fafc, #eef4ff);
        }

        .paid-card {
            background: linear-gradient(135deg, #ecfdf3, #dff7ea);
        }

        .due-card {
            background: linear-gradient(135deg, #fff1f2, #ffe4e6);
        }

        .progress-card {
            background: linear-gradient(135deg, #f5f3ff, #ede9fe);
        }

        /* Labels */
        .stat-label {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6b7280;
        }

        /* Subtitle */
        .stat-subtitle {
            color: #6b7280;
            font-size: 13px;
            font-weight: 500;
        }

        /* Number Styling */
        .stat-card h2 {
            font-size: 30px;
            letter-spacing: -1px;
            color: #111827;
        }

        /* Progress */
        /* Progress Wrapper */
        .custom-progress {
            height: 10px;

            background: rgba(203, 213, 225, 0.45);

            border: 1px solid rgba(148, 163, 184, 0.25);

            border-radius: 50px;

            overflow: hidden;

            box-shadow:
                inset 0 1px 2px rgba(15, 23, 42, 0.06);
        }

        /* Progress Fill */
        .custom-progress .progress-bar {
            border-radius: 50px;

            background: linear-gradient(90deg,
                    #22c55e,
                    #16a34a) !important;

            transition: width .6s ease;
        }

        /* Optional:
                                                                                                                                           show tiny line even at 0%
                                                                                                                                        */
        .custom-progress .progress-bar[style*="0%"] {
            min-width: 6px;
            opacity: .35;
        }

        /* =========================
                                                                                                                                                                                           COMMON CARD STYLE
                                                                                                                                                                                        ========================= */

        .customer-info-card,
        .financial-card {
            overflow: hidden;
            position: relative;
            transition: all .3s ease;
            border: 1px solid rgba(255, 255, 255, .4);
            backdrop-filter: blur(10px);
        }

        .customer-info-card:hover,
        .financial-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08) !important;
        }

        /* =========================
                                                                                                                                                                                           LEFT CARD
                                                                                                                                                                                        ========================= */

        .customer-info-card {
            background: linear-gradient(135deg,
                    #ffffff 0%,
                    #f0f7ff 100%);
        }

        /* =========================
                                                                                                                                                                                           RIGHT CARD
                                                                                                                                                                                        ========================= */

        .financial-card {
            background: linear-gradient(135deg,
                    #ffffff 0%,
                    #f4fff7 100%);
        }

        /* =========================
                                                                                                                                                                                           HEADING
                                                                                                                                                                                        ========================= */

        .card h6 {
            font-size: 13px;
            letter-spacing: 1px;
            font-weight: 700;
            color: #64748b !important;
        }

        /* =========================
                                                                                                                                                                                           LABELS
                                                                                                                                                                                        ========================= */

        .card small {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .4px;
            color: #94a3b8 !important;
            text-transform: uppercase;
        }

        /* =========================
                                                                                                                                                                                           VALUES
                                                                                                                                                                                        ========================= */

        .card strong {
            color: #0f172a;
            font-size: 15px;
            font-weight: 700;
        }

        /* =========================
                                                                                                                                                                                           DIVIDER
                                                                                                                                                                                        ========================= */

        .card hr {
            border-color: rgba(148, 163, 184, .18);
            opacity: 1;
        }

        /* =========================
                                                                                                                                                                                           FINANCIAL ROWS
                                                                                                                                                                                        ========================= */

        .financial-card .d-flex {
            position: relative;
            z-index: 2;
        }

        .financial-card span {
            color: #475569;
            font-weight: 500;
        }

        /* =========================
                                                                                                                                                                                           PROGRESS BAR
                                                                                                                                                                                        ========================= */

        .progress {
            background: rgba(226, 232, 240, .7);
            border-radius: 50px;
            overflow: hidden;
        }

        .progress-bar {
            border-radius: 50px;
        }

        /* =========================
                                                                                                                                                                                           HOVER EFFECT
                                                                                                                                                                                        ========================= */

        .card:hover strong {
            transition: .3s ease;
            color: #111827;
        }

        /* =========================
                                                                                                                                                                               ORDER ITEMS CARD
                                                                                                                                                                            ========================= */

        .order-items-card {
            background: linear-gradient(135deg,
                    #ffffff 0%,
                    #f8fbff 100%);

            overflow: hidden;
            position: relative;

            border: 1px solid rgba(226, 232, 240, .7);

            transition: all .3s ease;
        }

        /* Hover Effect */
        .order-items-card:hover {
            transform: translateY(-4px);

            box-shadow:
                0 14px 34px rgba(15, 23, 42, 0.08) !important;
        }

        /* =========================
                                                                                                                                                                               HEADING
                                                                                                                                                                            ========================= */

        .order-items-card h6 {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #64748b !important;
        }

        /* =========================
                                                                                                                                                                               TABLE
                                                                                                                                                                            ========================= */

        .order-items-card .table {
            margin-bottom: 0;
        }

        .order-items-card .table thead {
            background: rgba(248, 250, 252, .8);
        }

        .order-items-card .table thead th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .6px;
            font-weight: 700;
            color: #94a3b8;

            border-bottom: 1px solid #e2e8f0;
            padding: 16px 14px;
        }

        /* =========================
                                                                                                                                                                               TABLE BODY
                                                                                                                                                                            ========================= */

        .order-items-card .table tbody tr {
            transition: all .25s ease;
        }

        .order-items-card .table tbody tr:hover {
            background: rgba(241, 245, 249, .45);
        }

        /* Table Cells */
        .order-items-card .table td {
            padding: 10px 10px;
            vertical-align: middle;
            border-color: rgba(226, 232, 240, .7);
        }

        /* =========================
                                                                                                                                                                               PRODUCT NAME
                                                                                                                                                                            ========================= */

        .order-items-card strong {
            color: #0f172a;
            font-weight: 700;
        }

        .order-items-card .text-muted {
            font-size: 13px;
            margin-top: 4px;
        }

        /* =========================
                                                                                                                                                                               BADGES
                                                                                                                                                                            ========================= */

        .order-items-card .badge {
            background: rgba(148, 163, 184, .12) !important;
            color: #334155 !important;

            font-size: 12px;
            font-weight: 600;

            padding: 8px 14px;
        }

        /* =========================
                                                                                                                                                                               PROGRESS BAR
                                                                                                                                                                            ========================= */

        .order-items-card .progress {
            height: 8px !important;

            border-radius: 50px;
            overflow: hidden;

            background: rgba(226, 232, 240, .7);
        }

        .order-items-card .progress-bar {
            border-radius: 50px;

            background: linear-gradient(90deg,
                    #22c55e,
                    #16a34a) !important;
        }

        /* =========================
                                                                                                                                                                               PRICE COLUMN
                                                                                                                                                                            ========================= */

        .order-items-card .fw-bold {
            color: #111827;
        }

        /* =========================
                                                                                                                                                                               RESPONSIVE
                                                                                                                                                                            ========================= */

        @media (max-width: 768px) {

            .order-items-card .table th,
            .order-items-card .table td {
                white-space: nowrap;
            }

        }

        /* ========================================
                                                                                                                                                                           COMMON CARD STYLE
                                                                                                                                                                        ======================================== */

        .production-card,
        .payment-history-card {
            position: relative;
            overflow: hidden;

            border: 1px solid rgba(226, 232, 240, .7);

            transition: all .3s ease;

            backdrop-filter: blur(10px);
        }

        .production-card:hover,
        .payment-history-card:hover {
            transform: translateY(-4px);

            box-shadow:
                0 14px 36px rgba(15, 23, 42, 0.08) !important;
        }

        /* ========================================
                                                                                                                                                                           LEFT CARD (BOM)
                                                                                                                                                                        ======================================== */

        .production-card {
            background: linear-gradient(135deg,
                    #ffffff 0%,
                    #f6fbff 100%);
        }

        /* ========================================
                                                                                                                                                                           RIGHT CARD (PAYMENT)
                                                                                                                                                                        ======================================== */

        .payment-history-card {
            background: linear-gradient(135deg,
                    #ffffff 0%,
                    #f8fff9 100%);
        }

        /* ========================================
                                                                                                                                                                           HEADINGS
                                                                                                                                                                        ======================================== */

        .production-card h6,
        .payment-history-card h6 {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #64748b !important;
        }

        /* ========================================
                                                                                                                                                                           LABELS
                                                                                                                                                                        ======================================== */

        .production-card small,
        .payment-history-card small {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #94a3b8 !important;
            font-weight: 600;
        }

        /* ========================================
                                                                                                                                                                           VALUES
                                                                                                                                                                        ======================================== */

        .production-card strong,
        .payment-history-card strong {
            color: #0f172a;
            font-weight: 700;
        }

        /* ========================================
                                                                                                                                                                           BADGES
                                                                                                                                                                        ======================================== */

        .production-card .badge {
            background: rgba(34, 197, 94, .12) !important;
            color: #15803d !important;

            border: 1px solid rgba(34, 197, 94, .2);

            font-weight: 600;
        }

        /* ========================================
                                                                                                                                                                           TABLE
                                                                                                                                                                        ======================================== */

        .payment-history-card .table {
            margin-bottom: 0;
        }

        .payment-history-card .table thead {
            background: rgba(248, 250, 252, .8);
        }

        .payment-history-card .table thead th {
            border-bottom: 1px solid #e2e8f0;

            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .6px;

            padding: 14px;

            color: #94a3b8;
        }

        /* Table Rows */
        .payment-history-card .table tbody tr {
            transition: .25s ease;
        }

        .payment-history-card .table tbody tr:hover {
            background: rgba(241, 245, 249, .45);
        }

        /* Table Cells */
        .payment-history-card .table td {
            padding: 5px 5px;
            border-color: rgba(226, 232, 240, .7);
        }

        .production-card hr,
        .payment-history-card hr {
            border-color: rgba(148, 163, 184, .18);
            opacity: 1;
        }

        /* ========================================
                                                                                                                                                                           REMARKS & TERMS
                                                                                                                                                                        ======================================== */

        .payment-history-card p {
            color: #64748b !important;

            line-height: 1.8;

            font-size: 14px;
        }

        /* =====================================
                                                                                                                           TABLE LAYOUT
                                                                                                                        ===================================== */

        .order-items-table {
            margin-bottom: 0;
        }

        .order-items-table thead th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .8px;

            color: #94a3b8;

            font-weight: 700;

            padding: 18px 14px;

            border-bottom: 1px solid #e2e8f0;
        }

        /* =====================================
                                                                                                                           ROWS
                                                                                                                        ===================================== */

        .order-items-table tbody tr {
            transition: .25s ease;
        }

        .order-items-table tbody tr:hover {
            background: rgba(248, 250, 252, .7);
        }

        .order-items-table td {
            padding: 22px 14px;
            vertical-align: middle;

            border-color: rgba(226, 232, 240, .7);
        }

        /* =====================================
                                                                                                                           ITEM TITLE
                                                                                                                        ===================================== */

        .item-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;

            margin-bottom: 6px;
        }

        /* =====================================
                                                                                                                           DESCRIPTION
                                                                                                                        ===================================== */

        .item-description {
            color: #64748b;

            font-size: 12px;

            line-height: 1.7;

            max-width: 520px;
        }

        /* =====================================
                                                                                                                           BADGE
                                                                                                                        ===================================== */

        .item-badge {
            background: rgba(148, 163, 184, .12);

            color: #475569;

            padding: 8px 14px;

            font-size: 12px;
            font-weight: 600;
        }

        /* =====================================
                                                                                                                           PRICE
                                                                                                                        ===================================== */

        .price-text {
            white-space: nowrap;

            font-size: 16px;

            color: #0f172a;
        }

        /* =====================================
                                                                                                                           PROGRESS
                                                                                                                        ===================================== */

        .progress-text {
            min-width: 42px;

            font-weight: 700;

            color: #475569;

            font-size: 13px;
        }

        .custom-progress {
            height: 10px;

            background: rgba(226, 232, 240, .8);

            border-radius: 50px;

            overflow: hidden;

            border: 1px solid rgba(203, 213, 225, .5);
        }

        .custom-progress .progress-bar {
            border-radius: 50px;

            background: linear-gradient(90deg,
                    #22c55e,
                    #16a34a) !important;
        }

        /* ========================================
                                                                                                                                                                           PRODUCTION ROWS
                                                                                                                                                                        ======================================== */

        .production-card .d-flex {
            padding: 8px 0;

            border-bottom: 1px dashed rgba(226, 232, 240, .7);
        }

        .production-card .d-flex:last-child {
            border-bottom: none;
        }

        /* ========================================
                                                                                                                                                                           NUMBER STYLE
                                                                                                                                                                        ======================================== */

        .production-card .fs-5 {
            font-size: 24px !important;

            letter-spacing: -.5px;
        }

        /* ========================================
                                                                                                                                                                           EXTRA PREMIUM EFFECT
                                                                                                                                                                        ======================================== */

        .production-card::after,
        .payment-history-card::after {
            content: '';

            position: absolute;

            inset: 0;

            background:
                linear-gradient(180deg,
                    rgba(255, 255, 255, .25),
                    transparent);

            pointer-events: none;
        }

        /* =====================================
                                                                                                                       TABLE
                                                                                                                    ===================================== */

        .payment-table {
            margin-bottom: 0;
        }

        /* =====================================
                                                                                                                       HEADER
                                                                                                                    ===================================== */

        .payment-table thead th {

            font-size: 12px;

            text-transform: uppercase;

            letter-spacing: .8px;

            color: #94a3b8;

            font-weight: 700;

            padding: 18px 16px;

            border-bottom: 1px solid #e2e8f0;

            background: rgba(248, 250, 252, .75);

            white-space: nowrap;
        }

        /* =====================================
                                                                                                                       BODY
                                                                                                                    ===================================== */

        .payment-table td {

            padding: 22px 16px;

            vertical-align: middle;

            border-color: rgba(226, 232, 240, .7);
        }

        /* =====================================
                                                                                                                       ROW HOVER
                                                                                                                    ===================================== */

        .payment-table tbody tr {
            transition: .25s ease;
        }

        .payment-table tbody tr:hover {
            background: rgba(248, 250, 252, .75);
        }

        /* =====================================
                                                                                                                       DATE
                                                                                                                    ===================================== */

        .payment-date {
            font-size: 14px;
            font-weight: 700;
            color: #334155;
        }

        .payment-time {

            margin-top: 5px;

            font-size: 13px;

            color: #94a3b8;

            font-weight: 500;
        }

        /* =====================================
                                                                                                                       TITLE
                                                                                                                    ===================================== */

        .payment-title {

            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }

        /* =====================================
                                                                                                                       META
                                                                                                                    ===================================== */

        .payment-meta {

            font-size: 13px;

            color: #64748b;
        }

        /* =====================================
                                                                                                                       POST DATED
                                                                                                                    ===================================== */

        .post-dated-badge {

            display: inline-flex;

            align-items: center;

            padding: 5px 5px;

            border-radius: 999px;

            background: rgba(245, 158, 11, .08);

            color: #d97706;

            font-size: 12px;

            font-weight: 700;
        }

        /* =====================================
                                                                                                                       PAYMENT MODE
                                                                                                                    ===================================== */
        .payment-mode-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 60px;
            padding: 6px 5px;
            border-radius: 10px;
            background: rgba(59, 130, 246, .08);
            color: #2563eb;
            font-size: 12px;
            font-weight: 700;
        }

        /* =====================================
                                                                                                                       STATUS PILLS
                                                                                                                    ===================================== */

        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 82px;
            padding: 5px 5px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        /* SUCCESS */
        .status-success {
            background: rgba(34, 197, 94, .08);
            color: #16a34a;
        }

        /* PRIMARY */
        .status-primary {
            background: rgba(59, 130, 246, .08);
            color: #2563eb;
        }

        /* WARNING */
        .status-warning {
            background: rgba(245, 158, 11, .08);
            color: #d97706;
        }

        /* DANGER */
        .status-danger {
            background: rgba(239, 68, 68, .08);
            color: #dc2626;
        }

        /* SECONDARY */
        .status-secondary {
            background: rgba(148, 163, 184, .12);
            color: #64748b;
        }

        /* =====================================
                                                                                                                       AMOUNTS
                                                                                                                    ===================================== */

        .payment-amount {

            font-size: 15px;

            font-weight: 800;

            color: #16a34a;

            white-space: nowrap;
        }

        .remaining-amount {

            font-size: 14px;

            font-weight: 700;

            color: #475569;

            white-space: nowrap;
        }

        /* =====================================
                                                                                                                       EMPTY STATE
                                                                                                                    ===================================== */

        .payment-table .text-center {

            font-size: 14px;

            font-weight: 500;
        }

        /* =====================================
                                                                                                                       MOBILE
                                                                                                                    ===================================== */

        @media (max-width: 768px) {

            .payment-table th,
            .payment-table td {
                white-space: nowrap;
            }

        }

        /* =====================================
                                                                                                                   MINI STAT CARD
                                                                                                                ===================================== */

        .mini-stat-card {
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, .9),
                    rgba(248, 250, 252, .95));
            border: 1px solid rgba(226, 232, 240, .8);
            border-radius: 18px;
            padding: 15px 15px;
            height: 100%;
            transition: .25s ease;
            position: relative;
            overflow: hidden;
        }

        /* Hover */
        .mini-stat-card:hover {
            transform: translateY(-3px);
            box-shadow:
                0 10px 24px rgba(15, 23, 42, .06);
        }

        /* =====================================
                                                                                                                   LABEL
                                                                                                                ===================================== */

        .mini-stat-label {

            font-size: 12px;

            text-transform: uppercase;

            letter-spacing: .8px;

            color: #94a3b8;

            font-weight: 700;

            margin-bottom: 10px;
        }

        /* =====================================
                                                                                                                   VALUE
                                                                                                                ===================================== */

        .mini-stat-value {

            font-size: 22px;

            line-height: 1;

            font-weight: 800;

            color: #0f172a;
        }

        /* =====================================
                                                                                                                   BADGE
                                                                                                                ===================================== */

        .mini-stat-card .badge {

            font-size: 12px;

            font-weight: 700;

            padding: 8px 16px;
        }
    </style>

    <style>
        @media print {

            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            .no-print {
                display: none !important;
            }

            #print-area {

                position: absolute;

                left: 0;
                top: 0;

                width: 100%;

                background: #fff;
            }

            .row {

                display: flex !important;

                flex-wrap: wrap !important;
            }

            .col-md-3 {

                width: 25% !important;

                flex: 0 0 25% !important;

                max-width: 25% !important;
            }

            .col-xl-3,
            .col-md-6 {

                width: 25% !important;

                flex: 0 0 25% !important;

                max-width: 25% !important;
            }

            /* 3 COLUMN LAYOUT */
            .col-md-4 {

                width: 33.333333% !important;

                flex: 0 0 33.333333% !important;

                max-width: 33.333333% !important;
            }

            .col-lg-6 {

                width: 50% !important;

                flex: 0 0 50% !important;

                max-width: 50% !important;
            }

            .col-lg-5 {

                width: 41.66666667% !important;

                flex: 0 0 41.66666667% !important;

                max-width: 41.66666667% !important;
            }

            .col-lg-7 {

                width: 58.33333333% !important;

                flex: 0 0 58.33333333% !important;

                max-width: 58.33333333% !important;
            }

            .card {

                break-inside: auto;

                page-break-inside: auto;

                overflow: hidden;

                margin-bottom: 15px !important;
            }

            table {

                width: 100% !important;

                border-collapse: collapse !important;
            }

            tr,
            td,
            th {

              page-break-inside: auto;
            }

            * {

                -webkit-print-color-adjust: exact !important;

                print-color-adjust: exact !important;
            }

            @page {

                size: A4;

                margin: 10mm;
            }
        }
    </style>
    <style>
        .bom-header {
            background: linear-gradient(135deg, #ffffff 0%, #f4f8ff 45%, #eef7ff 100%);
            padding: 15px 15px;
            border-radius: 18px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.05), inset 0 1px 0 rgba(255, 255, 255, .6);
            transition: all .3s ease;
            position: relative;
            overflow: hidden;
        }

        .summary-box {
            background: #fff;
            border: 1px solid #eef2f7;
            border-radius: 16px;
            padding: 20px;
            height: 100%;
        }

        .summary-label {
            color: #6c757d;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 28px;
            font-weight: 700;
        }

        .meta-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .meta-value {
            font-weight: 600;
        }

        .part-wrapper {
            border: 1px solid #edf2f7;
            border-radius: 20px;
            padding: 15px;
            background: #fff;
        }

        .part-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .meta-card {
            background: #f8fafc;
            border-radius: 12px;
            padding: 14px;
        }

        .meta-card label {
            font-size: 12px;
            color: #6c757d;
            display: block;
            margin-bottom: 6px;
        }

        .bom-progress-wrapper {
            min-width: 120px;
        }

        .progress-circle {

            --size: 55px;
            --progress: 0;

            width: var(--size);
            height: var(--size);

            border-radius: 50%;

            background:
                conic-gradient(#f4b400 calc(var(--progress) * 1%),
                    rgba(255, 255, 255, 0.15) 0);

            display: flex;
            align-items: center;
            justify-content: center;

            position: relative;
        }

        .progress-circle::before {

            content: '';

            position: absolute;

            width: 42px;
            height: 42px;

            background: #fff;

            border-radius: 50%;
        }

        .progress-circle span {

            position: relative;

            z-index: 2;

            font-size: 12px;
            font-weight: 700;

            color: #111827;
        }

        .info-card {
            background: #fff;
            border: 1px solid #edf2f7;
            border-radius: 18px;
            padding: 10px;
            transition: .2s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .03);
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .06);
        }

        .info-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #94a3b8;
            margin-bottom: 10px;
        }

        .info-name {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.4;
        }

        .info-subtitle {
            font-size: 13px;
            color: #64748b;
            margin-top: 6px;
        }
    </style>
@endpush

@push('scripts')
    <script>

        function printOrderPage() {

            window.print();

        }

    </script>
@endpush