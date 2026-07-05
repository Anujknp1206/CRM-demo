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
               <form method="POST" action="{{ route('stock-ins.update', [$company->id, $stockIn->id]) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

                    {{-- =========================
                    SECTION 1: DOCUMENT
                    ========================== --}}
                    <h4 class="text-primary"><b>1. Document Details</b></h4>

                     <div class="row">

                        {{-- 🔵 PO SELECT --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Select Purchase Order</label>

                                <select name="po_display" id="po_select" class="form-control select2" disabled>
                                    <option value="{{ $stockIn->purchaseOrder->id }}" selected>
                                        {{ $stockIn->purchaseOrder->po_code }}
                                    </option>
                                </select>

                                <input type="hidden" name="purchase_order_id" value="{{ $stockIn->purchase_order_id }}">
                            </div>
                        </div>

                        {{-- DOC NO --}}
                        <div class="col-md-4">
                            <label>Supplier Document Number</label>
                            <input type="text" name="sup_doc_num" placeholder="Enter Supplier PO Number"value={{ $stockIn->sup_doc_num }}
                                class="form-control"required readonly>
                        </div>
                        <div class="col-md-4">
                            <label>GRN Date *</label>
                            <div class="input-group">
                                <input type="text" name="doc_date" id="grn_date" class="form-control" value="{{ \Carbon\Carbon::parse($stockIn->grn_date)->format('d/m/Y') }}"
                                    placeholder="DD/MM/YYYY" required readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text calendar-icon" data-target="#grn_date">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        {{-- DOC DATE --}}
                        <div class="col-md-4">
                            <label>PO Date *</label>
                            <div class="input-group">
                                <input type="text" name="po_date" id="po_date" class="form-control" placeholder="DD/MM/YYYY" value="{{ \Carbon\Carbon::parse($stockIn->po_date)->format('d/m/Y') }}"
                                    required readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text calendar-icon" data-target="#po_date">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Supplier Date(Select) *</label>
                            <div class="input-group">
                                <input type="text" name="supplier_date" id="supplier_date" class="form-control" value="{{ \Carbon\Carbon::parse($stockIn->supplier_date)->format('d/m/Y') }}"
                                    placeholder="DD/MM/YYYY" required readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text calendar-icon" data-target="#supplier_date">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </div>


                        {{-- 🔵 SUPPLIER READ ONLY --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Supplier</label>

                                <!-- visible -->
                             <input type="text" id="supplier_name" class="form-control"value="{{ optional($stockIn->supplier)->name }}"readonly>

                                <!-- hidden for backend -->
                               <input type="hidden" name="supplier_id" value="{{ $stockIn->supplier_id }}">
                            </div>
                        </div>

                    </div>

                    <hr>

                    {{-- =========================
                    SECTION 2: STOCK ITEMS
                    ========================== --}}
                    <h4 class="text-primary"><b>2. Stock Items</b></h4>
<div class="table-responsive-custom">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">

    <tr>

        <th>#</th>

        <th>
            <input type="checkbox" id="select_all">
        </th>

        <th>Item</th>

        <th>Brand</th>

        <th>Condition</th>

        <th>Location</th>

        <!-- ENTRY UNIT -->
        <th>
            Entry Unit
        </th>

        <!-- ORDERED -->
        <th>
            Ordered Qty
        </th>

        <!-- REMAINING -->
        <th>
            Remaining Qty
        </th>

        <!-- RECEIVE -->
        <th>
            Receive Qty
        </th>

        <!-- BASE / STOCK UNIT -->
        <th>
            Base Unit
        </th>

        <!-- CONVERSION -->
        <th>
            Conversion Factor
        </th>

        <!-- FINAL STOCK -->
        <th>
            Final Stock Qty
        </th>

        <!-- PO RATE -->
        <th>
            PO Rate
        </th>

        <!-- SUPPLIER RATE -->
        <th>
            Supplier Rate
        </th>

    </tr>

</thead>

                       <tbody id="po_items_table">

@foreach($poItems as $index => $row)

<tr>

    <td>
        {{ $index + 1 }}
    </td>

    <!-- SELECT -->
    <td>

        <input
            type="checkbox"
            name="selected_items[]"
            value="{{ $row['po_item_id'] }}"
            {{ $row['checked'] ? 'checked' : '' }}>

    </td>

    <!-- ITEM -->
    <td>

        {{ $row['item_name'] }}

        <input
            type="hidden"
            name="items[{{ $row['po_item_id'] }}][item_id]"
            value="{{ $row['item_id'] }}">

        <input
            type="hidden"
            name="items[{{ $row['po_item_id'] }}][po_item_id]"
            value="{{ $row['po_item_id'] }}">

    </td>

    <!-- BRAND -->
    <td>

        {{ $row['brand_name'] }}

        <input
            type="hidden"
            name="items[{{ $row['po_item_id'] }}][brand_id]"
            value="{{ $row['brand_id'] }}">

    </td>

    <!-- CONDITION -->
    <td>

        {{ $row['condition_name'] }}

        <input
            type="hidden"
            name="items[{{ $row['po_item_id'] }}][condition_id]"
            value="{{ $row['condition_id'] }}">

    </td>

    <!-- LOCATION -->
    <td>

        {{ $row['location_name'] }}

        <input
            type="hidden"
            name="items[{{ $row['po_item_id'] }}][location_id]"
            value="{{ $row['location_id'] }}">

    </td>

    <!-- ENTRY UNIT -->
    <td>

        {{ $row['unit_name'] }}

        <input
            type="hidden"
            name="items[{{ $row['po_item_id'] }}][entry_unit_id]"
            value="{{ $row['unit_id'] }}">

    </td>

    <!-- ORDERED -->
    <td>
        {{ $row['po_qty'] }}
    </td>

    <!-- REMAINING -->
    @php
        $remaining = $row['po_qty'] - $row['received_qty'];
    @endphp

    <td>

        @if($remaining < 0)

            <span class="text-danger">
                Extra {{ abs($remaining) }}
            </span>

        @else

            {{ $remaining }}

        @endif

    </td>

    <!-- RECEIVE QTY -->
    <td>

        <input
            type="text"
            step="0.000001"
            name="items[{{ $row['po_item_id'] }}][entry_quantity]"
            value="{{ $row['received_qty'] }}"
            class="form-control form-control-sm qty-input"
            oninput="if(this.value < 0) this.value = 1;">

    </td>

    <!-- BASE UNIT -->
    <td>

        <input
            type="hidden"
            name="items[{{ $row['po_item_id'] }}][base_unit_id]"
            value="{{ $row['stock_unit_id'] ?? $row['unit_id'] }}">

        <select
            class="form-control form-control-sm"
            disabled>

            @foreach($units as $unit)

                <option
                    value="{{ $unit->id }}"
                    {{ ($row['stock_unit_id'] ?? $row['unit_id']) == $unit->id
                        ? 'selected'
                        : '' }}>

                    {{ $unit->name }}

                </option>

            @endforeach

        </select>

    </td>

    <!-- CONVERSION FACTOR -->
    <td>

        <input
            type="text"
            step="0.000001"
            name="items[{{ $row['po_item_id'] }}][conversion_factor]"
            value="{{ $row['conversion_factor'] ?? 1 }}"
            class="form-control form-control-sm conversion-factor"
            readonly>

        <small class="text-info">

            1 {{ $row['unit_name'] }}
            =
            {{ $row['conversion_factor'] ?? 1 }}
            {{ $row['base_unit_name'] ?? $row['unit_name'] }}

        </small>

    </td>

    <!-- FINAL STOCK QTY -->
    <td>

        <input
            type="text"
            step="0.000001"
            name="items[{{ $row['po_item_id'] }}][stock_quantity]"
            value="{{ $row['stock_quantity'] }}"
            class="form-control form-control-sm stock-quantity"
            readonly>

    </td>

    <!-- RATE -->
    <td>

        <input
            type="text"
            step="0.01"
            name="items[{{ $row['po_item_id'] }}][rate]"
            value="{{ $row['rate'] }}"
            class="form-control form-control-sm"
            oninput="if(this.value < 0) this.value = 1;">

    </td>

    <!-- SUPPLIER RATE -->
    <td>

        <input
            type="text"
            step="0.01"
            name="items[{{ $row['po_item_id'] }}][supplier_rate]"
            value="{{ $row['supplier_rate'] }}"
            class="form-control form-control-sm"
            oninput="if(this.value < 0) this.value = 1;">

    </td>

</tr>

@endforeach

</tbody>
                    </table>
</div>
                    <hr>

                    {{-- =========================
                    SECTION 3: REMARK
                    ========================== --}}
                    <h4 class="text-primary"><b>3. Remark</b></h4>
                    <textarea name="remark" class="form-control" rows="3">{{ $stockIn->remark }}</textarea>

 <hr>
                    <h4 class="text-primary"><b>4. Supplier Document Upload</b></h4>
                    <div class="form-group mt-3">
                        <label>Supplier Document (Invoice / GRN)</label>

                        <!-- Drop Area -->
                        <div id="drop-area" class="drop-area text-center p-4">

                            <!-- ICON -->
                            <div class="mb-2">
                                <div class="upload-icon">
                                   <i class="fas fa-upload fa-2x"></i>
                                </div>
                            </div>

                            <p class="mb-1 font-weight-bold">Drag & Drop file here</p>
                            <p class="text-muted mb-2">or click to browse</p>

                            <button type="button" class="btn btn-sm btn-success" id="browseBtn">
                                <i class="fa fa-upload"></i> Browse File
                            </button>

                            <!-- Hidden Input -->
                            <input type="file" name="supplier_document" id="supplier_document" hidden>
                        </div>

                        <!-- Preview -->
                        <div id="file_preview" class="mt-3"></div>
                    </div>
<hr>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Update Stock
                    </button>

                </form>
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
#stock_items {
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
#stock_items .select {
    min-width: 180px;
}

#stock_items input.form-control{
    min-width: 120px;
}
.form-control-sm{
    min-width: 100px;
}

.table-responsive-custom {
    overflow-x: auto;
    width: 100%;
}
        .upload-icon {
            width: 70px;
            height: 70px;
            background: #e7f1ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
        }

        .upload-icon i {
            font-size: 28px;
            color: #0d6efd;
        }

        .drop-area {
            border: 2px dashed #0d6efd;
            border-radius: 8px;
            background: #f8f9fa;
            cursor: pointer;
            transition: 0.3s;
        }

        .drop-area.dragover {
            background: #e7f1ff;
            border-color: #0a58ca;
        }
    </style>
@endpush
@push('scripts')
    <script>
         let existingFile = "{{ $stockIn->supplier_document ?? '' }}";
    // Select / Unselect all
    $('#select_all').on('change', function () {
        let checked = $(this).is(':checked');

        $('input[name="selected_items[]"]').prop('checked', checked);
    });

    // If any checkbox unchecked → uncheck select_all
    $(document).on('change', 'input[name="selected_items[]"]', function () {

        let total = $('input[name="selected_items[]"]').length;
        let checked = $('input[name="selected_items[]"]:checked').length;

        $('#select_all').prop('checked', total === checked);
    });
    </script>

      <script>
        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('supplier_document');
        const preview = document.getElementById('file_preview');
        const browseBtn = document.getElementById('browseBtn');

        // Open file dialog
        browseBtn.onclick = () => fileInput.click();
        dropArea.onclick = () => fileInput.click();

        // Drag events
        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('dragover');
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('dragover');
        });

        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('dragover');

            let file = e.dataTransfer.files[0];
            fileInput.files = e.dataTransfer.files;

            showPreview(file);
        });

        // File select
        fileInput.addEventListener('change', function () {
            let file = this.files[0];
            showPreview(file);
        });

        // Preview function
        function showPreview(file) {

            preview.innerHTML = '';

            if (!file) return;

            let url = URL.createObjectURL(file);

            if (file.type.includes('image')) {

                preview.innerHTML = `
                            <p class="text-success">Image Preview:</p>
                            <img src="${url}" style="max-width:250px; border:1px solid #ccc; padding:5px;">
                        `;

            } else if (file.type === 'application/pdf') {

                preview.innerHTML = `
                            <p class="text-success">PDF Preview:</p>
                            <iframe src="${url}" width="100%" height="700px"></iframe>
                        `;

            } else {

                preview.innerHTML = `
                            <p class="text-info">File Selected: ${file.name}</p>
                        `;
            }
        }
         $(document).on(
    'input',
    '.qty-input, .conversion-factor',
    function () {

        let row = $(this).closest('tr');

        let qty =
            parseFloat(
                row.find('.qty-input').val()
            ) || 0;

        let factor =
            parseFloat(
                row.find('.conversion-factor').val()
            ) || 0;

        let finalQty = qty * factor;

        row.find('.stock-quantity')
            .val(finalQty.toFixed(6));

    }
);
        function showExistingPreview(filePath) {

    if (!filePath) return;

    preview.innerHTML = '';

    let extension = filePath.split('.').pop().toLowerCase();

    let url = "/" + filePath;

    // IMAGE
    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {

        preview.innerHTML = `
            <p class="text-success">Existing Image:</p>

            <img src="${url}"
                 style="max-width:250px;
                        border:1px solid #ccc;
                        padding:5px;">
        `;

    }

    // PDF
    else if (extension === 'pdf') {

        preview.innerHTML = `
            <p class="text-success">Existing PDF:</p>

            <iframe src="${url}"
                    width="100%"
                    height="700px"></iframe>
        `;
    }

    // OTHER FILES
    else {

        preview.innerHTML = `
            <a href="${url}"
               target="_blank"
               class="btn btn-primary">

               View File
            </a>
        `;
    }
}// SHOW EXISTING FILE ON EDIT
showExistingPreview(existingFile);
    </script>
@endpush