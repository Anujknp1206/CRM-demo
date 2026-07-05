@extends('admin.layouts.master')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">List</a></li>
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
                            <div class="d-flex gap-2 ml-auto">
                                <a href="{{ route('companies.index') }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>

                        <div class="card-body">

                            <form action="{{ route('companies.store') }}" method="POST" autocomplete="off">
                                @csrf

                                <div class="row">

                                    {{-- Company Name --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Company Name:</label>
                                            <input type="text" name="company_name" class="form-control"
                                                placeholder="Enter Company Name" required>
                                        </div>
                                    </div>

                                    {{-- Country --}}
                                    <div class="col-md-4">
                                        <label>Country *</label>
                                        <div class="input-group">
                                            <select name="country" id="country" class="form-control select2" required>
                                                <option value="">Select Country</option>
                                                @foreach($countries as $country)
                                                    <option value="{{ $country->id }}"
                                                        data-phonecode="{{ $country->phonecode }}">
                                                        {{ $country->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn  btn-outline-success" type="button" data-toggle="modal"
                                                    data-target="#addCountryModal">
                                                    + Add
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- State --}}
                                    <div class="col-md-4">
                                        <label>State *</label>
                                        <div class="input-group">
                                            <select name="state" id="state" class="form-control select2" required>
                                                <option value="">Select State</option>
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-success" type="button" data-toggle="modal"
                                                    data-target="#addStateModal">
                                                    + Add
                                                </button>
                                            </div>
                                        </div>
                                    </div>


                                    {{-- City --}}

                                    <div class="col-md-4">
                                        <label>City *</label>
                                        <div class="input-group">
                                            <select name="city" id="city" class="form-control select2" required>
                                                <option value="">Select City</option>
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-success" type="button" data-toggle="modal"
                                                    data-target="#addCityModal">
                                                    + Add
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Email --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Email:</label>
                                            <input type="email" name="email" class="form-control" placeholder="Enter Email"
                                                required>
                                        </div>
                                    </div>

                                    {{-- Alternate Email --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Website:</label>
                                            <input type="text" name="website" placeholder="Enter Website"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Alternate Email:</label>
                                            <input type="email" name="alternate_email" placeholder="Enter Alternate Email"
                                                class="form-control">
                                        </div>
                                    </div>

                                    {{-- Mobile --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Mobile:</label>
                                            <input type="text" name="mobile" class="form-control" maxlength="10"
                                                pattern="[0-9]{10}" placeholder="10 digit mobile number" required>
                                        </div>
                                    </div>

                                    {{-- Alternate Mobile --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Alternate Mobile:</label>
                                            <input type="text" name="alternate_mobile" maxlength="10" pattern="[0-9]{10}"
                                                placeholder="10 digit mobile number" class="form-control">
                                        </div>
                                    </div>

                                    {{-- GSTIN --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>GSTIN:</label>
                                            <input type="text" name="gstin_no" class="form-control"
                                                placeholder="e.g. 07AAAAA0000A1Z5">
                                        </div>
                                    </div>

                                    {{-- HSN Registration No --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>HSN Registration No:</label>
                                            <input type="text" name="rex_registration_no" class="form-control"
                                                placeholder="Enter HSN Number">
                                        </div>
                                    </div>

                                    {{-- IEC Code --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>IEC Code:</label>
                                            <input type="text" name="iec_code" class="form-control"
                                                placeholder="10-digit IEC Code">
                                        </div>
                                    </div>

                                    {{-- PAN No --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>PAN No:</label>
                                            <input type="text" name="pan_no" class="form-control"
                                                placeholder="e.g. ABCDE1234F">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Address:</label>
                                            <input type="text" name="address" class="form-control"
                                                placeholder="Enter Address">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Pin Code</label>
                                            <input type="text" name="pincode" class="form-control"
                                                placeholder="Enter Pincode" maxlength="6">
                                        </div>
                                    </div>

                                    {{-- Submit Button --}}
                                    <div class="col-md-12 d-flex justify-content-end mt-3">
                                        <button type="submit" class="btn btn-success">Create Company</button>
                                    </div>

                                </div>

                            </form>

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
        $(document).ready(function () {
            $('.select2').select2({
                width: '80%'
            });
            // Update country code on country change
            $('#country').on('change', function () {
                let phonecode = $(this).find(':selected').data('phonecode');
                $('#country_code').val(phonecode);

                let country_id = $(this).val();

                // Fetch states via AJAX
                $.ajax({
                    url: "{{ route('getStates') }}",
                    type: "GET",
                    data: { country_id: country_id },
                    success: function (states) {
                        $('#state').empty().append('<option value="">Select State</option>');
                        $('#city').empty().append('<option value="">Select City</option>');
                        $.each(states, function (key, value) {
                            $('#state').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            });

            // Fetch cities on state change
            $('#state').on('change', function () {
                let state_id = $(this).val();
                $.ajax({
                    url: "{{ route('getCities') }}",
                    type: "GET",
                    data: { state_id: state_id },
                    success: function (cities) {
                        $('#city').empty().append('<option value="">Select City</option>');
                        $.each(cities, function (key, value) {
                            $('#city').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            });

        });
    </script>
    <script>
        $(document).ready(function () {

            $('#state_country').select2({
                dropdownParent: $('#addStateModal'), // IMPORTANT: binds dropdown inside modal
                width: '100%',
                placeholder: 'Select Country'
            });

            // Similarly, any other select in modals:
            $('#city_country').select2({ dropdownParent: $('#addCityModal'), width: '100%' });
            $('#city_state').select2({ dropdownParent: $('#addCityModal'), width: '100%' });
            // --- Add Country ---
            // --- Add Country ---
            $('#addCountryForm').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('countries.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function (country) {
                        // Append to main form select
                        $('#country').append('<option value="' + country.id + '" data-phonecode="' + country.phonecode + '">' + country.name + '</option>');

                        // Append to Add State modal select
                        $('#state_country').append('<option value="' + country.id + '">' + country.name + '</option>').trigger('change');

                        // Append to Add City modal select
                        $('#city_country').append('<option value="' + country.id + '">' + country.name + '</option>').trigger('change');

                        // Close modal and reset
                        $('#addCountryModal').modal('hide');
                        $('#addCountryForm')[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Country Added!',
                            text: country.name + ' has been added successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: xhr.responseJSON?.message || 'Something went wrong!',
                        });
                    }
                });
            });


            // --- Add State ---
            $('#addStateForm').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('states.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function (state) {
                        $('#state').append('<option value="' + state.id + '">' + state.name + '</option>');
                        $('#addStateModal').modal('hide');
                        $('#addStateForm')[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'State Added!',
                            text: state.name + ' has been added successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: xhr.responseJSON?.message || 'Something went wrong!',
                        });
                    }
                });
            });

            // --- Add City ---
            $('#addCityForm').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('cities.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function (city) {
                        $('#city').append('<option value="' + city.id + '">' + city.name + '</option>');
                        $('#addCityModal').modal('hide');
                        $('#addCityForm')[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'City Added!',
                            text: city.name + ' has been added successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: xhr.responseJSON?.message || 'Something went wrong!',
                        });
                    }
                });
            });

            // --- Load States in City modal based on selected country ---
            $('#city_country').change(function () {
                let country_id = $(this).val();
                $.ajax({
                    url: "{{ route('getStates') }}",
                    type: "GET",
                    data: { country_id: country_id },
                    success: function (states) {
                        $('#city_state').empty().append('<option value="">Select State</option>');
                        $.each(states, function (key, value) {
                            $('#city_state').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            });

        });
        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });

    </script>

@endpush