@if($data->isEmpty())
    <div class="text-center text-muted py-4">
        No quotations found
        <br><br>

        @if(isset($lead))
            @can('add quotation')
                <a href="{{ route('quotations.create', request()->company) }}?lead={{ $lead->id }}" class="btn btn-sm btn-success">
                    <i class="fa fa-plus"></i> Create Quotation
                </a>
            @endcan
        @else
            <span class="text-danger small">
                ⚠ Create a lead first to add quotation
            </span>
        @endif
    </div>
@else
    <table class="table table-sm table-bordered">
        <thead class="bg-light">
            <tr>
                <th>Quotation No</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $q)
                <tr>
                    <td>{{ $q->quote_number }}</td>
                    <td>
                        {{ $q->quote_date
                    ? \Carbon\Carbon::parse($q->quote_date)->format('d/m/Y')
                    : '—' 
                                                        }}
                    </td>
                    <td>{{ $q->currency_symbol ?? '' }}{{ number_format($q->final_amount, 2) }}</td>
                    <td>
                        <span class="badge badge-success">{{ ucfirst($q->status) }}</span>
                    </td>
                    <td>
                        <!-- <a title="View Quotation Details" class="open-quotation-modal" data-id="{{ $q->id }}"
                            style="cursor:pointer;">
                            <i class="fa fa-search text-info"></i>
                        </a> -->
                        @can('print quotation')

                            <a href="{{ route('quotations.print', [$company->id, $q->id]) }}" title="Print Quotation"
                                class="btn btn-sm" target="_blank" style="cursor:pointer;">
                                <i class="fa fa-print text-warning ml-2"></i>
                            </a>

                        @endcan
                        @can('edit quotation')
                            <a href="{{ route('quotations.edit', [$company->id, $q->id]) }}" title="Edit Quotation"
                                class="btn btn-sm" target="_blank" style="cursor:pointer;">
                                <i class="fa fa-edit text-secondary ml-2"></i>
                            </a>
                        @endcan
                        @can('add order')
                            <a href="{{ route('orders.create', request()->company) }}?quotation={{ $q->id }}"
                                title="Add Order For This Quotation" class="btn btn-sm text-success">
                                <i class="fa fa-plus"></i>
                            </a>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif