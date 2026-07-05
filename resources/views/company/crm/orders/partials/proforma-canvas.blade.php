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
<div class="section-doc-label text-center doc-title doc-title-text">
    <h3>Proforma Invoice</h3>
</div>
{{-- ================= TITLE ================= --}}
<div class="section-docinfo">
    <table class="table table-bordered docinfo-table">

        {{-- ROW 1: PROFORMA --}}
        <tr class="doc-pi">
            <th class="pi-number">PI Number</th>
            <td class="pi-number">
                {{ $order->quotation->pi_number ?? '-' }}
            </td>

            <th class="pi-date">PI Date</th>
            <td class="pi-date">
                {{ optional($order->quotation->pi_date)->format('d/m/Y') ?? '-' }}
            </td>
        </tr>
        <tr class="doc-po">
            <th>PO Number</th>
            <td>{{ $order->po_number ?? '-' }}</td>

            <th>PO Date</th>
            <td>{{ optional($order->po_date)->format('d/m/Y') ?? '-' }}</td>
        </tr>
        {{-- ROW 2: ORDER --}}
        <tr>
            <th class="order-number">Order Number</th>
            <td class="order-number">
                {{ $order->order_number ?? '-' }}
            </td>

            <th class="order-date">Order Date</th>
            <td class="order-date">
                {{ optional($order->order_date)->format('d/m/Y') ?? '-' }}
            </td>
        </tr>

        {{-- ROW 3: QUOTATION --}}
        <tr>
            <th class="quote-number">Quotation Number</th>
            <td class="quote-number">
                {{ $order->quotation->quote_number ?? '-' }}
            </td>

            <th class="quote-date">Quotation Date</th>
            <td class="quote-date">
                {{ optional($order->quotation->quote_date)->format('d/m/Y') ?? '-' }}
            </td>
        </tr>

    </table>
</div>

<!-- ================= BUYER / SELLER ================= -->

<div class="buyer-seller section-customer" style="margin-bottom: 10px;">

    <table class="buyer-seller-table">

        <tr class="header-row">
            <th>BUYER (Importer)</th>
            <th>SELLER (Exporter / Manufacturer)</th>
        </tr>

        <tr>
            <td valign="top">

                <b>{{ $order->quotation->lead->customer->name }}</b><br>

                @if($order->contact_person)
                    Attn: {{ $order->contact_person }}<br>
                @endif

                {{-- GST --}}
                @if($order->quotation->lead->customer->gst)
                    <span class="customer-gst">
                        GST: {{ $order->quotation->lead->customer->gst }}<br>
                    </span>
                @endif

                {{-- PAN --}}
                @if($order->quotation->lead->customer->pan)
                    <span class="customer-pan">
                        PAN: {{ $order->quotation->lead->customer->pan }}<br>
                    </span>
                @endif

                {{-- EMAIL --}}
                <span class="customer-email">
                    Email: {{ $order->quotation->lead->customer->email ?? '-' }}<br>
                </span>

                {{-- MOBILE --}}
                <span class="customer-mobile">
                    Mobile:
                    +{{ optional($order->quotation->lead->customer->country)->phonecode }}
                    {{ optional($order->quotation->lead->customer->primaryPhone)->phone ?? '-' }}
                    <br>
                </span>

                {{-- ADDRESS --}}
                <span class="customer-address">
                    Address:
                    {{ $order->quotation->lead->customer->address ?? '-' }},
                    {{ optional($order->quotation->lead->customer->city)->name ?? '' }},
                    {{ optional($order->quotation->lead->customer->state)->name ?? '' }},
                    {{ optional($order->quotation->lead->customer->country)->name ?? '' }}
                </span>

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
<!-- ================= ORDER ITEMS ================= -->

<div class="section-items">

    <table class="items-table">
        <thead>
            <tr>
                <th style="width:5%">S.N.</th>
                <th style="width:20%">Item</th>
                <th style="width:35%">Description</th>
                <th class="center" style="width:10%">Qty</th>
                <th class="right" style="width:15%">Rate (<span class="currency-symbol">{{ $currencySymbol }}</span>)
                </th>
                <th class="right" style="width:15%">Total (<span class="currency-symbol">{{ $currencySymbol }}</span>)
                </th>
                <th class="Cfv">CFV (₹)</th>
            </tr>
        </thead>

        <tbody>

            @foreach($order->items as $i => $item)

                <tr>

                    <td class="center">{{ $i + 1 }}</td>

                    <td>
                        {{ $item->machine->name ?? $item->component->name ?? 'Item' }}
                    </td>

                    <td>
                        {!!$item->description ?? '-' !!}
                    </td>

                    <td class="center">
                        {{ $item->quantity }}
                    </td>
                    @php
                        $isINR = $order->currency === 'INR';
                        $rate = $order->conversion_rate ?? 1;
                    @endphp
                    <td class="right">
                        {{ number_format($item->unit_price, 2) }}
                    </td>

                    <td class="right">
                        {{ number_format($item->total_price, 2) }}
                    </td>
                    <td class="Cfv"> {{ $isINR ? $item->total_price : $item->converted_total_price }}</td>
                </tr>

            @endforeach

        </tbody>

    </table>
    {{-- ================= ORDER TOTALS ================= --}}


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
                <span class="currency-symbol"><span
                        class="currency-symbol">{{ $currencySymbol }}</span></span>{{ number_format($subTotal, 2) }}
            </td>
        </tr>

        <tr class="discount">
            <td class="label">Discount</td>
            <td class="value">
                <span class="currency-symbol"><span class="currency-symbol">{{ $currencySymbol }}</span></span>
                {{ number_format($order->discount ?? 0, 2) }}
            </td>
        </tr>

        <tr class="taxable-amount">
            <td class="label"><strong>Taxable Amount</strong></td>
            <td class="value">
                <span class="currency-symbol"><span class="currency-symbol">{{ $currencySymbol }}</span></span>
                {{ number_format($taxableAmount, 2) }}
            </td>
        </tr>

        <tr class="tax-percent">
            <td class="label">Tax (%)</td>
            <td class="value">
                {{ $taxPercent }} %
            </td>
        </tr>

        <tr class="tax-amount">
            <td class="label">Tax Amount</td>
            <td class="value">
                <span class="currency-symbol"><span class="currency-symbol">{{ $currencySymbol }}</span></span>
                {{ number_format($taxAmount, 2) }}
            </td>
        </tr>

        <tr class="final-row">
            <td class="label">Final Amount</td>
            <td class="value">
                <span class="currency-symbol"><span class="currency-symbol">{{ $currencySymbol }}</span></span>
                {{ number_format($grandTotal, 2) }}
            </td>
        </tr>

    </table>
</div>
<div class="section-payments">

    <table class="items-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Payment No</th>
                <th>Date</th>
                <th>Mode</th>
                @php
                    $hasReference = $payments->contains(function ($pay) {
                        return !empty($pay->transaction_reference);
                    });
                @endphp
                @if($hasReference)
                    <th>Reference</th>
                @endif
                <th class="right">Amount (<span class="currency-symbol">{{ $currencySymbol }}</span>)</th>

            </tr>

        </thead>

        <tbody>

            @php
                $totalPaid = 0;
            @endphp

            @foreach($payments as $i => $p)

                @php
                    $totalPaid += $p->amount;
                @endphp

                <tr>

                    <td class="center">{{ $i + 1 }}</td>

                    <td>{{ $p->payment_number }}</td>

                    <td>{{ $p->payment_date->format('d-m-Y') }}</td>

                    <td>
                        {{ ucfirst(str_replace('_', ' ', $p->payment_mode)) }}
                    </td>
                    @if ($p->transaction_reference)
                        <td>
                            {{ $p->transaction_reference ?? '-' }}
                    </td>@endif

                    <td class="right">
                        <span class="currency-symbol">{{ $currencySymbol }}</span>{{ number_format($p->amount, 2) }}
                    </td>
                </tr>
                @php
                    $remaining = $order->final_amount - $totalPaid;
                    $postDatePayment = $payments->firstWhere('is_post_dated', true);
                @endphp

                @if($postDatePayment && $postDatePayment->post_date)
                    <tr class="post-summary post-date">
                        <td colspan="{{ $hasReference ? 6 : 5 }}">

                            <div class="post-box">
                                <div class="post-left">
                                    <strong>Post Date:</strong>
                                    {{ \Carbon\Carbon::parse($postDatePayment->post_date)->format('d-m-Y') }}
                                </div>

                                <div class="post-right">
                                    <strong>Remaining:</strong>
                                    <span class="currency-symbol">{{ $currencySymbol }}</span>
                                    {{ number_format($remaining, 2) }}
                                </div>
                            </div>

                        </td>
                    </tr>
                @endif
            @endforeach

        </tbody>

    </table>
</div>
<div class="section-paymenttotals">

    <table class="totals-table">

        <tr class="total-received">
            <td class="label"><b>Total Received</b></td>
            <td class="value">
                <b><span class="currency-symbol"><span class="currency-symbol">{{ $currencySymbol }}</span></span>
                    {{ number_format($totalPaid, 2) }}</b>
            </td>
        </tr>

        <tr class="order-amount">
            <td class="label"><b>Order Amount</b></td>
            <td class="value">
                <b><span class="currency-symbol"><span class="currency-symbol">{{ $currencySymbol }}</span></span>
                    {{ number_format($order->final_amount, 2) }}</b>
            </td>
        </tr>

        <tr class="remaining">
            <td class="label"><b>Remaining</b></td>
            <td class="value">
                <b>
                    <span class="currency-symbol"><span class="currency-symbol">{{ $currencySymbol }}</span></span>
                    {{ number_format($order->final_amount - $totalPaid, 2) }}
                </b>
            </td>
        </tr>

    </table>

</div>

@if($payments->last() && $payments->last()->note)
    <div class="receipt-note">
        {!! $payments->last()->note !!}
    </div>
@endif
<div class="section-sign sign-block">

    <div class="sign-title">
        For {{ $company->company_name }}
    </div>

    <br><br>

    <b> R.R. Khare (Rishabh Rai Khare)</b><br>

    {{ $company->designation ?? 'Partner' }}<br>

    Date: {{ now()->format('d-m-Y') }}

</div>
<div class="print-info">
    This is a computer-generated receipt and is valid without a physical signature.
    Authorised signatory stamp and signature to be affixed on original copy.
</div>