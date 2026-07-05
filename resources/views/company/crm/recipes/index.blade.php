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
                                @can('create recipes')
                                    <a href="{{ route('recipes.create', ['company' => $company->id]) }}">
                                        <button class="btn btn-default btn-sm">
                                            <i class="fa fa-plus"></i> Add Recipe
                                        </button>
                                    </a>
                                @endcan
                                <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="row g-3 align-items-end">
                                <!-- Combined Search -->
                                <div class="col-md-8">

                                    <label>
                                        Search Recipe / Machine / Component
                                    </label>

                                    <select id="recipe_search" class="form-control" style="width:100%">

                                    </select>

                                </div>

                                <!-- Buttons -->
                                <div class="col-md-4 d-flex gap-2 mt-4">

                                    <button id="filter" class="btn btn-success w-50">

                                        <i class="fa fa-filter"></i>

                                        Search

                                    </button>
                                    <button id="reset" class="btn btn-secondary w-50">
                                        <i class="fa fa-undo"></i>
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="loader" style="display:none;
                                        text-align:center;
                                        padding:20px;">
                                <i class="fa fa-spinner fa-spin" style="font-size:28px;
                                        color:#17a2b8;"></i>
                                <p>Loading data...</p>
                            </div>
                            <div class="table-responsive">
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>

                                                <th>S.N.</th>

                                                <th>Recipe Name</th>

                                                <th>Type</th>

                                                <th>Machine/Component</th>

                                                <th>Total Parts</th>
                                                <th>Total Items</th>

                                                <!-- <th>Default</th> -->

                                                <th>Action</th>

                                            </tr>
                                        </thead>


                                        <tbody id="reciperows">

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection

@push('styles')
    <style>
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
    <!-- Daterange picker -->
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

        function loadRecipes() {

            let params = {};

            let search =
                $('#recipe_search').val() || "";

            /* Send only filled filters */
            if (search !== "")
                params.search = search;

            /* Loader */
            $("#loader").show();

            $("#example1").hide();



            $.ajax({

                url:
                    "{{ route('recipes.ajax', ['company' => $company->id]) }}",

                type: "GET",

                data: params,


                success: function (response) {

                    try {

                        if (
                            $.fn.DataTable.isDataTable(
                                '#example1'
                            )
                        ) {

                            let dt =
                                $('#example1').DataTable();

                            dt.clear();

                            dt.destroy();

                        }
                        console.log(
                            $('#recipe_search').val()
                        );

                        $('#reciperows').html(
                            response
                        );


                        if (
                            $("#reciperows tr").length
                        ) {

                            $('#example1').DataTable({
                                responsive: true,
                                autoWidth: false,
                                lengthChange: false,
                                paging: false,
                                searching: true,
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

                    }
                    catch (e) {

                        console.error(e);

                    }

                },


                error: function (xhr) {

                    console.error(
                        xhr.responseText
                    );

                },


                complete: function () {

                    $("#loader").hide();

                    $("#example1").show();

                }
            });

        }



        /* initial load */
        loadRecipes();





        /*
        Merged Recipe Search
        Searches recipe + machine + component
        */
        $('#recipe_search').select2({

            placeholder:
                'Search Recipe / Machine / Component',

            minimumInputLength: 1,

            width: '100%',

            ajax: {

                url:
                    "{{ route('recipes.search', $company) }}",

                dataType: 'json',

                delay: 300,

                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };

                },

                cache: true

            }

        });



        /* Filter */
        $('#filter').click(
            function () {
                loadRecipes();

            }
        );




        /* Reset */
        $('#reset').click(
            function () {

                $('#recipe_search')
                    .val(null)
                    .trigger('change');
                loadRecipes();

            }
        );

    </script>
    <script>
        $(document).on('click', '.delete-confirm', function (e) {
            e.preventDefault();
            const $el = $(this);
            const itemName = $el.data('name') || 'this item';

            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete ${itemName}. This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
                focusCancel: true,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const $form = $el.closest('form');
                    if ($form.length) {
                        $form.trigger('submit');
                        return;
                    }
                    const href = $el.attr('href');
                    if (href) window.location.href = href;
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Cancelled',
                        text: 'Your item is safe.',
                        icon: 'info',
                        timer: 1400,
                        showConfirmButton: false
                    });
                }
            });
        });
    </script>
    <script> $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
    </script>
    <script>

        $(document).on(
            'click',
            '.deleteRecipe',
            function () {

                let id =
                    $(this).data('id');



                Swal.fire({

                    title:
                        'Delete Recipe?',

                    text:
                        'This cannot be undone.',

                    icon: 'warning',

                    showCancelButton: true,

                    confirmButtonText:
                        'Yes Delete',

                    cancelButtonText:
                        'Cancel'

                })

                    .then(
                        (result) => {

                            if (
                                result.isConfirmed
                            ) {

                                $.ajax({

                                    url:
                                        "{{ url('company/' . $company->id . '/recipes') }}/"
                                        + id,

                                    type: 'DELETE',

                                    data: {
                                        _token:
                                            '{{ csrf_token() }}'
                                    },

                                    success: function (res) {

                                        Swal.fire(
                                            'Deleted!',
                                            'Recipe removed.',
                                            'success'
                                        );


                                        /* reload table */
                                        loadRecipes();

                                    },

                                    error: function () {

                                        Swal.fire(
                                            'Error',
                                            'Delete failed',
                                            'error'
                                        );

                                    }

                                });

                            }

                        });

            });

    </script>
@endpush