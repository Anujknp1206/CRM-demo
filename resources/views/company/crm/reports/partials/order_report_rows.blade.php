@forelse($orders as $order)

    @php

        /*
        |--------------------------------------------------------------------------
        | STATUS COLORS
        |--------------------------------------------------------------------------
        */

        $statusColor = match ($order->status) {

            'confirmed' => 'success',

            'planning' => 'info',

            'in_production' => 'warning',

            'on_hold' => 'secondary',

            'delayed' => 'danger',

            'ready' => 'primary',

            'dispatched' => 'success',

            'cancelled' => 'danger',

            default => 'secondary',

        };

        /*
        |--------------------------------------------------------------------------
        | PAYMENT COLORS
        |--------------------------------------------------------------------------
        */

        $paymentColor = match ($order->payment_status) {

            'paid' => 'success',

            'partial' => 'warning',

            'unpaid' => 'danger',

            default => 'secondary',

        };

        /*
        |--------------------------------------------------------------------------
        | PROGRESS COLORS
        |--------------------------------------------------------------------------
        */

        $progressColor =
            $order->progress_percent >= 100
            ? 'success'
            : (
                $order->progress_percent > 0
                ? 'warning'
                : 'secondary'
            );

    @endphp

    {{-- MAIN ROW --}}
    <tr class="order-summary-row" style="cursor:pointer;" onclick="window.open(
                                '{{ route('company.reports.orders.details', [$company->id, $order->id]) }}',
                                '_blank'
                            )">

        {{-- ORDER --}}
        <td class="nowrap">

            <strong class="text-primary"style="font-size: 15px;">

                {{ $order->order_number }}

            </strong>

        </td>

        {{-- CUSTOMER --}}
        {{-- CUSTOMER --}}
        <td>

            <div class="font-weight-bold">
                {{ $order->customer_name }}
            </div>

            @if($order->mobile)

                <small class="text-muted nowrap" style="font-size: smaller;">

                    <i class="fa fa-phone"></i>

                    {{ optional($order->quotation?->lead?->customer)->full_primary_mobile }}

                </small>

            @endif

        </td>

        {{-- ORDER DATE --}}
        <td>

            {{ optional($order->order_date)->format('d M Y') }}

        </td>

        {{-- DELIVERY --}}
        <td>

            {{ optional($order->delivery_date)->format('d M Y') }}

        </td>

        {{-- AMOUNT --}}
        {{-- AMOUNT --}}
        <td class="nowrap">

            <strong class="text-dark">

                {{ $order->currency_symbol }}&nbsp;{{ number_format($order->final_amount, 2) }}

            </strong>

        </td>

        {{-- PAID --}}
        {{-- PAID --}}
        <td class="nowrap">

            <span class="text-success font-weight-bold">

                {{ $order->currency_symbol }}&nbsp;{{ number_format($order->paid_amount, 2) }}

            </span>

        </td>

        {{-- PAYMENT --}}
        {{-- PAYMENT --}}
        <td class="nowrap">

            <span class="badge badge-{{ $paymentColor }}">

                {{ strtoupper($order->payment_status) }}

            </span>

        </td>

        {{-- PROGRESS --}}
        <td>

            <div class="d-flex align-items-center">

                <div class="progress flex-grow-1 mr-2" style="height:8px;">

                    <div class="progress-bar bg-{{ $progressColor }}" style="width:{{ $order->progress_percent }}%">

                    </div>

                </div>

                <small class="font-weight-bold">

                    {{ $order->progress_percent }}%

                </small>

            </div>

        </td>

        {{-- STATUS --}}
        {{-- STATUS --}}
        <td class="nowrap">

            <span class="badge badge-{{ $statusColor }}">

                {{ strtoupper(str_replace('_', ' ', $order->status)) }}

            </span>

        </td>

    </tr>

@empty

    <tr>

        <td colspan="9" class="text-center text-muted py-5">

            No Orders Found

        </td>

    </tr>

@endforelse