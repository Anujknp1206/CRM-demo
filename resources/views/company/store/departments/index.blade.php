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
                    @can('add department')
                        <button class="btn btn-default btn-sm" onclick="openCreateDepartment()">
                            <i class="fa fa-plus"></i> Add Department
                        </button>
                    @endcan
                    <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div id="loader" style="display:none; text-align:center; padding:20px;">
                    <i class="fa fa-spinner fa-spin" style="font-size:28px; color:#17a2b8;"></i>
                    <p>Loading data...</p>
                </div>
                <div class="table-responsive">

                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Department Name</th>
                                    <th width="120">Action</th>
                                </tr>
                            </thead>
                            <tbody id="departmentTable">

                            </tbody>
                        </table>
                    </div>
                </div>
    </section>
@endsection
@push('styles')
    <style>
        .gap-2 {
            gap: 10px;
        }

        .btn-primary,
        .btn-secondary {
            border-radius: 6px;
            height: 40px;
        }

        table td {
            white-space: normal !important;
            word-break: break-word;
            max-width: 200px;
        }
    </style>
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
    <script src="{{url('/')}}/admin/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script>
        $('#search_name').select2({
            placeholder: "Search Department...",
            minimumInputLength: 1,
            width: '100%',

            ajax: {
                url: "{{ route('ajax.departments.search', ['company' => $company->id]) }}",
                dataType: 'json',
                delay: 300,

                data: function (params) {
                    return {
                        search: params.term
                    };
                },

                processResults: function (data) {
                    return {
                        results: data.map(item => ({
                            id: item.id,
                            text: item.name
                        }))
                    };
                }
            }
        });
        $(document).ready(function () {

            // ✅ INIT LOAD
            loadDepartments();

            // ✅ LIVE SEARCH (optional like lead typing)
            $('#search_name').on('keyup', function () {
                loadDepartments();
            });

        });

        function loadDepartments() {

            let params = {};

            let name = $('#search_name').val() || "";

            // ✅ SEND ONLY FILLED VALUES
            if (name !== "") params.name = name;

            // ✅ SHOW LOADER
            $("#loader").show();
            $("#example1").hide();

            $.ajax({
                url: "{{ route('departments.data', $company->id) }}",
                type: "GET",
                data: params,

                success: function (response) {

                    // ✅ DESTROY OLD DATATABLE (same as Lead)
                    if ($.fn.DataTable.isDataTable('#example1')) {
                        let dt = $('#example1').DataTable();

                        if ($.isFunction(dt.destroy)) {
                            dt.clear().draw(false);
                            dt.destroy();
                        }
                    }

                    // ✅ LOAD DATA INTO TBODY ONLY
                    $('#departmentTable').html(response);

                    // ✅ EMPTY CHECK (same pattern as Lead)
                    if (
                        $("#departmentTable").find("tr").length === 0 ||
                        $("#departmentTable").find("tr td").length === 1
                    ) {
                        $("#loader").hide();
                        $("#example1").show();
                        return;
                    }

                    // ✅ INIT DATATABLE (Lead style)
            $('#example1').DataTable({
    responsive: true,
    autoWidth: false,

    paging: true,
    pageLength: 10,
    lengthChange: true,
    lengthMenu: [10,25,50,100,-1],

    searching: true,
    info: true,

    dom: '<"d-flex justify-content-between align-items-center"Bf>rt<"d-flex justify-content-between mt-2"ip>',

    buttons: [
        {
            extend: 'colvis',
            text: 'Column visibility'
        }
    ]
});
                    // ✅ HIDE LOADER
                    $("#loader").hide();
                    $("#example1").show();
                },

                error: function () {
                    $("#loader").hide();
                    $("#example1").show();
                }
            });
        }

        // ✅ FILTER BUTTON (manual search)
        $('#filterDept').click(function () {
            loadDepartments();
        });

        // ✅ RESET BUTTON (same as Lead behavior)
        $('#resetDept').click(function () {

            // clear input
            $('#search_name').val('');

            // reload table
            loadDepartments();
        });

    </script>
    <script>
        let deptSaveUrl = "{{ route('departments.store', $company->id) }}";
        let deptMethod = "POST";

        function openCreateDepartment() {
            $('#departmentForm')[0].reset();
            $('#department_id').val('');
            $('#departmentModalTitle').text('Add Department');
            deptSaveUrl = "{{ route('departments.store', $company->id) }}";
            deptMethod = "POST";
            $('#departmentModal').modal('show');
        }

        $(document).on('click', '.edit-department', function () {
            $('#department_id').val($(this).data('id'));
            $('#department_name').val($(this).data('name'));
            $('#departmentModalTitle').text('Edit Department');

            deptSaveUrl = "{{ route('departments.update', [$company->id, 'ID']) }}"
                .replace('ID', $(this).data('id'));
            deptMethod = "PUT";
            $('#departmentModal').modal('show');
        });

        $('#departmentForm').submit(function (e) {
            e.preventDefault();

            $.ajax({
                url: deptSaveUrl,
                type: deptMethod,
                data: {
                    _token: "{{ csrf_token() }}",
                    name: $('#department_name').val()
                },
                success: function (res) {
                    let d = res.department;

                    if (deptMethod === 'PUT') {

                        let row = $('#department-row-' + d.id);

                        // update text
                        row.find('.dept-name').text(d.name);

                        // 🔥 update edit button data (THIS FIXES OLD VALUE ISSUE)
                        row.find('.edit-department')
                            .data('name', d.name);
                    }
                    else {
                        $('#no-department-row').remove();
                        $('#departmentTable').prepend(`
                                                                                                          <tr id="department-row-${d.id}">
                                                                                                            <td></td>
                                                                                                            <td class="dept-name">${d.name}</td>
                                                                                                            <td>
                                                                                                              <a href="javascript:void(0)" class="edit-department"
                                                                                                                 data-id="${d.id}" data-name="${d.name}">
                                                                                                                 <i class="fa fa-edit text-green"></i>
                                                                                                              </a>
                                                                                                              <button class="delete-department"
                                                                                                                data-id="${d.id}" data-name="${d.name}"
                                                                                                                style="border:none;background:none">
                                                                                                                <i class="fa fa-trash text-red"></i>
                                                                                                              </button>
                                                                                                            </td>
                                                                                                          </tr>`);
                    }

                    refreshDepartmentIndex();
                    $('#departmentModal').modal('hide');
                    Swal.fire({ icon: 'success', title: 'Saved', timer: 1200, showConfirmButton: false });
                }
            });
        });

        $(document).on('click', '.delete-department', function () {
            let id = $(this).data('id'), name = $(this).data('name');

            Swal.fire({
                title: 'Delete?',
                text: `Delete ${name}?`,
                icon: 'warning',
                showCancelButton: true
            }).then(r => {
                if (r.isConfirmed) {
                    $.ajax({
                        url: "{{ route('departments.destroy', [$company->id, 'ID']) }}".replace('ID', id),
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function () {
                            $('#department-row-' + id).fadeOut(200, function () {
                                $(this).remove();
                            });
                        }

                    });
                }
            });
        });
    </script>
@endpush