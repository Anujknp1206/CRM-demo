@extends('company.layouts.master')

@section('content')

    <section class="content-header">
        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Parts Master</h1>
                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-right">

                        <li class="breadcrumb-item">
                            <a href="{{ route('company.dashboard', ['company' => $company->id]) }}">
                                Dashboard
                            </a>
                        </li>

                        <li class="breadcrumb-item active">
                            Parts Master
                        </li>

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

                        {{-- HEADER --}}
                        <div class="card-header d-flex justify-content-between align-items-center">

                            <h3 class="card-title">
                                Parts Master
                            </h3>

                            <div class="d-flex align-items-center ml-auto" style="gap:8px;">

                                @can('add parts')
                                    <a href="{{ route('parts.create', $company->id) }}">
                                        <button class="btn btn-block btn-default btn-sm">
                                            <i class="fa fa-plus"></i> Add Part
                                        </button>
                                    </a>
                                @endcan
                                <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="btn btn-success btn-sm">
                                    <i class="fa fa-arrow-left"></i>
                                    Back

                                </a>

                            </div>

                        </div>


                        {{-- FILTERS --}}
                        <div class="card-body border-bottom">

                            <div class="row">

                                {{-- FROM DATE --}}
                                <div class="col-md-3">

                                    <label>
                                        From Date
                                    </label>

                                    <input type="text" id="from_date" class="form-control flatpickr"
                                        placeholder="Select From Date">

                                </div>

                                {{-- TO DATE --}}
                                <div class="col-md-3">

                                    <label>
                                        To Date
                                    </label>

                                    <input type="text" id="to_date" class="form-control flatpickr"
                                        placeholder="Select To Date">

                                </div>

                                {{-- SEARCH --}}
                                <div class="col-md-3">

                                    <label>
                                        Search Part
                                    </label>

                                    <select id="part_search" class="form-control" style="width:100%">
                                    </select>

                                </div>

                                {{-- BUTTONS --}}
                                <div class="col-md-3 d-flex align-items-end">

                                    <button id="filter" class="btn btn-success mr-2 w-50">

                                        <i class="fa fa-filter"></i>
                                        Search

                                    </button>

                                    <button id="reset" class="btn btn-secondary w-50">

                                        <i class="fa fa-undo"></i>
                                        Reset

                                    </button>

                                </div>

                            </div>

                        </div>


                        {{-- TABLE --}}
                        <div class="card-body">

                            {{-- LOADER --}}
                            <div id="loader" style="display:none; text-align:center; padding:20px;">

                                <i class="fa fa-spinner fa-spin" style="font-size:28px; color:#17a2b8;"></i>

                                <p class="mt-2">
                                    Loading Parts...
                                </p>

                            </div>

                            {{-- TABLE --}}
                            <div class="table-responsive">

                                <table id="example1" class="table table-bordered table-hover">

                                    <thead>

                                        <tr>

                                            <th width="5%">
                                                SN
                                            </th>

                                            <th>
                                                Part
                                            </th>

                                            <th width="12%">
                                                Items
                                            </th>

                                            <th width="12%">
                                                Date
                                            </th>

                                            <th width="15%">
                                                Action
                                            </th>

                                        </tr>

                                    </thead>

                                    <tbody id="partRows">

                                        {{-- AJAX DATA --}}

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>
    {{-- PART DETAILS MODAL --}}
    <div class="modal fade" id="partDetailsModal">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <div class="modal-header text-white"
                    style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">

                    <h5 class="modal-title">
                        Part Details
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal">

                        &times;

                    </button>

                </div>

                <div class="modal-body" id="partDetailsBody">

                    <div class="text-center p-4">

                        <i class="fa fa-spinner fa-spin fa-2x"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>
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
        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
        $(document).ready(function () {

            /* =========================================
               FLATPICKR
            ========================================== */
            $('.flatpickr').flatpickr({

                dateFormat: "d/m/Y",

                allowInput: false

            });


            /* =========================================
               SELECT2 AJAX SEARCH
            ========================================== */
            $('#part_search').select2({

                placeholder: 'Search Part by name or code',

                allowClear: false,

                width: '100%',

                minimumInputLength: 1,

                ajax: {

                    url: "{{ route('parts.ajaxSearch', ['company' => $company->id]) }}",

                    dataType: 'json',

                    delay: 250,

                    data: function (params) {

                        return {
                            search: params.term
                        };
                    },

                    processResults: function (data) {

                        return {
                            results: data
                        };
                    },

                    cache: true
                }
            });


            /* =========================================
               LOAD PARTS
            ========================================== */
            function loadParts() {

                let params = {};

                // SEARCH
                let search = $('#part_search').val();

                if (search) {

                    params.search = search;
                }

                // DATES
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();

                if (from_date) {
                    params.from_date = from_date;
                }

                if (to_date) {
                    params.to_date = to_date;
                }

                $("#loader").show();
                $("#example1").hide();

                $.ajax({

                    url: "{{ route('parts.data', ['company' => $company->id]) }}",

                    type: "GET",

                    data: params,

                    beforeSend: function () {

                        $("#loader").show();
                        $("#example1").hide();

                    },

                    success: function (response) {

                        // DESTROY OLD DATATABLE
                        if ($.fn.DataTable.isDataTable('#example1')) {

                            $('#example1').DataTable().destroy();

                        }

                        // LOAD ROWS
                        $('#partRows').html(response);

                        // NO DATA
                        if ($("#partRows").find("tr.no-data").length > 0) {

                            return;

                        }

                        // INIT DATATABLE
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

                        console.error('Part fetch failed');

                    },

                    complete: function () {

                        $("#loader").hide();
                        $("#example1").show();

                    }

                });
            }

            /* =========================================
               INITIAL LOAD
            ========================================== */
            loadParts();


            /* =========================================
               SEARCH BUTTON
            ========================================== */
            /* =========================================
      AUTO FILTER - DATES
    ========================================== */
            $('#from_date, #to_date').change(function () {

                loadParts();

            });


            /* =========================================
               AUTO FILTER - PART SEARCH
            ========================================== */
            $('#part_search').on('change', function () {

                loadParts();

            });


            /* =========================================
               SEARCH BUTTON
            ========================================== */
            $('#filter').click(function () {

                loadParts();

            });


            /* =========================================
               RESET BUTTON
            ========================================== */
            $('#reset').click(function () {

                $('#part_search').val(null).trigger('change');

                $('#from_date').val('');

                $('#to_date').val('');

                loadParts();

            });


            /* =========================================
               AUTO FILTER
            ========================================== */
            $('#from_date, #to_date').change(function () {

                loadParts();

            });

        });

    </script>
    <script>/* =========================================
    VIEW PART DETAILS
    ========================================== */
        $(document).on('click', '.viewPartBtn', function () {

            let partId = $(this).data('id');

            $('#partDetailsModal').modal('show');

            $('#partDetailsBody').html(`
                <div class="text-center p-4">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                </div>
            `);

            $.ajax({

                url: "{{ url('company/' . $company->id . '/parts') }}/" + partId + "/details",

                type: "GET",

                success: function (response) {

                    $('#partDetailsBody').html(response);

                },

                error: function () {

                    $('#partDetailsBody').html(`
                        <div class="alert alert-danger">
                            Failed to load part details.
                        </div>
                    `);

                }

            });

        });</script>

    <script>

        $(document).on('submit', '.deletePartForm', function (e) {

            e.preventDefault();

            let form = this;

            Swal.fire({

                title: 'Delete Part?',

                text: "This action cannot be undone!",

                icon: 'warning',

                showCancelButton: true,

                confirmButtonColor: '#d33',

                cancelButtonColor: '#3085d6',

                confirmButtonText: 'Yes, Delete'

            }).then((result) => {

                if (result.isConfirmed) {

                    form.submit();

                }

            });

        });

    </script>
@endpush