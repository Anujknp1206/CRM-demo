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

                        <li class="breadcrumb-item">

                            <a href="{{ route('company.dashboard', $company->id) }}">
                                Dashboard
                            </a>

                        </li>

                        <li class="breadcrumb-item active">
                            {{ $label }}
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
                                {{ $label }}
                            </h3>

                            <div class="d-flex align-items-center ml-auto" style="gap:8px;">
                                @can('export orders')

                                    {{-- PRINT BUTTON --}}
                                    <button onclick="printReport()" class="btn btn-dark btn-sm no-print">

                                        <i class="fa fa-print"></i>

                                        Print Report

                                    </button>

                                @endcan
                                <a href="{{ route('company.dashboard', $company->id) }}" class="btn btn-success btn-sm">

                                    <i class="fa fa-arrow-left"></i>

                                    Back

                                </a>

                            </div>

                        </div>
                        {{-- FILTER + SUMMARY --}}
                        <div class="card-body border-bottom bg-light">

                            {{-- SUMMARY CARDS --}}
                            <div class="row mb-4">

                                {{-- TOTAL ORDERS --}}
                                <div class="col">

                                    <div class="small-box bg-white shadow-sm border font-small">

                                        <div class="inner">

                                            <h4>
                                                {{ $summary['total_orders'] }}
                                            </h4>

                                            <p>
                                                Total Orders
                                            </p>

                                        </div>

                                        <div class="icon">
                                            <i class="fas fa-shopping-cart text-info"></i>
                                        </div>

                                    </div>

                                </div>

                                {{-- TOTAL VALUE --}}
                                <div class="col">

                                    <div class="small-box bg-white shadow-sm border font-small">

                                        <div class="inner">

                                            <h4 class="text-dark">

                                                ₹{{ number_format($summary['total_value'], 2) }}
                                            </h4>

                                            <p>
                                                Total Value
                                            </p>

                                        </div>

                                        <div class="icon">
                                            <i class="fas fa-rupee-sign text-success"></i>
                                        </div>

                                    </div>

                                </div>

                                {{-- PAID --}}
                                <div class="col">

                                    <div class="small-box bg-white shadow-sm border font-small">

                                        <div class="inner">

                                            <h4 class="text-success">

                                                ₹{{ number_format($summary['total_paid'], 2) }}

                                            </h4>

                                            <p>
                                                Total Paid
                                            </p>

                                        </div>

                                        <div class="icon">
                                            <i class="fas fa-wallet text-success"></i>
                                        </div>

                                    </div>

                                </div>

                                {{-- DUE --}}
                                <div class="col">

                                    <div class="small-box bg-white shadow-sm border font-small">

                                        <div class="inner">

                                            <h4 class="text-danger">

                                                ₹{{ number_format($summary['total_due'], 2) }}

                                            </h4>

                                            <p>
                                                Total Due
                                            </p>

                                        </div>

                                        <div class="icon">
                                            <i class="fa fa-credit-card text-danger"></i>
                                        </div>

                                    </div>

                                </div>

                                {{-- PROGRESS --}}
                                <div class="col">

                                    <div class="small-box bg-white shadow-sm border font-small">

                                        <div class="inner">

                                            <h4 class="text-warning">

                                                {{ $summary['avg_progress'] }}%

                                            </h4>

                                            <p>
                                                Avg Progress
                                            </p>

                                        </div>

                                        <div class="icon">
                                            <i class="fa fa-industry text-warning"></i>
                                        </div>

                                    </div>

                                </div>

                            </div>

                            {{-- FILTERS --}}
                            <div class="row align-items-end">

                                {{-- SEARCH --}}
                                <div class="col-md-3">

                                    <label class="small text-muted">
                                        Search Orders
                                    </label>

                                    <select id="order_search" class="form-control shadow-sm" style="width:100%">
                                    </select>

                                </div>

                                {{-- FROM DATE --}}
                                <div class="col-md-2">

                                    <label class="small text-muted">
                                        From Date
                                    </label>

                                    <div class="input-group">

                                        <div class="input-group-prepend">

                                            <span class="input-group-text">
                                                <i class="fa fa-calendar"></i>
                                            </span>

                                        </div>

                                        <input type="text" id="from_date" class="form-control shadow-sm"
                                            placeholder="DD/MM/YYYY">

                                    </div>

                                </div>

                                {{-- TO DATE --}}
                                <div class="col-md-2">

                                    <label class="small text-muted">
                                        To Date
                                    </label>

                                    <div class="input-group">

                                        <div class="input-group-prepend">

                                            <span class="input-group-text">
                                                <i class="fa fa-calendar"></i>
                                            </span>

                                        </div>

                                        <input type="text" id="to_date" class="form-control shadow-sm"
                                            placeholder="DD/MM/YYYY">

                                    </div>

                                </div>

                                {{-- ORDER STATUS --}}
                                <div class="col-md-2">

                                    <label class="small text-muted">
                                        Order Status
                                    </label>

                                    <select id="status" class="form-control shadow-sm">

                                        <option value="">
                                            All Status
                                        </option>

                                        <option value="pending">
                                            Pending
                                        </option>

                                        <option value="confirmed">
                                            Confirmed
                                        </option>

                                        <option value="planning">
                                            Planning
                                        </option>

                                        <option value="in_production">
                                            In Production
                                        </option>

                                        <option value="on_hold">
                                            On Hold
                                        </option>

                                        <option value="delayed">
                                            Delayed
                                        </option>

                                        <option value="ready">
                                            Ready
                                        </option>

                                        <option value="dispatched">
                                            Dispatched
                                        </option>

                                        <option value="cancelled">
                                            Cancelled
                                        </option>

                                    </select>

                                </div>

                                {{-- PAYMENT STATUS --}}
                                <div class="col-md-1">

                                    <label class="small text-muted">
                                        Payment
                                    </label>

                                    <select id="payment_status" class="form-control shadow-sm">

                                        <option value="">
                                            All
                                        </option>

                                        <option value="paid">
                                            Paid
                                        </option>

                                        <option value="partial">
                                            Partial
                                        </option>

                                        <option value="unpaid">
                                            Unpaid
                                        </option>

                                    </select>

                                </div>

                                {{-- BUTTONS --}}
                                <div class="col-md-2">

                                    <div class="d-flex" style="gap:8px;">

                                        <button id="filter" class="btn btn-success w-50 shadow-sm">
                                            <i class="fa fa-filter"></i> Search
                                        </button>

                                        <button id="reset" class="btn btn-secondary w-50 shadow-sm">
                                            <i class="fa fa-undo"></i> Reset
                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- TABLE --}}
                        <div class="card-body">

                            {{-- LOADER --}}
                            <div id="loader" style="
                                                                                                                            display:none;
                                                                                                                            text-align:center;
                                                                                                                            padding:20px;
                                                                                                                         ">

                                <i class="fa fa-spinner fa-spin"
                                    style="
                                                                                                                            font-size:28px;
                                                                                                                            color:#17a2b8;
                                                                                                                           ">
                                </i>

                                <p>Loading data...</p>

                            </div>

                            <div id="reportRows">

                                @include('company.crm.reports.partials.order_report_table', ['orders' => $orders])

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
        .card-body {
            padding: 10px 5px !important;
        }

        .small-box {
            position: relative !important;
            overflow: hidden !important;
        }

        .small-box .icon {

            position: absolute !important;

            right: -10px !important;

            bottom: 65px !important;

            z-index: 0;
        }

        .font-small {
            font-size: 30px !important;
        }

        .small-box .icon i {
            font-size: 40px !important;
            line-height: 1 !important;
        }

        .small-box .inner {

            position: relative;

            z-index: 2;
        }

        .gap-2 {
            gap: 10px;
        }

        .btn-success,
        .btn-secondary {
            border-radius: 6px;
        }

        .order-card {

            border-radius: 14px;
            overflow: hidden;

            border: 1px solid #e5e7eb;

            background: #fff;

            transition: all .2s ease;
        }

        .order-card:hover {

            box-shadow: 0 8px 25px rgba(0, 0, 0, .08);
        }

        .order-header-btn {

            width: 100%;

            border: 0 !important;

            background: transparent !important;

            padding: 0 !important;

            text-decoration: none !important;
        }

        .order-header-btn:hover,
        .order-header-btn:focus {

            text-decoration: none !important;

            box-shadow: none !important;
        }

        .progress-circle {

            --size: 62px;

            width: var(--size);
            height: var(--size);

            border-radius: 50%;
            background:
                conic-gradient(#28a745 calc(var(--progress) * 1%),
                    #edf2f7 0);

            display: flex;
            align-items: center;
            justify-content: center;

            position: relative;

            flex-shrink: 0;
        }

        .progress-circle::before {

            content: "";

            position: absolute;

            width: 48px;
            height: 48px;

            border-radius: 50%;

            background: #fff;
        }

        .progress-circle span {

            position: relative;

            z-index: 2;

            font-size: 11px;
            font-weight: 700;

            color: #111827;
        }

        .order-title {

            font-size: 18px;
            font-weight: 700;

            color: #111827;
        }

        .order-meta {
            font-size: 13px;
            font-weight: 900;
            color: #040404;
        }

        .order-price {

            display: inline-block;

            padding: 10px 18px;

            background: #ecfdf3;

            border: 1px solid #16a34a;

            border-radius: 10px;

            color: #16a34a;

            font-weight: 700;

            font-size: 22px;

            line-height: 1;

            box-shadow: 0 2px 6px rgba(22, 163, 74, 0.12);
        }

        .badge {

            font-size: 11px !important;

            letter-spacing: .3px;

            border-radius: 30px;

            padding: 7px 12px;
        }

        .report-box {

            background: #fff;

            border-radius: 10px;

            padding: 16px;

            border: 1px solid #eef2f7;

            height: 100%;
        }

        .report-box-icon {

            width: 42px;
            height: 42px;

            border-radius: 10px;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #fff;

            font-size: 18px;
        }

        .report-box-title {

            font-size: 12px;

            color: #6b7280;

            margin-bottom: 4px;
        }

        .report-box-value {

            font-size: 20px;

            font-weight: 700;

            color: #111827;
        }

        .item-card {

            border: 1px solid #e5e7eb;

            border-radius: 12px;

            overflow: hidden;

            margin-bottom: 15px;
        }

        .item-header {

            background: #f8fafc;

            padding: 16px 18px;

            border-bottom: 1px solid #eef2f7;
        }

        .item-header-btn {

            width: 100%;

            border: 0;

            background: transparent;

            padding: 0;

            text-align: left;
        }

        .item-header-btn:hover,
        .item-header-btn:focus {

            outline: none;

            text-decoration: none;
        }

        .item-name {

            font-size: 15px;

            font-weight: 700;

            color: #111827;
        }

        .item-qty {

            font-size: 12px;

            color: #6b7280;
        }

        .bom-table {

            margin-bottom: 0;
        }

        .bom-table thead th {

            background: #f9fafb;

            font-size: 12px;

            text-transform: uppercase;

            letter-spacing: .5px;

            color: #6b7280;

            border-bottom: 1px solid #e5e7eb;
        }

        .bom-table td {

            vertical-align: middle !important;

            padding: 14px 12px;
        }

        .progress {

            height: 10px;

            border-radius: 30px;

            background: #edf2f7;
        }

        .progress-bar {

            border-radius: 30px;
        }

        @media(max-width:768px) {

            .order-title {

                font-size: 15px;
            }

            .progress-circle {

                --size: 54px;
            }

            .report-box {

                margin-bottom: 12px;
            }
        }
    </style>
    <style>
        /* =====================================
           REPORT TABLE WRAPPER
        ===================================== */

        .report-table-wrapper {

            background: #ffffff;

            border-radius: 20px;

            padding: 18px;

            border: 1px solid #e2e8f0;

            box-shadow:
                0 6px 20px rgba(15, 23, 42, .05);
        }

        /* =====================================
           TABLE
        ===================================== */

        .report-table {

            width: 100%;

            border-collapse: separate;

            border-spacing: 0 12px;
        }

        /* =====================================
           MAIN HEADER
        ===================================== */

        .report-table thead tr:first-child th {

            background: linear-gradient(135deg,
                    #f8fafc,
                    #eef2ff);

            color: #475569;

            font-size: 11px;

            text-transform: uppercase;

            letter-spacing: 1px;

            font-weight: 800;

            padding: 16px 14px;

            border: none;

            border-bottom: 2px solid #dbeafe;
        }

        /* =====================================
           SUB HEADER
        ===================================== */

        .report-table thead tr:last-child th {

            background: #f8fafc;

            color: #64748b;

            font-size: 12px;

            font-weight: 700;

            padding: 14px;

            border-top: none;

            border-bottom: 1px solid #e2e8f0;
        }

        /* =====================================
           BODY ROW
        ===================================== */

        .report-table tbody tr {

            background: #ffffff;

            transition: .25s ease;

            box-shadow:
                0 2px 8px rgba(15, 23, 42, .03);
        }

        /* Hover */
        .report-table tbody tr:hover {

            transform: translateY(-2px);

            background: #f8fbff;
        }

        /* Alternate row */
        .report-table tbody tr:nth-child(even) {

            background: #fcfdff;
        }

        /* =====================================
           CELLS
        ===================================== */

        .report-table td {

            padding: 18px 14px;

            vertical-align: middle;

            border-top: 1px solid #edf2f7;

            border-bottom: 1px solid #edf2f7;
        }

        /* Rounded Row Effect */
        .report-table tbody tr td:first-child {

            border-left: 1px solid #edf2f7;

            border-radius: 14px 0 0 14px;
        }

        .report-table tbody tr td:last-child {

            border-right: 1px solid #edf2f7;

            border-radius: 0 14px 14px 0;
        }

        /* =====================================
           ORDER NUMBER
        ===================================== */

        .report-order-no {

            color: #2563eb;

            font-weight: 800;

            font-size: 13px;

            text-decoration: none;
        }

        /* =====================================
           CUSTOMER
        ===================================== */

        .report-customer {

            font-size: 14px;

            font-weight: 700;

            color: #0f172a;
        }

        .report-mobile {

            margin-top: 4px;

            color: #94a3b8;

            font-size: 12px;
        }

        /* =====================================
           MONEY
        ===================================== */

        .report-amount {

            font-size: 15px;

            font-weight: 800;

            color: #0f172a;

            white-space: nowrap;
        }

        .report-paid {

            font-size: 15px;

            font-weight: 800;

            color: #16a34a;

            white-space: nowrap;
        }

        /* =====================================
           BADGES
        ===================================== */

        .report-pill {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            min-width: 88px;

            padding: 7px 14px;

            border-radius: 999px;

            font-size: 11px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .5px;
        }

        /* Payment */
        .pill-unpaid {
            background: rgba(148, 163, 184, .12);
            color: #64748b;
        }

        .pill-partial {
            background: rgba(59, 130, 246, .08);
            color: #2563eb;
        }

        .pill-paid {
            background: rgba(34, 197, 94, .08);
            color: #16a34a;
        }

        /* Status */
        .pill-pending {
            background: rgba(245, 158, 11, .08);
            color: #d97706;
        }

        .pill-confirmed {
            background: rgba(34, 197, 94, .08);
            color: #16a34a;
        }

        /* =====================================
           PROGRESS
        ===================================== */

        .report-progress {

            font-size: 13px;

            font-weight: 800;

            color: #334155;
        }

        /* =====================================
           PRINT
        ===================================== */

        @media print {

            body {
                background: #fff !important;
            }

            .report-table-wrapper {

                box-shadow: none !important;

                border: 1px solid #e2e8f0;
            }

            .nowrap {
                white-space: nowrap !important;
            }

            .report-table td,
            .report-table th {
                vertical-align: middle !important;
            }

            .badge {
                white-space: nowrap !important;
            }

            .report-table tbody tr {

                box-shadow: none !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>

        function printReport() {

            const printContents =
                document.getElementById('reportRows').innerHTML;

            const originalContents =
                document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;

            window.location.reload();
        }

    </script>
    <script>

        $(document).ready(function () {

            /*
            |--------------------------------------------------------------------------
            | SELECT2 SEARCH
            |--------------------------------------------------------------------------
            */

            $('#order_search').select2({

                placeholder: 'Search Orders',

                allowClear: true,

                width: '100%',

                ajax: {

                    url: "{{ route('company.reports.orders.search', $company->id) }}",

                    dataType: 'json',

                    delay: 250,

                    data: function (params) {

                        return {
                            q: params.term
                        };

                    },

                    processResults: function (data) {

                        return {
                            results: data.results
                        };

                    },

                    cache: true

                }

            });

            /*
            |--------------------------------------------------------------------------
            | DATE PICKERS
            |--------------------------------------------------------------------------
            */

            const fromPicker = flatpickr("#from_date", {

                dateFormat: "Y-m-d",

                altInput: true,

                altFormat: "d/m/Y",

                allowInput: true

            });

            const toPicker = flatpickr("#to_date", {

                dateFormat: "Y-m-d",

                altInput: true,

                altFormat: "d/m/Y",

                allowInput: true

            });

            /*
            |--------------------------------------------------------------------------
            | LOAD REPORTS
            |--------------------------------------------------------------------------
            */

            function loadReports(page = 1) {

                $('#loader').show();

                $.ajax({

                    url: "{{ route('company.reports.orders.ajax', $company->id) }}",

                    type: "GET",

                    data: {

                        page: page,

                        from_date: $('#from_date').val(),

                        to_date: $('#to_date').val(),

                        selected: $('#order_search').val(),

                        status: $('#status').val(),

                        payment_status: $('#payment_status').val()

                    },

                    success: function (response) {

                        $('#reportRows').html(response);

                    },

                    error: function () {

                        alert('Failed to load reports.');

                    },

                    complete: function () {

                        $('#loader').hide();

                    }

                });

            }

            /*
            |--------------------------------------------------------------------------
            | FILTER
            |--------------------------------------------------------------------------
            */

            $('#filter').on('click', function () {

                loadReports();

            });

            /*
            |--------------------------------------------------------------------------
            | RESET
            |--------------------------------------------------------------------------
            */

            $('#reset').on('click', function () {

                /*
                |--------------------------------------------------------------------------
                | RESET SELECT2
                |--------------------------------------------------------------------------
                */

                $('#order_search')
                    .val(null)
                    .trigger('change');

                /*
                |--------------------------------------------------------------------------
                | RESET STATUS
                |--------------------------------------------------------------------------
                */

                $('#status').val('');

                $('#payment_status').val('');

                /*
                |--------------------------------------------------------------------------
                | RESET FLATPICKR
                |--------------------------------------------------------------------------
                */

                fromPicker.clear();

                toPicker.clear();

                /*
                |--------------------------------------------------------------------------
                | RELOAD
                |--------------------------------------------------------------------------
                */

                loadReports();

            });

            /*
            |--------------------------------------------------------------------------
            | PAGINATION AJAX
            |--------------------------------------------------------------------------
            */

            $(document).on(
                'click',
                '.pagination a',
                function (e) {

                    e.preventDefault();

                    let page = $(this)
                        .attr('href')
                        .split('page=')[1];

                    loadReports(page);

                }
            );

        });

    </script>

@endpush