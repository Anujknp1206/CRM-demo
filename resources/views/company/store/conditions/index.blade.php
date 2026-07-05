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
                            <a href="{{ route('company.dashboard', $company->id) }}">Dashboard</a>
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
                    @can('add condition')
                        <button class="btn btn-default btn-sm" onclick="openCreateCondition()">
                            <i class="fa fa-plus"></i> Add Condition
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
                            <th>Condition Name</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>

                    <tbody id="conditionTable">
                        @forelse($conditions as $key => $condition)
                            <tr id="condition-row-{{ $condition->id }}">
                                <td>{{ $key + 1 }}</td>
                                <td class="condition-name">{{ $condition->name }}</td>
                                <td>
                                    @can('edit condition')
                                        <a href="javascript:void(0)" class="edit-condition" data-id="{{ $condition->id }}"
                                            data-name="{{ $condition->name }}" title="Edit Condition">
                                            <i class="fa fa-edit text-green"></i>
                                        </a>
                                    @endcan
                                    @can('delete condition')
                                        <button class="delete-condition" data-id="{{ $condition->id }}"
                                            data-name="{{ $condition->name }}" style="border:none;background:none"
                                            title="Delete Condition">
                                            <i class="fa fa-trash text-red"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr id='no-condition-row'>
                                <td colspan="4" class="text-center"> 😢 No condition found</td>
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
        let saveUrl = "{{ route('conditions.store', $company->id) }}";

        /* =======================
           OPEN CREATE MODAL
        ======================= */
        function openCreateCondition() {
            $('#conditionModalTitle').text('Add Condition');
            $('#conditionForm')[0].reset();
            $('#condition_id').val('');
            saveUrl = "{{ route('conditions.store', $company->id) }}";
            $('#conditionModal').modal('show');
        }

        /* =======================
           OPEN EDIT MODAL
        ======================= */
        $(document).on('click', '.edit-condition', function () {
            let id = $(this).data('id');
            let name = $(this).data('name');

            $('#conditionModalTitle').text('Edit Condition');
            $('#condition_id').val(id);
            $('#condition_name').val(name);

            saveUrl = "{{ route('conditions.update', [$company->id, 'ID']) }}"
                .replace('ID', id);

            $('#conditionModal').modal('show');
        });

        /* =======================
           SAVE (CREATE / UPDATE)
        ======================= */
        $('#conditionForm').on('submit', function (e) {
            e.preventDefault();

            let id = $('#condition_id').val();

            let formData = {
                _token: "{{ csrf_token() }}",
                name: $('#condition_name').val()
            };

            if (id) {
                formData._method = 'PUT';
            }

            $.ajax({
                url: id
                    ? "{{ route('conditions.update', [$company->id, 'ID']) }}".replace('ID', id)
                    : "{{ route('conditions.store', $company->id) }}",
                type: 'POST',
                data: formData,

                success: function (res) {
                    let condition = res.condition;

                    if (id) {
                        $('#condition-row-' + id)
                            .find('.condition-name')
                            .text(condition.name);

                        $('#condition-row-' + id)
                            .find('.edit-condition')
                            .data('name', condition.name);
                        $('#condition-row-' + id)
                            .find('.delete-condition')
                            .data('name', condition.name);
                    } else {
                        $('#no-condition-row').remove();
                        let count = $('#conditionTable tr').length + 1;

                        $('#conditionTable').append(`
                                        <tr id="condition-row-${condition.id}">
                                            <td>${count}</td>
                                            <td class="condition-name">${condition.name}</td>
                                            <td>
                                                <a href="javascript:void(0)"
                                                   class="edit-condition"
                                                   data-id="${condition.id}"
                                                   data-name="${condition.name}">
                                                    <i class="fa fa-edit text-green"></i>
                                                </a>
                                                <button class="delete-condition"
                                                        data-id="${condition.id}"
                                                        data-name="${condition.name}"
                                                        style="border:none;background:none">
                                                    <i class="fa fa-trash text-red"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    `);
                    }

                    $('#conditionModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Saved successfully',
                        timer: 1200,
                        showConfirmButton: false
                    });
                },

                error: function (xhr) {
                    console.log(xhr.responseJSON);
                    let msg = xhr.responseJSON?.errors?.name?.[0] || 'Validation failed';
                    Swal.fire('Error', msg, 'error');
                }
            });
        });


        /* =======================
           DELETE
        ======================= */
        $(document).on('click', '.delete-condition', function () {
            let id = $(this).data('id');
            let name = $(this).data('name');

            Swal.fire({
                title: 'Delete?',
                text: `Delete ${name}?`,
                icon: 'warning',
                showCancelButton: true
            }).then(result => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('conditions.destroy', [$company->id, 'ID']) }}".replace('ID', id),
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "DELETE"
                        },

                        success: function () {
                            $('#condition-row-' + id).fadeOut(300, function () {
                                $(this).remove();

                                if ($('#conditionTable tr[id^="condition-row"]').length === 0) {
                                    $('#conditionTable').html(`
                                                    <tr id="no-condition-row">
                                                        <td colspan="3" class="text-center"> 😢 No condition found</td>
                                                    </tr>
                                                `);
                                }
                            });

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                timer: 1000,
                                showConfirmButton: false
                            });
                        },

                        error: function (xhr) {
                            console.log(xhr.responseText);
                            Swal.fire('Error', 'Delete failed', 'error');
                        }
                    });
                }
            });
        });

    </script>
@endpush