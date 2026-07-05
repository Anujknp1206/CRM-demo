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
                    @can('add items')
                        <a href="{{ route('items.create', ['company' => $company->id]) }}">
                            <button class="btn btn-default btn-sm">
                                <i class="fa fa-plus"></i> Add Item
                            </button>
                        </a>
                    @endcan
                    <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <select id="search_item" class="form-control"></select>
                    </div>
                    <div class="col-md-3">
                        <select id="category_id" class="form-control">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="subcategory_id" class="form-control">

                            <option value="">All Subcategories</option>

                            @foreach($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}">
                                    {{ $subcategory->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>
                    <div class="col-md-3 d-flex">
                        <button type="button" id="clearFilters" class="btn btn-secondary w-100">
                            <i class="fa fa-times"></i> Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div id="loader" style="display:none; text-align:center; padding:20px;">
                    <i class="fa fa-spinner fa-spin" style="font-size:28px; color:#17a2b8;"></i>
                    <p>Loading data...</p>
                </div>
                <table id="example1" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Item Code</th>
                            <th>Name (English)</th>
                            <th>Name (Hindi)</th>
                            <th>Category</th>
                            <th>Sub-category</th>
                            <th>Base Unit</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody id="itemsTableBody"></tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
@push('style')
    <style>
        .modal {
            overflow-y: auto !important;
        }

        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        .gap-2 {
            gap: 10px;
        }

        .card-body {
            padding: 10px 5px !important;
        }

        .btn-primary,
        .btn-secondary {
            border-radius: 6px;
            height: 40px;
        }

        table td {
            white-space: normal !important;
            word-break: break-word;
            max-width: 200px;
        }
    </style>
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/summernote/summernote-bs4.min.css">
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
        $(document).on('click', '.delete-item', function () {
            let id = $(this).data('id');
            let name = $(this).data('name');

            Swal.fire({
                title: 'Delete?',
                text: `Delete ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('items.destroy', [$company->id, 'ID']) }}".replace('ID', id),
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: 'DELETE'
                        },

                        success: function () {
                            $('#item-row-' + id).remove();

                            // optional: show empty row if no items left
                            if ($('#itemsTable tbody tr').length === 0) {
                                $('#itemsTable tbody').html(`
                                                                                                                <tr id="no-item-row">
                                                                                                                    <td colspan="7" class="text-center">😢 No items left.</td>
                                                                                                                </tr>
                                                                                                            `);
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                timer: 1200,
                                showConfirmButton: false
                            });
                        },

                        error: function (xhr) {
                            Swal.fire(
                                'Error',
                                xhr.responseJSON?.message || 'Delete failed',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    </script>

    <script>

        $(document).ready(function () {

            fetchItems();

            // Category Filter
            $('#category_id').on('change', function () {
                fetchItems();
            });

            // Subcategory Filter
            $('#subcategory_id').on('change', function () {
                fetchItems();
            });

        });
        $('#search_item').on('select2:select', function () {
            fetchItems();
        }); $('#search_item').on('select2:clear', function () {
            fetchItems();
        });
        function fetchItems() {

            let params = {};

            let itemId = $('#search_item').val();

            let category = $('#category_id').val();
            let subcategory = $('#subcategory_id').val();

            // ITEM FILTER
            if (itemId) {
                params.item_id = itemId;
            }

            // CATEGORY FILTER
            if (category) {
                params.category_id = category;
            }

            // SUBCATEGORY FILTER
            if (subcategory) {
                params.subcategory_id = subcategory;
            }

            $("#loader").show();
            $("#example1").hide();

            $.ajax({

                url: "{{ route('items.data', ['company' => $company->id]) }}",

                type: "GET",

                data: params,

                success: function (response) {

                    if ($.fn.DataTable.isDataTable('#example1')) {
                        $('#example1').DataTable().destroy();
                    }

                    $('#itemsTableBody').html(response);

                    let rowCount = $('#itemsTableBody tr').length;

                    let hasNoData = $('#itemsTableBody')
                        .find('td[colspan]')
                        .length > 0;

                    if (rowCount > 0 && !hasNoData) {

                        $('#example1').DataTable({
                            responsive: true,
                            autoWidth: false,
                            lengthChange: false,
                            paging: false,
                            searching: true,
                            ordering: false,
                            info: false,

                            dom: '<"d-flex justify-content-between align-items-center"Bf>rt',

                            buttons: [
                                {
                                    extend: 'colvis',
                                    text: 'Column visibility'
                                }
                            ]
                        });
                    }

                    $("#loader").hide();
                    $("#example1").show();
                },

                error: function () {

                    $("#loader").hide();
                    $("#example1").show();
                }

            });
        } $('.select2').not('#search_item').select2({
            width: '100%',
            placeholder: 'Select an option',
            allowClear: false
        }); $('#search_item').select2({

            width: '100%',
            placeholder: 'Search by name or code',
            minimumInputLength: 1,

            ajax: {

                url: "{{ route('parts.searchItems', $company->id) }}",

                dataType: 'json',

                delay: 300,

                data: function (params) {

                    return {
                        search: params.term
                    };
                },

                processResults: function (data) {

                    return {

                        results: $.map(data, function (item) {

                            return {

                                id: item.id,
                                text: item.text,
                                item: item
                            };
                        })
                    };
                },

                cache: true
            }

        });
        $('#category_id').on('change', function () {

            let categoryId = $(this).val();

            $('#subcategory_id').html(
                '<option value="">Loading...</option>'
            );
            if (categoryId) {
                $.ajax({
                    url: "{{ route('subcategories.byCategory', ['company' => $company->id, 'category' => ':id']) }}"
                        .replace(':id', categoryId),
                    type: "GET",
                    success: function (response) {
                        let options =
                            '<option value="">Select Sub-category</option>';
                        $.each(response, function (key, subcategory) {
                            options += `<option value="${subcategory.id}">${subcategory.name}</option>`;
                        });
                        $('#subcategory_id').html(options);
                    }
                });
            } else {
                $('#subcategory_id').html(
                    '<option value="">Select Sub-category</option>'
                );
            }
        });
    </script>
    <script>
        $('#clearFilters').on('click', function () {
            $('#search').val('');
            $('#category_id').val('');
            $('#subcategory_id').html(`
                                                        <option value="">All Subcategories</option>
                                                    `);
            fetchItems();
        });
    </script>
@endpush