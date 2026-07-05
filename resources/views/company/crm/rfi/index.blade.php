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

            <div class="card card-teal">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">{{$label}}</h3>
                    <div class="d-flex align-items-center ml-auto" style="gap: 8px;">

                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-success">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div id="loader" class="table-loader">
                        <div class="spinner"></div>
                        <p>Loading RFI data...</p>
                    </div>
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Sn No.</th>
                                <th>RFI Code</th>
                                <th>Date</th>
                                <th>Req. Items</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="rfiRows"></tbody>
                    </table>
                </div>

            </div>

        </div>
    </section>
    <div class="modal fade" id="rfiModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <!-- ✅ HEADER (ONLY TITLE) -->
                <div class="modal-header"
                    style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
                    <h5 class="modal-title text-white">RFI Details</h5>
                    <button class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="rfi_id" name="rfi_id">
                    <!-- ✅ SECTION 1: RFI DETAILS -->
                    <div class="row mb-3">

                        <!-- LEFT: RFI CODE -->
                        <div class="col-md-4">
                            <label class="text-muted">RFI Code</label>
                            <input type="text" id="modalRfiCode" class="form-control" readonly>
                        </div>

                        <!-- RIGHT: DATE + TIME -->
                        <div class="col-md-4">
                            <label class="text-muted">Date & Time</label>
                            <input type="text" id="modalRfiDate" class="form-control" readonly>
                        </div>

                        <!-- SUPPLIER -->
                        <div class="col-md-4">
                            <label class="text-muted">Supplier</label>

                            <div class="input-group">
                                <select id="supplier_id" class="form-control select2" required>
                                    <option value="">Select Supplier</option>

                                    @foreach($suppliers as $sup)
                                        <option value="{{ $sup->id }}">
                                            {{ $sup->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- ✅ ADD BUTTON (SAME AS STOCK-IN) -->
                                <!-- <div class="input-group-append">
                                                                                                                                                                                                                                                                                                    <button type="button" class="btn btn-outline-success" data-toggle="modal"
                                                                                                                                                                                                                                                                                                        data-target="#supplierModal">
                                                                                                                                                                                                                                                                                                        <i class="fa fa-plus"></i>
                                                                                                                                                                                                                                                                                                    </button>
                                                                                                                                                                                                                                                                                                </div> -->
                            </div>
                        </div>

                    </div>

                    <hr>
                    <div class="row mb-3">

                        <!-- PO CODE -->
                        <div class="col-md-4">
                            <label class="text-muted">PO Code</label>
                            <input type="text" id="modalPoCode" class="form-control" readonly>
                        </div>

                        <!-- PO DATE + TIME -->
                        <div class="col-md-4">
                            <label class="text-muted">PO Date & Time</label>
                            <input type="text" id="modalPoDate" class="form-control" readonly>
                        </div>

                        <!-- CREATED BY -->
                        <div class="col-md-4">
                            <label class="text-muted">Created By</label>
                            <input type="text" id="modalCreatedBy" class="form-control" readonly>
                        </div>

                        <!-- SUPPLIER -->


                    </div>
                    <hr>

                    <!-- ✅ SECTION 2: ITEMS TABLE -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th width="20%">Item</th>
                                <!-- <th width="20%">Specs</th> -->
                                <th width="10%">Brand</th>
                                <th width="10%">Condition</th>
                                <th width="10%">Unit</th>
                                <th width="15%">Qty</th>
                                <th width="15%">Rate</th>
                                <th width="15%">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="rfiItemsRows"></tbody>
                    </table>
                    <hr>
                    <div class="row mt-3">

                        <div class="col-md-2">
                            <label>Subtotal</label>
                            <input type="text" id="subtotal" name="subtotal" class="form-control" readonly>
                        </div>

                        <div class="col-md-2">
                            <label>Discount</label>
                            <input type="number" id="discount" name="discount" class="form-control" value="0"oninput="if(this.value < 0) this.value = 1;">
                        </div>

                        <div class="col-md-2">
                            <label>Tax (%)</label>
                            <input type="number" id="tax" name="tax" class="form-control" value="0"oninput="if(this.value < 0) this.value = 1;">
                        </div>

                        <div class="col-md-3">
                            <label>Tax Amount</label>
                            <input type="text" id="tax_amount" name="tax_amount" class="form-control" readonly>
                        </div>

                        <div class="col-md-3">
                            <label>Final Total</label>
                            <input type="text" id="final_total" name="final_amount" class="form-control" readonly>
                        </div>

                    </div>
                    <hr>
                    <div class="row mt-1">

                        <!-- NOTES (SUMMERNOTE) -->
                        <div class="col-md-12">
                            <label class="text-muted">Notes</label>
                            <textarea id="modalNotes" class="summernote"></textarea>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-1">

                        <!-- REMARK (READ ONLY) -->
                        <div class="col-md-12">
                            <label class="text-muted">Remark</label>

                            <div id="modalRemarkBox" class="remark-box">
                                <i class="fa fa-sticky-note text-success mr-1"></i>
                                <span id="modalRemark"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ✅ FOOTER -->
                <div class="modal-footer">
                    <button class="btn btn-success approveRfi">
                        <i class="fa fa-check"></i> Approve & Create PO
                    </button>

                    <button class="btn btn-danger rejectRfi">
                        <i class="fa fa-times"></i> Reject
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="editRfiModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div id="rfiLoader" class="rfi-loader">
                    <div class="spinner"></div>
                    <p>Loading items...</p>
                </div>
                <div class="modal-header " style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
                    <h5 class="modal-title text-white">
                        <i class="fa fa-exclamation-triangle"></i> Generate RFI
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <form id="rfiForm" method="POST" action="{{ route('rfis.store', $company) }}">
                    @csrf

                    <div class="modal-body">

                        {{-- TOP --}}
                        <div class="row mb-3">

                            {{-- RFI CODE --}}
                            <div class="col-md-4">
                                <label>RFI Code</label>
                                <input type="text" class="form-control" id="rfi_preview_code" value="" readonly>
                            </div>

                            {{-- DATE --}}
                            <div class="col-md-4">
                                <label>Date & Time</label>

                                <div class="input-group">
                                  <input type="text"id="rfi_modal_date"name="rfi_date" class="form-control" readonly>

                                    <div class="input-group-append">
                                        <span class="input-group-text" id="rfiCalendarIcon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>Created By</label>
                                <input type="text" class="form-control" value="" id="rfi_created_by" readonly>
                            </div>
                        </div>
                        {{-- TABLE --}}
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center"><input type="checkbox" id="modalSelectAll"></th>
                                        <th>Item</th>
                                        <th>Brand</th>
                                        <th>Condition</th>
                                        <th>Location</th>
                                        <th>Unit</th>
                                        <th style="width: 10%;" class="text-center">Rate</th>
                                        <th class="text-center">Current</th>
                                        <th class="text-center">Min</th>
                                        <th style="width: 10%;" class="text-center">Req Qty</th>
                                        <th class="text-right">
                                            <!-- <button type="button" class="btn btn-success btn-sm mt-1" id="addManualRow">
                                                Add Items
                                            </button> -->
                                            <button type="button" class="btn btn-warning btn-sm mt-1" id="loadLowStock">
                                                Load Stock
                                            </button>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody id="rfiModalBody">
                                    {{-- AJAX LOAD --}}
                                </tbody>
                            </table>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label>Remark</label>
                                <textarea id="rfi_modal_remark" name="remark" class="summernote"></textarea>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-save"></i> Create RFI
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
    </div>
    <div class="modal fade" id="rfiViewModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header text-white d-flex justify-content-between align-items-center"
                    style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
                    <h5 class="modal-title">
                        RFI Summary - <span id="rfiCode"></span>
                    </h5>

                    <button class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="px-3 py-2 bg-light border-bottom">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>RFI Code:</strong> <span id="rfiCode2"></span>
                        </div>
                        <div class="col-md-6 text-right">
                            <strong>Date:</strong> <span id="rfiDate"></span>
                        </div>

                        <div class="col-md-6">
                            <strong>Created By:</strong> <span id="rfiCreator"></span>
                        </div>
                        <div class="col-md-6 text-right">
                            <strong>Approved / Rejected By:</strong> <span id="rfiApprover"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-body">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Brand</th>
                                <th>Condition</th>
                                <th>Unit</th>
                                <th class="text-center">Requested</th>
                                <th class="text-center">Approved</th>
                                <th class="text-center">Rejected</th> <!-- ✅ NEW -->
                                <th class="text-center">Rate</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>

                        <tbody id="rfiViewRows"></tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
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
    <style>
        .gap-2 {
            gap: 10px;
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
        .table-loader {
            display: none;
            text-align: center;
            padding: 40px;
        }

        .remark-box {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 10px 12px;
            border-radius: 5px;
            min-height: 80px;
            font-size: 14px;
            color: #333;
            line-height: 1.6;
        }

        .table-loader .spinner {
            width: 40px;
            height: 40px;
            margin: 0 auto 10px;
            border: 4px solid #ccc;
            border-top: 4px solid #17a2b8;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
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
        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
        $(document).ready(function () {
            loadRfis();
        });

        function loadRfis() {

            $("#loader").show();
            $("#example1").hide();

            $.ajax({
                url: "{{ route('rfis.data', $company->id) }}",
                type: "GET",
                data: {
                    rfi_code: $('#rfi_code').val(),
                    from_date: $('#from_date').val(),
                    to_date: $('#to_date').val(),
                    status: $('#status').val(),
                },
                success: function (res) {

                    // Destroy old DataTable (same as leads)
                    if ($.fn.DataTable.isDataTable('#example1')) {
                        let dt = $('#example1').DataTable();
                        if ($.isFunction(dt.destroy)) {
                            dt.clear().draw(false);
                            dt.destroy();
                        }
                    }

                    // ✅ ONLY this (same as leads)
                    $('#rfiRows').html(res);

                    // ✅ Empty check (same logic)
                    if ($("#rfiRows").find("tr").length === 0 || $("#rfiRows").find("tr td").length === 1) {
                        $("#loader").hide();
                        $("#example1").show();
                        return;
                    }

                    // ✅ Init DataTable
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

                    // ✅ Hide loader AFTER everything is ready
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
        let currentRfiId = null;

        $(document).on('click', '.view-rfi', function () {

            // ✅ GET ID FROM BUTTON
            currentRfiId = $(this).data('id');

            console.log('CLICK ID:', currentRfiId); // debug

            $.get("{{ url('company/' . $company->id . '/rfis') }}/" + currentRfiId, function (res) {

                $('#modalRfiCode').val(res.rfi.rfi_code);
                $('#modalRfiDate').val(res.rfi.created_at);

                $('#modalNotes').summernote('code', res.rfi.notes ?? '');

                let remark = res.rfi.remark ?? 'No remark provided';
                remark = remark.replace(/\n/g, '<br>');
                $('#modalRemark').html(remark);


                // ================= 🔥 PO HEADER PREVIEW =================

                $('#modalPoCode').val(res.rfi.po_code);
                $('#modalPoDate').val(new Date().toLocaleString());
                $('#modalCreatedBy').val('{{ auth()->user()->name }}');


                // ================= BUILD ITEMS =================

                let rows = '';

                res.items.forEach((item, index) => {

                    let qty = item.requested_quantity || 0;
                    let rate = item.rate || 0;
                    let amount = qty * rate;
                    let specsHtml = `<select name="items[${index}][specification_id]"class="form-control">`;
                    specsHtml += `<option value="">Select Spec</option>`;

                    res.specifications.forEach(spec => {
                        specsHtml += `<option value="${spec.id}">${spec.name}</option>`;
                    });

                    specsHtml += `</select>`;
                    rows += `
                                                                                                                                        <tr>
                                                                                                                                            <td>
                                                                                                                                                <input type="checkbox" class="selectItem" value="${item.id}">
                                                                                                                                            </td>

                                                                                                                                            <td>${item.item?.name ?? '-'}</td>

                                                                                                                                            <td>${item.brand?.name ?? '-'}</td>
                                                                                                                                            <td>${item.condition?.name ?? '-'}</td>
                                                                                                                                            <td>${item.unit?.name ?? '-'}</td>

                                                                                                                                            <td>
                                                                                                                                                <input type="number" 
                                                                                                                                                       name="items[${item.id}][quantity]"
                                                                                                                                                       class="form-control qty" 
                                                                                                                                                       value="${qty}"oninput="if(this.value < 0) this.value = 1;">
                                                                                                                                            </td>

                                                                                                                                            <td>
                                                                                                                                                <input type="number" 
                                                                                                                                                       name="items[${item.id}][rate]"
                                                                                                                                                       class="form-control rate" 
                                                                                                                                                       value="${rate}"oninput="if(this.value < 0) this.value = 1;">
                                                                                                                                            </td>

                                                                                                                                            <td>
                                                                                                                                                <input type="text" 
                                                                                                                                                       class="form-control amount" 
                                                                                                                                                       value="${amount.toFixed(2)}" readonly>
                                                                                                                                            </td>
                                                                                                                                        </tr>
                                                                                                                                    `;
                });

                $('#rfiItemsRows').html(rows);
                setTimeout(() => {
                    calculateTotals();
                }, 100);

                // ================= 🔥 INITIAL TOTAL CALC =================

                calculateTotals();


                // ================= RESET TOTAL INPUTS =================

                $('#discount').val(0);
                $('#tax').val(0);


                // ================= OPEN MODAL =================

                $('#rfiModal').modal('show');
            });
        });
        // ================= INIT MODAL =================
        function openPOModal() {

            // reset table
            $('#poItemsTable tbody').html('');

            addRow(0);

            // 🔥 PO CODE (preview)

            // 🔥 DATE TIME
            let now = new Date();
            $('#modalPoDate').val(now.toLocaleString());

            // 🔥 CREATED BY
            $('#modalCreatedBy').val('{{ auth()->user()->name }}');

            // reset totals
            $('#discount').val(0);
            $('#tax').val(0);
            $('#subtotal').val('0.00');
            $('#tax_amount').val('0.00');
            $('#final_total').val('0.00');

            $('#poModal').modal('show');
        }


        // ================= ADD ROW =================
        function addRow(index) {

            let row = `
                                                                                                                                    <tr>
                                                                                                                                        <td>
                                                                                                                                            <select name="items[${index}][item_id]" class="form-control select2">
                                                                                                                                                ${ITEM_OPTIONS}
                                                                                                                                            </select>
                                                                                                                                        </td>

                                                                                                                                        <td>
                                                                                                                                            <input type="number" name="items[${index}][quantity]" 
                                                                                                                                                   class="form-control qty" min="0" value="1">
                                                                                                                                        </td>

                                                                                                                                        <td>
                                                                                                                                            <input type="number" name="items[${index}][rate]" 
                                                                                                                                                   class="form-control rate" min="0" value="0">
                                                                                                                                        </td>

                                                                                                                                        <td>
                                                                                                                                            <input type="text" name="items[${index}][amount]" 
                                                                                                                                                   class="form-control amount" readonly>
                                                                                                                                        </td>
                                                                                                                                    </tr>
                                                                                                                                `;

            $('#poItemsTable tbody').append(row);
        }


        // ================= CALCULATE ROW =================
        function calculateRow(row) {

            let qty = parseFloat(row.find('.qty').val()) || 0;
            let rate = parseFloat(row.find('.rate').val()) || 0;

            let amount = qty * rate;

            row.find('.amount').val(amount.toFixed(2));
        }


        // ================= CALCULATE TOTAL =================
        function calculateTotals() {

            let subtotal = 0;

            $('#rfiItemsRows tr').each(function () {

                let amount = parseFloat($(this).find('.amount').val());

                if (!isNaN(amount)) {
                    subtotal += amount;
                }
            });

            let discount = parseFloat($('#discount').val()) || 0;
            let tax = parseFloat($('#tax').val()) || 0;

            let afterDiscount = subtotal - discount;

            let taxAmount = (afterDiscount * tax) / 100;

            let finalTotal = afterDiscount + taxAmount;

            $('#subtotal').val(subtotal.toFixed(2));
            $('#tax_amount').val(taxAmount.toFixed(2));
            $('#final_total').val(finalTotal.toFixed(2));
        }

        // ================= EVENTS =================

        // qty / rate change
        $(document).on('input', '.qty, .rate', function () {

            let row = $(this).closest('tr');

            calculateRow(row);
            calculateTotals();
        });

        // discount / tax change
        $(document).on('input', '#discount, #tax', function () {
            calculateTotals();
        });


        // ================= AUTO RECALC ON LOAD =================
        $(document).on('change', '.qty, .rate', function () {
            calculateTotals();
        });
        $('#rfiModal').on('shown.bs.modal', function () {

            $('#supplier_id').select2({
                width: '100%',
                dropdownParent: $('#rfiModal') // ✅ MUST for modal
            });

        });
        function addOptionAndSelect(selector, id, text) {
            let option = new Option(text, id, true, true);
            $(selector).append(option).trigger('change.select2');
        }

        // SELECT ALL
        $(document).on('change', '#selectAll', function () {
            $('.selectItem').prop('checked', $(this).prop('checked'));
        });
        $(document).on('click', '.approveRfi', function () {

            let supplierId = $('#supplier_id').val();

            // 🔥 CHECK SUPPLIER FIRST
            if (!supplierId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Supplier Required',
                    text: 'Please select a supplier before approving.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            let items = [];

            $('#rfiItemsRows tr').each(function () {

                let checkbox = $(this).find('.selectItem');

                if (checkbox.is(':checked')) {

                    items.push({
                        id: checkbox.val(),
                        qty: $(this).find('.qty').val(),
                        rate: $(this).find('.rate').val()
                    });
                }
            });

            // 🔥 CHECK ITEMS
            if (items.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Items Selected',
                    text: 'Please select at least one item.',
                    confirmButtonText: 'OK'
                });
                return;
            }
            $('#supplier_id').focus();
            // ✅ AJAX CALL
            $.ajax({
                url: "{{ route('rfis.approve', $company->id) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",

                    rfi_id: currentRfiId,
                    supplier_id: supplierId,
                    notes: $('#modalNotes').summernote('code'),
                    // 🔥 ADD THESE (IMPORTANT)
                    discount: $('#discount').val(),
                    tax: $('#tax').val(),
                    tax_amount: $('#tax_amount').val(),
                    final_amount: $('#final_total').val(),

                    items: items
                },
                success: function (res) {
                    if (res.status) {
                        window.location.href = res.redirect;
                    }
                }
            });
        });
        $(document).on('click', '.rejectRfi', function () {

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to reject this RFI!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Reject it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('rfis.reject', $company->id) }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            rfi_id: currentRfiId,
                            notes: $('#modalNotes').summernote('code'),
                        },
                        success: function () {

                            Swal.fire({
                                icon: 'success',
                                title: 'Rejected!',
                                text: 'RFI has been rejected successfully.',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $('#rfiModal').modal('hide');
                            loadRfis();
                        }
                    });

                }

            });
        });
    </script>
    <script>
        $(document).on('click', '.remove-row', function () {

            let row = $(this).closest('tr');

            Swal.fire({
                title: 'Are you sure?',
                text: "This item will be removed!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    row.remove();

                    Swal.fire({
                        icon: 'success',
                        title: 'Removed!',
                        timer: 1200,
                        showConfirmButton: false
                    });
                }
            });

        });
        $(document).on('click', '.edit-rfi', function () {

            let rfiId = $(this).data('id');

            // ✅ OPEN CORRECT MODAL
            $('#rfiLoader').show();
            $('#editRfiModal').modal('show');

            $.get("{{ url('company/' . $company->id . '/rfis') }}/" + rfiId + "/edit", function (res) {

                $('#rfiLoader').hide();

                // ✅ FIXED ACTION URL
                $('#rfiForm').attr(
                    'action',
                    "{{ url('company/' . $company->id . '/rfis') }}/" + rfiId
                );

                // ✅ FIX DUPLICATE _method
                $('#rfiForm input[name="_method"]').remove();
                $('#rfiForm').append('<input type="hidden" name="_method" value="PUT">');
                $('#rfi_created_by').val(res.rfi.creator?.name ?? 'N/A');
                // ✅ SET HIDDEN ID
                $('#rfi_id').val(rfiId);

                // ✅ HEADER CHANGE
                $('#editRfiModal .modal-title')
                    .html('<i class="fa fa-edit"></i> Edit RFI');

                // ✅ BUTTON TEXT CHANGE
                $('#rfiForm button[type="submit"]')
                    .html('<i class="fa fa-save"></i> Update RFI');

                // ✅ SET BASIC DATA
                $('#rfi_preview_code').val(res.rfi.rfi_code);

                // ✅ SUMMERNOTE (safe)
                if ($('#rfi_modal_remark').next('.note-editor').length === 0) {
                    $('#rfi_modal_remark').summernote({ height: 120 });
                }
                $('#rfi_modal_remark').summernote('code', res.rfi.remark ?? '');

                // ✅ BUILD ITEMS (IMPORTANT FIX)
                let rows = '';

                res.items.forEach((item, i) => {
                    console.log(res.items);
                    rows += `
                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                    <td class="text-center">
                                                                                                                                                                                                                        <input type="checkbox" name="items[${i}][selected]" checked>
                                                                                                                                                                                                                    </td>

                                                                                                                                                                                                                    <td>
                                                                                                                                                                                                                        ${item.item?.name ?? ''}

                                                                                                                                                                                                                        <input type="hidden" name="items[${i}][item_id]" value="${item.item_id}">
                                                                                                                                                                                                                        <input type="hidden" name="items[${i}][brand_id]" value="${item.brand_id}">
                                                                                                                                                                                                                        <input type="hidden" name="items[${i}][condition_id]" value="${item.condition_id}">
                                                                                                                                                                                                                        <input type="hidden" name="items[${i}][unit_id]" value="${item.unit_id}">
                                                                                                                                                                                                                        <input type="hidden" name="items[${i}][location_id]" value="${item.location_id}">
                                                                                                                                                                                                                        <input type="hidden" name="items[${i}][current_quantity]" value="${item.current_quantity}">
                                                                                                                                                                                                                        <input type="hidden" name="items[${i}][min_quantity]" value="${item.min_quantity}">
                                                                                                                                                                                                                    </td>

                                                                                                                                                                                                                    <td>${item.brand?.name ?? ''}</td>
                                                                                                                                                                                                                      <td>${item.condition?.name ?? '-'}</td>



                                                                                                                                                                                                                    <td>${item.location?.name ?? ''}</td>
                                                                                                                                                                                                                    <td>${item.unit?.name ?? ''}</td>
                                                                                                                                                                                                                   <td>
                                                                                                                                                                                                                        <input type="number" name="items[${i}][rate]" 
                                                                                                                                                                                                                               class="form-control rate"
                                                                                                                                                                                                                               value="${item.rate}"oninput="if(this.value < 0) this.value = 1;">
                                                                                                                                                                                                                    </td>

                                                                                                                                                                                                                    <td class="text-center">${item.current_quantity}</td>
                                                                                                                                                                                                                    <td class="text-center">${item.min_quantity}</td>

                                                                                                                                                                                                                    <td>
                                                                                                                                                                                                                        <input type="number" name="items[${i}][requested_quantity]" 
                                                                                                                                                                                                                               class="form-control qty"
                                                                                                                                                                                                                               value="${item.requested_quantity}"oninput="if(this.value < 0) this.value = 1;">
                                                                                                                                                                                                                    </td>

                                                                                                                                                                                                                    <td class="text-right">
                                                                                                                                                                                                                        <button type="button" class="btn btn-danger btn-sm removeRow remove-row">
                                                                                                                                                                                                                            <i class="fa fa-trash"></i>
                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                    </td>
                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                `;
                });

                $('#rfiModalBody').html(rows);

            });

        });
       $('#editRfiModal').on('shown.bs.modal', function () {

    let now = new Date();

    let formatted =
        String(now.getDate()).padStart(2, '0') + '/' +
        String(now.getMonth() + 1).padStart(2, '0') + '/' +
        now.getFullYear() + ' ' +
        String(now.getHours()).padStart(2, '0') + ':' +
        String(now.getMinutes()).padStart(2, '0');

    $('#rfi_modal_date').val(formatted);

});
        let manualIndex = 5000;

        $(document).on('click', '#addManualRow', function () {

            let row = `
                                                                                                                                                                                                    <tr>

                                                                                                                                                                                                        <td>
                                                                                                                                                                                                            <input type="checkbox" name="items[${manualIndex}][selected]" checked>
                                                                                                                                                                                                        </td>

                                                                                                                                                                                                        <td>
                                                                                                                                                                                                            <select name="items[${manualIndex}][item_id]" class="form-control select2">
                                                                                                                                                                                                                <option value="">Select Item</option>
                                                                                                                                                                                                                @foreach($items as $id => $name)
                                                                                                                                                                                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                                                                                                                                                                                @endforeach
                                                                                                                                                                                                            </select>
                                                                                                                                                                                                        </td>

                                                                                                                                                                                                        <td>
                                                                                                                                                                                                            <select name="items[${manualIndex}][brand_id]" class="form-control select2">
                                                                                                                                                                                                                @foreach($brands as $id => $name)
                                                                                                                                                                                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                                                                                                                                                                                @endforeach
                                                                                                                                                                                                            </select>
                                                                                                                                                                                                        </td>

                                                                                                                                                                                                        <td>
                                                                                                                                                                                                            <select name="items[${manualIndex}][condition_id]" class="form-control select2">
                                                                                                                                                                                                                @foreach($conditions as $condition)
                                                                                                                                                                                                                    <option value="{{ $condition->id }}">{{ $condition->name }}</option>
                                                                                                                                                                                                                @endforeach
                                                                                                                                                                                                            </select>
                                                                                                                                                                                                        </td>

                                                                                                                                                                                                        <td>
                                                                                                                                                                                                            <select name="items[${manualIndex}][location_id]" class="form-control select2">
                                                                                                                                                                                                                @foreach($locations as $id => $name)
                                                                                                                                                                                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                                                                                                                                                                                @endforeach
                                                                                                                                                                                                            </select>
                                                                                                                                                                                                        </td>

                                                                                                                                                                                                        <td>
                                                                                                                                                                                                            <input type="number" class="form-control" name="items[${manualIndex}][rate]" value="0">
                                                                                                                                                                                                        </td>

                                                                                                                                                                                                        <td><input type="number" class="form-control" name="items[${manualIndex}][current_quantity]" value="0"></td>
                                                                                                                                                                                                        <td><input type="number" class="form-control" name="items[${manualIndex}][min_quantity]" value="0"></td>

                                                                                                                                                                                                        <td>
                                                                                                                                                                                                            <input type="number" class="form-control" name="items[${manualIndex}][requested_quantity]" value="1">
                                                                                                                                                                                                        </td>

                                                                                                                                                                                                        <td class="text-right">
                                                                                                                                                                                                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                                                                                                                                                                                                <i class="fa fa-trash"></i>
                                                                                                                                                                                                            </button>
                                                                                                                                                                                                        </td>

                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                    `;

            let newRow = $(row);
            $('#rfiModalBody').append(newRow);

            newRow.find('.select2').select2({
                dropdownParent: $('#editRfiModal'),
                width: '100%'
            });

            manualIndex++;
        });
        $(document).on('click', '#loadLowStock', function () {

            $('#rfiLoader').show();

            $.get("{{ route('rfis.lowStock', $company) }}", function (res) {

                $('#rfiLoader').hide();

                let newRows = $(res);

                // ✅ GET EXISTING ITEM IDs
                let existingIds = [];

                $('input[name*="[item_id]"]').each(function () {
                    existingIds.push($(this).val());
                });

                // ✅ FILTER DUPLICATES
                newRows.each(function () {

                    let itemId = $(this).find('input[name*="[item_id]"]').val();

                    if (!existingIds.includes(itemId)) {
                        $('#rfiModalBody').append($(this)); // ✅ only unique
                    }

                });

            });

        });
    </script>
    <script>
        $(document).on('click', '.view-summary', function () {

            let rfiId = $(this).data('id');

            $.get("{{ url('company/' . $company->id . '/rfis') }}/" + rfiId, function (res) {

                let rfi = res.rfi;

                // ✅ Header + Top Info

                $('#rfiCode').text(rfi.rfi_code ?? '-');
                $('#rfiCode2').text(rfi.rfi_code ?? '-');

                // ✅ DATE (NO new Date)
                $('#rfiDate').text(rfi.created_at ?? '-');

                // ✅ CREATED BY
                $('#rfiCreator').text(rfi.created_by ?? '-');

                // ✅ APPROVED / REJECTED BY
                $('#rfiApprover').text(rfi.approved_by ?? 'Pending');

                // ✅ Status Badge (Top Right)
                let statusBadge = '';
                if (rfi.status == 'rejected') {
                    statusBadge = '<span class="badge badge-danger">Rejected</span>';
                } else if (rfi.status == 'approved') {
                    statusBadge = '<span class="badge badge-success">Approved</span>';
                } else {
                    statusBadge = '<span class="badge badge-warning">Pending</span>';
                }

                let rows = '';

                res.items.forEach((item, index) => {

                    let requested = item.requested_quantity ?? 0;
                    let approved = item.approved_quantity ?? 0;
                    let rejected = requested - approved;
                    let rate = item.rate ?? 0;

                    let statusBadge = '';
                    let rowClass = '';

                    // ✅ ALL CONDITIONS MUST BE HERE
                    if (approved == 0) {
                        statusBadge = '<span class="badge badge-danger">Rejected</span>';
                        rowClass = 'table-danger';
                    }
                    else if (approved == requested) {
                        statusBadge = '<span class="badge badge-success">Approved</span>';
                        rowClass = 'table-success';
                    }
                    else {
                        statusBadge = '<span class="badge badge-warning">Partial</span>';
                        rowClass = 'table-warning'; // no row color
                    }

                    rows += `
                                                                                                                                                                        <tr class="${rowClass}">
                                                                                                                                                                         <td class="text-center">${index + 1}</td> <!-- ✅ SR NO -->
                                                                                                                                                                            <td>${item.item?.name ?? '-'}</td>
                                                                                                                                                                            <td>${item.brand?.name ?? '-'}</td>
                                                                                                                                                                            <td>${item.condition?.name ?? '-'}</td>
                                                                                                                                                                            <td>${item.unit?.name ?? '-'}</td>

                                                                                                                                                                            <td class="text-center">${requested}</td>

                                                                                                                                                                            <td class="text-center text-success">
                                                                                                                                                                                ${approved}
                                                                                                                                                                            </td>

                                                                                                                                                                            <td class="text-center text-danger">
                                                                                                                                                                                ${rejected}
                                                                                                                                                                            </td>

                                                                                                                                                                            <td class="text-center">${rate}</td>

                                                                                                                                                                            <td class="text-center">${statusBadge}</td>
                                                                                                                                                                        </tr>
                                                                                                                                                                    `;
                });

                $('#rfiViewRows').html(rows);
                $('#rfiViewModal').modal('show');
            });
        });
    </script>
@endpush