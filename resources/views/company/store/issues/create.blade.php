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
                        <li class="breadcrumb-item">
                            <a href="{{ route('issues.index', $company) }}">Issue List</a>
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
                <h3 class="card-title mb-0">{{ $label }}</h3>
                <div class="ml-auto">
                    <a href="{{ route('issues.index', $company) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <form id="issueForm" method="POST" action="{{ route('issues.store', $company->id) }}">
                @csrf
                <input type="hidden" name="bom_id" id="hidden_bom_id">
                <div class="card-body">
                    <div class="row">

                        <!-- Issue No -->
                        <div class="col-md-4">
                            <label>Issue No</label>
                            <input type="text" name="issue_no" class="form-control" value="{{ $nextIssueNumber }}" readonly>
                        </div>


                        <!-- Issue Date -->
                        <div class="col-md-4">
                            <label>Issue Date</label>

                            <div class="input-group">

                                <input type="text" name="issue_date" id="issue_date" class="form-control"
                                    value="{{ now()->format('d/m/Y') }}" placeholder="DD/MM/YYYY" readonly>

                                <button type="button" class="btn btn-outline-secondary date-trigger">
                                    <i class="fa fa-calendar"></i>
                                </button>

                            </div>

                        </div>


                        <!-- Issue Time -->
                        <div class="col-md-4">
                            <label>Issue Time</label>

                            <div class="input-group">

                                <input type="text" name="issue_time" id="issue_time" class="form-control"
                                    value="{{ now()->format('h:i A') }}" readonly>

                                <button type="button" class="btn btn-outline-secondary time-trigger">
                                    <i class="fa fa-clock"></i>
                                </button>

                            </div>

                        </div>

                    </div>

                    <hr>

                    <div class="row">

                        <!-- Employee Search -->
                        <div class="col-md-3">
                            <label>Search Employee</label>

                            <select name="employee_id" id="employee_id" class="form-control" required>
                            </select>
                        </div>


                        <!-- Department -->
                        <div class="col-md-3">
                            <label>Department</label>

                            <input type="text" id="department_name" class="form-control" readonly>

                            <input type="hidden" name="department_id" id="department_id">
                        </div>


                        <!-- Order -->
                        <div class="col-md-3">
                            <label>Order ID</label>

                            <input type="text" id="order_id" name="order_id" class="form-control" readonly>
                        </div>


                        <!-- BOM -->
                        <div class="col-md-3">
                            <label>BOM ID</label>

                            <select name="bom_id" id="bom_id" class="form-control" disabled>

                                <option value="">
                                    Select BOM
                                </option>

                            </select>
                        </div>

                    </div>

                    <hr>

                    <h5>Issue Items</h5>

                    <div class="table-responsive">

                        <table class="table table-bordered" id="issueItemsTable">

                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Brand</th>
                                    <th>Condition</th>
                                    <th>Unit</th>
                                    <th>Location</th>
                                    <th>Stock Qty</th>
                                    <th>Req. Qty</th>
                                </tr>
                            </thead>

                            <tbody id="issueItemsBody">

                                <tr>
                                    <td colspan="7" align="center">
                                        Search Employee First
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>


                <div class="card-footer">
                    <button class="btn btn-success" id="saveIssueBtn">
                        Save Issue
                    </button>
                </div>

        </div>

        </form>
        </div>
    </section>

@endsection

@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let unitsList = @json($units);
        $(function () {

            const dateInput = document.getElementById('issue_date');
            const timeInput = document.getElementById('issue_time');


            function openDatePicker() {

                if (dateInput.showPicker) {

                    dateInput.showPicker();

                } else {

                    dateInput.click();

                }

            }


            function openTimePicker() {

                if (timeInput.showPicker) {

                    timeInput.showPicker();

                } else {

                    timeInput.click();

                }

            }


            /* only click, not focus */
            $('#issue_date').on(
                'click',
                openDatePicker
            );

            $('#issue_time').on(
                'click',
                openTimePicker
            );


            /* icons */
            $('.date-trigger').on(
                'click',
                openDatePicker
            );

            $('.time-trigger').on(
                'click',
                openTimePicker
            );

        });
        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
        $('#employee_id').select2({

            placeholder: 'Search Employee',

            ajax: {
                url: "{{ route('ajax.employees.assigned.search', ['company' => $company->id]) }}",
                dataType: 'json',
                delay: 250,

                data: function (params) {
                    return {
                        search: params.term
                    };
                },

                processResults: function (data) {
                    return data;
                }
            }

        });

        $('#employee_id').on(
            'select2:select',
            function (e) {

                let emp = e.params.data;


                /* set department immediately */

                $('#department_id').val(
                    emp.department_id
                );

                $('#department_name').val(
                    emp.department_name
                );

            });

        $(document).ready(function () {


            /*=============================
            EMPLOYEE SELECT
            =============================*/

            $('#employee_id').on(
                'change',
                function () {

                    let employeeId = $(this).val();

                    if (!employeeId) return;


                    $.get(
                        '{{ route("ajax.employee.boms", $company->id) }}',
                        {
                            employee_id: employeeId
                        },
                        function (res) {

                            /* single bom */

                            if (res.boms.length === 1) {

                                let bom = res.boms[0];


                                $('#bom_id')
                                    .html(`
                                    <option value="${bom.id}" selected>
                                    ${bom.bom_number}
                                    </option>
                                    `)
                                    .val(bom.id);

                                $('#hidden_bom_id').val(
                                    bom.id
                                );


                                $('#order_id').val(
                                    bom.order_number
                                );


                                loadBomItems(
                                    employeeId,
                                    bom.id
                                );

                            }



                            /* multiple boms */

                            else if (res.boms.length > 1) {

                                let options =
                                    '<option value="">Select BOM</option>';


                                res.boms.forEach(function (b) {

                                    options += `
                                                                    <option
                                                                    value="${b.id}"
                                                                    data-order="${b.order_number}">
                                                                    ${b.bom_number}
                                                                    </option>
                                                                    `;

                                });


                                $('#bom_id')
                                    .html(options)
                                    .prop('disabled', false);


                                $('#order_id').val('');


                                $('#issueItemsBody').html(`
                                                                                <tr>
                                                                                <td colspan="7" align="center">
                                                                                Select BOM
                                                                                </td>
                                                                                </tr>
                                                                                `);

                            }



                            /* none */

                            else {

                                $('#issueItemsBody').html(`
                                                                                <tr>
                                                                                <td colspan="7" align"center">
                                                                                No BOM assigned
                                                                                </td>
                                                                                </tr>
                                                                                `);

                            }


                        });

                });




            /*=============================
            BOM SELECT
            =============================*/

            $('#bom_id').on(
                'change',
                function () {

                    let bomId = $(this).val();

                    if (!bomId) return;


                    let employeeId =
                        $('#employee_id').val();


                    let orderId = $(
                        this.options[
                        this.selectedIndex
                        ]).data('order');

                    let orderCode = $(
                        this.options[
                        this.selectedIndex
                        ]).data('order');

                    $('#order_id').val(
                        orderCode
                    );


                    loadBomItems(
                        employeeId,
                        bomId
                    );


                });






            /*=============================
            LOAD BOM ITEMS
            =============================*/

            function loadBomItems(
                employeeId,
                bomId
            ) {

                $.get(
                    '{{ route("ajax.employees.working.features", ["company" => $company->id]) }}',
                    {
                        employee_id: employeeId,
                        bom_id: bomId
                    },
                    function (data) {

                        let rows = '';


                        if (data.items.length === 0) {

                            rows = `
                                                                                <tr>
                                                                                <td colspan="7"align="center">
                                                                                No Working Features Found / All Items already issued
                                                                                </td>
                                                                                </tr>
                                                                                `;

                            $('#issueItemsBody').html(
                                rows
                            );

                            return;

                        }



                        data.items.forEach(function (item) {
                            if (!item.unit_id) {

                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Base Unit Missing',
                                    text: item.item_name + ' does not have a base unit.',
                                    confirmButtonText: 'Set Base Unit'
                                }).then((result) => {

                                    if (result.isConfirmed) {

                                        window.open(
                                            "{{ route('items.edit', [$company->id, ':id']) }}"
                                                .replace(':id', item.item_id),
                                            '_blank'
                                        );

                                    }

                                });

                                return;
                            }
                            rows += `

                                                                                <tr>

                                                                                <td>

                                                                                ${item.item_name}

                                                                                <input
                                                                                type="hidden"
                                                                                name="item_id[]"
                                                                                value="${item.item_id}">

                                                                                <input
                                                                                type="hidden"
                                                                                name="bom_item_id[]"
                                                                                value="${item.bom_item_id}">

                                                                                </td>



                                                                                <td>

                                                                                <select
                                                                                class="brand-select form-control select2"
                                                                                data-row="${item.bom_item_id}"
                                                                                data-item="${item.item_id}"
                                                                                name="brand_id[]"required>

                                                                                <option value="">
                                                                                Select Brand
                                                                                </option>

                                                                                ${data.brands.map(b =>
                                `<option value="${b.id}">
                                                                                ${b.name}
                                                                                </option>`
                            ).join('')}

                                                                                </select>

                                                                                </td>



                                                                                <td>

                                                                                <select
                                                                                class="condition-select form-control select2"
                                                                                data-row="${item.bom_item_id}"
                                                                                data-item="${item.item_id}"
                                                                                name="condition_id[]" required>

                                                                                <option value="">
                                                                                Select Condition
                                                                                </option>

                                                                                ${data.conditions.map(c =>
                                `<option value="${c.id}">
                                                                                ${c.name}
                                                                                </option>`
                            ).join('')}

                                                                                </select>

                                                                                </td>



                                                                           <td>

            <select
                class="form-control unit-${item.bom_item_id} select2"
                disabled
            >

                <option
                    value="${item.unit_id}"
                    selected
                >
                    ${item.unit_name ?? '-'}
                </option>

            </select>

            <input
                type="hidden"
                class="hidden-unit-${item.bom_item_id}"
                name="unit_id[]"
                value="${item.unit_id}"
            >

        </td>



                                                                                <td>

                                                                                <select
                                                                                class="location-${item.bom_item_id} form-control"
                                                                                name="location_id[]" required>

                                                                                <option>
                                                                                Select Location
                                                                                </option>

                                                                                </select>

                                                                                </td>



                                                                                <td class="stock-${item.bom_item_id}">
                                                                                0
                                                                                </td>



                                                                                <td>

                                                                                <input
                                                                                type="text"
                                                                                name="issue_qty[]"
                                                                                class="form-control"
                                                                                value="${item.pending_qty}"
                                                                                max="${item.pending_qty}"  oninput="this.value = Math.min(${item.pending_qty},Math.max(1, this.value));">

                                                                                </td>

                                                                                </tr>

                                                                                `;

                        });


                        $('#issueItemsBody').html(
                            rows
                        );


                    });

            }






            /*=============================
            BRAND + CONDITION CHANGE
            =============================*/

            $(document).on(
                'change',
                '.brand-select,.condition-select',
                function () {

                    let row = $(this).data('row');

                    let itemId = $(this).data('item');

                    let brandId = $(
                        '.brand-select[data-row="' + row + '"]'
                    ).val();

                    let conditionId = $(
                        '.condition-select[data-row="' + row + '"]'
                    ).val();

                    /*
                    |--------------------------------------------------------------------------
                    | REQUIRE BRAND + CONDITION
                    |--------------------------------------------------------------------------
                    */

                    if (!brandId || !conditionId)
                        return;

                    /*
                    |--------------------------------------------------------------------------
                    | FETCH STOCK DETAILS
                    |--------------------------------------------------------------------------
                    */

                    $.get(
                        '/company/{{ $company->id }}/ajax/stock-details',
                        {
                            item_id: itemId,
                            brand_id: brandId,
                            condition_id: conditionId
                        },

                        function (res) {

                            /*
                            |--------------------------------------------------------------------------
                            | UNITS
                            |--------------------------------------------------------------------------
                            */
                            /*
                            |--------------------------------------------------------------------------
                            | UNITS
                            |--------------------------------------------------------------------------
                            */

                            let units = '';

                            let selectedUnitId =
                                res.default_unit_id || res.units[0]?.id || '';

                            res.units.forEach(function (u) {

                                let selected =
                                    selectedUnitId == u.id
                                        ? 'selected'
                                        : '';

                                units += `
            <option value="${u.id}" ${selected}>
                ${u.name}
            </option>
        `;

                            });

                            /*
                            |--------------------------------------------------------------------------
                            | UPDATE UNIT SELECT
                            |--------------------------------------------------------------------------
                            */

                            $('.unit-' + row).html(units);

                            /*
                            |--------------------------------------------------------------------------
                            | UPDATE HIDDEN UNIT INPUT
                            |--------------------------------------------------------------------------
                            */

                            $('.hidden-unit-' + row).val(
                                selectedUnitId
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | REFRESH SELECT2
                            |--------------------------------------------------------------------------
                            */

                            $('.unit-' + row).trigger('change');
                            /*
                            |--------------------------------------------------------------------------
                            | LOCATIONS
                            |--------------------------------------------------------------------------
                            */

                            let locations = '';

                            res.locations.forEach(function (l) {

                                locations += `
                                    <option value="${l.id}">
                                        ${l.name}
                                        (Stock:${l.stock})
                                    </option>
                                `;

                            });

                            $('.location-' + row).html(
                                locations
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | REFRESH SELECT2
                            |--------------------------------------------------------------------------
                            */

                            $('.location-' + row)
                                .trigger('change');

                            /*
                            |--------------------------------------------------------------------------
                            | AVAILABLE STOCK
                            |--------------------------------------------------------------------------
                            */

                            $('.stock-' + row).html(
                                res.available
                            );

                        }

                    );

                }

            );
        });
        $('#issueForm').on('submit', function (e) {

            let hasAnyStock = false; // ✅ track ANY stock

            $('#issueItemsBody tr').each(function () {

                let stockText = $(this).find('[class^="stock-"]').text().trim();
                let stockQty = parseInt(stockText) || 0;

                if (stockQty > 0) {
                    hasAnyStock = true; // ✅ at least one item has stock
                }
            });

            // 🔥 BLOCK ONLY IF ALL ARE ZERO
            if (!hasAnyStock) {

                Swal.fire({
                    icon: 'error',
                    title: 'Out of Stock',
                    text: 'Cannot issue item because stock is not available.',
                    confirmButtonText: 'OK'
                });

                e.preventDefault();
                return false;
            }
        }); function checkAnyStockAvailable() {

            let hasStock = false;

            $('.item_id').each(function () {

                let stock = $(this).find(':selected').data('stock');

                if (stock > 0) {
                    hasStock = true;
                }
            });

            return hasStock;
        }

    </script>

@endpush