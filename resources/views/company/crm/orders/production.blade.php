@extends('company.layouts.master')
@section('content')
    <!-- Content Header -->
    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-3">

                <div class="col-sm-6">

                    <h1>
                        {{ $label }}
                    </h1>

                    <p class="text-muted mb-0">
                        Production tracking for Order:
                        {{ $order->order_number }}
                    </p>

                </div>


                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-right">

                        <li class="breadcrumb-item">

                            <a href="{{ route('company.dashboard', ['company' => $company->id]) }}">
                                Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ $label }}
                        </li>
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
                    @can('add production stage')
                       <button
    type="button"
    class="btn btn-primary"
    id="openStageModal">

    Add Stage

</button>
                    @endcan
                    @can('add production status')
                        <button type="button" class="btn btn-success" onclick="openStatusModal()">
                            Add Status
                        </button>
                    @endcan
                    <a href="{{ route('boms.index', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <!-- Production Table -->
            <div class="card-body">
                <div class="table-responsive">

                    <table class="table table-bordered">

                        <thead>
                            <tr>
                                <th>Machine/Component</th>

                                @foreach($stages as $stage)
                                    <th>
                                        @can('edit production stage')
                                        <button type="button" class="btn text-success btn-sm edit-stage"
                                            data-id="{{ $stage->id }}" data-name="{{ $stage->name }}"
                                            data-sort="{{ $stage->sort_order }}" {{-- 🔥 ADD THIS --}}
                                            data-order-id="{{ $stage->order_id }}"> {{-- 🔥 ALSO ADD --}}
                                            <i class="fa fa-edit text-success"></i>

                                        </button>
                                        @endcan
                                        <span class="stage-title">
                                            {{ $stage->name }}
                                        </span>
                                        @can('delete production stage')
                                        <button type="button" class="btn text-danger btn-sm delete-stage"
                                            data-url="{{ route('production.stage.destroy', [$company->id, $stage->id]) }}"
                                            title="Delete Stage"><i class="fa fa-trash text-danger"></i>
                                        </button>
                                        @endcan
                                    </th>
                                @endforeach
                                <th>Progress</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($order->items as $item)

                                @php
                                    $parts = $item->bomItems
                                        ->pluck('part')
                                        ->filter()
                                        ->unique('id')   // 🔥 THIS FIXES YOUR ISSUE
                                        ->values();
                                @endphp
@php

    /*
    -----------------------------------------
    TOTAL PART WEIGHT
    -----------------------------------------
    */
    $totalWeight = $parts->sum('weightage');

    /*
    -----------------------------------------
    ITEM PROGRESS
    -----------------------------------------
    */
    $itemProgress = 0;

    if ($totalWeight > 0) {

        foreach ($parts as $part) {

            $itemProgress += (
                ($part->progress_percent ?? 0)
                *
                ($part->weightage ?? 0)
            );
        }

        $itemProgress = round(
            $itemProgress / $totalWeight,
            2
        );
    }

@endphp
                                {{-- ORDER ITEM HEADER --}}
                                <tr class="bg-light">
                                    <td colspan="{{ count($stages) + 2 }}">
                                      <div class="d-flex justify-content-between align-items-center">

    <div>
        <strong>
            {{ $loop->iteration }}.
            {{ $item->item_name }}
        </strong>
    </div>

    <div>

        <span class="badge badge-primary p-2">

            Production Progress:
            {{ $itemProgress }}%

        </span>

    </div>

</div>
                                    </td>
                                </tr>

                                @forelse($parts as $part)

                                    <tr>
                                        <td>
                                            <span class="ml-3">
                                                {{ $loop->iteration }}. {{ $part->part_name }}
                                            </span>
                                            <br>
                                            <small class="text-muted ml-3">
                                                Weight: {{ $part->weightage }}
                                            </small>
                                        </td>

                                        {{-- 🔥 STAGE COLUMNS --}}
                                        @foreach($stages as $stage)

                                         @php
    $track = $part->stageProgress
        ->where('stage_id', $stage->id)
        ->where('order_item_id', $item->id)
        ->first();
@endphp

                                            <td class="text-center">
                                                <select class="form-control stage-status-select" data-id="{{ $track?->id }}"
                                                    data-original="{{ $track?->status_id }}">
                                                    @foreach($statuses as $status)
                                                        <option value="{{ $status->id }}" @if($track?->status_id == $status->id) selected
                                                        @endif>
                                                            {{ $status->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>

                                        @endforeach

                                        {{-- PROGRESS --}}
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $part->progress_percent }}%
                                            </span>
                                        </td>
                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="{{ count($stages) + 2 }}" class="text-muted">
                                            No Parts Found
                                        </td>
                                    </tr>

                                @endforelse

                            @endforeach

                        </tbody>
                    </table>

                </div>

            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">

                <small class="text-muted">
                    Changes will affect production tracking immediately
                </small>
                <div class="ml-auto">
                    @can('save production')
                    <button type="submit" id="saveBoardBtn" class="btn btn-success">
                        <i class="fa fa-save"></i> Save Status
                    </button>
                    @endcan
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="editStageModal" tabindex="-1" data-backdrop="static">

        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form id="editStageForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header text-white"
                        style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
                        <h5 class="modal-title">Edit Production Stage</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">

                        {{-- Stage Name --}}
                        <div class="form-group">
                            <label>Stage Name *</label>

                            <input type="text" id="edit_stage_name" name="name" class="form-control"
                                placeholder="e.g. Cutting, Assembly, Painting, Dispatch" required>

                            <small class="text-muted">
                                Update the production step name (e.g. Cutting, Welding, Finishing).
                            </small>
                        </div>


                        {{-- Sort Order --}}
                        <div class="form-group">
                            <label>Sort Order</label>

                            <input type="number" id="edit_sort_order" name="sort_order" class="form-control"
                                placeholder="e.g. 1 for first stage, 2 for next">

                            <small class="text-muted">
                                Lower numbers appear earlier in the production workflow.
                            </small>
                        </div>


                        {{-- APPLY TYPE (READ-ONLY 🔥 IMPORTANT) --}}
                        <div class="form-group">
                            <label>Stage Scope</label>

                            <input type="text" id="edit_apply_type" class="form-control" readonly>

                            <small class="text-muted">
                                This cannot be changed after creation to prevent data inconsistency.
                            </small>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Update
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <div class="modal fade" id="stageModal" tabindex="-1" data-backdrop="static">

        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form method="POST" action="{{ route('production.stage.store', $company->id) }}">
                    @csrf

                    <div class="modal-header text-white"
                        style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
                        <h5 class="modal-title">Add Production Stage</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">

                        {{-- Stage Name --}}
                        <div class="form-group">
                            <label>Stage Name *</label>

                            <input type="text" name="name" class="form-control"
                                placeholder="e.g. Cutting, Assembly, Painting, Dispatch" required>

                            <small class="text-muted">
                                Define a production step (e.g. Cutting, Welding, Finishing).
                            </small>
                        </div>


                        {{-- Sort Order --}}
                        <div class="form-group">
                            <label>Sort Order</label>

                            <input type="number" name="sort_order" class="form-control" value="1"
                                placeholder="e.g. 1 for first stage, 2 for next">

                            <small class="text-muted">
                                Lower numbers appear first in the production flow.
                            </small>
                        </div>


                        {{-- Apply Type --}}
                        <div class="form-group">
                            <label>Apply Stage To</label>

                            <select name="apply_type" class="form-control" required>
                                <option value="order">This Order Only</option>
                                <option value="global">All Orders</option>
                            </select>

                            <small class="text-muted">
                                • <strong>This Order Only:</strong> Stage applies only to this order<br>
                                • <strong>All Orders:</strong> Stage will be available in all orders
                            </small>

                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Save
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <div class="modal fade" id="statusModal" tabindex="-1" data-backdrop="static">

        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form method="POST" action="{{ route('production.status.store', $company->id) }}">
                    @csrf

                    <div class="modal-header  text-white"
                        style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
                        <h5 class="modal-title">Add Production Status</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Status Name *</label>
                            <input type="text" name="name" class="form-control"
                                placeholder="e.g. Pending, In progress, Completed, On Hold" required>
                        </div>

                        <div class="form-group">
                            <label>Badge Color</label>
                            <select name="badge_color" class="form-control">
                                <option value="secondary">Gray</option>
                                <option value="warning">Yellow</option>
                                <option value="info">Blue</option>
                                <option value="success">Green</option>
                                <option value="danger">Red</option>
                            </select>
                        </div>
                        <div class="form-group">

                            <label>Default Progress (%)</label>

                            <input type="number" name="default_progress" id="default_progress" class="form-control" min="0"
                                max="100" value="0">

                            <small class="text-muted">
                                Used for production progress calculation
                            </small>

                        </div>
                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="1">
                            <small class="text-muted">
                                Lower numbers appear first in the production flow.
                            </small>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Save
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
@push('styles')
    <style>
        .card-body {
            padding: 10px 5px !important;
        }

        .gap-2 {
            gap: 10px;
        }

        .btn-primary,
        .btn-success,
        .btn-secondary {
            border-radius: 6px;
            padding: 2px 5px;
        }
    </style>
@endpush
@push('scripts')
    <script>

        /* -----------------------------
        OPEN MODALS
        ----------------------------- */
        function openStageModal() {
            $('#stageModal').modal('show');
        }

        function openStatusModal() {
            $('#statusModal').modal('show');
        }


        /* -----------------------------
        EDIT STAGE
        ----------------------------- */
        $(document).on('click', '.edit-stage', function () {

            let id = $(this).data('id');
            let name = $(this).data('name');
            let sort = $(this).data('sort') ?? 0;
            let orderId = $(this).data('order-id'); // 🔥 IMPORTANT

            // Set values
            $('#edit_stage_name').val(name);
            $('#edit_sort_order').val(sort);

            // 🔥 Show scope (readonly field)
            $('#edit_apply_type').val(
                orderId ? 'This Order Only' : 'All Orders'
            );

            // Set form action
            $('#editStageForm').attr(
                'action',
                '/company/{{ $company->id }}/production-stage/' + id
            );

            // Open modal
            $('#editStageModal').modal('show');
        });

        /* -----------------------------
        DELETE STAGE
        ----------------------------- */
        $(document).on('click', '.delete-stage', function () {

            let url = $(this).data('url');

            Swal.fire({
                title: 'Delete Stage?',
                text: 'This cannot be undone',
                icon: 'warning',
                showCancelButton: true
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },

                        success: function () {

                            Swal.fire('Deleted', 'Stage removed', 'success');

                            setTimeout(() => location.reload(), 800);
                        },

                        error: function () {

                            Swal.fire(
                                'Blocked',
                                'Stage linked to production records',
                                'error'
                            );
                        }

                    });

                }

            });

        });


        /* -----------------------------
        SAVE BOARD (UPDATED 🔥)
        ----------------------------- */
     $(document).on('click', '#saveBoardBtn', function (e) {

    e.preventDefault();

    let btn = $(this);

    let data = [];

    /*
    -----------------------------------------
    DISABLE BUTTON
    -----------------------------------------
    */
    btn.prop('disabled', true)
        .html('<i class="fa fa-spinner fa-spin"></i> Saving...');

    /*
    -----------------------------------------
    COLLECT ONLY CHANGED ROWS
    -----------------------------------------
    */
    $('.stage-status-select').each(function () {

        let select = $(this);

        let id = select.attr('data-id');

        let status_id = select.val();

        // IMPORTANT: use attr not data()
        let original = select.attr('data-original');

        console.log({
            id: id,
            current: status_id,
            original: original
        });

        /*
        -----------------------------------------
        SKIP UNCHANGED
        -----------------------------------------
        */
        if (String(status_id) === String(original)) {
            return;
        }

        /*
        -----------------------------------------
        SKIP INVALID
        -----------------------------------------
        */
        if (!id || id === 'undefined') {

            console.warn(
                'Missing stage_progress_id'
            );

            return;
        }

        /*
        -----------------------------------------
        PUSH CHANGED DATA
        -----------------------------------------
        */
        data.push({
            id: id,
            status_id: status_id
        });

    });

    /*
    -----------------------------------------
    VALIDATION
    -----------------------------------------
    */
    if (data.length === 0) {

        btn.prop('disabled', false)
            .html('<i class="fa fa-save"></i> Save Status');

        Swal.fire({
            icon: 'warning',
            title: 'No Changes',
            text: 'Nothing to update'
        });

        return;
    }

    console.log('Sending Data:', data);

    /*
    -----------------------------------------
    AJAX SAVE
    -----------------------------------------
    */
    $.ajax({

        url: "{{ route('production.part.update', $company->id) }}",

        type: "POST",

        data: {
            _token: "{{ csrf_token() }}",
            data: data
        },

        success: function (res) {

            console.log('Response:', res);

            /*
            -----------------------------------------
            UPDATE ORIGINAL VALUES
            -----------------------------------------
            */
            $('.stage-status-select').each(function () {

                $(this).attr(
                    'data-original',
                    $(this).val()
                );

            });

            /*
            -----------------------------------------
            SUCCESS MESSAGE
            -----------------------------------------
            */
            Swal.fire({
                icon: 'success',
                title: 'Updated',
                text: res.message ??
                    'Production updated successfully'
            });

            /*
            -----------------------------------------
            OPTIONAL RELOAD
            -----------------------------------------
            */
            setTimeout(() => {
                location.reload();
            }, 600);

        },

        error: function (xhr) {

            console.error(xhr.responseText);

            let message =
                'Something went wrong';

            if (xhr.responseJSON?.message) {

                message =
                    xhr.responseJSON.message;
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });

        },

        complete: function () {

            /*
            -----------------------------------------
            ENABLE BUTTON AGAIN
            -----------------------------------------
            */
            btn.prop('disabled', false)
                .html(
                    '<i class="fa fa-save"></i> Save Status'
                );
        }

    });

});
  </script>
  <script>

    /*
    |--------------------------------------------------------------------------
    | OPEN STAGE MODAL
    |--------------------------------------------------------------------------
    */

    $('#openStageModal').on('click', function (e) {

        e.preventDefault();

        let statusCount = {{ $statuses->count() ?? 0 }};

        /*
        |--------------------------------------------------------------------------
        | NO STATUS FOUND
        |--------------------------------------------------------------------------
        */

        if (statusCount <= 0) {

            Swal.fire({

                icon: 'warning',

                title: 'Production Status Required',

                html: `
                    <div class="text-left">

                        <p>
                            Before adding production stages,
                            you must create at least one
                            production status.
                        </p>

                        <div class="alert alert-info mt-3 mb-0">

                            Example Statuses:

                            <ul class="mb-0 pl-3 mt-2">

                                <li>Pending</li>

                                <li>In Progress</li>

                                <li>Completed</li>

                                <li>On Hold</li>

                            </ul>

                        </div>

                    </div>
                `,

                confirmButtonText: 'Add Status',

                showCancelButton: true,

                cancelButtonText: 'Cancel'

            }).then((result) => {

                if (result.isConfirmed) {

                    $('#statusModal').modal('show');

                }

            });

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | OPEN STAGE MODAL
        |--------------------------------------------------------------------------
        */

        $('#stageModal').modal('show');

    });

</script>
@endpush