@forelse($rfis as $rfi)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>
            <strong>{{ $rfi->rfi_code }}</strong>
        </td>

        {{-- DATE --}}
        <td>
            {{ \Carbon\Carbon::parse($rfi->rfi_date)->format('d/m/Y') }}
        </td>

        {{-- TOTAL ITEMS --}}
        <td class="text-center">
            <span class="badge badge-info">
                {{ $rfi->items_count }}
            </span>
        </td>
        <td>
            ₹ {{ number_format($rfi->total_amount, 2) }}
        </td>
        {{-- STATUS --}}
        <td>
            <span class="badge 
                                                        @if($rfi->status === 'pending') badge-warning
                                                        @elseif($rfi->status === 'approved') badge-success
                                                        @elseif($rfi->status === 'rejected') badge-danger   
                                                        @else badge-secondary
                                                        @endif
                                                    ">
                {{ ucfirst($rfi->status) }}
            </span>
        </td>

        {{-- ACTION --}}
        <td class="text-center">

            {{-- VIEW --}}
            @if($rfi->status != 'pending')
                <button class="btn text-info btn-sm view-summary" data-id="{{ $rfi->id }}" title="View RFI Details"> <i class="fas fa-list"></i>
                </button>
            @endif
            @if($rfi->status === 'pending')
                <a href="javascript:void(0)" class="btn btn-sm view-rfi" data-id="{{ $rfi->id }}" title="Approve/Reject RFI">
                  <i class="fa fa-gavel"></i>
                </a>
                <a href="javascript:void(0)" class="btn btn-sm text-primary edit-rfi" data-id="{{ $rfi->id }}" title="Edit RFI">
                    <i class="fa fa-edit"></i>
                </a>
            @endif
        </td>

    </tr>

@empty
    <tr>
        <td colspan="7" class="text-center">
            😢 No RFIs found.
        </td>
    </tr>
@endforelse