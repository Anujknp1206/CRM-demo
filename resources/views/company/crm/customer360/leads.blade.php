@if($data->isEmpty())
    <div class="text-center text-muted py-4">
        No leads found for this customer
        <br>
        @can('add lead')
            <a href="{{ route('leads.create', request()->company) }}" class="btn btn-sm btn-primary">
                <i class="fa fa-plus"></i> Create Lead
            </a>
        @endcan
    </div>
@else
    <table class="table table-sm table-bordered">
        <thead class="bg-light">
            <tr>
                <th>Lead Code</th>
                <th>Status</th>
                <th>Last Action</th>
                <th>Next Action</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $lead)
                <tr>
                    <td>{{ $lead->lead_code }}</td>
                    <td>
                        <span class="badge badge-info">{{ ucfirst($lead->status) }}</span>
                    </td>
                    <td>{{ optional($lead->latestFollowup)->describeAction ?? '—' }}</td>
                    <td>
                        {{ optional($lead->latestFollowup)->nextactionDate
                    ? \Carbon\Carbon::parse($lead->latestFollowup->nextactionDate)->format('d/m/Y')
                    : '—'
                                                                    }}
                    </td>
                    <td>
                        <a title="View Lead Details" class="open-lead-modal" data-id="{{ $lead->id }}">
                            <i class="fa fa-search text-primary" style="cursor: pointer;"></i>
                        </a>
                        @can('edit lead')
                            <a title="Edit Lead" class="btn btn-sm"
                                href="{{ route('leads.edit', ['lead' => $lead->id, 'company' => $company->id]) }}"><i
                                    class="fa fa-edit text-secondary"></i></a>
                        @endcan
                        @can('add quotation')
                            <a href="{{ route('quotations.create', request()->company) }}?lead={{ $lead->id }}"
                                class="btn btn-sm text-success" title="Add Quotation For This Lead">
                                <i class="fa fa-plus"></i>
                            </a>
                        @endcan
                        @can('add followup')
                            <a href="javascript:void(0)" class="btn btn-sm text-primary open-followup-modal"
                                data-lead-id="{{ $lead->id }}" data-customer="{{ $lead->customer->name }}" title="Add Follow-up">
                                <i class="fas fa-comment-medical text-success"></i>
                            </a>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif