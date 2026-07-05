@extends('company.layouts.master')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $label }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('company.dashboard', $company) }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $label }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card card-teal">

            {{-- Header --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Stock Overview</h3>
                <div class="ml-auto d-flex gap-2">
                    @can('add rfi')
                        <button class="btn btn-danger btn-sm" id="openRfiModal">
                            <i class="fa fa-exclamation-triangle"></i> Generate RFI
                        </button>
                    @endcan
                     <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            {{-- 🔍 FILTER FORM --}}
            <div class="card-body pb-0">
                <div class="row g-2 align-items-end">

                    <div class="col-md-4">
                        <label>Search Stock</label>
                        <input type="text" id="stock_search" class="form-control" placeholder="Item / Brand / Location">
                    </div>

                    <div class="col-md-3">
                        <label>Status</label>
                        <select id="stock_status" class="form-control">
                            <option value="">All</option>
                            <option value="low">Low Stock</option>
                            <option value="ok">OK Stock</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2 mt-4">
                        <button id="filter" class="btn btn-success w-50">
                            <i class="fa fa-filter"></i> Search
                        </button>

                        <button id="reset" class="btn btn-secondary w-50">
                            <i class="fa fa-undo"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="card-body">
                <div id="loader" style="display:none; text-align:center; padding:20px;">
                    <i class="fa fa-spinner fa-spin" style="font-size:28px; color:#17a2b8;"></i>
                    <p>Loading data...</p>
                </div>

                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>Item</th>
                                <th>Brand</th>
                                <th>Condition</th>
                                <th>Location</th>
                                <th>Unit</th>
                                <th class="text-center">Stock Lvl</th>
                                <th class="text-center">Min Stock Lvl</th>
                                <!-- <th>Price</th> -->
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>

                        <tbody id="stockRows">
                            @include('company.store.stocks.partials.row', ['stocks' => $stocks])
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </section>

    <div class="modal fade" id="rfiModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div id="rfiLoader" class="rfi-loader">
                    <div class="spinner"></div>
                    <p>Loading items...</p>
                </div>
                <div class="modal-header " style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
                    <h5 class="modal-title text-white">
                        <i class="fa fa-exclamation-triangle"></i> Generate RFI
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <form id="rfiForm" method="POST" action="{{ route('rfis.store', $company) }}">
                    @csrf

                    <div class="modal-body">

                        {{-- TOP --}}
                        <div class="row mb-3">

                            {{-- RFI CODE --}}
                            <div class="col-md-4">
                                <label>RFI Code</label>
                                <input type="text" class="form-control" id="rfi_preview_code" value="{{ $previewCode }}"
                                    readonly>
                            </div>

                            {{-- DATE --}}
                            <div class="col-md-4">
                                <label>Date & Time</label>

                                <div class="input-group">
                                    <input type="datetime-local" id="rfi_modal_date" name="rfi_date" class="form-control"
                                        required>

                                    <div class="input-group-append">
                                        <span class="input-group-text" id="rfiCalendarIcon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>Created By</label>
                                <input type="text" class="form-control" value="{{ $previewCreatedBy }}" readonly>
                            </div>
                        </div>
                        {{-- TABLE --}}
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center"><input type="checkbox" id="modalSelectAll"></th>
                                        <th>Item</th>
                                        <th>Brand</th>
                                        <th>Condition</th>
                                        <th>Location</th>
                                        <th>Unit</th>
                                        <th style="width: 10%;" class="text-center">Rate</th>
                                        <th style="width: 10%;" class="text-center">Req Qty</th>
                                        <th class="text-right">
                                            <button type="button" class="btn btn-success btn-sm" id="addManualRow">
                                                <i class="fa fa-plus"></i> Add Item
                                            </button>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody id="rfiModalBody">
                                    {{-- AJAX LOAD --}}
                                </tbody>
                            </table>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label>Remark</label>
                                <textarea id="rfi_modal_remark" name="remark"></textarea>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-save"></i> Create RFI
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>

   <div class="modal fade"
     id="conversionModal"
     tabindex="-1"
     data-backdrop="static"
     data-keyboard="false">

    <div class="modal-dialog">

        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header text-white"
                 style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">

                <h5 class="modal-title">
                    Unit Conversion Required
                </h5>

                <button type="button"
                        class="close text-white"
                        data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            {{-- BODY --}}
            <div class="modal-body">

                <div class="alert alert-info">

                    <strong>
                        No conversion found for this item.
                    </strong>

                    <hr>

                    <p class="mb-2">

                        Please define how this item should be converted.

                    </p>

                    <small>

                        Example:<br>

                        If you purchase in BOX
                        but store stock in PCS:<br><br>

                        <strong>
                            1 BOX = 12 PCS
                        </strong>

                    </small>

                </div>

                <input type="hidden"
                       id="conversion_item_id">

                {{-- PURCHASE UNIT --}}
                <div class="form-group">

                    <label class="font-weight-bold">

                        Purchase Unit

                    </label>

                    <small class="d-block text-muted mb-1">

                        Unit used while purchasing.

                    </small>

                    <select id="conversion_from_unit"
                            class="form-control">

                        <option value="">
                            Select Purchase Unit
                        </option>

                        @foreach($units as $unit)

                            <option value="{{ $unit->id }}">

                                {{ $unit->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- STOCK UNIT --}}
                <div class="form-group">

                    <label class="font-weight-bold">

                        Stock Unit

                    </label>

                    <small class="d-block text-muted mb-1">

                        Unit used for stock storage.

                    </small>

                    <select id="conversion_to_unit"
                            class="form-control">

                        <option value="">
                            Select Stock Unit
                        </option>

                        @foreach($units as $unit)

                            <option value="{{ $unit->id }}">

                                {{ $unit->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- FACTOR --}}
                <div class="form-group">

                    <label class="font-weight-bold">

                        Conversion Factor

                    </label>

                    <small class="d-block text-muted mb-1">

                        Enter how many stock units are equal
                        to 1 purchase unit.

                    </small>

                    <input type="number"
                           step="0.000001"
                           id="conversion_factor"
                           class="form-control"
                           value="1">

                    <small class="text-primary d-block mt-2">

                        Example:<br>

                        1 BOX = 12 PCS<br>

                        Enter factor = 12

                    </small>

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">

                    Cancel

                </button>

                <button type="button"
                        class="btn btn-primary"
                        id="saveConversionBtn">

                    Save Conversion

                </button>

            </div>

        </div>

    </div>

</div>

    </div>
@endsection
@push('styles')
    <style>
        .rfi-loader {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            display: none;

            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .rfi-loader .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #ccc;
            border-top: 4px solid #dc3545;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .no-cursor {
            cursor: grab !important;
        }

        .input-group-text {
            background: #f3f6f9;
            border-right: none;
            font-size: 14px;
        }

        #example1_filter {
            display: none !important;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #17a2b8;
        }

        .input-group .form-control {
            border-radius: 0 6px 6px 0;
        }

        .gap-2 {
            gap: 10px;
        }

        .btn-primary,
        .btn-secondary {
            border-radius: 6px;
            height: 40px;
        }
    </style>
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/summernote/summernote-bs4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{url('/')}}/admin/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script>
        function loadStocks() {
            $('#loader').show();
            $('#stockRows').html('');

            $.get("{{ route('stocks.data', $company) }}", {
                search: $('#stock_search').val(),
                status: $('#stock_status').val()
            }, function (res) {

                $('#stockRows').html(res);
                $('#loader').hide();
            });
        }

        $('#filter').click(function () {
            loadStocks();
        });

        $('#reset').click(function () {
            $('#stock_search').val('');
            $('#stock_status').val('');
            loadStocks();
        });

        // Optional: Live search
        $('#stock_search').on('keyup', function () {
            loadStocks();
        });
    </script>
    <script src="{{url('/')}}/admin/plugins/select2/js/select2.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $('#stock_status').select2({
            width: '100%',
            placeholder: "Select Status",
            allowClear: true
        });

        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
    </script>

    <script>

        let rfiPicker = null;

        // 🚀 OPEN MODAL
        $('#openRfiModal').click(function () {

            $('#rfiModal').modal('show');

            $('#rfiLoader').show();
            // 🔥 SHOW LOADER FIRST
            $('#rfiModalBody').html(`
                                                                                                                                                                                <tr>
                                                                                                                                                                                    <td colspan="10" class="text-center">
                                                                                                                                                                                        <i class="fa fa-spinner fa-spin"></i> Loading items...
                                                                                                                                                                                    </td>
                                                                                                                                                                                </tr>
                                                                                                                                                                            `);
        });


        // 🔥 RUN AFTER MODAL FULLY OPEN
        $('#rfiModal').off('shown.bs.modal').on('shown.bs.modal', function () {

            // ✅ LOAD LOW STOCK ITEMS
            $.get("{{ route('rfis.lowStock', $company) }}", function (res) {

                $('#rfiLoader').hide();
                $('#rfiModalBody').html(res);
                console.log(res);
            });
            // ✅ FLATPICKR FIX
            if (rfiPicker) {
                rfiPicker.destroy();
            }

            rfiPicker = flatpickr("#rfi_modal_date", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                altInput: true,
                altFormat: "d/m/Y h:i K",
                defaultDate: new Date(),
                allowInput: true,
                clickOpens: false // 🔥 IMPORTANT (disable default icon click)
            });

            // calendar icon click
            $('#rfiCalendarIcon').off('click').on('click', function () {
                rfiPicker.open();
            });
            initSummernote();
        });


        // ✅ SELECT ALL
        $(document).on('change', '#modalSelectAll', function () {
            $('.modal-item-checkbox')
                .prop('checked', this.checked)
                .trigger('change');
        });


        // ✅ ADD MANUAL ROW
        let manualIndex = 9999;

        $('#addManualRow').click(function () {

            let row = `
                                                                                                                                <tr>

                                                                                                                                    <td>
                                                                                                                                        <input type="checkbox" class="modal-item-checkbox" checked name="items[${manualIndex}][selected]">
                                                                                                                                    </td>

                                                                                                                                    <td>
                                                                                                                                        <select name="items[${manualIndex}][item_id]" class="form-control select2 item_select" required>
                                                                                                                                        <option value="">Item</option>
                                                                                                                                        </select>
                                                                                                                                    </td>

                                                                                                                                    <td>
                                                                                                                                        <select name="items[${manualIndex}][brand_id]" class="form-control select2">
                                                                                                                                            @foreach($brands as $id => $name)
                                                                                                                                                <option value="{{ $id }}">{{ $name }}</option>
                                                                                                                                            @endforeach
                                                                                                                                        </select>
                                                                                                                                    </td>

                                                                                                                                    <td>
                                                                                                                                       <select name="items[${manualIndex}][condition_id]" class="form-control select2" required>

                                                                                        <option value="">Select Condition</option>

                                                                                        @foreach($conditions as $condition)

                                                                                            <option value="{{ is_object($condition) ? $condition->id : $condition['id'] }}">
                                                                                                {{ is_object($condition) ? $condition->name : $condition['name'] }}
                                                                                            </option>

                                                                                        @endforeach

                                                                                    </select>
                                                                                                                                    </td>

                                                                                                                                    <td>
                                                                                                                                        <select name="items[${manualIndex}][location_id]" class="form-control select2">
                                                                                                                                            @foreach($locations as $id => $name)
                                                                                                                                                <option value="{{ $id }}">{{ $name }}</option>
                                                                                                                                            @endforeach
                                                                                                                                        </select>
                                                                                                                                    </td>
                                                                                                                                    <td>
                                                                                                                                        <select name="items[${manualIndex}][unit_id]" class="form-control select2">
                                                                                                                                            @foreach($units as $unit)

                                                                                                                                                <option value="{{ is_object($unit) ? $unit->id : $unit['id'] }}">
                                                                                                                                                    {{ is_object($unit) ? $unit->name : $unit['name'] }}
                                                                                                                                                </option>

                                                                                                                                            @endforeach
                                                                                                                                        </select>
                                                                                                                                    </td>

                                                                                                                                    <td>
                                                                                                                                        <input type="number" step="0.01"
                                                                                                                                            class="form-control rate-input"
                                                                                                                                            name="items[${manualIndex}][rate]" value="0" oninput="if(this.value < 0) this.value = 1;">
                                                                                                                                    </td>
                                                                                                                                    <td>
                                                                                                                                        <input type="number"
                                                                                                                                            class="form-control req-qty"
                                                                                                                                            name="items[${manualIndex}][requested_quantity]" value="1" oninput="if(this.value < 0) this.value = 1;">
                                                                                                                                    </td>

                                                                                                                                    <td>
                                                                                                                                        <button type="button" class="btn btn-danger btn-sm remove-row">
                                                                                                                                            <i class="fa fa-trash"></i>
                                                                                                                                        </button>
                                                                                                                                    </td>

                                                                                                                                </tr>
                                                                                                                                `;

            // 🔥 INIT SELECT2 FOR NEW ROW
            let newRow = $(row);
            $('#rfiModalBody').append(newRow);

            newRow.find('.select2:not(.item_select)').select2({
                dropdownParent: $('#rfiModal'),
                width: '100%'
            });
            initAjaxItemSelect(newRow);
            manualIndex++;
        });


        function initAjaxItemSelect(context = document) {

            $(context).find('.item_select').each(function () {

                let $this = $(this);

                if ($this.hasClass('select2-hidden-accessible')) {
                    return;
                }

                $this.select2({

                    width: '100%',

                    dropdownParent: $('#rfiModal'),

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
        // ✅ ENABLE / DISABLE QTY + AUTO SUGGEST
        $(document).on('change', '.modal-item-checkbox', function () {

            let row = $(this).closest('tr');
            let input = row.find('.req-qty');

            if ($(this).is(':checked')) {

                input.prop('disabled', false);

                let current = parseInt(row.find('input[name*="current_quantity"]').val()) || 0;
                let min = parseInt(row.find('input[name*="min_quantity"]').val()) || 0;

            } else {
                input.prop('disabled', true).val('');
            }
        });

        // 🔥 SUMMERNOTE FIX (CORRECT WAY)
        function initSummernote() {

            let el = $('#rfi_modal_remark');

            // destroy if already initialized
            if (el.next('.note-editor').length) {
                el.summernote('destroy');
            }

            // small delay ensures modal DOM ready
            setTimeout(() => {
                el.summernote({
                    height: 200,
                    toolbar: [
                        ['style', ['bold', 'clear']],
                        ['font', ['fontsize']],   // enable font size
                        ['para', ['ul', 'ol']],
                        ['view', ['codeview']]
                    ],
                    fontSizes: ['8', '9', '10', '11', '12', '14', '18', '24', '36'], // optional sizes
                    disableDragAndDrop: true,
                    callbacks: {
                        onPaste: function (e) {
                            e.preventDefault();
                            let text = (e.originalEvent || e).clipboardData.getData('text/plain');
                            document.execCommand('insertText', false, text);
                        },
                        onKeydown: function (e) {
                            if (e.keyCode === 13) {
                                document.execCommand('insertLineBreak');
                                e.preventDefault();
                            }
                        }
                    },
                    placeholder: 'Write remark...',
                });
            }, 200);
        }
        $(document).on('click', '.remove-row', function () {

            let row = $(this).closest('tr');

            Swal.fire({
                title: 'Are you sure?',
                text: "This item will be removed!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    row.remove();

                    Swal.fire({
                        icon: 'success',
                        title: 'Removed!',
                        timer: 1200,
                        showConfirmButton: false
                    });
                }
            });

        });
        // ✅ UPDATE RFI CODE
        $(document).on('change', '#rfi_modal_date', function () {

            let date = $(this).val();
            if (!date) return;

            let formatted = date.split(' ')[0].replaceAll('-', '');

            let base = "{{ $company->initials() }}";
            let id = "{{ \App\Models\Rfi::max('id') + 1 }}";

            $('#rfi_preview_code').val(`RFI-${base}-${formatted}-${id}`);
        });

        window.unitsHtml = `
                            @foreach($units as $unit)

                                <option value="{{ $unit->id }}">

                                    {{ $unit->name }}

                                </option>

                            @endforeach
                        `;

        $(document).on(
            'select2:select',
            '.item_select',
            function (e) {

                let data = e.params.data;

                let itemId = data.id;

                let row = $(this).closest('.stock-row');

                if (!itemId) return;

                /*
                |--------------------------------------------------------------------------
                | LAST RATE
                |--------------------------------------------------------------------------
                */

                $.get(
                    "{{ route('items.lastRate', $company) }}",
                    {
                        item_id: itemId
                    },
                    function (res) {

                        row.find('.rate-input')
                            .val(res.rate || 0);

                    }
                );

                /*
                |--------------------------------------------------------------------------
                | UNIT CONVERSIONS
                |--------------------------------------------------------------------------
                */

                let conversions = data.conversions || [];

                /*
                |--------------------------------------------------------------------------
                | NO CONVERSION FOUND
                |--------------------------------------------------------------------------
                */

                if (conversions.length === 0) {

                    /*
                    |--------------------------------------------------------------------------
                    | RESET MODAL
                    |--------------------------------------------------------------------------
                    */

                    $('#conversion_item_id')
                        .val(itemId);

                    $('#conversion_from_unit')
                        .val('');

                    $('#conversion_to_unit')
                        .val('');

                    $('#conversion_factor')
                        .val(1);

                    /*
                    |--------------------------------------------------------------------------
                    | STORE CURRENT ROW
                    |--------------------------------------------------------------------------
                    */

                    $('#conversionModal')
                        .data('row', row);

                    /*
                    |--------------------------------------------------------------------------
                    | OPEN MODAL
                    |--------------------------------------------------------------------------
                    */

                    $('#conversionModal')
                        .modal('show');

                    /*
                    |--------------------------------------------------------------------------
                    | AUTO FOCUS
                    |--------------------------------------------------------------------------
                    */

                    setTimeout(() => {

                        $('#conversion_factor')
                            .trigger('focus');

                    }, 300);

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | AUTO USE FIRST CONVERSION
                |--------------------------------------------------------------------------
                */

                let found = conversions[0];

                /*
                |--------------------------------------------------------------------------
                | AUTO FILL CONVERSION
                |--------------------------------------------------------------------------
                */

                row.find('.conversion-factor')
                    .val(found.factor);

                row.find('.base-unit-name')
                    .val(found.to_unit_name);

                row.find('.base-unit-id')
                    .val(found.to_unit_id);

                /*
                |--------------------------------------------------------------------------
                | OPTIONAL SELECT AUTO FILL
                |--------------------------------------------------------------------------
                */

                row.find('.from-unit-select')
                    .val(found.from_unit_id)
                    .trigger('change');

                row.find('.to-unit-select')
                    .val(found.to_unit_id)
                    .trigger('change');

                /*
                |--------------------------------------------------------------------------
                | HELP TEXT
                |--------------------------------------------------------------------------
                */

                row.find('.conversion-help')
                    .html(
                        `
                    1 ${found.from_unit_name}
                    =
                    ${found.factor}
                    ${found.to_unit_name}
                    `
                    );

                /*
                |--------------------------------------------------------------------------
                | AUTO CALCULATE STOCK QTY
                |--------------------------------------------------------------------------
                */

                let qty =
                    parseFloat(
                        row.find('.req-qty').val()
                    ) || 0;

                let factor =
                    parseFloat(found.factor) || 1;

                row.find('.stock-qty')
                    .val(qty * factor);

            }
        );

        /*
        |--------------------------------------------------------------------------
        | SAVE CONVERSION
        |--------------------------------------------------------------------------
        */

        $('#saveConversionBtn').click(function () {

            let itemId =
                $('#conversion_item_id').val();

            let fromUnit =
                $('#conversion_from_unit').val();

            let toUnit =
                $('#conversion_to_unit').val();

            let factor =
                $('#conversion_factor').val();

            let row =
                $('#conversionModal').data('row');

            /*
            |--------------------------------------------------------------------------
            | VALIDATION
            |--------------------------------------------------------------------------
            */

            if (!fromUnit) {

                alert('Please select purchase unit');

                return;
            }

            if (!toUnit) {

                alert('Please select stock unit');

                return;
            }

            if (!factor || parseFloat(factor) <= 0) {

                alert('Please enter valid factor');

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | SAVE AJAX
            |--------------------------------------------------------------------------
            */

            $.ajax({

                url: "{{ route('item-unit-conversions.ajaxStore', $company) }}",

                type: "POST",

                data: {

                    _token:
                        "{{ csrf_token() }}",

                    item_id: itemId,

                    from_unit_id: fromUnit,

                    to_unit_id: toUnit,

                    factor: factor,
                },

                success: function (res) {

                    let conversion =
                        res.conversion;

                    /*
                    |--------------------------------------------------------------------------
                    | AUTO FILL
                    |--------------------------------------------------------------------------
                    */

                    row.find('.conversion-factor')
                        .val(conversion.factor);

                    row.find('.base-unit-name')
                        .val(conversion.to_unit_name);

                    row.find('.base-unit-id')
                        .val(conversion.to_unit_id);

                    /*
                    |--------------------------------------------------------------------------
                    | OPTIONAL SELECTS
                    |--------------------------------------------------------------------------
                    */

                    row.find('.from-unit-select')
                        .val(conversion.from_unit_id)
                        .trigger('change');

                    row.find('.to-unit-select')
                        .val(conversion.to_unit_id)
                        .trigger('change');

                    /*
                    |--------------------------------------------------------------------------
                    | HELP TEXT
                    |--------------------------------------------------------------------------
                    */

                    row.find('.conversion-help')
                        .html(
                            `
                        1 ${conversion.from_unit_name}
                        =
                        ${conversion.factor}
                        ${conversion.to_unit_name}
                        `
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | AUTO STOCK QTY
                    |--------------------------------------------------------------------------
                    */

                    let qty =
                        parseFloat(
                            row.find('.req-qty').val()
                        ) || 0;

                    let factor =
                        parseFloat(conversion.factor) || 1;

                    row.find('.stock-qty')
                        .val(qty * factor);

                    /*
                    |--------------------------------------------------------------------------
                    | CLOSE MODAL
                    |--------------------------------------------------------------------------
                    */

                    $('#conversionModal')
                        .modal('hide');

                },

                error: function () {

                    alert(
                        'Failed to save conversion'
                    );

                }

            });

        });
/*
|--------------------------------------------------------------------------
| FIX MODAL SCROLL ISSUE
|--------------------------------------------------------------------------
*/

$('#conversionModal').on('hidden.bs.modal', function () {

    // Re-enable scroll for parent modal
    $('body').addClass('modal-open');

    // Optional: restore focus
    $('#rfiModal').focus();

});
        /*
        |--------------------------------------------------------------------------
        | ENTER KEY SUPPORT
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'keydown',
            '#conversion_factor',
            function (e) {

                if (e.key === 'Enter') {

                    e.preventDefault();

                    $('#saveConversionBtn')
                        .click();
                }

            }
        );
        $('#rfiForm').on('submit', function (e) {

            let checkedItems = $('.modal-item-checkbox:checked').length;

            // ❌ NO ITEM SELECTED
            if (checkedItems === 0) {

                e.preventDefault();

                Swal.fire({
                    icon: 'warning',
                    title: 'No Items Selected',
                    text: 'Please select at least one item to create RFI!',
                    confirmButtonColor: '#dc3545'
                });

                return false;
            }

            // ✅ CONFIRM BEFORE SUBMIT
            e.preventDefault(); // stop default submit

            Swal.fire({
                title: 'Are you sure?',
                html: `
                                                                                                        <b>Before submitting RFI:</b><br><br>
                                                                                                        You can edit RFI only if it is <b>NOT approved by admin</b>.<br><br>
                                                                                                        Do you want to continue?
                                                                                                    `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Submit RFI'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#rfiForm')[0].submit(); // manual submit
                }
            });

        });
    </script>
@endpush