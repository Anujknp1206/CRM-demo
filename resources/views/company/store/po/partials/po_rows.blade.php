@forelse($pos as $key => $po)
    <tr>
        <td>{{ $key + 1 }}</td>

        <td>{{ $po->po_code }}</td>

        <td>{{ \Carbon\Carbon::parse($po->po_date)->format('d/m/Y') }}</td>

        <td>{{ $po->supplier->name ?? '-' }}</td>

        <td>₹ {{ number_format($po->total_amount, 2) }}</td>
        <td>
            @php
                $totalQty = $po->items->sum('quantity');
                $receivedQty = $po->items->sum('received_quantity');

                $percent = $totalQty > 0
                    ? round(($receivedQty / $totalQty) * 100)
                    : 0;
            @endphp

            <div class="progress-circle" style="--percent: {{ $percent }}">
                <span>{{ $receivedQty }}/{{ $totalQty }}</span>
            </div>
        </td>
        <td>
            <span class="badge 
                                    @if($po->status == 'pending') badge-warning
                                    @elseif($po->status == 'approved') badge-success
                                    @else badge-info
                                    @endif">
                {{ ucfirst($po->status) }}
            </span>
        </td>

        <td>
            @can('view post order')
                <button class="btn btn-sm  view-po" data-id="{{ $po->id }}">
                    <i class="fa fa-eye"></i>
                </button>
            @endcan
            @can('edit post order')

                <button class="btn btn-sm text-success edit-po" data-id="{{ $po->id }}">
                    <i class="fa fa-edit"></i>
                </button>
            @endcan

            @can('print post order')
                <a href="{{ route('pos.print', [$company->id, $po->id]) }}" target="_blank" class="btn btn-sm" title="Print PO">
                    <i class="fa fa-print text-warning"></i>
                </a>
            @endcan
            @can('stockin')
                <a href="{{ route('stock-ins.create.po', [$company->id, 'po_id' => $po->id]) }}" class="btn btn-sm text-primary"
                    title="Stock In">
                    <i class="fa fa-download"></i>
                </a>
            @endcan
        </td>
    </tr>

@empty
    <tr class="no-data-row"> {{-- 🔥 IMPORTANT --}}
        <td colspan="8" class="text-center text-muted">
            😢 No PO Found
        </td>
    </tr>
@endforelse