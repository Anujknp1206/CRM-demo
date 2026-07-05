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
                    @can('add supllier')
                        <button class="btn btn-default btn-sm float-right" onclick="openCreateSupplier()">
                            <i class="fa fa-plus"></i> Add Supplier
                        </button>
                    @endcan
                    <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="form-group mb-2">
                    <label for="supplierSearch" class="fw-bold">
                        Search Items
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fa fa-search"></i>
                        </span>
                        <input type="text" id="supplierSearch" class="form-control"
                            placeholder="Name / Email / Mobile / Tin No. for search...">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="example1">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>State</th>
                            <th>City</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $s)
                            <tr id="supplier-row-{{ $s->id }}">
                                <td>{{ $s->name }}</td>
                                <td>{{ $s->mobile }}</td>
                                <td>{{ $s->email }}</td>
                                <td>{{ optional($s->state)->name }}</td>
                                <td>{{ optional($s->city)->name }}</td>
                                <td>
                                    @can('edit supplier')
                                        <a href="javascript:void(0)" class="edit-supplier" data-id="{{ $s->id }}"
                                            title="Edit Supplier">
                                            <i class="fa fa-edit text-green"></i>
                                        </a>
                                    @endcan
                                    @can('delete supplier')
                                        <button class="delete-supplier" data-id="{{ $s->id }}" data-name="{{ $s->name }}"
                                            title="Delete Supplier" style="border:none;background:none">
                                            <i class="fa fa-trash text-red"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr id="no-supplier-row">
                                <td colspan="6" class="text-center">😢 No suppliers found</td>
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
        let originalSupplierRows = '';

        $(document).ready(function () {
            originalSupplierRows = $('#example1 tbody').html();
        });
    </script>

    <script>
        function openCreateSupplier() {
            $('#supplierForm')[0].reset();
            $('#supplier_id').val('');
            let indiaId = 101;   // <-- replace with real ID
            $('#sup_country').val(indiaId).trigger('change');
            $('#supplierModalTitle').text('Add Supplier');
            $('#supplierModal').modal('show');
            initSelect2('#supplierModal', '80%');
        }

        $(document).on('click', '.edit-supplier', function () {

            let id = $(this).data('id');

            $.get("{{ route('suppliers.show', [$company, 'ID']) }}".replace('ID', id), function (s) {

                $('#supplier_id').val(s.id);
                $('#sup_name').val(s.name);
                $('#sup_address').val(s.address);
                $('#sup_mobile').val(s.mobile);
                $('#sup_email').val(s.email);
                $('#sup_tin').val(s.tin_no);

                // 🔥 Inject country option manually
                $('#sup_country').html(`
                <option value="${s.country_id}" selected>${s.country.name}</option>
                `);

                // 🔥 Load states
                $.get("{{ route('getStates') }}", { country_id: s.country_id }, function (states) {

                    let stateOptions = '';
                    states.forEach(st => {
                        let selected = st.id == s.state_id ? 'selected' : '';
                        stateOptions += `<option value="${st.id}" ${selected}>${st.name}</option>`;
                    });

                    $('#sup_state').html(stateOptions);

                    // 🔥 Load cities
                    $.get("{{ route('getCities') }}", { state_id: s.state_id }, function (cities) {

                        let cityOptions = '';
                        cities.forEach(ct => {
                            let selected = ct.id == s.city_id ? 'selected' : '';
                            cityOptions += `<option value="${ct.id}" ${selected}>${ct.name}</option>`;
                        });

                        $('#sup_city').html(cityOptions);

                        // Re-init Select2 AFTER data exists
                        initSelect2('#supplierModal', '80%');
                    });
                });

                $('#supplierModalTitle').text('Edit Supplier');
                $('#supplierModal').modal('show');
            });
        });


        /* SAVE SUPPLIER */
        $('#supplierForm').submit(function (e) {
            e.preventDefault();

            let id = $('#supplier_id').val();

            let url = id
                ? "{{ route('suppliers.update', [$company, 'ID']) }}".replace('ID', id)
                : "{{ route('suppliers.store', $company) }}";

            let method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function (res) {

                    let s = res.supplier;

                    // 🔥 remove "no supplier" row if exists
                    $('#no-supplier-row').remove();

                    if (id) {
                        // ===== UPDATE EXISTING ROW =====
                        let row = $('#supplier-row-' + s.id);

                        row.find('td:eq(0)').text(s.name);
                        row.find('td:eq(1)').text(s.mobile);
                        row.find('td:eq(2)').text(s.email);
                        row.find('td:eq(3)').text(s.state?.name ?? '');
                        row.find('td:eq(4)').text(s.city?.name ?? '');

                    } else {
                        // ===== CREATE NEW ROW =====
                        $('#example1 tbody').append(`
                                                                                                        <tr id="supplier-row-${s.id}">
                                                                                                            <td>${s.name}</td>
                                                                                                            <td>${s.mobile}</td>
                                                                                                            <td>${s.email ?? ''}</td>
                                                                                                            <td>${s.state?.name ?? ''}</td>
                                                                                                            <td>${s.city?.name ?? ''}</td>
                                                                                                            <td>
                                                                                                                <a href="javascript:void(0)" class="edit-supplier" data-id="${s.id}">
                                                                                                                    <i class="fa fa-edit text-green"></i>
                                                                                                                </a>
                                                                                                                <button class="delete-supplier"
                                                                                                                        data-id="${s.id}"
                                                                                                                        data-name="${s.name}"
                                                                                                                        style="border:none;background:none">
                                                                                                                    <i class="fa fa-trash text-red"></i>
                                                                                                                </button>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    `);
                    }

                    $('#supplierModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Saved',
                        timer: 1200,
                        showConfirmButton: false
                    });
                }
            });
        });


        /* DELETE */
        /* DELETE SUPPLIER */
        $(document).on('click', '.delete-supplier', function () {

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
                    url: "{{ route('suppliers.destroy', [$company, 'ID']) }}".replace('ID', id),
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },

                    success: function () {

                        $('#supplier-row-' + id).remove();

                        // 🔥 if no rows left → show empty message
                        if ($('#example1 tbody tr').length === 0) {
                            $('#example1 tbody').html(`
                                                                                                            <tr id="no-supplier-row">
                                                                                                                <td colspan="6" class="text-center">
                                                                                                                    😢 No suppliers found
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
        function openCountryModal() {
            $('#supplierModal').modal('hide');

            setTimeout(() => {
                $('#addCountryForm')[0].reset();
                $('#addCountryModal').modal('show');
            }, 300);
        }


        function openStateModal() {
            let countryId = $('#sup_country').val();
            if (!countryId) {
                Swal.fire('Select Country first');
                return;
            }

            $('#supplierModal').modal('hide');

            setTimeout(() => {
                $('#state_country').val(countryId).trigger('change');
                $('#addStateForm')[0].reset();
                $('#addStateModal').modal('show');
                initSelect2('#addStateModal', '100%');
            }, 300);
        }


        function openCityModal() {
            let countryId = $('#sup_country').val();
            let stateId = $('#sup_state').val();

            if (!countryId || !stateId) {
                Swal.fire('Select Country & State first');
                return;
            }

            $('#supplierModal').modal('hide');

            setTimeout(() => {
                $('#city_country').val(countryId).trigger('change');
                $('#city_state').val(stateId).trigger('change');
                $('#addCityForm')[0].reset();
                $('#addCityModal').modal('show');
                initSelect2('#addCityModal', '100%');
            }, 300);
        }

        $('#addCountryForm').submit(function (e) {
            e.preventDefault();

            $.post("{{ route('countries.store') }}", $(this).serialize(), function (c) {

                // Supplier modal
                $('#sup_country')
                    .append(`<option value="${c.id}">${c.name}</option>`)
                    .val(c.id)
                    .trigger('change');

                // State modal
                $('#state_country')
                    .append(`<option value="${c.id}">${c.name}</option>`)
                    .val(c.id)
                    .trigger('change');

                // City modal
                $('#city_country')
                    .append(`<option value="${c.id}">${c.name}</option>`)
                    .val(c.id)
                    .trigger('change');

                $('#addCountryModal').modal('hide');

                setTimeout(() => {
                    $('#supplierModal').modal('show');
                    initSelect2('#supplierModal', '80%');
                }, 300);

                Swal.fire({
                    icon: 'success',
                    title: 'Country added',
                    timer: 1200,
                    showConfirmButton: false
                });
            });
        });


        $('#addStateForm').submit(function (e) {
            e.preventDefault();

            $.post("{{ route('states.store') }}", $(this).serialize(), function (s) {

                // Supplier modal
                $('#sup_state')
                    .append(`<option value="${s.id}">${s.name}</option>`)
                    .val(s.id)
                    .trigger('change');

                // City modal
                $('#city_state')
                    .append(`<option value="${s.id}">${s.name}</option>`)
                    .val(s.id)
                    .trigger('change');

                $('#addStateModal').modal('hide');

                setTimeout(() => {
                    $('#supplierModal').modal('show');
                    initSelect2('#supplierModal', '80%');
                }, 300);

                Swal.fire({
                    icon: 'success',
                    title: 'State added',
                    timer: 1200,
                    showConfirmButton: false
                });
            });
        });

        $('#addCityForm').submit(function (e) {
            e.preventDefault();

            $.post("{{ route('cities.store') }}", $(this).serialize(), function (c) {

                $('#sup_city')
                    .append(`<option value="${c.id}">${c.name}</option>`)
                    .val(c.id)
                    .trigger('change');

                $('#addCityModal').modal('hide');

                setTimeout(() => {
                    $('#supplierModal').modal('show');
                    initSelect2('#supplierModal', '80%');
                }, 300);

                Swal.fire({
                    icon: 'success',
                    title: 'City added',
                    timer: 1200,
                    showConfirmButton: false
                });
            });
        });
        $('#sup_country').on('change', function () {
            let countryId = $(this).val();
            $('#sup_state').html('<option value="">Loading...</option>');
            $('#sup_city').html('<option value="">Select City</option>');

            $.get("{{ route('getStates') }}", { country_id: countryId }, function (states) {
                let options = '<option value="">Select State</option>';
                states.forEach(s => options += `<option value="${s.id}">${s.name}</option>`);
                $('#sup_state').html(options);
            });
        });

        $('#sup_state').on('change', function () {
            let stateId = $(this).val();
            $('#sup_city').html('<option value="">Loading...</option>');

            $.get("{{ route('getCities') }}", { state_id: stateId }, function (cities) {
                let options = '<option value="">Select City</option>';
                cities.forEach(c => options += `<option value="${c.id}">${c.name}</option>`);
                $('#sup_city').html(options);
            });
        });
        function initSelect2(parent = document, width = '100%') {
            $(parent).find('.select2').each(function () {

                // Destroy old instance safely
                if ($(this).data('select2')) {
                    $(this).select2('destroy');
                }

                $(this).select2({
                    width: width,
                    dropdownParent: $(parent)
                });
            });
        }


        $(document).ready(function () {
            initSelect2(document, '100%');
            bindLocationChangeEvents();
        });
        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        }); 
    </script>
    <script>
        function loadStates(countryId, selectedStateId = null) {
            return $.get("{{ route('getStates') }}", { country_id: countryId }, function (states) {

                let options = '<option value="">Select State</option>';
                states.forEach(s => {
                    options += `<option value="${s.id}">${s.name}</option>`;
                });

                $('#sup_state').html(options);

                if (selectedStateId) {
                    $('#sup_state').val(selectedStateId).trigger('change');
                }
            });
        }

        function loadCities(stateId, selectedCityId = null) {
            return $.get("{{ route('getCities') }}", { state_id: stateId }, function (cities) {

                let options = '<option value="">Select City</option>';
                cities.forEach(c => {
                    options += `<option value="${c.id}">${c.name}</option>`;
                });

                $('#sup_city').html(options);

                if (selectedCityId) {
                    $('#sup_city').val(selectedCityId).trigger('change');
                }
            });
        }

    </script>
    <script>
        $('#supplierSearch').on('keyup', function () {

            let q = $(this).val().trim();

            // ✅ Restore original rows (NO reload)
            if (q.length === 0) {
                $('#example1 tbody').html(originalSupplierRows);
                return;
            }

            $.get(
                "{{ route('suppliers.search', $company) }}",
                { q: q },
                function (suppliers) {

                    let html = '';

                    if (suppliers.length === 0) {
                        html = `
                                                                                        <tr id="no-supplier-row">
                                                                                            <td colspan="6" class="text-center">
                                                                                                😢 No suppliers found
                                                                                            </td>
                                                                                        </tr>`;
                    } else {
                        suppliers.forEach(s => {
                            html += `
                                                                                            <tr id="supplier-row-${s.id}">
                                                                                                <td>${s.name}</td>
                                                                                                <td>${s.mobile}</td>
                                                                                                <td>${s.email ?? ''}</td>
                                                                                                <td>${s.state?.name ?? ''}</td>
                                                                                                <td>${s.city?.name ?? ''}</td>
                                                                                                <td>
                                                                                                    <a href="javascript:void(0)"
                                                                                                       class="edit-supplier"
                                                                                                       data-id="${s.id}">
                                                                                                        <i class="fa fa-edit text-green"></i>
                                                                                                    </a>
                                                                                                    <button class="delete-supplier"
                                                                                                            data-id="${s.id}"
                                                                                                            data-name="${s.name}"
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
    <script>
        function bindLocationChangeEvents() {

            $('#sup_country').on('change', function () {
                let countryId = $(this).val();

                $('#sup_state').html('<option value="">Loading...</option>');
                $('#sup_city').html('<option value="">Select City</option>');

                $.get("{{ route('getStates') }}", { country_id: countryId }, function (states) {
                    let options = '<option value="">Select State</option>';
                    states.forEach(s => options += `<option value="${s.id}">${s.name}</option>`);
                    $('#sup_state').html(options);
                });
            });

            $('#sup_state').on('change', function () {
                let stateId = $(this).val();

                $('#sup_city').html('<option value="">Loading...</option>');

                $.get("{{ route('getCities') }}", { state_id: stateId }, function (cities) {
                    let options = '<option value="">Select City</option>';
                    cities.forEach(c => options += `<option value="${c.id}">${c.name}</option>`);
                    $('#sup_city').html(options);
                });
            });
        }

    </script>

@endpush