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
                        <li class="breadcrumb-item">
                            <a href="{{ route('company.dashboard', ['company' => $company->id]) }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('items.index', ['company' => $company->id]) }}">Item List</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $label }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card card-teal">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">{{$label}}</h3>
                <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                    <a href="{{ route('items.index', ['company' => $company->id]) }}" class="btn btn-success btn-sm">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <form action="{{ route('items.store', ['company' => $company->id]) }}" method="POST">
                @csrf
                {{-- ITEM DETAILS --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Item Details</h5>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            {{-- Category --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Category</label>

                                    <select id="category_id" name="category_id" class="form-control select2" required>

                                        <option value="">Select Category</option>

                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            {{-- Subcategory --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Sub-category</label>

                                    <select id="subcategory_id" name="subcategory_id" class="form-control select2" required>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Min Quantity</label>
                                    <input type="text" name="min_quantity" class="form-control" min="0" value="10">
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            {{-- Item Name English --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Item Name (English)</label>

                                    <input type="text" name="name" class="form-control" placeholder="Enter Item Name"
                                        required>
                                </div>
                            </div>

                            {{-- Item Name Hindi --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Item Name (Hindi)</label>

                                    <input type="text" name="hi_name" class="form-control"
                                        placeholder="आइटम का नाम दर्ज करें" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">

                                    <label>Base Unit</label>
                                    <small class="text-muted">(Stock will always be maintained in this unit)</small>
                                    <select name="base_unit_id" id="base_unit_id" class="form-control select2" required>
                                        <option value="">Select Base Unit</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- INITIAL STOCK --}}
                <div class="card mt-3">

                    <div class="card-header d-flex justify-content-between align-items-center">

                        <h5 class="mb-0">
                            Initial Stock Entries
                        </h5>
                        <div class="ml-auto">
                            <button type="button" class="btn btn-sm btn-success" id="add-stock-row">

                                <i class="fa fa-plus"></i>
                                Add More

                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="stock-rows-wrapper">
                            {{-- FIRST ROW --}}
                            <div class="stock-row border rounded p-3 mb-3">
                                <div class="row">
                                    {{-- Brand --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Brand</label>
                                            <select name="stocks[0][brand_id]" class="form-control select2">
                                                <option value="">
                                                    Select Brand
                                                </option>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand->id }}">
                                                        {{ $brand->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    {{-- Location --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Location</label>
                                            <select name="stocks[0][location_id]" class="form-control select2">
                                                <option value="">
                                                    Select Location
                                                </option>
                                                @foreach($locations as $location)
                                                    <option value="{{ $location->id }}">
                                                        {{ $location->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    {{-- Condition --}}
                                    {{-- Condition --}}
                                    <div class="col-md-4">

                                        <div class="d-flex justify-content-between align-items-center mb-1">

                                            <label class="mb-0">
                                                Condition
                                            </label>
                                        </div>

                                        <select name="stocks[0][condition_id]" class="form-control select2">

                                            <option value="">
                                                Select Condition
                                            </option>

                                            @foreach($conditions as $condition)
                                                <option value="{{ $condition->id }}">
                                                    {{ $condition->name }}
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>
                                </div>
                                <div class="row">
                                    {{-- Stock Unit --}}
                                    <div class="col-md-4">
                                        <div class="form-group">

                                            <label>Stock Entry Unit</label>

                                            <select name="stocks[0][stock_unit_id]" class="form-control select2 stock-unit">

                                                <option value="">
                                                    Select Unit
                                                </option>

                                                @foreach($units as $unit)
                                                    <option value="{{ $unit->id }}">
                                                        {{ $unit->name }}
                                                    </option>
                                                @endforeach

                                            </select>

                                        </div>
                                    </div>
                                    {{-- Conversion Factor --}}
                                    <div class="col-md-4 conversion-wrapper d-none">

                                        <div class="form-group">

                                            <label>
                                                Conversion Factor
                                            </label>

                                            <input type="text" step="0.000001" min="0" name="stocks[0][conversion_factor]"
                                                class="form-control conversion-factor" placeholder="Conversion Factor">

                                        </div>

                                    </div>
                                    {{-- Qty --}}
                                    <div class="col-md-4">

                                        <div class="form-group">

                                            <label>
                                                Opening Quantity
                                            </label>

                                            <input type="text" step="0.000001" min="0" value="0"
                                                name="stocks[0][initial_stock]" class="form-control initial-stock">

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                {{-- Inventory Notes --}}

                {{-- Inventory Notes --}}
                <div class="alert alert-info mt-3">

                    <div class="d-flex align-items-center mb-3">

                        <i class="fa fa-info-circle mr-2"></i>

                        <h6 class="mb-0">
                            Easy Understanding of Stock Units
                        </h6>

                    </div>

                    <div class="row">

                        {{-- MAIN UNIT --}}
                        <div class="col-md-4 mb-3">

                            <div class="border rounded p-3 h-100 bg-white">

                                <h6 class="text-primary mb-2">
                                    Main Unit
                                </h6>

                                <p class="small text-muted mb-2">

                                    This is the unit in which the system will finally store stock.

                                </p>

                                <p class="small mb-0">

                                    Example:

                                    Nuts are finally counted in

                                    <strong id="preview-base-unit">
                                        PCS
                                    </strong>

                                </p>

                            </div>

                        </div>

                        {{-- PURCHASE UNIT --}}
                        <div class="col-md-4 mb-3">

                            <div class="border rounded p-3 h-100 bg-white">

                                <h6 class="text-success mb-2">
                                    Purchase / Entry Unit
                                </h6>

                                <p class="small text-muted mb-2">

                                    This is the unit in which stock is actually received or entered.

                                </p>

                                <p class="small mb-0">

                                    Example:

                                    Supplier gives nuts in

                                    <strong id="preview-stock-unit">
                                        KG
                                    </strong>

                                </p>

                            </div>

                        </div>

                        {{-- CONVERSION --}}
                        <div class="col-md-4 mb-3">

                            <div class="border rounded p-3 h-100 bg-white">

                                <h6 class="text-danger mb-2">
                                    Conversion
                                </h6>

                                <p class="small text-muted mb-2">

                                    Tell the system how many pieces exist inside 1 unit.

                                </p>

                                <div>

                                    <code id="preview-conversion">
                                                                        1 KG = 250 PCS
                                                                    </code>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- SIMPLE EXAMPLE --}}
                    <div class="border rounded p-3 bg-white">

                        <h6 class="mb-3">
                            Simple Example
                        </h6>

                        <div class="row text-center">

                            {{-- SYSTEM STORES --}}
                            <div class="col-md-3 mb-2">

                                <div class="border rounded p-2">

                                    <small class="text-muted d-block">
                                        System Stores In
                                    </small>

                                    <strong id="preview-store-unit">
                                        PCS
                                    </strong>

                                </div>

                            </div>

                            {{-- RECEIVED IN --}}
                            <div class="col-md-3 mb-2">

                                <div class="border rounded p-2">

                                    <small class="text-muted d-block">
                                        Received In
                                    </small>

                                    <strong id="preview-received-unit">
                                        KG
                                    </strong>

                                </div>

                            </div>

                            {{-- CONTAINS --}}
                            <div class="col-md-3 mb-2">

                                <div class="border rounded p-2">

                                    <small class="text-muted d-block">
                                        1 Unit Contains
                                    </small>

                                    <strong id="preview-unit-contains">
                                        250 PCS
                                    </strong>

                                </div>

                            </div>

                            {{-- RECEIVED QTY --}}
                            <div class="col-md-3 mb-2">

                                <div class="border rounded p-2">

                                    <small class="text-muted d-block">
                                        Received Quantity
                                    </small>

                                    <strong id="preview-received-qty">
                                        2 KG
                                    </strong>

                                </div>

                            </div>

                        </div>
                    </div>

                    {{-- FOOTER NOTE --}}
                    <div class="mt-3 small">

                        <i class="fa fa-lightbulb-o"></i>

                        Later, stock reports can show both values together.

                    </div>

                </div>
                <div class="card-footer mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Save Item
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>

        /*
        |--------------------------------------------------------------------------
        | SELECT2 INIT
        |--------------------------------------------------------------------------
        */

        $('.select2').select2({
            width: '100%',
            placeholder: 'Select an option',
            allowClear: false
        });

        /*
        |--------------------------------------------------------------------------
        | CATEGORY CHANGE
        |--------------------------------------------------------------------------
        */

        $('#category_id').on('change', function () {

            let categoryId = $(this).val();

            $('#subcategory_id').html(
                '<option value="">Loading...</option>'
            );

            if (categoryId) {

                $.ajax({

                    url: "{{ route('subcategories.byCategory', ['company' => $company->id, 'category' => ':id']) }}"
                        .replace(':id', categoryId),

                    type: "GET",

                    success: function (response) {

                        let options =
                            '<option value="">Select Sub-category</option>';

                        $.each(response, function (key, subcategory) {

                            options += `
                                        <option value="${subcategory.id}">
                                            ${subcategory.name}
                                        </option>
                                    `;
                        });

                        $('#subcategory_id').html(options);
                    }
                });

            } else {

                $('#subcategory_id').html(
                    '<option value="">Select Sub-category</option>'
                );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | TOGGLE CONVERSION FACTOR
        |--------------------------------------------------------------------------
        */

        function toggleConversionFactor(row) {

            let baseUnit = $('#base_unit_id').val();

            let stockUnit = row.find('.stock-unit').val();

            if (
                baseUnit &&
                stockUnit &&
                baseUnit != stockUnit
            ) {

                row.find('.conversion-wrapper')
                    .removeClass('d-none');

            } else {

                row.find('.conversion-wrapper')
                    .addClass('d-none');

                row.find('.conversion-factor')
                    .val('');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE STOCK PREVIEW
        |--------------------------------------------------------------------------
        */

        function updateStockPreview() {

            let baseUnit =
                $('#base_unit_id option:selected')
                    .text()
                    .trim();

            if (
                !baseUnit ||
                baseUnit === 'Select Base Unit'
            ) {
                baseUnit = 'PCS';
            }

            /*
            |--------------------------------------------------------------------------
            | FIRST ROW
            |--------------------------------------------------------------------------
            */

            let firstRow =
                $('.stock-row').first();

            let stockUnit =
                firstRow.find('.stock-unit option:selected')
                    .text()
                    .trim();

            let qty =
                parseFloat(
                    firstRow.find('.initial-stock').val()
                ) || 0;

            let conversion;

            if (
                $('#base_unit_id').val() ==
                firstRow.find('.stock-unit').val()
            ) {

                conversion = 1;

            } else {

                conversion =
                    parseFloat(
                        firstRow.find('.conversion-factor').val()
                    ) || 0;
            }

            if (
                !stockUnit ||
                stockUnit === 'Select Unit'
            ) {
                stockUnit = 'BOX';
            }

            /*
            |--------------------------------------------------------------------------
            | TOTAL OF ALL ROWS
            |--------------------------------------------------------------------------
            */

            let totalBaseQty = 0;

            $('.stock-row').each(function () {

                let row = $(this);

                let rowQty =
                    parseFloat(
                        row.find('.initial-stock').val()
                    ) || 0;

                let rowConversion;

                if (
                    $('#base_unit_id').val() ==
                    row.find('.stock-unit').val()
                ) {

                    rowConversion = 1;

                } else {

                    rowConversion =
                        parseFloat(
                            row.find('.conversion-factor').val()
                        ) || 0;
                }

                totalBaseQty +=
                    rowQty * rowConversion;
            });

            /*
            |--------------------------------------------------------------------------
            | TOP PREVIEW
            |--------------------------------------------------------------------------
            */

            $('#preview-base-unit').text(baseUnit);

            $('#preview-stock-unit').text(stockUnit);

            if (conversion == 1) {

                $('#preview-conversion').text(
                    `1 ${stockUnit} = 1 ${baseUnit}`
                );

            } else {

                $('#preview-conversion').text(
                    `1 ${stockUnit} = ${conversion} ${baseUnit}`
                );
            }

            $('#preview-store-unit').text(baseUnit);

            $('#preview-received-unit').text(stockUnit);

            $('#preview-unit-contains').text(
                `${conversion} ${baseUnit}`
            );

            $('#preview-received-qty').text(
                `${qty} ${stockUnit}`
            );

            /*
            |--------------------------------------------------------------------------
            | FORMULA
            |--------------------------------------------------------------------------
            */

            let firstRowFinal =
                qty * conversion;

            $('#preview-formula').text(
                `${qty} ${stockUnit} × ${conversion} ${baseUnit} = ${firstRowFinal} ${baseUnit}`
            );

            /*
            |--------------------------------------------------------------------------
            | TOTAL FINAL STOCK
            |--------------------------------------------------------------------------
            */

            $('#preview-final-stock').text(
                `${totalBaseQty} ${baseUnit}`
            );

            $('#preview-report-example').text(
                `${totalBaseQty} ${baseUnit}`
            );
        }
        /*
        |--------------------------------------------------------------------------
        | LIVE EVENTS
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'keyup change',
            '.stock-unit, .conversion-factor, .initial-stock, #base_unit_id',
            function () {

                let row = $(this).closest('.stock-row');

                toggleConversionFactor(row);

                updateStockPreview();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | ADD STOCK ROW
        |--------------------------------------------------------------------------
        */

        let stockIndex = 1;

        $('#add-stock-row').on('click', function () {

            let html = `

                        <div class="stock-row border rounded p-3 mb-3">

                            <div class="row">

                                {{-- BRAND --}}
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label>Brand</label>

                                        <select name="stocks[${stockIndex}][brand_id]"
                                            class="form-control select2">

                                            <option value="">
                                                Select Brand
                                            </option>

                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}">
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                </div>

                                {{-- LOCATION --}}
                                <div class="col-md-3">

                                    <div class="form-group">

                                        <label>Location</label>

                                        <select name="stocks[${stockIndex}][location_id]"
                                            class="form-control select2">

                                            <option value="">
                                                Select Location
                                            </option>

                                            @foreach($locations as $location)
                                                <option value="{{ $location->id }}">
                                                    {{ $location->name }}
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                </div>

                                {{-- CONDITION --}}
                                <div class="col-md-3">

                                    <div class="d-flex justify-content-between align-items-center mb-1">

                                        <label class="mb-0">
                                            Condition
                                        </label>


                                    </div>

                                    <select name="stocks[${stockIndex}][condition_id]"
                                        class="form-control select2">

                                        <option value="">
                                            Select Condition
                                        </option>

                                        @foreach($conditions as $condition)
                                            <option value="{{ $condition->id }}">
                                                {{ $condition->name }}
                                            </option>
                                        @endforeach

                                    </select>

                                </div>
                               <div class="col-md-3">

    <div class="d-flex justify-content-between align-items-center mb-1">

        <label class="mb-0">
            Opening Quantity
        </label>

        <button type="button"
            class="btn btn-xs btn-danger remove-stock-row">

            <i class="fa fa-times"></i>

        </button>

    </div>

    <input type="text"
        step="0.000001"
        min="0"
        value="0"
        name="stocks[${stockIndex}][initial_stock]"
        class="form-control initial-stock">

</div>

                            </div>

                        </div>
                    `;

            $('#stock-rows-wrapper').append(html);

            $('.select2').select2({
                width: '100%'
            });

            stockIndex++;

            updateStockPreview();
        });

        /*
        |--------------------------------------------------------------------------
        | REMOVE STOCK ROW
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'click',
            '.remove-stock-row',
            function () {

                $(this)
                    .closest('.stock-row')
                    .remove();

                updateStockPreview();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | INITIAL LOAD
        |--------------------------------------------------------------------------
        */

        $(document).ready(function () {

            $('.stock-row').each(function () {

                toggleConversionFactor($(this));
            });

            updateStockPreview();
        });

    </script>
@endpush