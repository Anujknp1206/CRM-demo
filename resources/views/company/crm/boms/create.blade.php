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
                        <li class="breadcrumb-item active"><a href="{{ route('boms.index', ['company' => $company->id]) }}">
                                BOM List</a></li>
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

                                <a href="{{ route('boms.index', ['company' => $company->id]) }}"
                                    class="btn btn-sm btn-success">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="bomForm" autocomplete="off">
                                @csrf

                                <!-- 🔥 1. SELECT ORDER + BASIC DETAILS -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <b>1. Select Order & BOM Details</b>
                                    </div>

                                    <div class="card-body">

                                        <!-- TOP ROW -->
                                        <div class="row mb-3">

                                            <!-- ORDER -->
                                            <div class="col-md-12">
                                                <label>Select Order *</label>
                                                <select id="order_id" name="order_id" class="form-control"></select>
                                            </div>




                                        </div>

                                        <!-- SECOND ROW -->
                                        <div class="row">
                                            <!-- BOM NUMBER -->
                                            <div class="col-md-3">
                                                <label>BOM Number</label>
                                                <input type="text" name="bom_number" id="bom_number"
                                                    value="Select Order First" class="form-control" readonly>
                                            </div>
                                            <!-- DEPARTMENT (INCHARGE) -->
                                            <div class="col-md-3">
                                                <label>Department (Incharge)</label>
                                                <select name="incharge_department_id" id="incharge_department_id"
                                                    class="form-control" required>
                                                    <option value="">Select</option>
                                                    @foreach($departments as $d)
                                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                                    @endforeach
                                                    <option value="add_new">➕ Add New</option>
                                                </select>
                                            </div>

                                            <!-- SUPERVISOR -->
                                            <div class="col-md-3">
                                                <label>BOM Incharge</label>
                                                <select name="supervisor_id" id="supervisor_id" class="form-control"
                                                    required>
                                                    <option value="">Select</option>
                                                    <option value="">Select Department First</option>
                                                    <option value="add_new">➕ Add New</option>
                                                </select>
                                            </div>


                                            <!-- SHIFT -->
                                            <div class="col-md-3">
                                                <label>Shift</label>
                                                <select name="shift_id" id="shift_id" class="form-control" required>
                                                    <option value="">Select</option>

                                                    @foreach($shifts as $s)
                                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                    @endforeach

                                                    <option value="add_new">➕ Add New</option>
                                                </select>
                                            </div>


                                        </div>

                                        <!-- THIRD ROW -->
                                        <div class="row mt-3">
                                            <!-- PRIORITY -->
                                            <div class="col-md-3">
                                                <label>Priority</label>
                                                <select name="priority_id" id="priority_id" class="form-control" required>
                                                    <option value="">Select</option>

                                                    @foreach($priorities as $p)
                                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                    @endforeach

                                                    <option value="add_new">➕ Add New</option>
                                                </select>
                                            </div>

                                            <!-- REVIEW DEPARTMENT -->
                                            <div class="col-md-3">
                                                <label>Department (Review)</label>
                                                <select name="review_department_id" id="review_department_id"
                                                    class="form-control" required>
                                                    <option value="">Select</option>
                                                    @foreach($departments as $d)
                                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                                    @endforeach
                                                    <option value="add_new">➕ Add New</option>
                                                </select>
                                            </div>

                                            <!-- CHECKED BY -->
                                            <div class="col-md-3">
                                                <label>Checked By</label>
                                                <select name="checked_by" id="checked_by" class="form-control" required>
                                                    <option value="">Select</option>
                                                    <option value="">Select Department First</option>
                                                    <option value="add_new">➕ Add New</option>
                                                </select>
                                            </div>

                                            <!-- DELIVERY DATE -->
                                            <div class="col-md-3">
                                                <label>Delivery Date</label>

                                                <div class="input-group">
                                                    <input type="text" name="delivery_date" id="delivery_date"
                                                        class="form-control" required>

                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="calendar_icon"
                                                            style="cursor:pointer;">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                                <!-- 🔥 ORDER ITEMS -->
                                <div class="card mt-3" id="items_box" style="display:none;">
                                    <div class="card-header bg-light">
                                        <b>Order Items</b>
                                    </div>

                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th width="50">#</th>
                                                    <th>Item</th>
                                                    <th width="80">Qty</th>
                                                    <th width="100">BOM</th>
                                                </tr>
                                            </thead>
                                            <tbody id="items_table_body"></tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- 🔥 2. BOM DETAILS -->
                                <div class="card mb-3">

                                    <div class="card-header bg-light">
                                        <b>2. BOM Details</b>
                                    </div>

                                    <div class="card-body">

                                        <div class="row">

                                            <!-- LEFT REMARK -->
                                            <div class="col-md-6">

                                                <label>Remark</label>

                                                <textarea name="remarks" id="remarks"
                                                    class="form-control summernote"></textarea>

                                            </div>

                                            <!-- RIGHT REMARK -->
                                            <div class="col-md-6">

                                                <label>Hindi Remark</label>

                                                <textarea name="hi_remarks" id="hi_remarks"
                                                    class="form-control summernote"></textarea>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <!-- 🔥 ITEMS -->
                                <div id="items_section"></div>

                                <button class="btn btn-success">Save BOM</button>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="employeeModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <form id="employeeForm" class="modal-content">

                <div class="modal-header" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
                    <h5 class=" text-white">Add Employee</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <input type="text" id="emp_first_name" class="form-control mb-2" placeholder="First Name" required>
                    <input type="text" id="emp_last_name" class="form-control mb-2" placeholder="Last Name" required>
                    <input type="text" id="emp_mobile" class="form-control mb-2" placeholder="Mobile" required>

                    <select id="emp_department_id" class="form-control">
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Save</button>
                </div>

            </form>
        </div>
    </div>

    <div class="modal fade" id="missingRecipeModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
                    <h5 class="modal-title">⚠ Missing Recipes</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <p>These items don't have recipes:</p>
                    <ul id="missingItemsList"></ul>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('recipes.create', $company) }}" target="_blank" class="btn btn-success">
                        Create Recipe
                    </a>
                    <button class="btn btn-danger" data-dismiss="modal">
                        Ignore
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .toggle-icon i {
            transition: transform 0.2s ease;
        }

        /* OPEN STATE */
        .toggle-icon[aria-expanded="true"] i {
            transform: rotate(90deg);
        }

        .card-header .form-control,
        .card-header .select2-container {
            max-width: 300px;
        }

        .gap-2 {
            gap: 10px;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const orderItems = @json($orderItems);
    </script>
    <script>
        function initSelect2(context = document) {

            function applySelect2(el, options = {}) {
                el.each(function () {

                    let $this = $(this);

                    // 🔥 detect modal parent
                    let modalParent = $this.closest('.modal');

                    let dropdownParent = modalParent.length ? modalParent : $('body');

                    // destroy if already initialized
                    if ($this.hasClass("select2-hidden-accessible")) {
                        $this.select2('destroy');
                    }

                    $this.select2({
                        width: '100%',
                        dropdownParent: dropdownParent, // ✅ AUTO FIX
                        ...options
                    });
                });
            }
            $(context).find('.item_id').each(function () {

                let $this = $(this);

                let modalParent = $this.closest('.modal');

                let dropdownParent = modalParent.length
                    ? modalParent
                    : $('body');

                // destroy old
                if ($this.hasClass("select2-hidden-accessible")) {

                    $this.select2('destroy');

                }

                $this.select2({

                    width: '100%',

                    dropdownParent: dropdownParent,

                    placeholder: "Search Item",

                    minimumInputLength: 1,

                    ajax: {

                        url: "{{ route('parts.searchItems', ['company' => $company->id]) }}",

                        dataType: 'json',

                        delay: 250,

                        data: function (params) {

                            return {
                                search: params.term
                            };

                        },

                        processResults: function (data) {

                            // add add_new manually
                            data.push({
                                id: 'add_new',
                                text: '➕ Add New'
                            });

                            return {
                                results: data
                            };

                        },

                        cache: true
                    }

                });

            });
            // DEPARTMENT
            applySelect2($(context).find('.department_id, #incharge_department_id, #review_department_id'), {
                placeholder: "Select Department"
            });

            // EMPLOYEE
            applySelect2($(context).find('.employee_id, #supervisor_id, #checked_by'), {

                placeholder: "Select Employee",
            });
            // SHIFT
            // 🔥 MAIN SHIFT (CREATE PAGE)
            applySelect2($(context).find('#shift_id'), {
                placeholder: "Select Shift"
            });

            // 🔥 ROW SHIFT (MODAL)
            applySelect2($(context).find('.shift_id'), {
                placeholder: "Select Shift"
            });

            // PRIORITY
            applySelect2($(context).find('#priority_id'), {
                placeholder: "Select Priority"
            });


            // SPEC
            applySelect2($(context).find('.spec_id'));
        }
        $(document).ready(function () {
            initSelect2(); // 🔥 APPLY TO STATIC FIELDS
        });
        let currentShiftSelect = null;
        let currentUnitSelect = null;
        let currentCategorySelect = null;
        let currentSubcategorySelect = null;
        let currentConditionSelect = null;
        let currentItemSelect = null;
        let currentSpecSelect = null;
        let partIndex = 0;
        let shiftsList = @json($shifts);
        let departmentsList = @json($departments);
        let employeesList = @json($employees);
    </script>
    <script>

        // ORDER SEARCH
        $('#order_id').select2({
            placeholder: "Search Order...",
            width: '100%',
            ajax: {
                url: "{{ route('ajax.orders.search', ['company' => $company->id]) }}",
                dataType: 'json',
                delay: 300,
                data: params => ({ search: params.term }),
                processResults: data => ({
                    results: data.map(item => ({
                        id: item.id,
                        text: item.order_number, // required
                        order_number: item.order_number,
                        customer_name: item.customer_name,
                        mobile: item.mobile,
                        delivery_date: item.delivery_date
                    }))
                })
            },

            // 🔥 DROPDOWN DESIGN
            templateResult: function (item) {

                if (!item.id) return item.text;

                return $(`<div>
                                                                                                                                                                                                <b>${item.order_number}</b><br>
                                                                                                                                                                                                <small>
                                                                                                                                                                                                👤 ${item.customer_name || '-'} |
                                                                                                                                                                                                📞 ${item.mobile || '-'} <br>
                                                                                                                                                                                                📅 ${item.delivery_date || '-'}
                                                                                                                                                                                                </small>
                                                                                                                                                                                                </div>
                                                                                                                                                                                                `);
            },

            // 🔥 SELECTED VALUE
            templateSelection: function (item) {
                return item.order_number || item.text;
            }
        });

        $('#order_id').on('select2:select', function (e) {
            let data = e.params.data;
            let orderId = data.id;

            if (!orderId) return;
            const filteredItems = orderItems.filter(item => item.order_id == orderId);

            let missing = [];

            filteredItems.forEach(item => {

                if (item.machine && !item.machine.recipe) {
                    missing.push(item.item_name);
                }
                else if (item.component && !item.component.recipe) {
                    missing.push(item.item_name);
                }
                else if (item.item && !item.item.recipe) {
                    missing.push(item.item_name);
                }
            });

            // ✅ remove duplicates
            missing = [...new Set(missing)];

            if (missing.length > 0) {

                let list = document.getElementById('missingItemsList');
                list.innerHTML = '';

                missing.forEach(name => {
                    list.innerHTML += `<li>${name}</li>`;
                });

                $('#missingRecipeModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                $('#missingRecipeModal').modal('show');
            }
            let firstMissingItem = filteredItems.find(item =>
                (item.machine && !item.machine.recipe) ||
                (item.component && !item.component.recipe) ||
                (item.item && !item.item.recipe)
            );

            if (firstMissingItem) {

                let baseUrl = "{{ route('recipes.create', $company) }}";

                let createUrl = baseUrl + '?order_item_id=' + firstMissingItem.id;

                // ✅ UPDATE BUTTON LINK
                $('#missingRecipeModal a.btn-success').attr('href', createUrl);
            }
            /*
            🔥 ADD THIS BLOCK (BOM NUMBER UPDATE)
            */
            $.get("{{ route('ajax.generate.bom.number', ['company' => $company->id]) }}", {
                order_id: orderId
            }, function (res) {
                $('#bom_number').val(res.bom);
            });
            if (!orderId) return;
            let deliveryPicker = flatpickr("#delivery_date", {
                dateFormat: "d/m/Y",
                minDate: "today",
                clickOpens: false // 🔥 IMPORTANT
            });

            // =====================================
            // 🔥 COMMON FUNCTION
            // =====================================

            function openDeliveryCalendar() {

                let orderId = $('#order_id').val();

                if (!orderId) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Select Order First'
                    });

                    return;
                }

                $.ajax({

                    url: "{{ route('ajax.order.delivery.date', ['company' => $company->id]) }}",

                    type: "GET",

                    data: {
                        order_id: orderId
                    },

                    success: function (res) {

                        if (!res.success) {

                            Swal.fire({
                                icon: 'error',
                                title: 'Order Not Found'
                            });

                            return;
                        }

                        let today = new Date();

                        let deliveryDate = new Date(res.delivery_date);

                        // remove time
                        today.setHours(0, 0, 0, 0);
                        deliveryDate.setHours(0, 0, 0, 0);

                        // =====================================
                        // 🔥 EXPIRED DELIVERY DATE
                        // =====================================

                        if (deliveryDate < today) {

                            Swal.fire({

                                icon: 'warning',

                                title: 'Delivery Date Expired',

                                html: `
                            Order delivery date has already passed.<br><br>
                            Please update delivery date first.
                        `,

                                showCancelButton: true,

                                confirmButtonText: 'Edit Order',

                                cancelButtonText: 'Close'

                            }).then((result) => {

                                if (result.isConfirmed) {

                                    window.open(res.edit_url, '_blank');

                                }

                            });

                            return;
                        }

                        // =====================================
                        // 🔥 VALID DATE
                        // =====================================

                        deliveryPicker.set('maxDate', deliveryDate);

                        deliveryPicker.open();
                    }

                });

            }

            // =====================================
            // 🔥 BOTH EVENTS
            // =====================================

            $('#calendar_icon').on('click', function () {

                openDeliveryCalendar();

            });

            $('#delivery_date').on('click', function () {

                openDeliveryCalendar();

            });
            // 🔥 SET MAX DATE FROM ORDER
            if (data.delivery_date) {

                // convert to JS date (important)
                let maxDate = new Date(data.delivery_date);

                deliveryPicker.set('maxDate', maxDate);
            }
            $('#customer_box').show();
            $('#cust_name').text(data.customer_name || '-');
            $('#cust_mobile').text(data.mobile || '-');
            $('#cust_delivery').text(data.delivery_date || '-');

            // 🔥 FETCH ORDER ITEMS
            $.get("{{ route('ajax.order.items.bom', ['company' => $company->id]) }}", {
                order_id: orderId
            }, function (res) {

                $('#items_box').show();

                // ✅ show order remark
                if (res.order_remark) {
                    $('#remarks').summernote('code', res.order_remark);
                }

                let html = '';

                res.items.forEach((item, index) => {

                    html += `
                                                                                                                                                                                            <tr><td>${index + 1}</td>

                                                                                                                                                                                            <td>
                                                                                                                                                                                            <b>${item.name}</b><br>
                                                                                                                                                                                            <small class="text-muted">
                                                                                                                                                                                            ${item.description ?? '-'}
                                                                                                                                                                                            </small>
                                                                                                                                                                                            </td>
                                                                                                                                                                                            <td>${item.quantity}</td>
                                                                                                                                                                                            <td>
                                                                                                                                                                                            ${item.has_bom
                            ? '<span class="badge badge-success"><i class="fa fa-check"></i></span>'
                            : `<button type="button" 
                                                                                                                                                                                                 class="btn btn-sm btn-success open-bom-modal"
                                                                                                                                                                                                 data-id="${item.id}">
                                                                                                                                                                                                 <i class="fa fa-plus"></i>
                                                                                                                                                                                            </button>`}
                                                                                                                                                                                            </td></tr>`;
                });

                $('#items_table_body').html(html);
            });
        });
        $(document).on('click', '.view-spec', function () {

            let fullDesc = $(this).data('description') || '';

            $('#modalSpec').summernote('code', fullDesc);

            $('#specModal').modal('show');

        });

        let bomItemsData = {};
        let loadedRecipes = [];
        // 🔥 OPEN MODAL
        $(document).on('click', '.open-bom-modal', function () {

            let orderItemId = $(this).data('id');

            let desc = $(this).closest('tr').find('td:eq(1)').text().trim();
            let words = desc.split(/\s+/);

            if (words.length > 20) {
                desc = words.slice(0, 20).join(' ') + '...';
            }

            $('#modal_order_item_id').val(orderItemId);
            $('#bom_item_title').text(desc);
            $('#bomPartsWrapper').html('');
            partIndex = 0;
            $.get("{{ route('specifications', $company) }}", function (specs) {
                window.allSpecifications = specs;
                // 🔥 FETCH RECIPES FIRST
                $.get("{{ route('recipes.by.order.item', $company) }}",
                    { order_item_id: orderItemId },
                    function (res) {

                        let recipes = res.recipes;
                        loadedRecipes = recipes;

                        let hasSaved = bomItemsData[orderItemId];

                        // =========================================
                        // 🔥 CASE 1: BOM ALREADY SAVED
                        // =========================================
                        if (hasSaved) {

                            // 👉 MULTIPLE RECIPES → ASK USER
                            if (recipes.length > 1) {

                                Swal.fire({
                                    icon: 'question',
                                    title: 'Change Recipe?',
                                    html: `
                                                                                                                                            You already filled BOM for this item.<br><br>
                                                                                                                                            <b>If you change recipe, previous data will be lost.</b>
                                                                                                                                        `,
                                    showCancelButton: true,
                                    confirmButtonText: 'Yes, Change Recipe',
                                    cancelButtonText: 'No, Continue'
                                }).then((result) => {

                                    if (result.isConfirmed) {

                                        // 🔥 CLEAR OLD DATA
                                        delete bomItemsData[orderItemId];

                                        showRecipePicker(recipes);

                                    } else {

                                        // 🔥 LOAD SAVED DATA
                                        currentRecipeId = hasSaved.recipe_id;

                                        loadRecipeItemsIntoBom({
                                            parts: hasSaved.parts
                                        });

                                        $('#bomItemModal').modal('show');
                                    }

                                });

                                return; // 🚨 STOP FLOW
                            }

                            // 👉 SINGLE RECIPE → DIRECT LOAD SAVED
                            currentRecipeId = hasSaved.recipe_id;

                            loadRecipeItemsIntoBom({
                                parts: hasSaved.parts
                            });

                            $('#bomItemModal').modal('show');

                            return;
                        }

                        // =========================================
                        // 🔥 CASE 2: FIRST TIME OPEN
                        // =========================================

                        if (recipes.length === 0) {

                            Swal.fire({
                                icon: 'warning',
                                title: 'Recipe Missing',
                                html: `No recipe found for this item.<br>Please create recipe first.`,
                                showCancelButton: true,
                                confirmButtonText: 'Create Recipe',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {

                                if (result.isConfirmed) {

                                    let baseUrl = "{{ route('recipes.create', $company) }}";

                                    window.open(
                                        `${baseUrl}?order_item_id=${orderItemId}`,
                                        '_blank'
                                    );
                                }
                            });

                            return;
                        }

                        // 👉 SINGLE RECIPE
                        if (recipes.length === 1) {

                            currentRecipeId = recipes[0].id;

                            loadRecipeItemsIntoBom(recipes[0]);

                            $('#bomItemModal').modal('show');
                            return;
                        }

                        // 👉 MULTIPLE RECIPES
                        showRecipePicker(recipes);
                    }
                );
            });
        });
        $.get("{{ route('shifts.get', $company) }}", function (shifts) {

            window.allShifts = shifts;

        });
        function showRecipePicker(recipes) {

            let html = '';

            recipes.forEach(r => {
                html += `<option value="${r.id}">${r.name}</option>`;
            });

            $('#recipe_picker').html(html);

            $('#recipePickerModal').modal('show');
        } $(document).on('click', '#loadRecipeBtn', function () {

            let recipeId = $('#recipe_picker').val();

            if (!recipeId) {
                Swal.fire('Please select recipe');
                return;
            }

            // 🔥 FIND FROM MEMORY (NO API CALL)
            let recipe = loadedRecipes.find(r => r.id == recipeId);

            if (!recipe) {
                Swal.fire('Recipe not found');
                return;
            }
            currentRecipeId = $('#recipe_picker').val();
            loadRecipeItemsIntoBom(recipe);

            $('#recipePickerModal').modal('hide');
            $('#bomItemModal').modal('show');
        });
        $('#specsModal').on('show.bs.modal', function () {

            $('#specForm')[0].reset();

            $('#spec_id').val('');

        });
        function loadRecipeItemsIntoBom(recipe) {

            $('#bomPartsWrapper').html('');
            partIndex = 0;

            if (!recipe || !recipe.parts) {
                addBomPart();
                return;
            }

            recipe.parts.forEach(function (part) {

                addBomPart({

                    part_name: part.part_name || part.name || '',

                    hi_part_name: part.hi_part_name || part.hi_name || '',

                    spec_id: part.spec_id || '',

                    shift_id: part.shift_id || '',

                    weightage: part.weightage || part.pivot?.weightage || 0,

                    sort_order: part.sort_order || ''

                });

                let partIdx = partIndex - 1;

                if (part.items && part.items.length > 0) {

                    part.items.forEach(function (item) {

                        addBomRow(partIdx, {
                            item_id: item.item_id,
                            item_name: item.item_name || item.item?.name || '',
                            department_id: item.department_id,
                            employee_id: item.employee_id,
                            quantity: item.quantity,
                            notes: item.notes,
                            hi_notes: item.hi_notes || '',
                        });

                    });

                } else {
                    addBomRow(partIdx);
                }

            });
        }
        function addBomPart(prefill = null) {
            let specOptions = '';

            window.allSpecifications.forEach(spec => {

                specOptions += `
                                                                                                        <option value="${spec.id}">
                                                                                                            ${spec.name}
                                                                                                        </option>
                                                                                                    `;

            });
            let shiftOptions = '';

            window.allShifts.forEach(shift => {

                shiftOptions += `
                                                                                        <option value="${shift.id}">
                                                                                            ${shift.name}
                                                                                        </option>
                                                                                    `;

            });
            let html = `
                                                                                                                                                                                <div class="card mb-2" id="bom_part_${partIndex}">

                                                                                                                                                                                    <div class="card-header d-flex justify-content-between align-items-center">

                                                                                                                                                                                        <div class="d-flex align-items-center" style="gap:10px; width:100%;">
                                                      <input type="text"
                                                               class="form-control part_sort_order"
                                                               placeholder="sort"
                                                               min="1"
                                                               style="width:80px">
                                                                                                                                                                                            <button class="btn btn-link p-0 toggle-icon"
                                                            data-toggle="collapse"
                                                            data-target="#collapse_${partIndex}"
                                                            aria-expanded="true">

                                                        <i class="fa fa-chevron-right"></i>

                                                    </button>

                                                                                                                                                                                            <input type="text" class="form-control form-control part_name w-50"
                                                                                                                                                                                                placeholder="Part Name">
                                                                                                                                                                                                <input type="text" class="form-control hi_part_name w-50"  placeholder="कलपुर्जों के नाम">
                                                                                                      <input type="text" 
                                                                                                                    class="form-control part_weightage"
                                                                                                                    placeholder="Wt"
                                                                                                                    min="1" max="10" step="1"oninput="if (this.value > 10) this.value = 10; if (this.value < 0) this.value = 0;"
                                                                                                                    style="width:80px" required>

                                                                                                                <!-- OR if you want hidden -->
                                                                                                                <input type="hidden" class="part_weightage_hidden">
                                                                                                                                                                                            <select class="form-control form-control spec_id">
                                                                                                                                                                                                <option value="">Spec</option>
                                                                                                                                                                                                 ${specOptions}
                                                                                                                                                                                                <option value="add_new">➕ Add New</option>
                                                                                                                                                                                            </select>
                                                                                                                                                                                        </div>

                                                                                                                                                                                     <div class="card-header d-flex justify-content-between align-items-center gap-2">
                                                                                                                                                                                           <button class="btn btn-success btn-sm add-part-after" data-part="${partIndex}" title="Add Part">
                                                                                                                                                            <i class="fa fa-plus"></i>
                                                                                                                                                        </button>

                                                                                                                                                        <!-- REMOVE PART -->
                                                                                                                                                        <button class="btn btn-danger btn-sm remove-part-btn" data-part="${partIndex}" title="Remove Part">
                                                                                                                                                            <i class="fa fa-times"></i>
                                                                                                                                                        </button>
                                                                                                                                                                                        </div>

                                                                                                                                                                                    </div>

                                                                                                                                                                                    <div id="collapse_${partIndex}" class="collapse show">
                                                                                                                                                                                        <div class="card-body p-0">

                                                                                                                                                                                            <table class="table table-bordered table-sm mb-0">
                                                                                                                                                                                               <thead>
                                                                                                                                                        <tr>
                                                                                                                                                            <th>Item</th>
                                                                                                                                                            <th>Department</th>
                                                                                                                                                            <th>Employee</th>
                                                                                                                                                            <th>Qty</th>
                                                                                                                                                            <th>Notes</th>
                                                                                                                                                            <th width="60" class="text-center">
                                                                                                                                                                <button type="button" class="btn btn-success btn-sm add-item-btn" data-part="${partIndex}">
                                                                                                                                                                    <i class="fa fa-plus"></i>
                                                                                                                                                                </button>
                                                                                                                                                            </th>
                                                                                                                                                        </tr>
                                                                                                                                                    </thead>
                                                                                                                                                                                                <tbody id="bom_rows_${partIndex}"></tbody>
                                                                                                                                                                                            </table>

                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>

                                                                                                                                                                                </div>
                                                                                                                                                                                `;

            $('#bomPartsWrapper').append(html);

            let partBox = $(`#bom_part_${partIndex}`);

            initSelect2(partBox);

            // 🔥 PREFILL (IMPORTANT)
            if (prefill) {
                partBox.find('.part_name').val(prefill.part_name).attr('data-original', prefill.part_name);
                partBox.find('.hi_part_name').val(prefill.hi_part_name || '');
                partBox.find('.spec_id').val(prefill.spec_id);
                partBox.find('.shift_id').val(prefill.shift_id);
                partBox.find('.part_weightage').val(prefill.weightage || 0);
                partBox.find('.part_sort_order').val(prefill.sort_order || '');
            }

            partIndex++;
        }
        $(document).on('click', '.add-item-btn', function () {
            let partIndex = $(this).data('part');
            addBomRow(partIndex);
        });// REMOVE PART
        $(document).on('click', '.remove-part-btn', function () {
            let partIndex = $(this).data('part');
            $(`#bom_part_${partIndex}`).remove();
        });

        // ADD PART BELOW CURRENT
        $(document).on('click', '.add-part-after', function () {

            let currentPart = $(this).closest('.card');

            let newPartHtmlIndex = partIndex; // use global index

            addBomPart();

            let newPart = $(`#bom_part_${newPartHtmlIndex}`);

            currentPart.after(newPart);
        });
        function loadBomRowsFromData(data) {
            $('#bom_rows').html('');
            data.forEach(row => {
                addBomRow();
                let tr = $('#bom_rows tr:last');
                tr.find('.item_id').val(row.item_id);
                tr.find('.department_id').val(row.department_id);
                tr.find('.shift_id').val(row.shift_id);
                tr.find('.qty').val(row.quantity);
                tr.find('.description_data').val(row.notes);

                // 🔥 SPEC
                let specId = Object.keys(row.specifications || {})[0];
                tr.find('.spec_id').val(specId);

                // 🔥 LOAD EMPLOYEE AFTER DEPT
                loadEmployees(
                    row.department_id,
                    tr.find('.employee_id'),
                    row.employee_id
                );

                setTimeout(() => {
                    tr.find('.employee_id').val(row.employee_id);
                }, 300);
            });
        }
        function addBomRow(partIndex, prefill = null) {

            let deptOptions = `<option value="">Select</option>`;
            departmentsList.forEach(d => {
                deptOptions += `<option value="${d.id}">${d.name}</option>`;
            });
            deptOptions += `<option value="add_new">➕ Add New</option>`;

            let empOptions = `<option value="">Select</option>`;
            employeesList.forEach(e => {
                empOptions += `<option value="${e.id}">
                                                                                                                                                                                    ${e.first_name} ${e.last_name}
                                                                                                                                                                                </option>`;
            });
            empOptions += `<option value="add_new">➕ Add New</option>`;

            let row = `
                                                                                                                                                                            <tr>

                                                                                                                                                                                <td>
                                                                                                                                                                                    <select class="form-control item_id">
                                                                    <option value="">Search Item</option>
                                                                </select>
                                                                                                                                                                                </td>

                                                                                                                                                                                <td>
                                                                                                                                                                                    <select class="form-control department_id" required>
                                                                                                                                                                                        ${deptOptions}
                                                                                                                                                                                    </select>
                                                                                                                                                                                </td>

                                                                                                                                                                                <td>
                                                                                                                                                                                    <select class="form-control employee_id" required>
                                                                                                                                                                                        ${empOptions}
                                                                                                                                                                                    </select>
                                                                                                                                                                                </td>

                                                                                                                                                                                <td>
                                                                                                                                                                                    <input type="text" class="form-control qty" value="1" min="0" step="1"oninput="if(this.value < 0) this.value = 1;">
                                                                                                                                                                                </td>

                                                                                                                                                                                <td>
                                                                                                                                                                                    <input type="text" class="form-control notes">
                                                                                                                                                                                    <input  type="text" class="form-control hi_notes">
                                                                                                                                                                                </td>

                                                                                                                                                                                <td>
                                                                                                                                                                                    <button class="btn btn-danger btn-sm remove-row">
                                                                                                                                                                                        <i class="fa fa-times"></i>
                                                                                                                                                                                    </button>
                                                                                                                                                                                </td>

                                                                                                                                                                            </tr>
                                                                                                                                                                            `;

            $(`#bom_rows_${partIndex}`).append(row);

            let newRow = $(`#bom_rows_${partIndex} tr:last`);

            initSelect2(newRow);

            // 🔥 PREFILL SUPPORT
            if (prefill) {

                if (prefill.item_id) {

                    let itemSelect = newRow.find('.item_id');

                    // avoid duplicate option
                    if (
                        itemSelect.find(`option[value="${prefill.item_id}"]`).length === 0
                    ) {

                        let option = new Option(
                            prefill.item_name || 'Selected Item',
                            prefill.item_id,
                            true,
                            true
                        );

                        itemSelect.append(option).trigger('change');

                    }

                    itemSelect.val(prefill.item_id).trigger('change.select2');
                }

                newRow.find('.hi_notes').val(prefill.hi_notes || '');
                newRow.find('.department_id').val(prefill.department_id);

                loadEmployees(prefill.department_id, newRow.find('.employee_id'), prefill.employee_id);

                newRow.find('.qty').val(prefill.quantity);
                newRow.find('.notes').val(prefill.notes);
            }
        }
        $('#bomItemModal').on('shown.bs.modal', function () {
            initSelect2(this);
        });
        $(document).on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
        }); let currentSpecRow = null;
        $(document).on('change', '.item_id', function () {
            if ($(this).val() === 'add_new') {
                currentItemSelect = $(this);
                $(this).val('');
                $('#itemModal').modal('show');
            }
        }); $(document).on('change', '.spec_id', function () {
            if ($(this).val() === 'add_new') {
                currentSpecSelect = $(this);
                $(this).val('');
                $('#specsModal').modal('show');
            }
        }); $(document).on('change', '.shift_id', function () {
            if ($(this).val() === 'add_new') {
                currentShiftSelect = $(this);
                $(this).val('');
                $('#shiftModal').modal('show');
            }
        });
        $(document).on('click', '.open-spec-modal', function () {

            currentSpecRow = $(this).closest('tr');

            let existing = currentSpecRow.find('.spec_data').val();

            $('#modalSpec').summernote('code', existing || '');

            $('#specModal').modal('show');
        });
        $('#saveSpec').click(function () {

            let specHtml = $('#modalSpec').summernote('code');

            if (currentSpecRow) {
                currentSpecRow.find('.spec_data').val(specHtml);
            }

            $('#specModal').modal('hide');
        });
        let currentRecipeId = null; // 🔥 GLOBAL (set when recipe selected)

        function saveBomItems() {

            let orderItemId = $('#modal_order_item_id').val();

            let parts = [];

            let isValid = true;

            // 🔥 LOOP ALL PARTS
            $('[id^="bom_part_"]').each(function () {
                let partBox = $(this);
                let partIndex = partBox.attr('id').split('_')[2];
                let partName = partBox.find('.part_name').val();
                let weightage = partBox.find('.part_weightage').val();
                if (!partName || partName.trim() === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Part Name Required'
                    });
                    partBox.find('.part_name').focus();
                    isValid = false;
                    return false;
                }
                if (!weightage || parseFloat(weightage) <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Weightage Required'
                    });
                    partBox.find('.part_weightage').focus();
                    isValid = false;
                    return false;
                }
                let part = {
                    part_name: partName,
                    hi_part_name: partBox.find('.hi_part_name').val(),
                    spec_id: partBox.find('.spec_id').val(),
                    shift_id: partBox.find('.shift_id').val(),
                    weightage: weightage,
                    sort_order: partBox.find('.part_sort_order').val(),
                    items: []
                };
                partBox.find(`#bom_rows_${partIndex} tr`).each(function () {
                    let row = $(this);
                    let itemSelect = row.find('.item_id');
                    let department = row.find('.department_id').val();
                    let employee = row.find('.employee_id').val();
                    let qty = row.find('.qty').val();
                    if (!itemSelect.val()) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Item Required'
                        });
                        itemSelect.focus();
                        isValid = false;
                        return false;
                    }
                    if (!department) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Department Required'
                        });
                        row.find('.department_id').focus();
                        isValid = false;
                        return false;
                    }

                    if (!employee) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Employee Required'
                        });
                        row.find('.employee_id').focus();
                        isValid = false;
                        return false;
                    }
                    if (!qty || parseFloat(qty) <= 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Quantity Required'
                        });
                        row.find('.qty').focus();
                        isValid = false;
                        return false;
                    }
                    part.items.push({
                        item_id: itemSelect.val(),
                        item_name: itemSelect.find('option:selected').text(),
                        department_id: department,
                        employee_id: employee,
                        quantity: qty,
                        notes: row.find('.notes').val(),
                        hi_notes: row.find('.hi_notes').val()
                    });
                });
                if (!isValid) {
                    return false;
                }
                parts.push(part);
            });
            if (!isValid) {
                return;
            }
            bomItemsData[orderItemId] = {
                recipe_id: currentRecipeId,
                parts: parts
            };
            let btn = $(`.open-bom-modal[data-id="${orderItemId}"]`);
            btn.removeClass('btn-success')
                .addClass('btn-warning')
                .html('<i class="fa fa-check"></i>');
            $('#bomItemModal').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'BOM Saved Successfully',
                timer: 1200,
                showConfirmButton: false
            });

        } $('#bomForm').submit(function (e) {
            e.preventDefault();

            let formData = {
                _token: "{{ csrf_token() }}",
                order_id: $('#order_id').val(),
                remarks: $('#remarks').val(),
                hi_remarks: $('#hi_remarks').val(),
                incharge_department_id: $('[name="incharge_department_id"]').val(),
                review_department_id: $('[name="review_department_id"]').val(),
                supervisor_id: $('[name="supervisor_id"]').val(),
                priority_id: $('#priority_id').val(),
                shift_id: $('#shift_id').val(),
                checked_by: $('[name="checked_by"]').val(),
                delivery_date: $('#delivery_date').val(),

                items: bomItemsData
            };

            $.ajax({
                url: "{{ route('boms.store', ['company' => $company->id]) }}",
                type: "POST",
                data: JSON.stringify(formData),   // 🔥 FIX
                contentType: "application/json",  // 🔥 FIX
                processData: false,               // 🔥 FIX

                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: res.message || 'BOM Created Successfully'
                    });

                    setTimeout(() => {
                        window.location.href = "{{ route('boms.index', $company->id) }}";
                    }, 1500);
                },

                error: function (xhr) {

                    if (xhr.status === 422 && xhr.responseJSON?.bom_id) {

                        let data = xhr.responseJSON;

                        Swal.fire({
                            icon: 'warning',
                            title: `BOM Already Exists`,
                            html: `
                                                                                                                                                <b>BOM Number:</b> ${data.bom_number} <br><br>
                                                                                                                                                Do you want to edit it?
                                                                                                                                            `,
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Edit',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {

                            if (result.isConfirmed) {
                                window.location.href = `/company/{{ $company->id }}/boms/${data.bom_id}/edit`;
                            }
                        });

                        return;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: xhr.responseJSON?.message || 'Something went wrong'
                    });
                }
            });
        });
        $('#saveDescription').click(function () {

            let html = $('#modalDescription').summernote('code');

            if (currentDescRow) {
                currentDescRow.find('.description_data').val(html);
            }

            $('#descriptionModal').modal('hide');
        });
        let currentDescRow = null;

        $(document).on('click', '.open-description-modal', function () {

            currentDescRow = $(this).closest('tr');

            let existing = currentDescRow.find('.description_data').val();

            // load into summernote
            $('#modalDescription').summernote('code', existing || '');

            $('#descriptionModal').modal('show');
        });
    </script>
    <script>


        $('#priority_id').change(function () {

            if ($(this).val() === 'add_new') {

                $('#priorityForm')[0].reset();
                $('#priority_id').val('');

                $('#priorityModalTitle').text('Add Priority');
                $('#priorityModal').modal('show');
            }
        });
        $('#shift_id').change(function () {

            if ($(this).val() === 'add_new') {

                $('#shiftForm')[0].reset();
                $('#shift_id').val('');

                $('#shiftModalTitle').text('Add Shift');
                $('#shiftModal').modal('show');
            }
        });
        $('#priorityForm').submit(function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('priorities.store', ['company' => $company->id]) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    name: $('#priority_name').val(),
                    level: $('#priority_level').val()
                },
                success: function (res) {

                    $('#priority_id option:last').before(`
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <option value="${res.priority.id}" selected>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ${res.priority.name}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            `);

                    $('#priorityModal').modal('hide');

                    // ✅ SWEET Swal
                    Swal.fire({
                        icon: 'success',
                        title: 'Priority added successfully'
                    });
                }
            });
        });
        let currentDeptSelect = null;
        let currentEmpSelect = null;
        $(document).on(
            'change',
            'select[name="incharge_department_id"], select[name="review_department_id"], .department_id',
            function () {

                let deptId = $(this).val();

                // -------------------
                // ADD NEW Department
                // -------------------
                if (deptId === 'add_new') {

                    currentDeptSelect = $(this);

                    $('#departmentForm')[0].reset();

                    $(this).val('').trigger('change');

                    $('#departmentModal').modal('show');

                    return;
                }

                // -------------------
                // REFETCH EMPLOYEES
                // -------------------

                // Main form → BOM Incharge
                if ($(this).is('[name="incharge_department_id"]')) {

                    loadEmployees(
                        deptId,
                        '#supervisor_id'
                    );

                    return;
                }

                // Main form → Checked By
                if ($(this).is('[name="review_department_id"]')) {

                    loadEmployees(
                        deptId,
                        '#checked_by'
                    );

                    return;
                }

                // BOM Modal rows
                if ($(this).hasClass('department_id')) {

                    let row = $(this).closest('tr');

                    let empSelect = row.find('.employee_id');

                    loadEmployees(
                        deptId,
                        empSelect
                    );
                }

            });
        $(document).on(
            'change',
            'select[name="supervisor_id"], select[name="checked_by"], .employee_id',
            function () {

                if ($(this).val() === 'add_new') {

                    currentEmpSelect = $(this);

                    $('#employeeForm')[0].reset();

                    $(this).val('').trigger('change');

                    $('#employeeModal').modal('show');
                }

            });
        $('#departmentForm').submit(function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('departments.store', ['company' => $company->id]) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    name: $('#department_name').val()
                },
                success: function (res) {

                    let dept = res.department;

                    let option = `<option value="${dept.id}">
                                                                                                                                                                                                                                                        ${dept.name}
                                                                                                                                                                                                                                                    </option>`;

                    // ✅ Update ALL department dropdowns INCLUDING employee modal
                    $('select[name="incharge_department_id"], select[name="review_department_id"], .department_id, #emp_department_id')
                        .each(function () {

                            if ($(this).find(`option[value="${dept.id}"]`).length === 0) {

                                let addNewOption = $(this).find('option[value="add_new"]');

                                if (addNewOption.length) {
                                    addNewOption.before(`<option value="${dept.id}">${dept.name}</option>`);
                                } else {
                                    $(this).append(`<option value="${dept.id}">${dept.name}</option>`);
                                }
                            }
                        });

                    // ✅ Push into global list
                    departmentsList.push(dept);

                    // ✅ Select where user triggered
                    if (currentDeptSelect) {
                        currentDeptSelect.val(dept.id).trigger('change');
                    }

                    // ✅ ALSO select in employee modal dropdown
                    $('#emp_department_id')
                        .val(dept.id)
                        .trigger('change');

                    $('#departmentModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Department added successfully'
                    });
                }
            });
        });



        $('#employeeForm').submit(function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('employees.store', ['company' => $company->id]) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    first_name: $('#emp_first_name').val(),
                    last_name: $('#emp_last_name').val(),
                    mobile: $('#emp_mobile').val(),
                    department_id: $('#emp_department_id').val(),
                    status: 1
                },
                success: function (res) {

                    let emp = res.employee;
                    employeesList.push(emp);
                    let option = `<option value="${emp.id}">
                                                                                                                                                                                                                                                ${emp.name}
                                                                                                                                                                                                                                                </option>`;

                    // ✅ update ALL employee dropdowns
                    $('select[name="supervisor_id"], select[name="checked_by"], .employee_id')
                        .each(function () {

                            $(this).find('option[value="add_new"]').before(option);
                        });

                    // ✅ select current
                    if (currentEmpSelect) {
                        currentEmpSelect.val(emp.id);
                    }

                    $('#employeeModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Employee added successfully'
                    });
                }
            });
        });
        $(document).on('show.bs.modal', '.modal', function () {

            let zIndex = 1050 + (10 * $('.modal.show').length);

            $(this).css('z-index', zIndex);

            setTimeout(() => {
                $('.modal-backdrop').not('.modal-stack')
                    .css('z-index', zIndex - 1)
                    .addClass('modal-stack');
            }, 0);
        });
        $('#shiftForm').on('submit', function (e) {
            e.preventDefault();

            let btn = $('#shiftSaveBtn');

            // ✅ Disable button + show loading
            btn.prop('disabled', true)
                .html('<i class="fa fa-spinner fa-spin"></i> Adding...');

            $.ajax({
                url: "{{ route('shifts.store', ['company' => $company->id]) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    name: $('#shift_name').val(),
                    start_time: $('#shift_start').val(),
                    end_time: $('#shift_end').val()
                },

                success: function (res) {

                    let shift = res.shift;
                    window.allShifts.push(shift);

                    let option = `<option value="${shift.id}">${shift.name}</option>`;

                    // ✅ Add to all shift dropdowns
                    $('#shift_id, .shift_id').each(function () {

                        let select = $(this);

                        // avoid duplicate option
                        if (select.find(`option[value="${shift.id}"]`).length === 0) {

                            select.find('option[value="add_new"]').before(option);

                        }

                        select.trigger('change');

                    });

                    // ✅ Select current one
                    if (currentShiftSelect) {
                        currentShiftSelect.val(shift.id).trigger('change');
                    } else {
                        $('#shift_id').val(shift.id).trigger('change');
                    }

                    currentShiftSelect = null;

                    $('#shiftForm')[0].reset();
                    $('#shiftModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Shift Added Successfully'
                    });
                },

                error: function (xhr) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Something went wrong'
                    });
                },
                complete: function () {
                    // ✅ Enable button again
                    btn.prop('disabled', false)
                        .html('Save');
                }
            });
        });
        $('#specForm').submit(function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('specifications.store', ['company' => $company->id]) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    name: $('#spec_name').val()
                },
                success: function (res) {

                    let option = `<option value="${res.spec.id}">
                                                                                                                                                                                                                                                                                                                                                                                                                                            ${res.spec.name}
                                                                                                                                                                                                                                                                                                                                                                                                                                        </option>`;

                    // ✅ update ALL spec dropdowns
                    $('.spec_id').each(function () {
                        $(this).find('option[value="add_new"]').before(option);
                    });

                    if (currentSpecSelect) {
                        currentSpecSelect.val(res.spec.id);
                    }

                    $('#specsModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Specification added successfully'
                    });
                }
            });
        });
        $(document).on('change', '.item_id', function () {
            if ($(this).val() === 'add_new') {
                currentItemSelect = $(this);
                $(this).val('');
                $('#itemModal').modal('show');
            }
        });
        function loadEmployees(deptId, target, selectedEmployee = null) {
            let select = $(target);
            if (!deptId) {
                select.html(`
                                                                            <option value="">Select</option>
                                                                            <option value="">Select Department First</option>
                                                                            <option value="add_new">➕ Add New</option>
                                                                            `).trigger('change');

                return;
            }

            $.get("{{ route('ajax.employees.by.department', ['company' => $company->id]) }}", {
                department_id: deptId
            }, function (res) {

                let html = '<option value="">Select Employee</option>';

                res.forEach(emp => {
                    html += `<option value="${emp.id}" data-present="${emp.is_present}">
                                                                                                                                                                                                                                                                        ${emp.name}
                                                                                                                                                                                                                                                                     </option>`;
                });

                html += '<option value="add_new">➕ Add New</option>';

                // ✅ IMPORTANT: destroy select2 before changing options
                if (select.hasClass("select2-hidden-accessible")) {
                    select.select2('destroy');
                }

                // ✅ set new options
                select.html(html);

                // ✅ re-init select2
                select.select2({
                    width: '100%',
                    dropdownParent: select.closest('.modal').length ? select.closest('.modal') : $('body')
                });

                // ✅ NOW set value (after init)
                if (selectedEmployee) {
                    select.val(selectedEmployee).trigger('change');
                }
            });
        }

        let urlParams = new URLSearchParams(window.location.search);
        let orderId = urlParams.get('order_id');

        if (orderId) {

            $.ajax({
                url: "{{ route('ajax.orders.search', ['company' => $company->id]) }}",
                data: { search: orderId }, // or create dedicated API if needed
                success: function (data) {

                    let order = data.find(o => o.id == orderId);

                    if (order) {

                        let option = new Option(order.order_number, order.id, true, true);

                        $('#order_id').append(option).trigger('change');

                        // 🔥 TRIGGER SELECT EVENT MANUALLY
                        $('#order_id').trigger({
                            type: 'select2:select',
                            params: {
                                data: {
                                    id: order.id,
                                    order_number: order.order_number,
                                    customer_name: order.customer_name,
                                    mobile: order.mobile,
                                    delivery_date: order.delivery_date
                                }
                            }
                        });
                    }
                }
            });
        }

    </script>
    <script>

        $('#order_id').on('change', function () {

            let orderId = $(this).val();

            if (!orderId) return;

            let filteredItems = orderItems.filter(item => {
                return item.order_id == orderId;
            });

            let missing = [];

            filteredItems.forEach(item => {

                if (item.machine && !item.machine.recipe) {
                    missing.push(item.item_name);
                }
                else if (item.component && !item.component.recipe) {
                    missing.push(item.item_name);
                }
                else if (item.item && !item.item.recipe) {
                    missing.push(item.item_name);
                }

            });

            missing = [...new Set(missing)];

            if (missing.length > 0) {

                let html = '';

                missing.forEach(name => {
                    html += `<li>${name}</li>`;
                });

                $('#missingItemsList').html(html);

                // ✅ Bootstrap 4
                $('#missingRecipeModal').modal('show');
            }

        });

    </script>
@endpush