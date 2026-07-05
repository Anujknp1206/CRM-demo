@extends('company.layouts.master')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $label }}</h1>
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
                            <h3 class="card-title">{{$label}}</h3>
                            <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                                @can('add jobcard')
                                    <a href="{{ route('jobcard.create', $company->id) }}">
                                        <button class="btn btn-block btn-default btn-sm">
                                            <i class="fa fa-plus"></i> Add Job Card
                                        </button>
                                    </a>
                                @endcan
                                <a href="{{ url()->previous() }}" class="btn btn-success btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 align-items-end">

                                <!-- From Date -->
                                <div class="col-md-3">
                                    <label class="form-label text-muted">From Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        <input type="date" id="from_date" class="form-control shadow-sm"
                                            placeholder="DD/MM/YYYY">
                                    </div>
                                </div>

                                <!-- To Date -->
                                <div class="col-md-3">
                                    <label class="form-label text-muted">To Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        <input type="date" id="to_date" class="form-control shadow-sm"
                                            placeholder="DD/MM/YYYY">
                                    </div>
                                </div>

                                <!-- Search -->
                                <div class="col-md-3">
                                    <label>Search Planning</label>
                                    <select id="planning_search" class="form-control" style="width:100%"></select>

                                </div>

                                <!-- Buttons -->
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
                                <table id="example1" class="table table-bordered table-striped mt-3">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>PO No</th>
                                            <th>Customer</th>
                                            <th>Incharge</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="planningRows"></tbody>
                                </table>
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
        .input-group-text {
            background: #f3f6f9;
            border-right: none;
            font-size: 14px;
        }

        #example1_filter {
            display: none !important;
        }

        .form-control {
            border-left: none;
            border-radius: 0 5px 6px 0 !important;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #17a2b8;
        }

        .input-group .form-control {
            border-radius: 0 6px 6px 0;
        }

        .gap-2 {
            gap: 10px;
        }

        .btn-success,
        .btn-secondary {
            border-radius: 6px;
        }

        /* PDF Styling */
        .pdf-mode table {
            border-collapse: collapse !important;
        }

        .pdf-mode th,
        .pdf-mode td {
            padding: 4px 6px !important;
            /* 🔽 reduce padding */
            margin: 0 !important;
            font-size: 11.5px !important;
            /* slightly smaller */
            line-height: 1.2 !important;
        }

        .pdf-mode tr {
            margin: 0 !important;
        }

        /* Reduce heading spacing */
        .pdf-mode h5,
        .pdf-mode h6 {
            margin: 6px 0 !important;
        }

        .pdf-mode thead {
            display: table-header-group !important;
        }

        .pdf-mode tfoot {
            display: table-footer-group !important;
        }

        .pdf-mode tr {
            page-break-inside: avoid !important;
        }

        .pdf-mode table {
            page-break-inside: auto !important;
        }

        .pdf-mode .section-terms {
            font-size: 11px !important;
            line-height: 1.25 !important;
        }

        .pdf-mode .section-terms p {
            margin: 2px 0 !important;
            line-height: 1.25 !important;
        }

        /* 🔴 FORCE SIGNATURE SPACING (FINAL OVERRIDE) */
        .pdf-mode .section-terms .pdf-keep-margin {
            margin-top: 50px !important;
        }

        .pdf-mode .section-terms .pdf-keep-margin * {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            line-height: 1.6 !important;
        }

        .pdf-mode .section-terms br {
            line-height: 1.2 !important;
        }

        .pdf-mode .section-terms * {
            margin-top: 2px !important;
            margin-bottom: 2px !important;
        }

        .pdf-mode .terms-title {
            display: inline-block;
            border-bottom: 1.9px solid #000;
            padding-bottom: 1px;
            margin-bottom: 3px;
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
        function initDatePicker(selector) {

            // 🔴 FORCE remove native date behavior
            const input = document.querySelector(selector);
            input.type = "text";

            const picker = flatpickr(input, {
                dateFormat: "Y-m-d",   // backend
                altInput: true,        // UI input
                altFormat: "d/m/Y",    // visible format
                allowInput: true,
                clickOpens: true
            });

            // 🟢 Open calendar on icon click
            input.closest('.input-group')
                .querySelector('.input-group-text')
                .addEventListener('click', () => picker.open());

            return picker;
        }

        const fromPicker = initDatePicker("#from_date");
        const toPicker = initDatePicker("#to_date");
    </script>
    <script src="{{url('/')}}/admin/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/jszip/jszip.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function () {

            // Init Select2 (SAME as quotation)
            $('#planning_search').select2({
                placeholder: "Search PO / Customer...",
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('ajax.jobcard.search', ['company' => $company->id]) }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { search: params.term };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (o) {
                                return {
                                    id: o.id,
                                    text: o.text
                                };
                            })
                        };
                    }
                }
            });

            loadPlannings();

            // Search button
            $('#filter').on('click', function () {
                loadPlannings();
            });

            // Reset button
            $('#reset').on('click', function () {
                $('#planning_search').val(null).trigger('change');
                $('#from_date').val(''); fromPicker.clear();
                toPicker.clear();
                $('#to_date').val('');
                loadPlannings();
            });

        });

        function loadPlannings() {

            let params = {};

            let search = $('#planning_search').val();
            if (search) {
                params.search = search;
            }

            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            if (from_date) params.from_date = from_date;
            if (to_date) params.to_date = to_date;

            $("#loader").show();
            $("#example1").hide();

            $.ajax({
                url: "{{ route('jobcard.data', ['company' => $company->id]) }}",
                type: "GET",
                data: params,

                beforeSend: function () {
                    $("#loader").show();
                    $("#example1").hide();
                },

                success: function (response) {
                    if ($.fn.DataTable.isDataTable('#example1')) {
                        $('#example1').DataTable().destroy();
                    }

                    $('#planningRows').html(response);

                    // No data → do NOT init DataTable
                    if ($("#planningRows").find("tr.no-data").length > 0) {
                        return;
                    }

                    $('#example1').DataTable({
                        responsive: true,
                        autoWidth: false,
                        paging: true,
                        searching: false,
                        ordering: true,
                        dom: 'Bfrtip',
                        buttons: [
                            { extend: 'copy', className: 'btn btn-secondary btn-sm' },
                            { extend: 'csv', className: 'btn btn-secondary btn-sm' },
                            { extend: 'pdf', className: 'btn btn-secondary btn-sm' },
                            { extend: 'print', className: 'btn btn-secondary btn-sm' },
                            { extend: 'colvis', className: 'btn btn-secondary btn-sm' },
                        ]
                    });
                },

                error: function () {
                    console.error('Order fetch failed');
                },

                // ALWAYS runs
                complete: function () {
                    $("#loader").hide();
                    $("#example1").show();
                }
            });
        } $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
    </script>
@endpush