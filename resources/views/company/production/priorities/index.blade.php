@extends('company.layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('company.dashboard', ['company' => $company->id]) }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $label }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card card-teal">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">{{$label}}</h3>
                <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                    @can('add units')
                        <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#priorityModal"
                            onclick="openCreatePriority()">
                            <i class="fa fa-plus"></i> Add Priority
                        </button>
                    @endcan
                    <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Priority Name</th>
                            <th>Level</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody id="priorityTable">
                        @forelse($priorities as $key => $p)
                            <tr id="priority-row-{{ $p->id }}">
                                <td>{{ $key + 1 }}</td>
                                <td class="priority-name">{{ $p->name }}</td>
                                <td class="priority-level">{{ $p->level }}</td>
                                <td>
                                    <a href="javascript:void(0)" class="edit-priority" data-id="{{ $p->id }}"
                                        data-name="{{ $p->name }}" data-level="{{ $p->level }}">
                                        <i class="fa fa-edit text-green"></i>
                                    </a>
                                    <button class="delete-confirm" data-id="{{ $p->id }}" data-name="{{ $p->name }}"
                                        style="border:none;background:none;" title="Delete Priority">
                                        <i class="fa fa-trash text-red"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="no-priority-row">
                                <td colspan="4" class="text-center"> 😢 No priorities found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
@push('style')
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/summernote/summernote-bs4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script> 
        let prioritySaveUrl = "{{ route('priorities.store', $company->id) }}";
        function refreshPriorityIndex() {
            $('#priorityTable tr[id^="priority-row"]').each(function (index) {
                $(this).find('td:first').text(index + 1);
            });
        }
        /* OPEN CREATE */
        function openCreatePriority() {
            $('#priorityModalTitle').text('Add Priority');
            $('#priorityForm')[0].reset();
            $('#priority_id').val('');
            prioritySaveUrl = "{{ route('priorities.store', $company->id) }}";
            $('#priorityModal').modal('show');
        }

        /* OPEN EDIT */
        $(document).on('click', '.edit-priority', function () {
            let id = $(this).data('id');

            $('#priorityModalTitle').text('Edit Priority');
            $('#priority_id').val(id);
            $('#priority_name').val($(this).data('name'));
            $('#priority_level').val($(this).data('level'));

            prioritySaveUrl = "{{ route('priorities.update', [$company->id, 'ID']) }}".replace('ID', id);

            $('#priorityModal').modal('show');
        });

        /* SAVE */
        $('#priorityForm').submit(function (e) {
            e.preventDefault();

            let id = $('#priority_id').val();
            let method = id ? 'PUT' : 'POST';

            $('#prioritySaveBtn').prop('disabled', true);

            $.ajax({
                url: prioritySaveUrl,
                type: method,
                data: {
                    _token: "{{ csrf_token() }}",
                    name: $('#priority_name').val(),
                    level: $('#priority_level').val()
                },
                success: function (res) {

                    let p = res.priority;

                    if (id) {
                        $('#priority-row-' + p.id).find('.priority-name').text(p.name);
                        $('#priority-row-' + p.id).find('.priority-level').text(p.level);
                    } else {
                        $('#no-priority-row').remove();
                        $('#priorityTable').append(`
        <tr id="priority-row-${p.id}">
            <td></td>
            <td class="priority-name">${p.name}</td>
            <td class="priority-level">${p.level}</td>
            <td>
                <a href="#" class="edit-priority"
                   data-id="${p.id}"
                   data-name="${p.name}"
                   data-level="${p.level}">
                    <i class="fa fa-edit text-green"></i>
                </a>
              
                 <button class="delete-confirm"  data-id="${p.id}"
                    data-name="${p.name}"
                                        style="border:none;background:none;" title="Delete Priority">
                                        <i class="fa fa-trash text-red"></i>
                                    </button>
            </td>
        </tr>
    `);

                        // ✅ ADD THIS
                        refreshPriorityIndex();
                    }

                    $('#priorityModal').modal('hide');
                    $('#prioritySaveBtn').prop('disabled', false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Priority saved successfully',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        $(document).on('click', '.delete-confirm', function () {

            let id = $(this).data('id');
            let name = $(this).data('name') ?? 'this item';

            Swal.fire({
                title: 'Are you sure?',
                text: `Delete ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('priorities.destroy', [$company->id, 'ID']) }}".replace('ID', id),
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },

                        success: function () {

                            // remove row
                            $('#priority-row-' + id).fadeOut(300, function () {
                                $(this).remove();

                                // 🔥 check if table is empty
                                if ($('#priorityTable tr[id^="priority-row"]').length === 0) {

                                    $('#priorityTable').html(`
                                        <tr id="no-priority-row">
                                            <td colspan="4" class="text-center">
                                                😢 No priorities found
                                            </td>
                                        </tr>
                                    `);
                                }
                            });

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted successfully',
                                timer: 1200,
                                showConfirmButton: false
                            });
                        }
                    });

                }
            });
        });</script>
@endpush