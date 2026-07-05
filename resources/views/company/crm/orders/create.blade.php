@extends('company.layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Add Order</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('company.dashboard', ['company' => $company->id]) }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('orders.index', ['company' => $company->id]) }}">Order List</a>
                        </li>
                        <li class="breadcrumb-item active">Add Order</li>
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
                            <div class="d-flex gap-2 ml-auto">
                                <a href="{{ route('orders.index', ['company' => $company->id]) }}"
                                    class="btn btn-sm btn-success">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('orders.store', ['company' => $company->id]) }}"
                                autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="update_customer_master" id="update_customer_master" value="0">
                                <h4 class="text-primary"><b>1.Quotation</b></h4>
                                <div class="row">

                                    <div class="col-md-3">
                                        <label>Quotation *</label>
                                        <select name="quotation_id" id="quotation_id" class="form-control select2"
                                            required></select>
                                    </div>
                                    <input type="hidden" name="lead_id" id="lead_id">

                                    <input type="hidden" name="company_id" id="company_id" value="{{ $company->id }}">

                                    @php
                                        $authUser = auth()->user();

                                    @endphp
                                    <div class="col-md-3">
                                        <label>Assign Staff *</label>
                                        {{-- Staff --}}
                                        <input type="text" class="form-control" value="{{ $authUser->name }}" readonly>
                                        <input type="hidden" name="assigned_user_id" value="{{ $authUser->id }}">

                                    </div>

                                </div>
                                <hr>
                                <h4 class="text-primary"><b>2. Order Details</b></h4>
                                <div class="mobile-scroll">
                                    <div class="row flex-nowrap">
                                        <div class="row">
                                            <div class="col">
                                                <label>Order Number</label>
                                                <input type="text" name="order_number" id="order_number"
                                                    class="form-control" value="Select Quotation First" readonly>
                                            </div>

                                            <div class="col">
                                                <label>Order Date *</label>
                                                <div class="input-group">
                                                    <input type="text" name="order_date" id="order_date"
                                                        class="form-control datepicker" placeholder="DD/MM/YYYY"
                                                        value="{{ old('order_date') }}" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>


                                            </div>

                                            <div class="col">
                                                <label>PO (No.)</label>
                                                <input type="text" name="po_number" id="po_number" class="form-control"
                                                    value="Select Quotation First" readonly>
                                            </div>

                                            <div class="col">
                                                <label>PO Date</label>
                                                <div class="input-group">
                                                    <input type="text" name="po_date" id="po_date"
                                                        class="form-control datepicker" placeholder="DD/MM/YYYY"
                                                        value="{{ old('po_date')}}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <label>Delivery Date</label>
                                                <div class="input-group">
                                                    <input type="text" name="delivery_date" id="delivery_date"
                                                        class="form-control datepicker" placeholder="DD/MM/YYYY"
                                                        value="{{ old('delivery_date') }}" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>


                                {{-- ================================================= --}}
                                {{-- 2. CUSTOMER DETAILS (SNAPSHOT) --}}
                                {{-- ================================================= --}}
                                <h4 class="text-primary"><b>3. Customer Details</b></h4>
                                <label class="form-check-label d-none" for="update_customer_master">
                                    Updating Customer Details Will
                                </label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Customer Name</label>
                                        <input type="text" name="customer_name" id="customer_name" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label>Contact Person</label>
                                        <input type="text" name="contact_person" id="contact_person" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label>Customer GST</label>
                                        <input type="text" name="customer_gst" id="customer_gst" class="form-control">
                                    </div>

                                    <div class="col-md-4 mt-2">
                                        <label>Email</label>
                                        <input type="email" name="email" id="email" class="form-control">
                                    </div>

                                    <div class="col-md-4 mt-2">
                                        <label>Mobile</label>

                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <select id="country_code_select" class="form-control"
                                                    style="max-width:80px">
                                                    <option value="">+--</option>
                                                </select>
                                            </div>

                                            <input type="text" id="mobile" maxlength="10" name="mobile" class="form-control"
                                                placeholder="Enter mobile number">
                                        </div>

                                        {{-- extra mobiles --}}
                                        <div id="extra-mobiles" class="mt-1 text-muted small"></div>
                                    </div>

                                    <div class="col-md-4 mt-2">
                                        <label>Country</label>
                                        <input type="text" name="country" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-6 mt-2">
                                        <label>Office Address</label>
                                        <textarea name="office_address" id="office_address" class="form-control"></textarea>
                                    </div>

                                    <div class="col-md-6 mt-2">
                                        <label>Delivery Address</label>
                                        <textarea name="delivery_address" id="delivery_address"
                                            class="form-control"></textarea>
                                    </div>

                                </div>

                                <hr>
                                <hr>
                                <h4 class="text-primary"><b>4. Currency & Rate of conversion</b></h4>

                                <div class="row">
                                    <div class="col">
                                        <label>Currency *</label>
                                        <select name="currency" id="currency" class="form-control" required>
                                            <option value="INR">INR (₹)</option>
                                            <option value="USD">USD ($)</option>
                                            <option value="EUR">EUR (€)</option>
                                        </select>
                                    </div>

                                    <div class="col">
                                        <label>Conversion Rate</label>
                                        <input type="text" step="0.01" name="conversion_rate" id="conversion_rate"
                                            class="form-control" readonly>
                                    </div>
                                </div>
                                <hr>
                                <h4 class="text-primary"><b>5. Order Items</b></h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="quotation_items_table">
                                        <thead>
                                            <tr>
                                                <th style="width:30px;"></th>
                                                <th>Type</th>
                                                <th>Origin</th>
                                                <th>Item Name(English)</th>
                                                <th>Item Name (Hindi)</th>
                                                <th>Description</th>
                                                <th>Qty</th>
                                                <th class="price-col price-header">Price (<span
                                                        class="currency-label">₹</span>)</th>
                                                <th class="total-col total-header">Total (<span
                                                        class="currency-label">₹</span>)</th>
                                                <th class="cfv-col d-none">CFV (₹)</th>
                                                <th>
                                                    <!-- <button type="button" class="btn btn-success" id="add_row">+</button> -->
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody id="item-body">
                                            {{-- EMPTY initially --}}
                                        </tbody>
                                        <div id="deleted-items-wrapper"></div>

                                    </table>
                                </div>

                                {{-- MASTER ITEMS --}}
                                <select id="master_items" style="display:none;">
                                    @foreach($machines as $m)
                                        <option value="{{ $m->id }}" data-type="machine"
                                            data-origin="{{ strtolower($m->origin) }}" data-description="{{ $m->description }}"
                                            data-hindi_name="{{ $m->hi_name }}">
                                            {{ $m->name }}
                                        </option>
                                    @endforeach

                                    @foreach($components as $c)
                                        <option value="{{ $c->id }}" data-type="component"
                                            data-origin="{{ strtolower($c->origin) }}" data-description="{{ $c->description }}"
                                            data-hindi_name="{{ $c->hi_name }}">
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <hr>

                                {{-- ================================================= --}}
                                {{-- 6. TOTALS --}}
                                {{-- ================================================= --}}
                                <div class="totals-scroll">
                                    <div class="row flex-nowrap totals-row">

                                        <div class="col">
                                            <label>Sub Total</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text currency-symbol">₹</span>
                                                </div>
                                                <input type="text" name="total_amount" class="form-control">
                                            </div>
                                        </div>

                                        <div class="col">
                                            <label>Discount(amt.)</label>
                                            <input type="text" name="discount" class="form-control discount">
                                        </div>

                                        <div class="col">
                                            <label>Tax %</label>
                                            <input type="text" name="tax" class="form-control tax">
                                        </div>
                                        <div class="col">
                                            <label>Tax (amount)</label>
                                            <div class="input-group">
                                                <input type="text" name="tax_amount" id="tax_amount" class="form-control"
                                                    step="0.01" readonly>

                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-success" id="round_tax">
                                                        Round
                                                    </button>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col">
                                            <label>Final Amount</label>
                                            <input type="text" step="0.01" name="final_amount" class="form-control"
                                                readonly>
                                        </div>

                                    </div>
                                </div>

                                <hr>

                                {{-- ================================================= --}}
                                {{-- 7. FILES & TERMS --}}
                                {{-- ================================================= --}}
                                <h4 class="text-primary"><b>6. Files & Terms</b></h4>
                                <div id="quotation_files"></div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Upload Files</label>
                                        <input type="file" name="uploads[]" id="fileInput" class="form-control" multiple>
                                        <small class="text-muted">Existing quotation files are shown above. Only newly
                                            uploaded files will be added to this order.</small>
                                        <div id="filePreview" class="row mt-2"></div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="d-flex mb-1 justify-content-between align-items-center">
                                            <label class="mb-0"><b>Remark (Special Clause)</b></label>
                                        </div>
                                        <textarea name="remark" id="remark" class="form-control summernote"></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex mb-1 justify-content-between align-items-center">
                                            <label class="mb-0"><b>Remark (Special Clause)</b></label>
                                        </div>
                                        <textarea name="hi_remark" id="hi_remark"
                                            class="form-control summernote"></textarea>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label>Terms & Conditions</label>
                                        <div class="d-flex mb-1 justify-content-end">
                                        </div>
                                        <textarea name="terms_conditions" id="terms_conditions"
                                            class="form-control summernote"></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Terms & Conditions</label>
                                        <div class="d-flex mb-1 justify-content-end">
                                        </div>
                                        <textarea name="hi_terms_conditions" id="hi_terms_conditions"
                                            class="form-control summernote"></textarea>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success mt-3 text-right">
                                    <i class="fa fa-save"></i> Save Order
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        #extra-mobiles .list-group-item {
            font-size: 13px;
            background: #f8fafc;
        }

        .file-preview {
            position: relative;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 6px;
            text-align: center;
            background: #f9fafb;
        }

        .file-preview img {
            max-width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }

        .file-preview .remove-file {
            position: absolute;
            top: 2px;
            right: 4px;
            cursor: pointer;
            color: #dc2626;
            font-weight: bold;
        }

        @media (max-width: 768px) {

            /* ONLY table scroll */
            #quotation_items_table {
                min-width: 900px;
                width: max-content;
            }

            #quotation_items_table th,
            #quotation_items_table td {
                white-space: nowrap;
            }



            /* Scroll container */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .totals-scroll {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .totals-row {
                min-width: 600px;
                /* adjust if needed */
            }

            .totals-row .col {
                min-width: 140px;
            }

        }

        @media (max-width: 768px) {

            h4 {
                font-size: 14px !important;
            }

        }

        @media (max-width: 768px) {

            .mobile-scroll {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .mobile-scroll .row {
                flex-wrap: nowrap;
                min-width: 700px;
                /* adjust if needed */
            }

            .mobile-scroll .col {
                min-width: 150px;
            }
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // init flatpickr
            flatpickr('.datepicker', {
                dateFormat: "d/m/Y",
                allowInput: true
            });

            // icon click safety check
            document.querySelectorAll('.input-group-text').forEach(icon => {
                icon.addEventListener('click', function () {
                    const input = this.closest('.input-group').querySelector('.datepicker');
                    if (input && input._flatpickr) {
                        input._flatpickr.open();
                    }
                });
            });

        });
    </script>
    <script>
        let orderFp, poFp, deliveryFp;


        $(document).ready(function () {
            orderFp = flatpickr('#order_date', {
                dateFormat: "d/m/Y",
                altInput: true,
                altFormat: "d/m/Y",
                allowInput: true
            });

            poFp = flatpickr('#po_date', {
                dateFormat: "d/m/Y",
                altInput: true,
                altFormat: "d/m/Y", defaultDate: "today",
                allowInput: true
            });

            deliveryFp = flatpickr('[name="delivery_date"]', {
                dateFormat: "d/m/Y",
                altInput: true,
                altFormat: "d/m/Y",
                allowInput: true
            });

            let companyId = $('#company_id').val();

            if (!companyId) return;
        });
    </script>
    <script>
        $(document).on('change', '.item_type, .origin', function () {
            let row = $(this).closest('tr');
            applyFilter(row);
        });
        function applyFilter(row) {

            let type = row.find('.item_type').val();
            let origin = row.find('.origin').val();

            let select = row.find('.item_select');
            select.empty().append('<option value="">Select Item</option>');

            if (!type || !origin) return;

            MASTER.forEach(opt => {
                if ($(opt).data('type') == type && $(opt).data('origin') == origin) {
                    select.append($(opt).clone());
                }
            });

            select.trigger('change');
        }

        let MASTER = [];

        $(document).ready(function () {
            $('#master_items option').each(function () {
                MASTER.push($(this).clone());
            });
        });

        function initRow(row) {

            let select = row.find('.item_select');

            applyFilter(row);   // 🔥 THIS WAS MISSING

            let saved = row.find('.saved_item_id').val();

            if (saved) {
                select.val(saved).trigger('change');
            }
            let existing = row.find('.description').val().trim();

            if (!existing) {
                row.find('.description').val(
                    select.find(':selected').data('description') || ''
                );
            }


            select.select2({ width: '100%' });
        }
        $(document).on('change', '.item_select', function () {

            let row = $(this).closest('tr');

            let selected = $(this).find(':selected');

            // Hindi name
            row.find('.item_name_hi').val(
                selected.data('hindi_name') || ''
            );

            // Existing description
            let existing = row.find('.description_html').val()?.trim();

            // Fallback from machine/component master
            if (!existing) {

                let masterDescription = selected.data('description') || '';

                row.find('.description').val(
                    $('<div>').html(masterDescription).text()
                );

                row.find('.description_html').val(masterDescription);
            }
        });



        function calculateRow(row) {

            let qty = parseFloat(row.find('.qty').val()) || 0;
            let rate = parseFloat($('#conversion_rate').val()) || 1;
            let currency = $('#currency').val();

            let price = parseFloat(row.find('.price_input').val()) || 0;

            let totalSelected = qty * price;
            let totalINR = (currency === 'INR')
                ? totalSelected
                : totalSelected * rate;

            row.find('.total_selected').val(totalSelected.toFixed(2));
            row.find('.total_inr').val(totalINR.toFixed(2));
        }

        function calculateSummary() {

            let subtotal = 0;
            let currency = $('#currency').val();
            let rate = parseFloat($('#conversion_rate').val()) || 1;

            // ✅ SUM selected currency totals (NOT INR)
            $('.total_selected').each(function () {
                subtotal += parseFloat($(this).val()) || 0;
            });

            // ✅ discount & tax
            let discount = parseFloat($('input[name="discount"]').val()) || 0;
            let taxPercent = parseFloat($('input[name="tax"]').val()) || 0;

            let afterDiscount = subtotal - discount;
            if (afterDiscount < 0) afterDiscount = 0;

            let taxAmount = (afterDiscount * taxPercent) / 100;
            let finalAmount = afterDiscount + taxAmount;

            // ✅ SET VALUES (IN SELECTED CURRENCY)
            $('input[name="total_amount"]').val(subtotal.toFixed(2));
            $('input[name="tax_amount"]').val(taxAmount.toFixed(2));
            $('input[name="final_amount"]').val(finalAmount.toFixed(2));
        }
        $(document).on('input', 'input[name="discount"]', function () {
            let total = parseFloat($('input[name="total_amount"]').val()) || 0;
            let discount = parseFloat($(this).val()) || 0;

            if (discount > total) {
                Swal.fire('Invalid Discount', 'Discount cannot be greater than Total Amount', 'error');
                $(this).val(0);
                calculateSummary();
            }
        });

        $(document).on('input', '.qty, .unit_price, input[name="discount"], input[name="tax"]', function () {
            calculateSummary();
        });

        $('#add_row').on('click', function () {

            let row = $(`
                                                                                                                                                                                                                                                                                                                                                                                                                                <tr class="item_row">
                                                                                                                                                                                                                                                                                                                                                                                                                                    <input type="hidden" class="saved_item_id" value="">
                                                                                                                                                                                                                                                                            <td class="text-center align-middle drag-handle" style="cursor: grab;">
                                                                                                                                                                                                                                                                                <i class="fa fa-bars text-muted"></i>
                                                                                                                                                                                                                                                                                <input type="hidden" name="sort_order[]" class="sort_order" value="1">
                                                                                                                                                                                                                                                                            </td>

                                                                                                                                                                                                                                                                                                                                                                                                                                    <td>
                                                                                                                                                                                                                                                                                                                                                                                                                                        <select name="item_type[]" class="form-control item_type">
                                                                                                                                                                                                                                                                                                                                                                                                                                            <option value="machine">Machine</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                            <option value="component">Component</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                        </select>
                                                                                                                                                                                                                                                                                                                                                                                                                                    </td>

                                                                                                                                                                                                                                                                                                                                                                                                                                    <td>
                                                                                                                                                                                                                                                                                                                                                                                                                                        <select name="origin[]" class="form-control origin">
                                                                                                                                                                                                                                                                                                                                                                                                                                            <option value="self">Self</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                            <option value="outsource">Outsource</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                        </select>
                                                                                                                                                                                                                                                                                                                                                                                                                                    </td>

                                                                                                                                                                                                                                                                                                                                                                                                                                    <td>
                                                                                                                                                                                                                                                                                                                                                                                                                                        <select name="item_id[]" class="form-control item_select" required></select>
                                                                                                                                                                                                                                                                                                                                                                                                                                    </td>

                                                                                                                                                                                                                                                                                                                                                                                                                                  <td>
                                                                                                                                                                                                                                                                                                        <div class="d-flex gap-2">

                                    <input type="text" class="form-control description" readonly>

                                    <input type="hidden" name="description_html[]" class="description_html"><input type="hidden"
                           name="description_hi_html[]"
                           class="description_hi_html">
                                                                                                                                                                                                                                                                                                            <button type="button"
                                                                                                                                                                                                                                                                                                                    class="btn btn-sm btn-outline-success edit-description"
                                                                                                                                                                                                                                                                                                                    title="Edit Description">
                                                                                                                                                                                                                                                                                                                <i class="fa fa-edit"></i>
                                                                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                                                                             <button type="button"
                                                                                                                                                                                                                                                                    class="btn btn-sm btn-outline-info translate-description"
                                                                                                                                                                                                                                                                    title="Translate">
                                                                                                                                                                                                                                                                    <i class="fa fa-language"></i>
                                                                                                                                                                                                                                                                </button>
                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                    </td>


                                                                                                                                                                                                                                                                                                                                                                                                                                    <td>
                                                                                                                                                                                                                                                                                                                                                                                                                                        <input type="text" name="quantity[]" value="1" class="form-control qty" required>
                                                                                                                                                                                                                                                                                                                                                                                                                                    </td>

                                                                        <td class="price-col">
                                                                            <input type="text" name="unit_price[]" class="form-control price_input"  value="0">
                                                                        </td>

                                                                        <td class="total-col">
                                                                            <input type="text" name="total[]" class="form-control total_selected" readonly>
                                                                        </td>

                                                                        <td class="cfv-col d-none">
                                                                            <input type="text" name="converted_total_price[]" class="form-control total_inr" readonly>
                                                                        </td>
                                                                                                                                                                                                                                                                                                                                                                                                                                    <td>
                                                                                                                                                                                                                                                                                                                                                                                                                                        <button type="button" class="btn btn-danger remove_row">X</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                    </td>
                                                                                                                                                                                                                                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                            `);

            $('#item-body').append(row);

            // ✅ DEFAULT VALUES
            row.find('.item_type').val('machine');
            row.find('.origin').val('self');

            // ✅ Populate item list immediately
            initRow(row);
            calculateRow(row); applyCurrencyUIToTable($('#currency').val());
            // ✅ Select2 init
            row.find('.item_select').select2({ width: '100%' });
        });
        $(document).on('input', '.qty, .price_input', function () {
            let row = $(this).closest('tr');
            calculateRow(row);
            calculateSummary();
        });
        $(document).on("click", ".remove_row", function () {
            let row = $(this).closest("tr");

            let itemId = row.find("input[name='row_item_id[]']").val(); // existing DB ID

            Swal.fire({
                title: "Delete Item?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes Remove"
            }).then(res => {
                if (res.isConfirmed) {

                    if (itemId) {   // 🟢 push id only if row exists in DB
                        $("#deleted-items-wrapper").append(
                            `<input type="hidden" name="deleted_items_ids[]" value="${itemId}">`
                        );
                    }

                    row.remove();
                    calculateSummary();
                }
            });
        });


    </script>
    <script>
        $(document).ready(function () {

            $('#quotation_id').select2({
                placeholder: 'Search quotation...',
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('ajax.quotations.search', ['company' => $company->id]) }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { search: params.term };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(q => ({
                                id: q.id,
                                text: `${q.quote_number} - ${q.customer_name} (${q.mobile || '-'})`
                            }))
                        };
                    }
                }
            });

        });

    </script>
    <script>
        $(document).on('input', '.discount,.tax,.qty,.unit_price, .total_price, .converted_price, .converted_total,.price_input', function () {

            let value = this.value;

            // allow only digits and one decimal point
            value = value.replace(/[^0-9.]/g, '');

            let parts = value.split('.');

            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            this.value = value;
        });
        $('#quotation_id').on('change', function () {

            let quotationId = $(this).val();
            if (!quotationId) return;

            $.get(
                "{{ route('ajax.quotation.for.order', ['company' => $company->id]) }}",
                { id: quotationId },
                function (q) {

                    /* ================= COMPANY ================= */
                    if (q.company_id) {
                        $('#company_id').val(q.company_id).trigger('change');
                    }
                    /* ================= PI DETAILS ================= */
                    $('input[name="pi_number"]').val(q.pi_number);
                    if (q.pi_date) {
                        $('#po_date')[0]._flatpickr.setDate(q.pi_date, true);
                    }

                    isAutoCurrencySet = true;

                    $('#currency').val(q.currency).trigger('change');

                    $('#conversion_rate').val(q.conversion_rate);

                    // 👇 reset after small delay
                    setTimeout(() => {
                        isAutoCurrencySet = false;
                    }, 100);
                    if (q.order_date) {
                        $('#order_date')[0]._flatpickr.setDate(q.order_date, true);
                    }

                    if (q.delivery_date) {
                        $('input[name="delivery_date"]')[0]._flatpickr.setDate(q.delivery_date, true);
                    }



                    /* ================= STAFF ================= */
                    $('select[name="assigned_user_id"]').val(q.assigned_user_id);

                    /* ================= FILES ================= */
                    /* ================= FILES ================= */
                    if (q.files && q.files.length) {

                        let html = `
                                                                                                                                                                                                                                                                                                                                                                                                    <table class="table table-bordered table-sm mt-2">
                                                                                                                                                                                                                                                                                                                                                                                                        <thead>
                                                                                                                                                                                                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                <th>#</th>
                                                                                                                                                                                                                                                                                                                                                                                                                <th>File</th>
                                                                                                                                                                                                                                                                                                                                                                                                                <th>Download</th>
                                                                                                                                                                                                                                                                                                                                                                                                                <th>Delete?</th>
                                                                                                                                                                                                                                                                                                                                                                                                                <th>Replace</th>
                                                                                                                                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                                                                                                                                                        </thead>
                                                                                                                                                                                                                                                                                                                                                                                                        <tbody>
                                                                                                                                                                                                                                                                                                                                                                                                `;

                        q.files.forEach((f, i) => {
                            html += `
                                                                                                                                                                                                                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                                                                                                                                                                                                                            <td>${i + 1}</td>
                                                                                                                                                                                                                                                                                                                                                                                                            <td>${f.file_name}</td>

                                                                                                                                                                                                                                                                                                                                                                                                            <td>
                                                                                                                                                                                                                                                                                                                                                                                                                <a href="/admin/uploads/${f.file_path}"
                                                                                                                                                                                                                                                                                                                                                                                                                   target="_blank"
                                                                                                                                                                                                                                                                                                                                                                                                                   class="btn btn-sm btn-info">
                                                                                                                                                                                                                                                                                                                                                                                                                    Download
                                                                                                                                                                                                                                                                                                                                                                                                                </a>
                                                                                                                                                                                                                                                                                                                                                                                                            </td>

                                                                                                                                                                                                                                                                                                                                                                                                            <td class="text-center">
                                                                                                                                                                                                                                                                                                                                                                                                                <input type="checkbox" name="deleted_files[]" value="${f.id}">
                                                                                                                                                                                                                                                                                                                                                                                                                <input type="hidden" name="existing_file_ids[]" value="${f.id}">
                                                                                                                                                                                                                                                                                                                                                                                                            </td>

                                                                                                                                                                                                                                                                                                                                                                                                            <td>
                                                                                                                                                                                                                                                                                                                                                                                                                <input type="file" name="replace_file_${f.id}" class="form-control-file">
                                                                                                                                                                                                                                                                                                                                                                                                                <input type="hidden" name="replace_file_ids[]" value="${f.id}">
                                                                                                                                                                                                                                                                                                                                                                                                            </td>
                                                                                                                                                                                                                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                                                                                                                                                                                                                    `;
                        });

                        html += `</tbody></table>`;

                        $('#quotation_files').html(html);

                    } else {
                        $('#quotation_files').html('');
                    }




                    /* ================= CUSTOMER ================= */
                    $('#customer_name').val(q.customer_name);
                    $('#email').val(q.email);

                    const phoneCode = q.country?.phonecode ?? '';

                    // COUNTRY CODE
                    $('#country_code_select')
                        .empty()
                        .append(`<option value="${phoneCode}">+${phoneCode}</option>`)
                        .val(phoneCode);

                    // PRIMARY MOBILE
                    $('#mobile').val(q.phones?.[0] ?? '');

                    // EXTRA MOBILES
                    let mobilesHtml = '';
                    if (Array.isArray(q.phones) && q.phones.length) {
                        mobilesHtml = `
                                                                                                                                                                                                                                                                                            <ul class="list-group list-group-sm mt-1">
                                                                                                                                                                                                                                                                                                ${q.phones.map((phone, index) => `
                                                                                                                                                                                                                                                                                                    <li class="list-group-item d-flex justify-content-between py-1">
                                                                                                                                                                                                                                                                                                        <span>
                                                                                                                                                                                                                                                                                                            <i class="fa fa-phone text-success mr-1"></i>
                                                                                                                                                                                                                                                                                                            +${phoneCode} ${phone}
                                                                                                                                                                                                                                                                                                        </span>
                                                                                                                                                                                                                                                                                                        <span class="badge ${index === 0 ? 'badge-success' : 'badge-secondary'}">
                                                                                                                                                                                                                                                                                                            ${index === 0 ? 'Primary' : 'Alt'}
                                                                                                                                                                                                                                                                                                        </span>
                                                                                                                                                                                                                                                                                                    </li>
                                                                                                                                                                                                                                                                                                `).join('')}
                                                                                                                                                                                                                                                                                            </ul>
                                                                                                                                                                                                                                                                                        `;
                    }
                    $('#extra-mobiles').html(mobilesHtml);
                    $('#customer_gst').val(q.customer_gst);
                    $('#contact_person').val(q.contact_person);
                    $('#office_address').val(q.office_address);
                    $('#delivery_address').val(q.delivery_address);
                    $('input[name="country"]').val(q.country?.name ?? '');

                    /* ================= LEAD ================= */
                    if (q.lead) {

                        $('#lead_id').val(q.lead.id);

                        // NEW: generate Order + PO preview
                        $.get("{{ route('ajax.generate.order.number') }}", {
                            company_id: $('#company_id').val(),
                            lead_id: q.lead.id
                        }, function (res) {

                            $('#order_number').val(res.order);
                            $('#po_number').val(res.po);

                            if (res.today) {
                                orderFp.setDate(res.today, true);
                                poFp.setDate(res.today, true);
                            }

                        });

                    }


                    /* ================= TOTALS ================= */
                    $('input[name="discount"]').val(q.discount);
                    $('input[name="tax"]').val(q.tax);

                    /* ================= TERMS ================= */
                    // Order Remark ← quotation special clause
                    $('textarea[name="remark"]').summernote('code', q.special_clause ?? '');
                    $('textarea[name="hi_remark"]').summernote('code', q.hi_special_clause ?? '');

                    // Order Terms & Conditions ← quotation terms_conditions
                    $('textarea[name="terms_conditions"]').summernote('code', q.terms_conditions ?? '');
                    $('textarea[name="hi_terms_conditions"]').summernote('code', q.hi_terms_conditions ?? '');



                    /* ================= ITEMS (🔥 FIXED) ================= */
                    $('#item-body').empty();

                    q.items.forEach(it => {

                        let item = it.machine ?? it.component;
                        let type = it.machine ? 'machine' : 'component';

                        let row = $(`
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <tr class="item_row">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <input type="hidden" class="saved_item_id" value="${item.id}">
                                                                                                                                                                                                                                                                <td class="text-center align-middle drag-handle" style="cursor: grab;">
                                                                                                                                                                                                                                                                    <i class="fa fa-bars text-muted"></i>
                                                                                                                                                                                                                                                                    <input type="hidden" name="sort_order[]" class="sort_order" value="1">
                                                                                                                                                                                                                                                                </td>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <select name="item_type[]" class="form-control item_type">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <option value="machine">Machine</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <option value="component">Component</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </select>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </td>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <select name="origin[]" class="form-control origin">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <option value="self">Self</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <option value="outsource">Outsource</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </select>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </td>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <select name="item_id[]" class="form-control item_select"></select>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 <td>
                                                                                <input type="text" name="item_name_hindi[]"
                                                                                    class="form-control item_name_hi">
                                                                            </td>

                                                                                                                                                                 <td>
                                       <div class="d-flex gap-2 align-items-start">

                    ${(() => {

                                // =========================
                                // DESCRIPTION PRIORITY
                                // =========================

                                let finalHtml = '';

                                if (it.description && it.description.trim() !== '') {
                                    finalHtml = it.description;
                                } else if (item.description && item.description.trim() !== '') {
                                    finalHtml = item.description;
                                }

                                let finalHiHtml = '';

                                if (it.hi_description && it.hi_description.trim() !== '') {
                                    finalHiHtml = it.hi_description;
                                } else if (item.hi_description && item.hi_description.trim() !== '') {
                                    finalHiHtml = item.hi_description;
                                }

                                // CLEAN HTML
                                finalHtml = finalHtml.replace(/["'>]+$/, '');
                                finalHiHtml = finalHiHtml.replace(/["'>]+$/, '');

                                // HTML → TEXT
                                const plainText = $('<div>').html(finalHtml).text().trim();

                                // SHORT PREVIEW
                                const shortText = plainText.length > 120
                                    ? plainText.substring(0, 120) + '...'
                                    : plainText;

                                return `

                            <!-- SHORT DESCRIPTION -->
                            <textarea
                                class="form-control description"
                                rows="2"
                                readonly
                                style="min-width:250px; resize:none;">${shortText}</textarea>

                            <!-- FULL TEXT -->
                            <input type="hidden"
                                   class="full_description_text"
                                   value="${encodeURIComponent(plainText)}">

                            <!-- FULL HTML -->
                            <input type="hidden"
                                   name="description_html[]"
                                   class="description_html"
                                   value="${finalHtml}">

                            <!-- HINDI HTML -->
                            <input type="hidden"
                                   name="description_hi_html[]"
                                   class="description_hi_html"
                                   value="${finalHiHtml}">
                        `;
                            })()}

                    <!-- EDIT BUTTON -->
                    <button type="button"
                            class="btn btn-sm btn-outline-success edit-description">
                        <i class="fa fa-edit"></i>
                    </button>

                </div>
                                    </td>


                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <input type="text" name="quantity[]" class="form-control qty" value="${it.quantity}">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </td>

                                                                        <td class="price-col">
                                                                            <input type="text"  name="unit_price[]" class="form-control price_input" value="${it.converted_unit_price ?? it.unit_price}">
                                                                        </td>

                                                                        <td class="total-col">
                                                                            <input type="text" name="total[]" class="form-control total_selected" readonly>
                                                                        </td>

                                                                        <td class="cfv-col d-none">
                                                                            <input type="text" name="converted_total_price[]" class="form-control total_inr" readonly>
                                                                        </td>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <button type="button" class="btn btn-danger remove_row">X</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        `);

                        row.find('.item_type').val(type);
                        let origin = (item.origin || 'self').toLowerCase();
                        row.find('.origin').val(origin);


                        $('#item-body').append(row);

                        initRow(row);
                        calculateRow(row);
                    });
                    updateCurrencyUI(q.currency);
                    updateHeaders(q.currency);

                    // 🔥 THIS IS THE FIX
                    applyCurrencyUIToTable(q.currency);

                    recalculateAll();
                    calculateSummary();
                }
            );
        }); $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });

    </script>
    <script>
        new Sortable(document.querySelector('#quotation_items_table tbody'), {
            handle: '.drag-handle',
            animation: 150,
            onEnd: updateSortOrder
        });

        function updateSortOrder() {
            $('#quotation_items_table tbody tr').each(function (i) {
                $(this).find('.sort_order').val(i + 1);
            });
        }
    </script>
    <script>
        $(document).ready(function () {

            /* ================= SELECT2 ================= */
            $('#quotation_id').select2({
                placeholder: 'Search quotation...',
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('ajax.quotations.search', ['company' => $company->id]) }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { search: params.term };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(q => ({
                                id: q.id,
                                text: q.quote_number + ' - ' + q.customer_name + ' (' + q.mobile + ')'
                            }))
                        };
                    }
                }
            });

            /* ================= AUTO SELECT FROM URL ================= */
            const urlParams = new URLSearchParams(window.location.search);
            const quotationId = urlParams.get('quotation');

            if (quotationId) {
                // Fetch quotation once and select it
                $.get(
                    "{{ route('ajax.quotation.for.order', ['company' => $company->id]) }}",
                    { id: quotationId },
                    function (q) {

                        // Inject option into Select2
                        let option = new Option(
                            q.quote_number + ' - ' + q.customer_name + ' (' + q.mobile + ')',
                            q.id,
                            true,
                            true
                        );

                        $('#quotation_id').append(option).trigger('change');
                    }
                );
            }

        });
    </script>
    <script>
        let activeRow = null;

        // OPEN MODAL
        $(document).on('click', '.edit-description', function () {

            activeRow = $(this).closest('tr');

            // ENGLISH DESCRIPTION
            let enHtml = activeRow.find('.description_html').val() || '';
            let hiHtml = activeRow.find('.description_hi_html').val() || '';

            // FALLBACK FROM MASTER ITEM
            if (!enHtml.trim()) {

                let selected = activeRow.find('.item_select option:selected');

                enHtml = selected.data('description') || '';
            }

            // SET SUMMERNOTE
            $('#modalDescriptionEn').summernote('code', enHtml);
            $('#modalDescriptionHi').summernote('code', hiHtml);

            $('#descriptionModal').modal('show');
        });

        // Save back to row
        $('#saveDescription').on('click', function () {

            if (!activeRow) return;

            // GET HTML
            let enHtml = $('#modalDescriptionEn').summernote('code');
            let hiHtml = $('#modalDescriptionHi').summernote('code');

            // CONVERT TO PLAIN TEXT
            let enText = $('<div>').html(enHtml).text().trim();

            // SHOW IN TABLE
            activeRow.find('.description').val(enText);

            // STORE HTML
            activeRow.find('.description_html').val(
                encodeURIComponent(enHtml)
            );

            activeRow.find('.description_hi_html').val(
                encodeURIComponent(hiHtml)
            );
            $('#descriptionModal').modal('hide');
        });

        // 🔵 ROUND OFF TAX AMOUNT
        $(document).on('click', '#round_tax', function () {

            let taxAmount = parseFloat($('#tax_amount').val()) || 0;

            // Round to nearest integer
            let roundedTax = Math.round(taxAmount);

            // Set rounded tax
            $('#tax_amount').val(roundedTax.toFixed(2));

            // 🔁 Recalculate final total using rounded tax
            let subtotal = parseFloat($('#subtotal').val())
                || parseFloat($('input[name="total_amount"]').val())
                || 0;

            let discount = parseFloat($('#discount').val())
                || parseFloat($('input[name="discount"]').val())
                || 0;

            let afterDiscount = subtotal - discount;
            if (afterDiscount < 0) afterDiscount = 0;

            let finalTotal = afterDiscount + roundedTax;

            // Order / Quotation compatible
            $('#final_total, input[name="final_amount"]').val(finalTotal.toFixed(2));

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Tax rounded off',
                showConfirmButton: false,
                timer: 1200
            });
        });
        $(document).on('input', '#tax_amount', function () {

            let taxAmount = parseFloat($(this).val()) || 0;

            let subtotal = parseFloat($('#subtotal').val())
                || parseFloat($('input[name="total_amount"]').val())
                || 0;

            let discount = parseFloat($('#discount').val())
                || parseFloat($('input[name="discount"]').val())
                || 0;

            let afterDiscount = subtotal - discount;
            if (afterDiscount < 0) afterDiscount = 0;

            let finalTotal = afterDiscount + taxAmount;

            $('#final_total, input[name="final_amount"]').val(finalTotal.toFixed(2));
        });

    </script>
    <script>
        let customerUpdateDecisionTaken = false;

        // All customer-related fields
        const customerFields = [
            '#customer_name',
            '#email',
            '#mobile',
            '#customer_gst',
            '#contact_person',
            '#office_address',
            '#delivery_address'
        ];

        // Attach listener
        $(document).on('input change', customerFields.join(','), function () {

            // Already decided once → don't ask again
            if (customerUpdateDecisionTaken) return;

            customerUpdateDecisionTaken = true;

            Swal.fire({
                title: 'Update Customer?',
                text: 'You are editing customer details. Do you want to update the main customer record as well?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, update customer',
                cancelButtonText: 'No, only this order',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
            }).then((result) => {

                if (result.isConfirmed) {
                    $('#update_customer_master').val('1');

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Customer master will be updated',
                        showConfirmButton: false,
                        timer: 1500
                    });

                } else {
                    $('#update_customer_master').val('0');

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: 'Only order snapshot will be saved',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const fileInput = document.getElementById('fileInput');
            const previewContainer = document.getElementById('filePreview');

            if (!fileInput || !previewContainer) return; // safety

            let selectedFiles = [];

            fileInput.addEventListener('change', function () {
                selectedFiles = Array.from(fileInput.files);
                renderPreviews();
            });

            function renderPreviews() {
                previewContainer.innerHTML = '';

                selectedFiles.forEach((file, index) => {

                    // OPTIONAL size limit (10MB)
                    if (file.size > 10 * 1024 * 1024) {
                        Swal.fire('File too large', file.name + ' exceeds 10MB', 'error');
                        return;
                    }

                    const col = document.createElement('div');
                    col.className = 'col-md-3 mb-2';

                    const preview = document.createElement('div');
                    preview.className = 'file-preview';

                    // REMOVE BUTTON
                    const removeBtn = document.createElement('span');
                    removeBtn.className = 'remove-file';
                    removeBtn.innerHTML = '&times;';
                    removeBtn.onclick = function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        removeFile(index);
                    };

                    const url = URL.createObjectURL(file);

                    // IMAGE
                    if (file.type.startsWith('image/')) {
                        const link = document.createElement('a');
                        link.href = url;
                        link.target = '_blank';

                        const img = document.createElement('img');
                        img.src = url;

                        link.appendChild(img);
                        preview.appendChild(link);
                    }
                    // PDF / DOC
                    else {
                        const link = document.createElement('a');
                        link.href = url;
                        link.target = '_blank';
                        link.className = 'text-decoration-none';

                        link.innerHTML = `
                                                                                                                                                                                                                                    <i class="fa fa-file-alt fa-3x text-secondary"></i>
                                                                                                                                                                                                                                    <p class="small mt-1 text-break">${file.name}</p>
                                                                                                                                                                                                                                `;

                        preview.appendChild(link);
                    }

                    preview.appendChild(removeBtn);
                    col.appendChild(preview);
                    previewContainer.appendChild(col);
                });
            }

            function removeFile(index) {
                selectedFiles.splice(index, 1);

                const dt = new DataTransfer();
                selectedFiles.forEach(file => dt.items.add(file));

                fileInput.files = dt.files;
                renderPreviews();
            }
        });
    </script>
    <script>
        function isHindi(text) {
            return /[\u0900-\u097F]/.test(text);
        }

        function translateText(text, callback) {

            if (!text) return;

            const hindi = isHindi(text);

            $.ajax({
                url: "{{ route('translate.text') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    text: text,
                    source: hindi ? 'hi' : 'en',
                    target: hindi ? 'en' : 'hi'
                },
                success: function (res) {
                    callback(res.translated);
                },
                error: function () {
                    Swal.fire("Error", "Translation failed", "error");
                }
            });
        }

        /* ===========================
           SUMMERNOTE TRANSLATION
        =========================== */
        $(document).on('click', '.translate-btn', async function () {

            const btn = $(this);
            const targetId = btn.data('target');
            const editor = $('#' + targetId);
            const html = editor.summernote('code');

            if (!html.trim()) return;

            btn.prop('disabled', true);
            $('#translateLoader').css('display', 'flex');

            let tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;

            async function translateNode(node) {

                if (node.nodeType === 3) { // TEXT NODE

                    const text = node.nodeValue.trim();

                    if (text.length > 0) {

                        const translated = await new Promise((resolve) => {
                            translateText(text, function (result) {
                                resolve(result);
                            });
                        });

                        node.nodeValue = translated;
                    }

                } else {
                    for (let child of node.childNodes) {
                        await translateNode(child);
                    }
                }
            }

            await translateNode(tempDiv);

            editor.summernote('code', tempDiv.innerHTML);

            $('#translateLoader').hide();
            btn.prop('disabled', false);
        });


        /* ===========================
           DESCRIPTION TRANSLATION
        =========================== */
        $(document).on('click', '.translate-description', async function () {

            const btn = $(this);
            const row = btn.closest('tr');
            const descField = row.find('.description');
            const text = descField.val();

            if (!text.trim()) return;

            btn.prop('disabled', true);
            $('#translateLoader').css('display', 'flex');

            const translated = await new Promise((resolve) => {
                translateText(text, function (result) {
                    resolve(result);
                });
            });

            descField.val(translated);
            descField.attr('data-user-edited', '1');

            $('#translateLoader').hide();
            btn.prop('disabled', false);
        });
    </script>
    <script>
        let isAutoCurrencySet = false;
        $('#currency').on('change', function () {

            let currency = $(this).val();

            updateCurrencyUI(currency);
            updateHeaders(currency);
            applyCurrencyUIToTable(currency);

            // ✅ CASE 1: INR
            if (currency === 'INR') {

                $('#conversion_rate').val(1).prop('readonly', true);

                $('.inr-col').removeClass('d-none');
                $('.converted-col').addClass('d-none');

                recalculateAll();
                return;
            }

            // ✅ CASE 2: AUTO LOAD (FROM BACKEND)
            if (isAutoCurrencySet) {

                $('.converted-col').removeClass('d-none');

                recalculateAll();
                return;
            }

            // ✅ CASE 3: MANUAL CHANGE → SHOW MODAL
            $('#conversion_rate').val('');
            $('#modal_rate').val('');

            $('#currencyModal').modal('show');
        });
        $(document).on('input', '.unit_price', function () {
            let row = $(this).closest('tr');
            calculateRow(row, 'inr');
            calculateSummary();
        });

        // Converted change
        $(document).on('input', '.converted_price', function () {
            let row = $(this).closest('tr');
            calculateRow(row, 'converted');
            calculateSummary();
        });

        // Quantity change
        $(document).on('input', '.qty', function () {
            let row = $(this).closest('tr');
            calculateRow(row);
            calculateSummary();
        });
        function getCurrencySymbol(currency) {
            switch (currency) {
                case 'USD': return '$';
                case 'EUR': return '€';
                default: return '₹';
            }
        } function updateCurrencyUI(currency) {

            let symbol = getCurrencySymbol(currency);

            $('.currency-label').text(symbol);
        }
        $('#save_rate').on('click', function () {

            let rate = parseFloat($('#modal_rate').val());

            if (!rate || rate <= 0) {
                alert('Enter valid rate');
                return;
            }

            $('#conversion_rate').val(rate);

            $('.converted-col').removeClass('d-none');

            recalculateAll();

            $('#currencyModal').modal('hide');
        }); function recalculateAll() {
            $('.item_row').each(function () {
                calculateRow($(this));
            });

            calculateSummary();
        } function updateHeaders(currency) {

            if (currency === 'INR') {
                $('.cfv-header').text('Total');
            } else {
                $('.cfv-header').text('CFV');
            }
        }
        function applyCurrencyUIToTable(currency) {

            if (currency === 'INR') {
                $('.cfv-col').addClass('d-none');   // hide CFV
            } else {
                $('.cfv-col').removeClass('d-none'); // show CFV
            }
        }

    </script>
@endpush