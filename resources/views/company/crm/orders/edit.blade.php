@extends('company.layouts.master')

@section('content')
    @php
        $customer = optional($order->quotation?->lead?->customer);
        $country = optional($customer->country);
        $phones = $customer->phones ?? collect();
        $primaryPhone = $phones->firstWhere('is_primary', 1);
    @endphp

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Order</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('company.dashboard', ['company' => $company->id]) }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('orders.index', ['company' => $company->id]) }}">Order List</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Order</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <form method="POST" action="{{ route('orders.update', [$company->id, $order->id]) }}" autocomplete="off"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

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

                        {{-- ================= 1. LEAD & QUOTATION ================= --}}
                        <h4 class="text-primary"><b>1. Lead & Quotation</b></h4>

                        <div class="row">
                            <div class="col-md-3">
                                <label>Quotation</label>
                                <input type="text" class="form-control" value="{{ $order->quotation->quote_number ?? '-' }}"
                                    readonly>
                            </div>
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

                        {{-- ================= 2. ORDER DETAILS ================= --}}
                        <h4 class="text-primary"><b>2. Order Details</b></h4>
                        <div class="mobile-scroll">
                            <div class="row flex-nowrap">

                                <div class="row">
                                    <div class="col">
                                        <label>Order Number</label>
                                        <input type="text" class="form-control" value="{{ $order->order_number }}" readonly>
                                    </div>

                                    <div class="col">
                                        <label>Order Date *</label>
                                        <div class="input-group">
                                            <input type="text" name="order_date" id="order_date"
                                                class="form-control datepicker" placeholder="DD/MM/YYYY"
                                                value="{{ optional($order->order_date)->format('d/m/Y') }}" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                                        </div>


                                    </div>

                                    <div class="col">
                                        <label>PO (No.)</label>
                                        <input type="text" class="form-control" value="{{ $order->po_number }}">
                                    </div>

                                    <div class="col">
                                        <label>PO Date</label>
                                        <div class="input-group">
                                            <input type="text" name="po_date" id="po_date" class="form-control datepicker"
                                                placeholder="DD/MM/YYYY"
                                                value="{{ optional($order->po_date)->format('d/m/Y') }}">
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
                                                value="{{ optional($order->delivery_date)->format('d/m/Y') }}" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col">
                                        <label>Order Status</label>
                                        <select name="status" class="form-control">
                                            @foreach($statuses as $value => $label)
                                                <option value="{{ $value }}" {{ $order->status == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted d-block">
                                            (only previous or already delayed orders)
                                        </small>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <hr>

                        {{-- ================= 3. CUSTOMER DETAILS ================= --}}
                        <h4 class="text-primary"><b>3. Customer Details</b></h4>

                        <div class="row">
                            <div class="col-md-4">
                                <label>Customer Name</label>
                                <input type="text" name="customer_name" class="form-control"
                                    value="{{ $order->customer_name ?? $customer->name }}">
                            </div>

                            <div class="col-md-4">
                                <label>Contact Person</label>
                                <input type="text" name="contact_person" class="form-control"
                                    value="{{ $order->contact_person }}">
                            </div>

                            <div class="col-md-4">
                                <label>Customer GST</label>
                                <input type="text" name="customer_gst" class="form-control"
                                    value="{{ $order->customer_gst ?? $customer->gst }}">
                            </div>

                            <div class="col-md-4 mt-2">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ $order->email ?? $customer->email }}">
                            </div>

                            <div class="col-md-4 mt-2">
                                <label>Mobile</label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <select id="country_code_select" class="form-control" style="max-width:80px">
                                            @if($country)
                                                <option value="{{ $country->phonecode }}" selected>
                                                    +{{ $country->phonecode }}
                                                </option>
                                            @else
                                                <option value="">+--</option>
                                            @endif
                                        </select>
                                    </div>

                                    <input type="text" id="mobile" maxlength="10" name="mobile" class="form-control"
                                        value="{{ $order->mobile ?? optional($primaryPhone)->phone }}"
                                        placeholder="Enter mobile number">
                                </div>

                                {{-- Extra mobiles --}}
                                <div id="extra-mobiles" class="mt-1 text-muted small">
                                    @foreach($phones as $phone)
                                        <div>
                                            <i class="fa fa-phone text-success"></i>
                                            +{{ $country->phonecode ?? '' }} {{ $phone->phone }}
                                            @if($phone->is_primary)
                                                <span class="badge badge-success ml-1">Primary</span>
                                            @else
                                                <span class="badge badge-secondary ml-1">Alt</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>


                            <div class="col-md-4 mt-2">
                                <label>Country</label>
                                <input type="text" name="country" class="form-control" value="{{ $country->name ?? '-' }}"
                                    readonly>

                            </div>

                            <div class="col-md-6 mt-2">
                                <label>Office Address</label>
                                <textarea name="office_address"
                                    class="form-control">{{ $order->office_address ?? $customer->address }}</textarea>

                            </div>

                            <div class="col-md-6 mt-2">
                                <label>Delivery Address</label>
                                <textarea name="delivery_address"
                                    class="form-control">{{ $order->delivery_address }}</textarea>
                            </div>
                        </div>

                        <hr>
                        <h4 class="text-primary"><b>4. Currency & Rate of conversion</b></h4>

                        <div class="row">
                            <div class="col">
                                <label>Currency *</label>
                                <select name="currency" id="currency" class="form-control" required>
                                    <option value="INR" {{ $order->currency == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                                    <option value="USD" {{ $order->currency == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="EUR" {{ $order->currency == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                </select>
                            </div>

                            <div class="col">
                                <label>Conversion Rate</label>
                                <input type="text" step="0.01" name="conversion_rate" id="conversion_rate"
                                    class="form-control" readonly value="{{ $order->conversion_rate }}">
                            </div>
                        </div>
                        <hr>
                        {{-- ================= 4. ORDER ITEMS (SAME AS ADD) ================= --}}
                        <h4 class="text-primary"><b>4. Order Items</b></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="quotation_items_table">
                                <thead>
                                    <tr>
                                        <th style="width:30px">↕</th>
                                        <th>Type</th>
                                        <th>Origin</th>
                                        <th>Item Name(English)</th>
                                        <th>Item Name(Hindi)</th>
                                        <th>Description</th>
                                        <th>Qty</th>
                                        <th class="price-col">Price (<span class="currency-label">₹</span>)</th>
                                        <th class="total-col">Total (<span class="currency-label">₹</span>)</th>
                                        <th class="cfv-col d-none">CFV (₹)</th>
                                        <th>
                                            <button type="button" class="btn btn-success" id="add_row">+</button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="item-body">

                                    @foreach($order->items as $item)
                                        <tr class="item_row">

                                            {{-- Existing row ID (important for update/delete) --}}
                                            <input type="hidden" name="row_item_id[]" value="{{ $item->id }}">
                                            <input type="hidden" name="sort_order[]" class="sort_order"
                                                value="{{ $item->sort_order }}">


                                            @php
                                                $isMachine = !is_null($item->machine_id);
                                                $itemModel = $isMachine ? $item->machine : $item->component;
                                            @endphp
                                            <td class="drag-handle text-center" style="cursor:grab">
                                                <i class="fa fa-bars"></i>
                                            </td>

                                            <td>
                                                <select name="item_type[]" class="form-control item_type">
                                                    <option value="machine" {{ $isMachine ? 'selected' : '' }}>Machine</option>
                                                    <option value="component" {{ !$isMachine ? 'selected' : '' }}>Component
                                                    </option>
                                                </select>
                                            </td>


                                            <td>
                                                <select name="origin[]" class="form-control origin">
                                                    <option value="{{ strtolower($itemModel->origin) }}" selected>
                                                        {{ ucfirst($itemModel->origin) }}
                                                    </option>
                                                </select>
                                            </td>


                                            @php
                                                $isMachine = !is_null($item->machine_id);
                                                $itemModel = $isMachine ? $item->machine : $item->component;
                                                $itemType = $isMachine ? 'machine' : 'component';
                                            @endphp

                                            <td>
                                                <input type="hidden" class="saved_item_id" value="{{ $itemModel->id }}">

                                                <select name="item_id[]" class="form-control item_select">
                                                    <option value="{{ $itemModel->id }}" selected>
                                                        {{ $itemModel->name }}
                                                    </option>
                                                </select>
                                            </td>

                                            <td><input type="text" name="item_name_hindi[]" class="form-control item_name_hi"
                                                    value="{{ $item->hi_name ?? '' }}"></td>

                                            <td>
                                                <div class="d-flex gap-2">
                                                    <input type="text" class="form-control description" readonly
                                                        value="{{ strip_tags($item->description) }}">

                                                    <input type="hidden" name="description_html[]" class="description_html"
                                                        value="{{ $item->description }}">
                                                    <input type="hidden" name="description_hi_html[]"
                                                        class="description_hi_html" value="{{ $item->hi_description }}">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-success edit-description"
                                                        title="Edit Description">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    </button>
                                                </div>
                                            </td>


                                            <td>
                                                <input type="text" name="quantity[]" class="form-control qty"
                                                    value="{{ $item->quantity }}">
                                            </td>


                                            <td class="price-col">
                                                <input type="text" name="unit_price[]" class="form-control price_input"
                                                    value="{{ $item->unit_price }}">
                                            </td>

                                            <td class="total-col">
                                                <input type="text" name="total[]" class="form-control total_selected"
                                                    value="{{ $item->total_price }}" readonly>
                                            </td>

                                            <td class="cfv-col d-none">
                                                <input type="text" name="converted_total_price[]" class="form-control total_inr"
                                                    readonly>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger remove_row">X</button>
                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>
                        </div>
                        <div id="deleted-items-wrapper"></div>

                        {{-- MASTER ITEMS (same as create) --}}
                        <select id="master_items" style="display:none;">
                            @foreach($machines as $m)
                                <option value="{{ $m->id }}" data-type="machine" data-origin="{{ strtolower($m->origin) }}"
                                    data-description="{{ $m->description }}" data-hi_description="{{ $m->hi_description }}"
                                    data-hindi_name="{{ $m->hi_name }}">
                                    {{ $m->name }}
                                </option>
                            @endforeach
                            @foreach($components as $c)
                                <option value="{{ $c->id }}" data-type="component" data-origin="{{ strtolower($c->origin) }}"
                                    data-description="{{ $c->description }}" data-hi_description="{{ $c->hi_description }}"
                                    data-hindi_name="{{ $c->hi_name }}">
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>

                        <hr>

                        {{-- ================= 5. TOTALS ================= --}}
                        <div class="totals-scroll">
                            <div class="row flex-nowrap totals-row">
                                <div class="col">
                                    <label>Sub Total</label>
                                    <input type="text" name="total_amount" class="form-control" readonly
                                        value="{{ $order->total_amount }}">
                                </div>
                                <div class="col">
                                    <label>Discount(amt.)</label>
                                    <input type="text" name="discount" class="form-control" value="{{ $order->discount }}">
                                </div>
                                <div class="col">
                                    <label>Tax %</label>
                                    <input type="text" name="tax" class="form-control" value="{{ $order->tax }}">
                                </div>
                                <div class="col">
                                    <label>Tax (amount)</label>
                                    <div class="input-group">
                                        <input type="text" name="tax_amount" id="tax_amount" class="form-control"
                                            step="0.01" value="{{ $order->tax_amount }}">

                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-success" id="round_tax">
                                                Round
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col">
                                    <label>Final Amount</label>
                                    <input type="text" step="0.01" name="final_amount" class="form-control" readonly
                                        value="{{ $order->final_amount }}">
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- ================= 6. FILES & TERMS (SAME AS CREATE) ================= --}}
                        <h4 class="text-primary"><b>5. Files & Terms</b></h4>

                        @if($order->files->count())
                            <table class="table table-bordered table-sm">
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
                                    @foreach($order->files as $i => $f)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $f->file_name }}</td>
                                            <td>
                                                <a href="/admin/uploads/{{ $f->file_path }}" target="_blank"
                                                    class="btn btn-sm btn-info">Download</a>
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" name="deleted_files[]" value="{{ $f->id }}">
                                            </td>
                                            <td>
                                                <input type="file" name="replace_file_{{ $f->id }}">
                                                <input type="hidden" name="replace_file_ids[]" value="{{ $f->id }}">
                                            </td><input type="hidden" name="row_item_id[]" value="">
                                            <input type="hidden" class="saved_item_id" value="">
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <input type="file" name="uploads[]" class="form-control" multiple>
                            </div>
                            <div class="col-md-4">
                                <textarea name="remark" id="remark"
                                    class="form-control summernote">{{ $order->remark }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea name="hi_remark" id="hi_remark"
                                    class="form-control summernote">{{ $order->hi_remark }}</textarea>
                            </div>
                        </div>
                        <h4 class="text-primary"><b>6. Terms & Conditions</b></h4>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <textarea name="terms_conditions" id="terms_conditions"
                                    class="form-control summernote">{{ $order->terms_conditions }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <textarea name="hi_terms_conditions" id="hi_terms_conditions"
                                    class="form-control summernote">{{ $order->hi_terms_conditions }}</textarea>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer text-right">
                        <button class="btn btn-success">
                            <i class="fa fa-save"></i> Update Order
                        </button>
                        <a href="{{ route('orders.index', $company->id) }}" class="btn btn-secondary">Cancel</a>
                    </div>


                </div>
            </form>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
        $('.description').each(function () {

            let html = $(this).closest('tr').find('.description_html').val();

            if (!html) html = '';

            let text = $('<div>').html(html).text();

            $(this).val(text);
        });
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
        let MASTER = [];

        $(document).ready(function () {
            updateHeaders($('#currency').val());
            $('#master_items option').each(function () {
                MASTER.push($(this).clone());
            });
        });
        function applyFilter(row) {
            let type = row.find('.item_type').val();
            let origin = row.find('.origin').val();
            let select = row.find('.item_select');

            if (select.hasClass('select2-hidden-accessible')) {
                select.select2('destroy');
            }

            select.empty().append('<option value="">Select Item</option>');

            if (!type || !origin) return;

            MASTER.forEach(opt => {
                // Ensure origin comparison is case-insensitive if necessary
                if ($(opt).data('type') === type && $(opt).data('origin').toString().toLowerCase() === origin.toLowerCase()) {
                    select.append($(opt).clone());
                }
            });

            select.select2({ width: '100%' });
        }
        function initRow(row) {
            let select = row.find('.item_select');
            let savedItemId = row.find('.saved_item_id').val();

            // 1. Fill the dropdown options based on Type and Origin
            applyFilter(row);

            // 2. Pre-select the item if it exists
            if (savedItemId) {
                select.val(savedItemId).trigger('change');
            }
            let descField = row.find('.description');

            if (!descField.val().trim()) {
                descField.val(
                    select.find(':selected').data('description') || ''
                );
            }
            // 3. Force calculation for existing rows
            calculateRow(row);
        }

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
            }
            calculateSummary();
        });
        $(document).on('input', 'input[name="tax"]', function () {
            calculateSummary();
        });

        $(document).on('change', '.item_type, .origin', function () {
            applyFilter($(this).closest('tr'));
        });

        $(document).on('change', '.item_select', function () {
            let row = $(this).closest('tr');

            let selected = $(this).find(':selected');

            // HINDI NAME
            row.find('.item_name_hi').val(
                selected.data('hindi_name') || ''
            );

            // ENGLISH DESCRIPTION
            let enDescription =
                selected.data('description') || '';

            // HINDI DESCRIPTION
            let hiDescription =
                selected.data('hi_description') || '';

            // SET ONLY IF EMPTY
            if (!row.find('.description_html').val()?.trim()) {

                row.find('.description').val(
                    $('<div>').html(enDescription).text()
                );

                row.find('.description_html').val(enDescription);
            }

            if (!row.find('.description_hi_html').val()?.trim()) {

                row.find('.description_hi_html').val(hiDescription);
            }

            calculateRow(row);
            calculateSummary();
        });


        $(document).on('input', '.qty, .price_input', function () {
            let row = $(this).closest('tr');
            calculateRow(row);
            calculateSummary();
        });
    </script>
    <script>
        $('#add_row').on('click', function () {

            let row = $(`
                                                                                                                        <tr class="item_row">
                                                                                                                            <input type="hidden" class="saved_item_id" value="">
                                    <input type="hidden" name="sort_order[]" class="sort_order">
                                    <td class="drag-handle text-center" style="cursor:grab">
                                        <i class="fa fa-bars"></i>
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
                    <input type="text"
                           name="item_name_hindi[]"
                           class="form-control item_name_hi"
                           readonly>
                </td>
                                                                                                                           <td>
                                        <div class="d-flex gap-2">
                                            <input type="text" name="description[]" class="form-control description" readonly>

                                            <button type="button"
                                                    class="btn btn-sm btn-outline-success edit-description"
                                                    title="Edit Description">
                                                <i class="fa fa-edit"></i>
                                        </div>
                                    </td>


                                                                                                                            <td>
                                                                                                                                <input type="text" name="quantity[]" class="form-control qty" value="1" required>
                                                                                                                            </td>

                                                                                                                           <td class="price-col">
                                        <input type="text" name="unit_price[]" class="form-control price_input" value="0">
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

            // default values
            row.find('.item_type').val('machine');
            row.find('.origin').val('self');
            let index = $('#item-body tr').length;
            row.find('.sort_order').val(index);
            initRow(row);
            applyCurrencyUIToTable($('#currency').val());
            recalculateAll();
        });
    </script>
    <script>
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
        let activeRow = null;

        // OPEN MODAL
        $(document).on('click', '.edit-description', function () {

            activeRow = $(this).closest('tr');

            let selected =
                activeRow.find('.item_select option:selected');

            // ENGLISH DESCRIPTION
            let enHtml =
                activeRow.find('.description_html').val()
                || selected.data('description')
                || '';

            // HINDI DESCRIPTION
            let hiHtml =
                activeRow.find('.description_hi_html').val()
                || selected.data('hi_description')
                || '';

            // SET EDITORS
            $('#modalDescriptionEn').summernote('code', enHtml);

            $('#modalDescriptionHi').summernote('code', hiHtml);

            $('#descriptionModal').modal('show');
        });


        // SAVE
        $('#saveDescription').on('click', function () {

            if (!activeRow) return;

            let enHtml =
                $('#modalDescriptionEn').summernote('code');

            let hiHtml =
                $('#modalDescriptionHi').summernote('code');

            let plainText =
                $('<div>').html(enHtml).text().trim();

            // TABLE DISPLAY
            activeRow.find('.description').val(plainText);

            // SAVE HTML
            activeRow.find('.description_html').val(enHtml);

            activeRow.find('.description_hi_html').val(hiHtml);

            $('#descriptionModal').modal('hide');
        });
        $(document).ready(function () {
            $('#item-body .item_row').each(function () {
                initRow($(this));
                calculateRow($(this));
            });

            applyCurrencyUIToTable($('#currency').val());
            calculateSummary();
        });
        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
        $('#item-body').sortable({
            handle: '.drag-handle',
            update: function () {
                $('#item-body tr').each(function (index) {
                    $(this).find('.sort_order').val(index + 1);
                });
            }
        });
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

            // ✅ CASE 1: INR
            if (currency === 'INR') {

                $('#conversion_rate').val(1).prop('readonly', true);

                $('.inr-col').removeClass('d-none');
                $('.cfv-col').addClass('d-none');

                recalculateAll();
                return;
            }

            // ✅ CASE 2: AUTO LOAD (FROM BACKEND)
            if (isAutoCurrencySet) {

                $('.cfv-col').removeClass('d-none');

                recalculateAll();
                return;
            }

            // ✅ CASE 3: MANUAL CHANGE → SHOW MODAL
            $('#conversion_rate').val('');
            $('#modal_rate').val('');

            $('#currencyModal').modal('show');
        });
        $(document).on('input', '.price_input', function () {
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

            $('.currency-symbol').text(symbol);
        } $('#save_rate').on('click', function () {

            let rate = parseFloat($('#modal_rate').val());

            if (!rate || rate <= 0) {
                alert('Enter valid rate');
                return;
            }

            $('#conversion_rate').val(rate);

            $('.cfv-col').removeClass('d-none');

            recalculateAll();

            $('#currencyModal').modal('hide');
        }); function recalculateAll() {
            $('.item_row').each(function () {
                calculateRow($(this));
            });

            calculateSummary();
        } function updateHeaders(currency) {

            let symbol = getCurrencySymbol(currency);

            $('.currency-label').text(symbol);
        }
        function applyCurrencyUIToTable(currency) {

            if (currency === 'INR') {
                $('.cfv-col').addClass('d-none');
            } else {
                $('.cfv-col').removeClass('d-none');
            }
        }
    </script>
@endpush