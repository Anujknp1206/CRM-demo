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
                <h3>{{ $label }}</h3>

                <div class="d-flex align-items-center ml-auto" style="gap:8px;">
                    <button class="btn btn-default btn-sm" onclick="openCreateSpec()">
                        <i class="fa fa-plus"></i> Add Specification
                    </button>

                    <!-- ✅ BACK BUTTON -->
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
                            <th width="120">Action</th>
                        </tr>
                    </thead>

                    <tbody id="specTable">
                        @forelse($specifications as $key => $s)
                            <tr id="spec-row-{{ $s->id }}">
                                <td>{{ $key + 1 }}</td>
                                <td class="spec-name">{{ $s->name }}</td>
                                <td>
                                    <a href="javascript:void(0)" class="edit-spec" data-id="{{ $s->id }}"
                                        data-name="{{ $s->name }}">
                                        <i class="fa fa-edit text-green"></i>
                                    </a>

                                    <button class="delete-confirm" data-id="{{ $s->id }}" style="border:none;background:none;">
                                        <i class="fa fa-trash text-red"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="no-spec-row">
                                <td colspan="3" class="text-center">😢 No specifications found</td>
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
        let specSaveUrl = "{{ route('specifications.store', $company->id) }}";

        function refreshSpecIndex() {
            $('#specTable tr[id^="spec-row"]').each(function (i) {
                $(this).find('td:first').text(i + 1);
            });
        }

        function openCreateSpec() {
            $('#specForm')[0].reset();
            $('#spec_id').val('');
            specSaveUrl = "{{ route('specifications.store', $company->id) }}";
            $('#specsModal').modal('show');
        }

        $(document).on('click', '.edit-spec', function () {
            let id = $(this).data('id');
            $('#spec_id').val(id);
            $('#spec_name').val($(this).data('name'));

            specSaveUrl = "{{ route('specifications.update', [$company->id, 'ID']) }}".replace('ID', id);
            $('#specsModal').modal('show');
        });

        $('#specForm').submit(function (e) {
            e.preventDefault();

            let id = $('#spec_id').val();
            let method = id ? 'PUT' : 'POST';

            $.ajax({
                url: specSaveUrl,
                type: method,
                data: {
                    _token: "{{ csrf_token() }}",
                    name: $('#spec_name').val()
                },
                success: function (res) {

                    let s = res.spec;

                    if (id) {
                        $('#spec-row-' + s.id).find('.spec-name').text(s.name);
                    } else {
                        $('#no-spec-row').remove();

                        $('#specTable').append(`
                        <tr id="spec-row-${s.id}">
                            <td></td>
                            <td class="spec-name">${s.name}</td>
                            <td>
                                <a href="#" class="edit-spec"
                                   data-id="${s.id}"
                                   data-name="${s.name}">
                                    <i class="fa fa-edit text-green"></i>
                                </a>
                                <button class="delete-confirm"
                                    data-id="${s.id}"style="border:none;background:none;">
                                    <i class="fa fa-trash text-red"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                    }

                    refreshSpecIndex();
                    $('#specsModal').modal('hide');

                    // ✅ toast
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: id ? 'Specification updated' : 'Specification added',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });

        $(document).on('click', '.delete-confirm', function () {

            let id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: 'Delete this specification?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('specifications.destroy', [$company->id, 'ID']) }}".replace('ID', id),
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },

                        success: function () {

                            $('#spec-row-' + id).fadeOut(300, function () {
                                $(this).remove();

                                refreshSpecIndex();

                                // ✅ empty check
                                if ($('#specTable tr[id^="spec-row"]').length === 0) {
                                    $('#specTable').html(`
                                            <tr id="no-spec-row">
                                                <td colspan="3" class="text-center">
                                                    😢 No specifications found
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
                                title: 'Specification deleted',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    });

                }
            });
        });   </script>
@endpush