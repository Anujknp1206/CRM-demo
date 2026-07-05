@extends('company.layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Stock In</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('company.dashboard', $company->id) }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('stock-ins.index', $company->id) }}">Stock In</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
          <div class="card card-teal">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">{{ $label }}</h3>
                <div class="ml-auto">
                    <a href="{{ route('stock-ins.index', $company->id) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" autocomplete="off"
                    action="{{ route('stock-ins.update', [$company->id, $stockIn->id]) }}">
                    @csrf
                    @method('PUT')
                    <h4 class="text-primary"><b>1. Document Details</b></h4>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Doc No</label>
                            <input type="text" name="doc_no" class="form-control" value="{{ $stockIn->doc_no }}" readonly>
                        </div>

                        <div class="col-md-6">
                            <label>Doc Date *</label>
                            <div class="input-group">
                                <input type="text" name="doc_date" id="doc_date" class="form-control"
                                    value="{{ $stockIn->doc_date }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text calendar-icon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h4 class="text-primary"><b>2. Stock Items</b></h4>
                   <div class="table-responsive-custom">

                        <table class="table table-bordered" id="stock_items">
                            <thead>
                                <tr>
                                    
                                    <th>
                                        <button type="button" id="add_row" class="btn btn-success btn-sm"><i
                                                class="fa fa-plus"></i></button>
                                    </th>
                                    <th class="align-middle">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Item</span>
                                            @can('add items')
                                                <!-- <button type="button" class="btn btn-outline-success btn-xs px-2" title="Add Item"
                                                                onclick="$('#itemModal').modal('show')">
                                                                <i class="fa fa-plus"></i> Add
                                                            </button> -->
                                            @endcan
                                        </div>
                                    </th>


                                    <th class="align-middle">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Brand</span>
                                            @can('add brands')
                                                <button type="button" class="btn btn-outline-success btn-xs ml-1"
                                                    title="Add Brand" onclick="$('#brandModal').modal('show')">
                                                    <i class="fa fa-plus"></i> Add</button>
                                            @endcan
                                        </div>
                                    </th>

                                    <th class="align-middle">
                                        <div class="d-flex justify-content-between align-items-center">

                                            <span>Condition</span>
                                            @can('add conditions')
                                                <button type="button" class="btn btn-outline-success btn-xs ml-1"
                                                    title="Add Condition" onclick="$('#conditionModal').modal('show')"> <i
                                                        class="fa fa-plus"></i> Add</button>
                                            @endcan
                                        </div>
                                    </th>
                                    <th class="align-middle">
                                        <div class="d-flex justify-content-between align-items-center">

                                            <span>Location</span>
                                            @can('add locations')
                                                <button type="button" class="btn btn-outline-success btn-xs ml-1"
                                                    title="Add Location" onclick="$('#locationModal').modal('show')"> <i
                                                        class="fa fa-plus"></i> Add</button>
                                            @endcan
                                        </div>
                                    </th>
                                    <th>Entry Unit
                                        @can('add units')
                                            <button type="button" class="btn btn-outline-success btn-xs ml-1" title="Add Unit"
                                                onclick="$('#unitModal').modal('show')"> <i class="fa fa-plus"></i> Add</button>
                                        @endcan
                                    </th>

                                    <th>Entry Qty</th>

                                    <th width="180">Conversion</th>

                                    <th>Base Unit
                                       
                                    </th>

                                    <th>Final Stock Qty</th>
                                    <th>Rate</th>
                                </tr>
                            </thead>

                            <tbody>
                               @foreach($stockIn->items as $i => $row)

    @php

        $conversionFactor =
            ($row->quantity > 0 && $row->stock_quantity > 0)
                ? ($row->stock_quantity / $row->quantity)
                : 1;

    @endphp

    <tr class="stock-row">
 {{-- REMOVE --}}
        <td>

            <button
                type="button"
                class="btn btn-danger remove_row"
            >
                X
            </button>

        </td>
        {{-- ITEM --}}
        <td>

            <select
                name="items[{{ $i }}][item_id]"
                class="form-control select2 item_select"
                required
            >

                @foreach($items as $item)

                    <option
                        value="{{ $item->id }}"
                        {{ $row->item_id == $item->id ? 'selected' : '' }}
                    >

                        {{ $item->name }}

                    </option>

                @endforeach

            </select>

        </td>

        {{-- BRAND --}}
        <td>

            <select
                name="items[{{ $i }}][brand_id]"
                class="form-control select2 brand_select"
            >

                <option value="">
                    Brand
                </option>

                @foreach($brands as $brand)

                    <option
                        value="{{ $brand->id }}"
                        {{ $row->brand_id == $brand->id ? 'selected' : '' }}
                    >

                        {{ $brand->name }}

                    </option>

                @endforeach

            </select>

        </td>

        {{-- CONDITION --}}
        <td>

            <select
                name="items[{{ $i }}][condition_id]"
                class="form-control select2 condition_select"
            >

                <option value="">
                    Condition
                </option>

                @foreach($conditions as $condition)

                    <option
                        value="{{ $condition->id }}"
                        {{ $row->condition_id == $condition->id ? 'selected' : '' }}
                    >

                        {{ $condition->name }}

                    </option>

                @endforeach

            </select>

        </td>

        {{-- LOCATION --}}
        <td>

            <select
                name="items[{{ $i }}][location_id]"
                class="form-control select2 location_select"
                required
            >

                <option value="">
                    Select Location
                </option>

                @foreach($locations as $hall)

                    <optgroup label="{{ $hall->name }}">

                        <option
                            value="{{ $hall->id }}"
                            {{ $row->location_id == $hall->id ? 'selected' : '' }}
                        >

                            {{ $hall->name }}

                        </option>

                        @foreach($hall->children as $room)

                            <option
                                value="{{ $room->id }}"
                                {{ $row->location_id == $room->id ? 'selected' : '' }}
                            >

                                — {{ $room->name }}

                            </option>

                            @foreach($room->children as $shelf)

                                <option
                                    value="{{ $shelf->id }}"
                                    {{ $row->location_id == $shelf->id ? 'selected' : '' }}
                                >

                                    • {{ $shelf->name }}

                                </option>

                            @endforeach

                        @endforeach

                    </optgroup>

                @endforeach

            </select>

        </td>

        {{-- ENTRY UNIT --}}
        <td>

            <select
                name="items[{{ $i }}][entry_unit_id]"
                class="form-control select2 entry-unit-select"
                required
            >

                <option value="">
                    Entry Unit
                </option>

                @foreach($units as $unit)

                    <option
                        value="{{ $unit->id }}"
                        {{ optional($row->unit)->id == $unit->id ? 'selected' : '' }}
                    >

                        {{ $unit->name }}

                    </option>

                @endforeach

            </select>

        </td>

        {{-- ENTRY QTY --}}
        <td>

            <input
                type="text"
                step="0.000001"
                name="items[{{ $i }}][entry_quantity]"
                class="form-control entry-qty"
                value="{{ $row->quantity }}"
                required
            >

        </td>

        {{-- CONVERSION FACTOR --}}
        <td>

            <input
                type="text"
                step="0.000001"
                name="items[{{ $i }}][conversion_factor]"
                class="form-control conversion-factor"
                value="{{ $conversionFactor }}"
                required readonly
            >

            <small class="text-info conversion-help">

                1 {{ optional($row->unit)->name }}
                =
                {{ $conversionFactor }}
                {{ optional($row->stockUnit)->name }}

            </small>

        </td>

        {{-- BASE UNIT --}}
        <td>

            <input
                type="text"
                class="form-control base-unit-name"
                value="{{ optional($row->stockUnit)->name }}"
                readonly
            >

            <input
                type="hidden"
                name="items[{{ $i }}][base_unit_id]"
                class="base-unit-id"
                value="{{ $row->stock_unit_id }}"
            >

        </td>

        {{-- FINAL STOCK QTY --}}
        <td>

            <input
                type="text"
                class="form-control final-stock-qty"
                value="{{ $row->stock_quantity }} {{ optional($row->stockUnit)->name }}"
                readonly
            >

            <input
                type="hidden"
                name="items[{{ $i }}][stock_quantity]"
                class="stock-quantity-hidden"
                value="{{ $row->stock_quantity }}"
            >

        </td>

        {{-- RATE --}}
        <td>

            <input
                type="text"
                name="items[{{ $i }}][rate]"
                class="form-control"
                value="{{ $row->rate }}"
                step="0.01"
                required
            >

        </td>

       

    </tr>

@endforeach
                              <script type="text/template" id="stock-row-template">

<tr class="stock-row">
 {{-- REMOVE --}}
    <td>

        <button
            type="button"
            class="btn btn-danger remove_row"
        >
            X
        </button>

    </td>
    {{-- ITEM --}}
    <td>

        <select
            name="items[__INDEX__][item_id]"
            class="form-control select2 item_select"
            required
        >

            <option value="">
                Item
            </option>

            @foreach($items as $item)

                <option value="{{ $item->id }}">

                    {{ $item->name }}

                </option>

            @endforeach

        </select>

    </td>

    {{-- BRAND --}}
    <td>

        <select
            name="items[__INDEX__][brand_id]"
            class="form-control select2 brand_select"
        >

            <option value="">
                Brand
            </option>

            @foreach($brands as $brand)

                <option value="{{ $brand->id }}">

                    {{ $brand->name }}

                </option>

            @endforeach

        </select>

    </td>

    {{-- CONDITION --}}
    <td>

        <select
            name="items[__INDEX__][condition_id]"
            class="form-control select2 condition_select"
        >

            <option value="">
                Condition
            </option>

            @foreach($conditions as $condition)

                <option value="{{ $condition->id }}">

                    {{ $condition->name }}

                </option>

            @endforeach

        </select>

    </td>

    {{-- LOCATION --}}
    <td>

        <select
            name="items[__INDEX__][location_id]"
            class="form-control select2 location_select"
            required
        >

            <option value="">
                Select Location
            </option>

            @foreach($locations as $hall)

                <optgroup label="{{ $hall->name }}">

                    <option value="{{ $hall->id }}">

                        {{ $hall->name }}

                    </option>

                    @foreach($hall->children as $room)

                        <option value="{{ $room->id }}">

                            — {{ $room->name }}

                        </option>

                        @foreach($room->children as $shelf)

                            <option value="{{ $shelf->id }}">

                                • {{ $shelf->name }}

                            </option>

                        @endforeach

                    @endforeach

                </optgroup>

            @endforeach

        </select>

    </td>

    {{-- ENTRY UNIT --}}
    <td>

        <select
            name="items[__INDEX__][entry_unit_id]"
            class="form-control select2 entry-unit-select"
            required
        >

            <option value="">
                Entry Unit
            </option>

            @foreach($units as $unit)

                <option value="{{ $unit->id }}">

                    {{ $unit->name }}

                </option>

            @endforeach

        </select>

    </td>

    {{-- ENTRY QTY --}}
    <td>

        <input
            type="text"
            step="0.000001"
            name="items[__INDEX__][entry_quantity]"
            class="form-control entry-qty"
            value="1"
            required
        >

    </td>

    {{-- CONVERSION FACTOR --}}
    <td>

        <input
            type="text"
            step="0.000001"
            name="items[__INDEX__][conversion_factor]"
            class="form-control conversion-factor"
            value="1"
            required
        >

        <small class="text-info conversion-help">

            Select item & unit

        </small>

    </td>

    {{-- BASE UNIT --}}
    <td>

        <input
            type="text"
            class="form-control base-unit-name"
            readonly
        >

        <input
            type="hidden"
            name="items[__INDEX__][base_unit_id]"
            class="base-unit-id"
        >

    </td>

    {{-- FINAL STOCK --}}
    <td>

        <input
            type="text"
            class="form-control final-stock-qty"
            readonly
        >

        <input
            type="hidden"
            name="items[__INDEX__][stock_quantity]"
            class="stock-quantity-hidden"
        >

    </td>

    {{-- RATE --}}
    <td>

        <input
            type="text"
            name="items[__INDEX__][rate]"
            class="form-control"
            step="0.01"
            required
        >

    </td>

   

</tr>

</script>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <h4 class="text-primary"><b>3. Remark</b></h4>
                    <textarea name="remark" class="form-control">{{ $stockIn->remark }}</textarea>

                    <hr>

                    <button class="btn btn-success">
                        <i class="fa fa-save"></i> Update Stock
                    </button>

                </form>
            </div>
        </div>
    </section>

    <div class="modal fade" id="locationModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
                    <h5 class="modal-title text-white" id="locationModalTitle">
                        Add Location
                    </h5>
                    <button class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <form id="locationForm" autocomplete="off">
                    @csrf
                    <input type="hidden" id="location_id">

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Location Name *</label>
                            <input type="text" id="location_name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Parent Location</label>
                            <select id="parent_location" class="form-control select2 location_select">
                                <option value="">None (Top Level)</option>
                                @foreach($parentLocations as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Save
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .calendar-icon {
            cursor: pointer;
            background: #f3f6f9;
        }#stock_items {
    width: 100% !important;
    min-width: 1600px;
}

#stock_items td,
#stock_items th {
    vertical-align: middle;
    white-space: nowrap;
}

#stock_items .select2-container {
    min-width: 180px;
}

#stock_items input.form-control {
    min-width: 120px;
}

.table-responsive-custom {
    overflow-x: auto;
    width: 100%;
}
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function swalConfirm(title, text, cb) {
            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save',
            }).then((result) => {
                if (result.isConfirmed) cb();
            });
        }

        function swalSuccess(msg) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: msg,
                timer: 1500,
                showConfirmButton: false
            });
        }

        function swalError(msg) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: msg
            });
        }

    </script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const docPicker = flatpickr("#doc_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            defaultDate: "{{ now()->toDateString() }}"
        });

        $(document).on('click', '.calendar-icon', function () {
            docPicker.open();
        });
    </script>
    <script>
        $(function () {

            let ACTIVE_MODAL = null;

            function initSelect2(modal, width) {
                $(modal).find('.select2').each(function () {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                    $(this).select2({
                        width: width,
                        dropdownParent: $(modal)
                    });
                });
            }

            function addOptionAndSelect(selector, id, text) {
                let option = new Option(text, id, true, true);
                $(selector).append(option).trigger('change.select2');
            }

            function reopenParentModal() {
                if (ACTIVE_MODAL) {
                    $(ACTIVE_MODAL).modal('show');
                    ACTIVE_MODAL = null;
                }
            }

            function toast(msg) {
                swalSuccess(msg);
            }

            function errorToast(msg) {
                swalError(msg);
            }
            /* ================== MODAL SELECT2 ================== */

            [
                '#itemModal',
                '#categoryModal',
                '#subcategoryModal',
                '#unitModal',
                '#conditionModal',
                '#brandModal',
                '#locationModal'
            ].forEach(modal => {
                $(modal).on('shown.bs.modal', function () {
                    initSelect2(this, '80%');
                });
            });
            $('#locationModal').on('shown.bs.modal', function () {
                $('#parent_location').select2({
                    width: '100%',
                    dropdownParent: $('#locationModal') // ✅ MUST
                });
            });
            $(document).on('select2:open', function () {
                const el = document.querySelector('.select2-container--open .select2-search__field');
                if (el) el.focus();
            });

        });

        function initTableSelect2(context = document) {

            // 🔥 NORMAL SELECT2 ONLY
            $(context).find('select.select2:not(.item_select)').each(function () {

                if (!$(this).hasClass('select2-hidden-accessible')) {

                    $(this).select2({
                        width: '100%',
                        dropdownParent: $(this).closest('td')
                    });

                }

            });

            // 🔥 AJAX ITEM SELECT
            initAjaxItemSelect(context);

        }



        $(document).ready(function () {
            initTableSelect2();
        });

        // add row
   $('#add_row').on('click', function () {

    let index = $('.stock-row').length;

    /*
    -----------------------------------------
    CREATE ROW
    -----------------------------------------
    */

    let template = $('#stock-row-template')
        .html()
        .replace(/__INDEX__/g, index);

    let $row = $(template);

    /*
    -----------------------------------------
    RESET VALUES
    -----------------------------------------
    */

    $row.find('select').val('');

    $row.find('.entry-qty').val(1);

    $row.find('.conversion-factor').val(1);

    $row.find('.final-stock-qty').val('');

    $row.find('.stock-quantity-hidden').val('');

    /*
    -----------------------------------------
    APPEND ROW
    -----------------------------------------
    */

    $('#stock_items tbody').append($row);

    /*
    -----------------------------------------
    INIT SELECT2
    -----------------------------------------
    */

    initTableSelect2($row);

    /*
    -----------------------------------------
    INIT AJAX ITEM SELECT
    -----------------------------------------
    */

    initAjaxItemSelect($row);

}); </script>

    <script>
        // ================= OPEN MODALS FROM ITEM MODAL =================

        function openCreateCategory() {
            $('#itemModal').modal('hide');
            $('#categoryModal').modal('show');
        }

        function openCreateSubcategory() {
            $('#itemModal').modal('hide');
            $('#subcategoryModal').modal('show');
        }

        function openCreateUnit() {
            $('#itemModal').modal('hide');
            $('#unitModal').modal('show');
        }

        function openCreateCondition() {
            $('#itemModal').modal('hide');
            $('#conditionModal').modal('show');
        }

        function openCreateBrand() {
            $('#itemModal').modal('hide');
            $('#brandModal').modal('show');
        }
        function reopenItemModal() {
            $('#itemModal').modal('show');
        }
        $('#categoryModal').modal('hide');
        $('#categoryForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('categories.store', $company->id) }}",
                type: "POST",
                data: {
                    name: $('#category_name').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {

                    LAST_CREATED_CATEGORY_ID = res.category.id;

                    let option = new Option(
                        res.category.name,
                        res.category.id,
                        true,
                        true
                    );

                    // Subcategory modal (visible → update immediately)
                    $('#subcategory_category')
                        .append(option.cloneNode(true))
                        .val(res.category.id)
                        .trigger('change.select2');

                    // Item modal (hidden → ONLY append)
                    $('#item_category').append(option.cloneNode(true));

                    $('#categoryModal').modal('hide');

                    setTimeout(() => {
                        $('#itemModal').modal('show');
                    }, 300);

                    $('#categoryForm')[0].reset();
                    swalSuccess('Category added');
                },
                error: function () {
                    swalError('Failed to save category');
                }
            });
        });
        $(document).on('click', '#openCategoryFromSubcategory', function () {

            // Close subcategory modal first
            $('#subcategoryModal').modal('hide');

            // Wait for Bootstrap to clean backdrop, then open category modal
            setTimeout(function () {
                $('#categoryModal').modal('show');
            }, 300);
        });

        $('#itemForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('items.store', $company->id) }}",
                type: "POST",
                data: $(this).serialize(),
                success: function (res) {

                    let option = new Option(res.name, res.id, true, true);

                    $('.item_select').each(function () {
                        $(this).append(option.cloneNode(true));
                    });

                    $('#itemModal').modal('hide');
                    swalSuccess('Item added');
                },
                error: function () {
                    swalError('Failed to save item');
                }
            });
        });
    </script>
    <script>
        $('#item_category').on('change', function () {

            let categoryId = $(this).val();
            let $sub = $('#item_subcategory');

            $sub.empty().append('<option value="">Select Sub Category</option>');

            if (!categoryId) {
                $sub.trigger('change.select2');
                return;
            }

            $.ajax({
                url: "{{ url('company/' . $company->id . '/subcategories/by-category') }}/" + categoryId,
                type: "GET",
                success: function (res) {

                    res.forEach(sc => {
                        $sub.append(new Option(sc.name, sc.id));
                    });

                    $sub.trigger('change.select2');
                }
            });
        });
        $('#subcategoryForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('subcategories.store', $company->id) }}",
                type: "POST",
                data: $(this).serialize(),
                success: function (res) {

                    let option = new Option(
                        res.subcategory.name,
                        res.subcategory.id,
                        true,
                        true
                    );

                    $('#item_subcategory')
                        .append(option)
                        .trigger('change.select2');

                    $('#subcategoryModal').modal('hide');
                    reopenItemModal();
                    this.reset();

                    swalSuccess('Sub Category added');
                }
            });
        });
        function addOptionAndSelect(selector, id, text) {
            let option = new Option(text, id, true, true);
            $(selector).append(option).trigger('change.select2');
        }
        $('#unitForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('units.store', $company->id) }}",
                type: "POST",
                data: {
                    name: $('#unit_name').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {

                    let unit = response.unit;

                    let option =
                        `<option value="${unit.id}">
                                ${unit.name}
                            </option>`;

                    $('.unit_select').each(function () {
                        $(this).append(option);
                    });

                    $('.unit_select').trigger('change.select2');

                    $('#unitModal').modal('hide');

                    $('#unitForm')[0].reset();

                    swalSuccess('Unit added successfully');
                },
                error: function () {
                    swalError('Failed to save unit');
                }
            });
        });
        $('#conditionForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('conditions.store', $company->id) }}",
                type: "POST",
                data: {
                    name: $('#condition_name').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {

                    let condition = response.condition;

                    let option =
                        `<option value="${condition.id}">
                            ${condition.name}
                        </option>`;

                    $('.condition_select').each(function () {
                        $(this).append(option);
                    });

                    $('.condition_select').trigger('change.select2');

                    $('#conditionModal').modal('hide');

                    $('#conditionForm')[0].reset();

                    swalSuccess('Condition added successfully');
                },
                error: function () {
                    swalError('Failed to save condition');
                }
            });
        });
        $('#brandForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('brands.store', $company->id) }}",
                type: "POST",
                data: {
                    name: $('#brand_name').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {

                    let brand = response.brand;

                    let option =
                        `<option value="${brand.id}">
                        ${brand.name}
                    </option>`;

                    $('.brand_select').each(function () {
                        $(this).append(option);
                    });

                    $('.brand_select').trigger('change.select2');

                    $('#brandModal').modal('hide');

                    $('#brandForm')[0].reset();

                    swalSuccess('Brand added successfully');
                },
                error: function () {
                    swalError('Failed to save brand');
                }
            });
        });
        $('#locationForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('locations.store', $company->id) }}",
                type: "POST",
                data: {
                    name: $('#location_name').val(),
                    parent_id: $('#parent_location').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {

                    let location = response.location;

                    let option =
                        `<option value="${location.id}">
                                    ${location.name}
                                </option>`;

                    $('.location_select').each(function () {
                        $(this).append(option);
                    });

                    $('.location_select').trigger('change.select2');

                    $('#locationModal').modal('hide');

                    $('#locationForm')[0].reset();

                    swalSuccess('Location added successfully');
                },
                error: function () {
                    swalError('Failed to save location');
                }
            });
        });
        function refreshLocationSelects(selectedId = null) {

            $.get("{{ route('locations.search', $company->id) }}", function (locations) {

                // 🔥 STEP 1: build map
                let map = {};
                locations.forEach(loc => {
                    loc.children = [];
                    map[loc.id] = loc;
                });

                // 🔥 STEP 2: build tree
                let roots = [];
                locations.forEach(loc => {
                    if (loc.parent_id && map[loc.parent_id]) {
                        map[loc.parent_id].children.push(loc);
                    } else {
                        roots.push(loc);
                    }
                });

                // 🔥 STEP 3: rebuild all selects
                $('select.location_select').each(function () {

                    let $select = $(this);

                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }

                    $select.empty().append('<option value="">Select Location</option>');

                    roots.forEach(hall => {
                        let $group = $('<optgroup>', { label: hall.name });

                        // hall
                        $group.append(new Option(hall.name, hall.id));

                        hall.children.forEach(room => {
                            $group.append(new Option('— ' + room.name, room.id));

                            room.children.forEach(shelf => {
                                $group.append(new Option('• ' + shelf.name, shelf.id));
                            });
                        });

                        $select.append($group);
                    });

                    if (selectedId) {
                        $select.val(selectedId);
                    }

                    $select.select2({
                        width: '100%',
                        dropdownParent: $select.closest('td')
                    });
                });
            });
        }
        $('#locationModal').on('hidden.bs.modal', function () {
            $('body').addClass('modal-open');
        });
        // ================= REMOVE ROW WITH SWEET ALERT =================
        $(document).on('click', '.remove_row', function () {

            let $row = $(this).closest('.stock-row');

            // Prevent removing last row
            if ($('.stock-row').length === 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Not Allowed',
                    text: 'At least one item row is required.'
                });
                return;
            }

            Swal.fire({
                title: 'Remove this item?',
                text: 'This row will be removed from stock entry.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, remove',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $row.remove();

                    Swal.fire({
                        icon: 'success',
                        title: 'Removed',
                        text: 'Item row removed successfully.',
                        timer: 1200,
                        showConfirmButton: false
                    });
                }
            });
        });
        let isSubmitting = false;

        $('#stockForm').on('submit', function () {

            if (isSubmitting) return false;

            isSubmitting = true;

            let $btn = $('#saveStockBtn');

            $btn.prop('disabled', true);
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        });
    </script>

    <script>
        function initAjaxItemSelect(context = document) {

            $(context).find('.item_select').each(function () {

                let $this = $(this);

                // already initialized
                if ($this.hasClass('select2-hidden-accessible')) {
                    return;
                }

                $this.select2({

                    width: '100%',

                    dropdownParent: $('body'),

                    placeholder: 'Search Item',

                    minimumInputLength: 1,

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
                                results: data.results || data
                            };

                        },

                        cache: true
                    }

                });

            });

        }

        $(document).on(
            'select2:select',
            '.item_select',
            function (e) {

                let data = e.params.data;

                let $row = $(this).closest('.stock-row');

                /*
                |--------------------------------------------------------------------------
                | BASE UNIT
                |--------------------------------------------------------------------------
                */

                /*
    |--------------------------------------------------------------------------
    | BASE UNIT CHECK
    |--------------------------------------------------------------------------
    */

                if (!data.base_unit_id) {

                    Swal.fire({

                        icon: 'warning',

                        title: 'Base Unit Missing',

                        html: `
                        <div class="text-left">

                            <p class="mb-2">
                                This item does not have a base inventory unit.
                            </p>

                            <p class="mb-2">
                                Please configure:
                            </p>

                            <ul style="text-align:left;">
                                <li>Base Unit</li>
                                <li>Unit Conversion</li>
                            </ul>

                            <p class="mb-0 text-danger">
                                Stock entry cannot continue without it.
                            </p>

                        </div>
                    `,

                        showCancelButton: true,

                        confirmButtonText: 'Open Item',

                        cancelButtonText: 'Cancel',

                        confirmButtonColor: '#28a745',
                    })
                        .then((result) => {

                            if (result.isConfirmed) {
                                window.open(
                                    `/company/{{ $company->id }}/items/${data.id}/edit`,
                                    '_blank'
                                );
                            }
                        });

                    /*
                    |--------------------------------------------------------------------------
                    | RESET ITEM SELECT
                    |--------------------------------------------------------------------------
                    */

                    $(this).val(null).trigger('change');

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | AUTO FILL BASE UNIT
                |--------------------------------------------------------------------------
                */

                $row.find('.base-unit-name')
                    .val(data.base_unit_name || '');

                $row.find('.base-unit-id')
                    .val(data.base_unit_id || '');
                /*
                |--------------------------------------------------------------------------
                | SAVE CONVERSIONS IN ROW
                |--------------------------------------------------------------------------
                */

                $row.data(
                    'conversions',
                    data.conversions || []
                );

                /*
                |--------------------------------------------------------------------------
                | RESET VALUES
                |--------------------------------------------------------------------------
                */

                $row.find('.conversion-factor').val('');

                $row.find('.entry-qty').val(1);

                $row.find('.final-stock-qty').val('');

                $row.find('.stock-quantity-hidden').val('');
            }
        );/*
                    |--------------------------------------------------------------------------
                    | ENTRY UNIT CHANGE
                    |--------------------------------------------------------------------------
                    */

        $(document).on(
            'change',
            '.entry-unit-select',
            function () {

                let $row = $(this).closest('.stock-row');

                let entryUnitId = $(this).val();

                let conversions =
                    $row.data('conversions') || [];

                /*
                |--------------------------------------------------------------------------
                | FIND CONVERSION
                |--------------------------------------------------------------------------
                */

                let found = conversions.find(c =>
                    String(c.from_unit_id) === String(entryUnitId)
                );

                /*
                |--------------------------------------------------------------------------
                | AUTO FILL FACTOR
                |--------------------------------------------------------------------------
                */

                if (found) {

                    $row.find('.conversion-factor')
                .val(found.factor)
                .prop('readonly', true);

                    $row.find('.conversion-help')
                        .html(
                            `
                                        1 ${found.from_unit_name}
                                        =
                                        ${found.factor}
                                        ${$row.find('.base-unit-name').val()}
                                        `
                        );

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | MANUAL ENTRY
                    |--------------------------------------------------------------------------
                    */

                    $row.find('.conversion-factor')
                        .val('');

                    $row.find('.conversion-help')
                        .html(
                            'Conversion not found. Enter manually.'
                        );
                }

                calculateFinalStock($row);
            }
        );/*
                    |--------------------------------------------------------------------------
                    | AUTO CALCULATE STOCK
                    |--------------------------------------------------------------------------
                    */

        $(document).on(
            'input',
            '.entry-qty, .conversion-factor',
            function () {

                let $row = $(this).closest('.stock-row');

                calculateFinalStock($row);
            }
        );

        function calculateFinalStock($row) {

            let qty = parseFloat(
                $row.find('.entry-qty').val()
            ) || 0;

            let factor = parseFloat(
                $row.find('.conversion-factor').val()
            ) || 0;

            /*
            |--------------------------------------------------------------------------
            | TOTAL BASE QUANTITY
            |--------------------------------------------------------------------------
            */

            let total = qty * factor;

            let baseUnit =
                $row.find('.base-unit-name').val();

            /*
            |--------------------------------------------------------------------------
            | SHOW FINAL STOCK
            |--------------------------------------------------------------------------
            */

            if (total > 0) {

                $row.find('.final-stock-qty')
                    .val(
                        total + ' ' + baseUnit
                    );

            } else {

                $row.find('.final-stock-qty')
                    .val('');
            }

            /*
            |--------------------------------------------------------------------------
            | SAVE HIDDEN STOCK QTY
            |--------------------------------------------------------------------------
            */

            $row.find('.stock-quantity-hidden')
                .val(total);
        }
    </script>
@endpush