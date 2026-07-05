@forelse($quotations as $quotation)
    <tr>
        <td>{{ $loop->iteration }}</td>
        @php

            // 🔥 Convert everything to INR
            $amount = ($quotation->currency === 'INR')
                ? $quotation->total_amount
                : ($quotation->total_amount * $quotation->conversion_rate);

            if ($amount < 700000) {

                $slab = 'Lower';
                $badge = 'badge-secondary';

                $bg = 'linear-gradient(135deg,#f1f3f5,#dee2e6)';
                $border = '#6c757d';
                $text = '#495057';

            } elseif ($amount <= 1500000) {

                $slab = 'Medium';
                $badge = 'badge-info';

                $bg = 'linear-gradient(135deg,#e3f8ff,#cceeff)';
                $border = '#17a2b8';
                $text = '#117a8b';

            } elseif ($amount <= 7000000) {

                $slab = 'High';
                $badge = 'badge-warning';

                $bg = 'linear-gradient(135deg,#fff8db,#ffe8a1)';
                $border = '#f0ad4e';
                $text = '#b9770e';

            } else {

                $slab = 'Premium';
                $badge = 'badge-danger';

                $bg = 'linear-gradient(135deg,#ffe5e5,#ffb3b3)';
                $border = '#dc3545';
                $text = '#b02a37';
            }

        @endphp
        <td class="text-center">

            <!-- SLAB BADGE -->
            <div>

                <span class="badge {{ $badge }}" style="
                                    min-width: 70px;
                                    padding: 7px 10px;
                                    font-size: 12px;
                                    font-weight: 700;
                                    border-radius: 20px;
                                    display: inline-block;
                                    text-align: center;
                                    letter-spacing: .5px;
                                  ">

                    {{ strtoupper($slab) }}

                </span>

            </div>
            @can('view quotation amount')

                <div class="mt-2 p-2 rounded" style="
                    background: {{ $bg }};
                    border: 1px solid {{ $border }};
                    box-shadow: 0 1px 3px rgba(0,0,0,.08);
                 ">

                    <div style="
                        font-size: 17px;
                        font-weight: 800;
                        color: {{ $text }};
                        line-height: 1.2;
                    ">

                        {{ $quotation->currency_symbol ?? '' }}
                        {{ number_format($quotation->final_amount ?? 0, 2) }}

                    </div>

                </div>

            @endcan
        </td>
        <td>{{ $quotation->quote_number }}</td>
        <td>{{ $quotation->lead?->customer?->name ?? '---' }}</td>
        <td>
            {{ $quotation->lead->customer->full_primary_mobile ?? '—' }}
        </td>

        <td>{{ \Carbon\Carbon::parse($quotation->quote_date)->format('d-m-Y') }}</td>
        <td>
            <span class="badge 
                                                                    @if($quotation->status === 'draft') badge-secondary
                                                                    @elseif($quotation->status === 'sent') badge-info
                                                                    @elseif($quotation->status === 'converted') badge-success
                                                                    @elseif($quotation->status === 'rejected') badge-danger
                                                                    @else badge-dark
                                                                    @endif
                                                                ">
                {{ ucfirst($quotation->status) }}
            </span>
        </td>
        <td>
            <a class="open-customer-360 btn btn-sm" data-customer-id="{{ $quotation->lead_id }}"
                data-customer-name="{{ $quotation->lead->customer->name }}"
                data-customer-mobile="{{ optional($quotation->lead->customer->primaryPhone)->phone }}">
                <i class="fa fa-user-circle" style="cursor: pointer;"></i>
            </a>
            @can('view quotation')
                <a class="open-details-modal open-quotation-modal btn btn-sm" data-id="{{ $quotation->id }}"
                    title="View Quotation Details">
                    <i class="fa fa-search text-primary" style="cursor: pointer;"></i>
                </a>
            @endcan
            @can('edit quotation')
                <a title="Edit Quotation" class="btn btn-sm"
                    href="{{ route('quotations.edit', [$company->id, $quotation->id]) }}">
                    <i class="fa fa-edit text-green"></i>
                </a>
            @endcan
            @can('print quotation')

                <a href="{{ route('quotations.print', [$company->id, $quotation->id]) }}" title="Print Quotation"
                    class="btn btn-sm" target="_blank" style="cursor:pointer;">
                    <i class="fa fa-print text-warning ml-2"></i>
                </a>

            @endcan

            @can('duplicate quotation')
                <a title="Duplicate Quotation" class="btn btn-sm"
                    href="{{ route('quotations.duplicate', [$company->id, $quotation->id]) }}">
                    <i class="fa fa-copy text-primary ml-2"></i>
                </a>
            @endcan
            @can('delete quotation')
                <form action="{{ route('quotations.destroy', [$company->id, $quotation->id]) }}" method="POST"
                    style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button title="Delete Quotation" class="delete-confirm btn btn-sm" type="submit"
                        style="background:none;border:none;">
                        <i class="fa fa-trash text-red"></i>
                    </button>
                </form>
            @endcan
            @can('add order')
                <a href="{{ route('orders.create', request()->company) }}?quotation={{ $quotation->id }}"
                    title="Add Order For This Quotation" class="btn btn-sm text-success">
                    <i class="fa fa-plus"></i>
                </a>
            @endcan
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center">
            😢 No quotations created today or matching your filters.
        </td>
    </tr>
@endforelse