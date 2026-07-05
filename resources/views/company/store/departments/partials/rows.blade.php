@forelse($departments as $key => $dept)
    <tr id="department-row-{{ $dept->id }}">
        <td>{{ $key + 1 }}</td>
        <td class="dept-name">{{ $dept->name }}</td>
        <td>
            @can('edit department')
                <a href="javascript:void(0)" class="edit-department" data-id="{{ $dept->id }}" data-name="{{ $dept->name }}"
                    title="Edit Department">
                    <i class="fa fa-edit text-green"></i>
                </a>
            @endcan
            @can('delete department')
                <button class="delete-department" data-id="{{ $dept->id }}" data-name="{{ $dept->name }}"
                    style="border:none;background:none" title="Delete Department">
                    <i class="fa fa-trash text-red"></i>
                </button>
            @endcan
        </td>
    </tr>
@empty
    <tr id="no-department-row">
        <td colspan="3" class="text-center">😢 No departments found</td>
    </tr>
@endforelse