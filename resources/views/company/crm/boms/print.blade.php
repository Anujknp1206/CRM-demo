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
                            <a href="{{ route('boms.index', ['company' => $company->id]) }}">Back</a>
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
                    <div class="preview-controls no-print">
                        @include('company.crm.boms.partials.print-controls')
                    </div>
                    {{-- RIGHT LIVE PREVIEW --}}
                    <div class="preview-content">
                        <div class="preview-wrapper">
                            <div id="printContent" class="document-canvas">
                                @include('company.crm.boms.partials.document')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="translateLoader"
        style="display:none;
                                                                                                            position:fixed;
                                                                                                            top:0;left:0;
                                                                                                            width:100%;height:100%;
                                                                                                            background:rgba(255,255,255,0.7);
                                                                                                            z-index:9999;         /* ✅ ADD */
                                                                                                            align-items:center;    /* ✅ */
                                                                                                            justify-content:center;">
        <div class="spinner-border text-success" style="width:3rem;height:3rem;"></div>
    </div>
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

        .items-table thead th {
            border: 1px solid #aaa !important;
            /* 🔥 white borders */
            /* keep your header color */
            color: #fff;
        }

        .items-table {
            width: 100%;
        }

        .items-table td,
        .items-table th {
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
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
            page-break-inside: avoid;
        }

        .section-company {
            color: #1e4620;
        }

        /* =============================== */
        /* TOTAL TABLE */
        /* =============================== */
        .doc-title {
            text-align: center;
            font-weight: 700;
            color: #1e4620;
            background: #e2efda;
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
            background: #e2efda;
            padding: 6px;
            border: 1px solid #bbb;
            text-align: left;
            color: #1e4620;
            width: 30%;
        }

        .docinfo-table td {
            padding: 6px;
            border: 1px solid #bbb;
            background: #e2efda;

        }

        .buyer-seller-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .buyer-seller-table th {
            background: #1e4620;
            color: white;
            padding: 6px;
            text-align: left;
            border: 1px solid #1e4620;
        }

        .buyer-seller-table td {
            border: 1px solid #1e4620;
            padding: 8px;
            line-height: 1.5;
        }

        .section-remarks {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .section-remarks th {
            color: #1e4620;
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

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
        }

        .signature-header th {
            background: #1e4620;
            /* ✅ quotation style */
            color: #fff;
            padding: 8px;
            border: 1px solid #1e4620;
        }

        .instructions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 13px;
        }

        .instructions-header td {
            background: #1e4620;
            /* ✅ quotation style */
            color: #fff;
            padding: 6px;
            font-weight: 600;
            border: 1px solid #1e4620;
        }

        .instructions-body td {
            background: #f5e6c8;
            /* light clean background */
            padding: 10px;
            border: 1px solid #999;
        }

        .signature-space td {
            height: 60px;
            border: 1px solid #ccc;
        }

        .signature-names td {
            background: #e2efda;
            font-weight: 600;
            border: 1px solid #ccc;
            padding: 6px;
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


        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .order-row {
            background: #1e4620;
            color: #fff;
        }

        .items-table th {
            background: #375623;
            color: #fff;
            border: 1px solid #375623;
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

        .col-hsn {
            text-align: center;
        }

        .col-qty {
            text-align: center;
        }


        .section-terms {
            margin-top: 10px;
        }

        .col-sn {
            width: 5%;
        }

        .col-code {
            width: 15%;
        }

        .col-part {
            width: 15%;
        }

        .col-specs {
            width: 10%;
        }

        .col-qty {
            width: 10%;
        }

        .col-notes {
            width: 15%;
        }

        .terms-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .terms-table th {
            background: #1e4620;
            color: #fff;
            text-align: left;
            padding: 6px 8px;
            border: 1px solid #1e4620;
        }

        .terms-table td {
            border: 1px solid #aaa;
            padding: 10px;
            line-height: 1.6;
        }

        .section-terms {
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .terms-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            page-break-inside: avoid;
        }

        .terms-table tr {
            page-break-inside: avoid;
        }

        .terms-table th,
        .terms-table td {
            page-break-inside: avoid;
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
    </style>
@endpush

@push('scripts')
    <script>
        const controls = $('.preview-controls');
        const toggleBtn = $('#toggleControls');
        let currentLang = 'en';
        toggleBtn.click(function () {
            controls.addClass('active');
            toggleBtn.hide();
        });

        // CLOSE BUTTON
        $(document).on('click', '#closeControls', function () {
            controls.removeClass('active');
            toggleBtn.show();
        });

        // CLICK OUTSIDE
        $(document).on('click', function (e) {
            if (
                controls.hasClass('active') &&
                !$(e.target).closest('.preview-controls, #toggleControls').length
            ) {
                controls.removeClass('active');
                toggleBtn.show();
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // 🔹 SECTION TOGGLE
            document.querySelectorAll('.toggle-section').forEach(cb => {
                cb.addEventListener('change', function () {
                    let target = this.dataset.target;

                    document.querySelectorAll('.toggle-' + target).forEach(el => {
                        el.style.display = this.checked ? '' : 'none';
                    });
                });
            });
            // 🔁 ORDER TOGGLE
            document.querySelectorAll('.order-toggle').forEach(cb => {
                cb.addEventListener('change', function () {

                    let orderId = this.dataset.order;

                    document.querySelectorAll('.order-group-' + orderId)
                        .forEach(row => {
                            row.style.display = this.checked ? '' : 'none';
                        });

                    recalculateSerials();
                });
            });
            function recalculateSerials() {

                let orderIndex = 1;

                document.querySelectorAll('.order-row').forEach(orderRow => {

                    if (orderRow.style.display === 'none') return;

                    // ✅ Set ORDER serial
                    orderRow.querySelector('.order-serial').innerText = orderIndex + '. ';
                    orderIndex++;

                    let orderId = orderRow.dataset.order;
                    let bomIndex = 1;

                    // ✅ Loop BOM rows inside this order
                    document.querySelectorAll('.bom-row[data-order="' + orderId + '"]')
                        .forEach(bomRow => {

                            if (bomRow.style.display === 'none') return;

                            bomRow.querySelector('.bom-serial').innerText = bomIndex;
                            bomIndex++;
                        });

                });
            }
            recalculateSerials();

            // 🔁 BOM ITEM TOGGLE
            document.querySelectorAll('.bom-toggle').forEach(cb => {
                cb.addEventListener('change', function () {

                    let bomId = this.dataset.bom;

                    document.querySelectorAll('.bom-id-' + bomId)
                        .forEach(row => {
                            row.style.display = this.checked ? '' : 'none';
                        });

                    recalculateSerials();
                });
            });
            // 🔹 COLUMN TOGGLE
            document.querySelectorAll('.column-toggle').forEach(cb => {
                cb.addEventListener('change', function () {
                    let col = this.dataset.col;

                    document.querySelectorAll('.col-' + col).forEach(el => {
                        el.style.display = this.checked ? '' : 'none';
                    });
                });
            });
            document.querySelectorAll('.order-toggle').forEach(cb => {
                cb.addEventListener('change', function () {

                    let orderId = this.dataset.order;
                    let isChecked = this.checked;

                    // Toggle BOM checkboxes
                    document.querySelectorAll('.bom-toggle[data-order="' + orderId + '"]')
                        .forEach(bomCb => {
                            bomCb.checked = isChecked;
                        });

                    // Toggle rows
                    document.querySelectorAll('.order-group-' + orderId)
                        .forEach(row => {
                            row.style.display = isChecked ? '' : 'none';
                        });

                    recalculateSerials();
                });
            }); document.querySelectorAll('.bom-toggle').forEach(cb => {
                cb.addEventListener('change', function () {

                    let orderId = this.dataset.order;

                    let all = document.querySelectorAll('.bom-toggle[data-order="' + orderId + '"]');
                    let checked = document.querySelectorAll('.bom-toggle[data-order="' + orderId + '"]:checked');

                    let orderCb = document.querySelector('.order-toggle[data-order="' + orderId + '"]');

                    // 🔥 If ANY checked → order checked
                    if (checked.length > 0) {
                        orderCb.checked = true;
                    }

                    // 🔥 If NONE checked → order unchecked
                    if (checked.length === 0) {
                        orderCb.checked = false;
                    }

                    // 🔹 Show/hide individual row
                    let bomId = this.dataset.bom;
                    document.querySelectorAll('.bom-id-' + bomId)
                        .forEach(row => {
                            row.style.display = this.checked ? '' : 'none';
                        });

                    recalculateSerials();
                });
            });
        });
    </script>
    <script>

        $('#toHindi').click(function () {

            $('.translatable-area').each(function () {

                let hi = $(this).data('hi');

                if (hi !== undefined) {

                    $(this).html(hi);

                }

            });

        });

        $('#toEnglish').click(function () {

            $('.translatable-area').each(function () {

                let en = $(this).data('en');

                if (en !== undefined) {

                    $(this).html(en);

                }

            });

        });

        function printDocument() {
            window.print();
        }
        $('#downloadPdf').click(function () {
            saveBomPdf(this);
        });
        function saveBomPdf(button) {

            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';

            const selectedSections = [];
            $('.toggle-section:checked').each(function () {
                selectedSections.push($(this).data('target'));
            });

            const selectedColumns = [];
            $('.column-toggle:checked').each(function () {
                selectedColumns.push($(this).data('col'));
            });

            const selectedRows = [];
            $('.bom-toggle:checked').each(function () {
                selectedRows.push($(this).data('bom'));
            });

            const params = new URLSearchParams({
                sections: selectedSections.join(','),
                columns: selectedColumns.join(','),
                rows: selectedRows.join(','),
                lang: currentLang // ✅ important
            });

            fetch("{{ route('bom.pdf', [$company->id, $bom->id]) }}?" + params.toString())
                .then(response => {
                    const disposition = response.headers.get('Content-Disposition');
                    let fileName = "BOM.pdf";

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
                    a.click();

                    window.URL.revokeObjectURL(url);
                })
                .catch(() => alert("Error generating PDF"))
                .finally(() => {
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-file-pdf"></i> Save PDF';
                });
        }
    </script>
@endpush