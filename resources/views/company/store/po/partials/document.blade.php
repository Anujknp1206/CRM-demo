{{-- HEADER --}}
<div class="section-company">
    <table width="100%">
        <tr>
            <td width="40%">
                <img src="{{ asset('admin/uploads/logo/' . $settings->logo) }}" class="company-logo">
            </td>
            <td width="60%" style="text-align:right; font-size:13px; line-height:1.6">
                <strong>
                    <div>
                        +{{ $company->country->phonecode ?? '' }}- {{ $company->mobile }}

                        @if($company->alternate_mobile)
                            | +{{ $company->country->phonecode ?? '' }}- {{ $company->alternate_mobile }}
                        @endif
                    </div>

                    <div>
                        {{ $company->email }}
                        @if($company->website)
                            | {{ $company->website }}
                        @endif
                    </div>

                    <div>
                        {{ $company->address }},
                        {{ $company->city->name ?? '' }},
                        {{ $company->state->name ?? '' }},
                        {{ $company->country->name ?? '' }} -
                        {{ $company->pincode }}
                    </div>
                </strong>
                <div style="font-size:11px">   GST In: {{ $company->gstin_no }} |
                    IEC: {{ $company->iec_code }} |
                    PAN: {{ $company->pan_no }} |
                    ESTD 1966
                </div>
            </td>
        </tr>
    </table>
    <div class="header-border"></div>
</div>
{{-- DOCUMENT LABEL --}}
<div class="section-doc-label text-center doc-title">
    <h5 class="doc-label doc-quotation">PURCHASE ORDER</h5>
</div>
{{-- DOC INFO --}}
<div class="section-docinfo">
    <table class="table table-bordered docinfo-table">
        <tr>

            <th class="po-code">PO Code</th>
            <td class="po-code">{{ $po->po_code }}</td>

            <th class="po-date">Date</th>
            <td class="po-date">
                {{ \Carbon\Carbon::parse($po->po_date)->format('d/m/Y') }}
            </td>

        </tr>

        <tr>

            <th class="rfi-code">RFI Code</th>
            <td class="rfi-code">{{ $po->rfi->rfi_code ?? '-' }}</td>

            <th class="created-by">Created By</th>
            <td class="created-by">{{ $po->creator->name ?? '-' }}</td>

        </tr>
    </table>
</div>
{{-- CUSTOMER --}}
<div class="buyer-seller section-supplier">
    <table class="buyer-seller-table">

        <tr class="header-row">
            <th>Company</th>
            <th>Supplier</th>
        </tr>

        <tr>

            <!-- COMPANY -->
            <td>

                <div class="company-name"><b>{{ $company->company_name }}</b></div>

                <div class="company-address">
                    {{ $company->address }}
                </div>

                <div class="company-phone">
                    {{ $company->mobile }}
                </div>

                <div class="company-email">
                    {{ $company->email }}
                </div>

            </td>

            <!-- SUPPLIER -->
            <td>

                <div class="supplier-name">
                    <b>{{ $po->supplier->name ?? '-' }}</b>
                </div>

                <div class="supplier-email">
                    {{ $po->supplier->email ?? '-' }}
                </div>

                <div class="supplier-phone">
                    {{ $po->supplier->phone ?? '-' }}
                </div>

                <div class="supplier-address">
                    {{ $po->supplier->address ?? '-' }}
                </div>

            </td>

        </tr>

    </table>
</div>
{{-- ITEMS --}}
<div class="section-items">
    <table class="items-table">

        <thead>
            <tr>
                <th class="col-sn">S.N.</th>
                <th class="col-name">Item</th>
                <th class="col-brand">Brand</th>
                <th class="col-condition">Condition</th>
                <th class="col-unit">Unit</th>
                <th class="col-qty">Qty</th>
                <th class="col-rate">Rate</th>
                <th class="col-total">Amount</th>
            </tr>
        </thead>

        <tbody>
            @foreach($po->items as $i => $item)
                <tr class="row-{{ $i }}">

                    <td class="col-sn">{{ $i + 1 }}</td>

                    <td class="col-name">
                        {{ $item->item->name ?? '-' }}
                    </td>
                    <td class="col-brand">
                        {{ $item->brand->name ?? '-' }}
                    </td>
                    <td class="col-condition">
                        {{ $item->condition->name?? '-' }}
                    </td>
                    <td class="col-unit">
                        {{ $item->unit->name ?? '-' }}
                    </td>

                    <td class="col-qty">{{ $item->quantity }}</td>

                    <td class="col-rate">
                        {{ number_format($item->rate, 2) }}
                    </td>

                    <td class="col-total">
                        {{ number_format($item->amount, 2) }}
                    </td>

                </tr>
            @endforeach
        </tbody>

    </table>
</div>
{{-- TOTALS --}}
<div class="section-totals">
    <table class="totals-table">

        <tr class="subtotal">
            <td>Subtotal</td>
            <td>₹ {{ number_format($po->subtotal, 2) }}</td>
        </tr>

        <tr class="discount">
            <td>Discount</td>
            <td>₹ {{ number_format($po->discount, 2) }}</td>
        </tr>

        <tr class="tax-percent">
            <td>Tax (%)</td>
            <td>{{ $po->tax }}%</td>
        </tr>

        <tr class="tax-amount">
            <td>Tax Amount</td>
            <td>₹ {{ number_format($po->tax_amount, 2) }}</td>
        </tr>

        <tr class="final-row">
            <td><b>Final Total</b></td>
            <td><b>₹ {{ number_format($po->final_amount, 2) }}</b></td>
        </tr>

    </table>
</div>
{{-- TERMS / REMARK --}}
<div class="section-remark">
    <table class="terms-table">
        <tr>
            <th>Remark</th>
        </tr>
        <tr>
            <td class="remark">
                {!! $po->remark !!}
            </td>
        </tr>
    </table>
</div>