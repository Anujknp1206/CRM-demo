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

                <div style="font-size:11px">
                    GST In: {{ $company->gstin_no }} |
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
    <h5 class="doc-label doc-order">ORDER</h5>
    <h5 class="doc-label doc-po" style="display:none;">PURCHASE ORDER</h5>
</div>


{{-- DOC INFO --}}
<div class="section-docinfo">
    <table class="table table-bordered docinfo-table">
        <tr>
            <th id="docNumberLabel">Order Number</th>
            <td id="docNumberValue" data-order="{{ $order->order_number }}" data-po="{{ $order->po_number }}">
                {{ $order->order_number }}
            </td>

            <th id="docDateLabel">Order Date</th>
            <td id="docDateValue" data-order="{{ optional($order->order_date)->format('d/m/Y') }}"
                data-po="{{ optional($order->po_date)->format('d/m/Y') }}">
                {{ optional($order->order_date)->format('d/m/Y') }}
            </td>

            <th>Delivery Date</th>
            <td>
                {{ optional($order->delivery_date)->format('d/m/Y') ?? '-' }}
            </td>
        </tr>
    </table>
</div>


<div class="buyer-seller section-customer">

    <table class="buyer-seller-table">

        <tr class="header-row">
            <th>BUYER (Importer)</th>
            <th>SELLER (Exporter / Manufacturer)</th>
        </tr>

        <tr>

            <td valign="top">

                <b>{{ $order->quotation->lead->customer->name }}</b><br>
                <div class="customer-attn">
                     Attn: {{ $order->contact_person }}<br>
                </div>

                @if ($order->quotation->lead->customer->pan)
                    PAN: {{ $order->quotation->lead->customer->pan }}<br>
                @endif

                @if ($order->quotation->lead->customer->gst)
                    <div class="customer-gst">
                        GST: {{ $order->quotation->lead->customer->gst }}
                    </div>
                @endif
                Email: {{ $order->quotation->lead->customer->email }}<br>

                Mobile:
                +{{ optional($order->quotation->lead->customer->country)->phonecode }}
                {{ optional($order->quotation->lead->customer->primaryPhone)->phone }}<br>

                <div class="customer-address">
                    Address: {{ $order->quotation->lead->customer->address }}
                </div>

            </td>

            <td valign="top">

                <b>{{ $company->company_name }}</b><br>

                {{ $company->address }}<br>

                {{ $company->city->name ?? '' }},
                {{ $company->state->name ?? '' }},
                {{ $company->country->name ?? '' }} -
                {{ $company->pincode }}<br>

                Tel:
                +{{ $company->country->phonecode ?? '' }}
                {{ $company->mobile }}

                @if($company->alternate_mobile)
                    / +{{ $company->country->phonecode ?? '' }} {{ $company->alternate_mobile }}
                @endif

                <br>
                GST In: {{ $company->gstin_no }}<br>
                Email: {{ $company->email }}<br>
                Website: {{ $company->website }}<br>
                IEC Code: {{ $company->iec_code }}

            </td>

        </tr>

    </table>

</div>
<div class="section-remarks">

    <table class="remarks-table">

        <tr class="quotation-remarks">
            @if ($order->remark)
                <th>Remarks</th>
                <td class="translatable-area" data-en='{!! $order->remark!!}' data-hi='{!! $order->hi_remark!!}'>
                    {!! $order->remark !!}
                </td>
            @endif
        </tr>

        <tr class="delivery-address">
            @if ($order->delivery_address)
                <th>Delivery Address</th>
                <td>{!! $order->delivery_address !!}</td>
            @endif
        </tr>

    </table>

</div>
<div class="section-items">
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-sn">S.N.</th>
                <th class="col-name">Item</th>
                <th class="col-desc ">Description</th>
                <th class="col-qty">Qty</th>
                <th class="col-rate">Rate (<span class="currency-symbol">₹</span>)</th>
                <th class="col-total">Total (<span class="currency-symbol">₹</span>)</th>
                <th class="col-Cfv">CFV (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $i => $item)
                    <tr class="row-{{ $i }}">
                        <td class="col-sn">{{ $i + 1 }}</td>
                        <td class="col-name translatable-area"
                            data-en="{{ optional($item->machine)->name ?? optional($item->component)->name ?? '' }}"
                            data-hi="{{ optional($item->machine)->hi_name ?? optional($item->component)->hi_name ?? '' }}">
                            {{ optional($item->machine)->name ?? optional($item->component)->name ?? '' }}
                        </td>
                        <td class="col-desc">

                            <div class="lang-en">
                                {!! $item->description !!}
                            </div>

                            <div class="lang-hi" style="display:none">
                                {!! $item->hi_description !!}
                            </div>

                        </td>
                        <td class="col-qty">{{ $item->quantity }}</td>
                        @php
                            $isINR = $order->currency === 'INR';
                            $rate = $order->conversion_rate ?? 1;
                        @endphp
                        <td class="col-rate">
                            {{ $isINR
                ? number_format($item->unit_price, 2)
                : number_format($item->unit_price, 2) }}
                        </td>
                        <td class="col-total">
                            {{ number_format($item->total_price, 2) }}
                        </td>
                        <td class="col-Cfv"> {{ $isINR ? $item->total_price : $item->converted_total_price }}</td>
                    </tr>
            @endforeach
        </tbody>
    </table>
</div>
@php
    $subTotal = $order->total_amount;
    $taxableAmount = $subTotal - $order->discount;
    $taxPercent = $order->tax ?? 0;
    $taxAmount = ($taxableAmount * $taxPercent) / 100;
    $grandTotal = $taxableAmount + $taxAmount;
@endphp
{{-- TOTALS --}}
<div class="section-totals">
    <table class="totals-table">
        <tr class="subtotal">
            <td class="label">Sub Total</td>
            <td class="value">
                <span class="currency-symbol">₹</span>{{ number_format($subTotal, 2) }}
            </td>
        </tr>

        <tr class="discount">
            <td class="label">Discount</td>
            <td class="value">
                <span class="currency-symbol">₹</span> {{ $order->discount ?? 0 }}
            </td>
        </tr>
        <tr class="taxable-amount">
            <td class="label"><strong>Taxable Amount</strong></td>
            <td class="value">
                <span class="currency-symbol">₹</span> {{ number_format($taxableAmount, 2) }}
            </td>
        </tr>
        <tr class="tax-percent">
            <td class="label">Tax (%)</td>
            <td class="value">
                {{ $order->tax ?? 0 }} %
            </td>
        </tr>

        <tr class="tax-amount">
            <td class="label">Tax Amount</td>
            <td class="value">
                <span class="currency-symbol">₹</span> {{ number_format($order->tax_amount ?? 0, 2) }}
            </td>
        </tr>

        <tr class="final-row">
            <td class="label">Final Amount</td>
            <td class="value">
                <span class="currency-symbol">₹</span> {{ number_format($order->final_amount, 2) }}
            </td>
        </tr>

    </table>
</div>

{{-- TERMS --}}
{{-- TERMS --}}
<div class="section-terms">

    <table class="terms-table">

        <tbody>

            <tr>
                <th>Terms & Conditions</th>
            </tr>

            <tr>
                <td class="translatable-area" data-en='{!! $order->terms_conditions !!}'
                    data-hi='{!! $order->hi_terms_conditions!!}'>
                    {!! $order->terms_conditions !!}
                </td>
            </tr>

        </tbody>

    </table>

</div>