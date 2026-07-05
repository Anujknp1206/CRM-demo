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
                        <li class="breadcrumb-item active"><a
                                href="{{ route('recipes.index', ['company' => $company->id]) }}"> Recipes List</a></li>
                        <li class="breadcrumb-item active">{{ $label }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">

        <div class="card ">

            <div class="card-header text-white d-flex justify-content-between align-items-center"
                style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
                <h3 class="card-title">
                    Recipe Form
                </h3>
                <div class="d-flex align-items-center ml-auto" style="gap: 8px;">

                    @can('add parts')
                        <a href="{{ route('parts.create', $company->id) }}" target="_blank">
                            <button class="btn btn-danger btn-sm">
                                <i class="fa fa-plus"></i> Create New Part
                            </button>
                        </a>
                    @endcan
                    <a href="{{ route('recipes.index', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>



            <form id="recipeForm" method="POST" action="{{ route('recipes.store', $company) }}"> @csrf

                <div class="card-body">

                    <div class="row">
                        <!-- Recipe Name -->
                        <div class="col-md-3">
                            <label>Recipe Name (English)</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter Recipe Name" required>
                        </div>
                        <div class="col-md-3">
                            <label>Recipe Name (Hindi)</label>
                            <input type="text" name="hi_name" class="form-control" placeholder="रेसिपी का नाम दर्ज करें"
                                required>
                        </div>
                        <!-- Type -->
                        <div class="col-md-3">
                            <label>Recipe For</label>
                            <select id="recipe_type" name="type" class="form-control">
                                <option value="">Select Type</option>
                                <option value="machine">Machine</option>
                                <option value="component">Component</option>
                            </select>
                        </div>
                        <!-- Machine Select -->
                        <div class="col-md-3" id="machineBox" style="display:none;">
                            <label>Select Machine</label>
                            <select name="recipeable_id" id="machineSelect" class="form-control select2">
                                <option value="">
                                    Choose Machine
                                </option>
                                @foreach($machines as $machine)
                                    <option value="{{$machine->id}}">
                                        {{$machine->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>



                        <!-- Component Select -->
                        <div class="col-md-3" id="componentBox" style="display:none;">
                            <label>Select Component</label>
                            <select name="recipeable_id" id="componentSelect" class="form-control select2">
                                <option value="">Choose Component</option>
                                @foreach($components as $component)
                                    <option value="{{$component->id}}">{{$component->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-2 px-3 py-2 rounded"
                        style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">

                        <h5 class="mb-0 text-white">Recipe Items</h5>
                        <div class="d-flex align-items-center ml-auto" style="gap: 8px;">


                            <button type="button" class="btn btn-success btn-sm fw-bold" onclick="addPart()">
                                + Add Part
                            </button>
                        </div>
                    </div>
                    <div id="partsWrapper" class="accordion"></div>
                    <!-- <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <label>Recipe Notes (English)</label>
                                                    <textarea name="notes" class="form-control summernote" rows="3"></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Recipe Notes (Hindi)</label>
                                                    <textarea name="hi_notes" class="form-control summernote" rows="3"></textarea>
                                                </div>
                                            </div> -->
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="is_default" value="1" class="custom-control-input" id="is_default">
                        <!-- <label class="custom-control-label" for="is_default">

                                                                                                                    Default Recipe

                                                                                                                </label> -->

                    </div>


                    <div class="ml-auto">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i>
                            Save Recipe
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
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
    </style>
@endpush
@push('scripts')
    <script>
        let orderItem = @json($orderItem);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $('#machineSelect').select2({
            placeholder: 'Select Machine',
            allowClear: false,
            width: '100%'
        });

        $('.itemSelect').select2({
            width: '100%',
            theme: 'bootstrap4',
            placeholder: 'Select Item'
        });
        $('#componentSelect').select2({
            placeholder: 'Select Component',
            allowClear: false,
            width: '100%'
        });
        $('.itemSelect').select2({
            placeholder: 'Select Item',
            allowClear: false,
            width: '100%'
        });
        $(function () {

            $('.select2').select2({

                placeholder: 'Select option',

                allowClear: false,

                width: '100%'

            });
        }); $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        }); 
    </script>
    <script>
        let index = 1;
        /*
        Show Machine / Component
        */
        $('#recipe_type').change(
            function () {

                let type = $(this).val();

                if (type == 'machine') {

                    $('#machineBox').show();

                    $('#componentBox').hide();

                    $('#componentSelect').prop(
                        'disabled',
                        true
                    );

                    $('#machineSelect').prop(
                        'disabled',
                        false
                    );

                }

                else if (
                    type == 'component'
                ) {

                    $('#componentBox').show();

                    $('#machineBox').hide();

                    $('#machineSelect').prop(
                        'disabled',
                        true
                    );

                    $('#componentSelect').prop(
                        'disabled',
                        false
                    );

                }

            }
        );



        /* First row Select2 */
        $(document).ready(function () {

            $('.itemSelect').select2({
                width: '100%',
                theme: 'bootstrap4',
                placeholder: 'Select Item'
            });

        });



        /* Add row */
        $(document).on(
            'click',
            '.addRow',
            function () {

                let row = $(`
                                                                                                                    <tr>

                                                                                                                        <td>

                                                                                                                            <select
                                                                                                                                name="items[${index}][item_id]"
                                                                                                                                class="form-control newItemSelect">

                                                                                                                                <option value="">
                                                                                                                                    Select Item
                                                                                                                                </option>

                                                                                                                                @foreach($items as $item)

                                                                                                                                                        <option
                                                                                                                                                            value="{{$item->id}}">

                                                                                                                                    {{$item->name}}

                                                                                                                                                        </option>

                                                                                                                                @endforeach

                                                                                                                            </select>

                                                                                                                        </td>


                                                                                                                        <td>
                                                                                                                            <input
                                                                                                                                name="items[${index}][quantity]"
                                                                                                                                class="form-control">
                                                                                                                        </td>



                                                                                            <td>

                                                                                            <input
                                                                                            type="text"
                                                                                            name="items[${index}][notes]"
                                                                                            class="form-control"
                                                                                            placeholder="Notes">

                                                                                            </td>


                                                                                                                                <td>

                                                                                                                                    <button
                                                                                                                                        type="button"
                                                                                                                                        class="btn btn-danger removeRow" title="Remove Item">

                                                                                                                                        -

                                                                                                                                    </button>

                                                                                                                                </td>

                                                                                                                            </tr>
                                                                                                            `);



                $('#itemTable tbody')
                    .append(row);



                /* only new select gets select2 */
                row.find(
                    '.newItemSelect'
                ).select2({

                    width: '100%',

                    theme: 'bootstrap4',

                    placeholder: 'Select Item'

                });



                index++;

            });



        /* Remove row */
        $(document).on(
            'click',
            '.removeRow',
            function () {

                $(this)
                    .closest('tr')
                    .remove();

            });



        /* Auto-fill unit */
        $(document).on(
            'change',
            '.itemSelect,.newItemSelect',
            function () {

                let row =
                    $(this).closest('tr');

                let unitId =
                    $(this)
                        .find(':selected')
                        .data('unit');

                let unitName =
                    $(this)
                        .find(':selected')
                        .data('unit-name');


                row.find('.unitId')
                    .val(unitId);

                row.find('.unitText')
                    .val(unitName);

            });



        $(document).on(
            'click',
            '.removeRow',
            function () {

                $(this)
                    .closest('tr')
                    .remove();

            });


    </script>
    <script>
        $(document).ready(function () {
            if (!orderItem) return;
            // 🔥 MACHINE
            if (orderItem.machine_id) {
                $('#recipe_type').val('machine').trigger('change');
                setTimeout(() => {
                    $('#machineSelect')
                        .val(orderItem.machine_id)
                        .trigger('change');
                }, 200);
            }
            // 🔥 COMPONENT
            else if (orderItem.component_id) {
                $('#recipe_type').val('component').trigger('change');
                setTimeout(() => {
                    $('#componentSelect')
                        .val(orderItem.component_id)
                        .trigger('change');
                }, 200);
            }
        });
    </script>
    <script>

        $('#recipeForm').submit(function (e) {

            e.preventDefault();

            $.ajax({

                url: "{{ route('recipes.store', $company) }}",

                type: "POST",

                data: $(this).serialize(),

                beforeSend: function () {

                    $('button[type=submit]')
                        .prop('disabled', true)
                        .html('<i class="fa fa-spinner fa-spin"></i> Saving...');

                },

                success: function (res) {

                    if (res.success) {

                        Swal.fire({

                            icon: 'success',

                            title: 'Success',

                            text: res.message ?? 'Recipe created successfully',

                            timer: 1500,

                            showConfirmButton: false

                        });

                        setTimeout(() => {

                            window.location =
                                "{{ route('recipes.index', $company) }}";

                        }, 1500);
                    }

                },

                error: function (xhr) {

                    let message = 'Something went wrong';

                    // VALIDATION ERRORS
                    if (xhr.status === 422) {

                        let errors = xhr.responseJSON.errors;

                        message = '';

                        $.each(errors, function (key, value) {

                            message += `• ${value[0]}<br>`;

                        });

                    }

                    // SERVER ERROR
                    else if (xhr.responseJSON?.message) {

                        message = xhr.responseJSON.message;

                    }

                    Swal.fire({

                        icon: 'error',

                        title: 'Validation Error',

                        html: message

                    });

                    console.log(xhr.responseText);

                },

                complete: function () {

                    $('button[type=submit]')
                        .prop('disabled', false)
                        .html('<i class="fa fa-save"></i> Save Recipe');

                }

            });

        }); </script>

    <script>

        let partIndex = 0;
        let itemIndexMap = {};

        /* =========================================
           ADD PART
        ========================================== */
        function addPart() {

            itemIndexMap[partIndex] = 0;

            let currentIndex = partIndex;

            let html = `
                                            <div class="card mb-2" id="part_${currentIndex}">

                                                <!-- HEADER -->
                                                <div class="card-header p-2"
                                                     style="background:#e7f1ff;">

                                                    <div class="d-flex justify-content-between align-items-center">

                                                        <div class="d-flex align-items-center w-75">

                                                            <!-- COLLAPSE -->
                                                            <button type="button"
                                                                    class="btn btn-link p-0 mr-2 toggle-icon"
                                                                    data-toggle="collapse"
                                                                    data-target="#collapse_${currentIndex}">

                                                                <i class="fa fa-chevron-right"></i>

                                                            </button>

                                                            <!-- PART SEARCH -->
                                                           <div class="d-flex align-items-center w-100">

                                <!-- PART SELECT -->
                                <div style="width:60%;">

                                    <select
                                        name="parts[${currentIndex}][part_id]"
                                        class="form-control recipe-part-select"
                                        onchange="loadPartItems(this, ${currentIndex})">
                                    </select>

                                </div>

                                <!-- HINDI NAME -->
                                <div class="part-hi-name ml-2 p-2 rounded text-center"
                                     style="
                                        width:40%;
                                        background:#fff3cd;
                                        border:1px solid #ffe69c;
                                        font-weight:600;
                                        color:#856404;
                                        min-height:38px;
                                     ">

                                    हिंदी नाम

                                </div>

                            </div>
                                                            <!-- WEIGHT -->
                                                            <input type="text"
                                                                   name="parts[${currentIndex}][weightage]"
                                                                   class="form-control ml-2"
                                                                   placeholder="part weightage"
                                                                   step="1"
                                                                   min="0"
                                                                   max="10"
                                                                   style="width:180px;"
                                                                   oninput="if(this.value > 10) this.value = 10; if(this.value < 0) this.value = 0;">

                                                        </div>

                                                        <!-- REMOVE -->
                                                        <button type="button"
                                                                class="btn btn-danger btn-sm"
                                                                onclick="removePart(${currentIndex})">

                                                            <i class="fa fa-trash"></i>

                                                        </button>

                                                    </div>

                                                </div>

                                                <!-- BODY -->
                                                <div id="collapse_${currentIndex}" class="collapse show">

                                                    <div class="card-body p-2">

                                                        <table class="table table-bordered">

                                                            <thead style="background:#f1f3f5;">

                                                                <tr>

                                                                    <th width="45%">
                                                                        Item
                                                                    </th>

                                                                    <th width="15%">
                                                                        Qty
                                                                    </th>

                                                                    <th width="30%">
                                                                        Notes
                                                                    </th>


                                                                </tr>

                                                            </thead>

                                                            <tbody id="itemTable_${currentIndex}"></tbody>

                                                        </table>

                                                    </div>

                                                </div>

                                            </div>
                                            `;

            $('#partsWrapper').append(html);

            /* =========================================
               PART AJAX SEARCH
            ========================================== */
            $(`#part_${currentIndex} .recipe-part-select`).select2({

                placeholder: 'Search Part by name or code',

                width: '100%',

                minimumInputLength: 1,

                ajax: {

                    url: "{{ route('parts.ajaxSearch', ['company' => $company->id]) }}",

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
                }
            });

            partIndex++;
        }


        /* =========================================
           LOAD PART ITEMS
        ========================================== */
        function loadPartItems(select, partIdx) {

            let partId = $(select).val();

            if (!partId) {

                return;
            }

            $.ajax({

                url: "{{ route('parts.items', ['company' => $company->id, 'part' => ':id']) }}"
                    .replace(':id', partId),

                type: "GET",

                success: function (response) {

                    // PART HINDI NAME
                    $(`#part_${partIdx} .part-hi-name`)
                        .html(response.part.hi_name ?? 'हिंदी नाम उपलब्ध नहीं');
                    $(`#itemTable_${partIdx}`).html('');

                    itemIndexMap[partIdx] = 0;

                    if (response.items.length > 0) {

                        response.items.forEach(function (item) {

                            addRow(
                                partIdx,
                                item.item_id,
                                item.quantity,
                                item.notes,
                                item.item.name,
                                item.hi_notes,
                                item.item.hi_name
                            );

                        });

                    }

                },

                error: function () {

                    alert('Unable to fetch part items.');

                }

            });
        }


        /* =========================================
           ADD ITEM ROW
        ========================================== */
        function addRow(
            partIdx,
            selectedItemId = '',
            qty = 1,
            notes = '',
            itemName = '',
            hiNotes = '',
            itemHiName = ''
        ) {
            let index = itemIndexMap[partIdx];

            let row = `
                            <tr>

                               <td>

                            <!-- HIDDEN INPUT -->
                            <input
                                type="hidden"
                                name="parts[${partIdx}][items][${index}][item_id]"
                                value="${selectedItemId}">

                            <!-- READONLY SELECT -->
                            <select
                                class="form-control item-select"
                                disabled>

                                ${selectedItemId
                    ?
                    `<option value="${selectedItemId}" selected>
                                        ${itemName}
                                    </option>`
                    :
                    `<option value="">Select Item</option>`
                }

                            </select>

                            <!-- HINDI NAME -->
                            <div class="mt-2 p-2 rounded text-center"
                                 style="
                                    background:#e8f5e9;
                                    border:1px solid #c8e6c9;
                                    color:#1b5e20;
                                    font-weight:600;
                                    font-size:14px;
                                    min-height:38px;
                                 ">

                                ${itemHiName ?? 'हिंदी नाम उपलब्ध नहीं'}

                            </div>

                        </td>

                                <td>

                                    <input type="text"
                                           step="1"
                                           min="1"
                                           value="${qty}"
                                           name="parts[${partIdx}][items][${index}][quantity]"
                                           class="form-control"readonly>

                                </td>

                                <td>

                                    <input type="text"
                                           value="${notes ?? ''}"
                                           name="parts[${partIdx}][items][${index}][notes]"
                                           class="form-control mb-2"
                                           placeholder="Enter Notes"readonly>

                                    <input type="text"
                                           value="${hiNotes ?? ''}"
                                           name="parts[${partIdx}][items][${index}][hi_notes]"
                                           class="form-control"
                                           placeholder="नोट्स दर्ज करें" readonly>

                                </td>

                            </tr>
                            `; $(`#itemTable_${partIdx}`).append(row);

            /* =========================================
               ITEM AJAX SEARCH
            ========================================== */
            $(`#itemTable_${partIdx} tr:last .item-select`).select2({

                placeholder: 'Search Item',

                width: '100%',

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

                        return {
                            results: data
                        };
                    },

                    cache: true
                }
            });

            itemIndexMap[partIdx]++;
        }


        /* =========================================
           REMOVE PART
        ========================================== */
        function removePart(index) {

            $(`#part_${index}`).remove();

        }

    </script>
@endpush