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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">{{ $label }}</h3>

                <div class="ml-auto">
                    <button class="btn btn-sm btn-info view-returns">
                        <i class="fa fa-history"></i> Returns
                    </button>
                    @can('add issue')

                        <a href="{{ route('issues.create', ['company' => $company->id]) }}"
                            onclick="return handleBomClick(event, this)" data-company-id="{{ $company->id }}">
                            <button class="btn btn-default btn-sm">
                                <i class="fa fa-plus"></i> New Issue
                            </button>
                        </a>
                    @endcan
                    <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>


            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="example1">
                        <thead>
                            <th>#</th>
                            <th>Issue No</th>
                            <th>Date & Time</th>
                            <th>BOM</th>
                            <th>Department</th>
                            <th>Employee</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @forelse($issues as $i)
                                @php
                                    $status = $i->dynamic_status;

                                    $rowClass = match ($status) {
                                        'issued' => 'table-success',
                                        'partial' => 'table-warning',
                                        default => 'table-danger'
                                    };
                                @endphp

                                <tr class="{{ $rowClass }}" id="issue-row-{{ $i->id }}">
                                    <td>{{ $loop->iteration }}</td>

                                    {{-- Issue No --}}
                                    <td>{{ $i->issue_no }}</td>

                                    {{-- Date + Time --}}
                                    <td>
                                        {{ \Carbon\Carbon::parse($i->issue_date)->format('d/m/Y') }}
                                        <br>
                                        <small>{{ $i->issue_time }}</small>
                                    </td>

                                    {{-- BOM --}}
                                    <td>{{ $i->bom->bom_number ?? '-' }}</td>

                                    {{-- Department --}}
                                    <td>{{ $i->department->name ?? '-' }}</td>

                                    {{-- Employee --}}
                                    <td>
                                        {{ $i->employee->first_name ?? '' }}
                                        {{ $i->employee->last_name ?? '' }}
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        @php $status = $i->dynamic_status; @endphp

                                        @if($status == 'issued')
                                            <span class="badge badge-success">Issued</span>

                                        @elseif($status == 'partial')
                                            <span class="badge badge-warning">Partial</span>

                                        @else
                                            <span class="badge badge-danger">Pending</span>
                                        @endif
                                    </td>
                                    {{-- Actions --}}
                                    <td>

                                        @can('return issue')
                                            @if ($i->dynamic_status != 'pending')
                                                <button class="btn btn-sm text-warning return-btn" data-issue="{{ $i->id }}"
                                                    title="Return Issued Items">
                                                    <i class="fa fa-undo"></i>
                                                </button>
                                            @endif
                                        @endcan

                                        @can('print issue')
                                            <a href="{{ route('issues.print', [$company, $i->id]) }}" class="btn btn-sm"
                                                title="Print Issue Recipt">
                                                <i class="fa fa-print text-primary"></i>
                                            </a>
                                        @endcan

                                        @can('view issue')
                                            <a href="javascript:void(0)" class="view-issue btn btn-sm" data-id="{{ $i->id }}"
                                                title="View Issue Items Details">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        @endcan

                                        @can('edit issue')
                                            @if($i->status == 'partial')
                                                <a href="{{ route('issues.edit', [$company->id, $i->id]) }}" class=" btn btn-sm"
                                                    data-id="{{ $i->id }}" onclick="return handleBomClick(event, this)" data-company-id="{{ $company->id }}">
                                                    <i class="fa fa-edit text-success"></i>
                                                </a>
                                            @endif
                                        @endcan

                                        @if($i->status == 'pending')
                                            @role('Super Admin|Admin')
                                            <button class="delete-issue btn btn-sm" data-id="{{ $i->id }}"
                                                data-name="{{ $i->issue_no }}" style="border:none;background:none">
                                                <i class="fa fa-trash text-danger"></i>
                                            </button>
                                            @endrole
                                        @endif

                                    </td>

                                </tr>
                            @empty
                                <tr id="no-issue-row">
                                    <td colspan="8" class="text-center">
                                        😢 No issues found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="issueModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header text-white" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
                    <h5 class="modal-title" id="issueModalTitle">Create Issue</h5>
                    <button class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <form id="issueForm" autocomplete="off">
                    @csrf
                    <input type="hidden" id="issue_id">
                    <div class="modal-body">

                        {{-- ================= HEADER ================= --}}
                        <div class="row">
                            <div class="col-md-3">
                                <label>Issue No</label>
                                <input type="text" name="issue_no" id="issue_no" class="form-control" readonly>
                            </div>

                            <div class="col-md-3">
                                <label>Issue Date *</label>
                                <input type="text" id="issue_date" name="issue_date" class="form-control" required>
                            </div>

                            <div class="col-md-3">
                                <label>Issue Time *</label>
                                <input type="time" id="issue_time" name="issue_time" class="form-control" required>
                            </div>

                            <div class="col-md-3">
                                <label>Order</label>
                                <input type="text" id="order_display" class="form-control" readonly>
                                <input type="hidden" name="order_id" id="order_id">
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col">
                                <label>Department</label>
                                <select name="department_id" id="department_id" class="form-control select2" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $d)
                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col">
                                <label>Employee</label>
                                <select name="employee_id" id="employee_id" class="form-control select2" required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $e)
                                        <option value="{{ $e->id }}">
                                            {{ $e->first_name }} {{ $e->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label>BOM</label>
                                <select id="bom_id" name="bom_id" class="form-control select2">
                                    <option value="">Select BOM</option>
                                    @foreach($boms as $bom)
                                        <option value="{{ $bom->id }}">
                                            {{ $bom->bom_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label>Remark</label>

                                <div class="input-group">
                                    <input type="text" name="remark" id="remark" class="form-control" readonly>

                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-success" id="openRemarkModal">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- ================= ITEMS ================= --}}
                        <h6><b>Issue Items</b></h6>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="issueItemsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="12%" class="text-center">Item</th>
                                        <th width="12%" class="text-center">Brand</th>
                                        <th width="12%" class="text-center">Condition</th>
                                        <th width="12%" class="text-center">Unit</th>
                                        <th width="12%" class="text-center">Location</th>
                                        <th width="12%" class="text-center">Available (In Stock)</th>
                                        <th width="10%" class="text-center">Qty (Req.)</th>
                                    </tr>

                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" id="saveIssueBtn" class="btn btn-success">
                            <i class="fa fa-save"></i> Save Issue
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="viewIssueModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header text-white" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
                    <h5 class="modal-title">Issue Details</h5>
                    <button class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div id="viewIssueContent">Loading...</div>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="returnModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl">
            <form id="returnForm" autocomplete="off">
                @csrf
                <input type="hidden" name="issue_id" id="return_issue_id">

                <div class="modal-content">

                    <div class="modal-header text-white"
                        style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
                        <h5 class="modal-title">Return Issued Items</h5>
                        <button class="close text-white" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">

                        <div id="returnIssueHeader" class="mb-3"></div>

                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Brand</th>
                                    <th>Condition</th>
                                    <th>Location</th>
                                    <th>Unit</th>
                                    <th>Issued</th>
                                    <th>Return Qty</th>
                                </tr>
                            </thead>
                            <tbody id="returnItemsTable"></tbody>
                        </table>

                        <div class="form-group mt-3">
                            <label>Return Remark</label>
                            <textarea name="remark" id="return_remark" class="form-control"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success">
                            <i class="fa fa-save"></i> Confirm Return
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="returnHistoryModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header text-white" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
                    <h5 class="modal-title">Return Summary</h5>
                    <button class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div id="returnSummaryHeader"></div>
                </div>

            </div>
        </div>
    </div>

@endsection
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* ================= MAIN LAYOUT ================= */

        .return-layout {
            display: flex;
            height: 70vh;
            min-height: 400px;
        }

        /* ================= LEFT PANEL ================= */

        #issueList {
            width: 260px;
            min-width: 220px;
            background: #f4f6f9;
            overflow-y: auto;
            border-right: 1px solid #ddd;
            padding: 5px 0;
        }

        /* Each row */
        .issue-row {
            position: relative;
            padding: 12px 15px 12px 50px;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Hover */
        .issue-row:hover {
            background: #e9ecef;
            cursor: pointer;
        }

        /* Active */
        .issue-row.active {
            background: linear-gradient(to right, #1f4e79, #2c6ea4);
            color: #fff;
            border-left: 4px solid #17a2b8;
            border-radius: 4px;
        }

        /* ================= RIGHT PANEL ================= */

        #issueDetails {
            flex: 1;
            padding: 15px;
            overflow: auto;
        }

        #issueDetails h5 {
            font-weight: bold;
        }

        /* Header */
        .issue-header-row {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .issue-header-row div:first-child {
            text-align: left;
        }

        .issue-header-row div:last-child {
            text-align: right;
        }

        /* Table */
        #issueDetails table {
            margin-top: 10px;
            border-radius: 6px;
            overflow: hidden;
        }

        #issueDetails table thead th {
            background: #f1f3f5;
            text-align: center;
            font-weight: 600;
        }

        #issueDetails table td {
            vertical-align: middle;
        }

        /* Center align */
        #issueDetails td:nth-child(5),
        #issueDetails td:nth-child(6),
        #issueDetails td:nth-child(7) {
            text-align: center;
        }

        /* ================= LARGE SCREENS ================= */

        @media (max-width: 1200px) {
            #issueList {
                width: 220px;
            }
        }

        /* ================= TABLET (768) ================= */

        @media (max-width: 768px) {

            .return-layout {
                flex-direction: column;
                height: auto;
            }

            /* 🔥 SLIDER */
            #issueList {
                display: flex;
                flex-direction: row;
                overflow-x: auto;
                overflow-y: hidden;
                white-space: nowrap;
                width: 100%;
                border-bottom: 1px solid #ddd;
            }

            .issue-row {
                flex: 0 0 auto;
                min-width: 160px;
                text-align: center;
                border-right: 1px solid #ddd;
            }

            /* header stack */
            .issue-header-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .issue-header-row div {
                width: 100%;
                text-align: left !important;
            }

            #issueDetails {
                padding: 10px;
                font-size: 12px;
            }

            #issueDetails h5 {
                font-size: 16px;
            }
        }

        /* ================= MOBILE ================= */

        @media (max-width: 576px) {

            .issue-row {
                min-width: 140px;
                font-size: 13px;
                padding: 8px;
            }

            #issueDetails table {
                font-size: 12px;
            }
        }

        /* ================= MODAL FIX ================= */


        /* ================= SCROLLBAR ================= */

        #issueList::-webkit-scrollbar,
        #issueDetails::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        #issueList::-webkit-scrollbar-thumb,
        #issueDetails::-webkit-scrollbar-thumb {
            background: #bbb;
            border-radius: 10px;
        }

        .select2-container--open .select2-dropdown--above {
            top: 100% !important;
            bottom: auto !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const ITEM_OPTIONS = `{!!
        collect($items)->map(
            fn($i) =>
            "<option value='{$i->id}'>{$i->name}</option>"
        )->implode('')
                                                                                                                                                            !!}`;

        const BRAND_OPTIONS = `<option value="" selected disabled>Select Brand</option>{!!
        collect($brands)->map(
            fn($b) =>
            "<option value='{$b->id}'>{$b->name}</option>"
        )->implode('')
                                                                                                                                                    !!}
                                                                                                                                                    `;

        const CONDITION_OPTIONS = `<option value="" selected disabled>Select Condition</option>{!!
        collect($conditions)->map(
            fn($c) =>
            "<option value='{$c->id}'>{$c->name}</option>"
        )->implode('')
                                                                                                                                                            !!}`;

        const LOCATION_OPTIONS = `<option value="" selected disabled>Select Location</option>{!!
        collect($locations)->map(
            fn($l) =>
            "<option value='{$l->id}'>{$l->name}</option>"
        )->implode('')
                                                                                                                                                            !!}`;
        const UNIT_OPTIONS = `<option value="" disabled selected>Select Unit</option>
                                {!! collect($units)->map(fn($u) => "<option value='{$u->id}'>{$u->name}</option>")->implode('') !!}`;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        /* SAVE */
        $('#issueForm').submit(function (e) {
            e.preventDefault();
            let hasStock = false;

            $('#issueItemsTable tbody tr').each(function () {

                let available = parseInt($(this).find('.available').text()) || 0;

                if (available > 0) {
                    hasStock = true; // ✅ found at least one
                }
            });

            // ❌ BLOCK if all zero
            if (!hasStock) {
                Swal.fire({
                    icon: 'error',
                    title: 'No Stock Available',
                    text: 'All items are out of stock. Cannot create issue.'
                });
                return;
            }
            let btn = $('#saveIssueBtn');

            // 🔥 Disable button
            btn.prop('disabled', true).html('Saving...');
            let id = $('#issue_id').val();
            let url = id
                ? "{{ route('issues.update', [$company, 'ID']) }}".replace('ID', id)
                : "{{ route('issues.store', $company) }}";

            let method = id ? 'PUT' : 'POST';
            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function (res) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Issue created successfully',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = "{{ route('boms.index', $company->id) }}";
                    });

                }

            });
        });
    </script>
    <script>
        let issueDatePicker;

        function openIssueModal() {

            // 🔥 Full reset
            $('#issueForm')[0].reset();
            $('#issue_id').val('');

            // Reset select2
            $('#project_id, #department_id, #employee_id').val(null).trigger('change');

            // Clear items
            $('#issueItemsTable tbody').html('');
            $('#issue_no').val("{{ $nextIssueNumber }}");
            // Date
            flatpickr("#issue_date", {
                dateFormat: "Y-m-d",     // what user sees
                altInput: true,
                altFormat: "d/m/Y",     // display format
                defaultDate: new Date()
            });


            // Time
            let now = new Date();
            let hours = String(now.getHours()).padStart(2, '0');
            let minutes = String(now.getMinutes()).padStart(2, '0');
            $('#issue_time').val(`${hours}:${minutes}`);

            // Title
            $('#issueModalTitle').text('Create Issue');

            // Show modal
            $('#issueModal').modal('show');
        }

    </script>
    <script>
        $(document).on(
            'change',
            '.item-select, .brand-select, .condition-select, .unit-select, .location-select',
            function () {

                let row = $(this).closest('tr');

                let itemId = row.find('.item-select').val();
                let brandId = row.find('.brand-select').val();
                let conditionId = row.find('.condition-select').val();
                let locationId = row.find('.location-select').val();
                let unitId = row.find('.unit-select').val();
                let locationSelect = row.find('.location-select');

                // 🔥 STEP 1: Highlight locations having stock (NOT FILTER)
                if ($(this).hasClass('item-select') ||
                    $(this).hasClass('brand-select') ||
                    $(this).hasClass('condition-select')) {

                    if (!itemId || !brandId || !conditionId) return;

                    $.get("{{ route('ajax.locations.by.item', $company) }}", {
                        item_id: itemId,
                        brand_id: brandId,
                        condition_id: conditionId
                    }, function (stockMap) {

                        let options = [];

                        locationSelect.find('option').each(function () {

                            let val = $(this).val();
                            let text = $(this).text();

                            if (!val) return;

                            let qty = stockMap[val] ?? 0;

                            options.push({
                                id: val,
                                text: qty > 0
                                    ? `${text.split(' (')[0]} (Stock: ${qty})`
                                    : text.split(' (')[0],
                                qty: qty
                            });
                        });

                        // ✅ SORT: highest stock first
                        options.sort((a, b) => b.qty - a.qty);

                        // ✅ Preserve current selected value
                        let currentValue = locationSelect.val();

                        // ✅ Clear & rebuild ONCE
                        locationSelect.empty().append('<option value="">Select Location</option>');

                        options.forEach(opt => {
                            locationSelect.append(new Option(opt.text, opt.id));
                        });

                        // ✅ Restore selection if still exists
                        if (currentValue && options.find(o => o.id == currentValue)) {
                            locationSelect.val(currentValue);
                        } else {
                            locationSelect.val(null);
                        }

                        // ✅ DO NOT destroy select2
                        // ✅ Just trigger update
                        locationSelect.trigger('change.select2');

                    });
                    return;
                }

                // 🔥 STEP 2: Original stock check (UNCHANGED)
                if (!itemId || !brandId || !conditionId || !locationId) {
                    row.find('.available').text(0);
                    return;
                }

                $.get("{{ route('issues.check-stock', $company) }}", {
                    item_id: itemId,
                    brand_id: brandId,
                    condition_id: conditionId,
                    location_id: locationId,
                    unit_id: unitId
                }, function (res) {
                    row.find('.available').text(parseInt(res.available));
                });
            }
        );

        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
    </script>
    <script>
        // $(document).on('click', '.edit-issue', function () {

        //     let id = $(this).data('id');

        //     $('#issueForm')[0].reset();
        //     $('#issueItemsTable tbody').html('');

        //     $.get(
        //         "{{ route('issues.show', [$company, 'ID']) }}".replace('ID', id),
        //         function (res) {
        //             issueDatePicker = flatpickr("#issue_date", {
        //                 dateFormat: "Y-m-d",
        //                 altInput: true,
        //                 altFormat: "d/m/Y"
        //             });
        //             $('#issue_id').val(res.id);
        //             $('#issue_no').val(res.issue_no);
        //             issueDatePicker.setDate(res.issue_date_raw, true);
        //             $('#issue_time').val(res.issue_time);
        //             // 🔥 SET BOM
        //             $('#bom_id').val(res.bom_id).trigger('change');

        //             // 🔥 SET ORDER
        //             $('#order_id').val(res.order_id);
        //             $('#order_display').val(res.order_number);
        //             $('#department_id').val(res.department_id).trigger('change');

        //             $.get("{{ route('ajax.employees.by.department', $company) }}", {
        //                 department_id: res.department_id
        //             }, function (employees) {

        //                 let options = '<option value="">Select Employee</option>';

        //                 employees.forEach(e => {
        //                     options += `<option value="${e.id}">
        //                                             ${e.first_name} ${e.last_name}
        //                                         </option>`;
        //                 });

        //                 $('#employee_id').html(options);

        //                 // 🔥 Now select the employee
        //                 $('#employee_id').val(res.employee_id).trigger('change');
        //             });

        //             $('#remark').val(res.remark);

        //             $('#issueModalTitle').text('Edit Issue');

        //             res.items.forEach((item, index) => {

        //                 let row = `
        //                                     <tr>
        //                                     <td>
        //                                         <select name="items[${index}][item_id]" class="form-control select2 item-select">
        //                                             ${ITEM_OPTIONS}
        //                                         </select>
        //                                     </td>
        //                                     <td>
        //                                         <select name="items[${index}][brand_id]" class="form-control select2 brand-select">
        //                                             ${BRAND_OPTIONS}
        //                                         </select>
        //                                     </td>
        //                                     <td>
        //                                         <select name="items[${index}][condition_id]" class="form-control select2 condition-select">
        //                                             ${CONDITION_OPTIONS}
        //                                         </select>
        //                                     </td>
        //                                     <td>
        //                                         <select name="items[${index}][location_id]" class="form-control select2 location-select">
        //                                             ${LOCATION_OPTIONS}
        //                                         </select>
        //                                     </td>
        //                                     <td class="available text-center">Loading...</td>
        //                                     <td>
        //                                         <input type="number" name="items[${index}][quantity]" class="form-control"
        //                                             value="${item.requested_qty}">
        //                                     </td>

        //                                     </tr>`;

        //                 $('#issueItemsTable tbody').append(row);

        //                 let tr = $('#issueItemsTable tbody tr:last');

        //                 tr.find('.item-select').val(item.item_id);
        //                 tr.find('.brand-select').val(item.brand_id);
        //                 tr.find('.condition-select').val(item.condition_id);
        //                 tr.find('.location-select').val(item.location_id);

        //                 // 🔥 NOW fetch stock (AFTER tr exists)
        //                 $.get("{{ route('issues.check-stock', $company) }}", {
        //                     item_id: item.item_id,
        //                     brand_id: item.brand_id,
        //                     condition_id: item.condition_id,
        //                     location_id: item.location_id
        //                 }, function (res) {
        //                     tr.find('.available').text(res.available);
        //                 });
        //             });



        //             $('#issueModal').modal('show');
        //         }
        //     );
        // });

        $(document).on('click', '.delete-issue', function () {

            let id = $(this).data('id');
            let name = $(this).data('name');

            Swal.fire({
                title: 'Delete Issue?',
                text: `Are you sure you want to delete issue ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {

                if (!result.isConfirmed) return;

                $.ajax({
                    url: "{{ route('issues.destroy', [$company, 'ID']) }}".replace('ID', id),
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (res) {

                        Swal.fire('Deleted!', res.message, 'success');

                        // remove row
                        $('#issue-row-' + id).remove();

                        // 🔥 check if table is empty
                        if ($('#example1 tbody tr').length === 0) {

                            $('#example1 tbody').html(`
                                                                                                                                                                <tr id="no-issue-row">
                                                                                                                                                                    <td colspan="8" class="text-center">
                                                                                                                                                                        😢 No issues found
                                                                                                                                                                    </td>
                                                                                                                                                                </tr>
                                                                                                                                                            `);
                        }
                    },
                    error: function (xhr) {
                        Swal.fire(
                            'Error',
                            xhr.responseJSON?.message ?? 'Cannot delete issue',
                            'error'
                        );
                    }
                });
            });
        });
        $('#issueModal').on('hidden.bs.modal', function () {
            $('#issueForm')[0].reset();
            $('#issueItemsTable tbody').html('');
            $('#project_id, #department_id, #employee_id').val(null).trigger('change');
        });

    </script>
    <script>
        $(document).on('click', '.view-issue', function () {

            let id = $(this).data('id');

            $.get("{{ route('issues.show', [$company, 'ID']) }}".replace('ID', id), function (res) {

                let html = `
                                                                                            <div class="row mb-2">
                                                                                                <div class="col-md-6"><strong>Issue No:</strong> ${res.issue_no}</div>
                                                                                                <div class="col-md-6 text-right"><strong>BOM No:</strong> ${res.bom_number ?? '-'}</div>
                                                                                            </div>

                                                                                            <div class="row mb-2">
                                                                                                <div class="col-md-6"><strong>Department:</strong> ${res.department_name ?? '-'}</div>
                                                                                                <div class="col-md-6 text-right"><strong>Assigned To:</strong> ${res.employee_name ?? '-'}</div>
                                                                                            </div>

                                                                                            <div class="row mb-3">
                                                                                                <div class="col-md-6">
                                                                                                    <strong>Date & Time:</strong> ${res.issue_date} ${res.issue_time}
                                                                                                </div>
                                                                                                <div class="col-md-6 text-right">
                                                                                                    <strong>Status:</strong> 
                                                                                                  <span class="badge badge-${res.status == 'issued' ? 'success' :
                        res.status == 'partial' ? 'warning' : 'danger'
                    }">
                                                                                    ${res.status}
                                                                                </span>
                                                                                                </div>
                                                                                            </div>

                                                                                            <table class="table table-bordered">
                                                                                                <thead class="bg-light">
                                                                                                    <tr>
                                                                                                        <th>Item</th>
                                                                                                        <th>Brand</th>
                                                                                                        <th>Condition</th>
                                                                                                        <th>Unit</th>
                                                                                                        <th>Requested</th>
                                                                                                        <th>Issued</th>
                                                                                                        <th>Pending</th>
                                                                                                        <th>Status</th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                        `;

                res.items.forEach(item => {

                    let statusBadge = '';

                    if (item.issued_qty == 0) {
                        statusBadge = `<span class="badge badge-danger">Pending</span>`;
                    }
                    else if (item.pending_qty == 0) {
                        statusBadge = `<span class="badge badge-success">Issued</span>`;
                    }
                    else {
                        statusBadge = `<span class="badge badge-warning">Partial</span>`;
                    }
                    html += `
                                                                                                <tr>
                                                                                                    <td>${item.item_name}</td>
                                                                                                    <td>${item.brand_name ?? '-'}</td>
                                                                                                    <td>${item.condition_name ?? '-'}</td>
                                                                                                   <td>${item.unit_name ?? '-'}</td>
                                                                                                    <td>${item.requested_qty}</td>
                                                                                                    <td>${item.issued_qty}</td>
                                                                                                    <td>${item.pending_qty}</td>
                                                                                                    <td>${statusBadge}</td>
                                                                                                </tr>
                                                                                            `;
                });

                html += `
                                                                                                </tbody>
                                                                                            </table>

                                                                                            ${res.remark ? `
                                                                                                <div class="mt-3">
                                                                                                    <strong>Remark:</strong>
                                                                                                    <p class="mb-0">${res.remark}</p>
                                                                                                </div>
                                                                                            ` : ''}
                                                                                        `;

                $('#viewIssueContent').html(html);
                $('#viewIssueModal').modal('show');
            });
        });
        $('#department_id').on('change', function () {

            let deptId = $(this).val();

            $('#employee_id').html('<option value="">Loading...</option>').trigger('change');

            if (!deptId) {
                $('#employee_id').html('<option value="">Select Employee</option>');
                return;
            }

            $.get("{{ route('ajax.employees.by.department', $company) }}", {
                department_id: deptId
            }, function (employees) {

                let options = '<option value="">Select Employee</option>';

                employees.forEach(e => {
                    options += `<option value="${e.id}" data-present="${e.is_present}">
                                                ${e.name}
                                            </option>`;
                });

                $('#employee_id').html(options).trigger('change');
                initEmployeeSelect2();
            });
        });
        function initEmployeeSelect2() {

            $('#employee_id').select2({

                dropdownParent: $('#issueModal'),

                templateResult: function (data) {

                    if (!data.id) return data.text;

                    let isPresent = $(data.element).data('present');

                    let bg = isPresent ? '#28a7468c' : '#dc35462c';

                    return $(`
                                                            <div style="
                                                                background:${bg};
                                                                color:white;
                                                                padding:5px;
                                                                border-radius:4px;
                                                            ">
                                                                ${data.text}
                                                            </div>
                                                        `);
                },

                templateSelection: function (data) {

                    if (!data.id) return data.text;

                    let isPresent = $(data.element).data('present');

                    let color = isPresent ? '#28a7468c' : '#dc35462c';

                    return $(`
                                                            <span style="color:${color}; font-weight:600;">
                                                                ${data.text}
                                                            </span>
                                                        `);
                }
            });
        }
    </script>
    <script>
        $(document).on('click', '.return-btn', function () {

            let id = $(this).data('issue');

            $('#returnItemsTable').html('');
            $('#return_issue_id').val(id);

            $.get("{{ route('issues.show', [$company, 'ID']) }}".replace('ID', id),
                function (res) {

                    let now = new Date();
                    let returnDate = now.toLocaleDateString('en-GB');
                    let returnTime = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                    $('#returnIssueHeader').html(`
                                                                    <div class="issue-header-row mb-2">
                                                                        <div><b>Issue No:</b> ${res.issue_no}</div>
                                                                        <div><b>Return Date:</b> ${returnDate} ${returnTime}</div>
                                                                    </div>

                                                                    <div class="issue-header-row">
                                                                        <div><b>Department:</b> ${res.department_name}</div>
                                                                        <div><b>Assigned To:</b> ${res.employee_name}</div>
                                                                    </div>
                                                                `);
                    res.items.forEach((item, index) => {

                        let returned = item.returned_qty ?? 0;
                        let remaining = item.issued_qty - returned;

                        if (remaining <= 0) return; // 🔥 hide fully returned items

                        let row = `
                                                                                        <tr>
                                                                                            <td>${item.item_name}</td>
                                                                                            <td>${item.brand_name}</td>
                                                                                            <td>${item.condition_name}</td>
                                                                                            <td>${item.location_name}</td>
                            <td>${item.unit_name ?? '-'}</td>
                                                                                            <td class="text-center">
                                                                                                ${item.issued_qty}
                                                                                                <br>
                                                                                                <small class="text-success">Returned: ${returned}</small>
                                                                                                <br>
                                                                                                <small class="text-danger">Remaining: ${remaining}</small>
                                                                                            </td>

                                                                                            <td>
                                                                                                <input type="number"
                                                                                                    name="items[${index}][return_qty]"
                                                                                                    class="form-control"
                                                                                                    max="${remaining}"
                                                                                                    step="1"
                                                                                                    required>

                                                                                                <input type="hidden" name="items[${index}][item_id]" value="${item.item_id}">
                                                                                                <input type="hidden" name="items[${index}][brand_id]" value="${item.brand_id}">
                                                                                                <input type="hidden" name="items[${index}][condition_id]" value="${item.condition_id}">
                                                                                                <input type="hidden" name="items[${index}][location_id]" value="${item.location_id}">
                                                                                                <input type="hidden" name="items[${index}][unit_id]" value="${item.unit_id}">
                                                                                            </td>
                                                                                        </tr>
                                                                                    `;

                        $('#returnItemsTable').append(row);
                    });

                    $('#returnModal').modal('show');
                }
            );
        });
        $('#returnForm').submit(function (e) {
            e.preventDefault();

            $.post("{{ route('issues.return', ['company' => $company->id]) }}",
                $(this).serialize(),
                function (res) {

                    Swal.fire('Success', 'Items returned successfully', 'success');

                    window.open(
                        "{{ route('issues.return.print', [$company, 'ID']) }}"
                            .replace('ID', res.return_id),
                        '_blank'
                    );

                    // Close modal
                    $('#returnModal').modal('hide');

                    // Optional: update issue row status to "Partial" or "Completed"
                    if (res.issue) {
                        updateIssueRow(res.issue);
                    }

                    // Reset form
                    $('#returnForm')[0].reset();
                    $('#return_remark').summernote('reset');
                    $('#returnItemsTable').html('');
                }
            );
        });
        $('#return_remark').summernote({
            height: 120,
            placeholder: 'Write return remark...',
            toolbar: [
                ['style', ['bold', 'italic', 'underline']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']],
                ['view', ['codeview']]
            ]
        });

        $(document).on('input', '#returnItemsTable input[type="number"]', function () {
            let max = parseFloat($(this).attr('max'));
            let val = parseFloat($(this).val());

            if (val > max) {
                $(this).val(max);

                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Quantity',
                    text: 'Return quantity cannot exceed issued quantity'
                });
            }

            if (val < 0) {
                $(this).val(0);
            }
        });

    </script>

    <script>
        $(document).on('click', '.view-returns', function () {

            $.get("{{ route('issues.returns.all', $company) }}", function (res) {
                // ✅ CHECK IF EMPTY
                if (!res.data || res.data.length === 0) {

                    $('#returnSummaryHeader').html(`
                                    <div class="text-center p-4">
                                        <h5 class="text-muted">😢 No return yet</h5>
                                    </div>
                                `);

                    $('#returnHistoryModal').modal('show');
                    return;
                }
                let left = '';
                let firstIssue = null;

                res.data.forEach((issue, index) => {

                    if (index === 0) firstIssue = issue;

                    left += `
                                                                                                <div class="issue-row p-2 border-bottom cursor-pointer ${index === 0 ? 'active' : ''}"
                                                                                                     data-id="${issue.issue_id}">
                                                                                                     ${issue.issue_no}
                                                                                                </div>
                                                                                            `;
                });

                $('#returnSummaryHeader').html(`
                                                    <div class="return-layout">
                                                        <div id="issueList">${left}</div>
                                                        <div id="issueDetails"></div>
                                                    </div>
                                                `);

                function renderDetails(issue) {

                    let html = `
                                                                                                <div class="mb-3">
                                                                                                    <h5>Issue No: ${issue.issue_no}</h5>
                                                                                                    <div><b>Department:</b> ${issue.department}</div>
                                                                                                    <div><b>Employee:</b> ${issue.employee}</div>
                                                                                                </div>

                                                                                                <table class="table table-bordered">
                                                                                                    <thead>
                                                                                                        <tr>
                                                                                                            <th>Item</th>
                                                                                                            <th>Brand</th>
                                                                                                            <th>Condition</th>
                                                                                                            <th>Location</th>
                                                                                                            <th>Unit</th>
                                                                                                            <th>Returned Qty</th>
                                                                                                            <th>Date</th>
                                                                                                            <th>Time</th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                            `;

                    issue.items.forEach(item => {
                        html += `
                                                                                                    <tr>
                                                                                                        <td>${item.item}</td>
                                                                                                        <td>${item.brand}</td>
                                                                                                        <td>${item.condition}</td>
                                                                                                        <td>${item.location}</td>
                                                                                                        <td>${item.unit}</td>
                                                                                                        <td class="text-center">${item.qty}</td>
                                                                                                        <td>${item.date}</td>
                                                                                                        <td>${item.time}</td>
                                                                                                    </tr>
                                                                                                `;
                    });

                    html += `
                                            </tbody>
                                            </table>

                                            <div class="d-flex justify-content-end mt-3">
                                                <button class="btn btn-danger btn-sm print-return" data-id="${issue.return_id}">
                                                    <i class="fa fa-file-pdf"></i> Save PDF
                                                </button>
                                            </div>
                                            `;

                    $('#issueDetails').html(html);
                }

                // 🔥 First auto load
                if (firstIssue) renderDetails(firstIssue);

                // 🔥 Click event
                $(document).on('click', '.issue-row', function () {

                    $('.issue-row').removeClass('active');
                    $(this).addClass('active');
                    let id = $(this).data('id');
                    let issue = res.data.find(i => i.issue_id == id);

                    renderDetails(issue);
                });

                $('#returnHistoryModal').modal('show');
            });
        });
        $(document).on('click', '.print-return', function () {

            let id = $(this).data('id');

            if (!id) {
                console.error('Return ID missing ❌');
                return;
            }

            window.open(
                "{{ route('issues.return.print', [$company, 'ID']) }}"
                    .replace('ID', id),
                '_blank'
            );
        });
        $(document).on('input', '.qty-input', function () {

            let max = parseInt($(this).attr('max')) || 0;
            let val = parseInt($(this).val()) || 0;

            if (val > max) {
                $(this).val(max);

                Swal.fire({
                    icon: 'warning',
                    title: 'Limit Exceeded',
                    text: 'Quantity cannot exceed BOM quantity'
                });
            }

            if (val < 0) {
                $(this).val(0);
            }
        });
        function initSelect2(context) {
            $(context).find('select.select2').each(function () {

                let $el = $(this);

                // ❌ DO NOT destroy if already initialized
                if ($el.hasClass('select2-hidden-accessible')) {
                    return; // ✅ JUST SKIP
                }

                $el.select2({
                    width: '100%',
                    dropdownParent: $('#issueModal') // ✅ VERY IMPORTANT
                });
            });
        }
        $('#issueModal').on('shown.bs.modal', function () {
            initSelect2(this);
        });
    </script>

    <script>
        $('#openRemarkModal').click(function () {

            $('#descriptionModalTitle').text('Edit Remark');

            // load existing value into summernote
            let currentValue = $('#remark').val();

            $('#modalDescription').summernote('code', currentValue);

            $('#descriptionModal').modal('show');
        }); $('#saveDescription').click(function () {

            let content = $('#modalDescription').summernote('code');

            // 🔥 remove HTML tags for input preview
            let plainText = $('<div>').html(content).text();

            // set full content (optional hidden input)
            $('#remark').val(plainText);

            // if you want full HTML → store separately
            $('#remark').data('html', content);

            $('#descriptionModal').modal('hide');
        });
        $(document).on('show.bs.modal', '.modal', function () {
            let zIndex = 1050 + (10 * $('.modal:visible').length);
            $(this).css('z-index', zIndex);

            setTimeout(() => {
                $('.modal-backdrop')
                    .not('.modal-stack')
                    .css('z-index', zIndex - 1)
                    .addClass('modal-stack');
            }, 0);
        }); $('#descriptionModal').on('hidden.bs.modal', function () {

            // 🔥 restore issue modal scroll + focus
            $('body').addClass('modal-open');

            $('#issueModal').css('overflow', 'auto');
        });
    </script>

    <script>
        $(document).ready(function () {

            let urlParams = new URLSearchParams(window.location.search);
            let bomId = urlParams.get('bom_id');

            if (bomId) {

                // 🔥 Open modal
                openIssueModal();

                // 🔥 Wait for modal render
                setTimeout(() => {

                    // Set BOM
                    $('#bom_id').val(bomId).trigger('change');

                }, 500);
            }
        });

        $('#bom_id').on('change', function () {

            let bomId = $(this).val();

            if (!bomId) return;

            $.get("{{ route('boms.details', [$company, 'ID']) }}".replace('ID', bomId), function (res) {

                // 🔥 SET ORDER
                $('#order_id').val(res.order_id);
                $('#order_display').val(res.order?.order_number ?? '');

                // 🔥 SET DEPARTMENT
                $('#department_id').val(res.incharge_department_id).trigger('change');

                // 🔥 LOAD EMPLOYEES BASED ON DEPARTMENT
                $.get("{{ route('ajax.employees.by.department', $company) }}", {
                    department_id: res.incharge_department_id
                }, function (employees) {

                    let options = '<option value="">Select Employee</option>';

                    employees.forEach(e => {
                        options += `<option value="${e.id}" data-present="${e.is_present}">
                                                            ${e.name}
                                                        </option>`;
                    });

                    $('#employee_id').html(options).trigger('change');

                    // 🔥 SELECT SUPERVISOR
                    $('#employee_id').val(res.supervisor_id).trigger('change');

                    initEmployeeSelect2();
                });

                // 🔥 LOAD ITEMS
                let tbody = $('#issueItemsTable tbody');
                tbody.html('');

                res.items.forEach((item, index) => {

                    let row = `
                                                        <tr>
                                                            <td>
                                                                <select name="items[${index}][item_id]" class="form-control  item-select" readonly>
                                                                    ${ITEM_OPTIONS}
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select name="items[${index}][brand_id]" class="form-control  brand-select">
                                                                    ${BRAND_OPTIONS}
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select name="items[${index}][condition_id]" class="form-control  condition-select">
                                                                    ${CONDITION_OPTIONS}
                                                                </select>
                                                            </td>
                                                            <td>
                                <select name="items[${index}][unit_id]" class="form-control  unit-select">
                                    ${UNIT_OPTIONS}
                                </select>
                            </td>
                                                            <td>
                                                                <select name="items[${index}][location_id]" class="form-control  location-select">
                                                                    ${LOCATION_OPTIONS}
                                                                </select>
                                                            </td>
                                                            <td class="available text-center">0</td>
                                                            <td>
                                                              <input type="number"
                                            name="items[${index}][quantity]"
                                            class="form-control qty-input"
                                            min="0"
                                            max="${item.quantity}"   // ✅ BOM quantity
                                            step="1"
                                            required
                                            value="${parseInt(item.quantity) || 0}">
                                                                <input type="hidden"name="items[${index}][bom_item_id]"value="${item.bom_item_id || item.id}">
                                                            </td>
                                                        </tr>
                                                    `;

                    tbody.append(row);

                    let tr = tbody.find('tr:last');

                    tr.find('.item-select').val(item.item_id);
                    tr.find('.brand-select').val(item.brand_id);
                    tr.find('.condition-select').val(item.condition_id);

                });

                initSelect2($('#issueItemsTable'));
            });
        });
    </script>
    <script>
        const attendanceCheckUrl = "{{ url('company') }}";
        function handleBomClick(e, el) {

            e.preventDefault();

            let companyId = $(el).data('company-id');

            $.ajax({
                url: attendanceCheckUrl + '/' + companyId + '/check-attendance',
                type: 'GET',
                success: function (response) {

                    if (!response.attendanceMarked) {

                        Swal.fire({
                            title: "Attendance Required",
                            text: "Today's attendance is not marked.",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonText: "Go to Attendance",
                            cancelButtonText: "Stay Here",
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33"

                        }).then((result) => {

                            if (result.isConfirmed) {

                                window.open(
                                    "{{ route('attendance.index', $company->id) }}",
                                    '_blank'
                                );
                            }
                        });

                        return false;
                    }

                    // attendance exists → go to BOM page
                    window.location.href = $(el).attr('href');
                },

                error: function () {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong!'
                    });
                }
            });

            return false;
        } </script>
@endpush