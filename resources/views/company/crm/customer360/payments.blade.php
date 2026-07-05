@if($data->isEmpty())
    <div class="text-center text-muted py-4">
        No payments found
        <br><br>

        @if(isset($order))
            @can('add payment')
                <a href="{{ route('payments.index', request()->company) }}?order={{ $order->id }}" class="btn btn-sm btn-info">
                    <i class="fa fa-plus"></i> Add Payment
                </a>
            @endcan
        @else
            <span class="text-danger small">
                ⚠ Create order before adding payment
            </span>
        @endif
    </div>
@else
    <table class="table table-sm table-bordered">
        <thead class="bg-light">
            <tr>
                <th>Payment No</th>
                <th>Date</th>
                <th>Post Date</th>
                <th>Mode</th>
                <th>Amount</th>
                <th>Est. Amount (CFV)</th>
                <th>Reference</th>
                <th>Status</th>
                <th>Print</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $p)
                <tr>
                    <td>{{ $p->payment_number }}</td>
                    <td>{{ $p->payment_date->format('d/m/Y') }}</td>
                    <td>
                        @if($p->is_post_dated && $p->post_date)
                            <span class="text-danger">
                                {{ $p->post_date->format('d/m/Y') }}
                            </span>
                        @else
                            ---
                        @endif
                    </td>
                    <td>{{ ucfirst(str_replace('_', ' ', $p->payment_mode)) }}</td>
                    <td>{{ $p->order->currency_symbol ?? '' }} {{ number_format($p->amount, 2) }}</td>
                    <td>
                        ₹ {{ number_format($p->amount * ($p->order->conversion_rate ?? 1), 2) }}
                    </td>
                    <td>{{ $p->transaction_reference ?? '—' }}</td>
                    <td>
                        @if($p->status === 'completed')
                            <span class="badge badge-success">Completed</span>
                        @else
                            <span class="badge badge-warning">Pending</span>
                        @endif
                    </td>

                    <td>
                        @can('view payment')
                            <button class="btn btn-sm  open-payment-modal" title="View Payment" data-id="{{ $p->id }}">
                                <i class="fa fa-search text-primary" style="cursor: pointer;"></i>
                            </button>
                        @endcan

                        @can('edit payment')
                            <a href="{{ route('payments.index', ['company' => request()->company, 'payment' => $p->id]) }}"
                                class="btn btn-sm" title="Edit Payment">

                                <i class="fa fa-edit"></i>

                            </a>
                        @endcan
                        @can('print payment')
                            @if($p->status === 'completed')

                                {{-- FULL RECEIPT PRINT --}}
                                <a target="_blank" href="{{ route('payments.print.full', [
                                    'company' => $company->id,
                                    'order' => $p->order->id
                                ]) }}" class="btn btn-sm text-primary" title="Print Full Payment">
                                    <i class="fa fa-print"></i>
                                </a>

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
                                <a target="_blank" href="{{ route('payments.print.single', [
                                    'company' => $company->id,
                                    'order' => $p->order->id,
                                    'payment' => $p->id
                                ]) }}" class="btn btn-sm text-info" title="Print Payment">
                                    <i class="fa fa-print"></i>
                                </a>
                                <a target="_blank" href="{{ route('payments.pdf.single', [
                                    'company' => $company->id,
                                    'order' => $p->order->id,
                                    'payment' => $p->id
                                ]) }}" class="btn btn-sm text-danger" title="Download PDF">
                                    <i class="fa fa-file-pdf-o"></i>
                                </a>
                            @endif
                        @endcan
                        @if(in_array($p->status, ['unpaid', 'partial']))
                            @can('add payment')
                                <a href="{{ route('payments.index', request()->company) }}?order={{ $p->order->id }}" class="btn btn-sm"
                                    title="Add Payment For This Order">
                                    <i class="fa fa-plus"></i>
                                </a>
                            @endcan
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif