@foreach($employees as $e)
    @php
        $attendance = $e->attendances->first();
        $isPresent = $attendance && $attendance->is_present; // ✅ FIX
    @endphp

    <tr data-employee="{{ $e->id }}">
        <!-- Serial -->
        <td>{{ $loop->iteration }}</td>

        <!-- Employee -->
        <td>{{ $e->first_name }} {{ $e->last_name }}</td>

        <!-- Department -->
        <td>{{ $e->department->name ?? '-' }}</td>

        <!-- Status -->
        <td>
            @if($isPresent)
                <span class="badge badge-success">Present</span>
            @else
                <span class="badge badge-danger">Absent</span>
            @endif
        </td>

        <!-- Action -->
        <td>
            <button class="btn mark-attendance {{ $isPresent ? 'btn-success' : 'btn-danger' }}" data-id="{{ $e->id }}"
                data-present="{{ $isPresent ? 1 : 0 }}">
                {{ $isPresent ? 'Mark Absent' : 'Mark Present' }}
            </button>
        </td>
    </tr>
@endforeach

{{-- EMPTY STATE --}}
@if($employees->isEmpty())
    <tr>
        <td colspan="5" class="text-center"> 😢 No Data</td>
    </tr>
@endif