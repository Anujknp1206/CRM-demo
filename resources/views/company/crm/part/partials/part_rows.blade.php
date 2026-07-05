@forelse($parts as $key => $part)

    <tr>

        <td>{{ $key + 1 }}</td>

        <td>

            <strong>
                {{ $part->name }}
                @if ($part->hi_name)
                    /{{ $part->hi_name }}
                @endif
            </strong>

            <br>

            <small class="text-muted">
                {{ $part->code ?? 'N/A' }}
            </small>

        </td>

        <td>

            <span class="badge badge-info">
                {{ $part->items->count() }}
            </span>

        </td>

        <td>

            {{ $part->created_at->format('d/m/Y') }}

        </td>

        <td>

            <div class="btn-group">

                {{-- VIEW MODAL --}}
                <button type="button" class="btn btn-sm viewPartBtn" data-id="{{ $part->id }}">

                    <i class="fa fa-eye"></i>

                </button>

                {{-- EDIT --}}
                @can('edit parts')

                    <a href="{{ route('parts.edit', [$company->id, $part->id]) }}" class="btn text-success btn-sm">

                        <i class="fa fa-edit"></i>

                    </a>

                @endcan

                {{-- DELETE --}}
                @can('delete parts')

                    <form action="{{ route('parts.destroy', [$company->id, $part->id]) }}" method="POST"
                        class="deletePartForm d-inline">

                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn text-danger btn-sm">

                            <i class="fa fa-trash"></i>

                        </button>

                    </form>

                @endcan

            </div>

        </td>

    </tr>

@empty
    <tr class="no-data">
        <td colspan="6" class="text-center py-4">

            <div class="text-muted">

                <i class="fa fa-folder-open fa-2x mb-2"></i>

                <h6 class="mb-1">
                    No Parts Found, No records matched your filters.
                </h6>
            </div>
        </td>
    </tr>

@endforelse