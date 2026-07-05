<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>
        {{ $quotation->lead->customer->name }} -
        {{ $docType === 'pi' ? 'Proforma Invoice' : 'Quotation' }}
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

        h3 {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1px;
            margin: 0;
        }


        .section {
            margin-bottom: 10px;
        }


        .section table th {
            background: #f1f1f1;
            color: #2b4c7e;
            font-weight: 600;
        }


        .buyer-seller th {
            background: #2b4c7e;
            color: #fff;
        }

        .buyer-seller td {
            line-height: 1.2;
        }


        .remarks-table th {
            width: 25%;
            background: #f3f3f3;
            font-weight: 600;
        }

        .remarks-table td {
            word-break: break-word;
        }

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

        .terms-content {
            background: #f2f2f2;
            padding: 5px;
            line-height: 1.5;
            font-size: 11px;
        }

        .terms-content p {
            margin: 4px 0;
        }


        .terms-section {
            width: 100%;
            border: 1px solid #cfcfcf;
            margin-top: 10px;
            page-break-inside: auto;
        }

        .terms-header {
            background: #2b4c7e;
            color: #fff;
            font-weight: bold;
            padding: 5px 15px;
            border-bottom: 1px solid #cfcfcf;
        }

        .terms-content {
            background: #f2f2f2;
            padding: 8px;
            font-size: 11px;
            line-height: 1.5;
            page-break-inside: auto;
        }

        .terms-content p {
            margin: 0 0 6px 0;
        }

        .terms-content ul,
        .terms-content ol {
            margin: 0 0 5px 20px;
            padding: 0;
        }

        .terms-content li {
            margin-bottom: 4px;
        }

        .terms-header {
            page-break-after: avoid;
        }

        .items-table tr,
        .items-table td,
        .items-table tbody {
            page-break-inside: auto !important;
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

    {{-- ================= DOCUMENT TITLE ================= --}}
    <div class="doc-title">
        <h3>
            {{ $docType === 'pi' ? 'PROFORMA INVOICE' : 'QUOTATION' }}
        </h3>
    </div>
    {{-- ================= DOC INFO ================= --}}
    @if(in_array('docinfo', $sections))
        <div class="section">
            <table class="doc-info">
                <tr>

                    <th width="35%" style="font-family: sourcesanssemibold;font-size:13px">
                        {{ $docType === 'pi' ? ' PI Number' : 'Quotation Number' }}
                    </th>

                    <td width="35%" style="font-size:13px">
                        {{ $docType === 'pi' ? $quotation->pi_number : $quotation->quote_number }}
                    </td>

                    <th width="12%" style="font-family: sourcesanssemibold;font-size:13px">Date</th>

                    <td width="12%" style="font-size:13px">
                        {{ \Carbon\Carbon::parse(
            $docType === 'pi' ? $quotation->pi_date : $quotation->quote_date
        )->format('d/m/Y') }}
                    </td>

                </tr>
            </table>
        </div>
    @endif

    {{-- ================= CUSTOMER ================= --}}
    @if(in_array('customer', $sections))

        <div>

            <table class="buyer-seller">

                <tr class="table-header" style="font-family:sourcesanssemibold;background: #2b4c7e;">
                    <th>BUYER (Importer)</th>
                    <th>SELLER (Exporter / Manufacturer)</th>
                </tr>

                <tr>

                    <td valign="top" style="width: 55%; line-height: 1.5;">

                        <b style="color: #2b4c7e;">{{ $quotation->lead->customer->name }}</b><br>
                        @if(in_array('customer-attn', $extras))
                            Attn: {{ $quotation->contact_person }}<br>
                        @endif
                        @if($quotation->lead->customer->pan)
                            PAN: {{ $quotation->lead->customer->pan }}<br>
                        @endif

                        @if(in_array('customer-gst', $extras))

                            GST: {{ $quotation->lead->customer->gst }}<br>
                        @endif
                        @if(in_array('customer-email', $extras))
                            Email: {{ $quotation->lead->customer->email }}<br>
                        @endif
                        @if(in_array('customer-phone', $extras))

                            Mobile:
                            +{{ optional($quotation->lead->customer->country)->phonecode }}
                            {{ optional($quotation->lead->customer->primaryPhone)->phone }}
                            <br>
                        @endif

                        @if(in_array('customer-address', $extras))
                            Address:
                            {{ $quotation->lead->customer->address }}
                        @endif
                    </td>


                    <td valign="top" style="width: 45%;line-height: 1.3;">

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
    @if(in_array('quotation-remarks', $extras) || in_array('delivery-address', $extras))

        <div class="section" style="margin-top: 10px;">

            <table class="remarks-table" style="line-height: 1.3;">

                @if(in_array('quotation-remarks', $extras))
                    <tr>
                        <th width="25%" style="font-family:sourcesanssemibold;">Special Remarks</th>

                        <td class="translatable-area" style="{{ $lang === 'hi' ? 'font-size:12px; line-height:1.2;' : '' }}">
                            {!!$lang === 'hi' ? ($quotation->hi_special_clause ?? $quotation->special_clause) : $quotation->special_clause!!}
                        </td>
                    </tr>
                @endif


                @if(in_array('delivery-address', $extras))
                    <tr>
                        <th style="font-family:sourcesanssemibold;">Destination</th>

                        <td>
                            {!! $quotation->delivery_address ?? '-'!!}
                        </td>
                    </tr>
                @endif

            </table>

        </div>

    @endif
    @if(in_array('items', $sections))
        <table class="items-table">
            <thead>
                <tr>
                    @if(in_array('sn', $columns))
                        <th class="col-sn" style="font-family:sourcesanssemibold;">S.N.</th>
                    @endif

                    @if(in_array('name', $columns))
                        <th class="col-name" style="font-family:sourcesanssemibold;">Item</th>
                    @endif

                    @if(in_array('desc', $columns))
                        <th class="col-desc" style="font-family:sourcesanssemibold;">Description</th>
                    @endif

                    @if(in_array('qty', $columns))
                        <th class="col-qty" style="font-family:sourcesanssemibold;">Qty</th>
                    @endif

                    @if(in_array('rate', $columns))
                        <th class="col-rate" style="font-family:sourcesanssemibold;">Rate ({{ $currencySymbol }})</th>
                    @endif

                    @if(in_array('total', $columns))
                        <th class="col-total" style="font-family:sourcesanssemibold;">Total ({{ $currencySymbol }})</th>
                    @endif
                    @if(in_array('Cfv', $columns))
                        <th class="col-Cfv" style="font-family:sourcesanssemibold;">CFV (₹)</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @foreach($quotation->items as $i => $item)

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

                        /*
                        |--------------------------------------------------------------------------
                        | Split only if numbered points exist
                        |--------------------------------------------------------------------------
                        */

                        preg_match_all(
                            '/(?:.*?)(?:<br\s*\/?>|$)/is',
                            $description,
                            $matches
                        );

                        $parts = array_filter(array_map('trim', $matches[0]));

                        /*
                        |--------------------------------------------------------------------------
                        | If numbering not found, keep original HTML untouched
                        |--------------------------------------------------------------------------
                        */

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

                        $isINR = $quotation->currency === 'INR';

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
                                    {!! $chunkIndex === 0 ? $itemName : '' !!}
                                </td>
                            @endif

                            @if(in_array('desc', $columns))
                                <td class="col-desc translatable-area {{ $chunkIndex > 0 ? 'continuation-cell' : '' }}"
                                    style="{{ $lang === 'hi' ? 'font-size:12px; line-height:1.2;' : '' }}">

                                    {!! $chunk !!}

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
                                    ? number_format(
                                        $isINR
                                        ? $item->unit_price
                                        : $item->converted_unit_price,
                                        2
                                    )
                                    : '' !!}
                                </td>
                            @endif

                            @if(in_array('total', $columns))
                                <td class="col-total text-right {{ $chunkIndex > 0 ? 'continuation-cell' : '' }}">
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

                            @if(in_array('Cfv', $columns))
                                <td class="col-Cfv text-right {{ $chunkIndex > 0 ? 'continuation-cell' : '' }}">
                                    {!! $chunkIndex === 0
                                    ? number_format($item->total_price, 2)
                                    : '' !!}
                                </td>
                            @endif

                        </tr>

                    @endforeach

                @endforeach
            </tbody>
        </table>
    @endif
    @php
        $isINR = $quotation->currency === 'INR';
        $subTotal = $quotation->total_amount ?? 0;
        $discount = $quotation->discount ?? 0;
        $taxableAmount = $subTotal - $discount;
        $taxPercent = $quotation->tax ?? 0;
        $taxAmount = $quotation->tax_amount ?? 0;
        $final = $quotation->final_amount ?? 0;
        $cfv = $isINR ? $final : $final * $quotation->conversion_rate;
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
                    <td class="value">
                        {{ $currencySymbol }} {{ number_format($taxableAmount, 2) }}
                    </td>
                </tr>
            @endif

            @if(in_array('tax-percent', $extras))
                <tr>
                    <td class="label">Tax (%) :</td>
                    <td class="value">
                        {{ $taxPercent }} %
                    </td>
                </tr>
            @endif

            @if(in_array('tax-amount', $extras))
                <tr>
                    <td class="label">Tax Amount :</td>
                    <td class="value">
                        {{ $currencySymbol }} {{ number_format($taxAmount, 2) }}
                    </td>
                </tr>
            @endif


            @if(in_array('final-row', $extras))
                <tr class="final-row">
                    <td class="label"><strong>Final Amount :</strong></td>
                    <td class="value">
                        <strong>
                            {{ $currencySymbol }} {{ number_format($final, 2) }}
                        </strong>
                    </td>
                </tr>
            @endif

        </table>
    </div>


    {{-- ================= TERMS ================= --}}
    @if(in_array('terms', $sections))
        @if($quotation->terms_conditions)

            <div class="terms-section">
                <div class="terms-header">
                    Terms & Conditions
                </div>

                <div class="terms-content translatable-area">
                    {!! $lang === 'hi'
                    ? ($quotation->hi_terms_conditions ?? $quotation->terms_conditions)
                    : $quotation->terms_conditions !!}
                </div>
            </div>

        @endif
    @endif

</body>

</html>