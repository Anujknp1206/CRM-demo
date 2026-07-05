<!DOCTYPE html>
<html>

<head>
    <title>Payment Receipt</title>

    <style>
        /* ===================================== */
        /* BASE */
        /* ===================================== */

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 3px 8px;
        }

        .page-container {
            padding: 0;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .mb {
            margin-bottom: 5px;
        }

        .small {
            font-size: 12px;
        }

        /* ===================================== */
        /* COMPANY HEADER */
        /* ===================================== */

        .section-company {
            color: #1b3a6b;
            font-size: 10px;
        }

        .items-table {
            page-break-inside: auto;
        }

        .items-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .items-table thead {
            display: table-header-group;
        }

        .receipt-footer {
            page-break-inside: avoid;
        }

        .sign-block {
            margin-top: 15px;
        }

        .print-info {
            page-break-inside: avoid;
        }

        .receipt-note {
            page-break-inside: avoid;
        }

        .no-break {
            page-break-inside: avoid;
        }

        .header-border {
            width: 100%;
        }

        .header-border::after {
            content: "";
            display: block;
            height: 7px;
            background: #0b3d6d;
        }

        .header-border::before {
            content: "";
            display: block;
            height: 7px;
            background: #2cca38;
        }

        /* ===================================== */
        /* RECEIPT CONFIRMATION NOTE */
        /* ===================================== */

        .receipt-note {
            margin-top: 15px;
            border: 2px solid #2cca38;
            padding: 12px;
            background: #f6fff6;
            line-height: 1.6;
        }

        .sign-block {
            margin-top: 25px;
            border: 1px solid #ccc;
            background: #f5f5f5;
            padding: 15px;
        }

        .sign-title {
            font-weight: 600;
            color: #1b3a6b;
            margin-bottom: 15px;
        }

        .print-info {
            font-size: 11px;
            color: #666;
            text-align: center;
            margin-top: 10px;
            font-style: italic;
        }

        /* ===================================== */
        /* DOCUMENT TITLE */
        /* ===================================== */

        .doc-title {
            text-align: center;
            font-weight: 700;
            color: #1b3a6b;
            background: #e9e9e9;
            padding: 1px;
            letter-spacing: 1px;
        }

        .doc-title h3 {
            margin: 0;
            padding: 5px;
            font-size: 16px;
            letter-spacing: 1px;
        }

        /* ===================================== */
        /* DOCUMENT INFO */
        /* ===================================== */

        .docinfo-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .docinfo-table th {
            background: #f1f1f1;
            border: 1px solid #bbb;
            text-align: left;
            color: #1b3a6b;
            width: 25%;
        }

        .docinfo-table td {
            border: 1px solid #bbb;
            background: #f8f8f8;
        }

        /* ===================================== */
        /* BUYER / SELLER SECTION */
        /* ===================================== */

        .buyer-seller {
            margin-top: 15px;
        }

        .buyer-seller-table {
            width: 100%;
            border-collapse: collapse;
        }

        .buyer-seller-table th {
            background: #2b4c7e;
            color: #fff;
            border: 1px solid #1f3961;
            padding: 7px;
            text-align: left;
        }

        .buyer-seller-table td {
            border: 1px solid #1f3961;
            padding: 10px;
            line-height: 1.6;
        }

        /* ===================================== */
        /* ITEMS TABLE */
        /* ===================================== */

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .items-table th {
            background: #2b4c7e;
            color: #fff;
            border: 1px solid #1f3961;
            padding: 7px;
            text-align: left;
        }

        .items-table td {
            border: 1px solid #aaa;
            padding: 7px;
            vertical-align: top;
        }

        .items-table .right {
            text-align: right;
        }

        .items-table .center {
            text-align: center;
        }

        /* ===================================== */
        /* TOTAL ROW */
        /* ===================================== */

        .totals-table td {
            font-weight: bold;
        }

        /* ===================================== */
        /* TERMS / SUMMARY */
        /* ===================================== */

        .terms-table {
            margin-top: 10px;
            border: 1px solid #aaa;
            padding: 10px;
            background: #fafafa;
            line-height: 1.6;
        }

        /* ===================================== */
        /* SIGNATURE */
        /* ===================================== */

        .signature-box {
            text-align: center;
        }

        .signature-box img {
            max-width: 120px;
            margin-bottom: 5px;
        }

        /* ===================================== */
        /* PRINT */
        /* ===================================== */
        .section-remarks {
            margin-top: 10px;
        }

        .remarks-table {
            width: 100%;
            border-collapse: collapse;
        }

        .remarks-table th {
            width: 20%;
            background: #2b4c7e;
            color: white;
            border: 1px solid #1f3961;
            padding: 6px;
            text-align: left;
        }

        .remarks-table td {
            border: 1px solid #aaa;
            padding: 8px;
            line-height: 1.6;
            background: #fafafa;
        }

        @media print {

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .pdf-footer {
                display: none !important;
            }

            .page-container {
                padding: 0 20px;
            }

            .docinfo-table th,
            .docinfo-table td {
                background: #f1f1f1 !important;
            }

            .doc-title {
                background: #e9e9e9 !important;
            }

            .buyer-seller-table th,
            .items-table th {
                background: #2b4c7e !important;
                color: #fff !important;
            }


        }

        @page {
            margin: 20px 15px 60px 15px;
            size: A4;
        }

        .pdf-footer {
            position: fixed;
            bottom: -25px;
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

<body @if(!isset($pdf)) onload="window.print()" @endif>
    <div class="page-container">
        <!-- ================= HEADER ================= -->

        <div class="section-company">
            <table width="100%">
                <tr>
                    <td width="40%">
                        <img src="{{ asset('admin/uploads/logo/' . $settings->logo) }}" style="width:220px;">
                    </td>
                    <td width="60%" style="text-align:right; font-size:10px; line-height:1.6">

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

                        <div style="font-size:9px">
                            IEC: {{ $company->iec_code }} |
                            PAN: {{ $company->pan_no }} |
                            ESTD 1966
                        </div>

                    </td>
                </tr>
            </table>

            <div class="header-border"></div>
        </div>

        <div class="doc-title">
            <h3>PAYMENT RECEIPT</h3>
        </div>

        <!-- ================= RECEIPT DETAILS ================= -->

        <table class="border mb docinfo-table">
            <tr>
                <th width="25%">Receipt No</th>
                <td width="25%">{{ $payment->payment_number }}</td>

                <th width="25%">Receipt Date</th>
                <td width="25%">{{ $payment->payment_date->format('d-m-Y') }}</td>
            </tr>

            <tr>
                <th>Order No</th>
                <td>{{ $order->order_number }}</td>

                <th>Payment Mode</th>
                <td>{{ ucfirst($payment->payment_mode) }}</td>
            </tr>

            @if($payment->transaction_reference)
                <tr>
                    <th>Transaction Ref</th>
                    <td colspan="3">{{ $payment->transaction_reference }}</td>
                </tr>
            @endif

        </table>

        <!-- ================= BUYER / SELLER ================= -->


        <div class="buyer-seller section-customer">

            <table class="buyer-seller-table">

                <tr class="header-row">
                    <th>BUYER</th>
                    <th>SELLER</th>
                </tr>

                <tr>

                    <td valign="top">

                        <b>{{ $order->quotation->lead->customer->name }}</b><br>

                        Attn: {{ $order->contact_person }}<br>

                        @if ($order->quotation->lead->customer->pan)
                            PAN: {{ $order->quotation->lead->customer->pan }}<br>
                        @endif

                        @if ($order->quotation->lead->customer->gst)
                            <div class="customer-gst">
                                GST: {{ $order->quotation->lead->customer->gst }}
                            </div>
                        @endif
                        @if ($order->quotation->lead->customer->email)

                            Email: {{ $order->quotation->lead->customer->email }}<br>
                        @endif
                        @if ($order->quotation->lead->customer->country->phonecode)
                            Mobile:
                            +{{ optional($order->quotation->lead->customer->country)->phonecode }}
                            {{ optional($order->quotation->lead->customer->primaryPhone)->phone }}<br>
                        @endif
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

                        Email: {{ $company->email }}<br>
                        Website: {{ $company->website }}<br>
                        IEC Code: {{ $company->iec_code }}

                    </td>

                </tr>

            </table>

        </div>

        <!-- ================= ORDER SUMMARY ================= -->

        <table class="border mb items-table">
            <tr>
                <th>Description</th>
                <th class="center">Qty</th>
                <th class="right">Amount</th>
            </tr>

            <tr>
                <td>Order Payment</td>
                <td class="center">1</td>
                <td class="right">{{ $order->currency_symbol }} {{ number_format($order->final_amount, 2) }}</td>
            </tr>

            <tr class="totals-table">
                <td colspan="2" class="right"><b>Total Order Value</b></td>
                <td class="right"><b>{{ $order->currency_symbol }} {{ number_format($order->final_amount, 2) }}</b></td>
            </tr>

        </table>

        <!-- ================= PAYMENT HISTORY ================= -->

        <table class="border mb items-table">

            <tr>
                <th>Inst. Date</th>
                <th>Mode</th>
                @if($payment->transaction_reference)
                    <th>Reference</th>
                @endif
                <th class="right">Amount</th>

            </tr>

            <tr>
                <td>{{ $payment->payment_date->format('d-m-Y') }}</td>
                <td>{{ ucfirst($payment->payment_mode) }}</td>
                @if($payment->transaction_reference)
                    <td>{{ $payment->transaction_reference }}</td>
                @endif
                <td class="right">{{ $order->currency_symbol }} {{ number_format($payment->amount, 2) }}</td>

            </tr>

        </table>
        <br>

        @if($payment->note)
            <div class="receipt-note">
                {!! $payment->note !!}
            </div>
        @endif

        <div class="sign-block">

            <div class="sign-title">
                For {{ $company->company_name }}
            </div>

            <br><br>

            <b> R.R. Khare (Rishabh Rai Khare)</b><br>

            {{ $company->designation ?? 'Partner' }}<br>

            Date: {{ now()->format('d-m-Y') }}

        </div>

        <div class="print-info" style="font-size:10px;">
            This is a computer-generated receipt and is valid without a physical signature.
            Authorised signatory stamp and signature to be affixed on original copy.
        </div>
    </div>
</body>

</html>