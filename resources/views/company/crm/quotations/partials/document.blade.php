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
    <h5 class="doc-label doc-quotation">QUOTATION</h5>
    <h5 class="doc-label doc-pi" style="display:none;">PROFORMA INVOICE</h5>
</div>
{{-- DOC INFO --}}
<div class="section-docinfo">
    <table class="table table-bordered docinfo-table ">
        <tr>
            <th id="docNumberLabel"> Quotation Number</th>
            <td id="docNumberValue" data-quote="{{ $quotation->quote_number }}" data-pi="{{ $quotation->pi_number }}">
                {{ $quotation->quote_number }}
            </td>
            <th>Date</th>
            <td id="docDateValue" data-quote="{{ optional($quotation->quote_date)->format('d/m/Y') }}"
                data-pi="{{ optional($quotation->pi_date)->format('d/m/Y') }}">
                {{ optional($quotation->quote_date)->format('d/m/Y') }}
            </td>
        </tr>
    </table>
</div>
{{-- CUSTOMER --}}
<div class="buyer-seller section-customer">
    <table class="buyer-seller-table">

        <tr class="header-row">
            <th>BUYER (Importer)</th>
            <th>SELLER (Exporter / Manufacturer)</th>
        </tr>

        <tr>
            <!-- BUYER -->
            <td valign="top" style="width: 55%;">

                <b>{{ $quotation->lead->customer->name }}</b><br>
                <div class="customer-attn">
                    Attn: {{ $quotation->contact_person }}<br>
                </div>
                @if ($quotation->lead->customer->pan)

                    PAN: {{ $quotation->lead->customer->pan ?? '' }}<br>
                @endif
                <div class="customer-gst">
                    GST: {{ $quotation->lead->customer->gst ?? '' }}
                </div>
                <div class="customer-email">
                    Email: {{ $quotation->lead->customer->email }}<br>
                </div>
                <div class="customer-phone">

                    Mobile:
                    +{{ optional($quotation->lead->customer->country)->phonecode }}
                    {{ optional($quotation->lead->customer->primaryPhone)->phone }}<br>
                </div>
                <div class="customer-address">
                    Address: {{ $quotation->lead->customer->address }}
                </div>

            </td>

            <!-- SELLER -->
            <td valign="top" style="width: 45%;">

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
                IEC Code: {{ $company->iec_code }}<br>

            </td>
        </tr>

    </table>
</div>
{{-- REMARKS (FULL WIDTH – NO TABLE) --}}
<div class="section-remarks">
    <table class="remarks-table">
        <tr class="quotation-remarks">
            <th>Special Remarks</th>
            <td class="translatable-area" data-en='{!! $quotation->special_clause!!}'
                data-hi='{!! $quotation->hi_special_clause!!}'>
                {!! $quotation->special_clause !!}
            </td>
        </tr>
        <tr class="delivery-address">
            <th>Destination</th>
            <td>
                {!! $quotation->delivery_address !!}
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
                <th class="col-desc">Description</th>
                <th class="col-qty">Qty</th>
                <th class="col-rate">Rate (<span class="currency-symbol">₹</span>)</th>
                <th class="col-total">Total (<span class="currency-symbol">₹</span>)</th>
                <th class="col-Cfv">CFV (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $i => $item)
                <tr class="row-{{ $i }}">
                    <td class="col-sn">{{ $i + 1 }}</td>
                    <td class="col-name translatable-area"
                        data-en="{{ optional($item->machine)->name ?? optional($item->component)->name ?? '' }}"
                        data-hi="{{ optional($item->machine)->hi_name ?? optional($item->component)->hi_name ?? '' }}">

                        {{ $item->machine->name ?? $item->component->name }}
                    </td>
                    <td class="col-desc translatable-area">

                        <div class="lang-en">
                            {!! $item->description !!}
                        </div>

                        <div class="lang-hi" style="display:none;">
                            {!! $item->hi_description !!}
                        </div>

                    </td>
                    <td class="col-qty">{{ $item->quantity }}</td>@php
                        $isINR = $quotation->currency === 'INR';
                    @endphp

                    <td class="col-rate">
                        {{ $isINR ? $item->unit_price : $item->converted_unit_price }}
                    </td>

                    <td class="col-total">
                        {{ $isINR ? $item->total_price : $item->converted_total_price }}
                    </td>

                    {{-- 🔥 CFV (ALWAYS INR) --}}
                    <td class="col-Cfv">
                        {{ number_format($item->total_price, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@php
    $subTotal = $quotation->total_amount;
    $taxableAmount = $subTotal - $quotation->discount;
    $taxPercent = $quotation->tax ?? 0;
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
                <span class="currency-symbol">₹</span> {{ $quotation->discount ?? 0 }}
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
                {{ $quotation->tax ?? 0 }} %
            </td>
        </tr>

        <tr class="tax-amount">
            <td class="label">Tax Amount</td>
            <td class="value">
                <span class="currency-symbol">₹</span> {{ number_format($quotation->tax_amount ?? 0, 2) }}
            </td>
        </tr>

        <tr class="final-row">
            <td class="label">Final Amount</td>
            <td class="value">
                <span class="currency-symbol">₹</span> {{ number_format($quotation->final_amount, 2) }}
            </td>
        </tr>

    </table>
</div>
{{-- TERMS --}}
<div class="section-terms">
    <table class="terms-table">

        <tbody>
            <tr>
                <th>Terms & Conditions</th>
            </tr>

            <tr>
                <td class="translatable-area" data-en='{!! $quotation->terms_conditions !!}'
                    data-hi='{!! $quotation->hi_terms_conditions!!}'>
                    {!! $quotation->terms_conditions !!}
                </td>
            </tr>
        </tbody>

    </table>
</div>