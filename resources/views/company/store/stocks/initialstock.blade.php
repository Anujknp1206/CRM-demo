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
            <div class="card-header">
                <h3 class="card-title">Stock In Entry</h3>
            </div>

            <div class="card-body">
                <form action="{{ route('addinitialstock', ['company' => $company->id]) }}" method="POST">
                    @csrf

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Brand</th>
                                <th>Location</th>
                                <th>Condition</th>
                                <th>Unit</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($items as $key => $item)
                                <tr>

                                    <td>
                                        {{ $item->name }}

                                        <input type="hidden" name="stocks[{{ $key }}][item_id]" value="{{ $item->id }}">
                                    </td>

                                    <td>
                                        <select name="stocks[{{ $key }}][brand_id]">
                                            <option value="">Select</option>

                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}">
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <input type="number" step="0.01" name="stocks[{{ $key }}][quantity]">
                                    </td>

                                    <td>
                                        <select name="stocks[{{ $key }}][unit_id]">

                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}">
                                                    {{ $unit->name }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <button type="submit" class="btn btn-primary">
                        Save Initial Stock
                    </button>
                </form>
            </div>
        </div>
    </section>
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
                defaultDate: "today",
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
                                                                                    <select name="items[${manualIndex}][item_id]" class="form-control select2 item-select">
                                                                                        <option value="">Select Item</option>
                                                                                        @foreach($items as $id => $name)
                                                                                            <option value="{{ $id }}">{{ $name }}</option>
                                                                                        @endforeach
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

                                                                                <td><input type="number" class="form-control" name="items[${manualIndex}][current_quantity]" value="0" oninput="if(this.value < 0) this.value = 1;"></td>
                                                                                <td><input type="number" class="form-control" name="items[${manualIndex}][min_quantity]" value="0" oninput="if(this.value < 0) this.value = 1;"></td>

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

            newRow.find('.select2').select2({
                dropdownParent: $('#rfiModal'),
                width: '100%'
            });

            manualIndex++;
        });

        // ✅ ENABLE / DISABLE QTY + AUTO SUGGEST
        $(document).on('change', '.modal-item-checkbox', function () {

            let row = $(this).closest('tr');
            let input = row.find('.req-qty');

            if ($(this).is(':checked')) {

                input.prop('disabled', false);

                let current = parseInt(row.find('input[name*="current_quantity"]').val()) || 0;
                let min = parseInt(row.find('input[name*="min_quantity"]').val()) || 0;

                let suggested = (min - current) > 0 ? (min - current) : 1;
                input.val(suggested);

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
        $(document).on('change', '.item-select', function () {

            let itemId = $(this).val();
            let row = $(this).closest('tr');

            if (!itemId) return;

            $.get("{{ route('items.lastRate', $company) }}", {
                item_id: itemId
            }, function (res) {

                row.find('.rate-input').val(res.rate || 0);

            });

        });
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