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
                        <li class="breadcrumb-item active"><a
                                href="{{ route('leads.index', ['company' => $company->id]) }}"> Lead List</a></li>
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

                                <a href="{{ route('leads.index', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('leads.store', ['company' => $company->id]) }}" method="POST"
                                autocomplete="off">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Mobile *</label>
                                            <div id="mobile-wrapper">
                                                <!-- Primary mobile -->
                                                <div class="input-group mb-2">
                                                    <input type="text" name="mobile" class="form-control"
                                                        placeholder="Enter customer mobile" maxlength="15"
                                                        pattern="[0-9]{9,15}" required>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-success" id="add-mobile">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <small class="text-muted">
                                                Add multiple mobile numbers if customer has more than one.
                                            </small>
                                        </div>


                                        <div class="form-group">
                                            <label>Customer Name *</label>
                                            <input type="text" name="customerName" class="form-control"
                                                placeholder="Enter customer name" value="{{ old('customerName') }}"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label>GST Number</label>
                                            <input type="text" name="gst" class="form-control"
                                                placeholder="Enter GST number" value="{{ old('gst') }}" maxlength="15">
                                        </div>

                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="text" name="email" placeholder="Enter customer email"
                                                value="{{ old('email') }}" class="form-control">
                                        </div>

                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea name="address" placeholder="Enter customer address"
                                                class="form-control">{{ old('address') }}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Reference</label>
                                            <input type="text" name="reference" placeholder="Enter reference"
                                                value="{{ old('reference') }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Code</label>
                                                    <input type="text" id="country_code" class="form-control" readonly
                                                        value="{{ $lead->country->phonecode ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <label>Country *</label>
                                                <div class="input-group">
                                                    <select name="country" id="country" class="form-control select2"
                                                        required>
                                                        <option value="">Select Country</option>
                                                        @foreach($countries as $country)
                                                            <option value="{{ $country->id }}"
                                                                data-phonecode="{{ $country->phonecode }}">
                                                                {{ $country->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="input-group-append">
                                                        <button class="btn  btn-outline-success" type="button"
                                                            data-toggle="modal" data-target="#addCountryModal">
                                                            + Add
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-10">
                                            <label>State *</label>
                                            <div class="input-group">
                                                <select name="state" id="state" class="form-control select2" required>
                                                    <option value="">Select State</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-success" type="button"
                                                        data-toggle="modal" data-target="#addStateModal">
                                                        + Add
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-10 mb-2">
                                            <label>City *</label>
                                            <div class="input-group">
                                                <select name="city" id="city" class="form-control select2" required>
                                                    <option value="">Select City</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-success" type="button"
                                                        data-toggle="modal" data-target="#addCityModal">
                                                        + Add
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Purpose</label>
                                            <input type="text" name="purpose" placeholder="Enter purpose"
                                                value="{{ old('purpose') }}" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Remark</label>
                                            <input type="text" name="remark" placeholder="Enter remark"
                                                value="{{ old('remark') }}" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Lead Date *</label>

                                            <div class="input-group">
                                                <input type="text" name="lead_date" id="lead_date" class="form-control"
                                                    placeholder="Select date"
                                                    value="{{ old('lead_date', now()->format('Y-m-d H:i')) }}" required>

                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="calendar-icon"
                                                        style="cursor:pointer;">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="form-group">
                                                                                                                                                                            <label>Message</label>
                                                                                                                                                                            <input type="text" name="message" placeholder="Enter message"
                                                                                                                                                                                value="{{ old('message') }}" class="form-control">
                                                                                                                                                                        </div> -->

                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">Save Lead</button>

                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let customerLookupInProgress = false;
        let lastFetchedMobile = null;
      let fp = flatpickr("#lead_date", {
    enableTime: true,

    dateFormat: "Y-m-d H:i",

    altInput: true,
    altFormat: "d/m/Y h:i K",

    // Real current date & time
    defaultDate: new Date(),

    maxDate: new Date(),

    allowInput: true,

    time_24hr: false
});

document.getElementById('calendar-icon').addEventListener('click', function () {
    fp.open();
});
        $(document).ready(function () {

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
                width: 'resolve',
                placeholder: 'Select Country'
            });
            // Similarly, any other select in modals:
            $('#city_country').select2({ dropdownParent: $('#addCityModal'), width: 'resolve' });
            $('#city_state').select2({ dropdownParent: $('#addCityModal'), width: 'resolve' });
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
        $(document).ready(function () {

            // Initialize Select2 for main form
            $('#country, #state, #city').select2({
                width: 'resolve',
                placeholder: 'Select option',
            });
        });
    </script>
    <script>
        $('input[name="mobile"]').on('input', function () {

            let mobile = this.value.replace(/\D/g, '').slice(0, 10);
            this.value = mobile;

            // Only when exactly 10 digits
            if (mobile.length !== 10) return;

            // 🔒 Prevent duplicate calls
            if (customerLookupInProgress) return;
            if (lastFetchedMobile === mobile) return;

            customerLookupInProgress = true;
            lastFetchedMobile = mobile;

            $.ajax({
                url: "{{ route('leads.checkCustomerByMobile', ['company' => $company->id]) }}",
                type: "GET",
                data: { mobile },

                success: function (res) {

                    if (!res.exists) {
                        customerLookupInProgress = false;
                        return;
                    }

                    const customer = res.customer;

                    // 🧠 Fill text fields
                    $('input[name="customerName"]').val(customer.name);
                    $('input[name="email"]').val(customer.email ?? '');
                    $('input[name="gst"]').val(customer.gst ?? '');
                    $('textarea[name="address"]').val(customer.address ?? '');

                    // 🌍 LOCATION (CHAIN SAFE)
                    if (customer.country_id) {

                        // COUNTRY → triggers state fetch
                        $('#country')
                            .val(customer.country_id)
                            .trigger('change');

                        // STATE → wait until states are loaded
                        const waitForState = setInterval(() => {
                            if ($('#state option').length > 1) {
                                $('#state')
                                    .val(customer.state_id)
                                    .trigger('change');
                                clearInterval(waitForState);
                            }
                        }, 100);

                        // CITY → wait until cities are loaded
                        const waitForCity = setInterval(() => {
                            if ($('#city option').length > 1) {
                                $('#city')
                                    .val(customer.city_id)
                                    .trigger('change');
                                clearInterval(waitForCity);
                            }
                        }, 150);
                    }


                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: 'Existing customer loaded',
                        showConfirmButton: false,
                        timer: 2000
                    });
                },

                complete: function () {
                    customerLookupInProgress = false;
                }
            });
        });

    </script>

    <script>
        let mobileCount = 1;
        const maxMobiles = 4; // limit (optional)

        $(document).on('click', '#add-mobile', function () {

            if (mobileCount >= maxMobiles) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Limit reached',
                    text: 'You can add maximum ' + maxMobiles + ' mobile numbers.'
                });
                return;
            }

            mobileCount++;

            $('#mobile-wrapper').append(`
                                                                                                                                    <div class="input-group mb-2 extra-mobile">
                                                                                                                                        <input type="text"
                                                                                                                                               name="extra_mobile[]"
                                                                                                                                               class="form-control"
                                                                                                                                               placeholder="Enter another mobile"
                                                                                                                                               maxlength="10"
                                                                                                                                               pattern="[0-9]{10}">

                                                                                                                                        <div class="input-group-append">
                                                                                                                                            <button type="button" class="btn btn-danger remove-mobile">
                                                                                                                                                <i class="fa fa-times"></i>
                                                                                                                                            </button>
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                `);
        });

        // Remove extra mobile
        $(document).on('click', '.remove-mobile', function () {
            $(this).closest('.extra-mobile').remove();
            mobileCount--;
        });
        $('input[name="mobile"]').on('keyup', function () {
            $('#add-mobile').prop('disabled', this.value.length !== 10);
        });
        $('#mobile-wrapper input:last').focus();
    </script>
    <script>
        $(document).on('input', '.mobile-input', function () {
            this.value = this.value
                .replace(/\D/g, '')   // remove non-digits
                .slice(0, 10);        // limit to 10 digits
        });
    </script>

@endpush