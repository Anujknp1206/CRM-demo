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
                    <div class="preview-controls no-print">
                        <div class="print-controls">
                            @include('company.crm.orders.partials.print-controls')
                        </div>
                    </div>
                    <div class="preview-content">
                        <div class="preview-wrapper">
                            <div id="printContent" class="document-canvas">
                                @include('company.crm.orders.partials.document')
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

        .quotation-remarks {

            line-height: 1.2;
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



        .section-totals {
            margin-top: 10px;
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
            width: 40%;
            line-height: 1.5;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        .col-hsn {
            text-align: center;
        }

        .col-qty {
            text-align: center;
        }

        .col-name {
            width: 15%;
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

            /* =========================
                                   HIDE UI
                                ========================== */
            .no-print,
            .preview-controls,
            .content-header,
            .main-footer {
                display: none !important;
            }

            /* =========================
                                   RESET LAYOUT
                                ========================== */
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
                width: 100% !important;
                height: auto !important;
                overflow: visible !important;
                background: #fff !important;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;

                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* =========================
                           TOTALS SECTION FIX
                        ========================== */

            .section-totals {

                width: 320px !important;

                margin-left: auto !important;
                margin-top: 15px !important;

                page-break-inside: avoid !important;
                break-inside: avoid !important;

                overflow: visible !important;
            }

            .totals-table {

                width: 100% !important;

                border-collapse: collapse !important;

                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .totals-table tr {

                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .totals-table td {

                padding: 8px 10px !important;

                border: 1px solid #cfcfcf !important;

                font-size: 12px !important;

                vertical-align: middle !important;
            }

            .totals-table .label {

                text-align: right !important;

                font-weight: 500 !important;

                width: 65% !important;
            }

            .totals-table .value {

                text-align: right !important;

                white-space: nowrap !important;

                width: 35% !important;
            }

            /* FINAL AMOUNT ROW */

            .final-row td {

                background: #2f4f88 !important;

                color: #fff !important;

                font-weight: bold !important;

                font-size: 13px !important;

                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* TAXABLE ROW */

            .taxable-amount td {

                font-weight: bold !important;
            }

            /* =========================
                                   DOCUMENT
                                ========================== */
            .document-canvas {
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                background: #fff !important;

                box-shadow: none !important;
                -webkit-box-shadow: none !important;
                filter: none !important;

                overflow: visible !important;
            }

            /* =========================
                                   REMOVE ALL EFFECTS
                                ========================== */
            * {
                box-shadow: none !important;
                -webkit-box-shadow: none !important;
                text-shadow: none !important;
                filter: none !important;
            }

            /* =========================
                                   TABLE PRINT FIX
                                ========================== */

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            /* Repeat table header on each page */
            thead {
                display: table-header-group;
            }

            /* Optional footer repeat */
            tfoot {
                display: table-footer-group;
            }

            /* ALLOW CONTENT TO FLOW */
            tr,
            td,
            th {
                page-break-inside: auto !important;
                break-inside: auto !important;
            }

            /* Prevent table breaking badly */
            .items-table {
                page-break-inside: auto !important;
            }

            .items-table tr {
                page-break-inside: auto !important;
            }

            .items-table td,
            .items-table th {
                vertical-align: top !important;
            }

            /* LONG DESCRIPTION FIX */
            .col-desc {
                white-space: normal !important;
                word-break: break-word !important;
                overflow-wrap: break-word !important;
                line-height: 1.5 !important;
            }

            /* =========================
                                   TERMS TABLE
                                ========================== */

            .terms-box {
                width: 100%;
                border: 1px solid #cfcfcf;
                border-collapse: collapse;
            }

            .terms-box th {
                background: #2b4c7e !important;
                color: #fff !important;
                text-align: left;
                font-weight: bold;
                padding: 7px 10px;
                border: 1px solid #2b4c7e;
            }

            .terms-box td {
                padding: 0;
                border: 1px solid #cfcfcf;
            }

            .terms-content {
                background: #f2f2f2 !important;
                padding: 12px;
                line-height: 1.7;
                font-size: 11px;
            }

            .terms-content p {
                margin: 4px 0;
            }

            /* =========================
                                   DOC INFO COLORS
                                ========================== */

            .docinfo-table th,
            .docinfo-table td {
                background-color: #f1f1f1 !important;

                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .items-table th,
            .buyer-seller-table th,
            .terms-table th,
            .final-row td {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* =========================
                                   PAGE
                                ========================== */

            @page {
                size: A4;
                margin: 8mm;
            }
        }



        .company-logo {
            width: 100%;
            max-width: 220px;
            /* desktop size */
            height: auto;
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
        }
    </style>

@endpush
@push('scripts')
    <script>
        /* ===============================
           GLOBAL MAPS
        ================================ */
        const CURRENT_CURRENCY = "{{ $order->currency }}";
        const CURRENCY_MAP = {
            INR: "₹",
            USD: "$",
            EUR: "€",
            GBP: "£"
        };
        const toggleBtn = $('#toggleControls');
        const controls = $('.preview-controls');
        window.CURRENT_LANG = 'en';
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
        /* ===============================
           VISIBILITY ENGINE
        ================================ */
        function applyVisibility() {

            /* ---------- SECTIONS ---------- */
            $('.toggle-section').each(function () {
                const target = '.section-' + $(this).data('target');
                $(target).toggle(this.checked);
            });

            /* ---------- EXTRA FIELDS ---------- */
            $('.toggle-extra').each(function () {
                const target = '.' + $(this).data('target');
                $(target).toggle(this.checked);
            });

            /* ---------- COLUMNS (HEADER + BODY) ---------- */
            $('.column-toggle').each(function () {
                const col = '.col-' + $(this).data('col');
                $(col).toggle(this.checked);
            });

            /* ---------- ROWS ---------- */
            $('.row-toggle').each(function () {
                const row = '.row-' + $(this).data('row');
                $(row).toggle(this.checked);
            });

            /* ---------- SERIAL NUMBER RE-CALC ---------- */
            recalcSerialNumbers();
        }

        /* ===============================
           SERIAL NUMBER FIX
        ================================ */
        function recalcSerialNumbers() {

            let sn = 1;

            $('.section-items tbody tr:visible').each(function () {
                $(this).find('.col-sn').text(sn);
                sn++;
            });
        }
        /* ===============================
           INIT
        ================================ */
        $(document).ready(function () {
            applyVisibility();
        });
        /* ===============================
        DOCUMENT TYPE (QUOTATION / PI)
        ================================ */
        function updateDocumentType() {

            const type = $('input[name="doc_type"]:checked').val();

            if (type === 'po') {

                $('.doc-order').hide();
                $('.doc-po').show();

                $('#docNumberLabel').text('PO Number');
                $('#docNumberValue').text($('#docNumberValue').data('po'));

                $('#docDateLabel').text('PO Date');
                $('#docDateValue').text($('#docDateValue').data('po'));

            } else {

                $('.doc-po').hide();
                $('.doc-order').show();

                $('#docNumberLabel').text('Order Number');
                $('#docNumberValue').text($('#docNumberValue').data('order'));

                $('#docDateLabel').text('Order Date');
                $('#docDateValue').text($('#docDateValue').data('order'));
            }
        }


        /* ===============================
        CURRENCY HANDLER
        ================================ */
        function updateCurrency() {

            const cur = CURRENT_CURRENCY; // ✅ from DB
            const symbol = CURRENCY_MAP[cur] ?? '₹';

            $('.currency-symbol').text(symbol);
        }

        /* ===============================
        EVENT BINDINGS
        ================================ */
        $(document).on(
            'change',
            `.toggle-section,.toggle-extra,.column-toggle,.row-toggle`,
            applyVisibility
        );

        $(document).on('change', 'input[name="doc_type"]', updateDocumentType);
        $(document).on('change', '#currencySelector', updateCurrency);

        /* ===============================
        INIT ON LOAD
        ================================ */
        $(document).ready(function () {
            updateDocumentType();
            updateCurrency();
            applyVisibility();
        });
    </script>
    <script>

        function switchLanguage(lang) {

            // Normal fields using data-en/data-hi
            $('.translatable-area').each(function () {

                let content = $(this).data(lang);

                if (content !== undefined) {
                    $(this).html(content);
                }
            });

            // Description fields using lang-en/lang-hi
            $('.col-desc').each(function () {

                if (lang === 'hi') {

                    $(this).find('.lang-en').hide();
                    $(this).find('.lang-hi').show();

                } else {

                    $(this).find('.lang-hi').hide();
                    $(this).find('.lang-en').show();
                }
            });

            window.CURRENT_LANG = lang;
        }
        $('#toHindi').click(function () {

            $('#selectedLanguage').val('hi');

            switchLanguage('hi');

            $('#pdfBtnWrapper').hide(); // Hide PDF button
        });

        $('#toEnglish').click(function () {

            $('#selectedLanguage').val('en');

            switchLanguage('en');

            $('#pdfBtnWrapper').show(); // Show PDF button
        });
        function printDocument() {
            window.print();
        }
        function savePdf(button) {

            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';

            const selectedSections = [];
            $('.toggle-section:checked').each(function () {
                selectedSections.push($(this).data('target'));
            });

            const selectedExtras = [];
            $('.toggle-extra:checked').each(function () {
                selectedExtras.push($(this).data('target'));
            });

            const selectedColumns = [];
            $('.column-toggle:checked').each(function () {
                selectedColumns.push($(this).data('col'));
            });

            const selectedRows = [];
            $('.row-toggle:checked').each(function () {
                selectedRows.push($(this).data('row'));
            });

            const docType = $('input[name="doc_type"]:checked').val();
            const currency = $('#currencySelector').val();
            const params = new URLSearchParams({
                sections: selectedSections.join(','),
                extras: selectedExtras.join(','),
                columns: selectedColumns.join(','),
                rows: selectedRows.join(','),
                doc_type: docType,
                lang: window.CURRENT_LANG
            });

            fetch("{{ route('orders.pdf', [$company->id, $order->id]) }}?" + params.toString())
                .then(response => {
                    const disposition = response.headers.get('Content-Disposition');
                    let fileName = "document.pdf";

                    if (disposition && disposition.includes("filename=")) {
                        fileName = disposition.split("filename=")[1].replace(/"/g, '');
                    }

                    return response.blob().then(blob => ({ blob, fileName }));
                })
                .then(({ blob, fileName }) => {

                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = fileName;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();

                    window.URL.revokeObjectURL(url);
                })
                .catch(() => {
                    alert("Error generating PDF");
                })
                .finally(() => {
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-file-pdf"></i> Save PDF';
                });
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
    </script>
@endpush