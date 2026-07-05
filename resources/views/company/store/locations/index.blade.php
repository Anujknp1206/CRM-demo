@extends('company.layouts.master')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('company.dashboard', $company->id) }}">Dashboard</a>
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
                <h3 class="card-title">{{$label}}</h3>
                <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                    @can('add location')
                        <button class="btn btn-default btn-sm float-sm-right" onclick="openCreateLocation()">
                            <i class="fa fa-plus"></i> Add Location
                        </button>
                    @endcan
                    <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <!-- 🔍 SEARCH -->
            <div class="card-body">
                <div class="form-group">
                    <label class="fw-bold">Search Locations</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                        <input type="text" id="locationSearch" class="form-control"
                            placeholder="Search by Location or Parent">
                    </div>
                </div>
            </div>

            <!-- 📋 TABLE -->
            <div class="card-body">
                <table id="example1" class="table table-bordered" id="locationsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Location Name</th>
                            <th>Parent Location</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($locations as $key => $location)
                            <tr id="location-row-{{ $location->id }}" data-parent-id="{{ $location->parent_id }}">
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $location->name }}</td>
                                <td>{{ optional($location->parent)->name ?? '-' }}</td>
                                <td>
                                    @can('edit location')
                                        <a href="javascript:void(0)" class="edit-location" data-id="{{ $location->id }}"
                                            title="Edit Location">
                                            <i class="fa fa-edit text-green"></i>
                                        </a>
                                    @endcan
                                    @can('delete location')
                                        <button class="delete-location" data-id="{{ $location->id }}"
                                            data-name="{{ $location->name }}" style="border:none;background:none"
                                            title="Delete Location">
                                            <i class="fa fa-trash text-red"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr id="no-location-row">
                                <td colspan="4" class="text-center">😢 No locations found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <div class="modal fade" id="locationModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
                    <h5 class="modal-title text-white" id="locationModalTitle">
                        Add Location
                    </h5>
                    <button class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <form id="locationForm" autocomplete="off">
                    @csrf
                    <input type="hidden" id="location_id">

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Location Name *</label>
                            <input type="text" id="location_name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Parent Location</label>
                            <select id="parent_location" class="form-control select2">
                                <option value="">None (Top Level)</option>
                                @foreach($parentLocations as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success">
                            <i class="fa fa-save"></i> Save
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
@push('style')
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
     <script>
        let saveLocationUrl = "{{ route('locations.store', $company->id) }}";

        /* =========================
           OPEN CREATE
        ========================= */
        function openCreateLocation() {
            $('#locationForm')[0].reset();
            $('#location_id').val('');
            $('#locationModalTitle').text('Add Location');
            saveLocationUrl = "{{ route('locations.store', $company->id) }}";
            $('#locationModal').modal('show');
        }

        /* =========================
           OPEN EDIT
        ========================= */
        $(document).on('click', '.edit-location', function () {
            let id = $(this).data('id');

            $.get("{{ route('locations.show', [$company->id, 'ID']) }}".replace('ID', id), function (res) {
                $('#location_id').val(res.id);
                $('#location_name').val(res.name);
                $('#locationModal').modal('show');

                setTimeout(() => {
                    $('#parent_location')
                        .val(res.parent_id)
                        .trigger('change.select2');
                }, 200);


                $('#locationModalTitle').text('Edit Location');
                saveLocationUrl = "{{ route('locations.update', [$company->id, 'ID']) }}".replace('ID', id);
                $('#locationModal').modal('show');
            });
        });

        /* =========================
           SAVE (CREATE / UPDATE)
        ========================= */
        $('#locationForm').submit(function (e) {
            e.preventDefault();

            let id = $('#location_id').val();
            let method = id ? 'PUT' : 'POST';

            $.ajax({
                url: saveLocationUrl,
                type: method,
                data: {
                    _token: "{{ csrf_token() }}",
                    name: $('#location_name').val(),
                    parent_id: $('#parent_location').val()
                },
                success: function (res) {
                    let l = res.location;

                    // ✅ ADD / UPDATE parent select
                    upsertParentLocationOption(l);

                    if (id) {
                        $('#location-row-' + l.id).attr('data-parent-id', l.parent_id ?? '').html(`
                                                    <td></td>
                                                    <td>${l.name}</td>
                                                    <td>${l.parent ? l.parent.name : '-'}</td>
                                                    <td>
                                                        <a href="javascript:void(0)" class="edit-location" data-id="${l.id}">
                                                            <i class="fa fa-edit text-green"></i>
                                                        </a>
                                                        <button class="delete-location"
                                                            data-id="${l.id}"
                                                            data-name="${l.name}"
                                                            style="border:none;background:none">
                                                            <i class="fa fa-trash text-red"></i>
                                                        </button>
                                                    </td>
                                                `);
                    } else {
                        $('#no-location-row').remove();

                        $('#locationsTable tbody').prepend(`
                                                 <tr id="location-row-${l.id}" data-parent-id="${l.parent_id ?? ''}">
                                                        <td></td>
                                                        <td>${l.name}</td>
                                                        <td>${l.parent ? l.parent.name : '-'}</td>
                                                        <td>
                                                            <a href="javascript:void(0)" class="edit-location" data-id="${l.id}">
                                                                <i class="fa fa-edit text-green"></i>
                                                            </a>
                                                            <button class="delete-location"
                                                                data-id="${l.id}"
                                                                data-name="${l.name}"
                                                                style="border:none;background:none">
                                                                <i class="fa fa-trash text-red"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                `);
                    }

                    refreshLocationIndex();
                    $('#locationModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Saved',
                        timer: 1200,
                        showConfirmButton: false
                    });
                }

            });
        });

        /* =========================
           DELETE
        ========================= */
        $(document).on('click', '.delete-location', function () {
            let id = $(this).data('id');
            let name = $(this).data('name');

            // count children (direct + indirect)
            let childCount = $(`#locationsTable tbody tr[data-parent-id="${id}"]`).length;

            let warningText = childCount
                ? `Deleting "${name}" will also delete all its sub-locations. This action cannot be undone.`
                : `Delete "${name}"? This action cannot be undone.`;

            Swal.fire({
                title: 'Are you sure?',
                text: warningText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('locations.destroy', [$company->id, 'ID']) }}".replace('ID', id),
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },

                        success: function () {

                            // 🔁 delete children recursively (frontend)
                            removeLocationRecursively(id);

                            // 🧹 remove parent row
                            $('#location-row-' + id).fadeOut(200, function () {
                                $(this).remove();
                                refreshLocationIndex();
                                checkEmptyLocations();
                            });

                            // 🧹 remove from parent dropdown
                            $('#parent_location')
                                .find(`option[value="${id}"]`)
                                .remove()
                                .trigger('change.select2');

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: `"${name}" and its sub-locations were deleted successfully.`,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },

                        error: function () {
                            Swal.fire(
                                'Error',
                                'Unable to delete the location. Please try again.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        $('#locationModal').on('shown.bs.modal', function () {
            $('#parent_location').select2({
                dropdownParent: $('#locationModal'),
                width: '100%'
            });
        });

        function refreshLocationIndex() {
            $('#locationsTable tbody tr').each(function (i) {
                $(this).find('td:first').text(i + 1);
            });
        } function checkEmptyLocations() {
            if ($('#locationsTable tbody tr[id^="location-row"]').length === 0) {
                $('#locationsTable tbody').html(`
                                                            <tr id="no-location-row">
                                                                <td colspan="4" class="text-center">😢 No locations found</td>
                                                            </tr>
                                                        `);
            }
        }
        function upsertParentLocationOption(location) {
            let select = $('#parent_location');

            // remove if already exists (avoid duplicates)
            select.find(`option[value="${location.id}"]`).remove();

            // append new option
            let newOption = new Option(location.name, location.id, false, false);
            select.append(newOption).trigger('change.select2');
        } function removeLocationRecursively(parentId) {
            let children = $(`#locationsTable tbody tr[data-parent-id="${parentId}"]`);

            children.each(function () {
                let childId = $(this).attr('id').replace('location-row-', '');

                // recursive call
                removeLocationRecursively(childId);

                // remove row
                $(this).remove();

                // remove from select
                $('#parent_location')
                    .find(`option[value="${childId}"]`)
                    .remove();
            });
        }



    </script>


@endpush