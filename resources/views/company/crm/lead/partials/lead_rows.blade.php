@forelse($leads as $lead)
    <tr>
        <td>
            {{ optional($lead->created_at)->format('d/m/Y h:i A') }}
        </td>
        <td>{{ $lead->lead_code }}</td>
        <td>{{ $lead->customer->name ?? '—' }}</td>

        <td>
            {{ $lead->customer->full_primary_mobile ?? '—' }}
        </td>

        <td>
            {{ $lead->customer->email ?? '—' }}
        </td>
        <td>
            {{ $lead->customer->address ?? '—' }}
        </td>
        <td>{{ $lead->purpose ?? '—' }}</td>
        <!-- <td>{{ optional($lead->creator)->name ?? '-----' }}</td> -->
        <td>
            {{ optional(optional($lead->latestFollowup)->action)->name ?? '-----' }}<br>
            @if($lead->status != 'new')
                <small class="text-success " style="font-weight: bold;">(Lead Confirmed)</small>
            @endif
        </td>
        <td>
            {{ optional($lead->latestFollowup)->nextactionDate ? \Carbon\Carbon::parse($lead->latestFollowup->nextactionDate)->format('d/m/Y') : '-----'}}
        </td>
        <td>
            <span
                class="badge @if($lead->status === 'new') badge-primary @elseif($lead->status === 'quoted') badge-success @elseif($lead->status === 'ordered') badge-success @elseif($lead->status === 'lost') badge-danger @else badge-secondary @endif">{{ ucfirst($lead->status) }}
            </span>
        </td>

        <td>
            <a class="open-customer-360 btn btn-sm" data-customer-id="{{ $lead->id }}" title="View Customer Details"
                data-customer-name="{{ $lead->customer->name }}"
                data-customer-mobile="{{ optional($lead->customer->primaryPhone)->phone }}">
                <i class="fa fa-user-circle" style="cursor: pointer;"></i>
            </a>

            @can('view lead')
                <a class="open-details-modal open-lead-modal btn btn-sm" data-id="{{ $lead->id }}" title="View Lead Details">
                    <i class="fa fa-search text-primary" style="cursor: pointer;"></i>
                </a>
            @endcan
            @can('edit lead')
                <a title="Edit Lead" class="btn btn-sm"
                    href="{{ route('leads.edit', ['lead' => $lead->id, 'company' => $company->id]) }}"><i
                        class="fa fa-edit text-green"></i></a>
            @endcan
            @can('view followups')
                <a title="View Followups" class="btn btn-sm"
                    href="{{ route('followups.index', ['lead_id' => $lead->id, 'company' => $company->id]) }}">
                    <i class="bi bi-card-list text-primary"></i>
                </a>
            @endcan
            @can('delete lead')
                <form action="{{ route('leads.destroy', ['lead' => $lead->id, 'company' => $company->id]) }}" method="POST"
                    style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button title="Delete Lead" class=" btn btn-sm delete-confirm" type="submit"
                        style="background:none;border:none;">
                        <i class="fa fa-trash text-red"></i>
                    </button>
                </form>
            @endcan
            @can('add quotation')
                <a href="{{ route('quotations.create', request()->company) }}?lead={{ $lead->id }}"
                    class="btn btn-sm text-success" title="Add Quotation For This Lead">
                    <i class="fa fa-plus"></i>
                </a>
            @endcan
            @can('add followup')
                @if($lead->status == 'new')
                    <a href="javascript:void(0)" class="btn btn-sm text-primary open-followup-modal" data-lead-id="{{ $lead->id }}"
                        data-customer="{{ $lead->customer->name }}" title="Add Follow-up">
                        <i class="fas fa-comment-medical text-success"></i>
                    </a>
                @endif
            @endcan

        </td>

    </tr>
@empty
    <tr>
        <td colspan="10" class="text-center">
            😢 No leads created today or matching your filters.
        </td>
    </tr>
@endforelse