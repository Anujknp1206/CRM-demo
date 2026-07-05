@if($payments->count() > 0)
    @foreach($payments as $i => $p)
        @php
            $paidTillNow = $p->order
                ->payments()
                ->where('id', '<=', $p->id)
                ->sum('amount');

            $remainingAfter = $p->order->final_amount - $paidTillNow;
        @endphp
        <tr id="payment-row-{{ $p->id }}">
            <td>{{ $i + 1 }}</td>
            <td>{{ $p->payment_number }}</td>
            <td>{{ $p->order->order_number }}</td>

            @php
                $name = $p->order->quotation?->lead?->customer->name ?? '-';
                $words = explode(' ', $name);
                $shortName = count($words) > 2
                    ? $words[0] . ' ' . $words[1] . '...'
                    : $name;
            @endphp

            <td>
                {{ $shortName }}
                <br>
                <small>{{ $p->order->quotation?->lead?->customer->email ?? '-' }}</small>
            </td>
            <td>{{ $p->order->currency_symbol }}{{ number_format($p->order->final_amount, 2) }}</td>
            <td>{{ $p->order->currency_symbol }}{{ number_format($p->amount, 2) }}</td>
            <td>{{ $p->order->currency_symbol }}{{ number_format($remainingAfter, 2) }}</td>

            <td>{{ ucfirst(str_replace('_', ' ', $p->payment_mode)) }}</td>
            <td>{{ $p->payment_date->format('d/m/Y') }}</td>
            <td>
                <span class="badge
                                                {{ $p->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                    {{ ucfirst($p->status) }}
                </span>
            </td>
            <td>
                @can('view payment')
                    <button class="btn btn-sm  open-payment-modal" title="View Payment" data-id="{{ $p->id }}">
                        <i class="fa fa-search"></i>
                    </button>
                @endcan
                @can('edit payment')
                    <button class="btn btn-sm text-success edit-payment" data-id="{{ $p->id }}" title="Edit Payment">
                        <i class="fa fa-edit"></i>
                    </button>
                @endcan
                @can('delete payment')
                    <button class="btn btn-sm text-danger delete-payment" data-id="{{ $p->id }}" title="Delete Payment">
                        <i class="fa fa-trash"></i>
                    </button>
                @endcan

                @can('print payment')
                    @if($p->status === 'completed')
                        {{-- FULL RECEIPT PRINT --}}
                        <!-- <a target="_blank" href="{{ route('payments.print.full', [
                                    'company' => $company->id,
                                    'order' => $p->order->id
                                ]) }}" class="btn btn-sm text-primary" title="Print Full Payment">
                            <i class="fa fa-print"></i>
                        </a> -->
                        {{-- FULL RECEIPT PDF --}}
                        <a target="_blank" href="{{ route('payments.pdf.full', [
                                    'company' => $company->id,
                                    'order' => $p->order->id,
                                    'payment' => $p->id
                                ]) }}" class="btn btn-sm text-danger" title="Download Full Payment PDF">
                            <i class="fa fa-file-pdf-o"></i>
                        </a>
                    @else
                        {{-- PARTIAL PAYMENT SLIP --}}
                        <!-- <a target="_blank" href="{{ route('payments.print.single', [
                                    'company' => $company->id,
                                    'order' => $p->order->id,
                                    'payment' => $p->id
                                ]) }}" class="btn btn-sm text-info" title="Print Payment">
                            <i class="fa fa-print"></i>
                        </a> -->
                        <a target="_blank" href="{{ route('payments.pdf.single', [
                                    'company' => $company->id,
                                    'order' => $p->order->id,
                                    'payment' => $p->id
                                ]) }}" class="btn btn-sm text-danger" title="Download PDF">
                            <i class="fa fa-file-pdf-o"></i>
                        </a>
                    @endif
                @endcan
            </td>
        </tr>

    @endforeach
@else
    <tr class="no-data">
        <td colspan="11" class="text-center">
            @if($isDefaultToday)
                😢 No payment made today
            @else
                😢 No payment found for selected filters
            @endif
        </td>
    </tr>
@endif