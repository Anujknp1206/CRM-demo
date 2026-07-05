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
                                <a href="{{ route('boms.index', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="bomForm" autocomplete="off">
                                @csrf
                                <input type="hidden" id="bom_id" value="{{ $bom->id }}">
                                <!-- 🔥 1. SELECT ORDER + BASIC DETAILS -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <b>1. Select Order & BOM Details</b>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label>Select Order *</label>
                                                <select id="order_id" name="order_id" class="form-control"></select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>BOM Number</label>
                                                <input type="text" name="bom_number" value="{{ $bom->bom_number }}"
                                                    class="form-control" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Department (Incharge)</label>
                                                <select name="incharge_department_id" id="incharge_department_id"
                                                    class="form-control">
                                                    <option value="">Select</option>
                                                    @foreach($departments as $d)
                                                        <option value="{{ $d->id }}" {{ $bom->incharge_department_id == $d->id ? 'selected' : '' }}>
                                                            {{ $d->name }}
                                                        </option>
                                                    @endforeach
                                                    <option value="add_new">➕ Add New</option>
                                                </select>
                                            </div>

                                            <!-- SUPERVISOR -->
                                            <div class="col-md-3">
                                                <label>BOM Incharge</label>
                                                <select name="supervisor_id" id="supervisor_id" class="form-control">
                                                    <option value="">Select</option>

                                                    @if($bom->supervisor)
                                                        <option value="{{ $bom->supervisor->id }}" selected>
                                                            {{ $bom->supervisor->first_name }} {{ $bom->supervisor->last_name }}
                                                        </option>
                                                    @endif

                                                    <option value="add_new">➕ Add New</option>
                                                </select>
                                            </div>

                                            <!-- SHIFT -->
                                            <div class="col-md-3">
                                                <label>Shift</label>
                                                <select name="shift_id" id="shift_id" class="form-control">
                                                    <option value="">Select</option>

                                                    @foreach($shifts as $s)
                                                        <option value="{{ $s->id }}" {{ $bom->shift_id == $s->id ? 'selected' : '' }}>
                                                            {{ $s->name }}
                                                        </option>
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
                                                <select name="priority_id" id="priority_id" class="form-control">
                                                    <option value="">Select</option>

                                                    @foreach($priorities as $p)
                                                        <option value="{{ $p->id }}" {{ $bom->priority_id == $p->id ? 'selected' : '' }}>
                                                            {{ $p->name }}
                                                        </option>
                                                    @endforeach

                                                    <option value="add_new">➕ Add New</option>
                                                </select>
                                            </div>
                                           
                                            <!-- REVIEW DEPARTMENT -->
                                            <div class="col-md-3">
                                                <label>Department (Review)</label>
                                                <select name="review_department_id" id="review_department_id"
                                                    class="form-control">
                                                    <option value="">Select</option>
                                                    @foreach($departments as $d)
                                                        <option value="{{ $d->id }}" {{ $bom->review_department_id == $d->id ? 'selected' : '' }}>
                                                            {{ $d->name }}
                                                        </option>
                                                    @endforeach
                                                    <option value="add_new">➕ Add New</option>
                                                </select>
                                            </div>

                                            <!-- CHECKED BY -->
                                            <div class="col-md-3">
                                                <label>Checked By</label>
                                                <select name="checked_by" id="checked_by" class="form-control">
                                                    <option value="">Select</option>

                                                    @if($bom->checker)
                                                        <option value="{{ $bom->checker->id }}" selected>
                                                            {{ $bom->checker->first_name }} {{ $bom->checker->last_name }}
                                                        </option>
                                                    @endif

                                                    <option value="add_new">➕ Add New</option>
                                                </select>
                                            </div>

                                            <!-- DELIVERY DATE -->
                                            <div class="col-md-3">
                                                <label>Delivery Date</label>

                                                <div class="input-group">
                                                    <input type="text" name="delivery_date" id="delivery_date"
                                                        value="{{ $bom->delivery_date ? \Carbon\Carbon::parse($bom->delivery_date)->format('d/m/Y') : '' }}"
                                                        class="form-control">
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

            <!-- ENGLISH REMARK -->
            <div class="col-md-6">

                <label>Remark</label>

                <textarea
                    name="remarks"
                    id="remarks"
                    class="form-control summernote"
                >{!! $bom->remarks !!}</textarea>

            </div>

            <!-- HINDI REMARK -->
            <div class="col-md-6">

                <label>Hindi Remark</label>

                <textarea
                    name="hi_remarks"
                    id="hi_remarks"
                    class="form-control summernote"
                >{!! $bom->hi_remarks ?? '' !!}</textarea>

            </div>

        </div>

    </div>

</div>

                                <!-- 🔥 ITEMS -->
                                <div id="items_section"></div>

                                <button class="btn btn-success">Update BOM</button>
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
@endsection
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
            .toggle-icon i {
                transition: transform 0.2s ease;
            }

            /* When open */
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
          $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
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

            // ITEM
           applySelect2($(context).find('.item_id'), {

    placeholder: "Search Item",

    ajax: {

        url: "{{ route('parts.searchItems', $company->id) }}",

        dataType: 'json',

        delay: 250,

        data: function (params) {

            return {
                search: params.term
            };

        },

        processResults: function (data) {

            return {
                results: data
            };

        },

        cache: true
    },

    minimumInputLength: 1

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
                        text: item.order_number,
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
                clickOpens: true
            });

            $('#calendar_icon').on('click', function () {
                deliveryPicker.open();
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
    <button type="button"
        class="btn btn-sm ${item.has_bom ? 'btn-warning' : 'btn-success'} open-bom-modal"
        data-id="${item.id}">
        
        <i class="fa ${item.has_bom ? 'fa-check' : 'fa-plus'}"></i>
    </button>
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
        $(document).on(
            'click',
            '.open-bom-modal',
            function () {
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
if (bomItemsData[orderItemId] && bomItemsData[orderItemId].parts.length > 0) {

    console.log("✅ EXISTING BOM FOUND");

    // 🔥 FETCH RECIPES FIRST
    $.get("{{ route('recipes.by.order.item', $company) }}",
        { order_item_id: orderItemId },
        function (res) {

            let recipes = res.recipes;
            loadedRecipes = recipes;

            // 👉 MULTIPLE RECIPES → ASK
            if (recipes.length > 1) {

                Swal.fire({
                    icon: 'question',
                    title: 'Change Recipe?',
                    html: `
                        You already created BOM for this item.<br><br>
                        <b>If you change recipe, previous data will be lost.</b>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Change Recipe',
                    cancelButtonText: 'No, Continue'
                }).then((result) => {

                    if (result.isConfirmed) {

                        // 🔥 CLEAR OLD DATA
                        delete bomItemsData[orderItemId];

                        // 🔥 SHOW RECIPE PICKER
                        let html = '';
                        recipes.forEach(r => {
                            html += `<option value="${r.id}">${r.name}</option>`;
                        });

                        $('#recipe_picker').html(html);
                        $('#recipePickerModal').modal('show');

                    } else {

                        // 🔥 LOAD SAVED DATA
                        loadRecipeItemsIntoBom(bomItemsData[orderItemId]);

                        currentRecipeId = bomItemsData[orderItemId].recipe_id;

                        $('#bomItemModal').modal('show');
                    }

                });

                return;
            }

            // 👉 ONLY ONE RECIPE → DIRECT LOAD SAVED
            loadRecipeItemsIntoBom(bomItemsData[orderItemId]);

            currentRecipeId = bomItemsData[orderItemId].recipe_id;

            $('#bomItemModal').modal('show');
        }
    );

    return;
}
                $('#modal_order_item_id')
                    .val(orderItemId);
                $.get("{{ route('recipes.by.order.item', $company) }}",
                    { order_item_id: orderItemId },
                    function (res) {
                        let recipes = res.recipes;
                        loadedRecipes = recipes;
                        if (recipes.length === 0) {

                            Swal.fire({
                                icon: 'warning',
                                title: 'Recipe Missing',
                                html: ` No recipe found for this item.<br>
                                                                            Please create recipe first.`,
                                showCancelButton: true,
                                confirmButtonText: 'Create Recipe',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {

                                if (result.isConfirmed) {

                                    let baseUrl = "{{ route('recipes.create', $company) }}";

                                    window.location.href = `${baseUrl}?order_item_id=${orderItemId}`;
                                }
                            });

                            return;
                        }
                        if (
                            recipes.length === 1
                        ) {
                            loadRecipeItemsIntoBom(recipes[0]);
                            currentRecipeId = recipes[0].id;
                            $('#bomItemModal').modal('show'); return;
                        }
                        let html = '';
                        recipes.forEach(r => {
                            html +=
                                `<option value="${r.id}">${r.name}</option>`;
                        });
                        $('#recipe_picker')
                            .html(html);
                        $('#recipePickerModal')
                            .modal('show');
                    }
                );
            });

        $(document).on('click', '#loadRecipeBtn', function () {

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

    weightage: part.weightage || 0,

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

    status: item.status

});

                    });

                } else {
                    addBomRow(partIdx);
                }

            });
        }   $.get("{{ route('specifications', $company) }}", function (specs) {
                window.allSpecifications = specs;
        });
        $.get("{{ route('shifts.get', $company) }}", function (shifts) {

            window.allShifts = shifts;

        });
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

                                                                            <input type="text"
           class="form-control part_name w-50"
           placeholder="Part Name">

    <!-- HINDI PART NAME -->
    <input type="text"
           class="form-control hi_part_name w-50"
           placeholder="कलपुर्जों के नाम">
 <input type="text" 
                class="form-control part_weightage"
                placeholder="Wt"
                min="0" max="10" step="1"oninput="if(this.value < 0) this.value = 1;"
                style="width:80px">

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
                                            <th>Status</th>
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

    // PART NAME
    partBox.find('.part_name')
        .val(prefill.part_name || '');

    // HINDI PART NAME
    partBox.find('.hi_part_name')
        .val(prefill.hi_part_name || '');

    // WEIGHTAGE
    partBox.find('.part_weightage')
        .val(prefill.weightage ?? '');

    // SORT ORDER
    partBox.find('.part_sort_order')
        .val(prefill.sort_order ?? '');

    // 🔥 WAIT FOR SELECT2
    setTimeout(() => {

        // SPEC
        partBox.find('.spec_id')
            .val(String(prefill.spec_id || ''))
            .trigger('change');

        // SHIFT
        partBox.find('.shift_id')
            .val(String(prefill.shift_id || ''))
            .trigger('change');

    }, 100);

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
                                                                    <select class="form-control department_id">
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
                                                                    <select class="form-control status">
                                                                        <option value="assigned">Assigned</option>
                                                                        <option value="in_progress">In Progress</option>
                                                                        <option value="completed">Completed</option>
                                                                        <option value="on_hold">On Hold</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control notes">
                                                                    <input type="text" class="form-control hi_notes">
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
            if (prefill) {
                if (prefill.item_id) {

    let itemSelect = newRow.find('.item_id');

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

    itemSelect
        .val(prefill.item_id)
        .trigger('change.select2');
}
                newRow.find('.department_id').val(prefill.department_id);
                loadEmployees(prefill.department_id, newRow.find('.employee_id'), prefill.employee_id);
                newRow.find('.qty').val(prefill.quantity);
                newRow.find('.notes').val(prefill.notes);
                newRow.find('.hi_notes').val(prefill.hi_notes);
                newRow.find('.status').val(prefill.status || 'assigned');
            }
        }
        $('#bomItemModal').on('shown.bs.modal', function () {
            initSelect2(this);
        });
        $(document).on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
        });
        let currentSpecRow = null;
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
        }); let currentRecipeId = null; // 🔥 GLOBAL (set when recipe selected)

        function saveBomItems() {

            let orderItemId = $('#modal_order_item_id').val();

            let parts = [];

            // 🔥 LOOP ALL PARTS
            $('[id^="bom_part_"]').each(function () {

                let partBox = $(this);

                let partIndex = partBox.attr('id').split('_')[2];

               let part = {

    part_name: partBox.find('.part_name').val(),

    // 🔥 IMPORTANT
    hi_part_name: partBox.find('.hi_part_name').val(),

    spec_id: partBox.find('.spec_id').val(),

    shift_id: partBox.find('.shift_id').val(),

    // 🔥 IMPORTANT
    weightage: partBox.find('.part_weightage').val(),

    // 🔥 IMPORTANT
    sort_order: partBox.find('.part_sort_order').val(),

    items: []

};

                // 🔥 LOOP ITEMS INSIDE PART
                partBox.find(`#bom_rows_${partIndex} tr`).each(function () {
let itemSelect = $(this).find('.item_id');
                   part.items.push({

    item_id: itemSelect.val(),

    item_name: itemSelect.find('option:selected').text(),

    department_id: $(this).find('.department_id').val(),

    employee_id: $(this).find('.employee_id').val(),

    quantity: $(this).find('.qty').val(),

    notes: $(this).find('.notes').val(),

    // 🔥 IMPORTANT
    hi_notes: $(this).find('.hi_notes').val(),

    // 🔥 IMPORTANT
    status: $(this).find('.status').val()

});
                });

                parts.push(part);
            });

            // 🔍 DEBUG LOGS
            console.log("ORDER ITEM ID:", orderItemId);
            console.log("RECIPE ID:", currentRecipeId);
            console.log("PARTS:", parts);

            // ✅ FINAL STRUCTURE
            bomItemsData[orderItemId] = {
                recipe_id: currentRecipeId,
                parts: parts
            };

            console.log("FINAL bomItemsData:", bomItemsData);

            // 🔥 BUTTON UPDATE
            let btn = $(`.open-bom-modal[data-id="${orderItemId}"]`);

            btn.removeClass('btn-success')
                .addClass('btn-warning')
                .html('<i class="fa fa-check"></i>');

            $('#bomItemModal').modal('hide');
        }
        $('#bomForm').submit(function (e) {
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
              url: "{{ route('boms.update', ['company'=>$company->id, 'bom'=>$bom->id]) }}",
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
        // PRIORITY
        $('#priority_id').change(function () {

            if ($(this).val() === 'add_new') {

                $('#priorityForm')[0].reset();
                $('#priority_id').val('');

                $('#priorityModalTitle').text('Add Priority');
                $('#priorityModal').modal('show');
            }
        });// SHIFT
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

        // =====================
        // 🔥 GLOBAL HOLDERS
        // =====================
        let currentDeptSelect = null;
        let currentEmpSelect = null;


        // =====================
        // 🔥 DEPARTMENT HANDLER
        // =====================
        // =====================
        // 🔥 DEPARTMENT HANDLER (UPDATED)
        // =====================
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

            });// =====================
        // 🔥 EMPLOYEE HANDLER
        // =====================
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

        // =====================
        // 🔥 SAVE DEPARTMENT
        // =====================
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


        // =====================
        // 🔥 SAVE EMPLOYEE
        // =====================
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
        }); $(document).on('show.bs.modal', '.modal', function () {

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

    // =====================================
    // 🔥 UPDATE GLOBAL MEMORY
    // =====================================

    window.allShifts.push(shift);

    shiftsList.push(shift);

    let option = `
        <option value="${shift.id}">
            ${shift.name}
        </option>
    `;

    // =====================================
    // 🔥 UPDATE ALL SHIFT DROPDOWNS
    // =====================================

    $('#shift_id, .shift_id').each(function () {

        let select = $(this);

        // avoid duplicate
        if (
            select.find(`option[value="${shift.id}"]`).length === 0
        ) {

            select.find('option[value="add_new"]')
                .before(option);

        }

        // 🔥 refresh select2
        select.trigger('change.select2');

    });

    // =====================================
    // 🔥 SELECT CURRENT DROPDOWN ONLY
    // =====================================

    if (currentShiftSelect) {

        currentShiftSelect
            .val(shift.id)
            .trigger('change');

    } else {

        $('#shift_id')
            .val(shift.id)
            .trigger('change');

    }

    // =====================================
    // 🔥 RESET
    // =====================================

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

    let spec = res.spec;

    // =====================================
    // 🔥 UPDATE GLOBAL MEMORY
    // =====================================

    window.allSpecifications.push(spec);

    let option = `
        <option value="${spec.id}">
            ${spec.name}
        </option>
    `;

    // =====================================
    // 🔥 UPDATE ALL SPEC DROPDOWNS
    // =====================================

    $('.spec_id').each(function () {

        let select = $(this);

        // avoid duplicate
        if (
            select.find(`option[value="${spec.id}"]`).length === 0
        ) {

            select.find('option[value="add_new"]')
                .before(option);

        }

        // refresh select2
        select.trigger('change.select2');

    });

    // =====================================
    // 🔥 SELECT CURRENT DROPDOWN ONLY
    // =====================================

    if (currentSpecSelect) {

        currentSpecSelect
            .val(spec.id)
            .trigger('change');

    }

    // =====================================
    // 🔥 RESET
    // =====================================

    currentSpecSelect = null;

    $('#specForm')[0].reset();

    $('#specsModal').modal('hide');

    Swal.fire({

        icon: 'success',

        title: 'Specification added successfully'

    });

}
            });
        });
    </script> <script>
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

                // ✅ set new options
                select.html(html);
                // ✅ NOW set value (after init)
                if (selectedEmployee) {
                    select.val(selectedEmployee).trigger('change');
                }
            });
        }
       
        $(document).on('change', '.department_id', function () {

            let deptId = $(this).val();
            let row = $(this).closest('tr');
            let empSelect = row.find('.employee_id');

            loadEmployees(deptId, empSelect);
        });

    </script>
    @if(isset($bom))
<script>
    $(document).ready(function () {

        let existingOrder = {
            id: "{{ $bom->order_id }}",
            order_number: "{{ $bom->order->order_number ?? '' }}",
            customer_name: "{{ $bom->order->customer_name ?? '' }}",
            mobile: "{{ $bom->order->mobile ?? '' }}",
            delivery_date: "{{ $bom->order->delivery_date ?? '' }}"
        };

        // ✅ SET SELECT2 VALUE
        let option = new Option(existingOrder.order_number, existingOrder.id, true, true);
        $('#order_id').append(option).trigger('change');

        // ✅ TRIGGER FULL FLOW (IMPORTANT)
        setTimeout(() => {
            $('#order_id').trigger({
                type: 'select2:select',
                params: { data: existingOrder }
            });
        }, 200);

    });
</script>
@endif
@if(isset($grouped))

<script>

    bomItemsData = @json($grouped);

    console.log('🔥 EDIT MODE DATA:', bomItemsData);

</script>

@endif

@if(request('existing'))

<script>

    Swal.fire({
        icon: 'info',
        title: 'Existing BOM',
        text: 'You are entering existing BOM edit page.'
    });

</script>

@endif
@endpush