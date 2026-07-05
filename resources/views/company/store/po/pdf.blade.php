<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>
        {{ $po->supplier->name ?? 'Supplier' }} - Purchase Order
    </title>
    <style>
        @page {
            margin: 130px 40px 130px 40px;
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
            padding: 2px 3px;
            vertical-align: top;
        }

        /* ===============================
HEADER
=============================== */

        .no-border td {
            border: none;
            color: #2b4c7e;
            padding: 3px 6px;
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
ITEMS TABLE
=============================== */
        .items-table {
            margin-top: 10px;
        }

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
            width: 5%;
            text-align: center;
        }

        .col-qty {
            width: 5%;
            text-align: center;
        }

        .col-desc {
            width: 35%;
        }


        .col-rate,
        .col-total {
            text-align: right;
        }

        /* ===============================
TOTALS BOX
=============================== */

        .totals {
            margin-top: 10px;
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
            background: #2b4c7e;
            color: #fff;
            text-align: left;
            font-weight: bold;
            padding: 7px 10px;
            border: 1px solid #2b4c7e;
        }

        .terms-box tr {
            page-break-inside: avoid;
        }

        .terms-box td {
            padding: 0;
            border: 1px solid #cfcfcf;
        }

        .terms-content {
            background: #f2f2f2 !important;
            padding: 10px !important;
            font-size: 11px;
            line-height: 1.5;
        }

        .terms-content p {
            margin: 0;
        }

        .pdf-footer {
            position: fixed;
            bottom: -120px;
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
        $tIndex = 0;
    @endphp
    {{-- ================= HEADER ================= --}}
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

                        <div style="font-size:10px"> GST In: {{ $company->gstin_no }} |
                            IEC: {{ $company->iec_code }} |
                            PAN: {{ $company->pan_no }} |
                            ESTD 1966
                        </div>

                    </td>
                </tr>
            </table>

            <!-- header border -->
            <div>
                <div style="height:6px;background:#2cca38;"></div>
                <div style="height:6px;background:#0b3d6d;"></div>
            </div>
        </div>
    @endif
    {{-- ================= DOCUMENT TITLE ================= --}}
    <div class="doc-title">
        <h3>
            PURCHASE ORDER
        </h3>
    </div>
    {{-- ================= DOC INFO ================= --}}
    @if(in_array('docinfo', $sections))
        <div class="section">
            <table>

                {{-- Row 1 --}}
                <tr>

                    @if(in_array('po-code', $extras))
                        <th width="30%">PO Code</th>
                        <td width="30%">{{ $po->po_code }}</td>
                    @endif

                    @if(in_array('po-date', $extras))
                        <th width="20%">Date</th>
                        <td width="20%">
                            {{ \Carbon\Carbon::parse($po->po_date)->format('d/m/Y') }}
                        </td>
                    @endif

                </tr>

                {{-- Row 2 --}}
                <tr>

                    @if(in_array('rfi-code', $extras))
                        <th width="30%">RFI Code</th>
                        <td width="30%">{{ $po->rfi->rfi_code ?? '-' }}</td>
                    @endif

                    @if(in_array('created-by', $extras))
                        <th width="20%">Created By</th>
                        <td width="20%">{{ $po->creator->name ?? '-' }}</td>
                    @endif

                </tr>

            </table>
        </div>
    @endif

    @if(in_array('supplier', $sections))
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
    @endif


    @if(in_array('items', $sections))

        <div>
            <table class="items-table">
                <thead>
                    <tr>

                        @if(in_array('sn', $columns))
                            <th>S.N.</th>
                        @endif

                        @if(in_array('name', $columns))
                            <th>Item</th>
                        @endif
                        @if(in_array('brand', $columns))
                            <th>Brand</th>
                        @endif
                        @if(in_array('condition', $columns))
                            <th>Condition</th>
                        @endif
                        @if(in_array('unit', $columns))
                            <th>Unit</th>
                        @endif

                        @if(in_array('qty', $columns))
                            <th>Qty</th>
                        @endif

                        @if(in_array('rate', $columns))
                            <th>Rate</th>
                        @endif

                        @if(in_array('total', $columns))
                            <th>Amount</th>
                        @endif

                    </tr>
                </thead>

                <tbody>
                    @foreach($po->items as $i => $item)
                        <tr>

                            @if(in_array('sn', $columns))
                                <td class="text-center">{{ $i + 1 }}</td>
                            @endif

                            @if(in_array('name', $columns))
                                <td>{{ $item->item->name ?? '-' }}</td>
                            @endif
                            @if(in_array('brand', $columns))
                                <td> {{ $item->brand->name ?? '-' }}</td>
                            @endif
                            @if(in_array('condition', $columns))
                                <td>{{ $item->condition->name ?? '-' }}</td>
                            @endif
                            @if(in_array('unit', $columns))
                                <td>{{ $item->unit->name ?? '-' }}</td>
                            @endif

                            @if(in_array('qty', $columns))
                                <td class="text-center">{{ $item->quantity }}</td>
                            @endif

                            @if(in_array('rate', $columns))
                                <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                            @endif

                            @if(in_array('total', $columns))
                                <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                            @endif

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    @endif

    @if(in_array('totals', $sections))
        <div class="section">
            <table class="totals">
                @if(in_array('subtotal', $extras))
                    <tr>
                        <td>Subtotal</td>
                        <td class="text-right">{{ number_format($po->subtotal, 2) }}</td>
                    </tr>
                @endif

                @if(in_array('discount', $extras))
                    <tr>
                        <td>Discount</td>
                        <td class="text-right">{{ number_format($po->discount, 2) }}</td>
                    </tr>
                @endif

                @if(in_array('tax-percent', $extras))
                    <tr>
                        <td>Tax (%)</td>
                        <td class="text-right">{{ $po->tax }}%</td>
                    </tr>
                @endif

                @if(in_array('tax-amount', $extras))
                    <tr>
                        <td>Tax Amount</td>
                        <td class="text-right">{{ number_format($po->tax_amount, 2) }}</td>
                    </tr>
                @endif

                @if(in_array('final-row', $extras))
                    <tr>
                        <td><b>Total</b></td>
                        <td class="text-right"><b>{{ number_format($po->final_amount, 2) }}</b></td>
                    </tr>
                @endif

            </table>
        </div>
    @endif
    @if(in_array('remark', $sections))

        <div class="section">
            <table class="terms-box">
                <tr>
                    <th>Remark</th>
                </tr>
                <tr>
                    <td class="terms-content">
                        {!! $po->remark !!}
                    </td>
                </tr>
            </table>
        </div>
    @endif
    <div class="pdf-footer">
        <table width="100%">
            <tr>
                <td class="footer-left">
                    ESTD 1966 | India's Most Preferred Pulveriser Brand |
                    ICICI Bank, Ashok Nagar, Kanpur |
                    A/C: 083205004030 | IFSC: ICIC0000832
                </td>

                <td class="footer-right">

                </td>
            </tr>
        </table>
    </div>

</body>

</html>