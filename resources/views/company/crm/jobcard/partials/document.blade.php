{{-- HEADER --}}
<div class="section-company">
    <table width="100%">
        <tr>
            <td width="40%">
                <img src="{{ asset('admin/uploads/logo/' . $settings->logo) }}" style="width:300px;">
            </td>
            <td width="60%" style="text-align:right; font-size:13px; line-height:1.6">

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

                <div style="font-size:11px">
                    IEC: {{ $company->iec_code }} |
                    PAN: {{ $company->pan_no }} |
                    ESTD 1966
                </div>

            </td>
        </tr>
    </table>

    <div class="header-border"></div>
</div>


{{-- TITLE --}}
<div class="section-doc-label text-center doc-title">
    <h5>Job Card / Production Order</h5>
</div>

<div class="section-docinfo">
    <table class="table table-bordered docinfo-table">

        <tr>
            <th>उत्पादन आदेश सं. / PO No.:</th>
            <td>{{ $planning->po_number }}</td>

            <th>विभाग / Department:</th>
            <td>{{ $planning->department->name ?? '-' }}</td>
        </tr>

        <tr>
            <th>दिनांक / Date:</th>
            <td>{{ optional($planning->created_at)->format('d/m/y') }}</td>

            <th>उत्पादन प्रभारी / Incharge:</th>
            <td>
                {{ $planning->incharge->first_name ?? '' }}
                {{ $planning->incharge->last_name ?? '' }}
            </td>
        </tr>

        <tr>
            <th>ग्राहक / Customer:</th>
            <td>
                {{ $planning->order->quotation->lead->customer->name ?? '-' }}
            </td>

            <th>प्राथमिकता / Priority:</th>
            <td>
                <strong style="color:red;">
                    {{ strtoupper($planning->priority) }}
                </strong>
            </td>
        </tr>

        <tr>
            <th>बिक्री आदेश सं. / SO Ref:</th>
            <td>{{ $planning->order->order_number ?? '-' }}</td>

            <th>शिफ्ट / Shift:</th>
            <td>{{ ucfirst($planning->shift) }}</td>
        </tr>

        <tr>
            <th>डिलीवरी तिथि / Delivery:</th>
            <td>{{ optional($planning->delivery_date)->format('d/m/y') }}</td>

            <th>स्थिति / Status:</th>
           <td>
    <strong
        class="
        {{ $planning->status == 'completed' ? 'text-success' : '' }}
        {{ $planning->status == 'in_progress' ? 'text-warning' : '' }}
        {{ $planning->status == 'pending' ? '' : '' }}
        {{ $planning->status == 'cancelled' ? 'text-danger' : '' }}
    ">
        @switch($planning->status)
            @case('pending') लंबित / Pending @break
            @case('in_progress') प्रगति में / In Progress @break
            @case('completed') पूर्ण / Completed @break
            @case('cancelled') रद्द / Cancelled @break
        @endswitch
    </strong>
</td>
        </tr>
        <tr>
            <th>विशेष टिप्पणी / Remarks:</th>
            <td colspan="3" class="remark-bg translatable">
                {!! $planning->remark ?? '-' !!}
            </td>
        </tr>

    </table>
</div>

{{-- ITEMS --}}
<div class="section-items">
    <table class="items-table">
        <thead>
            <tr>
                <th>क्र.सं. <br> S.No</th>
                <th>मशीन का नाम <br> Machine Name</th>
                <th>विवरण (हिंदी) <br> Description</th>
                <th>सामग्री / Specs</th>
                <th>मात्रा <br> Qty</th>
                <!-- <th>इकाई / Unit</th> -->
                <th>कर्मचारी <br> Worker</th>
                <th>टिप्पणी / Remarks</th>
                <th>स्थिति <br> Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach($planning->items as $i => $item)
                <tr>
                    {{-- S.NO --}}
                    <td>{{ $i + 1 }}</td>

                    {{-- MACHINE --}}
                    <td class="translatable"> <b>
                        {{ $item->orderItem->machine->name 
                            ?? $item->orderItem->component->name 
                            ?? '-' }}
                            </b>
                    </td>

                    {{-- DESCRIPTION --}}
                    <td class="translatable">{{ $item->description ?? '-' }}</td>

                    {{-- SPECS --}}
                    <td class="translatable">{{ $item->specs ?? '-' }}</td>

                    {{-- QTY --}}
                    <td>{{ $item->qty }}</td>

                    {{-- UNIT --}}
                    <!-- <td>{{ $item->unit ?? '-' }}</td> -->

                    {{-- WORKER --}}
                    <td>
                        {{ $item->employee->first_name ?? '-' }}
                    </td>

                    {{-- REMARKS --}}
                    <td class="translatable">{{ $item->remarks ?? '-' }}</td>

                    {{-- STATUS --}}
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
</div>

{{-- REMARKS --}}
<div class="section-remarks">
    <table class="remarks-table">
            <th>विशेष निर्देश / SPECIAL INSTRUCTIONS:</th>
        <tr class="translatable">
            <td style="background:#f5e6c8; color:#1e4620;">{!! $planning->term ?? '-' !!}</td>
        </tr>
    </table>
</div>
{{-- SIGNATURE SECTION --}}
<div class="section-signatures">
    <table class="sign-table">
        <tr>
            <th>तैयार किया / Prepared By:</th>
            <th>जाँच / Checked By (QC):</th>
            <th>अनुमोदित / Approved By:</th>
        </tr>

        <tr style="height:60px;">
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            {{-- PREPARED BY (INCHARGE) --}}
            <td>
                {{ $planning->incharge->first_name ?? '' }}
                {{ $planning->incharge->last_name ?? '' }}
            </td>

            {{-- CHECKED BY --}}
            <td>
                {{ $planning->checkedBy->first_name ?? '' }}
                {{ $planning->checkedBy->last_name ?? '' }}
            </td>

            {{-- APPROVED BY (YOU CAN CHANGE LOGIC) --}}
            <td>
                Rishab Rai Khare
            </td>
        </tr>
    </table>
</div>