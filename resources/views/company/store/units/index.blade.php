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
                        <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#unitModal"
                            onclick="openCreateUnit()">
                            <i class="fa fa-plus"></i> Add Unit
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
                            <th>Unit Name</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody id="unitTable">
                        @forelse($units as $key => $unit)
                            <tr id="unit-row-{{ $unit->id }}">
                                <td>{{ $key + 1 }}</td>

                                <td class="unit-name">{{ $unit->name }}</td>

                                <td>
                                    @can('edit units')
                                        <a href="javascript:void(0)" class="edit-unit" data-id="{{ $unit->id }}"
                                            data-name="{{ $unit->name }}" title="Edit Unit">
                                            <i class="fa fa-edit text-green"></i>
                                        </a>
                                    @endcan

                                    @can('delete units')
                                        <button type="button" class="delete-confirm" data-id="{{ $unit->id }}"
                                            data-name="{{ $unit->name }}" style="border:none;background:none" title="Delete Unit">
                                            <i class="fa fa-trash text-red"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr id="no-unit-row">
                                <td colspan="3" class="text-center"> 😢 No units found</td>
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
        let saveUrl = "{{ route('units.store', $company->id) }}";

        /* =========================
           HELPERS
        ========================== */
        function refreshUnitIndex() {
            $('#unitTable tr').each(function (index) {
                $(this).find('td:first').text(index + 1);
            });
        }

        /* =========================
           OPEN CREATE
        ========================== */
        function openCreateUnit() {
            $('#unitModalTitle').text('Add Unit');
            $('#unitForm')[0].reset();
            $('#unit_id').val('');
            saveUrl = "{{ route('units.store', $company->id) }}";
            $('#unitModal').modal('show');
        }

        /* =========================
           OPEN EDIT
        ========================== */
        function openEditUnit(id, name) {
            $('#unitModalTitle').text('Edit Unit');
            $('#unit_id').val(id);
            $('#unit_name').val(name);
            saveUrl = "{{ route('units.update', [$company->id, 'ID']) }}".replace('ID', id);
            $('#unitModal').modal('show');
        }

        $(document).on('click', '.edit-unit', function () {
            openEditUnit($(this).data('id'), $(this).data('name'));
        });

        /* =========================
           SUBMIT FORM
        ========================== */
        $('#unitForm').submit(function (e) {
            e.preventDefault();

            let id = $('#unit_id').val();
            let method = id ? 'PUT' : 'POST';

            $('#unitSaveBtn').prop('disabled', true);

            $.ajax({
                url: saveUrl,
                type: method,
                data: {
                    _token: "{{ csrf_token() }}",
                    name: $('#unit_name').val()
                },
                success: function (res) {

                    let unit = res.unit;

                    if (id) {
                        // UPDATE
                        $('#unit-row-' + unit.id)
                            .find('.unit-name')
                            .text(unit.name);

                        $('#unit-row-' + unit.id)
                            .find('.edit-unit')
                            .data('name', unit.name);

                    } else {
                        // CREATE
                        // ✅ remove empty row
                        $('#no-unit-row').remove();

                        $('#unitTable').append(`
                                                            <tr id="unit-row-${unit.id}">
                                                                <td></td>
                                                                <td class="unit-name">${unit.name}</td>
                                                                <td>
                                                                    <a href="javascript:void(0)"
                                                                       class="edit-unit"
                                                                       data-id="${unit.id}"
                                                                       data-name="${unit.name}">
                                                                        <i class="fa fa-edit text-green"></i>
                                                                    </a>
                                                                    <button class="delete-confirm"
                                                                            data-id="${unit.id}"
                                                                            data-name="${unit.name}"
                                                                            style="border:none;background:none">
                                                                        <i class="fa fa-trash text-red"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        `);
                    }

                    refreshUnitIndex();
                    $('#unitModal').modal('hide');
                    $('#unitSaveBtn').prop('disabled', false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Unit saved successfully',
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function (xhr) {
                    $('#unitSaveBtn').prop('disabled', false);
                    Swal.fire(
                        'Error',
                        xhr.responseJSON?.message ?? 'Validation failed',
                        'error'
                    );
                }
            });
        });

        /* =========================
           DELETE
        ========================== */
        $(document).on('click', '.delete-confirm', function (e) {
            e.preventDefault();

            let id = $(this).data('id');
            let name = $(this).data('name');

            Swal.fire({
                title: 'Delete?',
                text: `Delete ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('units.destroy', [$company->id, 'ID']) }}".replace('ID', id),
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function () {

                            $('#unit-row-' + id).fadeOut(300, function () {
                                $(this).remove();
                                refreshUnitIndex();

                                if ($('#unitTable tr[id^="unit-row"]').length === 0) {
                                    $('#unitTable').html(`
                                        <tr id="no-unit-row">
                                            <td colspan="3" class="text-center"> 😢 No units found</td>
                                        </tr>
                                    `);
                                }
                            });

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                timer: 1200,
                                showConfirmButton: false
                            });
                        }
                        ,
                        error: function () {
                            Swal.fire('Error', 'Delete failed', 'error');
                        }
                    });
                }
            });
        });
    </script>

@endpush