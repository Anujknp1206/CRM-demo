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
                            <a href="{{ route('company.dashboard', ['company' => $company->id]) }}">Back</a>
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
                        @include('company.store.po.partials.print-controls')
                    </div>
                    {{-- RIGHT LIVE PREVIEW --}}
                    <div class="preview-content">
                        <div class="preview-wrapper">
                            <div id="printContent" class="document-canvas">
                                @include('company.store.po.partials.document')
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
            page-break-inside: avoid;
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
            margin-top: 10px;
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
        }

        .col-hsn {
            text-align: center;
        }

        .col-qty {
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
            margin-top: 10px;
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
    </style>
@endpush
@push('scripts')
    <script>
        $('#downloadBtn').on('click', function () {

            let btn = $(this);

            btn.find('.btn-text').addClass('d-none');
            btn.find('.btn-loader').removeClass('d-none');

            let sections = [];
            let extras = [];
            let columns = [];
            let rows = [];

            // ✅ Sections
            $('.toggle-section:checked').each(function () {
                sections.push($(this).data('target'));
            });

            // ✅ Extra fields
            $('.toggle-extra:checked').each(function () {
                extras.push($(this).data('target'));
            });

            // ✅ Columns
            $('.column-toggle:checked').each(function () {
                columns.push($(this).data('col'));
            });

            // ✅ Rows
            $('.row-toggle:checked').each(function () {
                rows.push($(this).data('row'));
            });

            // ✅ CALL PDF ROUTE
            let url = "{{ route('po.pdf', [$company->id, $po->id]) }}";

            let form = $('<form>', {
                action: url,
                method: 'POST'
            });

            form.append('@csrf');

            form.append(`<input type="hidden" name="sections" value="${sections.join(',')}">`);
            form.append(`<input type="hidden" name="extras" value="${extras.join(',')}">`);
            form.append(`<input type="hidden" name="columns" value="${columns.join(',')}">`);
            form.append(`<input type="hidden" name="rows" value="${rows.join(',')}">`);

            $('body').append(form);
            form.submit();

            setTimeout(() => {
                btn.find('.btn-text').removeClass('d-none');
                btn.find('.btn-loader').addClass('d-none');
            }, 2000);
        });
    </script>
    <script>
        const toggleBtn = $('#toggleControls');
        const controls = $('.preview-controls');

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
        $(document).ready(function () {

            // ================= SECTION TOGGLE =================
            $('.toggle-section').on('change', function () {
                let target = $(this).data('target');

                if ($(this).is(':checked')) {
                    $('.section-' + target).show();
                } else {
                    $('.section-' + target).hide();
                }
            });

            // ================= EXTRA FIELDS =================
            $('.toggle-extra').on('change', function () {
                let target = $(this).data('target');

                if ($(this).is(':checked')) {
                    $('.' + target).show();
                } else {
                    $('.' + target).hide();
                }
            });

            // ================= COLUMN TOGGLE =================
            $('.column-toggle').on('change', function () {
                let col = $(this).data('col');

                if ($(this).is(':checked')) {
                    $('.col-' + col).show();
                } else {
                    $('.col-' + col).hide();
                }
            });

            // ================= ROW TOGGLE =================
            $('.row-toggle').on('change', function () {
                let row = $(this).data('row');

                if ($(this).is(':checked')) {
                    $('.row-' + row).show();
                } else {
                    $('.row-' + row).hide();
                }
            });

            // ================= LANGUAGE SWITCH =================
            $('#toHindi').on('click', function () {
                $('body').css('font-family', 'Noto Sans Devanagari');
            });

            $('#toEnglish').on('click', function () {
                $('body').css('font-family', 'Arial');
            });

            // ================= PDF DOWNLOAD =================
            $('#downloadBtn').on('click', function () {

                let btn = $(this);

                btn.find('.btn-text').addClass('d-none');
                btn.find('.btn-loader').removeClass('d-none');

                html2pdf()
                    .from(document.getElementById('printArea'))
                    .set({
                        margin: 10,
                        filename: 'purchase-order.pdf',
                        html2canvas: { scale: 2 },
                        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                    })
                    .save()
                    .then(() => {
                        btn.find('.btn-text').removeClass('d-none');
                        btn.find('.btn-loader').addClass('d-none');
                    });
            });

        });
    </script>
@endpush