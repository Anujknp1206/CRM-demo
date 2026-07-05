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
                                @can('add payment')
                                    <button class="btn btn-sm btn-default border-0" data-toggle="modal"
                                        data-target="#paymentModal" style="background:#ffffff; color:#000;"
                                        onclick="IS_EDIT_MODE = false">
                                        <i class="fa fa-plus"></i> Add Payment
                                    </button>
                                @endcan

                                <a href="{{ url()->previous() }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 align-items-end">

                                <div class="col-md-3">
                                    <label class="form-label text-muted">From Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="from_date" class="form-control shadow-sm"
                                            placeholder="DD/MM/YYYY" autocomplete="off">
                                    </div>
                                </div>

                                <!-- To Date -->
                                <div class="col-md-3">
                                    <label class="form-label text-muted">To Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="to_date" class="form-control shadow-sm"
                                            placeholder="DD/MM/YYYY" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label>Search</label>
                                    <input type="text" id="payment_search" class="form-control"
                                        placeholder="Payment No, Order No, Customer, Mobile">
                                </div>
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
                                            <th>#</th>
                                            <th>Payment No</th>
                                            <th>Order No</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Paid</th>
                                            <th>Remaining</th>
                                            <th>Mode</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody id="paymentRows"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- CREATE PAYMENT MODAL -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header text-white"
                    style="background:linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
                    <h5 class="modal-title">Add Payment</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <form id="paymentForm" method="POST" autocomplete="off"
                    action="{{ route('payments.store', ['company' => $company->id]) }}">
                    @csrf

                    <div class="modal-body">

                        {{-- ===================== --}}
                        {{-- 1. ORDER SELECTION --}}
                        {{-- ===================== --}}
                        <h5 class="text-success"><b>1. Order</b></h5>
                        <div class="row">
                            <div class="col-md-4">
                                <label>Select Order *</label>
                                <select id="order_id" name="order_id" class="form-control" required></select>
                            </div>

                            <div class="col-md-3">
                                <label>Payment Number</label>
                                <input type="text" name="payment_number" id="payment_number" class="form-control" readonly>
                            </div>

                            <div class="col-md-5">
                                <label>Payment Date & Time</label>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="text" name="payment_date" id="payment_date" class="form-control"
                                                placeholder="DD/MM/YYYY" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="text" name="payment_time" id="payment_time" class="form-control"
                                                placeholder="HH:MM">
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="fa fa-clock"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <hr>
                        {{-- ===================== --}}
                        {{-- 2. CUSTOMER SNAPSHOT --}}
                        {{-- ===================== --}}
                        <h5 class="text-success"><b>2. Customer Details</b></h5>

                        <div class="row">
                            <div class="col-md-4">
                                <label>Customer Name</label>
                                <input type="text" id="customer_name" class="form-control" readonly>
                            </div>

                            <div class="col-md-4">
                                <label>Email</label>
                                <input type="text" id="customer_email" class="form-control" readonly>
                            </div>

                            <div class="col-md-4">
                                <label>Mobile</label>
                                <input type="text" id="customer_mobile" class="form-control" readonly>
                            </div>
                        </div>

                        <hr>

                        {{-- ===================== --}}
                        {{-- 3. ORDER FINANCIALS --}}
                        {{-- ===================== --}}
                        <h5 class="text-success"><b>3. Order Summary</b></h5>

                        <div class="row">
                            <div class="col-md-4">
                                <label>Order Total (<span class="currency-symbol">₹</span>)</label>
                                <input type="text" id="order_total" class="form-control" readonly>
                            </div>

                            <div class="col-md-4">
                                <label>Already Paid (<span class="currency-symbol">₹</span>)</label>
                                <input type="text" id="paid_amount" class="form-control" readonly>
                            </div>

                            <div class="col-md-4">
                                <label>Remaining Due (<span class="currency-symbol">₹</span>)</label>
                                <input type="text" id="due_amount" class="form-control" readonly>
                            </div>
                        </div>


                        <hr>

                        {{-- ===================== --}}
                        {{-- 4. PAYMENT DETAILS --}}
                        {{-- ===================== --}}
                        <h5 class="text-success"><b>4. Payment Details</b></h5>

                        <div class="row">
                            <div class="col-md-4">
                                <label>Amount (<span class="currency-symbol">₹</span>) *</label>
                                <input type="text" name="amount" id="payment_amount" class="form-control"
                                    placeholder="Enter Amount" readonly required>

                                <small id="amount_error" class="text-danger d-none">
                                    Amount cannot exceed remaining due
                                </small>
                            </div>


                            <div class="col-md-4">
                                <label>Payment Mode *</label>
                                <select name="payment_mode" id="payment_mode" class="form-control" required>
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="online">Online</option>
                                </select>
                            </div>

                            <div class="col-md-4 d-none" id="txn_ref_wrapper">
                                <label>Transaction Reference</label>
                                <input type="text" id="transaction_reference" name="transaction_reference"
                                    class="form-control">
                            </div>
                        </div>

                        <hr>

                        <h5 class="text-success"><b>5. Post Date Details</b></h5>

                        <div class="row mt-3">
                            <div class="col-md-4 d-flex align-items-center">

                                <label class="switch mb-0 mr-2">
                                    <input type="checkbox" id="is_post_dated" name="is_post_dated">
                                    <span class="slider round"></span>
                                </label>

                                <label class="mb-0 mr-2">Post Dated Payment?</label>

                                <small id="post_date_status" class="text-danger">OFF</small>

                            </div>

                            <div class="col-md-4 d-none" id="post_date_wrapper">
                                <label>Post Date</label>
                                <input type="text" name="post_date" id="post_date" class="form-control"
                                    placeholder="DD/MM/YYYY">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label>Remark</label>
                                <textarea name="note" id="payment_note" class="form-control summernote"></textarea>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Save Payment
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
@push('styles')
    <style>
        .gap-2 {
            gap: 10px;
        }

        .btn-admin-white {
            background: #ffffff !important;
            color: #000 !important;
            border: 1px solid #dee2e6 !important;
        }

        .btn-admin-white:hover {
            background: #f8f9fa !important;
        }

        .card-body {
            padding: 10px 5px !important;
        }

        /* SWITCH STYLE */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            display: none;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #dc3545;
            /* RED (OFF) */
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        /* ON STATE */
        input:checked+.slider {
            background-color: #28a745;
            /* GREEN */
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }
    </style>
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/summernote/summernote-bs4.min.css">
    <!-- Select2 -->
    <!-- DataTables -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@endpush
@push('scripts')
    <script src="{{url('/')}}/admin/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/jszip/jszip.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let paymentDatePicker;
        let paymentTimePicker;
        let ORDER_TOTAL = 0;
        let PAID_AMOUNT = 0;
        let IS_EDIT_MODE = false;
        let toDatePicker;
        let fromDatePicker;
        let postDatePicker = flatpickr("#post_date", {
            dateFormat: "d/m/Y",
            minDate: "today", // only future dates
            allowInput: true
        });

        // Toggle show/hide
        $(document).ready(function () {

            $('#is_post_dated').on('change', function () {

                if ($(this).is(':checked')) {
                    $('#post_date_wrapper').removeClass('d-none');

                    $('#post_date_status')
                        .text('ON')
                        .removeClass('text-danger')
                        .addClass('text-success');

                } else {
                    $('#post_date_wrapper').addClass('d-none');
                    $('#post_date').val('');

                    $('#post_date_status')
                        .text('OFF')
                        .removeClass('text-success')
                        .addClass('text-danger');
                }

            });


            const urlParams = new URLSearchParams(window.location.search);
            const prefillOrderId = urlParams.get('order');

            if (prefillOrderId) {
                // Open modal
                $('#paymentModal').modal('show');

                // Force select order AFTER modal opens
                $('#paymentModal')
                    .off('shown.bs.modal')
                    .on('shown.bs.modal', function () {

                        $.get(
                            "{{ route('ajax.get.order.details', ['company' => $company->id]) }}",
                            { id: prefillOrderId },
                            function (order) {

                                // Create proper Select2 option
                                let option = new Option(
                                    order.order_number + ' - ' + order.customer_name,
                                    order.id,
                                    true,
                                    true
                                );

                                $('#order_id')
                                    .append(option)
                                    .trigger('change'); // 🔥 triggers your existing logic
                            }
                        );

                    });

            } const prefillPaymentId = urlParams.get('payment');

            if (prefillPaymentId) {

                IS_EDIT_MODE = true;

                let editUrl =
                    "{{ route('payments.edit', ['company' => $company->id, 'payment' => ':id']) }}"
                        .replace(':id', prefillPaymentId);

                $.get(editUrl, function (p) {

                    $('#paymentModal').modal('show');

                    $('#paymentForm')[0].reset();

                    $('#paymentForm')
                        .find('input[name=_method]')
                        .remove();

                    // ORDER
                    $('#order_id')
                        .empty()
                        .append(new Option(
                            p.order.order_number + ' - ' + p.order.customer_name,
                            p.order.id,
                            true,
                            true
                        ))
                        .prop('disabled', true);

                    // CUSTOMER
                    $('#customer_name').val(p.order.customer_name);
                    $('#customer_email').val(p.order.email);
                    $('#customer_mobile').val(p.order.mobile);

                    // PAYMENT
                    $('#payment_number').val(p.payment_number);

                    ORDER_TOTAL = parseFloat(p.order.final_amount);

                    PAID_AMOUNT =
                        parseFloat(p.order.paid_amount) - parseFloat(p.amount);

                    $('#order_total').val(ORDER_TOTAL.toFixed(2));

                    $('#paid_amount').val(
                        parseFloat(p.order.paid_amount).toFixed(2)
                    );

                    $('#due_amount').val(
                        (ORDER_TOTAL - parseFloat(p.order.paid_amount)).toFixed(2)
                    );

                    // DATE
                    let d = new Date(p.payment_date);

                    $('#payment_date').val(
                        String(d.getDate()).padStart(2, '0') + '/' +
                        String(d.getMonth() + 1).padStart(2, '0') + '/' +
                        d.getFullYear()
                    );

                    // TIME
                    $('#payment_time').val(
                        p.payment_time.slice(0, 5)
                    );

                    // PAYMENT DETAILS
                    $('#payment_amount')
                        .val(formatNumber(p.amount))
                        .prop('readonly', false);

                    $('#payment_mode')
                        .val(p.payment_mode)
                        .trigger('change');

                    $('#transaction_reference')
                        .val(p.transaction_reference ?? '');

                    $('#payment_note')
                        .summernote('code', p.note);

                    // POST DATE
                    if (p.is_post_dated && p.post_date) {

                        $('#is_post_dated')
                            .prop('checked', true)
                            .trigger('change');

                        postDatePicker.setDate(p.post_date, true);

                    } else {

                        $('#is_post_dated')
                            .prop('checked', false)
                            .trigger('change');

                        postDatePicker.clear();
                    }

                    // UPDATE URL
                    let updateUrl =
                        "{{ route('payments.update', ['company' => $company->id, 'payment' => ':id']) }}"
                            .replace(':id', p.id);

                    $('#paymentForm')
                        .attr('action', updateUrl)
                        .append('<input type="hidden" name="_method" value="PUT">');

                });

            }
            $('.summernote').summernote({
                height: 200,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline']],
                    ['para', ['ul', 'ol']],
                    ['view', ['codeview']]
                ],
                disableDragAndDrop: true,
                callbacks: {
                    onPaste: function (e) {
                        e.preventDefault();
                        let text = (e.originalEvent || e).clipboardData.getData('text/plain');
                        document.execCommand('insertText', false, text);
                    },
                    onKeydown: function (e) {
                        if (e.keyCode === 13) {
                            document.execCommand('insertLineBreak');
                            e.preventDefault();
                        }
                    }
                }
            }); $('#payment_amount').focus();
            loadPayments();
            /* ===============================
               FLATPICKR INIT
            ================================*/

            fromDatePicker = flatpickr("#from_date", {
                dateFormat: "d/m/Y",   // IMPORTANT: backend-friendly

                allowInput: true
            });

            toDatePicker = flatpickr("#to_date", {
                dateFormat: "d/m/Y",
                allowInput: true
            });

            paymentDatePicker = flatpickr("#payment_date", {
                dateFormat: "d/m/Y",
                defaultDate: new Date(),
                allowInput: true
            });

            paymentTimePicker = flatpickr("#payment_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultDate: new Date(),
                allowInput: true
            });

            // Open date picker on icon click
            $('#payment_date').closest('.input-group').find('.input-group-text')
                .on('click', function () {
                    paymentDatePicker.open();
                });

            // Open time picker on icon click
            $('#payment_time').closest('.input-group').find('.input-group-text')
                .on('click', function () {
                    paymentTimePicker.open();
                });

            /* ===============================
               ORDER SEARCH
            ================================*/
            $('#order_id').select2({
                dropdownParent: $('#paymentModal'),
                placeholder: 'Search by order no / customer / email',
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('ajax.orders.search', ['company' => $company->id]) }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ search: params.term }),
                    processResults: data => ({
                        results: data.map(o => ({
                            id: o.id,
                            text: o.order_number + ' - ' + o.customer_name
                        }))
                    })
                }
            });
            /* ===============================
               FETCH ORDER DETAILS
            ================================*/
            $('#order_id').on('change', function () {

                let orderId = $(this).val();
                if (!orderId) return;

                $.get(
                    "{{ route('ajax.get.order.details', ['company' => $company->id]) }}",
                    { id: orderId },
                    function (o) {

                        // Customer
                        $('#customer_name').val(o.customer_name);
                        $('#customer_email').val(o.email);
                        $('#customer_mobile').val(o.mobile);
                        $('.currency-symbol').text(o.currency_symbol);
                        // Financials
                        ORDER_TOTAL = parseFloat(o.final_amount);
                        PAID_AMOUNT = parseFloat(o.paid_amount ?? 0);

                        $('#order_total').val(ORDER_TOTAL.toFixed(2));
                        $('#paid_amount').val(PAID_AMOUNT.toFixed(2));
                        $('#due_amount').val((ORDER_TOTAL - PAID_AMOUNT).toFixed(2));
                        $('#payment_amount').prop('readonly', false);
                        $('#amount_error').addClass('d-none');
                        $('#payment_amount').val('');
                        // Payment number
                        $('#payment_number').val(
                            'PAY-' + o.company.initials + '/' +
                            new Date().toISOString().slice(2, 10).replace(/-/g, '') +
                            '/' + o.id
                        );
                    }
                );
            });

            /* ===============================
               AUTO UPDATE DUE ON AMOUNT
            ================================*/
            $('#payment_amount').on('input', function () {

                let raw = $(this).val().replace(/,/g, '');
                let pay = parseFloat(raw);
                let remaining = ORDER_TOTAL - PAID_AMOUNT;

                // Hide warning by default
                $('#amount_error').addClass('d-none');

                if (raw === '' || isNaN(pay)) {
                    $('#due_amount').val(remaining.toFixed(2));
                    return;
                }

                // Show warning if exceeds due
                if (pay > remaining) {
                    $('#amount_error').removeClass('d-none');
                    $('#due_amount').val('0.00');
                    return;
                }

                let due = remaining - pay;
                $('#due_amount').val(due.toFixed(2));
            });

            $('#payment_amount').on('blur', function () {
                let val = parseFloat($(this).val().replace(/,/g, ''));
                if (!isNaN(val)) {
                    $(this).val(formatNumber(val));
                }
            });
            $('#paymentForm').on('submit', function (e) {
                e.preventDefault();

                if (!$('#amount_error').hasClass('d-none')) {
                    Swal.fire('Error', 'Entered amount exceeds remaining due', 'error');
                    return;
                }

                let amt = $('#payment_amount').val().replace(/,/g, '');
                $('#payment_amount').val(amt);
                let form = $(this);
                let formData = form.serialize();

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    beforeSend: function () {
                        $('button[type=submit]').prop('disabled', true);
                    },
                    success: function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        });

                        // 🔥 Redirect to Orders Index after success
                        setTimeout(function () {
                            window.location.href = "{{ route('orders.index', $company->id) }}";
                        }, 1200);
                    },
                    error: function (xhr) {
                        let msg = 'Something went wrong';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }

                        Swal.fire('Error', msg, 'error');
                    },
                    complete: function () {
                        $('button[type=submit]').prop('disabled', false);
                    }
                });
            });

            /* ===============================
               PAYMENT MODE TOGGLE
            ================================*/
            $('#payment_mode').on('change', function () {

                let mode = $(this).val();
                let $txnWrapper = $('#txn_ref_wrapper');
                let $txnInput = $('#transaction_reference');

                if (mode === 'cash') {
                    $txnWrapper.addClass('d-none');
                    $txnInput.prop('required', false).val('');
                } else {
                    $txnWrapper.removeClass('d-none');
                    $txnInput.prop('required', true);
                }
            });


            /* ===============================
               RESET MODAL
            ================================*/
            $('#paymentModal').on('hidden.bs.modal', function () {
                $('#paymentForm')[0].reset();
                $('#paymentForm').find('input[name=_method]').remove();
                $('#paymentForm').attr(
                    'action',
                    "{{ route('payments.store', ['company' => $company->id]) }}"
                );
                $('#order_id').prop('disabled', false).val(null).trigger('change');
                $('#payment_amount').prop('readonly', true);
                $('#amount_error').addClass('d-none'); $('#payment_note').summernote('code', '');
            });

            /* ===============================
               PREFILL PAYMENT NUMBER
            ================================*/
            $('#paymentModal').on('shown.bs.modal', function () {
                if (IS_EDIT_MODE) return;
                paymentDatePicker.setDate(new Date(), true);

                // 🔥 Set current time (HH:MM)
                let now = new Date();
                let time = now.toTimeString().slice(0, 5); // 16:30
                $('#payment_time').val(time);
                $.get(
                    "{{ route('ajax.generate.payment.number', ['company' => $company->id]) }}",
                    function (res) {
                        $('#payment_number').val(res.payment_number);
                    }
                );

            });


        });
        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });

    </script>

    <script>
        function loadPayments() {

            let params = {
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val(),
                search: $('#payment_search').val()
            };

            $("#loader").show();
            $("#example1").hide();

            $.ajax({
                url: "{{ route('company.payments.data', ['company' => $company->id]) }}",
                type: "GET",
                data: params,

                beforeSend: function () {
                    $("#loader").show();
                    $("#example1").hide();
                },

                success: function (response) {

                    if ($.fn.DataTable.isDataTable('#example1')) {
                        $('#example1').DataTable().destroy();
                    }

                    $('#paymentRows').html(response);

                    // If no data row exists, skip DataTable
                    if ($("#paymentRows").find("tr.no-data").length > 0) {
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
                },

                error: function () {
                    console.error('Payment fetch failed');
                },

                complete: function () {
                    $("#loader").hide();
                    $("#example1").show();
                }
            });
        }
        $(document).on('click', '#filter', function () {
            loadPayments();
        });
        $(document).on('click', '#reset', function () {
            fromDatePicker.clear();
            toDatePicker.clear();
            $('#payment_search').val('');
            loadPayments();
        });
        let paymentTimer;
        $('#payment_search').on('keyup', function () {
            clearTimeout(paymentTimer);
            paymentTimer = setTimeout(function () {
                loadPayments();
            }, 500);

        });
        $('#from_date').closest('.input-group').find('.input-group-text')
            .on('click', function () {
                fromDatePicker.open();
            });

        $('#to_date').closest('.input-group').find('.input-group-text')
            .on('click', function () {
                toDatePicker.open();
            });
        function formatNumber(num) {
            return new Intl.NumberFormat('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num);
        }


    </script>

    <script>
        $(document).on('click', '.edit-payment', function () {
            IS_EDIT_MODE = true;

            let id = $(this).data('id');
            let editUrl = "{{ route('payments.edit', ['company' => $company->id, 'payment' => 0]) }}"
                .replace('/0/edit', '/' + id + '/edit');

            $.get(editUrl, function (p) {

                $('#paymentModal').modal('show');

                $('#paymentForm')[0].reset();
                $('#paymentForm').find('input[name=_method]').remove();

                $('#order_id')
                    .empty()
                    .append(new Option(
                        p.order.order_number + ' - ' + p.order.customer_name,
                        p.order.id,
                        true,
                        true
                    ))
                    .prop('disabled', true);
                $('#customer_name').val(p.order.customer_name);
                $('#customer_email').val(p.order.email);
                $('#customer_mobile').val(p.order.mobile);
                // ✅ Keep SAME payment number
                $('#payment_number').val(p.payment_number);
                // 🔥 FIX TOTALS
                ORDER_TOTAL = parseFloat(p.order.final_amount);
                PAID_AMOUNT = parseFloat(p.order.paid_amount) - parseFloat(p.amount);
                // Show values EXACTLY as backend
                $('#order_total').val(ORDER_TOTAL.toFixed(2));
                $('#paid_amount').val(parseFloat(p.order.paid_amount).toFixed(2));
                $('#due_amount').val(
                    (ORDER_TOTAL - parseFloat(p.order.paid_amount)).toFixed(2)
                );
                // Date
                let d = new Date(p.payment_date);
                $('#payment_date').val(
                    String(d.getDate()).padStart(2, '0') + '/' +
                    String(d.getMonth() + 1).padStart(2, '0') + '/' +
                    d.getFullYear()
                );
                $('#payment_time').val(p.payment_time.slice(0, 5)); // 16:30
                $('#payment_amount').val(formatNumber(p.amount)).prop('readonly', false);
                $('#payment_mode').val(p.payment_mode).trigger('change');
                $('#transaction_reference').val(p.transaction_reference ?? '');
                $('#payment_note').summernote('code', p.note);
                let updateUrl = "{{ route('payments.update', ['company' => $company->id, 'payment' => 0]) }}"
                    .replace('/0', '/' + p.id);
                if (p.is_post_dated && p.post_date) {
                    $('#is_post_dated')
                        .prop('checked', true)
                        .trigger('change');
                    // ✅ Set date safely using flatpickr
                    postDatePicker.setDate(p.post_date, true);
                } else {
                    $('#is_post_dated')
                        .prop('checked', false)
                        .trigger('change');
                    // ✅ Clear date
                    postDatePicker.clear();
                }
                $('#paymentForm')
                    .attr('action', updateUrl)
                    .append('<input type="hidden" name="_method" value="PUT">');
            });
        });

        $(document).on('click', '.delete-payment', function () {
            let id = $(this).data('id');

            let deleteUrl = "{{ route('payments.destroy', ['company' => $company->id, 'payment' => 0]) }}";
            deleteUrl = deleteUrl.replace('/0', '/' + id);

            Swal.fire({
                title: 'Delete Payment?',
                text: 'This action cannot be undone',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function () {
                            Swal.fire('Deleted!', 'Payment removed', 'success');
                            loadPayments();
                        }
                    });
                }
            });
        });
        function resetPaymentModal() {
            IS_EDIT_MODE = false;

            $('#paymentForm')[0].reset();
            $('#paymentForm').find('input[name=_method]').remove();

            $('#paymentForm').attr(
                'action',
                "{{ route('payments.store', ['company' => $company->id]) }}"
            );

            $('#order_id')
                .prop('disabled', false)
                .val(null)
                .trigger('change');

            $('#payment_amount').prop('readonly', true);
            $('#amount_error').addClass('d-none');

            // 🔥 IMPORTANT FIX
            $('#payment_mode').val('cash').trigger('change');
            $('#transaction_reference').val('');

            $('#payment_note').summernote('code', '');
        }

    </script>
@endpush