@extends('company.layouts.master')

@section('content')

    <section class="content-header">
        <div class="container-fluid">

            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $label }}</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button onclick="translateToHindi()" class="btn btn-primary btn-sm">
                        🌐 Hindi
                    </button>

                    <button onclick="resetTranslation()" class="btn btn-secondary btn-sm">
                        🔄 Original
                    </button>
                    {{-- ACTION BUTTONS --}}
                    <button onclick="window.print()" class="btn btn-success btn-sm">
                        <i class="fas fa-print"></i> Print
                    </button>

                    <button onclick="downloadPdf()" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                </div>
            </div>

        </div>
    </section>


    <section class="content">
        <div class="card">
            <div class="card-body">

                {{-- FULL WIDTH DOCUMENT --}}
                <div class="preview-wrapper">
                    <div id="printContent" class="document-canvas">
                        @include('company.crm.jobcard.partials.document')
                    </div>
                </div>

            </div>
        </div>
    </section>
    <div id="translateLoader" style="
                                                        display:none;
                                                        position:fixed;
                                                        top:0;
                                                        left:0;
                                                        width:100%;
                                                        height:100%;
                                                        background:rgba(255,255,255,0.7);
                                                        z-index:9999;
                                                        align-items:center;
                                                        justify-content:center;
                                                    ">
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

        .section-signatures {
            margin-top: 20px;
        }

        .sign-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .sign-table th {
            background: #1e4620;
            color: #fff;
            border: 1px solid #163818;
            padding: 6px;
            text-align: center;
        }

        .sign-table td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: center;
        }

        /* Bottom name row style */
        .sign-table tr:last-child td {
            font-weight: bold;
            background: #eaf4e4;
        }

        /* =============================== */
        /* LEFT PANEL */
        /* =============================== */

        .preview-controls {
            width: 320px;
            height: 800px;
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
            height: 800px;
            display: flex;
            overflow: hidden;
        }

        /* Scrollable preview area */
        .preview-wrapper {
            flex: 1;
            height: 800px;
            overflow-y: auto;
            background: #e9ecef;
            padding: 20px;
            -webkit-overflow-scrolling: touch;
        }

        /* =============================== */
        /* DOCUMENT */
        /* =============================== */

        .document-canvas {
            width: 100%;
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
            font-weight: 900;
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
            color: #1e4620;
            background: #e2efda;
            padding: 6px;
            border: 1px solid #bbb;
            text-align: left;
            width: 30%;
        }

        .docinfo-table td {
            padding: 6px;
            border: 1px solid #bbb;
            background: #fff;
            color: #000;
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
            border: 1px solid #163818;
        }

        .buyer-seller-table td {
            border: 1px solid #163818;
            padding: 8px;
            line-height: 1.5;
        }

        .section-remarks {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .section-remarks th {
            color: #fff;
            background: #1e4620;

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

        /* ALTERNATE ROW COLOR */
        .items-table tbody tr:nth-child(odd) {
            background: #f8f9f8;
            /* light grey/white */
        }

        .items-table th,
        .items-table td {
            font-size: 12px;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }

        /* COLUMN WIDTH CONTROL */
        .items-table td:nth-child(1) {
            width: 2%;

        }

        /* S.No */
        .items-table td:nth-child(2) {
            width: 10%;
        }

        /* Machine */
        .items-table td:nth-child(3) {
            width: 25%;
        }

        /* Description ↓ reduced */
        .items-table td:nth-child(4) {
            width: 15%;
        }

        /* Specs */
        .items-table td:nth-child(5) {
            width: 3%;
        }

        /* Qty */
        .items-table td:nth-child(6) {
            width: 5%;
        }

        /* Worker */
        .items-table td:nth-child(7) {
            width: 15%;
        }

        /* Remarks */
        .items-table td:nth-child(8) {
            width: 5%;
        }

        /* Status */
        .items-table tbody tr:nth-child(even) {
            background: #dfeadf;
            /* light green */
        }

        .remarks-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .remarks-table th {
            width: 25%;
            text-align: left;
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
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .items-table th {
            background: #1e4620;
            color: #fff;
            border: 1px solid #163818;
            padding: 6px;
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
            background: #1e4620;
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
            background: #1e4620;
            color: #fff;
            text-align: left;
            padding: 6px 8px;
            border: 1px solid #163818;
        }

        .terms-table td {
            border: 1px solid #aaa;
            padding: 10px;
            line-height: 1.6;
        }

        /* =============================== */
        /* PRINT (Safari Optimized) */
        /* =============================== */
        .remark-bg {
            background: #f5e6c8 !important;
            color: #1e4620 !important;
        }

        .docinfo-table {
            width: 100%;
            table-layout: fixed;
        }

        .docinfo-table th:nth-child(1),
        .docinfo-table th:nth-child(3) {
            width: 15%;
        }

        .docinfo-table td:nth-child(2),
        .docinfo-table td:nth-child(4) {
            width: 35%;
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

            .docinfo-table td,
            .docinfo-table th {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
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

            table.docinfo-table th {
                color: #1e4620 !important;
                background-color: #e2efda !important;
            }

            table.docinfo-table td {
                color: #1e4620 !important;
                background-color: #fff !important;
            }

            table.docinfo-table td.remark-bg {
                background-color: #f5e6c8 !important;
                color: #1e4620 !important;
            }


            .docinfo-table td.remark-bg {
                background-color: #f5e6c8 !important;
                color: #1e4620 !important;

                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                margin: 0mm;
            }

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
    </style>

@endpush
@push('scripts')
    <script>
        function downloadPdf() {
            const id = "{{ $planning->id ?? $jobcard->id ?? '' }}";
            const companyId = "{{ $company->id ?? request()->route('company')->id }}";

            window.open(`/company/${companyId}/jobcard/${id}/pdf`, '_blank');
        }
        async function translateText(text, targetLang) {
            const res = await fetch("{{ route('translate.text') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    text: text,
                    source: "auto",
                    target: targetLang
                })
            });

            const data = await res.json();
            return data.translated ?? text;
        }

        async function translateToHindi() {

            // 🔥 SHOW LOADER
            document.getElementById('translateLoader').style.display = 'flex';

            const elements = document.querySelectorAll('.translatable');

            for (let el of elements) {

                if (!el.dataset.original) {
                    el.dataset.original = el.innerHTML;
                }

                try {
                    const translated = await translateText(el.innerText, 'hi');
                    el.innerHTML = translated;
                } catch (e) {
                    console.error("Translation error:", e);
                }
            }

            // 🔥 HIDE LOADER
            document.getElementById('translateLoader').style.display = 'none';
        }

        function resetTranslation() {

            document.getElementById('translateLoader').style.display = 'flex';

            document.querySelectorAll('.translatable').forEach(el => {
                if (el.dataset.original) {
                    el.innerHTML = el.dataset.original;
                }
            });

            document.getElementById('translateLoader').style.display = 'none';
        }
    </script>
@endpush