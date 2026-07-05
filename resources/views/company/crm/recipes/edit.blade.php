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


            <form id="recipeForm" method="POST" action="{{ route('recipes.update', [$company, $recipe]) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <!-- Recipe Name -->
                        <div class="col-md-3">
                            <label>Recipe Name (English)</label>
                            <input type="text" name="name" value="{{$recipe->name}}" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>Recipe Name (Hindi)</label>
                            <input type="text" name="hi_name" value="{{$recipe->hi_name}}" class="form-control" required>
                        </div>
                        <!-- Type -->
                        <div class="col-md-3">
                            <label>Recipe For</label>
                            <select id="recipe_type" name="type" class="form-control" disabled>
                                <option value="machine" @if($recipe->recipeable_type== App\Models\Machine::class) selected @endif>Machine</option>
                                <option value="component" @if($recipe->recipeable_type== App\Models\Component::class)selected @endif>Component</option>
                            </select>
                        </div>
                        <!-- Machine -->
                        <div class="col-md-3" id="machineBox" @if($recipe->recipeable_type!= App\Models\Machine::class)style="display:none" @endif>
                            <label>Select Machine</label>
                            <select disabled class="form-control select2" style="width:100%">
                                @foreach($machines as $machine)
                                    <option value="{{$machine->id}}" @if($recipe->recipeable_id== $machine->id) selected @endif
                                        >{{$machine->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Component -->
                        <div class="col-md-3" id="componentBox" @if($recipe->recipeable_type!= App\Models\Component::class) style="display:none" @endif>
                            <label>Select Component</label>
                            <select disabled class="form-control select2" style="width:100%">
                                @foreach($components as $component)
                                    <option value="{{$component->id}}" @if($recipe->recipeable_id== $component->id) selected @endif
                                        >
                                        {{$component->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <hr>
                <div class="d-flex justify-content-between align-items-center text-white mb-2 px-3 py-2 rounded" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
                <h5 class="mb-0">Recipe Items</h5>

                <button type="button" class="btn btn-success btn-sm fw-bold" onclick="addPart()">
                    + Add Part
                </button>
            </div>

<div id="partsWrapper">

    @foreach($recipe->parts as $pIndex => $part)

        <div class="card mb-2"
             id="part_{{ $pIndex }}">

            <!-- HEADER -->
            <div class="card-header px-3 py-2"
                 style="background:#e7f1ff;">

                <div class="d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center w-75">

                        {{-- COLLAPSE --}}
                        <button type="button"
                                class="btn btn-link toggle-icon mr-2"
                                data-toggle="collapse"
                                data-target="#collapse_{{ $pIndex }}"
                                aria-expanded="true">

                            <i class="fa fa-chevron-right"></i>

                        </button>


                      <select
    name="parts[{{ $pIndex }}][part_id]"
    class="form-control select2 part-select"
    style="width:100%;"onchange="loadPartItems(this, {{ $pIndex }})">

    @foreach($allParts as $prt)

        <option value="{{ $prt->id }}"
            {{ $prt->id == $part->id ? 'selected' : '' }}>

            {{ $prt->name }}

        </option>

    @endforeach

</select>

                        {{-- PART HINDI NAME --}}
                        <div class="ml-2 p-2 rounded text-center"
                             style="
                                min-width:220px;
                                background:#fff3cd;
                                border:1px solid #ffe69c;
                                color:#856404;
                                font-weight:600;
                             ">

                            {{ $part->hi_name ?? 'हिंदी नाम उपलब्ध नहीं' }}

                        </div>


                        {{-- WEIGHTAGE --}}
                        <input type="text"
                               name="parts[{{ $pIndex }}][weightage]"
                              value="{{ $part->pivot->weightage ?? 0 }}"
                               class="form-control ml-2"
                               placeholder="Weight"
                               step="1"
                               min="0"
                               max="10"
                               style="width:200px;"
                               oninput="
                                    if(this.value > 10) this.value = 10;
                                    if(this.value < 0) this.value = 0;
                               ">

                    </div>


                    {{-- REMOVE PART --}}
                    <button type="button"
                            class="btn btn-danger btn-sm"
                            title="Remove Part"
                            onclick="removePart({{ $pIndex }})">

                        X

                    </button>

                </div>

            </div>


            <!-- BODY -->
            <div id="collapse_{{ $pIndex }}"
                 class="collapse show">

                <div class="card-body p-2">

                    <table class="table table-bordered">

                        <thead style="background:#f1f3f5;">

                            <tr>

                                <th width="40%">
                                    Item
                                </th>

                                <th width="15%">
                                    Qty
                                </th>

                                <th width="35%">
                                    Notes
                                </th>

                                <th width="10%">

                                    <button type="button"
                                            class="btn btn-success btn-sm"
                                            title="Add Item"
                                            onclick="addRow({{ $pIndex }})">

                                        +

                                    </button>

                                </th>

                            </tr>

                        </thead>


                        <tbody id="itemTable_{{ $pIndex }}">

                            @foreach($part->items as $iIndex => $item)

                                <tr>

                                    {{-- ITEM --}}
                                   {{-- ITEM --}}
                                    <td>
                                    
                                        {{-- HIDDEN INPUT --}}
                                        <input type="hidden"
                                               name="parts[{{ $pIndex }}][items][{{ $iIndex }}][item_id]"
                                               value="{{ $item->item_id }}">
                                    
                                        {{-- DISABLED SELECT --}}
                                        <select
                                            class="form-control select2"
                                            style="width:100%"
                                            disabled>
                                    
                                            @foreach($items as $itm)
                                    
                                                <option value="{{ $itm->id }}"
                                                    {{ $itm->id == $item->item_id ? 'selected' : '' }}>
                                    
                                                    {{ $itm->name }}
                                    
                                                </option>
                                    
                                            @endforeach
                                    
                                        </select>
                                    
                                    
                                        {{-- ITEM HINDI NAME --}}
                                        <div class="mt-2 p-2 rounded text-center"
                                             style="
                                                background:#e8f5e9;
                                                border:1px solid #c8e6c9;
                                                color:#1b5e20;
                                                font-weight:600;
                                                font-size:14px;
                                                min-height:38px;
                                             ">
                                    
                                            {{ optional($item->item)->hi_name ?? 'हिंदी नाम उपलब्ध नहीं' }}
                                    
                                        </div>
                                    
                                    </td>


                                    {{-- QTY --}}
                                    <td>

                                        <input type="text"
                                               name="parts[{{ $pIndex }}][items][{{ $iIndex }}][quantity]"
                                               value="{{ $item->quantity }}"
                                               class="form-control"
                                               min="1"
                                               oninput="
                                                    if(this.value < 1)
                                                    this.value = 1;
                                               "readonly>

                                    </td>


                                    {{-- NOTES --}}
                                    <td>

                                        <input type="text"
                                               name="parts[{{ $pIndex }}][items][{{ $iIndex }}][notes]"
                                               value="{{ $item->notes }}"
                                               class="form-control mb-2"
                                               placeholder="Enter Notes" readonly>


                                        <input type="text"
                                               name="parts[{{ $pIndex }}][items][{{ $iIndex }}][hi_notes]"
                                               value="{{ $item->hi_notes }}"
                                               class="form-control"
                                               placeholder="नोट्स दर्ज करें" readonly>

                                    </td>


                                    {{-- REMOVE ITEM --}}
                                    <td>

                                        <button type="button"
                                                title="Remove Item"
                                                class="btn btn-danger btn-sm removeRow">

                                            -

                                        </button>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    @endforeach

</div>
                    <!-- <div class="row mt-3">

                        <div class="col-md-6">

                            <label>
                                Recipe Notes (English)
                            </label>

                            <textarea name="notes" class="form-control summernote"
                                rows="3">{!! $recipe->notes !!}</textarea>

                        </div>
                        <div class="col-md-6">

                            <label>
                                Recipe Notes (Hindi)
                            </label>

                            <textarea name="hi_notes" class="form-control summernote"
                                rows="3">{!! $recipe->hi_notes !!}</textarea>

                        </div>

                    </div> -->

                </div>





                <div class="card-footer d-flex justify-content-between align-items-center">

                    <div class="custom-control custom-switch">

                        <input type="checkbox" name="is_default" value="1" class="custom-control-input" id="is_default"
                            @if($recipe->is_default) checked @endif>


                        <!-- <label
    class="custom-control-label" name="is_default"
    for="is_default">

    Default Recipe

    </label> -->

                    </div>



                    <div class="ml-auto">

                        <button type="submit" class="btn btn-success">

                            <i class="fa fa-save"></i>

                            Update Recipe

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
        }/* hide until initialized */
.select2-hidden-init {
    visibility: hidden;
}
    </style>
@endpush
@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>$(document).ready(function () {
    initSelect2(); // quick init
});

$(window).on('load', function () {
    initSelect2(); // fix layout issues
});
let partIndex = {{ $recipe->parts->count() }};
let itemIndexMap = {};

let isSubmitting = false;

/* Init item count per part */
@foreach($recipe->parts as $pIndex => $part)
    itemIndexMap[{{ $pIndex }}] = {{ $part->items->count() }};
@endforeach


/* Select2 focus fix */
$(document).on('select2:open', function () {
    document.querySelector('.select2-container--open .select2-search__field').focus();
});


function initSelect2(context = document) {

    $(context).find('.select2').each(function () {

        if ($(this).hasClass("select2-hidden-accessible")) {
            $(this).select2('destroy'); // ✅ destroy old instance
        }

        $(this).select2({
            theme: 'bootstrap4',
            width: '100%',
            width: 'style',
            placeholder: 'Select Item'
        });

    });
}
/* LOAD PART ITEMS */
function loadPartItems(select, partIdx) {

    let partId = $(select).val();

    let hiName = $(select)
        .find(':selected')
        .data('hi-name');

    // UPDATE PART HINDI NAME
    $(select)
        .closest('.d-flex')
        .find('.part-hi-name')
        .html(hiName ?? 'हिंदी नाम उपलब्ध नहीं');

    if (!partId) return;

    $.ajax({

        url: "{{ route('parts.items', ['company' => $company->id, 'part' => ':id']) }}"
            .replace(':id', partId),

        type: "GET",

        success: function (response) {

            // CLEAR OLD ITEMS
            $(`#itemTable_${partIdx}`).html('');

            itemIndexMap[partIdx] = 0;

            if (response.items.length > 0) {

                response.items.forEach(function (item) {

                    addLoadedRow(
                        partIdx,
                        item.item_id,
                        item.quantity,
                        item.notes,
                        item.hi_notes,
                        item.item.name,
                        item.item.hi_name
                    );

                });

            }

        },

        error: function () {

            Swal.fire(
                'Error',
                'Unable to load part items',
                'error'
            );

        }

    });

}


/* ADD LOADED ROW */
function addLoadedRow(
    partIdx,
    itemId,
    qty,
    notes,
    hiNotes,
    itemName,
    itemHiName
) {

    let i = itemIndexMap[partIdx];

    let row = `
    <tr>
      <td>

    <!-- HIDDEN INPUT -->
    <input type="hidden"
           name="parts[${partIdx}][items][${i}][item_id]"
           value="${itemId}">

    <!-- DISABLED SELECT -->
    <select
        class="form-control select2"
        style="width:100%"
        disabled>

        <option value="${itemId}" selected>

            ${itemName}

        </option>

    </select>

    <!-- ITEM HINDI NAME -->
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
                   name="parts[${partIdx}][items][${i}][quantity]"
                   value="${qty}"
                   class="form-control">

        </td>

        <td>

            <input type="text"
                   name="parts[${partIdx}][items][${i}][notes]"
                   value="${notes ?? ''}"
                   class="form-control mb-2"
                   placeholder="Enter Notes">

            <input type="text"
                   name="parts[${partIdx}][items][${i}][hi_notes]"
                   value="${hiNotes ?? ''}"
                   class="form-control"
                   placeholder="नोट्स दर्ज करें">

        </td>

        <td>

            <button type="button"
                    class="btn btn-danger btn-sm removeRow">

                -

            </button>

        </td>

    </tr>
    `;

    $(`#itemTable_${partIdx}`).append(row);

    initSelect2(`#itemTable_${partIdx}`);

    itemIndexMap[partIdx]++;
}
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

/* ADD ITEM */
function addRow(partIdx) {

    let i = itemIndexMap[partIdx];

   let row = `
<tr>

    <td>

        <select
            name="parts[${partIdx}][items][${i}][item_id]"
            class="form-control select2">

            <option value="">Select Item</option>

            @foreach($items as $item)

                <option value="{{ $item->id }}">

                    {{ $item->name }}

                </option>

            @endforeach

        </select>

        <div class="mt-2 p-2 rounded text-center"
             style="
                background:#e8f5e9;
                border:1px solid #c8e6c9;
                color:#1b5e20;
                font-weight:600;
                font-size:14px;
                min-height:38px;
             ">

            हिंदी नाम

        </div>

    </td>

    <td>

        <input type="text"
            name="parts[${partIdx}][items][${i}][quantity]"
            class="form-control">

    </td>

    <td>

        <input type="text"
            name="parts[${partIdx}][items][${i}][notes]"
            class="form-control mb-2"
            placeholder="Enter Notes">

        <input type="text"
            name="parts[${partIdx}][items][${i}][hi_notes]"
            class="form-control"
            placeholder="नोट्स दर्ज करें">

    </td>

    <td>

        <button type="button"
            class="btn btn-danger btn-sm removeRow">

            -

        </button>

    </td>

</tr>
`;

    $(`#itemTable_${partIdx}`).append(row);

initSelect2(`#itemTable_${partIdx}`);
    itemIndexMap[partIdx]++;
}


/* REMOVE ITEM */
$(document).on('click', '.removeRow', function () {
    $(this).closest('tr').remove();
});


/* REMOVE PART */
function removePart(index) {
    $(`#part_${index}`).remove();
}


/* UPDATE SUBMIT */
$('#recipeForm').on('submit', function (e) {

    e.preventDefault();

    if (isSubmitting) return;

    isSubmitting = true;

    let btn = $('button[type=submit]');

    btn.prop('disabled', true).html('Updating...');

    $.ajax({
        url: "{{ route('recipes.update', [$company,$recipe]) }}",
        type: 'POST',
        data: $(this).serialize(),

        success: function () {
            Swal.fire({
                icon: 'success',
                title: 'Updated',
                text: 'Recipe updated successfully',
                timer: 1500,
                showConfirmButton: false
            });

            setTimeout(() => {
                window.location = "{{ route('recipes.index',$company) }}";
            }, 1500);
        },

        error: function () {
            Swal.fire('Error', 'Update failed', 'error');

            isSubmitting = false;
            btn.prop('disabled', false).html('Update Recipe');
        }
    });
});
</script>
@endpush