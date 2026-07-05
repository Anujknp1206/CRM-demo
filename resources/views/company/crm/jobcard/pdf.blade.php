<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 100px 30px 60px 30px;
        }

        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ public_path('fonts/DejaVuSans.ttf') }}') format('truetype');
        }

        @font-face {
            font-family: 'NotoDevanagari';
            src: url('{{ public_path('fonts/NotoSansDevanagari-Bold.ttf') }}') format('truetype');
            font-weight: bold;
        }

        @font-face {
            font-family: 'NotoDevanagari';
            src: url('{{ public_path('fonts/NotoSansDevanagari-Regular.ttf') }}') format('truetype');
        }

        body {
            font-family: 'NotoDevanagari', DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 0;
        }

        .hindi {
            font-family: 'NotoDevanagari', DejaVu Sans, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 6px;
        }

        .no-border td {
            border: none;
        }

        .title {
            text-align: center;
            font-weight: bold;
            background: #1e4620;
            color: #fff;
            padding: 6px;
            margin-top: 10px;
        }

        .header-line {
            height: 5px;
            background: #2cca38;
        }

        .header-line2 {
            height: 5px;
            background: #0b3d6d;
        }

        .items th {
            background: #1e4620;
            color: #fff;
        }

        .remarks-box {
            background: #f5e6c8;
            color: #1e4620;
        }

        .sign th {
            background: #1e4620;
            color: #fff;
        }

        .sign td {
            height: 50px;
            text-align: center;
        }

        .pdf-footer {
            position: fixed;
            bottom: -70px;
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

        .docinfo-table th {
            color: #1e4620;
            background: #e2efda;
            padding: 6px;
            border: 1px solid #bbb;
            text-align: left;
            width: 30%;
        }

        .docinfo-table td {
            padding: 6px;
            border: 1px solid #bbb;
            background: #fff;
            color: #000;
        }

        .items tbody tr:nth-child(even) {
            background: #dfeadf;
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

    {{-- HEADER --}}
    <div style="position: fixed; top:-80px; left:0; right:0;">
        <table class="no-border">
            <tr>
                <td width="40%">
                    <img src="{{ public_path('admin/uploads/logo/' . $settings->logo) }}" width="220">
                </td>

                <td width="60%" style="text-align:right; font-size:10px; color:#1e4620;">
                    <strong>
                        +{{ $company->country->phonecode ?? '' }}-{{ $company->mobile }} <br>
                        {{ $company->email }} <br>
                        {{ $company->address }}
                    </strong>

                    <div style="font-size:9px;">
                        IEC: {{ $company->iec_code }} |
                        PAN: {{ $company->pan_no }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="header-line"></div>
        <div class="header-line2"></div>
    </div>


    {{-- TITLE --}}
    <div class="title">
        JOB CARD / PRODUCTION ORDER
    </div>


    {{-- DOC INFO --}}
    <table class="docinfo-table" style="margin-top:10px;">
        <tr>
            <th>उत्पादन आदेश सं. / PO No.:</th>
            <td>{{ $planning->po_number }}</td>

            <th>विभाग / Department:</th>
            <td>{{ $planning->department->name ?? '-' }}</td>
        </tr>

        <tr>
            <th>दिनांक / Date:</th>
            <td>{{ optional($planning->created_at)->format('d/m/Y') }}</td>

            <th>उत्पादन प्रभारी / Incharge:</th>
            <td>
                {{ $planning->incharge->first_name ?? '' }}
            </td>
        </tr>

        <tr>
            <th>ग्राहक / Customer:</th>
            <td>{{ $planning->order->quotation->lead->customer->name ?? '-' }}</td>

            <th>प्राथमिकता / Priority:</th>
            <td><b style="color:red;">{{ strtoupper($planning->priority) }}</b></td>
        </tr>

        <tr>
            <th>बिक्री आदेश सं. / SO Ref:</th>
            <td>{{ $planning->order->order_number ?? '-' }}</td>

            <th>शिफ्ट / Shift:</th>
            <td>{{ ucfirst($planning->shift) }}</td>
        </tr>

        <tr>
            <th>डिलीवरी तिथि / Delivery:</th>
            <td>{{ optional($planning->delivery_date)->format('d/m/Y') }}</td>

            <th>स्थिति / Status:</th>
            <td>
                {{ ucfirst(str_replace('_', ' ', $planning->status)) }}
            </td>
        </tr>

        <tr>
            <th>विशेष टिप्पणी / Remarks:</th>
            <td colspan="3" class="remarks-box">
                {!! $planning->remark ?? '-' !!}
            </td>
        </tr>
    </table>


    {{-- ITEMS --}}
    <table class="items" style="margin-top:10px;">
        <thead>
            <tr>
                <th>क्र.सं. <br> S.No</th>
                <th>मशीन का नाम <br> Machine Name</th>
                <th>विवरण (हिंदी) <br> Description</th>
                <th>सामग्री / Specs</th>
                <th>मात्रा <br> Qty</th>
                <th>कर्मचारी <br> Worker</th>
                <th>टिप्पणी / Remarks</th>
                <th>स्थिति <br> Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach($planning->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>

                        <td>
                            {{ $item->orderItem->machine->name
                ?? $item->orderItem->component->name ?? '-' }}
                        </td>

                        <td>{{ $item->description }}</td>
                        <td>{{ $item->specs }}</td>
                        <td>{{ $item->qty }}</td>

                        <td>{{ $item->employee->first_name ?? '-' }}</td>

                        <td>{{ $item->remarks }}</td>

                        <td>  
                            @switch($item->status)

        @case('pending')
            <span style="color:#6c757d; font-weight:600;">
                लंबित
            </span>
        @break

        @case('working')
            <span style="color:#ffc107; font-weight:600;">
                कार्यरत
            </span>
        @break

        @case('done')
            <span style="color:#28a745; font-weight:600;">
                पूर्ण
            </span>
        @break

        @case('hold')
            <span style="color:#dc3545; font-weight:600;">
                रोका गया
            </span>
        @break

    @endswitch
</td>
                    </tr>
            @endforeach
        </tbody>
    </table>
    {{-- TERMS --}}
    <table style="margin-top:10px;">
        <tr>
            <th>विशेष निर्देश / SPECIAL INSTRUCTIONS:</th>
        </tr>
        <tr>
            <td class="remarks-box">
                {!! $planning->term ?? '-' !!}
            </td>
        </tr>
    </table>


    {{-- SIGNATURE --}}
    <table class="sign" style="margin-top:10px;">
        <tr>
            <th>तैयार किया / Prepared By:</th>
            <th>जाँच / Checked By (QC):</th>
            <th>अनुमोदित / Approved By:</th>
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td>{{ $planning->incharge->first_name ?? '' }}</td>
            <td>{{ $planning->checkedBy->first_name ?? '' }}</td>
            <td>Rishab Rai Khare</td>
        </tr>
    </table>
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