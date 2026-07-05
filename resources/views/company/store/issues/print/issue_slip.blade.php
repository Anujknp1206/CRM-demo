<!DOCTYPE html>
<html>

<head>
    <title>Issue Slip</title>
    <style>
        @page {
            margin: 130px 40px 110px 40px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
        }

        h3 {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 1px;
            margin: 0;
        }

        .section {
            margin-bottom: 10px;
        }

        /* TITLE */
        .doc-title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            color: #1b3a6b;
            background: #e9e9e9;
            padding: 3px 3px;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .no-border td {
            border: none;
            color: #2b4c7e;
            padding: 2px 4px;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #cfcfcf;
            padding: 4px 6px;
        }

        /* HEADER STYLE */
        .table-header {
            background: #1b3a6b;
            color: #fff;
            font-weight: bold;
        }

        /* INFO TABLE */
        .info-table th {
            background: #f1f1f1;
            color: #2b4c7e;
            width: 20%;
        }

        /* ITEMS TABLE */
        .items-table th {
            background: #1b3a6b;
            color: #fff;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* TOTAL */
        .total-row td {
            font-weight: bold;
            background: #f3f3f3;
        }

        /* REMARK */
        .remarks-box {
            border: 1px solid #cfcfcf;
            padding: 8px;
            background: #f9f9f9;
        }

        /* SIGN */
        .signature td {
            text-align: center;
            padding-top: 40px;
        }

        /* HEADER FIX */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
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
            background: #1b3a6b;
            color: #fff;
            font-size: 9px;
            padding: 8px 12px;
        }

        .footer-right {
            background: #1b3a6b;
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

        .signature-header {
            background: #1b3a6b;
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

        .signature-space td {
            height: 60px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <div style="position: fixed; top:-100px; left:0; right:0; height:100px;">
        <table class="no-border">
            <tr>
                <td width="40%">
                    <img src="{{ public_path('admin/uploads/logo/' . $settings->logo) }}" width="220">
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

                    <div style="font-size:10px">   GST In: {{ $company->gstin_no }} |
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
    <div class="doc-title">
        <h3>ISSUE SLIP</h3>
    </div>

    {{-- ISSUE INFO --}}
    <div class="section">
        <table class="info-table">
            <tr>
                <th>Issue No</th>
                <td>{{ $issue->issue_no }}</td>

                <th>BOM No</th>
                <td>{{ $issue->bom->bom_number ?? '-' }}</td>
            </tr>

            <tr>
                <th>Date</th>
                <td>{{ \Carbon\Carbon::parse($issue->issue_date)->format('d/m/Y') }}</td>

                <th>Time</th>
                <td>{{ \Carbon\Carbon::parse($issue->issue_time)->format('h:i A') }}</td>
            </tr>

            <tr>
                <th>Department</th>
                <td>{{ $issue->department->name ?? '-' }}</td>

                <th>Assigned TO:</th>
                <td>
                    {{ $issue->employee->first_name ?? '' }}
                    {{ $issue->employee->last_name ?? '' }}
                </td>
            </tr>
        </table>
    </div>

    {{-- ITEMS --}}
    <div class="section">
        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Brand</th>
                    <th>Condition</th>
                    <th>Location</th>
                    <th>Unit</th>
                    <th>Requested</th>
                    <th>Issued</th>
                </tr>
            </thead>
            <tbody>
                @foreach($issue->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->item->name }}</td>
                        <td class="text-center">{{ $item->brand->name ?? '-' }}</td>
                        <td class="text-center">{{ $item->condition->name ?? '-' }}</td>
                        <td class="text-center">{{ $item->location->name ?? '-' }}</td>
                        <td class="text-center">{{ $item->unit->name ?? '-' }}</td>
                        <td class="text-center">{{ $item->requested_qty }}</td>
                        <td class="text-center">{{ $item->issued_qty }}</td>
                    </tr>

                @endforeach
                <tr class="total-row">
                    <td colspan="7" class="text-right">Total Issued</td>
                    <td class="text-center">
                        {{ $issue->items->sum('issued_qty') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @if($issue->remark)
        <div class="section">
            <table>
                <tr class="table-header">
                    <th>Remarks</th>
                </tr>
                <tr>
                    <td class="remarks-box">
                        {{ $issue->remark }}
                    </td>
                </tr>
            </table>
        </div>
    @endif
    <div class="section">
        <table class="signature-table">

            <tr class="signature-header">
                <th>Received By (Staff)</th>
                <th>Store Incharge</th>
            </tr>

            {{-- EMPTY SPACE --}}
            <tr class="signature-space">
                <td></td>
                <td></td>
            </tr>
            {{-- NAMES --}}
            <tr class="signature-names">
                <td>
                    <strong>
                        {{ $issue->employee->first_name ?? '' }}
                        {{ $issue->employee->last_name ?? '' }}
                    </strong>
                </td>
                <td>
                    <strong>
                    </strong>
                </td>
            </tr>
        </table>
    </div>
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