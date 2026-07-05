@extends('company.layouts.master')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">

                <div class="col-6 col-md-6">
                    <h1 class="mb-0">{{ $label }}</h1>
                </div>

                <div class="col-6 col-md-6 text-md-right">
                    <ol class="breadcrumb float-right mb-1">
                        <li class="breadcrumb-item">
                            <a href="{{ route('orders.index', ['company' => $company->id]) }}">Back</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $label }}</li>
                    </ol>

                    <!-- Mobile Toggle Button -->

                </div>


            </div>
            <div class="row mb-2 justify-content-end">
                <div class="col-12 text-right">

                    <button class="btn btn-success btn-sm d-md-none" id="toggleControls">
                        Show Controls
                    </button>

                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card">
            <div class="card-body print-body">

                <div class="print-layout">

                    {{-- LEFT CONTROLS --}}
                    @include('company.crm.orders.partials.proforma-controls')

                    {{-- RIGHT PREVIEW --}}
                    <div class="preview-content">
                        <div class="preview-wrapper">
                            <div id="printContent" class="document-canvas">
                                @include('company.crm.orders.partials.proforma-canvas')
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
        /* =============================== */
        /* FULL HEIGHT BASE */
        /* =============================== */

        html,
        body {
            height: 100%;
            margin: 0;
        }

        /* Prevent whole page scroll */
        .content,
        .card,
        .card-body,
        .print-body {
            height: 100%;
            overflow: hidden;
        }

        .header-border {
            width: 100%;
            margin-top: 10px;
        }

        .header-border::after {
            content: "";
            display: block;
            height: 7px;
            background: #0b3d6d;
        }

        .header-border::before {
            content: "";
            display: block;
            height: 7px;
            background: #2cca38;
        }

        /* =============================== */
        /* MAIN FLEX LAYOUT */
        /* =============================== */

        .print-layout {
            display: flex;
            height: 100%;
            overflow: hidden;
            /* Important for Safari */
        }

        /* =============================== */
        /* LEFT PANEL */
        /* =============================== */

        .preview-controls {
            width: 320px;
            height: 100%;
            flex-shrink: 0;
            overflow-y: auto;
            border-right: 1px solid #ddd;
            padding: 15px;
            background: #fff;
            -webkit-overflow-scrolling: touch;
            /* Smooth scroll Safari */
        }

        /* =============================== */
        /* RIGHT PANEL */
        /* =============================== */

        .preview-content {
            flex: 1;
            height: 100%;
            display: flex;
            overflow: hidden;
        }

        /* Scrollable preview area */
        .preview-wrapper {
            flex: 1;
            height: 100%;
            overflow-y: auto;
            background: #e9ecef;
            padding: 20px;
            -webkit-overflow-scrolling: touch;
        }

        /* =============================== */
        /* DOCUMENT */
        /* =============================== */

        .document-canvas {
            width: 794px;
            /* A4 width (px) */
            min-height: 1123px;
            /* A4 height */
            margin: auto;
            background: #fff;
            padding: 25px;
            font-size: 13px;
            /* Safari shadow safe */
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        }

        /* Prevent page break issues */
        .section-items,
        .document-canvas {
            page-break-inside: auto;
        }

        .section-company {
            color: #1b3a6b;
        }


        /* =============================== */
        /* TOTAL TABLE */
        /* =============================== */
        .doc-title {
            text-align: center;
            font-weight: 700;
            color: #1b3a6b;
            background: #e9e9e9;
            padding: 3px;
            letter-spacing: 1px;
        }

        .doc-label {
            font-weight: 600;
        }

        .docinfo-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .docinfo-table th {
            background: #f1f1f1;
            padding: 6px;
            border: 1px solid #bbb;
            text-align: left;
            color: #1b3a6b;
            width: 30%;
        }

        .docinfo-table td {
            padding: 6px;
            border: 1px solid #bbb;
            background: #f1f1f1;

        }

        .buyer-seller-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .buyer-seller-table th {
            background: #2b4c7e;
            color: white;
            padding: 6px;
            text-align: left;
            border: 1px solid #1f3961;
        }

        .buyer-seller-table td {
            border: 1px solid #1f3961;
            padding: 8px;
            line-height: 1.5;
        }

        .section-remarks {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .section-remarks th {
            color: #1b3a6b;
        }

        .remarks-table td {
            padding: 6px 8px;
            border: 1px solid #999;
            /* WORD WRAP FIX */
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
            white-space: normal;
        }

        .col-desc {
            line-height: 1.5;

            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        .remarks-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .remarks-table th {
            width: 25%;
            text-align: left;
            background: #f3f3f3;
            padding: 6px 8px;
            border: 1px solid #999;
        }

        .remarks-table td {
            padding: 6px 8px;
            border: 1px solid #999;
        }

        .section-totals {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .totals-table {
            width: 100%;
        }

        .totals-table td {
            padding: 6px 8px;
        }

        .totals-table td.label {
            text-align: right;
            white-space: nowrap;
        }

        .totals-table td.value {
            text-align: right;
            width: 40%;
        }

        .totals-table tr.final td {
            font-weight: bold;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .items-table th {
            background: #2b4c7e;
            color: #fff;
            border: 1px solid #1f3961;
            padding: 6px;
            text-align: left;
        }

        .items-table td {
            border: 1px solid #aaa;
            padding: 6px;
            vertical-align: top;
        }

        .col-sn {
            text-align: center;
        }

        .col-desc {
            width: 50%;
            line-height: 1.5;
        }

        .col-hsn {
            text-align: center;
        }

        .col-qty {
            width: 5%;
            text-align: center;
        }

        .col-rate {
            text-align: right;
        }

        .col-total {
            text-align: right;
        }

        .section-totals {
            margin-top: 10px;
            display: flex;
            justify-content: flex-end;
        }

        .section-paymenttotals {
            margin-top: 10px;
            display: flex;
            justify-content: flex-end;
        }

        .totals-table {
            width: 320px;
            border-collapse: collapse;
            font-size: 13px;
        }

        .totals-table td {
            border: 1px solid #aaa;
            padding: 6px 8px;
        }

        .totals-table .label {
            text-align: right;
            font-weight: 500;
        }

        .totals-table .value {
            text-align: right;
            width: 140px;
        }

        .final-row td {
            font-weight: bold;
            color: white;
            background: #2b4c7e;
        }

        .remaining td {
            font-weight: bold;
            color: white;
            background: #2b4c7e;
        }

        .section-terms {
            margin-top: 10px;
        }

        .terms-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .terms-table th {
            background: #2b4c7e;
            color: #fff;
            text-align: left;
            padding: 6px 8px;
            border: 1px solid #1f3961;
        }

        .terms-table td {
            border: 1px solid #aaa;
            padding: 10px;
            line-height: 1.6;
        }

        /* =============================== */
        /* PRINT (Safari Optimized) */
        /* =============================== */

        @media print {

            /* Hide UI */
            .no-print,
            .preview-controls,
            .content-header,
            .main-footer {
                display: none !important;
            }

            html,
            body,
            .content,
            .card,
            .card-body,
            .print-body,
            .print-layout,
            .preview-content,
            .preview-wrapper {
                display: block !important;
                height: auto !important;
                overflow: visible !important;
                background: #fff !important;
            }

            .preview-wrapper {
                background: #fff !important;
                padding: 0 !important;
            }

            .document-canvas {
                width: 100% !important;
                box-shadow: none !important;
                -webkit-box-shadow: none !important;
                filter: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }

            * {
                box-shadow: none !important;
                -webkit-box-shadow: none !important;
                text-shadow: none !important;
                filter: none !important;
            }

            body {
                margin: 0;
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .docinfo-table th,
            .docinfo-table td {
                background-color: #f1f1f1 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                margin: 0mm;
            }

        }

        .section-terms {
            margin-top: 10px;
            page-break-inside: auto;
        }

        .terms-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            page-break-inside: auto;
        }

        .terms-table tr {
            page-break-inside: auto;
        }

        .terms-table th,
        .terms-table td {
            page-break-inside: auto;
        }

        /* ===================================== */
        /* RECEIPT NOTE */
        /* ===================================== */

        .receipt-note {
            margin-top: 15px;
            border: 2px solid #2cca38;
            padding: 12px;
            background: #f6fff6;
            line-height: 1.6;
        }

        /* ===================================== */
        /* SIGN BLOCK */
        /* ===================================== */

        .sign-block {
            margin-top: 25px;
            border: 1px solid #ccc;
            page-break-inside: auto;
            background: #f5f5f5;
            padding: 15px;
        }

        .sign-title {
            font-weight: 600;
            color: #1b3a6b;
        }

        /* ===================================== */
        /* PRINT NOTE */
        /* ===================================== */

        .print-info {
            font-size: 11px;
            text-align: center;
            margin-top: 10px;
            font-style: italic;
        }

        .company-logo {
            width: 100%;
            max-width: 220px;
            /* desktop size */
            height: auto;
        }

        .post-summary td {
            padding: 0;
            border: none;
        }

        .post-box {
            display: flex;
            justify-content: space-between;
            align-items: center;

            background: #f8d7da;
            /* light red */
            border: 1px solid #f5c2c7;
            border-left: 5px solid #dc3545;

            padding: 10px 15px;
            margin-top: 5px;

            font-size: 13px;
        }

        .post-left {
            color: #842029;
        }

        .post-right {
            color: #842029;
            font-weight: bold;
        }

        /* Desktop only */
        @media (min-width: 769px) {
            .print-layout {
                height: 800px;
            }

            .preview-controls {
                height: 800px;
            }

            .preview-content {
                height: 800px;
            }

            .preview-wrapper {
                height: 800px;
            }
        }

        @media (max-width: 768px) {
            .company-logo {
                max-width: 150px;
            }

            h1 {
                font-size: 18px !important;
            }

            .breadcrumb-item {
                font-size: 14px !important;
            }

            .preview-controls {
                position: fixed;
                top: 0;
                left: 0;
                width: 85%;
                height: 100%;
                background: #fff;
                z-index: 9999;
                overflow-y: auto;

                /* 🔥 Animation */
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;

                box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
            }

            .preview-controls.active {
                transform: translateX(0);
            }

            .preview-wrapper {
                overflow-x: auto;
            }

            .document-canvas {
                transform: scale(0.7);
                transform-origin: top center;
            }
    </style>

@endpush
@push('scripts')
    <script>
        const CURRENT_CURRENCY = "{{ $order->currency }}";
        const CURRENCY_MAP = {
            INR: "₹",
            USD: "$",
            EUR: "€",
            GBP: "£"
        }; const toggleBtn = $('#toggleControls');
        const controls = $('.preview-controls');

        // OPEN
        toggleBtn.click(function () {
            controls.addClass('active');
            toggleBtn.hide(); // ❌ hide from header
        });

        // CLOSE BUTTON (inside panel)
        $(document).on('click', '#closeControls', function () {
            controls.removeClass('active');
            toggleBtn.show(); // ✅ show again in header
        });

        // CLICK OUTSIDE TO CLOSE
        $(document).on('click', function (e) {
            if (
                controls.hasClass('active') &&
                !$(e.target).closest('.preview-controls, #toggleControls').length
            ) {
                controls.removeClass('active');
                toggleBtn.show();
            }
        });
        function applyDocType() {
            const type = $('input[name="docType"]:checked').val();

            if (type === 'pi') {
                $('.doc-pi').show();
                $('.doc-po').hide();
                $('.doc-title-text h3').text('Proforma Invoice');
            } else {
                $('.doc-pi').hide();
                $('.doc-po').show();
                $('.doc-title-text h3').text('Purchase Order');
            }
        }

        $(document).on('change', 'input[name="docType"]', applyDocType);
        /* ===============================
           VISIBILITY ENGINE
        ================================ */
        function applyVisibility() {

            // Sections
            $('.toggle-section').each(function () {
                const target = '.section-' + $(this).data('target');
                $(target).toggle(this.checked);
            });

            // Extra Fields
            $('.toggle-extra').each(function () {
                const target = '.' + $(this).data('target');
                $(target).toggle(this.checked);
            });
        }

        /* ===============================
           CURRENCY
        ================================ */
        function updateCurrency() {

            const cur = CURRENT_CURRENCY; // ✅ from DB
            const symbol = CURRENCY_MAP[cur] ?? '₹';

            $('.currency-symbol').text(symbol);
        }

        /* ===============================
           EVENTS
        ================================ */
        $(document).on('change', '.toggle-section, .toggle-extra', applyVisibility);
        $(document).on('change', '#currencySelector', updateCurrency);

        /* ===============================
           INIT
        ================================ */
        $(document).ready(function () {
            applyVisibility();
            updateCurrency(); applyDocType();
        });
    </script>
    <script>
        function printDocument() {
            applyVisibility(); // ensure latest checkbox state
            window.print();
        }
    </script>
    <script>
        function savePdf(button) {

            button.disabled = true;

            const sections = [];
            $('.toggle-section:checked').each(function () {
                sections.push($(this).data('target'));
            });

            const extras = [];
            $('.toggle-extra:checked').each(function () {
                extras.push($(this).data('target'));
            });

            const currency = $('#currencySelector').val();
            const docType = $('input[name="docType"]:checked').val();
            const params = new URLSearchParams({
                sections: sections.join(','),
                extras: extras.join(','),
                currency: currency, type: docType
            });

            const url = "{{ route('orders.proforma', [$company->id, $order->id]) }}?" + params;

            window.open(url, '_blank');

            button.disabled = false;
        }

        $(document).on('change', '#buyer_seller', function () {

            // ONLY when unchecked
            if (!$(this).is(':checked')) {

                $('.buyer_seller-checkbox').prop('checked', false);
            } else {
                $('.buyer_seller-checkbox').prop('checked', true);

                applyVisibility();
            }
        });
        $(document).on('change', '#Item_section', function () {

            let isChecked = $(this).prop('checked');

            $('.item_section').prop('checked', isChecked);

            applyVisibility();
        });
        $(document).on('change', '#total_section', function () {

            let isChecked = $(this).prop('checked');

            $('.total-sections').prop('checked', isChecked);

            applyVisibility();
        });
        $(document).on('change', '#payments_total', function () {

            let isChecked = $(this).prop('checked');

            $('.payment-total-sections').prop('checked', isChecked);

            applyVisibility();
        });
        $(document).on('change', '#doc_info', function () {

            let isChecked = $(this).prop('checked');

            $('.document-info').prop('checked', isChecked);

            applyVisibility();
        });
    </script>
@endpush