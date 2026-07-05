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
                <h3 class="card-title">{{$label}}</h3>
                <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                    @can('add project')
                        <button class="btn btn-default btn-sm float-right" onclick="openCreateProject()">
                            <i class="fa fa-plus"></i> Add Project
                        </button>
                    @endcan
                    <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group mb-2">
                    <label for="projectSearch" class="fw-bold">
                        Search Items
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fa fa-search"></i>
                        </span>
                        <input type="text" id="projectSearch" class="form-control"
                            placeholder="Enter Code / Name to search...">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="example1">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $p)
                            <tr id="project-row-{{ $p->id }}">
                                <td>{{ $p->name }}</td>
                                <td>{{ $p->code }}</td>
                                <td>{{ $p->start_date ? \Carbon\Carbon::parse($p->start_date)->format('d/m/Y') : '' }}</td>
                                <td>{{ $p->end_date ? \Carbon\Carbon::parse($p->end_date)->format('d/m/Y') : '' }}</td>
                                <td>
                                    @can('edit project')
                                        <a href="javascript:void(0)" class="edit-project" data-id="{{ $p->id }}"
                                            title="Edit Project">
                                            <i class="fa fa-edit text-green"></i>
                                        </a>

                                    @endcan
                                    @can('delete project')
                                        <button class="delete-project" data-id="{{ $p->id }}" data-name="{{ $p->name }}"
                                            style="border:none;background:none" title="Delete Project">
                                            <i class="fa fa-trash text-red"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr id="no-project-row">
                                <td colspan="5" class="text-center">😢 No projects found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
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
    <script src="{{url('/')}}/admin/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script>
        $(document).ready(function () {

            if ($.fn.DataTable.isDataTable('#example1')) {
                $('#example1').DataTable().destroy();
            }

            $('#example1').DataTable({
                responsive: true,
                autoWidth: false,

                paging: true,
                pageLength: 10,
                lengthChange: true,
                lengthMenu: [10, 25, 50, 100, -1],

                searching: true,
                info: true,
                ordering: true,

                dom: '<"d-flex justify-content-between align-items-center"Bf>rt<"d-flex justify-content-between mt-2"ip>',

                buttons: [
                    {
                        extend: 'colvis',
                        text: 'Column visibility'
                    }
                ]
            });

        });

        <script>
            let projectStartPicker, projectEndPicker;
            function formatDateDMY(date) {
                if (!date) return '';
            const d = new Date(date);
            return String(d.getDate()).padStart(2, '0') + '/' +
            String(d.getMonth() + 1).padStart(2, '0') + '/' +
            d.getFullYear();
            }

            function initProjectDatePickers() {

                const today = new Date();

            // force text
            document.querySelector('#project_start').type = 'text';
            document.querySelector('#project_end').type = 'text';

            projectStartPicker = flatpickr("#project_start", {
                dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            allowInput: true,
            minDate: today,          // 🔥 today
            onChange: function (selectedDates) {
                        if (selectedDates.length) {
                projectEndPicker.set('minDate', selectedDates[0]);
                        } else {
                projectEndPicker.set('minDate', today);
                        }
                    }
                });

            projectEndPicker = flatpickr("#project_end", {
                dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            allowInput: true,
            minDate: today            // 🔥 today
                });

            // calendar icon click
            document.querySelector('#project_start')
            .closest('.input-group')
            .querySelector('.input-group-text')
                    .addEventListener('click', () => projectStartPicker.open());

            document.querySelector('#project_end')
            .closest('.input-group')
            .querySelector('.input-group-text')
                    .addEventListener('click', () => projectEndPicker.open());
            }


            $(document).ready(function () {
                initProjectDatePickers();
            });

            function openCreateProject() {
                $('#projectForm')[0].reset();
            $('#project_id').val('');
            $('#projectModalTitle').text('Add Project');

            projectStartPicker.clear();
            projectEndPicker.clear();

            $('#projectModal').modal('show');
            }
            $(document).on('click', '.edit-project', function () {

                let id = $(this).data('id');

            $.get(
            "{{ route('projects.show', [$company, 'ID']) }}".replace('ID', id),
            function (p) {

                $('#project_id').val(p.id);
            $('#project_name').val(p.name);
            $('#project_code').val(p.code);
            $('#project_desc').val(p.description);

            projectStartPicker.setDate(p.start_date);
            projectEndPicker.setDate(p.end_date);

            $('#projectModalTitle').text('Edit Project');
            $('#projectModal').modal('show');
                    }
            );
            });


            /* SAVE */
            $('#projectForm').submit(function (e) {
                e.preventDefault();

            let id = $('#project_id').val();

            let url = id
            ? "{{ route('projects.update', [$company, 'ID']) }}".replace('ID', id)
            : "{{ route('projects.store', $company) }}";

            let method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
            type: method,
            data: $(this).serialize(),
            success: function (res) {

                let p = res.project;

            // remove empty row if exists
            $('#no-project-row').remove();

            if (id) {
                // UPDATE
                let row = $('#project-row-' + p.id);
            row.find('td:eq(0)').text(p.name);
            row.find('td:eq(1)').text(p.code);
            row.find('td:eq(2)').text(p.start_date);
            row.find('td:eq(3)').text(p.end_date);
                        } else {
                // CREATE
                $('#example1 tbody').append(`
                                                                                        <tr id="project-row-${p.id}">
                                                                                            <td>${p.name}</td>
                                                                                            <td>${p.code}</td>
                                                                                           <td>${formatDateDMY(p.start_date)}</td>
                                                                                            <td>${formatDateDMY(p.end_date)}</td>

                                                                                            <td>
                                                                                                <a href="javascript:void(0)" class="edit-project" data-id="${p.id}">
                                                                                                    <i class="fa fa-edit text-green"></i>
                                                                                                </a>
                                                                                                <button class="delete-project"
                                                                                                        data-id="${p.id}"
                                                                                                        data-name="${p.name}"
                                                                                                        style="border:none;background:none">
                                                                                                    <i class="fa fa-trash text-red"></i>
                                                                                                </button>
                                                                                            </td>
                                                                                        </tr>
                                                                                    `);
                        }

            $('#projectModal').modal('hide');

            Swal.fire({
                icon: 'success',
            title: 'Saved',
            timer: 1200,
            showConfirmButton: false
                        });
                    }
                });
            });


            $(document).on('click', '.delete-project', function () {

                let id = $(this).data('id');
            let name = $(this).data('name');

            Swal.fire({
                title: 'Delete?',
            text: `Delete ${name}?`,
            icon: 'warning',
            showCancelButton: true
                }).then(r => {

                    if (!r.isConfirmed) return;

            $.ajax({
                url: "{{ route('projects.destroy', [$company, 'ID']) }}".replace('ID', id),
            type: 'DELETE',
            data: {_token: "{{ csrf_token() }}" },

            success: function () {

                $('#project-row-' + id).remove();

            // show empty row if no projects left
            if ($('#example1 tbody tr').length === 0) {
                $('#example1 tbody').html(`
                                                                                        <tr id="no-project-row">
                                                                                            <td colspan="5" class="text-center">
                                                                                                😢 No projects found
                                                                                            </td>
                                                                                        </tr>
                                                                                    `);
                            }

            Swal.fire({
                icon: 'success',
            title: 'Deleted',
            timer: 1000,
            showConfirmButton: false
                            });
                        }
                    });
                });
            });
    </script>
    <script>
            let originalProjectRows = '';

            $(document).ready(function () {
                originalProjectRows = $('#example1 tbody').html();
            });
    </script>

    <script>
            $('#projectSearch').on('keyup', function () {

                let q = $(this).val().trim();

            // ✅ Restore original rows (NO reload)
            if (q.length === 0) {
                $('#example1 tbody').html(originalProjectRows);
            return;
                }

            $.get(
            "{{ route('projects.search', $company) }}",
            {q: q },
            function (projects) {

                let html = '';

            if (projects.length === 0) {
                html = `
                                                        <tr id="no-project-row">
                                                            <td colspan="5" class="text-center">
                                                                😢 No projects found
                                                            </td>
                                                        </tr>`;
                        } else {
                projects.forEach(p => {
                    html += `
                                                            <tr id="project-row-${p.id}">
                                                                <td>${p.name}</td>
                                                                <td>${p.code}</td>
                                                                <td>${p.start_date ?? ''}</td>
                                                                <td>${p.end_date ?? ''}</td>
                                                                <td>
                                                                    <a href="javascript:void(0)" class="edit-project" data-id="${p.id}">
                                                                        <i class="fa fa-edit text-green"></i>
                                                                    </a>
                                                                    <button class="delete-project"
                                                                            data-id="${p.id}"
                                                                            data-name="${p.name}"
                                                                            style="border:none;background:none">
                                                                        <i class="fa fa-trash text-red"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        `;
                });
                        }

            $('#example1 tbody').html(html);
                    }
            );
            });
    </script>


@endpush