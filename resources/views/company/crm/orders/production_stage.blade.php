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
                        <li class="breadcrumb-item"><a
                                href="{{ route('company.dashboard', ['company' => $company->id]) }}">Dashboard</a></li>
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

                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">{{$label}}</h3>

                            <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                                @can('add production status')
                                    <button class="btn btn-default btn-sm" onclick="openCreateModal()">
                                        <i class="fa fa-plus"></i> Add Status
                                    </button>
                                @endcan
                                <a href="{{ route('boms.index', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">

                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Badge</th>
                                            <th>Progress %</th>
                                            <th>Sort Order</th>
                                            <th width="180">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse($statuses as $status)

                                            <tr>

                                                <td>{{ $status->id }}</td>

                                                <td>
                                                    <strong>{{ $status->name }}</strong>
                                                </td>

                                                <td>
                                                    <span class="badge badge-{{ $status->badge_color ?? 'secondary' }}">
                                                        {{ $status->name }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $status->default_progress ?? 0 }}%
                                                </td>
                                                <td>
                                                    {{ $status->sort_order ?? 0 }}
                                                </td>

                                                <td>
@can('edit production status')
                                                    {{-- EDIT --}}
                                                    <button class="btn btn-sm" onclick="openEditModal(
                                                                '{{ $status->id }}',
                                                                '{{ $status->name }}',
                                                                '{{ $status->badge_color }}',
                                                                '{{ $status->default_progress ?? 0 }}',
                                                                '{{ $status->sort_order ?? 0 }}'
                                                            )" title="Edit Status">

                                                        <i class="fa fa-edit text-success"></i>
                                                    </button>
@endcan
                                                    {{-- DELETE --}}
                                                    @can('delete production status')
                                                    @if(($status->stage_progress_count ?? 0) == 0)
                                                        <button type="button" class="btn btn-sm deleteBtn"
                                                            data-url="{{ route('production.status.destroy', [$company->id, $status->id]) }}"
                                                            title="Delete Status">
                                                            <i class="fa fa-trash text-danger"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-sm" disabled
                                                            title="Cannot delete. Status is used in production">
                                                            <i class="fa fa-trash text-muted"></i>
                                                        </button>
                                                    @endif
@endcan
                                                </td>

                                            </tr>

                                        @empty

                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    No statuses found
                                                </td>
                                            </tr>

                                        @endforelse

                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="statusModal" tabindex="-1" data-backdrop="static">

        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- HEADER -->
                <div class="modal-header text-white"
                    style="background: linear-gradient(to bottom, #081a2d, #0f3057, #1b4f72);">

                    <h5 id="modalTitle" class="modal-title">
                        Add Production Status
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        &times;
                    </button>

                </div>

                <!-- FORM -->
                <form method="POST" id="statusForm" action="{{ route('production.status.store', $company->id) }}">

                    @csrf
                    <div id="methodField"></div>

                    <div class="modal-body">

                        {{-- STATUS NAME --}}
                        <div class="form-group">
                            <label>Status Name *</label>

                            <input type="text" name="name" id="status_name" class="form-control"
                                placeholder="e.g. Pending, In Progress, Completed, On Hold" required>

                            <small class="text-muted">
                                Define the production state (e.g. Pending → Work not started).
                            </small>
                        </div>

                        <div class="form-group">

                            <label>Default Progress (%)</label>

                            <input type="number" name="default_progress" id="default_progress" class="form-control" min="0"
                                max="100" value="0">

                            <small class="text-muted">
                                Used for production progress calculation
                            </small>

                        </div>
                        {{-- BADGE COLOR --}}
                        <div class="form-group">
                            <label>Badge Color</label>

                            <select name="badge_color" id="badge_color" class="form-control">
                                <option value="secondary">Gray</option>
                                <option value="warning">Yellow</option>
                                <option value="info">Blue</option>
                                <option value="success">Green</option>
                                <option value="danger">Red</option>
                            </select>

                            <small class="text-muted">
                                Used for visual identification in production board.
                            </small>
                        </div>

                        {{-- SORT ORDER --}}
                        <div class="form-group">
                            <label>Sort Order</label>

                            <input type="number" name="sort_order" id="sort_order" class="form-control" value="1"
                                placeholder="Lower numbers appear first">

                            <small class="text-muted">
                                Controls display order in dropdowns and boards.
                            </small>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Save Status
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

        .btn-success,
        .btn-secondary {
            border-radius: 6px;
        }
    </style>
@endpush

@push('scripts')
    <script>

        function openCreateModal() {

            document.getElementById('modalTitle').innerHTML = 'Add Status';

            document.getElementById('statusForm').action =
                "{{ route('production.status.store', $company->id) }}";

            document.getElementById('methodField').innerHTML = '';

            document.getElementById('status_name').value = '';
            document.getElementById('default_progress').value = 0;
            document.getElementById('badge_color').value = '#000000';

            $('#statusModal').modal('show');
        }


        function openEditModal(id, name, color, progress, sortOrder) {

            document.getElementById('modalTitle').innerHTML = 'Edit Status';

            document.getElementById('statusForm').action =
                '/company/{{ $company->id }}/production-status/' + id;

            document.getElementById('methodField').innerHTML =
                '@method("PUT")';

            document.getElementById('status_name').value = name;

            document.getElementById('badge_color').value = color;

            document.getElementById('default_progress').value = progress;

            document.getElementById('sort_order').value = sortOrder;

            $('#statusModal').modal('show');
        }
    </script>

    <script>

        $(document).on('click', '.deleteBtn', function (e) {

            e.preventDefault();

            let url = $(this).data('url');

            Swal.fire({
                title: 'Delete Status?',
                text: 'This action cannot be undone',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, Delete'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },

                        success: function (response) {

                            Swal.fire(
                                'Deleted!',
                                'Status removed successfully',
                                'success'
                            );

                            setTimeout(function () {
                                location.reload();
                            }, 1000);

                        },

                        error: function (xhr) {

                            Swal.fire(
                                'Blocked',
                                'Status is linked and cannot be deleted.',
                                'error'
                            );

                        }

                    });

                }

            });

        });

    </script>
@endpush