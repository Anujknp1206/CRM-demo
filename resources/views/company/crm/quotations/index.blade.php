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
                            <h3 class="card-title">{{$label}}</h3>
                            <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                                @can('add quotation')
                                    <a href="{{ route('quotations.create', ['company' => $company->id]) }}">
                                        <button class="btn btn-default btn-sm">
                                            <i class="fa fa-plus"></i> Add Quotation
                                        </button>
                                    </a>
                                @endcan
                                <a href="{{ url()->previous() }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 align-items-end">

                                <!-- From Date -->
                                <div class="col-md-2 col-12">
                                    <label>From Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="from_date" class="form-control" placeholder="DD/MM/YYYY">
                                    </div>
                                </div>

                                <!-- To Date -->
                                <div class="col-md-2 col-12">
                                    <label>To Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="to_date" class="form-control" placeholder="DD/MM/YYYY">
                                    </div>
                                </div>

                                <!-- Amount Slab -->
                                <div class="col-md-2 col-12">
                                    <label>Amount Slab</label>
                                    <select id="amount_slab" class="form-control">
                                        <option value="">All</option>
                                        <option value="low">Below 7 Lakh</option>
                                        <option value="medium">7 - 15 Lakh</option>
                                        <option value="average">15 - 70 Lakh</option>
                                        <option value="high">Above 70 Lakh</option>
                                    </select>
                                </div>

                                <!-- Search -->
                                <div class="col-md-3 col-12">
                                    <label>Search Quotation</label>
                                    <input type="text" id="quotation_search" class="form-control"
                                        placeholder="Quote No, Customer, Mobile, Email">
                                </div>

                                <!-- Buttons -->
                                <div class="col-md-3 col-12 d-flex gap-2 mt-2">
                                    <button id="filter" class="btn btn-success w-50">
                                        <i class="fa fa-filter"></i> Search
                                    </button>

                                    <button id="reset" class="btn btn-secondary w-50">
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
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>S.N.</th>
                                            <th>Priority</th>
                                            <th>Quote Number</th>
                                            <th>Customer Name</th>
                                            <th>Mobile</th>
                                            <th>Quote Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody id="quoteRows">

                                    </tbody>

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

        .card-body {
            padding: 10px 5px !important;
        }

        .gap-2 {
            gap: 10px;
        }

        .btn-primary,
        .btn-secondary {
            border-radius: 6px;
            height: 40px;
        }
    </style>

    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/summernote/summernote-bs4.min.css">
    <!-- Select2 -->
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
            let quotationTimer;

            $('#quotation_search').on('keyup', function () {

                clearTimeout(quotationTimer);

                quotationTimer = setTimeout(function () {
                    loadQuotations();
                }, 500);
            });
        });
    </script>
    <script>
        $(document).ready(function () {

            loadQuotations();

            // Search button click
            $('#filter').on('click', function () {
                loadQuotations();
            });

            // Reset button click
            $('#reset').on('click', function () {
                $('#quotation_search').val(null).trigger('change');
                $('#from_date').val('');
                $('#to_date').val(''); fromPicker.clear();
                toPicker.clear();
                $('#amount_slab').val('');
                loadQuotations();
            });

        });
        function loadQuotations() {
            let params = {};

            let search = $('#quotation_search').val();

            if (search) {
                params.search = search;
            }

            let slab = $('#amount_slab').val();
            if (slab) params.amount_slab = slab;
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            if (from_date) params.from_date = from_date;
            if (to_date) params.to_date = to_date;

            $("#loader").show();
            $("#example1").hide();

            $.ajax({
                url: "{{ route('quotations.data', ['company' => $company->id]) }}",
                type: "GET",
                data: params,
                success: function (response) {

                    if ($.fn.DataTable.isDataTable('#example1')) {
                        let dt = $('#example1').DataTable();
                        if ($.isFunction(dt.destroy)) {
                            dt.clear().draw(false);
                            dt.destroy();
                        }
                    }

                    $('#quoteRows').html(response);
                    if ($("#quoteRows").find("tr").length === 0 || $("#quoteRows").find("tr td").length === 1) {
                        $("#loader").hide();
                        $("#example1").show();
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

                    $("#loader").hide();
                    $("#example1").show();
                },
                error: function () {
                    $("#loader").hide();
                    $("#example1").show();
                }
            });
        }

    </script>
    <script>
        $(document).on('click', '.delete-confirm', function (e) {
            e.preventDefault();
            const $el = $(this);
            const itemName = $el.data('name') || 'this item';

            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete ${itemName}. This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
                focusCancel: true,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const $form = $el.closest('form');
                    if ($form.length) {
                        $form.trigger('submit');
                        return;
                    }
                    const href = $el.attr('href');
                    if (href) window.location.href = href;
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Cancelled',
                        text: 'Your item is safe.',
                        icon: 'info',
                        timer: 1400,
                        showConfirmButton: false
                    });
                }
            });
        }); $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
    </script>
@endpush