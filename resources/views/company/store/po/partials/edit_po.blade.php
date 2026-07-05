<form id="updatePoForm">
    @csrf

    <input type="hidden" name="po_id" value="{{ $po->id }}">

    {{-- ================= HEADER ================= --}}
    <div class="row mb-2">
        <div class="col-md-6">
            <b>RFI Code:</b> {{ $po->rfi->rfi_code ?? '-' }}
        </div>

        <div class="col-md-6 text-right">
            <b>PO Code:</b> {{ $po->po_code }}
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <b>Date & Time:</b>
            {{ \Carbon\Carbon::parse($po->po_date)->format('d/m/Y h:i A') }}
        </div>

        <div class="col-md-6 text-right">
            <b>Supplier:</b> {{ $po->supplier->name ?? '-' }}
        </div>
    </div>

    {{-- ================= ITEMS ================= --}}
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="10%">Item</th>
                    <th width="10%">Brand</th>
                    <th width="10%">Condition</th>
                    <th width="10%">Unit</th>
                    <th width="10%">RFI Qty</th>
                    <th width="10%">PO Qty</th>
                    <th width="10%">Rate</th>
                    <th width="10%">Amount</th>
                </tr>
            </thead>

            <tbody>
                @foreach($po->items as $key => $item)
                    @php
                        $rfiItem = $po->rfi->items->firstWhere('item_id', $item->item_id);
                    @endphp

                    <tr>
                        <td>{{ $key + 1 }}</td>

                        <td>{{ $item->item->name ?? '-' }}</td>
                        <td>{{ $item->brand->name ?? '-' }}</td>
                        <td>{{ $item->condition->name ?? '-' }}</td>
                        <td>{{ $item->unit->name ?? '-' }}</td>

                        <td>{{ $rfiItem->requested_quantity ?? 0 }}</td>

                        <td>
                            <input type="number" name="items[{{ $item->id }}][quantity]" value="{{ $item->quantity }}"oninput="if(this.value < 0) this.value = 1;"
                                class="form-control qty">
                        </td>

                        <td>
                            <input type="number" name="items[{{ $item->id }}][rate]" value="{{ $item->rate }}"oninput="if(this.value < 0) this.value = 1;"
                                class="form-control rate">
                        </td>

                        <td>
                            <input type="text" value="{{ $item->amount }}" class="form-control amount" readonly>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ================= TOTALS ================= --}}
    <div class="row mt-3">

        <div class="col">
            <label>Subtotal</label>
            <input type="text" id="subtotal" name="subtotal" class="form-control" value="{{ $po->subtotal }}" readonly>
        </div>

        <div class="col">
            <label>Discount</label>
            <input type="number" id="discount" name="discount" class="form-control" value="{{ $po->discount }}"oninput="if(this.value < 0) this.value = 1;">
        </div>

        <div class="col">
            <label>Tax (%)</label>
            <input type="number" id="tax" name="tax" class="form-control" value="{{ $po->tax }}"oninput="if(this.value < 0) this.value = 1;">
        </div>

        <div class="col">
            <label>Tax Amount</label>
            <input type="text" id="tax_amount" name="tax_amount" class="form-control" value="{{ $po->tax_amount }}"
                readonly>
        </div>

        <div class="col">
            <label><b>Final Total</b></label>
            <input type="text" id="final_total" name="final_amount" class="form-control" value="{{ $po->final_amount }}"
                readonly>
        </div>

    </div>

    {{-- ================= REMARK (SUMMERNOTE) ================= --}}
    <div class="mt-3">
        <label>Remark</label>
        <textarea id="remark" name="remark" class="form-control ">{!! $po->remark !!}</textarea>
    </div>

    <div class="text-right mt-3">
        <button type="submit" class="btn btn-success">
            Update PO
        </button>
    </div>
</form>