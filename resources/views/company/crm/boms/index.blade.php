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
                                @can('production status')
                                    <a href="{{ route('production-status.index', $company->id) }}"
                                        class="btn btn-danger btn-sm">
                                        <i class="fa fa-cogs"></i> Production Status
                                    </a>
                                @endcan
                                @can('add bom')
                                    <a href="{{ route('boms.create', ['company' => $company->id]) }}"
                                        onclick="return handleBomClick(event, this)" data-company-id="{{ $company->id }}">
                                        <button class="btn btn-default btn-sm">
                                            <i class="fa fa-plus"></i> Add BOM
                                        </button>
                                    </a>
                                @endcan
                                <a href="{{ route('company.dashboard', ['company' => $company->id]) }}"
                                    class="btn btn-sm btn-success">
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
                                    <label>Search BOM</label>
                                    <input type="text" id="bom_search" class="form-control" placeholder="Search BOM Number">
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

                                <div class="card-body">
                                    <table class="table table-bordered" id="example1">
                                        <thead>
                                            <tr>
                                                <th>SN No.</th>
                                                <th>Bom Progress</th>
                                                <th>BOM No</th>
                                                <th>Order</th>
                                                <th>Item</th>
                                                <th>Issued Progress</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Delivery Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody id="bomrows"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="bomIssuesModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header text-white" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
                    <h5 class="modal-title" id="bomIssueModalTitle">
                        BOM Issue History
                    </h5>
                    <button class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body p-0">

                    <div class="bom-issues-wrapper">
                        <!-- 🔥 LEFT SIDE (ISSUE LIST) -->
                        <div class="border-right" style="overflow-y:auto;">
                            <ul class="list-group list-group-flush" id="bomIssueList"></ul>
                        </div>

                        <!-- 🔥 RIGHT SIDE (DETAILS) -->
                        <div style="flex:1; padding:15px;" id="bomIssueDetails">
                            <div class="text-center text-muted mt-5">
                                👉 Select an issue to view details
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .gap-2 {
            gap: 10px;
        }

        .badge-teal {
            background-color: #20c997;
            color: #fff;
        }

        .btn-primary,
        .btn-secondary {
            border-radius: 6px;
            height: 40px;
        }

        table td {
            white-space: normal !important;
            word-break: break-word;
            max-width: 200px;
        }

        .progress-ring {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: conic-gradient(var(--color) calc(var(--progress) * 1%), #e9ecef 0%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
        }

        .progress-ring[data-progress="0"] {
            background: var(--color);
        }

        .progress-ring::before {
            content: '';
            width: 30px;
            height: 30px;
            background: #fff;
            border-radius: 50%;
            position: absolute;
        }

        .progress-ring span {
            position: relative;
            font-size: 10px;
            font-weight: bold;
        }

        #bomIssueDetails {
            background: #f9fbfd;
            border-left: 1px solid #ddd;
        }

        #bomIssueList .list-group-item {
            padding: 10px 12px;
            font-size: 13px;
        }

        #bomIssueList .list-group-item.active {
            background: #1b4f72 !important;
            border-color: #1b4f72 !important;
            color: #fff !important;
        }

        #bomIssueList .list-group-item.active small {
            color: #fff !important;
        }

        #bomIssueList .list-group-item.active .badge {
            background: #fff !important;
            color: #1b4f72 !important;
        }

        .status-circle {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .bom-issues-wrapper {
            display: flex;
            height: 420px;
        }

        .card-body {
            padding: 10px 5px !important;
        }

        #bomIssueList {
            overflow-y: auto;
        }

        #bomIssueDetails {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
        }

        .issue-header-row {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 5px;
        }

        .issue-header-row div {
            font-size: 14px;
        }

        @media (max-width: 768px) {

            .bom-issues-wrapper {
                flex-direction: column;
                height: auto;
            }

            .issue-header-row {
                flex-direction: column;
                /* 🔥 stack */
                align-items: flex-start;
                gap: 3px;
            }

            .issue-header-row div {
                width: 100%;
                font-size: 13px;
            }

            #bomIssueList {
                display: flex;
                flex-direction: row;
                overflow-x: auto;
                overflow-y: hidden;
                width: 100%;
                white-space: nowrap;
                border-bottom: 1px solid #ddd;
            }

            .table,
            .status-circle .remark {
                font-size: 11px;
            }

            #bomIssueList .list-group-item {
                flex: 0 0 auto;
                min-width: 180px;
                border-right: 1px solid #ddd;
                border-bottom: none;
                text-align: center;
            }

            #bomIssueList::-webkit-scrollbar {
                display: none;
            }

            .issue-item.active {
                background: #1b4f72 !important;
                color: #fff;
                border-radius: 0;
            }

            #bomIssueDetails {
                padding: 10px;
            }
        }

        .bom-part-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            background: #eaf2f8;
            padding: 10px;
            border-left: 4px solid #1b4f72;
            margin-bottom: 10px;
            border-radius: 6px;
        }

        .bom-part-grid div {
            background: #ffffff;
            padding: 6px;
            border-radius: 4px;
            font-size: 13px;
        }

        .bom-sub {
            background: #eef3f7;
            padding: 10px;
            margin: 8px 0 8px 15px;
            border-radius: 6px;
        }

        .bom-sub-title {
            margin-bottom: 8px;
            font-size: 14px;
        }

        .bom-sub-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 8px;
        }

        .bom-sub-full {
            background: #ffffff;
            padding: 6px;
            border-radius: 4px;
            margin-top: 5px;
            font-size: 13px;
        }

        .progress-wrapper {
            position: relative;
            display: inline-block;
        }

        .progress-tooltip {
            position: absolute;
            bottom: auto;
            left: 50%;
            transform: translateX(-50%);
            background: #2f2f2f;
            color: #fff;
            padding: 5px 7px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            line-height: 1.5;
            text-align: left;
            min-width: 240px;
            max-width: 320px;
            white-space: normal;
            z-index: 999999;
            min-height: auto;
            opacity: 0;
            visibility: hidden;
            transition: all .2s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* 🔥 Tooltip Arrow */
        .progress-tooltip::after {

            content: "";

            position: absolute;
            top: 50%;
            left: 50%;
            transform: translateX(-50%);

            border-width: 7px;

            border-style: solid;

            border-color: #2f2f2f transparent transparent transparent;
        }

        /* 🔥 SHOW ON HOVER */
        .progress-wrapper:hover .progress-tooltip {
            opacity: 1;
            visibility: visible;
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
            $('#from_date, #to_date').on("change keyup input", function () {
                if (!this.value || this.value === "") {
                    $(this).val(null);
                }
            });
            loadBoms();
        });
        function loadBoms() {
            let params = {};

            let search = $('#bom_search').val() || "";
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            // SEND ONLY NON-EMPTY VALUES
            if (search !== "") params.search = search;
            if (from_date !== "") params.from_date = from_date;
            if (to_date !== "") params.to_date = to_date;

            // SHOW LOADER
            $("#loader").show();
            $("#example1").hide();
            $.ajax({
                url: "{{ route('boms.data', ['company' => $company->id]) }}",
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

                    $('#bomrows').html(response);
                    if ($("#bomrows").find("tr").length === 0 || $("#bomrows").find("tr td").length === 1) {
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

                    // HIDE LOADER & SHOW TABLE
                    $("#loader").hide();
                    $("#example1").show();
                },
                error: function () {
                    $("#loader").hide();
                    $("#example1").show();
                }

            });
        }
        $(document).ready(function () {

            $('#from_date, #to_date').on("change keyup input", function () {
                if (!this.value || this.value === "") {
                    $(this).val(null);
                }
            });

            let bomTimer;

            $('#bom_search').on('keyup', function () {

                clearTimeout(bomTimer);

                bomTimer = setTimeout(function () {
                    loadBoms();
                }, 500);

            });

            loadBoms();
        });

        // Filter Button
        $('#filter').click(function () {
            loadBoms();
        });

        // Reset Button
        $('#reset').click(function () {
            $('#bom_search').val('');
            $('#from_date').val(null); fromPicker.clear();
            toPicker.clear();
            $('#to_date').val(null);

            loadBoms();
        });
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
        });
    </script>
    <script> $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
    </script>
    <script>
        $(document).on('click', '.show-bom', function () {

            let bomId = $(this).data('id');

            function getStatusBadge(status) {
                let badgeClass = 'secondary';
                let label = status;

                switch (status) {
                    case 'draft':
                        badgeClass = 'secondary';
                        label = 'Draft';
                        break;
                    case 'in_progress':
                        badgeClass = 'warning';
                        label = 'In Progress';
                        break;
                    case 'completed':
                        badgeClass = 'success';
                        label = 'Completed';
                        break;
                    default:
                        badgeClass = 'dark';
                }

                return `<span class="badge badge-${badgeClass}">${label}</span>`;
            }

            function getBomItemStatusBadge(status) {
                let badgeClass = 'secondary';
                let label = status;

                switch (status) {
                    case 'pending':
                        label = 'Pending';
                        break;
                    case 'assigned':
                        badgeClass = 'primary';
                        label = 'Assigned';
                        break;
                    case 'in_progress':
                        badgeClass = 'warning';
                        label = 'In Progress';
                        break;
                    case 'completed':
                        badgeClass = 'success';
                        label = 'Completed';
                        break;
                    case 'on_hold':
                        badgeClass = 'warning';
                        label = 'On Hold';
                        break;
                    default:
                        badgeClass = 'dark';
                }

                return `<span class="badge badge-${badgeClass}">${label}</span>`;
            }

            $('#bomDetailsModal').modal('show');
            $('#bomDetailsContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');

            $.ajax({
                url: `/company/{{ $company->id }}/boms/${bomId}/details`,
                type: "GET",

                success: function (res) {

                    let html = `
                                                                                                                                    <style>
                                                                                                                                        .bom-section { margin-bottom:20px; }
                                                                                                                                        .bom-title { font-weight:600; font-size:16px; margin-bottom:10px; border-bottom:1px solid #ddd; padding-bottom:5px; }
                                                                                                                                        .bom-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; }
                                                                                                                                        .bom-box { background:#f8f9fa; padding:8px; border-radius:6px; font-size:14px; }

                                                                                                                                        .bom-item-card { border:1px solid #ddd; border-radius:6px; margin-bottom:15px; }
                                                                                                                                        .bom-item-header { background:#1b4f72; color:#fff; padding:6px 10px; font-size:18px; }
                                                                                                                                        .bom-item-body { padding:10px; }

                                                                                                                                        .bom-part {
                                                                                                                                            background:#eaf2f8;
                                                                                                                                            padding:10px;
                                                                                                                                            border-left:4px solid #1b4f72;
                                                                                                                                            margin-bottom:10px;
                                                                                                                                            border-radius:6px;
                                                                                                                                        }

                                                                                                                                        .bom-sub {
                                                                                                                                            background:#eef3f7;
                                                                                                                                            padding:8px;
                                                                                                                                            margin:6px 0 6px 15px;
                                                                                                                                            border-radius:5px;
                                                                                                                                        }
                                                                                                                                    </style>
                                                                                                                                `;

                    // =============================
                    // 🔥 HEADER INFO
                    // =============================
                    html += `
                                                                                                                                    <div class="bom-section">
                                                                                                                                        <div class="bom-title">Customer / Order / BOM Info</div>

                                                                                                                                        <div class="bom-grid">
                                                                                                                                            <div class="bom-box"><b>Customer:</b> ${res.order.customer_name || '-'}</div>
                                                                                                                                            <div class="bom-box"><b>Email:</b> ${res.order.email || '-'}</div>
                                                                                                                                           <div class="bom-box"><b>Phone:</b>  ${res.custom_mobile || '-'}</div>

                                                                                                                                            <div class="bom-box"><b>Order No:</b> ${res.order.order_number || '-'}</div>
                                                                                                                                            <div class="bom-box"><b>Contact Person:</b> ${res.order.contact_person || '-'}</div>
                                                                                                                                            <div class="bom-box"><b>Delivery Date:</b> ${res.delivery_date_formatted || '-'}</div>

                                                                                                                                            <div class="bom-box"><b>BOM No:</b> ${res.bom_number}</div>
                                                                                                                                            <div class="bom-box"><b>Status:</b> ${getStatusBadge(res.status)}</div>
                                                                                                                                            <div class="bom-box"><b>Priority:</b> ${res.priority?.name || '-'}</div>

                                                                                                                                            <div class="bom-box"><b>Supervisor:</b> ${res.supervisor?.first_name || '-'}</div>
                                                                                                                                            <div class="bom-box"><b>Checked By:</b> ${res.checker?.first_name || '-'}</div>
                                                                                                                                            <div class="bom-box"><b>Remarks:</b> ${res.remarks || '-'}</div>
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                `;

                    // =============================
                    // 🔁 ORDER ITEMS
                    // =============================
                    html += `<div class="bom-title">Items</div>`;

                    res.order.items.forEach((orderItem, index) => {

                        html += `
                                                                                                                                        <div class="bom-item-card">
                                                                                                                                            <div class="bom-item-header">
                                                                                                                                                ${index + 1}. ${orderItem.item_name}
                                                                                                                                            </div>
                                                                                                                                            <div class="bom-item-body">
                                                                                                                                    `;

                        // 🔥 FILTER PARTS FOR THIS ORDER ITEM
                        let parts = res.parts.filter(part =>
                            part.items.some(i => i.order_item_id == orderItem.id)
                        );

                        if (parts.length === 0) {
                            html += `<div class="bom-sub">No BOM Parts</div>`;
                        }

                        // =============================
                        // 🔁 LOOP PARTS
                        // =============================
                        parts.forEach(part => {

                            html += `
                                                                                                                                           <div class="bom-part-grid">
                                                                                                                    <div><b>Part:</b> ${part.part_name}</div>
                                                                                                                    <div><b>Weightage:</b> ${part.weightage || 0}%</div>
                                                                                                                    <div><b>Spec:</b> ${part.spec?.name || '-'}</div>
                                                                                                                    <div><b>Shift:</b> ${part.shift?.name || '-'}</div>
                                                                                                                </div>
                                                                                                                                        `;

                            let partItems = part.items.filter(i => i.order_item_id == orderItem.id);

                            if (partItems.length === 0) {
                                html += `<div class="bom-sub">No Items</div>`;
                            }

                            // =============================
                            // 🔁 LOOP ITEMS
                            // =============================
                            partItems.forEach(bItem => {

                                html += `
                                                                                                                <div class="bom-sub">
                                                                                                                    <div class="bom-sub-title"><b>${bItem.item?.name || '-'}</b></div>

                                                                                                                    <div class="bom-sub-grid">
                                                                                                                        <div><b>Qty:</b> ${bItem.quantity}</div>
                                                                                                                        <div><b>Status:</b> ${getBomItemStatusBadge(bItem.status)}</div>
                                                                                                                        <div><b>Department:</b> ${bItem.department?.name || '-'}</div>
                                                                                                                        <div><b>Employee:</b> ${bItem.employee ? `${bItem.employee.first_name || ''} ${bItem.employee.last_name || ''}`.trim() : '-'}</div>
                                                                                                                    </div>

                                                                                                                    <div class="bom-sub-full">
                                                                                                                        <b>Notes:</b> ${bItem.notes || '-'}
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            `;
                            });

                        });

                        html += `</div></div>`;
                    });

                    $('#bomDetailsContent').html(html);
                }
            });
        });  </script>
    <script>
        $(document).on('click', '.show-bom-issues', function () {

            let bomId = $(this).data('id');

            $('#bomIssueList').html('<li class="list-group-item">Loading...</li>');
            $('#bomIssueDetails').html('<div class="text-center mt-5">Loading...</div>');

            $.get("{{ route('bom.issues', [$company->id, 'ID']) }}".replace('ID', bomId), function (res) {
                $('#bomIssueModalTitle').html(
                    `BOM Issue History (${res.total_bom_items} Items)`
                );
                // 🔴 NO ISSUE
                if (!res.has_issues) {

                    $('#bomIssueList').html(`
                                                                                                                        <li class="list-group-item text-center text-muted">
                                                                                                                            <i class="fa fa-info-circle"></i> No Issue Created
                                                                                                                        </li>
                                                                                                                    `);

                    $('#bomIssueDetails').html(`
                                                                                                                        <div class="text-center text-muted mt-5">
                                                                                                                            <i class="fa fa-folder-open fa-2x mb-2"></i>
                                                                                                                            <div class="mb-3">No Issue Selected</div>

                                                                                                                            <a href="{{ route('issues.create', ['company' => $company->id]) }}" 
                                                                                                                               class="btn btn-success">
                                                                                                                                <i class="fa fa-plus"></i> Create Issue
                                                                                                                            </a>
                                                                                                                        </div>
                                                                                                                    `);
                    $('#bomIssuesModal').modal('show');
                    return;
                }

                let listHtml = '';

                res.employees.forEach((row, index) => {

                    // CHECK STATUS
                    let hasPending = row.items.some(i => i.status === 'Pending');
                    let hasPartial = row.items.some(i => i.status === 'Partial');

                    let circleClass =
                        hasPending
                            ? 'bg-danger'
                            : hasPartial
                                ? 'bg-warning'
                                : 'bg-success';

                    listHtml += `
                                    <li class="list-group-item employee-item ${index === 0 ? 'active' : ''}"
                                        data-index="${index}"
                                        style="cursor:pointer;">

                                        <div class="d-flex justify-content-between align-items-center">

                                            <div class="d-flex align-items-center">

                                                <div class="status-circle ${circleClass} mr-2"></div>

                                                <div>
                                                    <div>
                                                        <b>${row.employee_name}</b>
                                                    </div>

                                                    <small class="text-muted">
                                                        ${row.department}
                                                    </small>
                                                </div>

                                            </div>

                                            <span class="badge badge-primary">
                                                ${row.total_assigned_qty}
                                            </span>

                                        </div>
                                    </li>
                                `;
                });

                $('#bomIssueList').html(listHtml);

                // 🔥 STORE DATA
                window.bomEmployeeData = res.employees;

                // 🔥 LOAD FIRST ITEM BY DEFAULT
                loadIssueDetails(0);

                $('#bomIssuesModal').modal('show');
            });
        });
        function loadIssueDetails(index) {
            let employee = window.bomEmployeeData[index];

            let html = `
                                                        <h5>${employee.employee_name}</h5>

                                                        <table class="table table-bordered table-sm mt-3">
                                                            <thead>
                                                                <tr>
                                                                    <th>Item</th>
                                                                    <th>Brand</th>
                                                                    <th>Condition</th>
                                                                    <th>Requested</th>
                                                                    <th>Issued</th>
                                                                    <th>Pending</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                    `;

            employee.items.forEach(item => {

                let badge =
                    item.status == 'Issued'
                        ? 'success'
                        : item.status == 'Partial'
                            ? 'warning'
                            : 'danger';

                html += `
                                                            <tr>
                                                                <td>${item.item_name}</td>
                                                                <td>${item.brand_name}</td>
                                                                <td>${item.condition_name}</td>
                                                                <td>${item.requested_qty}</td>
                                                                <td>${item.issued_qty}</td>
                                                                <td>${item.pending_qty}</td>
                                                                <td>
                                                                    <span class="badge badge-${badge}">
                                                                        ${item.status}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        `;
            });

            html += `
                                                            </tbody>
                                                        </table>
                                                    `;

            $('#bomIssueDetails').html(html);
        }
        $(document).on('click', '.employee-item', function () {

            $('.employee-item').removeClass('active');

            $(this).addClass('active');

            let index = $(this).data('index');

            loadIssueDetails(index);
        });
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
    </script>
@endpush