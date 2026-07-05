<!DOCTYPE html>
<html>

<head>
    <title>Return Slip</title>

    <style>
        @page {
            margin: 130px 40px 110px 40px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
        }

        .section {
            margin-bottom: 10px;
        }

        .no-border td {
            border: none;
            color: #2b4c7e;
            padding: 2px 4px;
        }

        .doc-title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            color: #4A4A4A;
            background: #e9e9e9;
            padding: 3px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #cfcfcf;
            padding: 4px 6px;
        }

        .items-table th,
        .table-header {
            background: #4A4A4A;
            color: #fff;
        }

        .info-table th {
            background: #f1f1f1;
            color: #2b4c7e;
            width: 20%;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total-row td {
            font-weight: bold;
            background: #f3f3f3;
        }

        .signature-header {
            background: #4A4A4A;
            color: #fff;
        }

        .signature-space td {
            height: 60px;
        }

        .signature-names td {
            text-align: center;
            background: #fdecec;
        }

        .pdf-footer {
            position: fixed;
            bottom: -90px;
            left: 0;
            right: 0;
        }

        .footer-left {
            background: #4A4A4A;
            color: #fff;
            padding: 8px;
            font-size: 9px;
        }

        .footer-right {
            background: #4A4A4A;
            color: #fff;
            padding: 8px;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
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
        <div>
            <div style="height:6px;background:#2cca38;"></div>
            <div style="height:6px;background:#0b3d6d;"></div>

        </div>

    </div>
    <div class="doc-title">
        RETURN SLIP
    </div>

    {{-- RETURN INFO --}}
    <div class="section">
        <table class="info-table">
            <tr>
                <th>Issue No</th>
                <td>{{ $return->issue->issue_no }}</td>

                <th>BOM No</th>
                <td>{{ $return->issue->bom->bom_number ?? '-' }}</td>

            </tr>
            <tr>

                <th>Return Date</th>
                <td>{{ \Carbon\Carbon::parse($return->return_date)->format('d/m/Y') }}</td>
                <th>Time</th>
                <td>{{ \Carbon\Carbon::parse($return->created_at)->format('h:i A') }}</td>
            </tr>

            <tr>
            <tr>
                <th>Department</th>
                <td>{{$return->issue->department->name ?? '-' }}</td>

                <th>Assigned To:</th>
                <td>
                    {{ $return->issue->employee->first_name ?? '' }}
                    {{ $return->issue->employee->last_name ?? '' }}
                </td>
            </tr>
            </tr>
        </table>
    </div>
    {{-- ITEM --}}
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
                    <th>Returned Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($return->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->item->name }}</td>
                        <td class="text-center">{{ $item->brand->name ?? '-' }}</td>
                        <td class="text-center">{{ $item->condition->name ?? '-' }}</td>
                        <td class="text-center">{{ $item->location->name ?? '-' }}</td>
                        <td class="text-center">{{ $item->unit->name ?? '-' }}</td>
                        <td class="text-center">{{ $item->return_qty }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="6" class="text-right">Total Returned</td>
                    <td class="text-center">
                        {{ $return->items->sum('return_qty') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- SIGNATURE --}}
    <div class="section">
        <table>
            <tr class="signature-header">
                <th>Returned By</th>
                <th>Store Incharge</th>
            </tr>

            <tr class="signature-space">
                <td></td>
                <td></td>
            </tr>

            <tr class="signature-names">
                <td>
                    {{ $return->issue->employee->first_name ?? '' }}
                    {{ $return->issue->employee->last_name ?? '' }}
                </td>
                <td></td>
            </tr>
        </table>
    </div>

    {{-- FOOTER --}}
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