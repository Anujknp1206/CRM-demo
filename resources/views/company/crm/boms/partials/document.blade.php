<div class="section-company toggle-company">
    <table width="100%">
        <tr>
            <td width="40%">
                <img src="{{ asset('admin/uploads/logo/' . $settings->logo) }}" class="company-logo">
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
                    GST In: {{ $company->gstin_no }} |
                    IEC: {{ $company->iec_code }} |
                    PAN: {{ $company->pan_no }} |
                    ESTD 1966
                </div>
            </td>
        </tr>
    </table>
    <div class="header-border"></div>
</div>
<div class="section-doc-label text-center doc-title toggle-docheading">
    <h5 class="doc-label doc-bom">
        BOM (Bill Of Materials)
    </h5>
</div>
<div class="section-docinfo toggle-docinfo">
    <table class="docinfo-table">

        <tr>
            <th>BOM No:</th>
            <td>{{ $bom->bom_number ?? '-' }}</td>


            <th>Department :</th>
            <td class="translatable-area" data-key="department_name">{{ $bom->department->name ?? '--' }}</td>
        </tr>

        <tr>
            <th>BOM Date:</th>
            <td>{{ optional($bom->created_at)->format('d/m/y') }}</td>

            <th>Incharge:</th>
            <td class="translatable-area" data-key="incharge_name">{{ $bom->supervisor->first_name ?? '-' }}
                {{ $bom->supervisor->last_name ?? '-' }}
            </td>
        </tr>

        <tr>

            <th>Customer Name:</th>
            <td class="translatable-area" data-key="customer_name">{{ $bom->order->customer_name ?? '-' }}</td>


            <th>Priority:</th>
            <td style="color:red; font-weight:600;" class="translatable-area" data-key="priority_name">
                {{ strtoupper($bom->priority->name ?? '--') }}
            </td>
        </tr>

        <tr>

            <th>Order Ref No:</th>
            <td>{{ $bom->order->order_number ?? '-' }}</td>

            <th>Shift:</th>
            <td class="translatable-area" data-key="shift_name">{{ $bom->shift->name ?? '--' }}</td>
        </tr>

        <tr>

            <th>Delivery Date:</th>
            <td>{{ $bom->delivery_date_formatted ?? '-' }}</td>

            <th>BOM Status:</th>
            <td><strong>
                    {{ ucfirst(str_replace('_', ' ', $bom->status)) }}
                </strong>
            </td>
        </tr>

    </table>
</div>
<table class="items-table mt-3 toggle-items">
    <thead>
        <tr>
            <th class="col-sn">#</th>
            <th class="col-code">Item Code</th>
            <th class="col-part">Item Name</th>
            <!-- <th class="col-specs">Spec</th> -->
            <th class="col-qty" style="text-align:center;">Qty</th>
            <th class="col-notes">Notes</th>
        </tr>
    </thead>
    @php $serial = 1; @endphp
    <tbody>

        {{-- 🔹 PARTS --}}
        @foreach($bom->order->items as $index => $orderItem)
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
                    <tr style="background:#eaf2f8; font-weight:600;">
                        <td colspan="8">
                            <div style="display:flex;justify-content:space-between;align-items:center;gap:40px;">

                                <div>
                                    <strong>Part Name:</strong>

                                    <span class="translatable-area" data-en="{{ $part->part_name }}"
                                        data-hi="{{ $part->hi_part_name ?? $part->part_name }}">

                                        {{ $part->part_name }}
                                    </span>
                                </div>
                                @if($part->spec)

    <div>
        <strong>Spec:</strong>

        <span class="translatable-area"
              data-key="spec_value_{{ $part->id }}">

            {{ $part->spec->name ?? '-' }}
        </span>
    </div>

@endif
                            </div>

                        </td>
                    </tr>

                    {{-- 🔹 PART ITEMS --}}
                    @foreach($partItems as $pIndex => $bomItem)

                        <tr>

                            <td class="col-sn bom-serial">{{ $serial++ }}</td>
                            <td class="col-code">{{ $bomItem->item->code ?? '-' }}</td>
                            <td class="col-part translatable-area" data-en="{{ $bomItem->item->name ?? '-' }}"
                                data-hi="{{ $bomItem->item->hi_name ?? ($bomItem->item->name ?? '-') }}">

                                {{ $bomItem->item->name ?? '-' }}

                            </td>
                            <!-- <td class="col-specs translatable-area" data-key="spec_{{ $bomItem->id }}">{{ $part->spec->name ?? '-' }} -->
                            <!-- </td> -->
                            <td class="col-qty">{{ $bomItem->quantity }}</td>
                            <td class="col-notes translatable-area" data-en="{!! $bomItem->notes ?? '-'!!}"
                                data-hi="{!! $bomItem->hi_notes ?? $bomItem->notes ?? '-' !!}">

                                {!! $bomItem->notes ?? '-' !!}

                            </td>
                        </tr>

                    @endforeach

                @endif

            @endforeach

        @endforeach

    </tbody>
</table>
{{-- ===============================
SPECIAL INSTRUCTIONS
=============================== --}}
<div class="section-instructions toggle-instructions">
    <table class="instructions-table">

        <tr class="instructions-header">
            <td>
                SPECIAL INSTRUCTIONS:
            </td>
        </tr>

        <tr class="instructions-body">

            <td class="translatable-area" data-en="{!! $bom->remarks ?? '-' !!}"
                data-hi="{!! $bom->hi_remarks ?? $bom->remarks ?? '-' !!}">

                {!! $bom->remarks ?? '-' !!}

            </td>

        </tr>

    </table>
</div>

{{-- ===============================
SIGNATURE SECTION
=============================== --}}
<div class="section-signature toggle-signature">
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
            <td class="translatable-area" data-key="prepared_by">{{ $bom->supervisor->first_name ?? '-' }}
                {{ $bom->supervisor->last_name ?? '-' }}
            </td>
            <td class="translatable-area" data-key="checked_by">{{ $bom->checker->first_name ?? '-' }}
                {{ $bom->checker->last_name ?? '-' }}
            </td>
            <td class="translatable-area" data-key="approved_by">{{ $user->name ?? 'Rishabh Rai Khare' }}</td>
        </tr>
    </table>
</div>