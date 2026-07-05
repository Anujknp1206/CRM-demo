<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <title>
        {{ optional($bom->order->quotation?->lead?->customer)->name }} -
        BOM (Bill Of Materials) / बिल ऑफ मटेरियल
    </title>
    @php
        $showCompany = in_array('company', $sections);
    @endphp

    <style>
        @page {
            margin:
                {{ $showCompany ? '130px' : '40px' }}
                40px 110px 40px;
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

        /* ===============================
BASE
=============================== */

        body {
            font-family: 'NotoDevanagari', DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
        }

        .hindi {
            font-family: 'NotoDevanagari', DejaVu Sans, sans-serif;
            font-size: 13px;
        }

        .text-right {
            text-align: right;
        }

        .doc-title {
            text-align: center;
            font-weight: bold;
            font-size: 11.5px;
            color: #1e4620;
            background: #e9e9e9;
            padding: 6px;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .signature-space td {
            height: 60px;
            border: 1px solid #ccc;
        }

        .docinfo-table {
            margin-top: 20px;
        }

        .docinfo-table th,
        .docinfo-table td {
            text-align: left;
        }

        h3 {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 1px;
            margin: 0;
        }

        .table-header {
            background: #e9e9e9;
            color: #1e4620;
            font-weight: bold;
        }

        .instructions-header,
        .signature-header {
            background: #1e4620;
            color: #e9e9e9;
        }

        .signature-names td {
            background: #e2efda;
            text-align: center;
            border: 1px solid #ccc;
            padding: 6px;
        }

        .instructions-body td {
            background: #f5e6c8;
            padding: 10px;
            border: 1px solid #999;
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
            padding: 3px 6px;
            vertical-align: top;
        }

        /* ===============================
HEADER
=============================== */

        .no-border td {
            border: none;
            color: #1e4620;
            padding: 2px 4px;
        }

        /* ===============================
TITLE
=============================== */


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
            color: #1e4620;
        }

        /* ===============================
BUYER SELLER TABLE
=============================== */

        .buyer-seller th {
            background: #1e4620;
            color: #fff;
            font-weight: 600;
        }

        .order-row {
            background: #1e4620;
            color: #fff;
        }

        .buyer-seller td {
            line-height: 1.5;
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
            margin-top: 0px;
        }

        .items-table th {
            background: #1e4620;
            color: #fff;
            text-align: left;
        }

        .items-table td {
            word-break: break-word;
        }

        .col-sn {
            width: 10%;
        }

        .col-code {
            width: 20%;
        }

        .col-part {
            width: 30%;
        }

        .col-qty {
            width: 15%;
        }

        .col-notes {
            width: 25%;
        }


        /* ===============================
TOTALS BOX
=============================== */

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
            background: #1e4620;
            color: #fff;
            font-weight: bold;
        }

        /* ===============================
TERMS
=============================== */

        .terms-table th {
            background: #1e4620;
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
            page-break-inside: avoid;
        }

        /* ===============================
TERMS & CONDITIONS BLOCK
=============================== */


        .terms-box {
            width: 100%;
            border: 1px solid #cfcfcf;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .terms-box th {
            background: #1e4620;
            color: #fff;
            text-align: left;
            font-weight: bold;
            padding: 7px 10px;
            border: 1px solid #1e4620;
        }

        .terms-box tr {
            page-break-inside: avoid;
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
            bottom: -90px;
            left: 0;
            right: 0;
            height: 40px;
        }

        .pdf-footer table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-left {
            background: #1e4620;
            color: #fff;
            font-size: 9px;
            padding: 8px 12px;
        }

        .footer-right {
            background: #1e4620;
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
</head>

<body>
    @php
        function cleanText($text)
        {
            if (!$text)
                return '';

            // REMOVE XML
            $text = preg_replace('/<\?xml.*?\?>/i', '', $text);

            // DECODE HTML
            $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // ✅ KEEP FORMATTING TAGS
            $text = strip_tags($text, '<b><strong><i><u>');

            // CLEAN SPACES
            $text = preg_replace('/\s+/', ' ', $text);

            return trim($text);
        }
        $tIndex = 0;
    @endphp
    @if(in_array('company', $sections))
        <div style="position: fixed; top:-100px; left:0; right:0; height:100px;">
            <table class="no-border">
                <tr>
                    <td width="40%">
                        <img src="{{ asset('admin/uploads/logo/' . $settings->logo) }}" width="220">
                    </td>

                    <td width="60%" class="text-right" style="font-size:10px;line-height:1.6">

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
    @if(in_array('docheading', $sections))

        <div class="section doc-title">
            <h3>
                BOM (Bill Of Materials)
            </h3>
        </div>
    @endif
    @if(in_array('docinfo', $sections))
        <div class="section">
            <table class="docinfo-table">
                <tr>
                    <th>BOM No</th>
                    <td>{{ $bom->bom_number ?? '-' }}</td>


                    <th>Department:</th>
                    <td class="translatable-area"
                        style="{{ isset($translations['department_name']['text']) ? 'font-size:12px; line-height:1.1;' : '' }}">
                        {!! cleanText($translations['department_name']['text'] ?? ($bom->department->name ?? '--'))!!}
                    </td>

                </tr>

                <tr>
                    <th>BOM Date:</th>
                    <td>{{ optional($bom->created_at)->format('d/m/y') }}</td>

                    <th>Incharge:</th>
                    <td class="translatable-area"
                        style="{{ isset($translations['incharge_name']['text']) ? 'font-size:12px; line-height:1.1;' : '' }}">
                        {!! cleanText($translations['incharge_name']['text'] ?? (
            ($bom->supervisor->first_name ?? '-') . ' ' . ($bom->supervisor->last_name ?? '')
        )) !!}
                    </td>

                </tr>

                <tr>

                    <th>Customer Name:</th>
                    <td class="translatable-area "
                        style="{{ isset($translations['customer_name']['text']) ? 'font-size:12px; line-height:1.1;' : '' }}">
                        {!! cleanText($translations['customer_name']['text'] ?? ($bom->order->customer_name ?? '-'))!!}
                    </td>


                    <th>Priority:</th>
                    <td class="translatable-area "
                        style="color:red;{{ isset($translations['priority_name']['text']) ? 'font-size:12px; line-height:1.1;' : '' }}">
                        <strong>
                            {!! cleanText($translations['priority_name']['text'] ?? strtoupper($bom->priority->name ?? '--'))!!}
                        </strong>
                    </td>

                </tr>

                <tr>

                    <th>Order Ref No:</th>
                    <td>{{ $bom->order->order_number ?? '-' }}</td>

                    <th>Shift:</th>
                    <td class="translatable-area "
                        style="{{ isset($translations['shift_name']['text']) ? 'font-size:12px; line-height:1.1;' : '' }}">
                        {!! cleanText($translations['shift_name']['text'] ?? ($bom->shift->name ?? '--'))!!}
                    </td>

                </tr>

                <tr>

                    <th>Delivery Date:</th>
                    <td>{{ $bom->delivery_date_formatted ?? '-' }}</td>

                    <th>BOM Status / स्थिति:</th>
                    <td> <strong>{{ ucfirst(str_replace('_', ' ', $bom->status)) }}</strong> </td>
                </tr>

            </table>
        </div>
    @endif
    @if(in_array('items', $sections))

        @php
            $colCount = 0;
            if (empty($columns) || in_array('sn', $columns))
                $colCount++;
            if (empty($columns) || in_array('code', $columns))
                $colCount++;
            if (empty($columns) || in_array('part', $columns))
                $colCount++;
            if (empty($columns) || in_array('specs', $columns))
                $colCount++;
            if (empty($columns) || in_array('qty', $columns))
                $colCount++;
            if (empty($columns) || in_array('remarks', $columns))
                $colCount++;
            if (empty($columns) || in_array('notes', $columns))
                $colCount++;
        @endphp

        <table class="section items-table">
            <thead>
                <tr>

                    @if(empty($columns) || in_array('sn', $columns))
                        <th class="col-sn" style="text-align:center;">#</th>
                    @endif

                    @if(empty($columns) || in_array('code', $columns))
                        <th class="col-code" style="text-align:center;">Item Code</th>
                    @endif

                    @if(empty($columns) || in_array('part', $columns))
                        <th class="col-part" style="text-align:center;">Item Name</th>
                    @endif
                    @if(empty($columns) || in_array('qty', $columns))
                        <th class="col-qty" style="text-align:center;">Qty</th>
                    @endif
                    @if(empty($columns) || in_array('notes', $columns))
                        <th class="col-notes" style="text-align:center;">Notes</th>
                    @endif

                </tr>
            </thead>

            <tbody>

                @php $serial = 1; @endphp

                @foreach($bom->order->items as $orderItem)

                    <tr class="order-row order-group-{{ $orderItem->id }}" data-order="{{ $orderItem->id }}">

                        <td colspan="8">

                            <b class="order-serial"></b>

                            <span class="translatable-area" data-en="{{ $orderItem->machine->name
                    ?? $orderItem->component->name
                    ?? $orderItem->description }}" data-hi="{{ $orderItem->machine->hi_name
                    ?? $orderItem->component->hi_name
                    ?? $orderItem->description }}">

                                {{ $orderItem->machine->name
                    ?? $orderItem->component->name
                    ?? $orderItem->description }}

                            </span>

                        </td>

                    </tr>
                    {{-- 🔹 PARTS --}}
                    @foreach($bom->parts->sortBy('sort_order') as $part)

                        @php
                            $partItems = $part->items->where('order_item_id', $orderItem->id);
                        @endphp

                        @if($partItems->count())

                            {{-- 🔹 PART HEADER --}}
                            <tr style="background:#eaf2f8; font-weight:600;">
                                <td colspan="8">

                                    <div style="display:flex; justify-content:space-between; align-items:center; width:100%;">

                                        <!-- LEFT -->
                                        <span>
                                            <strong>Part:</strong>

                                            <span class="translatable-area" data-en="{{ $part->part_name }}"
                                                data-hi="{{ $part->hi_part_name ?? $part->part_name }}">

                                                {{ $part->part_name }}

                                            </span>
                                        </span>

                                        <!-- RIGHT -->
                                        @if($part->spec)

                                                <span>
                                                    <strong>Spec:</strong>

                                                    <strong>
                                                        <span class="translatable-area" data-key="spec_value_{{ $part->id }}"
                                                            style="{{ isset($translations['special_instructions']['text']) ? 'font-size:12px; line-height:1.1;' : '' }}">

                                                            {{ cleanText(
                                                $translations['spec_value_' . $part->id]['text']
                                                ?? ($part->spec->name ?? '-')
                                            ) }}

                                                        </span>
                                                    </strong>
                                                </span>

                                        @endif

                                    </div>

                                </td>
                            </tr>

                            {{-- 🔹 PART ITEMS --}}
                            @foreach($partItems as $bomItem)

                                <tr>

                                    <td class="col-sn bom-serial" style="text-align:center;">{{ $serial++ }}</td>

                                    <td class="col-code" style="text-align:center;">
                                        {{ $bomItem->item->code ?? '-' }}
                                    </td>
                                    <td class="col-part translatable-area" data-en="{{ $bomItem->item->name ?? '-' }}"
                                        data-hi="{{ $bomItem->item->hi_name ?? ($bomItem->item->name ?? '-') }}" style="text-align:center;">

                                        {{ $bomItem->item->name ?? '-' }}

                                    </td>
                                    {{-- SPEC --}}
                                    <!-- <td class="col-specs translatable-area" data-key="spec_{{ $bomItem->id }}"
                                                            style="{{ isset($translations['special_instructions']['text']) ? 'font-size:12px; line-height:1.1;' : '' }}">
                                                            {{ cleanText(
                                                                $translations['spec_' . $bomItem->id]['text']
                                                                ?? ($part->spec->name ?? '-')
                                                            ) }}
                                                        </td> -->

                                    {{-- QTY --}}
                                    <td class="col-qty" style="text-align:center;">{{ $bomItem->quantity }}</td>
                                    <td class="col-notes translatable-area" data-en="{!! $bomItem->notes ?? '-'!!}"
                                        data-hi="{!! $bomItem->hi_notes ?? $bomItem->notes ?? '-' !!}" style="text-align:center;">

                                        {!! $bomItem->notes ?? '-' !!}
                                    </td>
                                </tr>
                            @endforeach

                        @endif

                    @endforeach

                @endforeach

            </tbody>
        </table>

    @endif
    {{-- ===============================
    SPECIAL INSTRUCTIONS
    =============================== --}}
    @if(in_array('instructions', $sections))
        <div class="section">
            <table class="instructions-table">

                <tr class="instructions-header">
                    <td>
                        SPECIAL INSTRUCTIONS:
                    </td>
                </tr>

                <tr class="instructions-body">
                    <td class="translatable-area "
                        style="{{ isset($translations['special_instructions']['text']) ? 'font-size:12px; line-height:1.1;' : '' }}">
                        {!!cleanText($translations['special_instructions']['text'] ?? ($bom->remarks ?? '-')) !!}
                    </td>

                </tr>

            </table>
        </div>
    @endif

    {{-- ===============================
    SIGNATURE SECTION
    =============================== --}}
    @if(in_array('signature', $sections))
        <div class="section">
            <table class="signature-table">

                <tr class="signature-header">
                    <th>Prepared By</th>
                    <th>Checked By (QC)</th>
                    <th>Approved By</th>
                </tr>

                {{-- EMPTY SPACE --}}
                <tr class="signature-space">
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                {{-- NAMES --}}
                <tr class="signature-names">
                    <td class="translatable-area"
                        style="{{ isset($translations['prepared_by']['text']) ? 'font-size:12px; line-height:1.1;' : '' }}">
                        <strong>
                            {!! cleanText($translations['prepared_by']['text'] ?? (($bom->supervisor->first_name ?? '-') . ' ' .
            ($bom->supervisor->last_name ?? '')))!!}
                        </strong>
                    </td>


                    <td class="translatable-area "
                        style="{{ isset($translations['checked_by']['text']) ? 'font-size:12px; line-height:1.1;' : '' }}">
                        <strong>
                            {!! cleanText($translations['checked_by']['text'] ?? (($bom->checker->first_name ?? '-') . ' ' .
            ($bom->checker->last_name ?? '')))!!}
                        </strong>
                    </td>


                    <td class="translatable-area "
                        style="{{ isset($translations['approved_by']['text']) ? 'font-size:12px; line-height:1.1;' : '' }}">
                        <strong>
                            {!!cleanText($translations['approved_by']['text'] ?? $user->name ?? 'Rishabh Rai Khare')!!}
                        </strong>
                    </td>

                </tr>

            </table>
        </div>
    @endif
    <div class="pdf-footer">
        <table width="100%">
            <tr>
                <td class="footer-left">
                    ESTD 1966 | India's Most Preferred Pulveriser Brand | ICICI Bank, Ashok Nagar, Kanpur | A/C:
                    083205004030 | IFSC: ICIC0000832
                </td>
                <td class="footer-right">
                </td>
            </tr>
        </table>
    </div>
</body>

</html>