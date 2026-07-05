<!DOCTYPE html>
<html>

<head>
    <title>Final Payment Receipt</title>

    <style>
        /* ===================================== */
        /* BASE */
        /* ===================================== */

        body {
            font-family: Arial, Helvetica, sans-serif;
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
            padding: 0 20px;
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

        /* ===================================== */
        /* COMPANY HEADER */
        /* ===================================== */

        .section-company {
            color: #1b3a6b;
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
        /* DOCUMENT TITLE */
        /* ===================================== */

        .doc-title {
            text-align: center;
            font-weight: 700;
            color: #1b3a6b;
            background: #e9e9e9;
            padding: 5px;
        }

        .doc-title h3 {
            margin: 0;
            padding: 5px;
            font-size: 16px;
            letter-spacing: 1px;
        }

        /* ===================================== */
        /* BUYER / SELLER */
        /* ===================================== */

        .buyer-seller {
            margin-top: 15px;
        }

        .buyer-seller-table th {
            background: #2b4c7e;
            color: #fff;
            border: 1px solid #1f3961;
            padding: 7px;
        }

        .buyer-seller-table td {
            border: 1px solid #1f3961;
            padding: 10px;
        }

        /* ===================================== */
        /* ITEMS TABLE */
        /* ===================================== */

        .items-table {
            margin-top: 15px;
        }

        .items-table th {
            background: #2b4c7e;
            color: #fff;
            border: 1px solid #1f3961;
            padding: 7px;
        }

        .items-table td {
            border: 1px solid #aaa;
            padding: 7px;
        }

        .total-table th {
            background: #2b4c7e;
            color: #fff;
            border: 1px solid #1f3961;
            padding: 7px;
        }

        .total-table td {
            border: 1px solid #aaa;
            padding: 7px;
        }


        /* ===================================== */
        /* RECEIPT NOTE */
        /* ===================================== */

        .receipt-note {
            margin-top: 15px;
            border: 2px solid #2cca38;
            padding: 12px;
            background: #f6fff6;
            line-height: 1.6;
        }

        /* ===================================== */
        /* SIGN BLOCK */
        /* ===================================== */

        .sign-block {
            margin-top: 25px;
            border: 1px solid #ccc;
            page-break-inside: avoid;
            F background: #f5f5f5;
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
            font-size: 11px;
            text-align: center;
            margin-top: 10px;
            font-style: italic;
        }

        /* ===================================== */
        /* PRINT */
        /* ===================================== */

        @media print {

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                margin: 20px 15px 60px 15px;
                size: A4;
            }

        }
    </style>
</head>

<body @if(!isset($pdf)) onload="window.print()" @endif>
    <div class="page-container">
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
        {{-- ================= TITLE ================= --}}
        <div class="doc-title">
            <h3>PAYMENT RECEIPT</h3>
        </div>

        {{-- ================= ORDER INFORMATION ================= --}}
        <!-- ================= BUYER / SELLER ================= -->

        <div class="buyer-seller section-customer">

            <table class="buyer-seller-table">

                <tr class="header-row">
                    <th width="50%">BUYER</th>
                    <th width="50%">SELLER</th>
                </tr>

                <tr>

                    <td valign="top">

                        <b>{{ $order->quotation->lead->customer->name }}</b><br>

                        @if($order->contact_person)
                            Attn: {{ $order->contact_person }}<br>
                        @endif

                        @if($order->quotation->lead->customer->gst)
                            GST: {{ $order->quotation->lead->customer->gst }}<br>
                        @endif

                        @if($order->quotation->lead->customer->pan)
                            PAN: {{ $order->quotation->lead->customer->pan }}<br>
                        @endif

                        @if ($order->quotation->lead->customer->email)

                            Email: {{ $order->quotation->lead->customer->email }}<br>
                        @endif
                        @if ($order->quotation->lead->customer->country->phonecode)
                            Mobile:
                            +{{ optional($order->quotation->lead->customer->country)->phonecode }}
                            {{ optional($order->quotation->lead->customer->primaryPhone)->phone }}<br>
                        @endif

                        Address:
                        {{ $order->quotation->lead->customer->address ?? '-' }},
                        {{ optional($order->quotation->lead->customer->city)->name ?? '' }},
                        {{ optional($order->quotation->lead->customer->state)->name ?? '' }},
                        {{ optional($order->quotation->lead->customer->country)->name ?? '' }}

                    </td>

                    <td valign="top">

                        <b>{{ $company->company_name }}</b><br>

                        @if($company->gst_number)
                            GST: {{ $company->gst_number }}<br>
                        @endif

                        @if($company->pan_no)
                            PAN: {{ $company->pan_no }}<br>
                        @endif

                        @if($company->iec_code)
                            IEC: {{ $company->iec_code }}<br>
                        @endif

                        Email: {{ $company->email }}<br>

                        Tel:
                        +{{ $company->country->phonecode ?? '' }}
                        {{ $company->mobile }}

                        @if($company->alternate_mobile)
                            / {{ $company->alternate_mobile }}
                        @endif
                        <br>

                        @if($company->website)
                            Website: {{ $company->website }}<br>
                        @endif

                        Address:
                        {{ $company->address }},
                        {{ $company->city->name ?? '' }},
                        {{ $company->state->name ?? '' }},
                        {{ $company->country->name ?? '' }} -
                        {{ $company->pincode }}

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
                        <th class="right" style="width:15%">Rate (₹)</th>
                        <th class="right" style="width:15%">Total (₹)</th>
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
                                {{ $item->description ?? '-' }}
                            </td>

                            <td class="center">
                                {{ $item->quantity }}
                            </td>

                            <td class="right">
                                {{ $order->currency_symbol }} {{ number_format($item->unit_price, 2) }}
                            </td>

                            <td class="right">
                                {{ $order->currency_symbol }} {{ number_format($item->total_price, 2) }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>
            {{-- ================= ORDER TOTALS ================= --}}


        </div>
        <table class="total-table" style="width:23%; margin-left:auto;">
            <tr>
                <td class="right"><b>Total</b></td>
                <td class="right">
                    {{ $order->currency_symbol }} {{ number_format($order->final_amount, 2) }}
                </td>
            </tr>

        </table>
        {{-- ================= ALL PAYMENTS ================= --}}
        <h5 class="mb-2"><b>Payment History</b></h5>

        <table class="items-table">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Payment No</th>
                    <th>Date</th>
                    <th>Mode</th>
                    <th>Reference</th>
                    <th class="right">Amount (₹)</th>

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

                        <td>
                            {{ $p->transaction_reference ?? '-' }}
                        </td>

                        <td class="right">
                            {{ $order->currency_symbol }} {{ number_format($p->amount, 2) }}
                        </td>
                    </tr>

                @endforeach

            </tbody>

        </table>
        <table class="items-table">

            <tr>
                <td colspan="5" class="right"><b>Total Received</b></td>
                <td class="right"><b>{{ $order->currency_symbol }} {{ number_format($totalPaid, 2) }}</b></td>
            </tr>

            <tr>
                <td colspan="5" class="right"><b>Order Amount</b></td>
                <td class="right"><b>{{ $order->currency_symbol }} {{ number_format($order->final_amount, 2) }}</b></td>
            </tr>

            <tr>
                <td colspan="5" class="right"><b>Remaining</b></td>
                <td class="right">
                    <b>
                        {{ $order->currency_symbol }} {{ number_format($order->final_amount - $totalPaid, 2) }}
                    </b>
                </td>
            </tr>

        </table>
        @if($p->note)
            <div class="receipt-note">
                {!! $p->note !!}
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
        <div class="print-info">
            This is a computer-generated receipt and is valid without a physical signature.
            Authorised signatory stamp and signature to be affixed on original copy.
        </div>
    </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</body>

</html>