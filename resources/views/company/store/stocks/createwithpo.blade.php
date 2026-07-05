@extends('company.layouts.master')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Add Stock In</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('company.dashboard', $company->id) }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('stock-ins.index', $company->id) }}">Stock In</a>
                        </li>
                        <li class="breadcrumb-item active">Add</li>
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
                              
                                <a href="{{ route('stock-ins.index', $company->id) }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>

            <div class="card-body">
                <form method="POST" action="{{ route('stock-ins.store', $company->id) }}" autocomplete="off"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="doc_no" class="form-control" value="{{ $docNo }}" readonly>
                       
                    {{-- =========================
                    SECTION 1: DOCUMENT
                    ========================== --}}
                    <h4 class="text-primary"><b>1. Document Details</b></h4>

                    <div class="row">

                        {{-- 🔵 PO SELECT --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Select Purchase Order</label>
                                <select name="purchase_order_id" id="po_select" class="form-control select2" required></select>
                            </div>
                        </div>

                        {{-- DOC NO --}}
                        <div class="col-md-4">
                            <label>Supplier Document Number</label>
                            <input type="text" name="sup_doc_num" placeholder="Enter Supplier PO Number"
                                class="form-control"required>
                        </div>
                        <div class="col-md-4">
                            <label>GRN Date *</label>
                            <div class="input-group">
                                <input type="text" name="doc_date" id="grn_date" class="form-control" value="{{ $grn_date }}"
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
                                <input type="text" name="po_date" id="po_date" class="form-control" placeholder="DD/MM/YYYY" value="{{ $po_date }}"
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
                                <input type="text" name="supplier_date" id="supplier_date" class="form-control"
                                    placeholder="DD/MM/YYYY" required>
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
                                <input type="text" id="supplier_name" class="form-control" readonly>

                                <!-- hidden for backend -->
                                <input type="hidden" name="supplier_id" id="supplier_id">
                            </div>
                        </div>

                    </div>

                    <hr>

                    {{-- =========================
                    SECTION 2: STOCK ITEMS
                    ========================== --}}
                    <h4 class="text-primary"><b>2. Stock Items</b></h4>
   <div class="table-responsive-custom">
                    <table id="stock_items" class="table table-bordered table-striped">
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
                            <tr>
                                <td colspan="14" class="text-center text-muted">
                                    Select a Purchase Order to load items
                                </td>
                            </tr>
                        </tbody>
                    </table>
</div>
                    <hr>

                    {{-- =========================
                    SECTION 3: REMARK
                    ========================== --}}
                    <h4 class="text-primary"><b>3. Remark</b></h4>
                    <textarea name="remark" class="form-control" rows="3"></textarea>

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
                        <i class="fa fa-save"></i> Save Stock
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

    let units = @json($units);

</script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // only initialize for supplier date
        const supplierPicker = flatpickr("#supplier_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y"
        });

        // 🔥 open ONLY when supplier icon clicked
        $(document).on('click', '.calendar-icon', function () {

            let target = $(this).data('target');

            if (target === '#supplier_date') {
                supplierPicker.open();
            }

        });
    </script>
    <script>

        // ================= REMOVE ROW WITH SWEET ALERT =================
        let isSubmitting = false;

        $('form').on('submit', function (e) {

            if (isSubmitting) return false;

            let checkedRows = $('.receive-check:checked').length;
 let supplierDate = $('#supplier_date').val();


    if (!supplierDate) {

        e.preventDefault(); // ❌ stop form submit

        Swal.fire({
            icon: 'warning',
            title: 'Missing Supplier Date',
            text: 'Please select supplier date before submitting.',
            confirmButtonText: 'OK'
        }).then(() => {
            $('#supplier_date').focus(); // 🔥 focus field
        });

        return false;
    }
            /* =========================
               ❌ NO ITEM SELECTED
            ========================= */
            if (checkedRows === 0) {
                e.preventDefault();

                Swal.fire({
                    icon: 'warning',
                    title: 'No Items Selected',
                    text: 'Please select at least one item to receive.',
                    confirmButtonText: 'OK'
                });

                return false;
            }

            /* =========================
               ✅ REMOVE UNCHECKED ITEMS
            ========================= */
            $('.receive-check').each(function () {

                if (!$(this).is(':checked')) {
                    let row = $(this).closest('tr');

                    // remove all input names so backend ignores
                    row.find('input').removeAttr('name');
                }
            });

            /* =========================
               🔒 PREVENT DOUBLE SUBMIT
            ========================= */
            isSubmitting = true;

            let $btn = $('#saveStockBtn');

            $btn.prop('disabled', true);
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        });

        $(document).on('change', '#po_select', function () {


            let poId = $(this).val();
            if (!poId) return;

            // loading state
            $('#po_items_table').html(`
                                                                                <tr>
                                                                                    <td colspan="14" class="text-center">Loading...</td>
                                                                                </tr>
                                                                            `);

            $.ajax({
                url: "{{ route('po.items', [$company->id, 'PO_ID']) }}"
                    .replace('PO_ID', poId),

                type: "GET",
                success: function (res) {

                    /* =========================
                     ✅ SET SUPPLIER (READ ONLY)
                    ========================= */
                    $('#supplier_name').val(res.supplier_name);
                    $('#supplier_id').val(res.supplier_id);
                    $('#po_date').val(res.po_date);
                    /* =========================
                     ✅ LOAD ITEMS
                    ========================= */
                    let html = '';

                    res.items.forEach((item, index) => {

                        if (item.remaining_qty <= 0) return;

                      html += `

<tr>

    <td>
        ${index + 1}
    </td>

    <td>
        <input type="checkbox" class="receive-check">
    </td>

    <td>

        ${item.item_name ?? '-'}

        <input
            type="hidden"
            name="items[${index}][item_id]"
            value="${item.item_id}">

        <input
            type="hidden"
            name="items[${index}][po_item_id]"
            value="${item.po_item_id}">

    </td>

    <td>

        ${item.brand_name ?? '-'}

        <input
            type="hidden"
            name="items[${index}][brand_id]"
            value="${item.brand_id ?? ''}">

    </td>

    <td>

        ${item.condition_name ?? '-'}

        <input
            type="hidden"
            name="items[${index}][condition_id]"
            value="${item.condition_id ?? ''}">

    </td>

    <td>

        ${item.location_name ?? '-'}

        <input
            type="hidden"
            name="items[${index}][location_id]"
            value="${item.location_id}">

    </td>

    <!-- ENTRY UNIT -->
    <td>

        ${item.unit_name ?? '-'}

        <input
            type="hidden"
           name="items[${index}][entry_unit_id]"
            value="${item.unit_id}">

    </td>

    <!-- ORDERED -->
    <td>
        ${item.ordered_qty}
    </td>

    <!-- REMAINING -->
    <td class="text-success">
        ${item.remaining_qty}
    </td>

    <!-- RECEIVE QTY -->
    <td>

        <input
            type="text"
           name="items[${index}][entry_quantity]"
            value="${item.remaining_qty}"
            step="0.000001"
            class="form-control form-control-sm qty-input"
            oninput="if(this.value < 0) this.value = 1;">

    </td>

    <!-- STOCK UNIT -->
   <td>

    <input
        type="hidden"
       name="items[${index}][base_unit_id]"
        value="${item.base_unit_id || item.unit_id}">

    <select
        class="form-control form-control-sm"
        disabled>

        ${units.map(unit => `

            <option
                value="${unit.id}"

                ${unit.id == (item.base_unit_id || item.unit_id)
                    ? 'selected'
                    : ''}>

                ${unit.name}

            </option>

        `).join('')}

    </select>

</td>

    <!-- CONVERSION FACTOR -->
    <td>

        <input
            type="text"
            step="0.000001"
            name="items[${index}][conversion_factor]"
            value="${item.conversion_factor ?? 1}"
            class="form-control form-control-sm conversion-factor"
            oninput="if(this.value < 0) this.value = 1;" readonly>

        <small class="text-info">

            1 ${item.unit_name ?? ''}

            =

            ${item.conversion_factor ?? 1}

            ${item.base_unit_name ?? item.unit_name ?? ''}

        </small>

    </td>

    <!-- FINAL STOCK QTY -->
    <td>

        <input
            type="text"
            name="items[${index}][stock_quantity]"
            value="${item.stock_quantity ?? item.remaining_qty}"
            step="0.000001"
            class="form-control form-control-sm stock-quantity"
            readonly>

    </td>

    <!-- RATE -->
    <td>

        <input
            type="text"
            name="items[${index}][rate]"
            value="${item.rate ?? 0}"
            class="form-control form-control-sm"
            oninput="if(this.value < 0) this.value = 1;">

    </td>

    <!-- SUPPLIER RATE -->
    <td>

        <input
            type="text"
            name="items[${index}][supplier_rate]"
            class="form-control form-control-sm supplier-rate"
            step="0.01"
            value="1"
            oninput="if(this.value < 0) this.value = 1;">

    </td>

</tr>

`;
                    });

                    if (html === '') {
                        html = `
                                                                                        <tr>
                                                                                            <td colspan="14" class="text-center text-danger">
                                                                                                All items already received
                                                                                            </td>
                                                                                        </tr>`;
                    }

                    $('#po_items_table').html(html);
                }
            });
        });
        $(document).ready(function () {

            $('#po_select').select2({
                placeholder: 'Search PO...',
                ajax: {
                    url: "{{ route('po.search', $company->id) }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(item => ({
                                id: item.id,
                                text: `${item.po_code ?? 'PO-' + item.id} | ${item.supplier_name}`
                            }))
                        };
                    }
                }
            });

            let selectedPo = "{{ $selected_po_id ?? '' }}";

            if (selectedPo) {

                // Fetch actual selected PO data
                $.ajax({
                    url: "{{ route('po.search', $company->id) }}",
                    data: {
                        search: selectedPo
                    },
                    success: function (data) {

                        let po = data.find(item => item.id == selectedPo);

                        if (po) {
                            let option = new Option(
                                `${po.po_code ?? 'PO-' + po.id} | ${po.supplier_name}`,
                                po.id,
                                true,
                                true
                            );

                            $('#po_select')
                                .append(option)
                                .trigger('change');
                        }
                    }
                });

            }

        });
        $(document).on('change', '.receive-check', function () {

            let row = $(this).closest('tr');
            let isChecked = $(this).is(':checked');

            row.find('input[type="text"]').prop('disabled', !isChecked);

        });
        $(document).on('input', '.qty-input', function () {

            let max = parseFloat($(this).attr('max'));
            let val = parseFloat($(this).val());

            if (val > max) {
                $(this).val(max);
            }
        });
        $('#select_all').on('change', function () {
            $('.receive-check').prop('checked', this.checked).trigger('change');
        });
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
    </script>
@endpush