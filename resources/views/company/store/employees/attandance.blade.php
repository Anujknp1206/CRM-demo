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
                        <li class="breadcrumb-item"><a
                                href="{{ route('company.dashboard', ['company' => $company->id]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">{{ $label }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-teal">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">{{ $label }}</h3>
                            <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                                {{-- ➕ Add Attendance --}}

                                {{-- 🔙 Back --}}
                                <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                <!-- 📅 Date -->
                                <div class="col">
                                    <label class="form-label text-muted">Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="date" class="form-control shadow-sm"
                                            value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <!-- 👤 Employee Search -->
                                <div class="col">
                                    <label>Search Employee</label>
                                    <select id="employee_search" class="form-control" style="width:100%"></select>
                                </div>
                                <!-- 🏢 Department -->
                                <div class="col">
                                    <label>Department</label>
                                    <select id="department" class="form-control select2">
                                        <option value="">All</option>
                                        @foreach($departments as $d)
                                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label>Status</label>
                                    <select id="status" class="form-control">
                                        <option value="">All</option> {{-- 🔥 IMPORTANT --}}
                                        <option value="1">Present</option>
                                        <option value="0">Absent</option>
                                    </select>
                                </div>
                                <!-- 🔍 Buttons -->
                                <div class="col-md-3 d-flex gap-2 mt-4">
                                    <button id="filter" class="btn btn-success w-50 shadow-sm">
                                        <i class="fa fa-filter"></i> Search
                                    </button>
                                    <button id="reset" class="btn btn-secondary w-50 shadow-sm">
                                        <i class="fa fa-undo"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="loader" style="display:none; text-align:center; padding:20px;">
                                <i class="fa fa-spinner fa-spin" style="font-size:28px; color:#17a2b8;"></i>
                                <p>Loading data...</p>
                            </div>
                            <div class="table-responsive">
                                <div class="card-body">
                                    <div id="tableWrapper">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Employee</th>
                                                    <th>Department</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="attendanceRows"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
    <!-- Daterange picker -->
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

        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
    </script>

    <script src="{{url('/')}}/admin/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script>
        $(document).ready(function () {

            // ✅ Flatpickr
            flatpickr("#date", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                maxDate: "today"
            });

            // ✅ Select2 Employee
            $('#employee_search').select2({
                placeholder: "Search Employee...",
                minimumInputLength: 1,
                width: '100%',
                ajax: {
                    url: "{{ route('ajax.employees.search', $company) }}",
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        return { search: params.term };
                    },
                    processResults: function (data) {
                        return { results: data };
                    }
                }
            });

            // ✅ Select2 Department
            $('#department').select2({
                width: '100%',
                placeholder: "Select Department"
            });

            // 🔥 INITIAL LOAD
            loadAttendance();
        });


        // =======================
        // 🔥 MAIN FUNCTION
        // =======================
        function loadAttendance() {

            let params = {};

            let search = $('#employee_search').val() || "";
            let dept = $('#department').val() || "";
            let date = $('#date').val();
            let status = $('#status').val() || "";

            // ✅ SEND ONLY NON-EMPTY
            if (search !== "") params.search = search;
            if (dept !== "") params.department_id = dept;
            if (date !== "") params.date = date;
            if (status !== "") params.status = status;

            // ✅ LOADER
            $("#loader").show();
            $("#example1").hide();

            $.ajax({
                url: "{{ route('attendance.data', $company) }}",
                type: "GET",
                data: params,

                success: function (response) {

                    // ✅ SAFE DESTROY (IMPORTANT)
                    if ($.fn.DataTable.isDataTable('#example1')) {
                        let dt = $('#example1').DataTable();
                        if ($.isFunction(dt.destroy)) {
                            dt.clear().draw(false);
                            dt.destroy();
                        }
                    }

                    // ✅ UPDATE ONLY TBODY (NO FULL TABLE REBUILD)
                    $('#attendanceRows').html(response);

                    // ✅ EMPTY CHECK (VERY IMPORTANT)
                    if ($("#attendanceRows").find("tr").length === 0 || $("#attendanceRows").find("td").length === 1) {
                        $("#loader").hide();
                        $("#example1").show();
                        return;
                    }

                    // ✅ RE-INIT DATATABLE
                    $('#example1').DataTable({
                        responsive: true,
                        autoWidth: false,

                        paging: true,
                        pageLength: 10,

                        lengthChange: true,
                        lengthMenu: [10, 25, 50, 100, -1],

                        searching: true,
                        info: true, // ✅ turn ON

                        dom: '<"d-flex justify-content-between align-items-center"Bf>rt<"d-flex justify-content-between mt-2"ip>',

                        buttons: [
                            {
                                extend: 'colvis',
                                text: 'Column visibility'
                            }
                        ]
                    });

                    // ✅ SHOW TABLE
                    $("#loader").hide();
                    $("#example1").show();
                },

                error: function () {
                    $("#loader").hide();
                    $("#example1").show();
                }
            });
        } // =======================
        // 🔍 FILTER
        // =======================
        $('#filter').click(function () {
            loadAttendance();
        });


        // =======================
        // 🔄 RESET
        // =======================
        $('#reset').click(function () {

            $('#employee_search').val(null).trigger('change');
            $('#department').val(null).trigger('change');
            $('#status').val('').trigger('change');
            $('#date').val('{{ date('Y-m-d') }}');

            loadAttendance();
        });


        // =======================
        // ✅ MARK ATTENDANCE
        // =======================
        $(document).on('click', '.mark-attendance', function () {

            let btn = $(this);

            let employee_id = btn.data('id');

            // toggle current value
            let current = parseInt(btn.data('present')) || 0;

            let newStatus = current === 1 ? 0 : 1;

            $.ajax({
                url: "{{ route('attendance.store', $company) }}",
                type: "POST",

                data: {
                    _token: "{{ csrf_token() }}",
                    employee_id: employee_id,
                    date: $('#date').val(),
                    is_present: newStatus
                },

                success: function () {

                    // store new state on button
                    btn.data('present', newStatus);

                    // current row
                    let row = btn.closest('tr');

                    // 4th td = status column
                    let statusCell = row.find('td:eq(3)');

                    if (newStatus === 1) {

                        // update status badge
                        statusCell.html(
                            '<span class="badge badge-success">Present</span>'
                        );

                        // update button
                        btn.removeClass('btn-danger')
                            .addClass('btn-success')
                            .text('Mark Absent');

                    } else {

                        statusCell.html(
                            '<span class="badge badge-danger">Absent</span>'
                        );

                        btn.removeClass('btn-success')
                            .addClass('btn-danger')
                            .text('Mark Present');
                    }

                }

            });

        });
        // =======================
        // 🔥 AUTO FILTER (UX)
        // =======================
        $('#employee_search, #department, #status').on('change', function () {
            loadAttendance();
        });

        $('#date').on('change', function () {
            loadAttendance();
        });

    </script>
@endpush