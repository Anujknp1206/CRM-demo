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
                            <a href="{{ route('company.dashboard', ['company' => $company->id]) }}">Dashboard</a>
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
                    @can('add categories')
                        <button class="btn btn-default btn-sm" onclick="openCreateCategory()">
                            <i class="fa fa-plus"></i> Add Category
                        </button>
                    @endcan
                    <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category Name</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody id="categoryTable">
                        @forelse($categories as $key => $category)
                            <tr id="category-row-{{ $category->id }}">
                                <td>{{ $key + 1 }}</td>
                                <td class="category-name">{{ $category->name }}</td>
                                <td>
                                    @can('edit categories')
                                        <a href="javascript:void(0)" class="edit-category" data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}" title="Edit Category">
                                            <i class="fa fa-edit text-green"></i>
                                        </a>
                                    @endcan

                                    @can('delete categories')
                                        <button class="delete-category" data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}" style="border:none;background:none"
                                            title="Delete Category">
                                            <i class="fa fa-trash text-red"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr id="no-category-row">
                                <td colspan="3" class="text-center"> 😢 No category found</td>
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
     <script>
        let saveUrl = "{{ route('categories.store', $company->id) }}";

        function openCreateCategory() {
            $('#categoryModalTitle').text('Add Category');
            $('#categoryForm')[0].reset();
            $('#category_id').val('');
            saveUrl = "{{ route('categories.store', $company->id) }}";
            $('#categoryModal').modal('show');
        }

        $(document).on('click', '.edit-category', function () {
            $('#categoryModalTitle').text('Edit Category');
            $('#category_id').val($(this).data('id'));
            $('#category_name').val($(this).data('name'));
            saveUrl = "{{ route('categories.update', [$company->id, 'ID']) }}"
                .replace('ID', $(this).data('id'));
            $('#categoryModal').modal('show');
        });

        $('#categoryForm').submit(function (e) {
            e.preventDefault();

            let id = $('#category_id').val();
            let method = id ? 'PUT' : 'POST';

            $.ajax({
                url: saveUrl,
                type: method,
                data: {
                    _token: "{{ csrf_token() }}",
                    name: $('#category_name').val()
                },

                success: function (res) {
                    let category = res.category;

                    if (id) {
                        $('#category-row-' + id)
                            .find('.category-name')
                            .text(category.name);

                        $('#category-row-' + id)
                            .find('.edit-category')
                            .data('name', category.name);
                    } else {
                        $('#no-category-row').remove();
                        let count = $('#categoryTable tr').length + 1;

                        $('#categoryTable').append(`
                                                        <tr id="category-row-${category.id}">
                                                            <td>${count}</td>
                                                            <td class="category-name">${category.name}</td>
                                                            <td>
                                                                <a href="javascript:void(0)"
                                                                   class="edit-category"
                                                                   data-id="${category.id}"
                                                                   data-name="${category.name}">
                                                                    <i class="fa fa-edit text-green"></i>
                                                                </a>
                                                                <button class="delete-category"
                                                                    data-id="${category.id}"
                                                                    data-name="${category.name}"
                                                                    style="border:none;background:none">
                                                                    <i class="fa fa-trash text-red"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    `);
                    }

                    $('#categoryModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Category saved successfully',
                        timer: 1500,
                        showConfirmButton: false
                    });
                },

                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let message = Object.values(errors)[0][0];

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: message
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong'
                        });
                    }
                }
            });
        });

        $(document).on('click', '.delete-category', function () {
            let id = $(this).data('id');
            let name = $(this).data('name');

            Swal.fire({
                title: 'Delete?',
                text: `Delete ${name}?`,
                icon: 'warning',
                showCancelButton: true
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('categories.destroy', [$company->id, 'ID']) }}".replace('ID', id),
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function () {

                            $('#category-row-' + id).fadeOut(300, function () {
                                $(this).remove();

                                // ✅ CHECK AFTER REMOVAL
                                if ($('#categoryTable tr[id^="category-row"]').length === 0) {
                                    $('#categoryTable').html(`
                                        <tr id="no-category-row">
                                            <td colspan="3" class="text-center"> 😢 No category found</td>
                                        </tr>
                                    `);
                                }
                            });

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: 'Category removed',
                                timer: 1200,
                                showConfirmButton: false
                            });
                        }

                    });
                }
            });
        });
    </script>
@endpush