<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <title>
        {{ optional($order->quotation?->lead?->customer)->name }} -
        {{ $docType === 'pi' ? 'Purchase Order' : 'Order' }}
    </title>

    <style>
        @font-face {
            font-family: 'SourceSans';
            font-style: normal;
            font-weight: 400;
            src: url("{{ public_path('fonts/SourceSans3-Regular.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'SourceSans';
            font-style: normal;
            font-weight: 700;
            src: url("{{ public_path('fonts/SourceSans3-Bold.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'SourceSansBlack';
            src: url("{{ public_path('fonts/SourceSans3-Black.ttf') }}") format('truetype');
        }

        .hindi,
        .translatable-area {
            font-family: notohindi !important;
        }


        body {
            font-family: 'SourceSans';
            font-size: 12px;
            font-weight: 400;
            color: #000;
            margin: 0;
        }

        .text-right {
            text-align: right;
        }

        .doc-title {
            font-family: sourcesanssemibold;
            font-size: 13px;
            color: #1b3a6b;
            background: #e9e9e9;
            text-align: center;
            padding: 5px;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .table-header {
            background: #e9e9e9;
            color: #1b3a6b;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }


        .doc-info th,
        .doc-info td,
        .buyer-seller th,
        .buyer-seller td,
        .remarks-table th,
        .remarks-table td,
        .totals th,
        .totals td {
            border: 1px solid #cfcfcf;
            padding: 2px 3px;
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
            line-height: 1.2;
        }

        .remarks-table td {
            word-break: break-word;
        }

        /* ===============================
ITEMS TABLE
=============================== */
        .items-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .items-table th {
            background: #2b4c7e;
            color: #fff;
            font-family: sourcesanssemibold;
            text-align: center;
            vertical-align: middle;
            padding: 5px 4px;
        }

        .items-table td {
            word-break: break-word;
        }

        .items-table thead {
            display: table-header-group;
        }

        .col-desc p,
        .col-desc div,
        .col-desc ul,
        .col-desc ol,
        .col-desc li {
            page-break-inside: auto !important;
        }

        .col-desc {
            white-space: normal !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
        }

        .items-table tbody {
            page-break-inside: auto;
            padding: 5px;
        }

        .items-table td,
        .items-table th {
            vertical-align: top;
            overflow: visible;
        }

        .col-sn {
            width: 4%;
            text-align: center;
        }

        .col-name {
            width: 18%;
        }

        .col-desc {
            width: 41%;
        }

        .col-qty {
            width: 3%;
            text-align: center !important;
        }

        .col-rate {
            width: 10%;
        }

        .col-total {
            width: 10%;
        }

        .col-Cfv {
            width: 10%;
        }

        .totals {
            width: 320px;
            margin-left: auto;
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

        .terms-table th {
            background: #2b4c7e;
            color: #fff;
            text-align: left;
        }

        .terms-table td {
            line-height: 1.6;
        }

        td {
            word-wrap: break-word;
            word-break: break-word;
        }


        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }


        .terms-box {
            width: 100%;
            border: 1px solid #cfcfcf;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        .terms-box th {
            background: #2b4c7e;
            color: #fff;
            text-align: left;
            font-weight: bold;
            padding: 7px 10px;
            border: 1px solid #2b4c7e;
        }

        tfoot {
            display: table-footer-group;
        }

        .terms-box tr {
            page-break-inside: auto;
        }

        .terms-box td {
            padding: 0;
            border: 1px solid #cfcfcf;
        }

        .terms-header {
            background: #2b4c7e;
            color: #fff;
            font-weight: bold;
            padding: 5px 5px;
            border-bottom: 1px solid #cfcfcf;
        }

        .terms-content {
            background: #f2f2f2;
            padding: 5px;
            line-height: 1.5;
            font-size: 11px;
        }

        .terms-content p {
            margin: 4px 0;
        }

        .pdf-footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: -43px;
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
    </style>
    <style>
        .items-table .first-chunk td {
            border-top: 1px solid #cfcfcf !important;
            border-right: 1px solid #cfcfcf !important;
            border-left: 1px solid #cfcfcf !important;
            border-bottom: none !important;
        }

        .items-table .middle-chunk td {
            border-top: none !important;
            border-bottom: none !important;
            border-left: 1px solid #cfcfcf !important;
            border-right: 1px solid #cfcfcf !important;
        }

        .items-table .last-chunk td {
            border-top: none !important;
            border-bottom: 1px solid #cfcfcf !important;
            border-left: 1px solid #cfcfcf !important;
            border-right: 1px solid #cfcfcf !important;
        }

        .items-table .single-chunk td {
            border: 1px solid #cfcfcf !important;
        }
    </style>
</head>

<body>
    @php
        function cleanText($text)
        {
            $text = preg_replace('/<\?xml.*?\?>/i', '', $text);
            $text = preg_replace('/<p[^>]*>/', '', $text);
            $text = preg_replace('/<\/p>/', '<br>', $text);
            $text = preg_replace('/(<br\s*\/?>\s*){2,}/i', '<br>', $text);
            $text = preg_replace('/&nbsp;/', ' ', $text);
            return trim($text);
        }
    @endphp


    {{-- ================= DOCUMENT TITLE ================= --}}
    <div class=" doc-title">
        <h3>
            {{ $docType === 'pi' ? 'PURCHASE ORDER' : 'ORDER' }}
        </h3>
    </div>


    {{-- ================= DOC INFO ================= --}}
    @if(in_array('docinfo', $sections))

        <div class="section">

            <table class="doc-info">

                <tr>

                    <th width="15%" style="font-family: sourcesanssemibold;font-size:13px">
                        {{ $docType === 'pi' ? 'PO Number' : 'Order Number' }}
                    </th>

                    <td width="25%" style="font-size:13px">
                        {{ $docType === 'pi' ? $order->po_number : $order->order_number }}
                    </td>

                    <th width="15%" style="font-family: sourcesanssemibold;font-size:13px">Order Date</th>

                    <td width="15%" style="font-size:13px">
                        {{ \Carbon\Carbon::parse(
            $docType === 'pi' ? $order->po_date : $order->order_date
        )->format('d/m/Y') }}
                    </td>

                    <th width="15%" style="font-family: sourcesanssemibold;font-size:13px">Delivery Date</th>

                    <td width="15%" style="font-size:13px">
                        {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') : '-' }}
                    </td>

                </tr>

            </table>

        </div>

    @endif


    {{-- ================= CUSTOMER ================= --}}
    @if(in_array('customer', $sections))


        <div>

            <table class="buyer-seller">

                <tr class="table-header" style=" background: #2b4c7e;">
                    <th>BUYER (Importer)</th>
                    <th>SELLER (Exporter / Manufacturer)</th>
                </tr>

                <tr>

                    <td valign="top" style="width: 55%;">

                        <b style="color: #2b4c7e;">{{ $order->quotation->lead->customer->name }}</b><br>
                        @if(in_array('customer-attn', $extras))
                            Attn: {{ $order->quotation->contact_person }}<br>
                        @endif
                        @if($order->quotation->lead->customer->pan)
                            PAN: {{ $order->quotation->lead->customer->pan }}<br>
                        @endif

                        @if($order->quotation->lead->customer->gst)
                            GST: {{ $order->quotation->lead->customer->gst }}<br>
                        @endif

                        Email: {{ $order->quotation->lead->customer->email }}<br>

                        Mobile:
                        +{{ optional($order->quotation->lead->customer->country)->phonecode }}
                        {{ optional($order->quotation->lead->customer->primaryPhone)->phone }}

                        <br>

                        Address:
                        {{ $order->quotation->lead->customer->address }}

                    </td>


                    <td valign="top" style="width: 45%;">

                        <b>{{ $company->company_name }}</b><br>
                        {{ $company->address }}<br>
                        {{ $company->city->name ?? '' }},
                        {{ $company->state->name ?? '' }},
                        {{ $company->country->name ?? '' }}
                        - {{ $company->pincode }}
                        <br>

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
    @if(in_array('section-remarks', $extras))

        <div class="section" style="margin-top: 10px;">

            <table class="remarks-table">

                <tr>
                    <th width="25%">Remarks</th>
                    <td class="translatable-area" style="{{ $lang === 'hi' ? 'font-size:12px; line-height:1.2;' : '' }}">
                        {!! cleanText($lang === 'hi' ? ($order->hi_remark ?? $order->remark) : $order->remark) !!}
                    </td>
                </tr>

                @if($order->delivery_address)
                    <tr>
                        <th>Delivery Address</th>
                        <td>{!! $order->delivery_address !!}</td>
                    </tr>
                @endif

            </table>

        </div>

    @endif

    {{-- ================= ITEMS ================= --}}
    @if(in_array('items', $sections))
        <div>
            <table class="items-table">
                <thead>
                    <tr>
                        @if(in_array('sn', $columns))
                            <th class="col-sn">S.N.</th>
                        @endif

                        @if(in_array('name', $columns))
                            <th class="col-name">Item</th>
                        @endif

                        @if(in_array('desc', $columns))
                            <th class="col-desc">Description</th>
                        @endif

                        @if(in_array('qty', $columns))
                            <th class="col-qty">Qty</th>
                        @endif

                        @if(in_array('rate', $columns))
                            <th class="col-rate">Rate ({{ $currencySymbol }})</th>
                        @endif

                        @if(in_array('total', $columns))
                            <th class="col-total">Total ({{ $currencySymbol }})</th>
                        @endif
                        @if(in_array('Cfv', $columns))
                            <th class="col-Cfv">CFV (₹)</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @foreach($order->items as $i => $item)

                        @php

                            $itemName = '';

                            if ($lang === 'hi') {
                                $itemName = $item->machine->hi_name
                                    ?? $item->component->hi_name
                                    ?? $item->machine->name
                                    ?? $item->component->name;
                            } else {
                                $itemName = $item->machine->name
                                    ?? $item->component->name;
                            }

                            $description = $lang === 'hi'
                                ? ($item->hi_description ?? $item->description)
                                : $item->description;

                            preg_match_all(
                                '/(?:.*?)(?:<br\s*\/?>|$)/is',
                                $description,
                                $matches
                            );

                            $parts = array_filter(array_map('trim', $matches[0]));

                            if (count($parts) <= 1) {

                                $chunks = [$description];

                            } else {

                                if (count($parts) > 8) {

                                    $chunks = array_map(
                                        fn($chunk) => implode('', $chunk),
                                        array_chunk($parts, 4)
                                    );

                                } else {

                                    $chunks = [
                                        implode('<br>', $parts)
                                    ];

                                }
                            }

                            $isINR = $order->currency === 'INR';

                        @endphp

                        @foreach($chunks as $chunkIndex => $chunk)

                            <tr
                                class="{{ count($chunks) === 1 ? 'single-chunk' : '' }}{{ $chunkIndex === 0 && count($chunks) > 1 ? 'first-chunk' : '' }}{{ $chunkIndex > 0 && $chunkIndex < count($chunks) - 1 ? 'middle-chunk' : '' }}{{ $chunkIndex === count($chunks) - 1 && count($chunks) > 1 ? 'last-chunk' : '' }}">

                                @if(in_array('sn', $columns))
                                    <td class="col-sn {{ $chunkIndex > 0 ? 'continuation-cell' : '' }}">
                                        {!! $chunkIndex === 0 ? ($i + 1) : '' !!}
                                    </td>
                                @endif

                                @if(in_array('name', $columns))
                                    <td class="col-name translatable-area {{ $chunkIndex > 0 ? 'continuation-cell' : '' }}"
                                        style="{{ $lang === 'hi' ? 'font-size:12px; line-height:1.2;' : '' }}">
                                        {!! $chunkIndex === 0 ? cleanText($itemName) : '' !!}
                                    </td>
                                @endif

                                @if(in_array('desc', $columns))
                                    <td class="col-desc translatable-area {{ $chunkIndex > 0 ? 'continuation-cell' : '' }}"
                                        style="{{ $lang === 'hi' ? 'font-size:12px; line-height:1.2;' : '' }}">
                                        {!! cleanText($chunk) !!}
                                    </td>
                                @endif

                                @if(in_array('qty', $columns))
                                    <td class="col-qty {{ $chunkIndex > 0 ? 'continuation-cell' : '' }}">
                                        {!! $chunkIndex === 0 ? $item->quantity : '' !!}
                                    </td>
                                @endif

                                @if(in_array('rate', $columns))
                                    <td class="col-rate text-right {{ $chunkIndex > 0 ? 'continuation-cell' : '' }}">
                                        {!! $chunkIndex === 0
                                    ? number_format($item->unit_price, 2)
                                    : '' !!}
                                    </td>
                                @endif

                                @if(in_array('total', $columns))
                                    <td class="col-total text-right {{ $chunkIndex > 0 ? 'continuation-cell' : '' }}">
                                        {!! $chunkIndex === 0
                                    ? number_format($item->total_price, 2)
                                    : '' !!}
                                    </td>
                                @endif

                                @if(in_array('Cfv', $columns))
                                    <td class="col-Cfv text-right {{ $chunkIndex > 0 ? 'continuation-cell' : '' }}">
                                        {!! $chunkIndex === 0
                                    ? number_format(
                                        $isINR
                                        ? $item->total_price
                                        : $item->converted_total_price,
                                        2
                                    )
                                    : '' !!}
                                    </td>
                                @endif

                            </tr>

                        @endforeach

                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @php
        $subTotal = $order->total_amount ?? 0;
        $discount = $order->discount ?? 0;

        $taxableAmount = $subTotal - $discount;

        $taxPercent = $order->tax ?? 0;
        $taxAmount = ($taxableAmount * $taxPercent) / 100;

        $grandTotal = $taxableAmount + $taxAmount;
    @endphp

    <div class="section">
        <table class="totals">

            @if(in_array('subtotal', $extras))
                <tr>
                    <td class="label">Subtotal :</td>
                    <td class="value">
                        {{ $currencySymbol }} {{ number_format($subTotal, 2) }}
                    </td>
                </tr>
            @endif


            @if(in_array('discount', $extras))
                <tr>
                    <td class="label">Discount :</td>
                    <td class="value">
                        {{ $currencySymbol }} {{ number_format($discount, 2) }}
                    </td>
                </tr>
            @endif

            @if(in_array('taxable-amount', $extras))
                <tr>
                    <td class="label"><strong>Taxable Amount :</strong></td>
                    <td class="value">{{ $currencySymbol }} {{ number_format($taxableAmount, 2) }}</td>
                </tr>
            @endif

            @if(in_array('tax-percent', $extras))
                <tr>
                    <td class="label">Tax (%) :</td>
                    <td class="value">{{ $taxPercent }} %</td>
                </tr>
            @endif
            @if(in_array('tax-amount', $extras))
                <tr>
                    <td class="label">Tax Amount :</td>
                    <td class="value">{{ $currencySymbol }} {{ number_format($taxAmount, 2) }}</td>
                </tr>
            @endif
            @if(in_array('final-row', $extras))
                <tr class="final-row">
                    <td class="label"><strong>Final Amount :</strong></td>
                    <td class="value">
                        <strong>
                            {{ $currencySymbol }} {{ number_format($grandTotal, 2) }}
                        </strong>
                    </td>
                </tr>
            @endif
        </table>
    </div>

    @if(in_array('terms', $sections))
        <div class="terms-section">
            <div class="terms-header">
                Terms & Conditions
            </div>

            <div class="terms-content translatable-area">
                {!! $lang === 'hi'
            ? ($order->hi_terms_conditions ?? $order->terms_conditions)
            : $order->terms_conditions !!}
            </div>
        </div>
    @endif
</body>

</html>