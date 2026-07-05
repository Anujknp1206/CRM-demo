{{-- resources/views/company/crm/quotations/edit.blade.php --}}
@extends('company.layouts.master')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Quotation</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                href="{{ route('company.dashboard', ['company' => $company->id]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('quotations.index', ['company' => $company->id]) }}">Quotation List</a></li>
                        <li class="breadcrumb-item active">Edit Quotation</li>
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
                                <a href="{{ url()->previous() }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">

                            <form method="POST"
                                action="{{ route('quotations.update', ['company' => $company->id, 'quotation' => $quotation->id]) }}"
                                autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                {{-- 1. Lead Details --}}
                                <h4 class="mb-2 text-primary"><b>1. Lead Details</b></h4>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Select Lead *</label>
                                        <div class="input-group">
                                            <select id="lead_id" name="lead_id" class="form-control select2" required>
                                                @if($quotation->lead_id)
                                                    <option value="{{ $quotation->lead_id }}" selected>
                                                        {{ $quotation->lead->lead_code }} -
                                                        {{ $quotation->lead->customer->name }}
                                                    </option>
                                                @endif
                                            </select>

                                            <div class="input-group-append">
                                                <button class="btn btn-outline-success" type="button" data-toggle="modal"
                                                    data-target="#addLeadModal">
                                                    + Add
                                                </button>
                                            </div>
                                        </div>
                                    </div>




                                    <div class="col-md-4"><label>Customer Name</label>
                                        <input type="text" id="customer_name" name="customer_name" class="form-control"
                                            value="{{ old('customer_name', $quotation->lead->customer->name ?? '') }}">
                                    </div>

                                    <div class="col-md-4"><label>Email</label>
                                        <input type="email" id="email" name="email" class="form-control"
                                            value="{{ old('email', $quotation->lead->customer->email ?? '') }}">
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
                                                value="{{ old('mobile', $quotation->lead->customer->primaryPhone->phone ?? '') }}"
                                                placeholder="Enter mobile number">
                                        </div>

                                        {{-- extra mobiles --}}
                                        <div id="extra-mobiles" class="mt-1 text-muted small"></div>
                                    </div>
                                    <div class="col-md-4 mt-2"><label>Country</label>
                                        <input type="text" id="country" name="country" class="form-control" readonly
                                            value="{{ $quotation->lead->customer->country->name ?? '' }}">
                                    </div>

                                    <div class="col-md-4 mt-2"><label>State</label>
                                        <input type="text" id="state" name="state" class="form-control" readonly
                                            value="{{ $quotation->lead->customer->state->name ?? '' }}">
                                    </div>

                                    <div class="col-md-4 mt-2"><label>City</label>
                                        <input type="text" id="city" name="city" class="form-control" readonly
                                            value="{{ $quotation->lead->customer->city->name ?? '' }}">
                                    </div>

                                    <div class="col-md-1 mt-2"><label> Code</label>
                                        <input type="text" id="phonecode" name="phonecode" class="form-control"
                                            value="{{ $quotation->lead->customer->country->phonecode ?? '' }}" readonly>
                                    </div>

                                    <div class="col-md-7 mt-2"><label>Address</label>
                                        <textarea id="office_address" name="office_address"
                                            class="form-control">{{ old('office_address', $quotation->office_address) }}</textarea>
                                    </div>
                                </div>

                                <hr>

                                {{-- 2. Additional Customer Details --}}
                                <h4 class="mb-2 text-primary"><b>2. Additional Customer Details</b></h4>
                                <div class="row">
                                    <div class="col-md-3"><label>Contact Person Name</label>
                                        <input type="text" id="contact_person" name="contact_person" class="form-control"
                                            value="{{ old('contact_person', $quotation->contact_person) }}">
                                    </div>

                                    <div class="col-md-3"><label>Customer GST</label>
                                        <input type="text" name="gst" class="form-control" maxlength="15"
                                            value="{{ old('gst', $quotation->lead->customer->gst ?? '--') }}">
                                    </div>

                                    <div class="col-md-3"><label>Delivery Address</label>
                                        <textarea name="delivery_address"
                                            class="form-control">{{ old('delivery_address', $quotation->delivery_address) }}</textarea>
                                    </div>
                                    @php
                                        $authUser = auth()->user();
                                    @endphp

                                    <div class="col-md-3">
                                        <label>Assigned Staff </label>


                                        {{-- Staff --}}
                                        <input type="text" class="form-control" value="{{ $authUser->name }}" readonly>
                                        <input type="hidden" name="assigned_user_id" value="{{ $authUser->id }}">

                                    </div>


                                </div>
                                <hr>
                                {{-- 4. Quotation Details --}}
                                <h4 class="mb-2 text-primary"><b>3. Quotation Details</b></h4>
                                <div class="mobile-scroll">
                                    <div class="row flex-nowrap">
                                        <div class="row">
                                            <div class="col"><label>Quotation Number</label>
                                                <input type="text" id="quote_preview" class="form-control"
                                                    value="{{ $quotation->quote_number }}" readonly>
                                            </div>
                                            <div class="col">
                                                <label>Quotation Date *</label>
                                                <div class="input-group">
                                                    <input type="text" id="quote_date" name="quote_date"
                                                        class="form-control" placeholder="DD/MM/YYYY"
                                                        value="{{ old('quote_date', $quotation->quote_date) }}" required>

                                                    <div class="input-group-append">
                                                        <span class="input-group-text calendar-icon"
                                                            data-target="#quote_date">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="col"><label>PI Number</label>
                                                <input type="text" id="pi_preview" class="form-control"
                                                    value="{{ $quotation->pi_number ?? '' }}">
                                            </div>
                                            <div class="col">
                                                <label>PI Date</label>
                                                <div class="input-group">
                                                    <input type="text" id="pi_date" name="pi_date" class="form-control"
                                                        placeholder="DD/MM/YYYY"
                                                        value="{{ old('pi_date', $quotation->pi_date) }}">

                                                    <div class="input-group-append">
                                                        <span class="input-group-text calendar-icon" data-target="#pi_date">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <hr>

                                {{-- 5. Upload Files (existing + new) --}}
                                <h4 class="mb-2 text-primary"><b>4. Upload Files & Remark</b></h4>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        @if($quotation->files && $quotation->files->count())
                                            <div class="mb-2">
                                                <strong>Existing Files</strong>
                                                <table class="table table-sm table-bordered">
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
                                                        @foreach($quotation->files as $f)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $f->file_name }}</td>
                                                                <td><a href="{{ asset('/admin/uploads/' . $f->file_path) }}"
                                                                        target="_blank" class="btn btn-sm btn-info">Download</a>
                                                                </td>
                                                                <td>
                                                                    <label><input type="checkbox" name="deleted_files[]"
                                                                            value="{{ $f->id }}"> Delete</label>
                                                                    <input type="hidden" name="existing_file_ids[]"
                                                                        value="{{ $f->id }}">
                                                                </td>
                                                                <td>
                                                                    <input type="file" name="replace_file_{{ $f->id }}"
                                                                        class="form-control-file">
                                                                    <input type="hidden" name="replace_file_ids[]"
                                                                        value="{{ $f->id }}">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif

                                        <div class="row">
                                            <!-- FILE UPLOAD SECTION -->
                                            <div class="col-md-4">
                                                <label><b>Upload Files (images, .pdf, .doc, .docx)</b></label>
                                                <div id="files-wrapper">
                                                    <div class="row mb-2 multiple">
                                                        <div class="col-md-12">
                                                            <input type="file" name="uploads[]" class="form-control"
                                                                id="fileInput" accept="image/*,.pdf,.doc,.docx" multiple>

                                                            <div id="filePreview" class="row mt-2"></div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <!-- REMARK SECTION -->
                                            <div class="col-md-4">
                                                <div class="d-flex mb-1 justify-content-between align-items-center">
                                                    <label class="mb-0"><b>Remark (Special Clause in English)</b></label>
                                                </div>
                                                <textarea name="special_clause" id="special_clause"
                                                    class="form-control summernote" rows="1"
                                                    placeholder="Enter any special instructions...">{{ old('remark', $quotation->special_clause) }} </textarea>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex mb-1 justify-content-between align-items-center">
                                                    <label class="mb-0"><b>Remark (Special Clause in Hindi)</b></label>
                                                </div>
                                                <textarea name="hi_special_clause" id="hi_special_clause"
                                                    class="form-control summernote" rows="1"
                                                    placeholder="Enter any special instructions...">{{ old('remark', $quotation->hi_special_clause) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h4 class="mb-2 text-primary"><b>5. Currency & Rate of conversion</b></h4>

                                <div class="row">
                                    <div class="col">
                                        <label>Currency</label>
                                        <select name="currency" id="currency" class="form-control">
                                            <option value="INR" {{ $quotation->currency == 'INR' ? 'selected' : '' }}>INR
                                            </option>
                                            <option value="USD" {{ $quotation->currency == 'USD' ? 'selected' : '' }}>USD
                                            </option>
                                            <option value="EUR" {{ $quotation->currency == 'EUR' ? 'selected' : '' }}>EUR
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label>Conversion Rate</label>

                                        <div class="input-group">
                                            <input type="text" step="0.01" name="conversion_rate" id="conversion_rate"
                                                class="form-control" value="{{ $quotation->conversion_rate }}" readonly>

                                            <div class="input-group-append">
                                                <button type="button" id="edit_rate" class="btn btn-outline-success"
                                                    title="Edit Rate" disabled>
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <hr>

                                {{-- 7. QUOTATION ITEMS --}}
                                <h4 class="mb-2 text-primary"><b>6. Quotation Items</b></h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="quotation_items_table">
                                        <thead>
                                            <tr>
                                                <th style="width:40px">↕</th>
                                                <th>Type</th>
                                                <th>Origin</th>
                                                <th>Item Name</th>
                                                <th>Item Name (Hindi)</th>
                                                <th>Description</th>
                                                <th>Qty</th>
                                                <th class="inr-col">Price</th>
                                                <th class="converted-col d-none price-header">Price</th>
                                                <th class="converted-col d-none total-header">Total</th>

                                                <th class="cfv-header">CFV (₹)</th>
                                                </th>
                                                <th><button type="button" class="btn btn-success" id="add_row">+</button>
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody id="item-body">
                                            @foreach($quotation->items as $it)
                                                <tr class="item_row">

                                                    <input type="hidden" name="row_item_id[]" value="{{ $it->id }}">

                                                    @php
                                                        $item = $it->machine ?? $it->component;
                                                        $itemType = $it->machine ? 'machine' : 'component';
                                                    @endphp
                                                    <td class="text-center drag-handle" style="cursor:move">
                                                        <i class="fa fa-bars text-muted"></i>
                                                    </td>

                                                    {{-- TYPE --}}
                                                    <td>
                                                        <select name="item_type[]" class="form-control item_type" required>
                                                            <option value="machine" {{ $it->machine ? 'selected' : '' }}>
                                                                Machine</option>
                                                            <option value="component" {{ $it->component ? 'selected' : '' }}>
                                                                Component</option>
                                                        </select>
                                                    </td>

                                                    <td>
                                                        <select name="origin[]" class="form-control origin" required>
                                                            <option value="self" {{ strtolower($item->origin ?? '') == 'self' ? 'selected' : '' }}>Self</option>
                                                            <option value="outsource" {{ strtolower($item->origin ?? '') == 'outsource' ? 'selected' : '' }}>Outsource</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" class="saved_item_id" value="{{ $item->id }}">
                                                        {{-- required for auto-select --}}
                                                        <select name="item_id[]" class="form-control item_select"
                                                            required></select>
                                                    </td>
                                                    <td><input type="text" name="item_name_hindi[]"
                                                            class="form-control item_name_hi"
                                                            value="{{ $item->hi_name ?? '' }}"></td>
                                                    <td>
                                                        <div class="d-flex gap-2">

                                                            <textarea name="description_en[]"
                                                                class="form-control description_en" rows="1"
                                                                readonly>{{ strip_tags($it->description ?: $item->description) }}</textarea>

                                                            <textarea name="description_en_html[]"
                                                                class="description_en_html d-none">{{ $it->description ?: $item->description }}</textarea>

                                                            <textarea name="description_hi[]"
                                                                class="description_hi d-none"> {{ $it->hi_description ?: ($item->hi_description ?? '') }}</textarea>

                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-success edit-description">
                                                                <i class="fa fa-edit"></i>
                                                            </button>

                                                        </div>
                                                    </td>
                                                    <td><input type="text" name="quantity[]" class="form-control qty"
                                                            value="{{ $it->quantity }}" required></td>
                                                    <td class="inr-col">
                                                        <input name="unit_price[]" class="form-control unit_price"
                                                            value="{{ $it->unit_price }}">
                                                    </td>

                                                    <td class="converted-col d-none">
                                                        <input name="converted_price[]" class="form-control converted_price"
                                                            value="{{ $it->converted_unit_price }}">
                                                    </td>
                                                    <td class="converted-col d-none">
                                                        <input name="converted_total[]" class="form-control converted_total"
                                                            value="{{ $it->converted_total_price }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input name="total[]" class="form-control total"
                                                            value="{{ $it->total_price }}" readonly>
                                                    </td>



                                                    <td><button type="button" class="btn btn-danger remove_row">X</button>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            {{-- If no items exists, add one empty row --}}
                                            @if($quotation->items->count() == 0)
                                                <tr class="item_row">

                                                    <input type="hidden" name="row_item_id[]" value="">
                                                    <td class="text-center drag-handle" style="cursor:move">
                                                        <i class="fa fa-bars text-muted"></i>
                                                    </td>

                                                    <td>
                                                        <select name="item_type[]" class="form-control item_type">
                                                            <option value="">-- Select Type --</option>
                                                            <option value="machine">Machine</option>
                                                            <option value="component">Component</option>
                                                        </select>
                                                    </td>

                                                    <td>
                                                        <select name="origin[]" class="form-control origin">
                                                            <option value="">-- Select Origin --</option>
                                                            <option value="self">Self</option>
                                                            <option value="outsource">Outsource</option>
                                                        </select>
                                                    </td>

                                                    <td>
                                                        <input type="hidden" class="saved_item_id" value="">
                                                        <select name="item_id[]" class="form-control item_select"
                                                            required></select>
                                                    </td>

                                                    {{-- Hindi Name --}}
                                                    <td>
                                                        <input type="text" name="item_name_hindi[]"
                                                            class="form-control item_name_hi">
                                                    </td>
                                                    {{-- Description --}}
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            {{-- Preview Text --}}
                                                            <textarea name="description_en[]"
                                                                class="form-control description_en" rows="1" readonly
                                                                required></textarea>

                                                            {{-- English HTML --}}
                                                            <textarea name="description_en_html[]"
                                                                class="description_en_html d-none"></textarea>

                                                            {{-- Hindi HTML --}}
                                                            <textarea name="description_hi[]"
                                                                class="description_hi d-none"></textarea>

                                                            <button type="button"
                                                                class="btn btn-sm btn-success edit-description">
                                                                Edit
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td><input type="text" name="quantity[]" class="form-control qty" value="1"
                                                            required></td>
                                                    <td class="inr-col"><input type="text" name="unit_price[]"
                                                            class="form-control unit_price"></td>

                                                    <td class="converted-col d-none">
                                                        <input type="text" name="converted_price[]"
                                                            class="form-control converted_price">
                                                    </td>
                                                    <td class="converted-col d-none">
                                                        <input type="text" name="converted_total[]"
                                                            class="form-control converted_total" readonly>
                                                    </td>
                                                    <td><input type="text" name="total[]" class="form-control total" readonly>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger remove_row">X</button>
                                                    </td>
                                                </tr>

                                            @endif
                                        </tbody>
                                    </table>
                                    <div id="deleted-items-wrapper"></div>
                                </div>
                                <br>

                                {{-- SUMMARY --}}
                                <div class="totals-scroll">
                                    <div class="row flex-nowrap totals-row">
                                        <div class="col">
                                            <label>Subtotal</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text currency-symbol">₹</span>
                                                </div>
                                                <input type="text" name="subtotal" id="subtotal" class="form-control"
                                                    value="{{ $quotation->total_amount }}" readonly>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <label>Discount (amt.)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text currency-symbol">₹</span>
                                                </div>
                                                <input type="text" name="discount" id="discount" class="form-control discount"
                                                    value="{{ $quotation->discount }}">
                                            </div>
                                        </div>

                                        <div class="col">
                                            <label>Tax %</label>
                                            <input type="text" name="tax" id="tax" class="form-control tax"
                                                value="{{ $quotation->tax }}">
                                        </div>

                                        <div class="col">
                                            <label>Tax (amount)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text currency-symbol">₹</span>
                                                </div>
                                                <input type="text" name="tax_amount" class="form-control" id="tax_amount"
                                                    step="0.01" value="{{ $quotation->tax_amount }}" readonly>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-success"
                                                        id="round_tax">Round</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <label>Final Total</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text currency-symbol">₹</span>
                                                </div>
                                                <input type="text" name="final_total" id="final_total"
                                                    value="{{ $quotation->final_amount }}" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <hr>

                                {{-- 8. Terms & Conditions --}}
                                <h4 class="mb-2 text-primary"><b>7. Terms & Conditions</b></h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="d-flex mb-1 justify-content-end">
                                        </div>
                                        <textarea name="terms_conditions" id="terms_conditions"
                                            class="form-control summernote"
                                            rows="5">{!! old('terms_conditions', $quotation->terms_conditions) !!}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex mb-1 justify-content-end">
                                        </div>
                                        <textarea name="hi_terms_conditions" id="hi_terms_conditions"
                                            class="form-control summernote"
                                            rows="5">{!! old('hi_terms_conditions', $quotation->hi_terms_conditions) !!}</textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success mt-3">Update Quotation</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <select id="master_items" style="display:none;">
        @foreach($machines as $m)
            <option value="{{ $m->id }}" data-type="machine" data-origin="{{ strtolower($m->origin) }}"
                data-description="{{ $m->description }}" data-description-hi="{{ $m->hi_description }}"
                data-name-hi="{{ $m->hi_name }}">
                {{ $m->name }}
            </option>
        @endforeach

        @foreach($components as $c)
            <option value="{{ $c->id }}" data-type="component" data-origin="{{ strtolower($c->origin) }}"
                data-description="{{ $c->description }}" data-description-hi="{{ $c->hi_description }}"
                data-name-hi="{{ $c->hi_name }}">
                {{ $c->name }}
            </option>
        @endforeach
    </select>
@endsection
@push('styles')
    <style>
        /* Force item_name dropdown width same as normal select */
        .item_select.select2-hidden-accessible+.select2-container {
            width: 100% !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .calendar-icon {
            cursor: pointer;
            background: #f3f6f9;
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
            font-size: 18px;
        }
    </style>

@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $(document).on('input', '.discount,.tax,.qty,.unit_price, .modal_rate,.total_price, .converted_price, .converted_total', function () {

            let value = this.value;

            // allow only digits and one decimal point
            value = value.replace(/[^0-9.]/g, '');

            let parts = value.split('.');

            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            this.value = value;
        });
        const pickers = {};

        function initDatePicker(selector) {
            pickers[selector] = flatpickr(selector, {
                dateFormat: "Y-m-d",   // backend
                altInput: true,
                altFormat: "d/m/Y",    // user sees
                allowInput: true
            });
        }

        // Init both fields
        initDatePicker("#quote_date");
        initDatePicker("#pi_date");

        // Open calendar on icon click
        $(document).on("click", ".calendar-icon", function () {
            const target = $(this).data("target");
            if (pickers[target]) {
                pickers[target].open();
            }
        });
    </script>

    <script>
        $(document).ready(function () {
            // Initialize Select2 for Leads
            $('#lead_id').select2({
                placeholder: "Search Lead by name, code, mobile...",
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('ajax.get.lead.details', ['company' => $company->id]) }}", // ✅ SEARCH API
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { search: params.term };
                    },
                    processResults: function (data) {
                        return data;
                    }
                }
            });


        });
        $('#lead_id').on('select2:select', function (e) {

            const leadId = e.params.data.id;
            if (!leadId) return;

            $.ajax({
                url: "{{ route('ajax.get.single.lead.details', ['company' => $company->id]) }}",
                type: "GET",
                data: { id: leadId },
                dataType: "json",
                success: function (res) {

                    // BASIC
                    $('#customer_name').val(res.customerName ?? '');
                    $('#email').val(
                        res.email && res.email !== '---' ? res.email : ''
                    );
                    $('#office_address').val(res.address ?? '');
                    $('input[name="gst"]').val(res.gst ?? '');

                    // COUNTRY CODE
                    const phoneCode = res.country?.phonecode ?? '';

                    $('#country_code_select')
                        .empty()
                        .append(`<option value="${phoneCode}">+${phoneCode}</option>`)
                        .val(phoneCode);

                    $('#phonecode').val(phoneCode);

                    // PRIMARY MOBILE
                    $('#mobile').val(res.phones?.[0] ?? '');

                    // EXTRA MOBILES
                    let mobilesHtml = '';
                    if (Array.isArray(res.phones)) {
                        mobilesHtml = `
                                                                                    <ul class="list-group list-group-sm mt-1">
                                                                                        ${res.phones.map((phone, index) => `
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

                    // LOCATION
                    $('#country').val(res.country?.name ?? '');
                    $('#state').val(res.state?.name ?? '');
                    $('#city').val(res.city?.name ?? '');
                }
            });
        });
        $('#country_code_select').on('change', function () {
            const code = $(this).val();
            $('#phonecode').val(code);

            $('#extra-mobiles .list-group-item span:first-child').each(function () {
                const text = $(this).text().replace(/^\+\d+\s/, '');
                $(this).html(`<i class="fa fa-phone text-success mr-1"></i> +${code} ${text}`);
            });
        });
        $(document).ready(function () {
            const existingLeadId = $('#lead_id').val();

            if (existingLeadId) {
                // Force trigger same logic as manual select
                $('#lead_id').trigger({
                    type: 'select2:select',
                    params: {
                        data: { id: existingLeadId }
                    }
                });
            }
        });

    </script>
    <script>
        let MASTER = [];

        $(document).ready(function () {

            /* 🟢 LOAD ALL ITEMS INTO MASTER[] */
            $('#master_items option').each(function () {
                MASTER.push($(this).clone());
            });

            /* 🟢 Load existing saved rows */
            $('#item-body tr').each(function () {
                initRow($(this));    // load options + preselect item
            });

            calculateSummary();
        });


        function initRow(row) {
            let type = row.find(".item_type").val();
            let origin = row.find(".origin").val();
            let saved = row.find(".saved_item_id").val();  // get current selected item ID

            let select = row.find(".item_select");
            select.empty().append('<option value="">Select Item</option>');

            MASTER.forEach(opt => {
                if ($(opt).data('type') == type && $(opt).data('origin') == origin) {
                    select.append($(opt).clone());
                }
            });
            if (saved) {
                select.val(saved);
            }

            let descField = row.find(".description_en");
            let existingDesc = descField.val().trim();

            let raw = select.find(":selected").data("description") ?? '';
            let rawHi = select.find(":selected").data("description-hi") ?? '';
            let hiName = select.find(":selected").data("name-hi") ?? '';

            if (!existingDesc && !descField.attr('data-user-edited')) {
                descField.val($('<div>').html(raw).text());
                row.find('.description_en_html').val(raw);
                row.find('.description_hi').val(rawHi);
                row.find('.item_name_hi').val(hiName);
            }

            select.select2();
        }

        /* 🔄 Filter options when type/origin changes */
        $(document).on('change', '.item_type,.origin', function () {
            let row = $(this).closest('tr');
            applyFilter(row);
        });

        function applyFilter(row) {
            let type = row.find('.item_type').val();
            let origin = row.find('.origin').val();
            let select = row.find('.item_select');

            select.empty().append("<option value=''>Select Item</option>");

            MASTER.forEach(opt => {
                if ($(opt).data("type") == type && $(opt).data("origin") == origin) {
                    select.append($(opt).clone());
                }
            });
        }
        $(document).on('change', '.item_select', function () {

            const row = $(this).closest('tr');
            const descField = row.find('.description_en');

            const selected = $(this).find(':selected');

            const raw = selected.data('description') || '';
            const rawHi = selected.data('description-hi') || '';
            const hiName = selected.data('name-hi') || '';

            // Preview only
            descField.val($('<div>').html(raw).text());

            // Save HTML
            row.find('.description_en_html').val(raw);

            // Save Hindi
            row.find('.description_hi').val(rawHi);

            // Save Hindi name
            row.find('.item_name_hi').val(hiName);
        });
        /* ➕ Add item row */
        $("#add_row").click(function () {

            let newRow = `
                                                                                                                                                                                                        <tr class="item_row">
                                                                                                                                                                                                        <td class="text-center drag-handle" style="cursor:move">
                                                                    <i class="fa fa-bars text-muted"></i>
                                                                </td>

                                                                                                                                                                                                            <td>
                                                                                                                                                                                                                <select name="item_type[]" class="form-control item_type">
                                                                                                                                                                                                                    <option value="">-- Select Type --</option>
                                                                                                                                                                                                                    <option value="machine">Machine</option>
                                                                                                                                                                                                                    <option value="component">Component</option>
                                                                                                                                                                                                                </select>
                                                                                                                                                                                                            </td>

                                                                                                                                                                                                            <td>
                                                                                                                                                                                                                <select name="origin[]" class="form-control origin">
                                                                                                                                                                                                                    <option value="">-- Select Origin --</option>
                                                                                                                                                                                                                    <option value="self">Self</option>
                                                                                                                                                                                                                    <option value="outsource">Outsource</option>
                                                                                                                                                                                                                </select>
                                                                                                                                                                                                            </td>

                                                                                                                                                                                                            <td>
                                                                                                                                                                                                                <input type="hidden" class="saved_item_id" value="">
                                                                                                                                                                                                                <select name="item_id[]" class="form-control item_select"></select>
                                                                                                                                                                                                            </td>

                                                                                                                                                                                                           <td>
                                            <input type="text"
                                                name="item_name_hindi[]"
                                                class="form-control item_name_hi">
                                        </td>

                                        <td>
                                            <div class="d-flex gap-2">
                                                <textarea
                                                    name="description_en[]"
                                                    class="form-control description_en"
                                                    rows="1"
                                                    readonly
                                                    required></textarea>
                                                <textarea
                                                    name="description_en_html[]"
                                                    class="description_en_html d-none"></textarea>
                                                <textarea
                                                    name="description_hi[]"
                                                    class="description_hi d-none"></textarea>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-success edit-description">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td><input type="text" name="quantity[]" class="form-control qty" value="1"></td>
                                        <td  class="inr-col"><input type="text" name="unit_price[]" class="form-control unit_price" value="1"></td>

                                                            <td class="converted-col d-none">
                                                                <input type="text" name="converted_price[]" class="form-control converted_price" value="1">
                                                            </td>

                                                            <td class="converted-col d-none">
                                                                <input type="text" name="converted_total[]" class="form-control converted_total" readonly>
                                                            </td>
                                                            <td><input type="text" name="total[]" class="form-control total" readonly></td>
                        <td>
                        <button type="button" class="btn btn-danger remove_row">X</button>
                        </td>
                        </tr>
                        `;

            let appendRow = $(newRow);
            $("#item-body").append(appendRow);
            initRow(appendRow);  // filter fresh dropdown

            toggleConvertedColumns($('#currency').val());
            // 🔥 ADD THIS
            calculateRow(appendRow);
            calculateSummary();
        });


        /* ❌ Delete row (SweetAlert) */
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
        function calculateSummary() {

            let subtotal = 0;
            let currency = $('#currency').val();

            // ✅ FIX: choose correct column
            let selector = (currency === 'INR') ? '.total' : '.converted_total';

            $(selector).each(function () {
                subtotal += parseFloat($(this).val()) || 0;
            });

            let discount = parseFloat($("#discount").val()) || 0;
            let taxPercent = parseFloat($("#tax").val()) || 0;

            let afterDiscount = subtotal - discount;
            if (afterDiscount < 0) afterDiscount = 0;

            let taxAmount = (afterDiscount * taxPercent) / 100;
            let finalTotal = afterDiscount + taxAmount;

            $("#subtotal").val(subtotal.toFixed(2));
            $("#tax_amount").val(taxAmount.toFixed(2));
            $("#final_total").val(finalTotal.toFixed(2));
        }
        $('#currency').on('change', function () {

            let currency = $(this).val();

            // ✅ Update UI
            updateCurrencyUI(currency);
            toggleConvertedColumns(currency);

            if (currency === 'INR') {

                // 👉 Reset to default
                $('#conversion_rate').val(1);
                $('#edit_rate').prop('disabled', true);

                $('.cfv-header').text('Total');
                recalculateAll();

            } else {

                // 🔥 VERY IMPORTANT: RESET OLD RATE
                $('#conversion_rate').val('');
                $('#modal_rate').val('');

                // 👉 Enable edit button
                $('#edit_rate').prop('disabled', false);

                $('.cfv-header').text('CFV (₹)');
                // 👉 Show modal to enter new rate
                $('#currencyModal').modal('show');
            }
        });
        function updateCurrencyUI(currency) {

            CURRENT_CURRENCY = currency;
            let symbol = currencySymbols[currency] || '';

            // ✅ Update table input symbols
            $('.currency-symbol').text(symbol);

            // ✅ Update totals section


            // ✅🔥 UPDATE TABLE HEADERS
            $('.price-header').text(`Price (${symbol})`);
            $('.total-header').text(`Total (${symbol})`);
        } $('#currency').on('change', function () {
            let currency = $(this).val();
            updateCurrencyUI(currency);

            calculateSummary();
            if (currency !== 'INR') {
                $('#currencyModal').modal('show');
                $('#edit_rate').prop('disabled', false);
            } else {
                $('#conversion_rate').val(1);
                toggleConvertedColumns(false);
                $('#edit_rate').prop('disabled', true);
            }
        });
        $(document).on('input', '#discount', function () {
            let subtotal = parseFloat($('#subtotal').val()) || 0;
            let discount = parseFloat($(this).val()) || 0;

            if (discount > subtotal) {
                Swal.fire('Invalid Discount', 'Discount cannot be greater than Subtotal', 'error');
                $(this).val(0);
                calculateSummary();
            }
        });

        $(document).on("click", "#add_row, .remove_row", function () {
            setTimeout(calculateSummary, 150);
        });

        // initial calculation
        $(document).ready(function () {

            $('#quotation_items_table tbody tr').each(function () {
                calculateRow($(this));   // 🔥 THIS WAS MISSING
            });
            let currency = $('#currency').val();

            if (currency === 'INR') {
                $('.cfv-header').text('Total');
            } else {
                $('.cfv-header').text('CFV (₹)');
            }
            updateCurrencyUI(currency);        // ✅ FIX
            toggleConvertedColumns(currency);
            calculateSummary(); // AFTER row calculation
        });
    </script>

    <script>
        const SweetToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    </script>
    <script>
        // 🔵 ROUND OFF TAX AMOUNT (EDIT)
        $('#round_tax').on('click', function () {

            let taxAmount = parseFloat($('#tax_amount').val()) || 0;
            let roundedTax = Math.round(taxAmount);

            $('#tax_amount').val(roundedTax.toFixed(2));

            let subtotal = parseFloat($('#subtotal').val()) || 0;
            let discount = parseFloat($('#discount').val()) || 0;

            let afterDiscount = subtotal - discount;
            if (afterDiscount < 0) afterDiscount = 0;

            let finalTotal = afterDiscount + roundedTax;
            $('#final_total').val(finalTotal.toFixed(2));

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
        $(document).on('submit', '#quickLeadForm', function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('leads.ajax.store', $company->id) }}",
                type: "POST",
                dataType: "json",
                data: $(this).serialize(),

                success: function (response) {

                    if (!response.status) return;

                    // Add lead to main Select2
                    let newOption = new Option(
                        response.data.text,
                        response.data.id,
                        true,
                        true
                    );

                    $('#lead_id')
                        .append(newOption)
                        .trigger('change.select2');

                    SweetToast.fire({
                        icon: 'success',
                        title: response.message
                    });

                    // Reset form
                    $('#quickLeadForm')[0].reset();
                    $('#quickLeadForm select').val(null).trigger('change');

                    // Close modal
                    $('#addLeadModal').modal('hide');

                    setTimeout(function () {
                        location.reload(); // ✅ reload page
                    }, 500);

                },

                error: function () {
                    SweetToast.fire({
                        icon: 'error',
                        title: 'Failed to create lead'
                    });
                }
            });
        });

        // Init select2 when modal opens
        $('#addLeadModal').on('shown.bs.modal', function () {
            $(this).find('.select2').select2({
                dropdownParent: $('#addLeadModal'),
                width: '100%'
            });
        });

    </script>
    <script>
        /* =========================
           MODAL COUNTRY → STATE
        ========================= */
        $(document).on('change', '#modal_country', function () {

            let countryId = $(this).val();

            $('#modal_state').html('<option value="">Loading...</option>');
            $('#modal_city').html('<option value="">Select City</option>');

            if (!countryId) return;

            $.ajax({
                url: "{{ route('getStates') }}",
                type: "GET",
                data: { country_id: countryId },
                success: function (states) {

                    $('#modal_state').html('<option value="">Select State</option>');

                    states.forEach(function (state) {
                        $('#modal_state').append(
                            `<option value="${state.id}">${state.name}</option>`
                        );
                    });
                }
            });
        });

        /* =========================
           MODAL STATE → CITY
        ========================= */
        $(document).on('change', '#modal_state', function () {

            let stateId = $(this).val();

            $('#modal_city').html('<option value="">Loading...</option>');

            if (!stateId) return;

            $.ajax({
                url: "{{ route('getCities') }}",
                type: "GET",
                data: { state_id: stateId },
                success: function (cities) {

                    $('#modal_city').html('<option value="">Select City</option>');

                    cities.forEach(function (city) {
                        $('#modal_city').append(
                            `<option value="${city.id}">${city.name}</option>`
                        );
                    });
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {

            // Initialize all existing rows
            $('.description_en').each(function () {

                const row = $(this).closest('tr');

                // English HTML
                let html = row.find('.description_en_html').val();

                // fallback
                if (!html) {
                    html = $(this).val();
                }

                // Save html
                row.find('.description_en_html').val(html);

                // Show clean preview text
                let text = $('<div>').html(html).text();

                $(this).val(text);
            });

        });

        let activeRow = null;

        // =========================
        // OPEN DESCRIPTION MODAL
        // =========================
        $(document).on('click', '.edit-description', function () {

            activeRow = $(this).closest('tr');

            // English HTML
            let enHtml = activeRow.find('.description_en_html').val();

            // Hindi HTML
            let hiHtml = activeRow.find('.description_hi').val();

            // fallback
            if (!enHtml) {
                enHtml = activeRow.find('.description_en').val();
            }

            // Load Summernote editors
            $('#modalDescriptionEn').summernote('code', enHtml || '');

            $('#modalDescriptionHi').summernote('code', hiHtml || '');

            $('#descriptionModal').modal('show');
        });

        // =========================
        // SAVE DESCRIPTION
        // =========================
        $('#saveDescription').on('click', function () {

            if (!activeRow) return;

            // Get HTML from editors
            const enHtml = $('#modalDescriptionEn').summernote('code');

            const hiHtml = $('#modalDescriptionHi').summernote('code');

            // Convert English HTML → plain preview text
            const plainText = $('<div>').html(enHtml).text();

            // Preview text
            activeRow.find('.description_en').val(plainText);

            // Save English HTML
            activeRow.find('.description_en_html').val(enHtml);

            // Save Hindi HTML
            activeRow.find('.description_hi').val(hiHtml);

            // Mark edited
            activeRow.find('.description_en')
                .attr('data-user-edited', '1');

            $('#descriptionModal').modal('hide');
        });

    </script>
    <script>
        $(document).ready(function () {

            $('#item-body').sortable({
                handle: '.drag-handle',
                items: 'tr.item_row',
                cursor: 'move',
                axis: 'y',
                opacity: 0.7,
                update: function () {
                    calculateSummary(); // recalc after reorder
                }
            });

        });
        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
    </script>
    <script>
        function isHindi(text) {
            return /[\u0900-\u097F]/.test(text);
        }
        // FREE GOOGLE TRANSLATE (Unofficial endpoint)
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
        // Translate Summernote fields
        $(document).on('click', '.translate-btn', async function () {

            const targetId = $(this).data('target');
            const editor = $('#' + targetId);
            const html = editor.summernote('code');

            if (!html.trim()) return;

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
        }); $(document).on('click', '.translate-description', async function () {

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

            btn.prop('disabled', false);
            $('#translateLoader').hide();
        });
    </script>
    <script>
        $(document).ready(function () {

            let currency = $('#currency').val();

            if (currency !== 'INR') {
                $('.converted-col').removeClass('d-none');

                $('#currency_label').text(currency);
                $('#currency_label_total').text(currency);
            }

        }); $('#edit_rate').on('click', function () {
            $('#modal_rate').val($('#conversion_rate').val());
            $('#currencyModal').modal('show');
        });
        function calculateRow(row, changed = 'inr') {

            let qty = parseFloat(row.find('.qty').val()) || 0;
            let rate = parseFloat($('#conversion_rate').val()) || 1;

            let inr = parseFloat(row.find('.unit_price').val()) || 0;
            let converted = parseFloat(row.find('.converted_price').val()) || 0;

            // INR → Converted
            if (changed === 'inr') {
                converted = rate ? (inr / rate) : 0;
                row.find('.converted_price').val(converted.toFixed(2));
            }

            // Converted → INR
            if (changed === 'converted') {
                inr = converted * rate;
                row.find('.unit_price').val(inr.toFixed(2));
            }

            // Totals
            let totalINR = qty * inr;
            let totalConverted = qty * converted;

            row.find('.total').val(totalINR.toFixed(2));
            row.find('.converted_total').val(totalConverted.toFixed(2));
        } $(document).on('input', '.unit_price', function () {
            calculateRow($(this).closest('tr'), 'inr');
            calculateSummary();
        });

        $(document).on('input', '.converted_price', function () {
            calculateRow($(this).closest('tr'), 'converted');
            calculateSummary();
        });
        $(document).on('input', '#discount, #tax', function () {
            calculateSummary();
        });
        $(document).on('input', '.qty', function () {
            calculateRow($(this).closest('tr'));
            calculateSummary();
        });
        $('#save_rate').on('click', function () {

            let rate = $('#modal_rate').val();

            if (!rate || rate <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Rate',
                    text: 'Please enter a valid conversion rate'
                });
                return;
            }

            Swal.fire({
                title: 'Update Conversion Rate?',
                text: 'All values will be recalculated!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Update'
            }).then((result) => {

                if (!result.isConfirmed) return;

                $('#conversion_rate').val(rate);
                $('#currencyModal').modal('hide');

                recalculateAll();
                toggleConvertedColumns(true);
                updateCurrencyLabels($('#currency').val());

                // 🔥 BEST PRACTICE
                $('#quotation_items_table tbody tr').each(function () {
                    calculateRow($(this));
                });

                calculateSummary();

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Conversion rate updated',
                    showConfirmButton: false,
                    timer: 1500
                });

            });
        });
        function recalculateAll() {

            $('#quotation_items_table tbody tr').each(function () {
                calculateRow($(this));
            });

            calculateSummary();
        }
        $(document).ready(function () {

            let currency = $('#currency').val();
            let rate = $('#conversion_rate').val();

            // 👉 If edit page already has non-INR currency
            if (currency && currency !== 'INR') {

                toggleConvertedColumns(true);
                updateCurrencyLabels(currency);

                $('#edit_rate').prop('disabled', false);

                // 🔥 VERY IMPORTANT: recalc all rows
                $('#quotation_items_table tbody tr').each(function () {
                    calculateRow($(this));
                });

                calculateSummary();
            }
        });
        const currencySymbols = {
            INR: '₹',
            USD: '$',
            EUR: '€'
        };
        function toggleConvertedColumns(currency) {
            if (currency === 'INR') {
                // ✅ Show INR
                $('.inr-col').removeClass('d-none');

                // ❌ Hide converted
                $('.converted-col').addClass('d-none');
            } else {
                // ❌ Hide INR
                $('.inr-col').addClass('d-none');

                // ✅ Show converted
                $('.converted-col').removeClass('d-none');
            }
        }
    </script>
    <script>
        const fileInput = document.getElementById('fileInput');
        const previewContainer = document.getElementById('filePreview');

        let selectedFiles = [];

        if (fileInput) {

            fileInput.addEventListener('change', function (e) {

                previewContainer.innerHTML = '';

                selectedFiles = Array.from(e.target.files);

                selectedFiles.forEach((file, index) => {

                    const col = document.createElement('div');
                    col.className = 'col-md-3 mb-2';

                    const preview = document.createElement('div');
                    preview.className = 'file-preview';

                    const removeBtn = document.createElement('span');

                    removeBtn.innerHTML = '&times;';
                    removeBtn.className = 'remove-file';

                    removeBtn.onclick = (e) => {
                        e.preventDefault();
                        e.stopPropagation();

                        removeFile(index);
                    };

                    // IMAGE PREVIEW
                    if (file.type.startsWith('image/')) {

                        const url = URL.createObjectURL(file);

                        const link = document.createElement('a');
                        link.href = url;
                        link.target = '_blank';

                        const img = document.createElement('img');
                        img.src = url;

                        link.appendChild(img);

                        preview.appendChild(link);

                    } else {

                        // PDF / DOC PREVIEW
                        const url = URL.createObjectURL(file);

                        const link = document.createElement('a');

                        link.href = url;
                        link.target = '_blank';
                        link.style.textDecoration = 'none';

                        link.innerHTML = `
                                    <i class="fa fa-file-alt fa-3x text-secondary"></i>
                                    <p class="small mt-1">${file.name}</p>
                                `;

                        preview.appendChild(link);
                    }

                    preview.appendChild(removeBtn);

                    col.appendChild(preview);

                    previewContainer.appendChild(col);
                });
            });
        }

        function removeFile(index) {

            selectedFiles.splice(index, 1);

            const dt = new DataTransfer();

            selectedFiles.forEach(file => dt.items.add(file));

            fileInput.files = dt.files;

            fileInput.dispatchEvent(new Event('change'));
        }
    </script>
@endpush