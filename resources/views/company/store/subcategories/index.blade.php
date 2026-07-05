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
                   @can('add subcategories')
                                <button class="btn btn-default btn-sm" onclick="openCreateSubcategory()">
                                    <i class="fa fa-plus"></i> Add Sub-category
                                </button>
                            @endcan
                    <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>S.N.</th>
                                    <th>Category</th>
                                    <th>Sub Category</th>
                                    <th width="120">Operations</th>
                                </tr>
                            </thead>
                            <tbody id="subcategoryTable">
                                @forelse($subcategories as $key => $sub)
                                    <tr id="subcategory-row-{{ $sub->id }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td class="subcategory-category">{{ $sub->category->name }}</td>
                                        <td class="subcategory-name">{{ $sub->name }}</td>
                                        <td>
                                             @can('edit subcategories')
                                            <a href="javascript:void(0)" class="edit-subcategory" data-id="{{ $sub->id }}"
                                                data-name="{{ $sub->name }}" data-category="{{ $sub->category_id }}"
                                                title="Edit Sub-category">
                                                <i class="fa fa-edit text-green"></i>
                                            </a>
                                            @endcan
                                            @can('delete subcategories')
                                            <button class="delete-subcategory" data-id="{{ $sub->id }}"
                                                data-name="{{ $sub->name }}" style="border:none;background:none"
                                                title="Delete Sub-category">
                                                <i class="fa fa-trash text-red"></i>
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="no-subcategory-row">
                                        <td colspan="4" class="text-center"> 😢 No sub-category found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
      
        let returnToSubcategory = false;
        let subcategorySaveUrl = "{{ route('subcategories.store', $company->id) }}";
        let subcategoryMethod = "POST";

        /* ===============================
           OPEN CATEGORY FROM SUBCATEGORY
        ================================ */
        $('#openCategoryFromSubcategory').on('click', function () {
            returnToSubcategory = true;

            $('#subcategoryModal').modal('hide');

            setTimeout(() => {
                $('#categoryModal').modal('show');
            }, 400);
        });
        function initSelect2(modalId) {
            $(modalId).find('.select2').each(function () {

                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }

                $(this).select2({
                    placeholder: 'Search category...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $(modalId),
                    minimumResultsForSearch: 0
                });
            });
        }

        $('#subcategoryModal').on('shown.bs.modal', function () {
            initSelect2('#subcategoryModal');
        });

        /* ===============================
           AFTER CATEGORY SAVED
        ================================ */
        function afterCategorySaved(category) {

            let option = new Option(category.name, category.id, true, true);
            $('#subcategory_category').append(option).trigger('change');

            $('#categoryModal').modal('hide');

            if (returnToSubcategory) {
                setTimeout(() => {
                    $('#subcategoryModal').modal('show');
                    returnToSubcategory = false;
                }, 400);
            }
        }

        /* ===============================
           CATEGORY FORM SUBMIT (AJAX)
        ================================ */
        $('#categoryForm').on('submit', function (e) {
            e.preventDefault();

            let name = $('#category_name').val().trim();
            let id = $('#category_id').val();

            let url = id
                ? "{{ route('categories.update', [$company->id, 'ID']) }}".replace('ID', id)
                : "{{ route('categories.store', $company->id) }}";

            $.ajax({
                url: url,
                type: id ? 'PUT' : 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    name: name
                },
                success: function (res) {
                    afterCategorySaved(res.category);

                    Swal.fire({
                        icon: 'success',
                        title: 'Category saved',
                        timer: 1200,
                        showConfirmButton: false
                    });
                },
                error: function (xhr) {
                    let msg =
                        xhr.responseJSON?.errors?.name?.[0] ||
                        xhr.responseJSON?.message ||
                        'Category already exists';

                    Swal.fire('Error', msg, 'error');
                }
            });
        });

        /* ===============================
           SUBCATEGORY FORM SUBMIT
        ================================ */
        function openCreateSubcategory() {
            $('#subcategoryForm')[0].reset();
            $('#subcategory_id').val('');
            $('#subcategoryModalTitle').text('Add Sub Category');

            subcategorySaveUrl = "{{ route('subcategories.store', $company->id) }}";
            subcategoryMethod = "POST";

            $('#subcategoryModal').modal('show');
        }

        $(document).on('click', '.edit-subcategory', function () {

            let id = $(this).data('id');
            let name = $(this).data('name');
            let categoryId = $(this).data('category');

            $('#subcategoryModalTitle').text('Edit Sub Category');
            $('#subcategory_id').val(id);
            $('#subcategory_name').val(name);
            $('#subcategory_category').val(categoryId).trigger('change');

            subcategorySaveUrl =
                "{{ route('subcategories.update', [$company->id, 'ID']) }}"
                    .replace('ID', id);

            subcategoryMethod = "PUT";

            $('#subcategoryModal').modal('show');
        });


        $('#subcategoryForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: subcategorySaveUrl,
                type: subcategoryMethod,
                data: {
                    _token: "{{ csrf_token() }}",
                    category_id: $('#subcategory_category').val(),
                    name: $('#subcategory_name').val()
                },
                success: function (res) {

                    let sub = res.subcategory;

                    if (subcategoryMethod === 'PUT') {

                        // UPDATE ROW
                        let row = $('#subcategory-row-' + sub.id);

                        row.find('.subcategory-category').text(sub.category.name);
                        row.find('.subcategory-name').text(sub.name);

                        row.find('.edit-subcategory')
                            .data('name', sub.name)
                            .data('category', sub.category_id);

                        row.find('.delete-subcategory')
                            .data('name', sub.name);


                    } else {

                        // CREATE ROW
                        $('#no-subcategory-row').remove();

                        // CREATE ROW
                        let rowCount = $('#subcategoryTable tr').length + 1;

                        $('#subcategoryTable').prepend(`
                                                                                                <tr id="subcategory-row-${sub.id}">
                                                                                                    <td>${rowCount}</td>
                                                                                                    <td class="subcategory-category">${sub.category.name}</td>
                                                                                                    <td class="subcategory-name">${sub.name}</td>
                                                                                                    <td>
                                                                                                        <a href="javascript:void(0)"
                                                                                                           class="edit-subcategory"
                                                                                                           data-id="${sub.id}"
                                                                                                           data-name="${sub.name}"
                                                                                                           data-category="${sub.category_id}">
                                                                                                            <i class="fa fa-edit text-green"></i>
                                                                                                        </a>

                                                                                                        <button class="delete-subcategory"
                                                                                                                data-id="${sub.id}"
                                                                                                                data-name="${sub.name}"
                                                                                                                style="border:none;background:none">
                                                                                                            <i class="fa fa-trash text-red"></i>
                                                                                                        </button>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            `);
                    }

                    $('#subcategoryModal').modal('hide');
                    $('#subcategoryForm')[0].reset();

                    Swal.fire({
                        icon: 'success',
                        title: 'Saved successfully',
                        timer: 1200,
                        showConfirmButton: false
                    });
                },
                error: function (xhr) {
                    let msg =
                        xhr.responseJSON?.errors?.name?.[0] ||
                        xhr.responseJSON?.message ||
                        'Validation failed';

                    Swal.fire('Error', msg, 'error');
                }
            });
        });

        $(document).on('click', '.delete-subcategory', function () {

            let id = $(this).data('id');
            let name = $(this).data('name');

            Swal.fire({
                title: 'Delete?',
                text: `Delete ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {

                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('subcategories.destroy', [$company->id, 'ID']) }}".replace('ID', id),
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function () {

                            $('#subcategory-row-' + id).fadeOut(300, function () {
                                $(this).remove();

                                // ✅ CHECK AFTER REMOVAL
                                if ($('#subcategoryTable tr[id^="subcategory-row"]').length === 0) {
                                    $('#subcategoryTable').html(`
                                <tr id="no-subcategory-row">
                                    <td colspan="4" class="text-center">😢 No sub-category found</td>
                                </tr>
                            `);
                                }
                            });

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                timer: 1200,
                                showConfirmButton: false
                            });
                        }
                        ,
                        error: function () {
                            Swal.fire('Error', 'Delete failed', 'error');
                        }
                    });
                }
            });
        });
        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
    </script>
@endpush