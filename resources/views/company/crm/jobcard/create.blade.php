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
                            <a href="{{ route('company.dashboard', $company->id) }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('jobcard.index', $company->id) }}">Planning List</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $label }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card card-teal">

                        <div class="card-header d-flex justify-content-between">
                            <h3 class="card-title">{{$label}}</h3>
                            <div class="d-flex align-items-center ml-auto" style="gap: 8px;"></div>
                            <a href="{{ url()->previous() }}" class="btn btn-success btn-sm">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">

                        <form method="POST" action="{{ route('jobcard.store', $company->id) }}">
                            @csrf

                            {{-- ================= ORDER SELECT ================= --}}
                            <h4 class="text-primary"><b>1. Select Order</b></h4>

                            <div class="row">

                                <div class="col">
                                    <label>Order *</label>
                                    <select name="order_id" id="order_id" class="form-control select2" required></select>
                                </div>
                                <div class="col">
                                    <label>PO Number</label>
                                    <input type="text" name="po_number" value="{{ $poNumber }}" class="form-control"
                                        readonly>
                                </div>
                                {{-- ✅ Department --}}
                                <div class="col">
                                    <label>Department *</label>
                                    <select name="department_id" class="form-control select2" required>
                                        <option value="">-- Select Department --</option>
                                        @foreach($departments as $d)
                                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- ✅ Incharge (Employee) --}}
                                <div class="col">
                                    <label>Planning Incharge *</label>
                                    <select name="planning_incharge_id" class="form-control select2" required>
                                        <option value="">-- Select --</option>
                                        @foreach($employees as $e)
                                            <option value="{{ $e->id }}">
                                                {{ $e->first_name }} {{ $e->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            {{-- ✅ Team (MULTI SELECT) --}}
                            <div class="row mt-3">
                                <div class="col">
                                    <label>Priority</label>
                                    <select name="priority" class="form-control">
                                        <option value="low">Low</option>
                                        <option value="normal" selected>Normal</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>

                                <div class="col">
                                    <label>Shift</label>
                                    <select name="shift" class="form-control">
                                        <option value="day">Day</option>
                                        <option value="night">Night</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label>Checked By</label>
                                    <select name="checked_by" class="form-control select2">
                                        <option value="">-- Select Checker --</option>
                                        @foreach($employees as $e)
                                            <option value="{{ $e->id }}">
                                                {{ $e->first_name }} {{ $e->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label>Delivery Date</label>
                                    <div class="input-group">
                                        <input type="text" name="delivery_date" id="delivery_date"
                                            class="form-control datepicker" placeholder="DD/MM/YYYY"
                                            value="{{ old('delivery_date') }}" autocomplete="off">

                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            {{-- ================= PLANNING DETAILS ================= --}}
                            <h4 class="text-primary"><b>2. Planning Details</b></h4>

                            <div class="row">


                                <div class="col">
                                    <label>Remark</label>
                                    <textarea name="remark" class="form-control summernote"></textarea>
                                </div>
                            </div>

                            <hr>

                            {{-- ================= ITEMS (AUTO LOAD) ================= --}}
                            <h4 class="text-primary"><b>3. Order Items</b></h4>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="planning-items-table">
                                    <thead>
                                        <tr>
                                            <th style="width:3%">#</th>
                                            <th style="width:10%">Item</th>
                                            <th style="width:20%">Description</th>
                                            <th style="width:18%">Specification</th>
                                            <th style="width:6%">Qty</th>
                                            <th style="width:12%">Employee</th>
                                            <th style="width:10%">Status</th>
                                            <th style="width:14%">Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody id="planning-items"></tbody>
                                </table>
                            </div>
                            <div class="col">
                                <label>Terms</label>
                                <textarea name="term" class="form-control summernote"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success mt-3">
                                <i class="fa fa-save"></i> Create Job Card
                            </button>

                        </form>
                    </div>
                </div>

            </div>
        </div>
        </div>
    </section>

@endsection
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        #planning-items-table {
            table-layout: fixed;
            width: 100%;
        }

        #planning-items-table th,
        #planning-items-table td {
            word-wrap: break-word;
            vertical-align: middle;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let employees = @json($employees);
    </script>
    <script>
        let selectedOrder = "{{ $selectedOrder ?? '' }}";
        let deliveryPicker = flatpickr("#delivery_date", {
            dateFormat: "d/m/Y",
            allowInput: true,
            minDate: "today"
        });
        $(document).ready(function () {

            // ✅ Normal Select2 (department + employee)
            $('.select2').not('#order_id').select2({
                width: '100%',
                placeholder: "Select option"
            });

            // ✅ AJAX Select2 (order only)
            $('#order_id').select2({
                width: '100%',
                placeholder: "Search order...",
                ajax: {
                    url: "{{ route('ajax.orders.search', $company->id) }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ search: params.term }),
                    processResults: function (data) {
                        return {
                            results: data.map(function (o) {
                                return {
                                    id: o.id,
                                    text: o.order_number + " - " + o.customer_name +
                                        (o.mobile ? " (" + o.mobile + ")" : "")
                                };
                            })
                        };
                    }
                }
            });
            // ✅ Autofill order if coming from button
            if (selectedOrder) {
                $.get("{{ route('ajax.orders.search', $company->id) }}", { search: '' }, function (data) {

                    let order = data.find(o => o.id == selectedOrder);

                    if (order) {
                        let option = new Option(
                            order.order_number + " - " + order.customer_name,
                            order.id,
                            true,
                            true
                        );

                        $('#order_id').append(option).trigger('change');
                    }
                });
            }
        });
        // 🔥 Load items when order selected
        $('#order_id').on('change', function () {

            let orderId = $(this).val();
            $.get("{{ route('ajax.generate.po', $company->id) }}", { order_id: orderId }, function (res) {
                $('input[name="po_number"]').val(res.po_number);
            });
            $.get("{{ route('ajax.get.order.details', $company->id) }}", { id: orderId }, function (res) {
                // ✅ Set max date from order
                if (res.delivery_date) {
                    deliveryPicker.set('maxDate', res.delivery_date);
                }
                let html = '';

                res.items.forEach((item, i) => {

                    html += `
                                            <tr>
                                                <td>${i + 1}</td>

                                                <td>
                                                    ${item.machine?.name || item.component?.name || '-'}
                                                    <input type="hidden" name="order_item_id[]" value="${item.id}">
                                                </td>

                                                <!-- DESCRIPTION -->
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <input type="text" name="description[]" class="form-control desc-input"
                                                            value="${item.description || ''}" readonly>

                                                        <button type="button" class="btn btn-sm btn-info edit-field"
                                                            data-type="description">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                    </div>
                                                </td>

                                                <!-- SPEC -->
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <input type="text" name="specs[]" class="form-control spec-input"
                                                            value="${item.specs || ''}" readonly>

                                                        <button type="button" class="btn btn-sm btn-success edit-field"
                                                            data-type="spec">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                    </div>
                                                </td>

                                                <!-- QTY -->
                                                <td>
                                                    <div class="input-group">
                                                       <input type="number" name="qty[]" value="${item.quantity}" class="form-control" readonly>
                                                    </div>
                                                </td>

                                                <!-- EMPLOYEE -->
                                                <td>
                                                    <select name="item_employee_id[]" class="form-control select2">
                                                        <option value="">-- Select --</option>
                                                        ${employees.map(e => `
                                                            <option value="${e.id}">${e.first_name} ${e.last_name}</option>
                                                        `).join('')}
                                                    </select>
                                                </td>

                                                <!-- STATUS -->
                                                <td>
                                                    <select name="status[]" class="form-control" disabled>
                                                        <option value="pending">Pending</option>
                                                        <option value="working">Working</option>
                                                        <option value="done">Done</option>
                                                        <option value="hold">Hold</option>
                                                    </select>
                                                </td>

                                                <!-- REMARK -->
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <input type="text" name="item_remarks[]" class="form-control remark-input">

                                                        <button type="button" class="btn btn-sm btn-warning edit-field"
                                                            data-type="remark">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            `;
                });
                $('#planning-items').html(html);
            });
        });
    </script>
    <script>
        let activeField = null;

        $(document).on('click', '.edit-field', function () {

            const row = $(this).closest('tr');
            const type = $(this).data('type');

            if (type === 'description') {
                activeField = row.find('.desc-input');
                $('.modal-title').text('Edit Description');
            } else if (type === 'spec') {
                activeField = row.find('.spec-input');
                $('.modal-title').text('Edit Specification');
            } else if (type === 'remark') {
                activeField = row.find('.remark-input');
                $('.modal-title').text('Edit Remark');
            }

            $('#specModal').modal('show');

            setTimeout(() => {

                $('#modalSpec').summernote({
                    height: 200,
                    focus: true,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline']],
                        ['para', ['ul', 'ol']],
                        ['view', ['codeview']]
                    ]
                });

                $('#modalSpec').summernote('code', activeField.val());

            }, 200);
        }); $('#saveSpec').on('click', function () {

            if (activeField) {

                const html = $('#modalSpec').summernote('code');

                const clean = html
                    .replace(/<[^>]*>/g, '')
                    .replace(/\s+/g, ' ')
                    .trim();

                activeField.val(clean);
            }

            $('#specModal').modal('hide');
        }); $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
    </script>
@endpush