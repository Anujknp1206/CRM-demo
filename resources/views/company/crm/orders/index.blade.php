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

                                @can('add order')
                                    <a href="{{ route('orders.create', $company->id) }}">
                                        <button class="btn btn-block btn-default btn-sm">
                                            <i class="fa fa-plus"></i> Add Order
                                        </button>
                                    </a>
                                @endcan
                                <a href="{{ route('company.dashboard', ['company' => $company->id]) }}"
                                    class="btn btn-success btn-sm">
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
                                    <label>Search Orders</label>
                                    <input type="text" id="order_search" class="form-control"
                                        placeholder="Order No, Customer, Mobile, Email">
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
                                            <th style="width:5%">#</th>
                                            <th style="width:12%">Progress</th>
                                            <th style="width:12%">Timeline</th>
                                            <th>Order No</th>
                                            <th>Customer</th>
                                            <th>Mobile</th>
                                            <th style="width:10%">Order Date</th>
                                            <th style="width:10%">Status</th>
                                            <th style="width:10%">Payment</th>
                                            <th style="width:10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="orderRows"></tbody>
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
        .progress-wrapper {
            position: relative;
            display: inline-block;
        }

        .progress-tooltip {
            display: none;
            position: absolute;
            bottom: 120%;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: #fff;
            padding: 8px 10px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 10;
        }

        .progress-wrapper:hover .progress-tooltip {
            display: block;
        }

        .progress-circle {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: conic-gradient(#28a745 calc(var(--progress) * 1%),
                    #e9ecef 0);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            position: relative;
        }

        .progress-circle::before {
            content: "";
            position: absolute;
            width: 35px;
            height: 35px;
            background: #fff;
            border-radius: 50%;
        }

        .progress-circle span {
            position: relative;
            z-index: 2;
        }

        .input-group-text {
            background: #f3f6f9;
            border-right: none;
            font-size: 14px;
        }

        .card-body {
            padding: 10px 5px !important;
        }

        .gap-2 {
            gap: 10px;
        }

        .btn-success,
        .btn-secondary {
            border-radius: 6px;
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
    <script src="{{url('/')}}/admin/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script>
        function initProgressCircle() {
            document.querySelectorAll('.progress-circle').forEach(el => {
                let value = el.getAttribute('data-progress') || 0;
                el.style.setProperty('--progress', value);
            });
        }
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

    <script>
        $(document).ready(function () {
            loadOrders();
            let orderTimer;

            $('#order_search').on('keyup', function () {

                clearTimeout(orderTimer);
                orderTimer = setTimeout(function () {
                    loadOrders();
                }, 500);

            });

            // Search button
            $('#filter').on('click', function () {
                loadOrders();
            });

            // Reset button
            $('#reset').on('click', function () {
                $('#order_search').val('');
                $('#from_date').val(''); fromPicker.clear();
                toPicker.clear();
                $('#to_date').val('');
                loadOrders();
            });

        });

        function loadOrders() {

            let params = {};

            let search = $('#order_search').val();
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
                url: "{{ route('orders.data', ['company' => $company->id]) }}",
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

                    $('#orderRows').html(response);
                    initProgressCircle();
                    // No data → do NOT init DataTable
                    if ($("#orderRows").find("tr.no-data").length > 0) {
                        return;
                    }

                    $('#example1').DataTable({
                        responsive: true,
                        autoWidth: false,
                        lengthChange: false,
                        paging: false,
                        searching: true,
                        info: false,

                        dom: '<"d-flex justify-content-between align-items-center"Bf>rt',

                        buttons: [
                            {
                                extend: 'colvis',
                                text: 'Column visibility'
                            }
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
        } 
    </script>
    <script>
        const attendanceCheckUrl = "{{ url('company') }}";
        function handleBomClick(e, el) {

            e.preventDefault();

            let companyId = $(el).data('company-id');

            $.ajax({
                url: attendanceCheckUrl + '/' + companyId + '/check-attendance',
                type: 'GET',
                success: function (response) {

                    if (!response.attendanceMarked) {

                        Swal.fire({
                            title: "Attendance Required",
                            text: "Today's attendance is not marked.",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonText: "Go to Attendance",
                            cancelButtonText: "Stay Here",
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33"

                        }).then((result) => {

                            if (result.isConfirmed) {

                                window.open(
                                    "{{ route('attendance.index', $company->id) }}",
                                    '_blank'
                                );
                            }
                        });

                        return false;
                    }

                    // attendance exists → go to BOM page
                    window.location.href = $(el).attr('href');
                },

                error: function () {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong!'
                    });
                }
            });

            return false;
        }
        function handleProductionClick(e, el) {

            let hasBom = $(el).data('has-bom');
            let bomUrl = $(el).data('bom-url');

            if (!hasBom) {

                e.preventDefault();

                Swal.fire({
                    title: 'BOM Required',
                    text: 'Please create BOM before starting production',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Create BOM'
                }).then((result) => {

                    if (result.isConfirmed) {
                        window.location.href = bomUrl;
                    }

                });

                return false;
            }

            return true;
        }
    </script>

@endpush