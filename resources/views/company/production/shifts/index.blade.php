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
                <h3 class="card-title">{{ $label }}</h3>

                <div class="d-flex ml-auto" style="gap:8px;">
                    <button class="btn btn-default btn-sm" onclick="openCreateShift()">
                        <i class="fa fa-plus"></i> Add Shift
                    </button>
                    <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="btn btn-success btn-sm">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <div class="card-body">
                <table id="example1" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody id="shiftTable">
                        @forelse($shifts as $key => $s)
                            <tr id="shift-row-{{ $s->id }}">
                                <td>{{ $key + 1 }}</td>
                                <td class="shift-name">{{ $s->name }}</td>
                                <td class="shift-start">{{ $s->start_time }}</td>
                                <td class="shift-end">{{ $s->end_time }}</td>
                                <td>
                                    <a href="javascript:void(0)" class="edit-shift" data-id="{{ $s->id }}"
                                        data-name="{{ $s->name }}" data-start="{{ $s->start_time }}"
                                        data-end="{{ $s->end_time }}">
                                        <i class="fa fa-edit text-green"></i>
                                    </a>

                                    <button class="delete-confirm" data-id="{{ $s->id }}" data-name="{{ $s->name }}"
                                        style="border:none;background:none;">
                                        <i class="fa fa-trash text-red"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="no-shift-row">
                                <td colspan="5" class="text-center">😢 No shifts found</td>
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
        let shiftSaveUrl = "{{ route('shifts.store', $company->id) }}";

        function refreshShiftIndex() {
            $('#shiftTable tr[id^="shift-row"]').each(function (i) {
                $(this).find('td:first').text(i + 1);
            });
        }

        function openCreateShift() {
            $('#shiftForm')[0].reset();
            $('#shift_id').val('');
            shiftSaveUrl = "{{ route('shifts.store', $company->id) }}";
            $('#shiftModal').modal('show');
        }

        $(document).on('click', '.edit-shift', function () {
            let id = $(this).data('id');
            $('#shift_id').val(id);
            $('#shift_name').val($(this).data('name'));
            $('#shift_start').val($(this).data('start'));
            $('#shift_end').val($(this).data('end'));

            shiftSaveUrl = "{{ route('shifts.update', [$company->id, 'ID']) }}".replace('ID', id);
            $('#shiftModal').modal('show');
        });

        $('#shiftForm').submit(function (e) {
            e.preventDefault();

            let id = $('#shift_id').val();
            let method = id ? 'PUT' : 'POST';

            $.ajax({
                url: shiftSaveUrl,
                type: method,
                data: {
                    _token: "{{ csrf_token() }}",
                    name: $('#shift_name').val(),
                    start_time: $('#shift_start').val(),
                    end_time: $('#shift_end').val()
                },
                success: function (res) {
                    let s = res.shift;

                    if (id) {
                        $('#shift-row-' + s.id).find('.shift-name').text(s.name);
                        $('#shift-row-' + s.id).find('.shift-start').text(s.start_time);
                        $('#shift-row-' + s.id).find('.shift-end').text(s.end_time);
                    } else {
                        $('#no-shift-row').remove();

                        $('#shiftTable').append(`
                <tr id="shift-row-${s.id}">
                    <td></td>
                    <td class="shift-name">${s.name}</td>
                    <td class="shift-start">${s.start_time ?? ''}</td>
                    <td class="shift-end">${s.end_time ?? ''}</td>
                    <td>
                        <a href="#" class="edit-shift"
                            data-id="${s.id}"
                            data-name="${s.name}"
                            data-start="${s.start_time}"
                            data-end="${s.end_time}">
                            <i class="fa fa-edit text-green"></i>
                        </a>
                        <button class="delete-confirm"
                            data-id="${s.id}"
                            data-name="${s.name}" style="border:none;background:none;">
                            <i class="fa fa-trash text-red"></i>
                        </button>
                    </td>
                </tr>
                `);
                    }

                    refreshShiftIndex();
                    $('#shiftModal').modal('hide');

                    // ✅ toast added
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: id ? 'Shift updated' : 'Shift added',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });
        $(document).on('click', '.delete-confirm', function () {

            let id = $(this).data('id');
            let name = $(this).data('name') ?? 'this shift';

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
                        url: "{{ route('shifts.destroy', [$company->id, 'ID']) }}".replace('ID', id),
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function () {

                            $('#shift-row-' + id).fadeOut(300, function () {
                                $(this).remove();

                                refreshShiftIndex();

                                // ✅ empty check
                                if ($('#shiftTable tr[id^="shift-row"]').length === 0) {
                                    $('#shiftTable').html(`
                                                <tr id="no-shift-row">
                                                    <td colspan="5" class="text-center">
                                                        😢 No shifts found
                                                    </td>
                                                </tr>
                                            `);
                                }
                            });

                            // ✅ toast
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Shift deleted',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    });

                }
            });
        });</script>
@endpush