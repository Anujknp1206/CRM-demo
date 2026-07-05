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
                            <h3 class="card-title">{{ $label }}</h3>
                            <div class="d-flex align-items-center ml-auto" style="gap: 8px;"></div>
                            <a href="{{ url()->previous() }}" class="btn btn-success btn-sm">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">

                        <form method="POST" action="{{ route('jobcard.update', [$company->id, $planning->id]) }}">
                            @csrf
                            @method('PUT')

                            {{-- ================= ORDER SELECT ================= --}}
                            <h4 class="text-primary"><b>1. Select Order</b></h4>

                            <div class="row">

                                <div class="col">
                                    <label>Order *</label>
                                    <select class="form-control" disabled><option>{{ $planning->order->order_number }}</option></select>
                                    <input type="hidden" name="order_id" value="{{ $planning->order_id }}">
                                </div>
                                <div class="col">
                                    <label>PO Number</label>
                                    <input type="text" name="po_number" value="{{ $planning->po_number }}" class="form-control"
                                        readonly>
                                </div>
                                {{-- ✅ Department --}}
                                <div class="col">
                                    <label>Department *</label>
                                    <select name="department_id" class="form-control select2" required>
                                        <option value="">-- Select Department --</option>
                                        @foreach($departments as $d)
                                            <option value="{{ $d->id }}" {{ $planning->department_id == $d->id ? 'selected' : '' }}> {{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- ✅ Incharge (Employee) --}}
                                <div class="col">
                                    <label>Planning Incharge *</label>
                                    <select name="planning_incharge_id" class="form-control select2" required>
                                        <option value="">-- Select --</option>
                                        @foreach($employees as $e)
                                           <option value="{{ $e->id }}" {{ $planning->planning_incharge_id == $e->id ? 'selected' : '' }}>
    {{ $e->first_name }} {{ $e->last_name }}
</option>  @endforeach
                                    </select>
                                </div>

                            </div>

                            {{-- ✅ Team (MULTI SELECT) --}}
                            <div class="row mt-3">
                                <div class="col">
                                    <label>Priority</label>
                                    <select name="priority" class="form-control">
                                        <option value="low" {{ $planning->priority == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="normal" {{ $planning->priority == 'normal' ? 'selected' : '' }}>Normal
                                        </option>
                                        <option value="high" {{ $planning->priority == 'high' ? 'selected' : '' }}>High
                                        </option>
                                        <option value="urgent" {{ $planning->priority == 'urgent' ? 'selected' : '' }}>Urgent
                                        </option>
                                    </select>
                                </div>
                                    <div class="col">
                                    <label>Checked By</label>
                                    <select name="checked_by" class="form-control select2">
                                        <option value="">-- Select Checker --</option>
                                        @foreach($employees as $e)
                                           <option value="{{ $e->id }}" {{ $planning->checked_by == $e->id ? 'selected' : '' }}>
                                                {{ $e->first_name }} {{ $e->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label>Shift</label>
                                    <select name="shift" class="form-control">
                                        <option value="day" {{ $planning->shift == 'day' ? 'selected' : '' }}>Day</option>
                                        <option value="night" {{ $planning->shift == 'night' ? 'selected' : '' }}>Night
                                        </option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label>Delivery Date</label>
                                    <div class="input-group">
                                        <input type="text" name="delivery_date" id="delivery_date"
                                            class="form-control datepicker" placeholder="DD/MM/YYYY"
                                            value="{{ $planning->delivery_date ? \Carbon\Carbon::parse($planning->delivery_date)->format('d/m/Y') : '' }}"
                                            autocomplete="off">

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
                                    <textarea name="remark"
                                        class="form-control summernote">{{ $planning->remark }}</textarea>
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
                                    <tbody>
                                        @foreach($planning->items as $i => $item)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>

                                                <td>
                                                    {{ $item->orderItem->machine->name ?? $item->orderItem->component->name ?? '-' }}
                                                    <input type="hidden" name="item_id[]" value="{{ $item->id }}">
                                                </td>

                                                <!-- DESCRIPTION -->
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <input type="text" name="description[]" class="form-control desc-input"
                                                            value="{{ $item->description }}" readonly>

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
                                                            value="{{ $item->specs }}" readonly>

                                                        <button type="button" class="btn btn-sm btn-success edit-field"
                                                            data-type="spec">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                    </div>
                                                </td>

                                                <!-- QTY -->
                                                <td>
                                                    <input type="number" name="qty[]" value="{{ $item->qty }}"
                                                        class="form-control">
                                                </td>

                                                <!-- EMPLOYEE -->
                                                <td>
                                                    <select name="item_employee_id[]" class="form-control select2">
                                                        <option value="">-- Select --</option>
                                                        @foreach($employees as $e)
                                                            <option value="{{ $e->id }}" {{ $item->employee_id == $e->id ? 'selected' : '' }}>
                                                                {{ $e->first_name }} {{ $e->last_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>

                                                <!-- STATUS -->
                                                <td>
                                                    <select name="status[]" class="form-control">
                                                        <option value="pending" {{ $item->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="working" {{ $item->status == 'working' ? 'selected' : '' }}>Working</option>
                                                        <option value="done" {{ $item->status == 'done' ? 'selected' : '' }}>Done
                                                        </option>
                                                        <option value="hold" {{ $item->status == 'hold' ? 'selected' : '' }}>Hold
                                                        </option>
                                                    </select>
                                                </td>

                                                <!-- REMARK -->
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <input type="text" name="item_remarks[]"
                                                            class="form-control remark-input" value="{{ $item->remarks }}">

                                                        <button type="button" class="btn btn-sm btn-warning edit-field"
                                                            data-type="remark">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="col">
                                <label>Terms</label>
                                <textarea name="term" class="form-control summernote">{{ $planning->term }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success mt-3">
                                <i class="fa fa-save"></i> Update Job Card
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

        $('.datepicker').flatpickr({
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