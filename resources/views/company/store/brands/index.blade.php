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
                    @can('add brand')
                        <button class="btn btn-default btn-sm" onclick="openCreateBrand()">
                            <i class="fa fa-plus"></i> Add Brand
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
                            <th width="50">#</th>
                            <th>Brand Name</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>

                    <tbody id="brandTable">
                        @forelse($brands as $key => $brand)
                            <tr id="brand-row-{{ $brand->id }}">
                                <td>{{ $key + 1 }}</td>
                                <td class="brand-name">{{ $brand->name }}</td>
                                <td>
                                    @can('edit brand')
                                        <a href="javascript:void(0)" class="edit-brand" data-id="{{ $brand->id }}"
                                            data-name="{{ $brand->name }}" title="Edit Brand">
                                            <i class="fa fa-edit text-green"></i>
                                        </a>
                                    @endcan
                                    @can('delete brand')
                                        <button class="delete-brand" data-id="{{ $brand->id }}" data-name="{{ $brand->name }}"
                                            style="border:none;background:none" title="Delete Brand">
                                            <i class="fa fa-trash text-red"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr id="no-brand-row">
                                <td colspan="3" class="text-center">😢 No brand found</td>
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
        let saveBrandUrl = "{{ route('brands.store', $company->id) }}";

        /* =========================
           OPEN CREATE MODAL
        ========================= */
        function openCreateBrand() {
            $('#brandForm')[0].reset();
            $('#brand_id').val('');
            $('#brandModalTitle').text('Add Brand');

            saveBrandUrl = "{{ route('brands.store', $company->id) }}";
            $('#brandModal').modal('show');
        }

        /* =========================
           OPEN EDIT MODAL
        ========================= */
        $(document).on('click', '.edit-brand', function () {
            let id = $(this).data('id');
            let name = $(this).data('name');

            $('#brand_id').val(id);
            $('#brand_name').val(name);
            $('#brandModalTitle').text('Edit Brand');

            saveBrandUrl = "{{ route('brands.update', [$company->id, 'ID']) }}".replace('ID', id);
            $('#brandModal').modal('show');
        });

        /* =========================
           SAVE BRAND (CREATE / UPDATE)
        ========================= */
        $('#brandForm').submit(function (e) {
            e.preventDefault();

            let id = $('#brand_id').val();
            let method = id ? 'PUT' : 'POST';

            $.ajax({
                url: saveBrandUrl,
                type: method,
                data: {
                    _token: "{{ csrf_token() }}",
                    name: $('#brand_name').val()
                },
                success: function (res) {
                    let brand = res.brand;

                    if (id) {
                        $('#brand-row-' + id).find('.brand-name').text(brand.name);
                        $('#brand-row-' + id).find('.edit-brand').data('name', brand.name);
                    } else {
                        $('#no-brand-row').remove();

                        let count = $('#brandTable tr').length + 1;

                        $('#brandTable').append(`
                                        <tr id="brand-row-${brand.id}">
                                            <td>${count}</td>
                                            <td class="brand-name">${brand.name}</td>
                                            <td>
                                                <a href="javascript:void(0)"
                                                   class="edit-brand"
                                                   data-id="${brand.id}"
                                                   data-name="${brand.name}">
                                                    <i class="fa fa-edit text-green"></i>
                                                </a>
                                                <button class="delete-brand"
                                                    data-id="${brand.id}"
                                                    data-name="${brand.name}"
                                                    style="border:none;background:none">
                                                    <i class="fa fa-trash text-red"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    `);
                    }

                    $('#brandModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Saved successfully',
                        timer: 1200,
                        showConfirmButton: false
                    });
                },
                error: function (xhr) {
                    Swal.fire(
                        'Error',
                        xhr.responseJSON?.message || 'Validation failed',
                        'error'
                    );
                }
            });
        });

        /* =========================
           DELETE BRAND
        ========================= */
        $(document).on('click', '.delete-brand', function () {
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
                        url: "{{ route('brands.destroy', [$company->id, 'ID']) }}".replace('ID', id),
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function () {
                            $('#brand-row-' + id).remove();

                            if (!$('#brandTable tr[id^="brand-row"]').length) {
                                $('#brandTable').html(`
                                                <tr id="no-brand-row">
                                                    <td colspan="3" class="text-center">😢 No brand found</td>
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
                }
            });
        });
    </script>
@endpush