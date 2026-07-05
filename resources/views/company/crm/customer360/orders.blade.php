@if(!$hasOrder)
    <div class="text-center text-muted py-4">
        No orders found
        <br><br>

        @if(isset($quotation))
            @can('add order')
                <a href="{{ route('orders.create', request()->company) }}?quotation={{ $quotation->id }}"
                    class="btn btn-sm btn-success">
                    <i class="fa fa-plus"></i> Create Order
                </a>
            @endcan
        @else
            <span class="text-danger small">
                ⚠ Create quotation before order
            </span>
        @endif
    </div>
@else
    <table class="table table-sm table-bordered">
        <thead class="bg-light">
            <tr>
                <th>Order No</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Payment Status</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->order_date?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $order->currency_symbol ?? '' }}{{ number_format($order->final_amount, 2) }}</td>
                    <td>
                        <span class="badge
                                                                                                                            @if($order->status === 'pending')
                                                                                                                                badge-warning
                                                                                                                            @elseif($order->status === 'confirmed')
                                                                                                                                badge-success
                                                                                                                            @else
                                                                                                                                badge-secondary
                                                                                                                            @endif
                                                                                                                        ">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>
                        @php
                            $status = $order->payment_status ?? 'pending';
                        @endphp

                        <span class="badge 
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

                        @if(in_array($order->payment_status, ['unpaid', 'partial']))
                            @can('add payment')
                                <a href="{{ route('payments.index', request()->company) }}?order={{ $order->id }}" class="btn btn-sm"
                                    title="Add Payment For This Order">
                                    <i class="fa fa-plus"></i>
                                </a>
                            @endcan
                        @endif
                        @unless ($order->payment_status === 'unpaid')
                            @can('print proforma')
                                <a href="{{ route('orders.proforma.preview', [$company->id, $order->id]) }}" target="_blank"
                                    class="btn btn-sm" title="Print Proforma Invoice">
                                    <i class="fa fa-file-pdf-o text-danger"></i>
                                </a>
                            @endcan
                        @endunless
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif