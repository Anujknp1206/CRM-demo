<!DOCTYPE html>
<html>
@php
    function hasSection($sections, $key)
    {
        return in_array($key, $sections ?? []);
    }

    function hasExtra($extras, $key)
    {
        return in_array($key, $extras ?? []);
    }
@endphp

<head>
    <title>Proforma Invoice</title>

    <style>
        @page {
            margin: 100px 40px 25px 40px;
        }

        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ storage_path('fonts/DejaVuSans.ttf') }}') format('truetype');
        }

        @font-face {
            font-family: 'NotoDevanagari';
            src: url('{{ storage_path('fonts/NotoSansDevanagari-Bold.ttf') }}') format('truetype');
            font-weight: bold;
        }

        @font-face {
            font-family: 'NotoDevanagari';
            src: url('{{ storage_path('fonts/NotoSansDevanagari-Regular.ttf') }}') format('truetype');
        }

        .hindi {
            font-family: 'NotoDevanagari', DejaVu Sans, sans-serif;
        }

        /* ===============================
BASE
=============================== */

        body {
            font-family: 'NotoDevanagari', DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
        }

        .text-right {
            text-align: right;
        }

        .doc-title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            color: #1b3a6b;
            background: #e9e9e9;
            padding: 6px;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .table-header {
            background: #e9e9e9;
            color: #1b3a6b;
            font-weight: bold;
        }

        /* ===============================
TABLE
=============================== */

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #cfcfcf;
            padding: 6px;
            vertical-align: top;
        }

        /* ===============================
HEADER
=============================== */

        .no-border td {
            border: none;
            color: #2b4c7e;
            padding: 2px 4px;
        }

        /* ===============================
TITLE
=============================== */

        h3 {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1px;
            margin: 0;
        }

        /* ===============================
SECTION SPACING
=============================== */

        .section {
            margin-bottom: 10px;
            line-height: 1.2;
        }

        /* ===============================
DOC INFO TABLE
=============================== */

        .section table th {
            background: #f1f1f1;
            color: #2b4c7e;
            font-weight: 600;
        }

        /* ===============================
BUYER SELLER TABLE
=============================== */

        .buyer-seller th {
            background: #2b4c7e;
            color: #fff;
            font-weight: 600;
        }

        .buyer-seller td {
            line-height: 1.2;
        }

        /* ===============================
REMARKS TABLE
=============================== */

        .remarks-table th {
            width: 25%;
            background: #f3f3f3;
            font-weight: 600;
        }

        .remarks-table td {
            word-break: break-word;
        }

        /* ===============================
ITEMS TABLE
=============================== */

        .items-table th {
            background: #2b4c7e;
            color: #fff;
            font-weight: 600;
            text-align: left;
        }

        .items-table td {
            word-break: break-word;
        }

        .col-sn {
            text-align: center;
        }

        .col-qty {
            text-align: center;
        }

        .col-desc {
            width: 50%;
        }


        .col-rate,
        .col-total {
            text-align: right;
        }

        /* ===============================
TOTALS BOX
=============================== */

        .totals {
            width: 320px;
            margin-left: auto;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .totals td {
            border: 1px solid #aaa;
        }

        .totals .label {
            text-align: right;
            font-weight: 500;
        }

        .totals .value {
            text-align: right;
        }

        .totals tr:last-child td {
            background: #2b4c7e;
            color: #fff;
            font-weight: bold;
        }

        /* ===============================
TERMS
=============================== */

        .terms-table th {
            background: #2b4c7e;
            color: #fff;
            text-align: left;
        }

        .terms-table td {
            line-height: 1.6;
        }

        /* ===============================
WORD WRAP FIX
=============================== */

        td {
            word-wrap: break-word;
            word-break: break-word;
        }

        /* ===============================
PAGE BREAK FIX
=============================== */

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: auto;
        }

        /* ===============================
TERMS & CONDITIONS BLOCK
=============================== */


        .terms-box {
            width: 100%;
            border: 1px solid #cfcfcf;
            border-collapse: collapse;
        }

        .terms-box th {
            background: #2b4c7e;
            color: #fff;
            text-align: left;
            font-weight: bold;
            padding: 7px 10px;
            border: 1px solid #2b4c7e;
        }

        .terms-box td {
            padding: 0;
            border: 1px solid #cfcfcf;
        }

        .terms-content {
            background: #f2f2f2;
            padding: 12px;
            line-height: 1.7;
            font-size: 11px;
        }

        .terms-content p {
            margin: 4px 0;
        }
   .pdf-footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 40px;
        }

        .pdf-footer table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-left {
            background: #2b4c7e;
            color: #fff;
            font-size: 9px;
            padding: 8px 12px;
        }

        .footer-right {
            background: #2b4c7e;
            color: #fff;
            font-size: 10px;
            text-align: right;
            padding: 8px 12px;
            width: 80px;
        }

        .pdf-footer table,
        .pdf-footer td,
        .pdf-footer tr {
            border: none !important;
        }

        /* ===================================== */
        /* RECEIPT NOTE */
        /* ===================================== */

        .receipt-note {
            margin-top: 15px;
            border: 2px solid #2cca38;
            padding: 5px;
            background: #f6fff6;
            line-height: 1.2;
        }

        /* ===================================== */
        /* SIGN BLOCK */
        /* ===================================== */

        .sign-block {
            margin-top: 25px;
            border: 1px solid #ccc;
            page-break-inside: auto;
            background: #f5f5f5;
            padding: 15px;
        }

        .sign-title {
            font-weight: 600;
            color: #1b3a6b;
        }

        /* ===================================== */
        /* PRINT NOTE */
        /* ===================================== */

        .print-info {
            font-size: 9px;
            text-align: center;
            margin-top: 10px;
            font-style: italic;
        }

        .totals-table {
            width: 320px;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals-table td {
            border: 1px solid #aaa;
            padding: 6px 8px;
        }

        .totals-table .label {
            text-align: right;
            font-weight: 500;
        }

        .totals-table .value {
            text-align: right;
        }

        .totals-table .final-row td {
            background: #2b4c7e;
            color: #fff;
            font-weight: bold;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .col-desc p,
        .col-desc ul,
        .col-desc ol {
            margin: 0;
            padding: 0;
        }

        .col-desc li {
            margin: 0;
            padding: 0;
        }

        .desc-content {
            line-height: 1.2;
        }

        .desc-content p {
            margin: 2px 0;
        }

        .desc-content br {
            line-height: 1;
        }

        .items-table thead {
            display: table-header-group;
        }


        .items-table tbody {
            page-break-inside: auto;
        }

        .items-table tr {
            page-break-inside: auto;
        }

        .items-table td,
        .items-table th {
            vertical-align: top;
        }

        .col-desc {
            width: 50%;
            line-height: 1.5;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .desc-content {
            page-break-inside: auto;
            white-space: normal;
        }

        /* RIGHT SIDE BOX */
        .totals-box {
            width: 280px;
            border-collapse: collapse;
            font-size: 11px;
            margin-left: auto;
        }

        /* NORMAL ROW */
        .totals-box td {
            border: 1px solid #cfcfcf;
            padding: 6px 10px;
        }

        /* LABEL COLUMN */
        .totals-box .label {
            text-align: right;
            font-weight: 600;
            background: #f5f5f5;
        }

        /* VALUE COLUMN */
        .totals-box .value {
            text-align: right;
            width: 110px;
        }

        /* FINAL ROW (BLUE) */
        .totals-box .final td {
            background: #2b4c7e;
            color: #fff;
            font-weight: bold;
        }
    </style>
</head>

<body>
    @if(hasSection($sections, 'company'))
        <div style="position: fixed; top:-100px; left:0; right:0; height:100px;">
            <table class="no-border">

                <tr>

                    <td width="40%">
                        <img src="{{ asset('admin/uploads/logo/' . $settings->logo) }}" width="220">
                    </td>

                    <td width="60%" class="text-right" style="font-size:10px;line-height:1.1">

                        <strong>

                            <div>
                                +{{ $company->country->phonecode ?? '' }}-{{ $company->mobile }}

                                @if($company->alternate_mobile)
                                    | +{{ $company->country->phonecode ?? '' }}-{{ $company->alternate_mobile }}
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
                                {{ $company->country->name ?? '' }}
                                - {{ $company->pincode }}
                            </div>

                        </strong>

                        <div style="font-size:10px">

                            GST In: {{ $company->gstin_no }} |
                            IEC: {{ $company->iec_code }} |
                            PAN: {{ $company->pan_no }} |
                            ESTD 1966
                        </div>
                    </td>
                </tr>
            </table>
            <div>
                <div style="height:6px;background:#2cca38;"></div>
                <div style="height:6px;background:#0b3d6d;"></div>
            </div>

        </div>
    @endif
    <div class="section doc-title">
        <h3>
            {{ $type === 'pi' ? 'Proforma Invoice' : 'Purchase Order' }}
        </h3>
    </div>
    @if(hasSection($sections, 'docinfo'))
        {{-- ================= TITLE ================= --}}
        <div class="section">
            <table>

                <tr>
                    @if(hasExtra($extras, 'pi-number'))

                        <th width="25%">Proforma Invoice No:</th>
                        <td width="25%">
                            {{ $order->quotation->pi_number ?? '-' }}
                        </td>
                    @endif
                    @if(hasExtra($extras, 'pi-date'))

                        <th width="25%">PI Date:</th>
                        <td width="25%">
                            {{ optional($order->quotation->pi_date)->format('d-m-Y') ?? '-' }}
                        </td>
                    @endif
                </tr>

                <tr>
                    @if(hasExtra($extras, 'order-number'))

                        <th>Order No:</th>
                        <td>
                            {{ $order->order_number ?? '-' }}
                        </td>
                    @endif
                    @if(hasExtra($extras, 'order-date'))

                        <th>Order Date:</th>
                        <td>
                            {{ optional($order->order_date)->format('d-m-Y') ?? '-' }}
                        </td>
                    @endif
                </tr>

                <tr>
                    @if(hasExtra($extras, 'quote-number'))
                        <th>Quotation No:</th>
                        <td>
                            {{ $order->quotation->quote_number ?? '-' }}
                        </td>
                    @endif
                    @if(hasExtra($extras, 'quote-date'))
                        <th> Quotation Date:</th>
                        <td>
                            {{ optional($order->quotation->quote_date)->format('d-m-Y') ?? '-' }}
                        </td>
                    @endif
                </tr>

            </table>
        </div>
    @endif
    <!-- ================= BUYER / SELLER ================= -->
    @if(hasSection($sections, 'customer'))
        <div class="buyer-seller section-customer" style="margin-bottom: 10px;">

            <table class="buyer-seller-table">
                <tr class="table-header" style=" background: #2b4c7e;">
                    <th width="50%">BUYER (Importer)</th>
                    <th width="50%">SELLER (Exporter / Manufacturer)</th>
                </tr>
                <tr>

                    <td valign="top">

                        <b>{{ $order->quotation->lead->customer->name }}</b><br>

                        @if($order->contact_person)
                            Attn: {{ $order->contact_person }}<br>
                        @endif
                        @if(hasExtra($extras, 'customer-gst'))

                            @if($order->quotation->lead->customer->gst)
                                GST: {{ $order->quotation->lead->customer->gst }}<br>
                            @endif
                        @endif
                        @if($order->quotation->lead->customer->pan)
                            PAN: {{ $order->quotation->lead->customer->pan }}<br>
                        @endif
                        @if(hasExtra($extras, 'customer-email'))
                            Email: {{ $order->quotation->lead->customer->email ?? '-' }}<br>
                        @endif
                        @if(hasExtra($extras, 'customer-mobile'))
                            Mobile:
                            +{{ optional($order->quotation->lead->customer->country)->phonecode }}
                            {{ optional($order->quotation->lead->customer->primaryPhone)->phone ?? '-' }}
                            <br>
                        @endif
                        @if(hasExtra($extras, 'customer-address'))
                            Address:
                            {{ $order->quotation->lead->customer->address ?? '-' }},
                            {{ optional($order->quotation->lead->customer->city)->name ?? '' }},
                            {{ optional($order->quotation->lead->customer->state)->name ?? '' }},
                            {{ optional($order->quotation->lead->customer->country)->name ?? '' }}
                        @endif
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
    @endif
    <!-- ================= ORDER ITEMS ================= -->
    @if(hasSection($sections, 'items'))
        <div class="section-items">

            <table class="items-table">

                <thead>
                    @php
                        $hasCfv = hasExtra($extras, 'Cfv');
                    @endphp

                    <tr>
                        <th style="width:5%">S.N.</th>

                        <th style="width:{{ $hasCfv ? '15%' : '18%' }}">Item</th>

                        <th style="width:{{ $hasCfv ? '35%' : '42%' }}">Description</th>

                        <th class="center" style="width:{{ $hasCfv ? '8%' : '10%' }}">Qty</th>

                        <th class="right" style="width:{{ $hasCfv ? '12%' : '12%' }}">
                            Rate ({{ $currencySymbol }})
                        </th>

                        <th class="right" style="width:{{ $hasCfv ? '12%' : '13%' }}">
                            Total ({{ $currencySymbol }})
                        </th>

                        @if($hasCfv)
                            <th class="Cfv" style="width:13%">CFV (₹)</th>
                        @endif
                    </tr>
                </thead>

                <tbody>

                    @foreach($order->items as $i => $item)

                        <tr>

                            <td class="center">{{ $i + 1 }}</td>

                            <td>
                                {{ $item->machine->name ?? $item->component->name ?? 'Item' }}
                            </td>

                            <td class="col-desc">
                                <div class="desc-content">
                                    {!! $item->description ?? '-' !!}
                                </div>
                            </td>

                            <td class="center">
                                {{ $item->quantity }}
                            </td>

                            <td class="right">
                                {{ $currencySymbol }} {{ number_format($item->unit_price, 2) }}
                            </td>

                            <td class="right">
                                {{ $currencySymbol }} {{ number_format($item->total_price, 2) }}
                            </td> @php
                                $isINR = $order->currency === 'INR';
                                $rate = $order->conversion_rate ?? 1;
                            @endphp
                            @if(hasExtra($extras, 'Cfv'))
                                <td class="Cfv"> {{ $isINR ? $item->total_price : $item->converted_total_price }}</td>
                            @endif
                        </tr>

                    @endforeach

                </tbody>

            </table>
            {{-- ================= ORDER TOTALS ================= --}}


        </div>
    @endif
    @php
        $subTotal = $order->total_amount;
        $taxableAmount = $subTotal - $order->discount;
        $taxPercent = $order->tax ?? 0;
        $taxAmount = ($taxableAmount * $taxPercent) / 100;
        $grandTotal = $taxableAmount + $taxAmount;
    @endphp
    @if(hasSection($sections, 'totals'))
        {{-- TOTALS --}}
        <div class="section-totals">
            <table class="totals">
                @if(hasExtra($extras, 'subtotal'))
                    <tr class="subtotal">
                        <td class="label">Sub Total</td>
                        <td class="value">
                            <span class="currency-symbol">{{ $currencySymbol }}</span>{{ number_format($subTotal, 2) }}
                        </td>
                    </tr>
                @endif
                @if(hasExtra($extras, 'discount'))
                    <tr class="discount">
                        <td class="label">Discount</td>
                        <td class="value">
                            <span class="currency-symbol">{{ $currencySymbol }}</span> {{ $order->discount ?? 0 }}
                        </td>
                    </tr>
                @endif
                @if(hasExtra($extras, 'taxable-amount'))
                    <tr class="taxable-amount">
                        <td class="label"><strong>Taxable Amount</strong></td>
                        <td class="value">
                            <span class="currency-symbol">{{ $currencySymbol }}</span> {{ number_format($taxableAmount, 2) }}
                        </td>
                    </tr>
                @endif
                @if(hasExtra($extras, 'tax-percent'))
                    <tr class="tax-percent">
                        <td class="label">Tax (%)</td>
                        <td class="value">
                            {{ $order->tax ?? 0 }} %
                        </td>
                    </tr>
                @endif
                @if(hasExtra($extras, 'tax-amount'))
                    <tr class="tax-amount">
                        <td class="label">Tax Amount</td>
                        <td class="value">
                            <span class="currency-symbol">{{ $currencySymbol }}</span>
                            {{ number_format($order->tax_amount ?? 0, 2) }}
                        </td>
                    </tr>
                @endif
                @if(hasExtra($extras, 'final-row'))
                    <tr class="final-row">
                        <td class="label">Final Amount</td>
                        <td class="value">
                            <span class="currency-symbol">{{ $currencySymbol }}</span>
                            {{ number_format($order->final_amount, 2) }}
                        </td>
                    </tr>
                @endif
            </table>
        </div>
    @endif
    @if(hasSection($sections, 'payments'))
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
                    <th class="right">Amount ({{ $currencySymbol }})</th>

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
                            {{ $currencySymbol }} {{ number_format($p->amount, 2) }}
                        </td>
                    </tr>
                    @php
                        $totalPaid = $payments->sum('amount');
                        $remaining = $order->final_amount - $totalPaid;
                        $postDatePayment = $payments->firstWhere('is_post_dated', true);
                    @endphp

                    @if($postDatePayment && $postDatePayment->post_date)
                        @if(hasExtra($extras, 'post-date'))

                            <tr>
                                <td colspan="{{ $hasReference ? 6 : 5 }}" style="padding:0; border:none;">

                                    <div style="
                                                background:#fdecea;
                                                border:1px solid #f5c2c7;
                                                border-left:5px solid #dc3545;
                                                padding:6px 10px;
                                                margin-top:5px;
                                                font-size:11px;
                                            ">

                                        <table width="100%" style="border:none;">
                                            <tr>

                                                <!-- LEFT -->
                                                <td style="border:none; text-align:left; color:#842029;">
                                                    <strong>Post Date:</strong>
                                                    {{ \Carbon\Carbon::parse($postDatePayment->post_date)->format('d-m-Y') }}
                                                </td>

                                                <!-- RIGHT -->
                                                <td style="border:none; text-align:right; color:#842029;">
                                                    <strong>Remaining:</strong>
                                                    {{ $currencySymbol }} {{ number_format($remaining, 2) }}
                                                </td>

                                            </tr>
                                        </table>

                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endif
                @endforeach

            </tbody>

        </table>
    @endif
    @if(hasSection($sections, 'paymenttotals'))
        <div style="width: 100%; display: flex; justify-content: flex-end; margin-top:10px;">
            <table class="totals-box">

                @if(hasExtra($extras, 'total-received'))
                    <tr>
                        <td class="label">Total Received</td>
                        <td class="value">
                            {{ $currencySymbol }} {{ number_format($totalPaid, 2) }}
                        </td>
                    </tr>
                @endif

                @if(hasExtra($extras, 'order-amount'))
                    <tr>
                        <td class="label">Order Amount</td>
                        <td class="value">
                            {{ $currencySymbol }} {{ number_format($order->final_amount, 2) }}
                        </td>
                    </tr>
                @endif

                @if(hasExtra($extras, 'remaining'))
                    <tr class="final">
                        <td class="label">Remaining</td>
                        <td class="value">
                            {{ $currencySymbol }} {{ number_format($order->final_amount - $totalPaid, 2) }}
                        </td>
                    </tr>
                @endif

            </table>
        </div>
    @endif

    @if($payments->last() && $payments->last()->note)
        @if(hasExtra($extras, 'receipt-note'))
            <div class="receipt-note">
                {!! $payments->last()->note !!}
            </div>
        @endif
    @endif
    @if(hasSection($sections, 'sign'))
        <div class="sign-block">

            <div class="sign-title">
                For {{ $company->company_name }}
            </div>

            <br><br>

            <b> R.R. Khare (Rishabh Rai Khare)</b><br>

            {{ $company->designation ?? 'Partner' }}<br>

            Date: {{ now()->format('d-m-Y') }}

        </div>
    @endif
    <div class="print-info">
        This is a computer-generated receipt and is valid without a physical signature.
        Authorised signatory stamp and signature to be affixed on original copy.
    </div>
    <div class="pdf-footer">
        <table width="100%">
            <tr>
                <td class="footer-left" width="90%">
                    ESTD 1966 | India's Most Preferred Pulveriser Brand |
                    ICICI Bank, Ashok Nagar, Kanpur |
                    A/C: 083205004030 | IFSC: ICIC0000832
                </td>

                <td class="footer-right" width="10%">

                </td>
            </tr>
        </table>
    </div>
</body>

</html>