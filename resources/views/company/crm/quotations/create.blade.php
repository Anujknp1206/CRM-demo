@extends('company.layouts.master')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Add Quotation</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                href="{{ route('company.dashboard', ['company' => $company->id]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a
                                href="{{ route('quotations.index', ['company' => $company->id]) }}">Quotation
                                List</a></li>
                        <li class="breadcrumb-item active">Add Quotation</li>
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
                            <form method="POST" autocomplete="off" novalidate
                                action="{{ route('quotations.store', ['company' => $company->id]) }}"
                                enctype="multipart/form-data">
                                @csrf


                                {{-- ========================================================= --}}
                                {{-- SECTION 1: SELECT LEAD & AUTO-FETCHED CUSTOMER DETAILS --}}
                                {{-- ========================================================= --}}

                                <h4 class="mb-2 text-primary"><b>1. Lead Details</b></h4>

                                <div class="row">

                                    <div class="col-md-4">
                                        <label>Select Lead *</label>
                                        <select name="lead_id" id="lead_id" class="form-control select2" required></select>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Customer Name</label>
                                        <input type="text" id="customer_name" name="customer_name" class="form-control"
                                            readonly>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Email</label>
                                        <input type="email" id="email" name="email" class="form-control">
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
                                        <input type="text" id="country" name="country" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-4 mt-2">
                                        <label>State</label>
                                        <input type="text" id="state" name="state" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-4 mt-2">
                                        <label>City</label>
                                        <input type="text" id="city" name="city" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-1 mt-2">
                                        <label>Code</label>
                                        <input type="text" id="country_code" name="country_code" class="form-control"
                                            readonly>
                                    </div>

                                    <div class="col-md-7 mt-2">
                                        <label>Address</label>
                                        <textarea id="address" name="address" class="form-control"></textarea>
                                    </div>

                                </div>


                                <hr>


                                {{-- ========================================================= --}}
                                {{-- SECTION 2: CUSTOMER ADDITIONAL DETAILS --}}
                                {{-- ========================================================= --}}

                                <h4 class="mb-2 text-primary"><b>2. Additional Customer Details & Staff</b></h4>

                                <div class="row">

                                    <div class="col-md-3">
                                        <label>Contact Person Name</label>
                                        <input type="text" id="contact_person" name="contact_person" class="form-control">
                                    </div>

                                    <div class="col-md-3">
                                        <label>Customer GST</label>
                                        <input type="text" name="gst_number" class="form-control" maxlength="15">
                                    </div>

                                    <div class="col-md-3">
                                        <label>Delivery Address</label>
                                        <textarea name="delivery_address" class="form-control"></textarea>
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

                                    <input type="hidden" name="company_id" id="company_id" value="{{ $company->id }}">

                                    <hr>
                                </div>

                                <hr>

                                {{-- ========================================================= --}}
                                {{-- SECTION 4: QUOTATION DETAILS --}}
                                {{-- ========================================================= --}}

                                <h4 class="mb-2 text-primary"><b>3. Quotation Details</b></h4>
                                <div class="mobile-scroll">
                                    <div class="row flex-nowrap">

                                        <div class="row">


                                            <div class="col">
                                                <label>Quotation Number</label>
                                                <input type="text" id="quote_preview" class="form-control"
                                                    value="Select Lead First" readonly>
                                            </div>
                                            <div class="col">
                                                <label>Quotation Date *</label>
                                                <div class="input-group">
                                                    <input type="text" id="quotation_date" name="quotation_date"
                                                        class="form-control" placeholder="DD/MM/YYYY" required>

                                                    <div class="input-group-append">
                                                        <span class="input-group-text calendar-icon"
                                                            data-target="#quotation_date">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col">
                                                <label>PI Number</label>
                                                <input type="text" id="pi_preview" class="form-control"
                                                    value="Select Lead First" readonly>
                                            </div>

                                            <div class="col">
                                                <label>PI Date</label>
                                                <div class="input-group">
                                                    <input type="text" id="pi_date" name="pi_date" class="form-control"
                                                        placeholder="DD/MM/YYYY">

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


                                {{-- ========================================================= --}}
                                {{-- SECTION 5: FILE UPLOAD --}}
                                {{-- ========================================================= --}}
                                <h4 class="mb-2 text-primary"><b>4. Upload Files & Remark</b></h4>

                                <div class="row">
                                    <!-- FILE UPLOAD SECTION -->
                                    <div class="col-md-4">
                                        <label><b>Upload Files (images, .pdf, .doc, .docx)</b></label>
                                        <div id="files-wrapper">
                                            <div class="row mb-2 multiple">
                                                <div class="col-md-12">
                                                    <input type="file" name="uploads[]" id="fileInput" class="form-control"
                                                        accept="image/*,.pdf,.doc,.docx" multiple>
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
                                        <textarea name="special_clause" id="special_clause" class="form-control summernote"
                                            rows="1" placeholder="Enter any special instructions..."></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex mb-1 justify-content-between align-items-center">
                                            <label class="mb-0"><b>Remark (Special Clause in Hindi)</b></label>
                                        </div>

                                        <textarea name="hi_special_clause" id="hi_special_clause"
                                            class="form-control summernote" rows="1"
                                            placeholder="Enter any special instructions..."></textarea>
                                    </div>
                                </div>
                                <hr>
                                <h4 class="mb-2 text-primary"><b>5. Currency & Rate of conversion</b></h4>

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

                                        <div class="input-group">
                                            <input type="text" step="0.01" name="conversion_rate" id="conversion_rate"
                                                class="form-control" readonly>

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
                                {{-- SECTION 7: Quotation Items --}}
                                <h4 class="mb-2 text-primary"><b>6. Quotation Items</b></h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="quotation_items_table">
                                                <thead>
                                                    <tr>
                                                        <th style="width:30px;"></th>
                                                        <th>Item Type</th>
                                                        <th>Origin</th>
                                                        <th>Select Item</th>
                                                        <th>Item Name (Hindi)</th>
                                                        <th>Description</th>
                                                        <th>Qty</th>
                                                        <th class="inr-col">Price</th>
                                                        <th class="converted-col d-none price-header">Price</th>
                                                        <th class="converted-col d-none total-header">Total</th>
                                                        <th class="cfv-header">CFV (₹)</th>
                                                        <th>
                                                            <button type="button" class="btn btn-success"
                                                                id="add_row">+</button>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <!-- THIS ROW MUST EXIST (contains full items for MASTER[]) -->
                                                    <tr class="item_row">
                                                        <td class="text-center align-middle drag-handle"
                                                            style="cursor: grab;">
                                                            <i class="fa fa-bars text-muted"></i>

                                                            {{-- sort order --}}
                                                            <input type="hidden" name="sort_order[]" class="sort_order"
                                                                value="1">
                                                        </td>

                                                        <td>
                                                            <select name="item_type[]" class="form-control item_type"
                                                                required>
                                                                <option value="machine">Machine</option>
                                                                <option value="component">Component</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="origin[]" class="form-control origin" required>
                                                                <option value="self">Self</option>
                                                                <option value="outsource">Outsource</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="item_id[]"
                                                                class="form-control item_select select2" required>
                                                                <option value="">-- Select Item --</option>
                                                                @foreach($machines as $machine)
                                                                    <option value="{{ $machine->id }}" data-type="machine"
                                                                        data-origin="{{ strtolower($machine->origin) }}"
                                                                        data-description="{{ $machine->description }}"
                                                                        data-description-hi="{{ $machine->hi_description }}"
                                                                        data-name-hi="{{ $machine->hi_name }}">
                                                                        {{ $machine->name }}
                                                                    </option>
                                                                @endforeach

                                                                @foreach($components as $component)
                                                                    <option value="{{ $component->id }}" data-type="component"
                                                                        data-origin="{{ strtolower($component->origin) }}"
                                                                        data-description="{{ $component->description }}"
                                                                        data-description-hi="{{ $component->hi_description }}"
                                                                        data-name-hi="{{ $component->hi_name }}">
                                                                        {{ $component->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="item_name_hindi[]"
                                                                class="form-control item_name_hi">
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-2">

                                                                <textarea name="description_en[]"
                                                                    class="form-control description_en" rows="1"
                                                                    readonly></textarea>

                                                                <!-- 🔥 IMPORTANT (for saving HTML) -->
                                                                <textarea name="description_en_html[]"
                                                                    class="description_en_html d-none"></textarea>

                                                                <textarea name="description_hi[]"
                                                                    class="description_hi d-none"></textarea>

                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-success edit-description">
                                                                    <i class="fa fa-edit"></i>
                                                                </button>
                                                            </div>
                                                        </td>

                                                        <td><input type="text" name="quantity[]" class="form-control qty"
                                                                value="1" min="1" required></td>
                                                        <td class="inr-col"><input type="text" name="unit_price[]"
                                                                class="form-control unit_price" value="1" required>
                                                        </td>
                                                        <td class="converted-col d-none">
                                                            <input type="text" name="converted_price[]"
                                                                class="form-control converted_price">
                                                        </td>
                                                        <td class="converted-col d-none">
                                                            <input type="text" name="converted_total[]"
                                                                class="form-control converted_total" readonly>
                                                        </td>
                                                        <td><input type="text" name="total[]" class="form-control total"
                                                                readonly style="pointer-events:none" required></td>

                                                        <td><button type="button"
                                                                class="btn btn-danger remove_row">X</button>
                                                        </td>
                                                    </tr>

                                                    <!-- TEMPLATE ROW (Used for cloning unlimited rows) -->
                                                    <tr class="item_row_template d-none" style="display:none" disabled>
                                                        <td class="text-center align-middle drag-handle"
                                                            style="cursor: grab;">
                                                            <i class="fa fa-bars text-muted"></i>

                                                            {{-- sort order --}}
                                                            <input type="hidden" name="sort_order[]" class="sort_order"
                                                                value="1" disabled>
                                                        </td>
                                                        <td>
                                                            <select name="item_type[]" class="form-control item_type"
                                                                required disabled>
                                                                <option value="machine">Machine</option>
                                                                <option value="component">Component</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="origin[]" class="form-control origin" required
                                                                disabled>
                                                                <option value="self">Self</option>
                                                                <option value="outsource">Outsource</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="item_id[]" class="form-control item_select"
                                                                disabled required></select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="item_name_hindi[]"
                                                                class="form-control item_name_hi" disabled>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-2">

                                                                <textarea name="description_en[]"
                                                                    class="form-control description_en" rows="1" readonly
                                                                    disabled></textarea>

                                                                <!-- 🔥 IMPORTANT (for saving HTML) -->
                                                                <textarea name="description_en_html[]"
                                                                    class="description_en_html d-none" disabled></textarea>

                                                                <textarea name="description_hi[]"
                                                                    class="description_hi d-none" disabled></textarea>

                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-success edit-description">
                                                                    <i class="fa fa-edit"></i>
                                                                </button>
                                                            </div>
                                                        </td>

                                                        <td><input type="text" name="quantity[]" class="form-control qty"
                                                                value="1" min="1" disabled required></td>
                                                        <td class="inr-col"><input type="text" name="unit_price[]"
                                                                class="form-control unit_price" value="1" disabled required>
                                                        </td>
                                                        <td class="converted-col d-none">
                                                            <input type="text" name="converted_price[]"
                                                                class="form-control converted_price" disabled>
                                                        </td>
                                                        <td class="converted-col d-none">
                                                            <input type="text" name="converted_total[]"
                                                                class="form-control converted_total" disabled>
                                                        </td>

                                                        <td><input type="text" name="total[]" class="form-control total"
                                                                readonly style="pointer-events:none" disabled required></td>
                                                        <td><button type="button"
                                                                class="btn btn-danger remove_row">X</button>
                                                        </td>
                                                    </tr>

                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- SECTION 7.1: Subtotal / Discount / Tax / Final Total --}}
                                <div class="totals-scroll">
                                    <div class="row flex-nowrap totals-row">
                                        <div class="col">
                                            <label>Subtotal</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text currency-symbol">₹</span>
                                                </div>
                                                <input type="text" name="subtotal" id="subtotal" class="form-control"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <label>Discount (amt.)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text currency-symbol">₹</span>
                                                </div>
                                                <input type="text" name="discount" id="discount" class="form-control discount"
                                                    value="0">
                                            </div>
                                        </div>

                                        <div class="col">
                                            <label>Tax %</label>
                                            <input type="text" name="tax" id="tax" class="form-control tax" value="0">
                                        </div>

                                        <div class="col">
                                            <label>Tax (amount)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text currency-symbol">₹</span>
                                                </div>
                                                <input type="text" name="tax_amount" class="form-control" id="tax_amount"
                                                    step="0.01" readonly>
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
                                                <input type="text" name="final_total" id="final_total" class="form-control"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- SECTION 8: Terms & Conditions --}}
                                <h4 class="mb-2 text-primary"><b>7. Terms & Conditions</b></h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <textarea name="terms_conditions" id="terms_conditions"
                                            class="form-control summernote" rows="5"
                                            placeholder="Enter Terms & Conditions here...">
                                                                                                                                    <p><strong>GST is extra @ 18%</strong></p>
                                                                                                                                    <p><u><strong>Delivery &amp; Payment: </strong></u></p>
                                                                                                                                    <p>Within 4-5&nbsp;week from the date of your confirmed order along with 60% advance and balance before the time of dispatch at our works</p>
                                                                                                                                    <p style="margin-top: 10px;"><strong>Note :</strong></p>
                                                                                                                                    <p><strong>1. </strong>Our rates are ex-works Kanpur &amp; without electrical motor.</p>
                                                                                                                                    <p><strong>2.</strong> All welding/ cutting other machinery with operator, skilled labor, unskilled labor at the time of installation has to be provided by you</p>
                                                                                                                                    <p><strong>3. </strong>No Civil work, structural work is under our scope. Anything else than mentioned is not in our scope.</p>
                                                                                                                                    <p><strong>4.</strong> You will pay &amp; provide fooding &amp; lodging to our Service Engineer at the time of Fitting at your site.</p>
                                                                                                                                    <p><strong>5.</strong> Any delays in payment and in taking delivery on time, will be charged extra in form of demurrage and penalty</p>
                                                                                                                                    <p><strong>6.</strong> Entire received amount advance or otherwise is nonrefundable. The entire amount is forfeited in case of any dispute.</p>
                                                                                                                                    <p><strong>7.</strong> All disputes are only allowed to be brought under Kanpur jurisdiction.</p>
                                                                                                                                    <p><strong>Warranty:</strong> Full one year from the date of sale against any manufacturing defect.</p>
                                                                                                                                    <p>Thanking you</p>
                                                                                                                                    <p>Yours Faithfully</p>
                                                                                                                                    <p><strong>For Shri Krishna Pulverisers</strong></p>
                                                                                                                                    <div  class="pdf-keep-margin" style="margin-top:50px;">
                                                                                                                                    <p><strong>R.R. Khare</strong></p>
                                                                                                                                </textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <textarea name="hi_terms_conditions" id="hi_terms_conditions"
                                            class="form-control summernote" rows="5"
                                            placeholder="Enter Terms & Conditions here...">
                                                                                                                                    <p><strong>जीएसटी 18% अतिरिक्त है</strong></p>
                                                                                                                                    <p><u><strong>डिलीवरी और भुगतान:</strong></u></p>
                                                                                                                                    <p>आपके पुष्ट ऑर्डर की तारीख से 4-5 सप्ताह के भीतर 60% अग्रिम भुगतान के साथ और शेष राशि हमारे कारखाने से माल भेजने से पहले जमा करनी होगी</p>
                                                                                                                                    <p style="margin-top: 10px;"><strong>नोट:</strong></p>
                                                                                                                                    <p><strong>1.</strong>हमारी दरें कानपुर स्थित कारखाने से हैं और इनमें विद्युत मोटर शामिल नहीं है।</p>
                                                                                                                                    <p><strong>2.</strong>इंस्टॉलेशन के समय वेल्डिंग/कटिंग और अन्य मशीनरी, ऑपरेटर, कुशल श्रमिक और अकुशल श्रमिक आपको स्वयं उपलब्ध कराने होंगे।</p>
                                                                                                                                    <p><strong>3.</strong>सिविल कार्य या संरचनात्मक कार्य हमारे कार्यक्षेत्र में नहीं आते हैं।</p> ऊपर उल्लिखित बातों के अलावा अन्य कोई भी कार्य हमारे कार्यक्षेत्र में नहीं आता है।
                                                                                                                                    <p><strong>4.</strong> आपके स्थान पर फिटिंग के समय हमारे सर्विस इंजीनियर के लिए भोजन और आवास का खर्च और व्यवस्था आपको ही करनी होगी।</p>
                                                                                                                                    <p><strong>5.</strong> भुगतान में देरी और समय पर डिलीवरी न लेने पर विलंब शुल्क और जुर्माना अतिरिक्त रूप से लिया जाएगा।</p>
                                                                                                                                    <p><strong>6.</strong> प्राप्त पूरी राशि, चाहे अग्रिम हो या अन्य, अप्रतिदेय है। किसी भी विवाद की स्थिति में पूरी राशि जब्त कर ली जाएगी।
                                                                                                                                    <p><strong>7.</strong> सभी विवाद केवल कानपुर के अधिकार क्षेत्र में ही लाए जा सकते हैं।
                                                                                                                                    <p><strong>वारंटी:</strong> बिक्री की तारीख से पूरे एक वर्ष तक किसी भी विनिर्माण दोष के विरुद्ध।
                                                                                                                                    <p>धन्यवाद</p> 
                                                                                                                                    <p>भवदीय</p>
                                                                                                                                     <p><strong>श्री कृष्णा पल्वराइज़र्स के लिए</strong>
                                                                                                                                      <div  class="pdf-keep-margin" style="margin-top:50px;"></div>
                                                                                                                                     <p><strong>आर.आर. खरे</strong>
                                                                                                                                </textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success mt-3">Save Quotation</button>
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
    </style>
    <style>
        #extra-mobiles .list-group-item {
            font-size: 13px;
            background: #f8fafc;
        }

        textarea.description {
            resize: none;
            min-height: 38px;
            line-height: 1.4;
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
        const pickers = {};

        function initDatePicker(selector, defaultDate = null) {
            pickers[selector] = flatpickr(selector, {
                dateFormat: "Y-m-d",   // backend format
                altInput: true,
                altFormat: "d/m/Y",    // user sees
                allowInput: true,
                defaultDate: defaultDate
            });
        }

        // Init pickers
        initDatePicker("#quotation_date", "{{ now()->toDateString() }}");
        initDatePicker("#pi_date", "{{ now()->toDateString() }}");

        // Open calendar when icon is clicked
        $(document).on("click", ".calendar-icon", function () {
            const target = $(this).data("target");
            if (pickers[target]) {
                pickers[target].open();
            }
        });
    </script>

    <script>
        function stripHtml(html) {
            let tmp = document.createElement("DIV");
            tmp.innerHTML = html;
            return tmp.textContent || tmp.innerText || "";
        }
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
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let today = new Date().toISOString().split('T')[0];
            document.querySelector('input[name="quotation_date"]').value = today;
            document.querySelector('input[name="pi_date"]').value = today;
        });
    </script>

    <script>
        let MASTER = []; // Store original <option> as DOM nodes

        $(document).ready(function () {

            // Store master item list for unlimited reuse
            $('.item_row:first .item_select option').each(function () {
                MASTER.push($(this).clone());
            });

            initRow($('.item_row:first')); // first row load correct
            calculateRow($('.item_row:first'));   // <<< THIS FIXES YOUR NULL INDEX🔥
            calculateSummary();
        });


        // Initialize a row (Select2 + fresh filtered options)
        function initRow(row) {
            applyFilter(row);
            row.find('.item_select').select2({ width: '100%' });
        }


        // Apply filtering based on type + origin
        function applyFilter(row) {

            let type = row.find('.item_type').val();
            let origin = row.find('.origin').val();
            let select = row.find('.item_select');

            // ✅ Clean existing description
            const existingDesc = stripHtml(
                row.find('.description_en').val()
            );

            select.empty().append('<option value="">-- Select Item --</option>');

            MASTER.forEach(opt => {
                if ($(opt).data('type') === type && $(opt).data('origin') === origin) {
                    select.append($(opt).clone());
                }
            });

            select.select2({ width: '100%' });

            // ✅ Preserve clean text only
            if (!existingDesc) {
                row.find('.description_en')
                    .val('')
                    .attr('data-user-edited', '0');
            } else {
                row.find('.description_en').val(existingDesc);
            }
        }



        // ADD NEW ROW — Unlimited & Safe
        $(document).on('click', '#add_row', function () {

            let newRow = $('.item_row_template').clone();

            newRow.removeClass('item_row_template d-none').show();

            // Enable all first
            newRow.find('input,select,textarea').prop('disabled', false);

            // ❗ FIX: disable calculated fields again
            newRow.find('.total').prop('readonly', true);
            newRow.find('.converted_total').prop('readonly', true);

            // Optional: prevent typing
            newRow.find('.total, .converted_total').css('pointer-events', 'none');

            $('#quotation_items_table tbody').append(newRow);

            calculateRow(newRow);
            initRow(newRow);
            calculateSummary();
        });

        // Refilter when type or origin changes
        $(document).on('change', '.item_type, .origin', function () {
            applyFilter($(this).closest('tr'));
        });


        $(document).on('change', '.item_select', function () {

            const row = $(this).closest('tr');
            const selected = $(this).find(':selected');

            const descEn = selected.data('description') || '';
            const descHi = selected.data('description-hi') || '';
            const nameHi = selected.data('name-hi') || '';

            // 👀 preview clean
            row.find('.description_en').val(cleanDescriptionHtml(descEn));

            // 💾 store HTML
            row.find('.description_en_html').val(descEn);

            // Hindi
            row.find('.description_hi').val(descHi);

            // Hindi name
            row.find('.item_name_hi').val(nameHi);
        });


        // Remove row
        // DELETE ITEM ROW WITH SWEET ALERT
        $(document).on('click', '.remove_row', function () {
            let row = $(this).closest('tr');   // targeted row

            Swal.fire({
                title: "Delete Item?",
                text: "This row will be permanently removed!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, Remove",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    row.remove();        // remove UI row
                    calculateSummary();  // recalc totals

                    Swal.fire({
                        icon: "success",
                        title: "Item Deleted",
                        showConfirmButton: false,
                        timer: 900
                    });
                }
            });
        });
        // ==== CALCULATE TOTAL OF A SINGLE ROW ====
        function calculateRow(row, changedField = 'inr') {

            let qty = parseFloat(row.find('.qty').val()) || 0;
            let rate = parseFloat($('#conversion_rate').val()) || 1;

            let inrPrice = parseFloat(row.find('.unit_price').val()) || 0;
            let convertedPrice = parseFloat(row.find('.converted_price').val()) || 0;

            // 🔁 If INR changed → update converted
            if (changedField === 'inr') {
                convertedPrice = inrPrice / rate;
                row.find('.converted_price').val(convertedPrice.toFixed(2));
            }

            // 🔁 If converted changed → update INR
            if (changedField === 'converted') {
                inrPrice = convertedPrice * rate;
                row.find('.unit_price').val(inrPrice.toFixed(2));
            }

            // Totals
            let totalINR = qty * inrPrice;
            let totalConverted = qty * convertedPrice;

            row.find('.total').val(totalINR.toFixed(2));
            row.find('.converted_total').val(totalConverted.toFixed(2));
        }
        // ===== CALCULATE SUBTOTAL, TAX, DISCOUNT, FINAL TOTAL =====
        $(document).on('input', '.qty, .unit_price', function () {
            calculateRow($(this).closest('tr'));
            calculateSummary();
        });

        // Listen for Discount + Tax update
        $(document).on('input', '#discount, #tax', function () {
            calculateSummary();
        });

        // Recalculate after removing row
        $(document).on('click', '.remove_row', function () {
            setTimeout(() => calculateSummary(), 100);
        });
        // INR price changed
        $(document).on('input', '.unit_price', function () {
            let row = $(this).closest('tr');
            calculateRow(row, 'inr');
            calculateSummary();
        });

        // Converted price changed
        $(document).on('input', '.converted_price', function () {
            let row = $(this).closest('tr');
            calculateRow(row, 'converted');
            calculateSummary();
        });

        // Quantity changed
        $(document).on('input', '.qty', function () {
            let row = $(this).closest('tr');
            calculateRow(row);
            calculateSummary();
        });
    </script>
    <script>
        // ===== CALCULATE SUBTOTAL, TAX, DISCOUNT, FINAL TOTAL =====
        function calculateSummary() {

            let subtotal = 0;
            let currency = $('#currency').val();

            // 🔥 Choose correct column based on currency
            let selector = (currency === 'INR') ? '.total' : '.converted_total';

            $(selector).each(function () {
                subtotal += parseFloat($(this).val()) || 0;
            });

            let discount = parseFloat($('#discount').val()) || 0;
            let taxPercent = parseFloat($('#tax').val()) || 0;

            let afterDiscount = subtotal - discount;
            if (afterDiscount < 0) afterDiscount = 0;

            let taxAmount = (afterDiscount * taxPercent) / 100;
            let finalTotal = afterDiscount + taxAmount;

            // ✅ Set values
            $('#subtotal').val(subtotal.toFixed(2));
            $('#tax_amount').val(taxAmount.toFixed(2));
            $('#final_total').val(finalTotal.toFixed(2));
        }
        $(document).on('input', '#discount', function () {
            let subtotal = parseFloat($('#subtotal').val()) || 0;
            let discount = parseFloat($(this).val()) || 0;

            if (discount > subtotal) {
                Swal.fire('Invalid Discount', 'Discount cannot be greater than Subtotal', 'error');
                $(this).val(0);
                calculateSummary();
            }
        });

        $(document).on('input', '.qty, .unit_price, #discount, #tax', function () {
            calculateRow($(this).closest('tr'));
            calculateSummary();
        });

        $(document).on('click', '#add_row, .remove_row', function () {
            setTimeout(calculateSummary, 150);
        });
        // Recalculate when quantity, price, discount, tax changes
        $(document).on('input', '.qty, .unit_price, #discount, #tax', function () {
            calculateRow($(this).closest('tr'));
            calculateSummary();
        });

        // Recalculate after row removal
        $(document).on('click', '.remove_row', function () {
            setTimeout(function () { calculateSummary(); }, 100);
        });

        // Recalculate after adding a row
        $(document).on('click', '#add_row', function () {
            setTimeout(function () { calculateSummary(); }, 200);
        });
    </script>
    <script>
        // 🔵 ROUND OFF TAX AMOUNT
        $('#round_tax').on('click', function () {

            let taxAmount = parseFloat($('#tax_amount').val()) || 0;
            let roundedTax = Math.round(taxAmount);

            // Set rounded tax
            $('#tax_amount').val(roundedTax.toFixed(2));

            // Recalculate final total using rounded tax
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
                timer: 1500
            });
        });
        $('#tax_amount').on('input', function () {
            let taxAmount = parseFloat($(this).val()) || 0;

            let subtotal = parseFloat($('#subtotal').val()) || 0;
            let discount = parseFloat($('#discount').val()) || 0;

            let afterDiscount = subtotal - discount;
            if (afterDiscount < 0) afterDiscount = 0;

            let finalTotal = afterDiscount + taxAmount;
            $('#final_total').val(finalTotal.toFixed(2));
        });


        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });

    </script>

    <script>
        function getQueryParam(name) {
            return new URLSearchParams(window.location.search).get(name);
        }
    </script>

    <script>
        $(document).ready(function () {

            const preselectedLead = getQueryParam('lead');

            if (preselectedLead) {
                $.ajax({
                    url: "{{ route('ajax.get.single.lead.details', ['company' => $company->id]) }}", // ✅ SINGLE API
                    type: "GET",
                    data: { id: preselectedLead },
                    dataType: 'json',
                    success: function (res) {

                        const primaryPhone = res.phones?.[0] ?? '';

                        const option = new Option(
                            `${res.lead_code} - ${res.customerName} (${primaryPhone})`,
                            preselectedLead,
                            true,
                            true
                        );

                        $('#lead_id').append(option).trigger('change');
                        // Generate preview for preselected lead
                        $.get("{{ route('ajax.generate.quote.number') }}", {
                            company_id: $('#company_id').val(),
                            lead_id: preselectedLead
                        }, function (data) {

                            $('#quote_preview').val(data.quotation);
                            $('#pi_preview').val(data.pi);

                        });

                        // Autofill fields
                        $('#customer_name').val(res.customerName ?? '');
                        $('#email').val(
                            res.email && res.email !== '---' ? res.email : ''
                        );
                        // ---------- MOBILES ----------
                        // ---------- COUNTRY CODE SELECT ----------
                        const phoneCode = res.country?.phonecode ?? '';

                        $('#country_code_select')
                            .empty()
                            .append(`<option value="${phoneCode}">+${phoneCode}</option>`)
                            .val(phoneCode);

                        // ---------- PRIMARY MOBILE ----------
                        if (res.phones && res.phones.length) {
                            $('#mobile').val(res.phones[0]);
                        } else {
                            $('#mobile').val('');
                        }
                        let mobilesHtml = '';

                        if (Array.isArray(res.phones) && res.phones.length) {
                            mobilesHtml = `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <ul class="list-group list-group-sm mt-1">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ${res.phones.map((phone, index) => `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <li class="list-group-item d-flex justify-content-between align-items-center py-1">
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

                        $('#country').val(res.country?.name ?? '');
                        $('#country_code').val(res.country?.phonecode ?? '');   // ✅ FIX
                        $('#state').val(res.state?.name ?? '');
                        $('#city').val(res.city?.name ?? '');
                        $('#address').val(res.address ?? '');

                        $('input[name="gst_number"]').val(res.gst ?? '');       // ✅ FIX

                        $('#lead_id').prop('disabled', true);
                    }

                });
            }

        });
        $('#country_code_select').on('change', function () {
            const code = $(this).val();

            $('#extra-mobiles .list-group-item span:first-child').each(function () {
                const text = $(this).text().replace(/^\+\d+\s/, '');
                $(this).html(`<i class="fa fa-phone text-success mr-1"></i> +${code} ${text}`);
            });
        });

    </script>
    <script>
        $(document).on('input','.discount,.tax,.qty,.unit_price,.modal_rate, .total_price, .converted_price, .converted_total', function () {

            let value = this.value;

            // allow only digits and one decimal point
            value = value.replace(/[^0-9.]/g, '');

            let parts = value.split('.');

            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            this.value = value;
        });
        $('#lead_id').on('select2:select', function (e) {

            const leadId = e.params.data.id;
            if (!leadId) return;
            $.get("{{ route('ajax.generate.quote.number') }}", {
                company_id: $('#company_id').val(),
                lead_id: leadId
            }, function (data) {
                $('#quote_preview').val(data.quotation);
                $('#pi_preview').val(data.pi);
            });
            $.ajax({
                url: "{{ route('ajax.get.single.lead.details', ['company' => $company->id]) }}",
                type: "GET",
                data: { id: leadId },
                dataType: "json",
                success: function (res) {

                    // BASIC
                    $('#customer_name').val(res.customerName ?? '');
                    $('#email').val(res.email ?? '');
                    $('#address').val(res.address ?? '');
                    $('input[name="gst_number"]').val(res.gst ?? '');

                    // COUNTRY CODE
                    const phoneCode = res.country?.phonecode ?? '';
                    $('#country_code_select')
                        .empty()
                        .append(`<option value="${phoneCode}">+${phoneCode}</option>`)
                        .val(phoneCode);

                    $('#country_code').val(phoneCode);

                    // MOBILES
                    $('#mobile').val(res.phones?.[0] ?? '');

                    let mobilesHtml = '';
                    if (Array.isArray(res.phones)) {
                        mobilesHtml = `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <ul class="list-group list-group-sm mt-1">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ${res.phones.map((phone, index) => `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <li class="list-group-item d-flex justify-content-between py-1">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span><i class="fa fa-phone text-success mr-1"></i> +${phoneCode} ${phone}</span>
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
    </script>

    <script>
        $('form').on('submit', function (e) {

            let missing = false;

            $('#quotation_items_table tbody tr').each(function () {

                const row = $(this);

                // skip template or hidden rows
                if (row.hasClass('item_row_template') || !row.is(':visible')) {
                    return;
                }

                const itemId = row.find('.item_select').val();
                const descEn = row.find('.description_en_html').val();
                const descHi = row.find('.description_hi').val() || '';

                // ❌ if item selected but no description
                if (itemId && !descEn.trim() && !descHi.trim()) {
                    missing = true;
                }

            });

            if (missing) {
                e.preventDefault();

                Swal.fire(
                    'Missing Description',
                    'Please add description for all selected items.',
                    'error'
                );

                return false;
            }

            // ✅ IMPORTANT: enable lead_id before submit
            $('#lead_id').prop('disabled', false);

        });

    </script>
    <script>
        $(document).ready(function () {

            $('.description').each(function () {

                let html = $(this).val();

                $(this).attr('data-html', html);

                let text = $('<div>').html(html).text();

                $(this).val(normalizeDescription(text));
            });

        });
        let activeDescriptionField = null;

        let activeRow = null;

        $(document).on('click', '.edit-description', function () {

            activeRow = $(this).closest('tr');

            // 🔥 load ORIGINAL HTML (not cleaned text)
            let enHtml = activeRow.find('.description_en_html').val();
            let hiHtml = activeRow.find('.description_hi').val();

            $('#modalDescriptionEn').summernote('code', enHtml || '');
            $('#modalDescriptionHi').summernote('code', hiHtml || '');

            $('#descriptionModal').modal('show');
        });
        $('#saveDescription').on('click', function () {

            if (!activeRow) return;

            let enHtml = $('#modalDescriptionEn').summernote('code');
            let hiHtml = $('#modalDescriptionHi').summernote('code');

            // 👀 CLEAN FOR PREVIEW ONLY
            let cleanEn = cleanDescriptionHtml(enHtml);

            // 👀 SHOW CLEAN TEXT
            activeRow.find('.description_en').val(cleanEn);

            // 💾 STORE ORIGINAL HTML (IMPORTANT)
            activeRow.find('.description_en_html').val(enHtml);

            // 💾 Hindi (full HTML)
            activeRow.find('.description_hi').val(hiHtml);

            $('#descriptionModal').modal('hide');
        });
    </script>
    <script>
        new Sortable(document.querySelector('#quotation_items_table tbody'), {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function () {
                updateSortOrder();
            }
        });
        function updateSortOrder() {
            $('#quotation_items_table tbody tr').each(function (index) {
                $(this).find('.sort_order').val(index + 1);
            });
        }
        function normalizeDescription(text) {
            if (!text) return '';

            return text
                .replace(/&nbsp;/g, ' ')
                .replace(/\r\n/g, '\n')
                .replace(/\n{2,}/g, '\n')
                .replace(/[ \t]{2,}/g, ' ')
                .trim();
        }
        function cleanDescriptionHtml(html) {

            if (!html) return '';

            let wrapper = document.createElement('div');
            wrapper.innerHTML = html;

            // ❌ Remove style tags
            wrapper.querySelectorAll('style').forEach(el => el.remove());

            // ❌ Remove inline styles
            wrapper.querySelectorAll('*').forEach(el => {
                el.removeAttribute('style');
            });

            // ✅ RETURN CLEAN TEXT (NOT HTML)
            return wrapper.textContent || wrapper.innerText || '';
        }
        $(document).ready(function () {
            let currency = $('#currency').val();

            if (currency === 'INR') {
                $('.cfv-header').text('Total');
            } else {
                $('.cfv-header').text('CFV (₹)');
            }
            updateSortOrder();
        });

    </script>
    <script>
        const fileInput = document.getElementById('fileInput');
        const previewContainer = document.getElementById('filePreview');

        let selectedFiles = [];

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
                    e.stopPropagation();
                    e.preventDefault();
                    removeFile(index);
                };


                // IMAGE PREVIEW
                // IMAGE PREVIEW WITH LINK
                if (file.type.startsWith('image/')) {
                    const url = URL.createObjectURL(file);

                    const link = document.createElement('a');
                    link.href = url;
                    link.target = '_blank';

                    const img = document.createElement('img');
                    img.src = url;

                    link.appendChild(img);
                    preview.appendChild(link);
                }

                // PDF / DOC PREVIEW
                else {
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

        function removeFile(index) {
            selectedFiles.splice(index, 1);

            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));

            fileInput.files = dt.files;
            fileInput.dispatchEvent(new Event('change'));
        }
        $('form').on('submit', function (e) {
            const assignUser = $('[name="assigned_user_id"]').val();

            if (!assignUser) {
                e.preventDefault();
                Swal.fire(
                    'Missing Staff',
                    'Please select an assigned staff member.',
                    'error'
                );
                return false;
            }
        });

    </script>
    <script>
        let CURRENT_CURRENCY = 'INR';

        const currencySymbols = {
            INR: '₹',
            USD: '$',
            EUR: '€'
        }; function updateCurrencyUI(currency) {

            CURRENT_CURRENCY = currency;
            let symbol = currencySymbols[currency] || '';

            // ✅ Update table input symbols
            $('.currency-symbol').text(symbol);

            // ✅ Update totals section


            // ✅🔥 UPDATE TABLE HEADERS
            $('.price-header').text(`Price (${symbol})`);
            $('.total-header').text(`Total (${symbol})`);
        }
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
        $('#currency').on('change', function () {
            let currency = $(this).val();
            updateCurrencyUI(currency);

            calculateSummary();
            if (currency !== 'INR') {
                $('#currencyModal').modal('show');
                $('.cfv-header').text('CFV (₹)');
                $('#edit_rate').prop('disabled', false);
            } else {
                $('#conversion_rate').val(1);
                $('.cfv-header').text('Total');
                toggleConvertedColumns(false);
                $('#edit_rate').prop('disabled', true);
            }
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

                toggleConvertedColumns(true);
                updateCurrencyLabels($('#currency').val());

                // 🔥 RECALCULATE ALL ROWS (VERY IMPORTANT)
                $('#quotation_items_table tbody tr').each(function () {

                    let row = $(this);

                    let qty = parseFloat(row.find('.qty').val()) || 0;
                    let inr = parseFloat(row.find('.unit_price').val()) || 0;

                    let converted = inr / rate;

                    row.find('.converted_price').val(converted.toFixed(2));
                    row.find('.converted_total').val((converted * qty).toFixed(2));

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
        $('#edit_rate').on('click', function () {

            let currentRate = $('#conversion_rate').val();

            // Pre-fill modal input
            $('#modal_rate').val(currentRate);

            $('#currencyModal').modal('show');
        });
    </script>
@endpush