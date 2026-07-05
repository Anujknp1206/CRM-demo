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
                                <a href="{{ url()->previous() }}" class="btn btn-success btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <label>
                                        Search Customer / Country / State / City
                                    </label>
                                    <select id="customer_search" class="form-control">
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button id="searchBtn" class="btn btn-success btn-block">
                                        <i class="fa fa-search"></i>
                                        Search
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <button id="resetBtn" class="btn btn-secondary btn-block">
                                        <i class="fa fa-refresh"></i>
                                        Reset
                                    </button>
                                </div>
                            </div>

                        </div>
                        <div class="card-body ">

                            <div class="table-responsive">

                                <table class="table table-bordered">

                                    <thead>

                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Country</th>
                                            <th>State</th>
                                            <th>City</th>
                                        </tr>

                                    </thead>

                                    <tbody id="reportRows">

                                        @if($customers->count())

                                            @foreach($customers as $c)

                                                <tr>

                                                    <td>
                                                        {{ $c->name }}
                                                    </td>

                                                    <td>
                                                        {{ $c->email ?? '-' }}
                                                    </td>

                                                    <td>
                                                        {{ $c->country->name ?? '-' }}
                                                    </td>

                                                    <td>
                                                        {{ $c->state->name ?? '-' }}
                                                    </td>

                                                    <td>
                                                        {{ $c->city->name ?? '-' }}
                                                    </td>
                                                </tr>

                                            @endforeach

                                        @else

                                            <tr>

                                                <td colspan="7" class="text-center py-4">

                                                    <i class="fa fa-database fa-2x mb-2 d-block text-muted"></i>

                                                    No customers found for today.
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
        $(document).ready(function () {


            /*==============================
            SELECT2 SEARCH
            ==============================*/

            $('#customer_search').select2({

                placeholder: 'Search Customer, Country, State, City',

                allowClear: false,

                width: '100%',

                ajax: {

                    url: "{{ route('company.reports.customer.search', $company->id) }}",

                    dataType: 'json',

                    delay: 300,

                    data: function (params) {

                        return {
                            q: params.term
                        };

                    },

                    processResults: function (data) {

                        return {
                            results: data.results
                        };

                    }

                }

            });



            /*==============================
            COMMON TABLE RENDER
            ==============================*/

            function renderRows(rows) {

                let html = '';


                if (!rows.length) {

                    html = `
                                    <tr>
                                    <td colspan="7" class="text-center">
                                    No records found
                                    </td>
                                    </tr>
                                    `;

                    $('#reportRows').html(html);

                    return;
                }



                rows.forEach(function (r) {

                    html += `
                                    <tr>

                                    <td>${r.name ?? '-'}</td>

                                    <td>${r.email ?? '-'}</td>

                                    <td>${r.country?.name ?? '-'}</td>

                                    <td>${r.state?.name ?? '-'}</td>

                                    <td>${r.city?.name ?? '-'}</td>

                                    </tr>
                                    `;

                });

                $('#reportRows').html(html);

            }




            /*==============================
            LOAD CUSTOMERS
            ==============================*/

            function loadCustomers(selected = '') {

                $.ajax({

                    url: "{{ route('company.reports.customers', $company->id) }}",

                    type: 'GET',

                    data: {
                        selected: selected
                    },

                    success: function (rows) {

                        renderRows(rows);

                    }

                });

            }




            /*==============================
            SEARCH BUTTON
            ==============================*/

            $('#searchBtn').click(function () {

                let selected =
                    $('#customer_search').val();

                if (!selected) {

                    alert('Please select search value');

                    return;

                }

                loadCustomers(selected);

            });



            /*==============================
            SELECT2 AUTO LOAD ON SELECT
            ==============================*/

            $('#customer_search').on(
                'select2:select',
                function (e) {

                    let selected =
                        e.params.data.id;

                    loadCustomers(selected);

                }
            );




            /*==============================
            RESET BUTTON
            ==============================*/

            $('#resetBtn').click(function () {

                $('#customer_search')
                    .val(null)
                    .trigger('change');

                loadCustomers();

            });

        });

    </script>

@endpush