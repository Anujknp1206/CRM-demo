@if($plannings->count())
    @foreach($plannings as $i => $p)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $p->po_number }}</td>
            <td>{{ $p->order->customer_name ?? '-' }}</td>
            <td>{{ $p->incharge->first_name }} {{ $p->incharge->last_name }}</td>
            <td>{{ \Carbon\Carbon::parse($p->date)->format('d/m/Y') }}</td>
            <td>
                <span class="badge 
                                                                                {{ $p->status == 'pending' ? 'badge-secondary' :
                    ($p->status == 'in_progress' ? 'badge-warning' :
                        ($p->status == 'completed' ? 'badge-success' :
                            'badge-danger')) }}">
                    {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                </span>
            </td>
            <td>
                <span
                    class="badge {{ $p->priority == 'low' ? 'badge-secondary' :
                    ($p->priority == 'normal' ? 'badge-success' : ($p->priority == 'high' ? 'badge-warning' : 'badge-danger')) }}">{{ ucfirst($p->priority) }}
                </span>
            </td>
            <td class="d-flex gap-1">

                <a href="javascript:void(0)" class="btn btn-sm open-planning-modal" title="View Job Card"
                    data-id="{{ $p->id }}">
                    <i class="fa fa-eye"></i>
                </a>
                @can('print jobcard')
                    <a href="{{ route('jobcard.preview', [$company->id, $p->id]) }}" class="btn text-info btn-sm" target="_blank"
                        title="Print Job Card">
                        <i class="fa fa-print"></i>
                    </a>
                @endcan
                @can('edit jobcard')
                    {{-- ✏️ EDIT --}}
                    <a href="{{ route('jobcard.edit', [$company->id, $p->id]) }}" class="btn btn-sm text-success"
                        title="Edit Job Card">
                        <i class="fa fa-edit"></i>
                    </a>
                @endcan
                @can('delete jobcard')
                    {{-- 🗑 DELETE --}}
                    <form action="{{ route('jobcard.destroy', [$company->id, $p->id]) }}" method="POST"
                        style="display:inline-block;">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-sm text-danger delete-confirm" title="Delete Job Card">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                @endcan
            </td>
        </tr>
    @endforeach
@else
    <tr class="no-data">
        <td colspan="8" class="text-center"> 😢No Job Card Found</td>
    </tr>
@endif