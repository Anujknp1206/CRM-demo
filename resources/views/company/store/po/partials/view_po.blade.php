<div class="row mb-2">
    <div class="col-md-6">
        <b>PO Code:</b> {{ $po->po_code }}
    </div>

    <div class="col-md-6 text-right">
        <b>Date & Time:</b> {{ \Carbon\Carbon::parse($po->po_date)->format('d/m/Y h:i A') }}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6">
        <b>Supplier:</b> {{ $po->supplier->name ?? '-' }}
    </div>

    <div class="col-md-6 text-right">
        <b>Created By:</b> {{ $po->creator->name ?? '-' }}
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <b>Status:</b>
        <span class="badge 
            @if($po->status == 'pending') badge-warning
            @elseif($po->status == 'approved') badge-success
            @else badge-info
            @endif">
            {{ ucfirst($po->status) }}
        </span>
    </div>
</div>

<hr>

{{-- ================= ITEMS ================= --}}
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <!-- <th>Specification</th> -->
                <th>Brand</th>
                <th>Condition</th>
                <th>Unit</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Amount</th>
            </tr>
        </thead>

        <tbody>
            @foreach($po->items as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->item->name ?? '-' }}</td>
                    <!-- <td>{{ $item->specification->name ?? '-' }}</td> -->
                    <td>{{ $item->brand->name ?? '-' }}</td>
                    <td>{{ $item->condition->name ?? '-' }}</td>
                    <td>{{ $item->unit->name ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->rate, 2) }}</td>
                    <td>{{ number_format($item->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- ================= TOTALS ================= --}}
<div class="row mt-3">
    <div class="col-md-6"></div>

    <div class="col-md-6">
        <table class="table table-bordered">
            <tr>
                <th>Subtotal</th>
                <td>₹ {{ number_format($po->subtotal, 2) }}</td>
            </tr>

            <tr>
                <th>Discount</th>
                <td>₹ {{ number_format($po->discount, 2) }}</td>
            </tr>

            <tr>
                <th>Tax ({{ $po->tax }}%)</th>
                <td>₹ {{ number_format($po->tax_amount, 2) }}</td>
            </tr>

            <tr>
                <th><b>Final Total</b></th>
                <td><b>₹ {{ number_format($po->final_amount, 2) }}</b></td>
            </tr>
        </table>
    </div>
</div>

{{-- ================= REMARK ================= --}}
@if($po->remark)
    <div class="mt-3">
        <b>Remark:</b>
        <div class="border p-2">
            {!! $po->remark !!}
        </div>
    </div>
@endif