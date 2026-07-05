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
                                @can('stockin')
                                    <div class="dropdown">
                                        <button class="btn btn-default btn-sm float-right dropdown-toggle" type="button"
                                            data-toggle="dropdown">
                                            <i class="fa fa-plus"></i> Add Stock
                                        </button>

                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" style="color:#000;"
                                                href="{{ route('stock-ins.create', ['company' => $company->id]) }}">
                                                ➤ Without PO
                                            </a>

                                            <a class="dropdown-item" style="color:#000;"
                                                href="{{ route('stock-ins.create.po', ['company' => $company->id]) }}">
                                                ➤ With PO
                                            </a>
                                        </div>
                                    </div>
                                @endcan
                                <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
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
                                    <label>Search Stock</label>
                                    <select id="stock_search" class="form-control" style="width:100%"></select>
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
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>S.N.</th>
                                            <th>Doc Number</th>
                                            <th>Entry Date</th>
                                            <th>Suplier Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="stockinrows">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="viewStockModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">

                <div class="modal-header text-white" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
                    <h5 class="modal-title">
                        <i class="fa fa-eye"></i> Stock In Details
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        &times;
                    </button>
                </div>

                <div class="modal-body">

                    {{-- 🔹 ROW 1 --}}
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <b>GRN No:</b> <span id="v_doc_no">-</span>
                        </div>

                        <div class="col-md-6 text-right">
                            <b>Date:</b> <span id="v_doc_date">-</span>
                        </div>
                    </div>

                    {{-- 🔹 ROW 2 --}}
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <b>PO No:</b> <span id="v_po_no">-</span>
                        </div>

                        <div class="col-md-6 text-right">
                            <b>Supplier:</b> <span id="v_supplier">-</span>
                        </div>
                    </div>
                    <div id="po_extra_section" style="display:none;">
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <b>Supplier Doc No:</b>
                                <span id="v_supplier_no">-</span>
                            </div>

                            <div class="col-md-6 text-right">
                                <b>Supplier Doc Date:</b>
                                <span id="v_supplier_date">-</span>
                            </div>


                        </div>
                    </div>
                    <hr>

                    {{-- 🔹 ITEMS TABLE --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Brand</th>
                                    <th>Condition</th>
                                    <th>Location</th>
                                    <th>GRN Unit</th>
                                    <th>GRN Qty</th>
                                    <th>Stock Unit</th>
                                    <th>Stock Qty</th>
                                    <th>Rate</th>
                                    <th class="supplier-rate-col" style="display:none;">Supplier Rate</th>
                                </tr>
                            </thead>
                            <tbody id="viewStockItems">
                                <tr>
                                    <td colspan="8" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- 🔹 REMARK FULL WIDTH --}}
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <b>Remark:</b>
                            <div id="v_remark" class="border p-2 rounded bg-light">-</div>
                        </div>
                    </div>
                    <div id="supplier_doc_section" style="display:none;">
                        <div class="col-md-12 mt-3">
                            <b>Document:</b>
                            <span id="v_supplier_doc">-</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
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
    <script src="{{url('/')}}/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
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
    </script>
    <script>
        $(document).ready(function () {

            initDatePicker('#from_date');
            initDatePicker('#to_date');
            loadStock();

            $('#filter').click(loadStock);

            $('#reset').click(function () {
                $('#from_date').val(null);
                $('#to_date').val(null);
                $('#stock_search').val(null).trigger('change');
                loadStock();
            });
        });

        function loadStock() {

            let params = {};

            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();
            let search = $('#stock_search').val() || '';

            if (from_date) params.from_date = from_date;
            if (to_date) params.to_date = to_date;
            if (search) params.search = search;

            $("#loader").show();
            $("#example1").hide();

            $.ajax({
                url: "{{ route('stock-ins.data', $company->id) }}",
                data: params,

                success: function (res) {

                    // Always stop loader FIRST
                    $("#loader").hide();
                    $("#example1").show();

                    // Destroy DataTable if exists
                    if ($.fn.DataTable.isDataTable('#example1')) {
                        $('#example1').DataTable().clear().destroy();
                    }

                    // Inject rows
                    $('#stockinrows').html(res.trim());

                    // 🔴 CHECK: no rows OR only empty text
                    const hasRows =
                        $('#stockinrows tr').length > 0 &&
                        !$('#stockinrows .no-record-row').length;

                    if (!hasRows) {
                        $('#stockinrows').html(noRecordRow());
                        return; // ❌ DO NOT INIT DATATABLE
                    }

                    // ✅ Init DataTable ONLY if rows exist
                    $('#example1').DataTable({
                        responsive: true,
                        autoWidth: false,

                        paging: true,
                        pageLength: 10,
                        lengthChange: true,
                        lengthMenu: [10, 25, 50, 100, -1],

                        searching: true,
                        info: true,
                        ordering: true,

                        dom: '<"d-flex justify-content-between align-items-center"Bf>rt<"d-flex justify-content-between mt-2"ip>',

                        buttons: [
                            {
                                extend: 'colvis',
                                text: 'Column visibility'
                            }
                        ]
                    });
                }
                ,


            });
        }
        $('#stock_search').select2({
            placeholder: "Search Doc No / Supplier",
            minimumInputLength: 1,
            ajax: {
                url: "{{ route('stock-ins.search', $company->id) }}",
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return { search: params.term };
                },
                processResults: function (data) {
                    return { results: data };
                }
            }
        });
    </script>
    <script>
        $(document).on('click', '.delete-confirm', function (e) {
            e.preventDefault();

            const $btn = $(this);
            const $form = $btn.closest('.delete-form');
            const deleteUrl = $form.data('url');
            const itemName = $btn.data('name') || 'this item';
            const $row = $btn.closest('tr');

            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete ${itemName}. This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {

                if (!result.isConfirmed) return;

                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    beforeSend: function () {
                        $btn.prop('disabled', true);
                    },
                    success: function () {

                        $row.fadeOut(300, function () {
                            $(this).remove();

                            // 🔴 CHECK AFTER DELETE
                            if ($('#stockinrows tr').length === 0) {
                                $('#stockinrows').html(noRecordRow());
                            }
                        });

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Record deleted successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function () {
                        $btn.prop('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong. Please try again.'
                        });
                    }
                });
            });
        });
        function noRecordRow() {
            return `
                                                                                            <tr class="no-record-row">
                                                                                                <td colspan="6" class="text-center">😢 No records found</td>
                                                                                            </tr>
                                                                                        `;
        }

        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
    </script>
    <script>
        $(document).on('click', '.view-stock', function () {

            const stockId = $(this).data('id');

            $('#viewStockItems').html(
                '<tr><td colspan="9" class="text-center">Loading...</td></tr>'
            );

            $.get(
                "{{ url('company/' . $company->id . '/stock-ins') }}/" + stockId + "/view",
                function (res) {

                    $('#v_doc_no').text(res.doc_no);
                    $('#v_doc_date').text(res.doc_date);
                    $('#v_po_no').text(res.po_code ?? 'Self');
                    $('#v_supplier').text(res.supplier?.name ?? 'Self');
                    $('#v_remark').text(res.remark ?? '-');
                    $('#v_supplier_no').text(res.sup_doc_num ?? '-');

                    let hasPO = !!res.purchase_order_id;

                    // 🔥 SHOW/HIDE EXTRA SECTION
                    if (hasPO) {

                        $('#po_extra_section').show();
                        $('#supplier_doc_section').show();
                        $('#v_supplier_date').text(res.supplier_date ?? '-');

                        if (res.supplier_document) {

                           let fileUrl = `/${res.supplier_document}`;

                           $('#v_supplier_doc').html(`
    <a href="${fileUrl}" target="_blank" 
       class="btn btn-sm btn-success d-inline-flex align-items-center gap-1">
        <i class="fa fa-eye mr-1"></i> 
        <span>View Document</span>
    </a>
`);

                        } else {
                            $('#v_supplier_doc').text('No Document Uploaded');
                        }
                        $('.supplier-rate-col').show();

                    } else {

                        $('#po_extra_section').hide();
                        $('.supplier-rate-col').hide();
                    }

                    let rows = '';

                    res.items.forEach((row, i) => {

                        rows += `
                                            <tr>
                                                <td>${i + 1}</td>
                                                <td>${row.item?.name ?? '-'}</td>
                                                <td>${row.brand?.name ?? '-'}</td>
                                                <td>${row.condition?.name ?? '-'}</td>
                                                <td>${row.location?.name ?? '-'}</td>
                                                <td>${row.unit?.name ?? '-'}</td>
                                                <td>${row.quantity}</td>
                                                <td>${row.stock_unit?.name ?? '-'}</td>
                                                <td>${row.stock_quantity}</td>
                                                <td>${row.rate ?? '-'}</td>
                                                ${hasPO ? `<td>${row.supplier_rate ?? '-'}</td>` : ''}
                                            </tr>
                                        `;
                    });

                    $('#viewStockItems').html(rows);
                    $('#viewStockModal').modal('show');
                }
            );
        });
    </script>

@endpush