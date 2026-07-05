@forelse($orders as $order)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td class="text-center position-relative">

            @php
                $progress = $order->progress_percent ?? 0;

                $details = nl2br(
                    $order->progress_details
                );
            @endphp
            <div class="progress-wrapper">

                <div class="progress-circle" data-progress="{{$progress}}">
                    <span>
                        {{$progress}}%
                    </span>
                </div>
                <div class="progress-tooltip">
                    {!! $details !!}
                </div>
            </div>
        </td>
        <td>

            @if($order->status === 'dispatched')

                <div class="mt-2 p-2 rounded" style="
                                    background: #e8fff1;
                                    border: 1px solid #28a745;
                                    box-shadow: 0 1px 3px rgba(0,0,0,.08);
                                ">

                    <div style="
                                        font-size: 14px;
                                        font-weight: 800;
                                        color: #28a745;
                                        line-height: 1.2;
                                        text-align:center;
                                    ">

                        Dispatched

                    </div>

                </div>

            @else

                @php
                    $daysLeft = (int) now()->startOfDay()
                        ->diffInDays(
                            \Carbon\Carbon::parse($order->delivery_date)->startOfDay(),
                            false
                        );

                    $bg = $daysLeft < 0
                        ? '#fff1f1'
                        : '#ecfff3';

                    $border = $daysLeft < 0
                        ? '#dc3545'
                        : '#28a745';

                    $text = $daysLeft < 0
                        ? '#dc3545'
                        : '#28a745';
                @endphp

                <div class="mt-2 p-2 rounded" style="
                                    background: {{ $bg }};
                                    border: 1px solid {{ $border }};
                                    box-shadow: 0 1px 3px rgba(0,0,0,.08);
                                ">

                    <div style="
                                        font-size: 14px;
                                        font-weight: 800;
                                        color: {{ $text }};
                                        line-height: 1.2;
                                        text-align:center;
                                    ">

                        @if($daysLeft < 0)

                            {{ abs($daysLeft) }} Days Late

                        @else

                            {{ $daysLeft }} Days Left

                        @endif

                    </div>

                </div>

            @endif

        </td>
        <td>{{ $order->order_number }}</td>
        <td>{{ optional($order->quotation?->lead?->customer)->name ?? '-' }}</td>
        <td>
            {{ optional($order->quotation?->lead?->customer)->full_primary_mobile ?? '-' }}
        </td>
        <!-- <td>{{ $order->creator->name ?? '-' }}</td> -->
        <td>{{ \Carbon\Carbon::parse($order->order_date)->format('d-m-Y') }}</td>
        <td>
            <span class="badge 
                                                                                        @if($order->status === 'pending') badge-secondary
                                                                                        @elseif($order->status === 'confirmed') badge-primary
                                                                                        @elseif($order->status === 'planning') badge-info
                                                                                        @elseif($order->status === 'in_production') badge-warning
                                                                                        @elseif($order->status === 'ready') badge-success
                                                                                        @elseif($order->status === 'delayed') badge-danger
                                                                                        @elseif($order->status === 'on_hold') badge-dark
                                                                                        @elseif($order->status === 'dispatched') badge-success
                                                                                        @elseif($order->status === 'cancelled') badge-danger
                                                                                        @endif">
                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
            </span>
        </td>
        <td>
            @php
                $status = $order->payment_status ?? 'pending';
            @endphp

            <span
                class="badge 
                                                                                                                                @if($status === 'unpaid') badge-danger
                                                                                                                                @elseif($status === 'partial') badge-warning
                                                                                                                                @elseif($status === 'paid') badge-success
                                                                                                                                @else badge-secondary
                                                                                                                                @endif
                                                                                                                            ">
                @if($status === 'unpaid')
                    <i class="fa fa-clock"></i> Unpaid
                @elseif($status === 'partial')
                    <i class="fa fa-adjust"></i> Partial
                @elseif($status === 'paid')
                    <i class="fa fa-check-circle"></i> Paid
                @else
                    {{ ucfirst($status) }}
                @endif
            </span>
        </td>
        <td>
            <a class="open-customer-360 btn btn-sm" data-customer-id="{{ $order->quotation->lead_id }}"
                data-customer-name="{{ $order->quotation->lead->customer->name }}"
                data-customer-mobile="{{ optional($order->quotation->lead->customer->primaryPhone)->phone }}">
                <i class="fa fa-user-circle" style="cursor: pointer;"></i>
            </a>
            @can('view order')
                <a class="open-details-modal open-order-modal" data-id="{{ $order->id }}" class="btn btn-sm"
                    title="View Order Details">
                    <i class="fa fa-search text-primary" style="cursor: pointer;"></i>
                </a>
            @endcan
            @if($order->status != 'dispatched')
                @can('edit order')
                    <a href="{{ route('orders.edit', [$company->id, $order->id]) }}" class="btn btn-sm" title="Edit Order">
                        <i class="fa fa-edit text-green"></i>
                    </a>
                @endcan
            @endif
            @can('print order')
                <a href="{{ route('orders.print', [$company->id, $order->id]) }}" target="_blank" class="btn btn-sm"
                    title="Print Order">
                    <i class="fa fa-print text-warning"></i>
                </a>
            @endcan
            @can('delete order')
                <form action="{{ route('orders.destroy', [$company->id, $order->id]) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button title="Delete Order" class="delete-confirm btn btn-sm" type="submit"
                        style="background:none;border:none;">
                        <i class="fa fa-trash text-red"></i>
                    </button>
                </form>
            @endcan
            @if(in_array($order->payment_status, ['unpaid', 'partial']))
                @can('add payment')
                    <a href="{{ route('payments.index', request()->company) }}?order={{ $order->id }}" class="btn btn-sm"
                        title="Add Payment For This Order">
                        <i class="fa fa-plus text-success"></i>
                    </a>
                @endcan
            @endif
            @unless ($order->payment_status === 'unpaid')
                @can('print proforma')
                    <a href="{{ route('orders.proforma.preview', [$company->id, $order->id]) }}" target="_blank" class="btn btn-sm"
                        title="Print Proforma Invoice">
                        <i class="fa fa-file-text-o text-info"></i>
                    </a>
                @endcan
            @endunless
            @if($order->status != 'dispatched')
                @can('add bom')
                    <a href="{{ route('bom.manage', ['company' => $company->id, 'orderId' => $order->id]) }}"
                        onclick="return handleBomClick(event, this)" data-company-id="{{ $company->id }}"
                        class="btn btn-sm text-primary" title="Create BOM">

                        <i class="fa fa-cogs mr-1"></i>
                    </a>
                @endcan
            @endif
        </td>
    </tr>
@empty
    <tr class="no-data">
        <td colspan="10" class="text-center">
            😢 No orders Created today or selected filters.
        </td>
    </tr>
@endforelse