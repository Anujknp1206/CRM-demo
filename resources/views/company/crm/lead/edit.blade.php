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
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard',['company' => $company->id]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('leads.index',['company' => $company->id]) }}"> Lead List</a></li>
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
 
           <form action="{{ route('leads.update', ['lead'=>$lead->id,'company' => $company->id]) }}" method="POST" autocomplete="off">
    @csrf
    @method('PUT')

    <div class="row">

        <!-- LEFT SIDE -->
        <div class="col-md-6">
            <div class="form-group">
                                            <label>Mobile *</label>
          <div id="mobile-wrapper">

@foreach($lead->customer->phones as $i => $phone)
    <div class="input-group mb-2 extra-mobile">
        <input type="text"
               name="{{ $i === 0 ? 'mobile' : 'extra_mobile[]' }}"
               class="form-control"
               value="{{ $phone->phone }}"
               maxlength="15" pattern="[0-9]{9,15}">

        <div class="input-group-append">
            @if($i === 0)
                <button type="button" class="btn btn-success" id="add-mobile">
                    <i class="fa fa-plus"></i>
                </button>
            @else
                <button type="button" class="btn btn-danger remove-mobile">
                    <i class="fa fa-times"></i>
                </button>
            @endif
        </div>
    </div>
    @endforeach
</div>

</div>
 <div class="form-group">
                <label>Customer Name *</label>
                <input type="text" name="customerName" class="form-control"
                       placeholder="Enter customerName"
                       value="{{ old('customerName', $lead->customer->name) }}" required>
            </div>
<div class="form-group">
    <label>GST Number</label>
    <input type="text"
           name="gst"
           class="form-control"
           placeholder="Enter GST number"
           value="{{ old('gst', $lead->customer->gst) }}"
           maxlength="15">
</div>


            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control"
                       placeholder="Enter customer email"
                       value="{{ old('email', $lead->customer->email) }}">
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control"
                          placeholder="Enter customer address">{{ old('address', $lead->customer->address) }}</textarea>
            </div>

            <div class="form-group">
                <label>Reference</label>
                <input type="text" name="reference" class="form-control"
                       placeholder="Enter reference"
                       value="{{ old('reference', $lead->reference) }}">
            </div>

        </div>

        <!-- RIGHT SIDE -->
        <div class="col-md-6">

            <!-- Country + Code -->
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Code</label>
                        <input type="text" id="country_code" class="form-control"
                               readonly
                               value="{{ $lead->customer->country->phonecode ?? '' }}">
                    </div>
                </div>

                <div class="col-md-8">
                    <label>Country *</label>
                    <div class="input-group">
                        <select name="country" id="country" class="form-control select2" required>
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}"
                                    data-phonecode="{{ $country->phonecode }}"
                                    {{ $lead->customer->country_id == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>

                        <div class="input-group-append">
                            <button class="btn btn-outline-success" type="button"
                                    data-toggle="modal" data-target="#addCountryModal">+ Add</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- State -->
            <div class="col-md-10">
                <label>State *</label>
                <div class="input-group">
                    <select name="state" id="state" class="form-control select2" required>
                        <option value="">Select State</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}"
                                {{ $lead->customer->state_id == $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    </select>

                    <div class="input-group-append">
                        <button class="btn btn-outline-success" type="button"
                                data-toggle="modal" data-target="#addStateModal">+ Add</button>
                    </div>
                </div>
            </div>

            <!-- City -->
            <div class="col-md-10">
                <label>City *</label>
                <div class="input-group">
                    <select name="city" id="city" class="form-control select2" required>
                        <option value="">Select City</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}"
                                {{ $lead->customer->city_id == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>

                    <div class="input-group-append">
                        <button class="btn btn-outline-success" type="button"
                                data-toggle="modal" data-target="#addCityModal">+ Add</button>
                    </div>
                </div>
            </div>

            <!-- Other fields -->
            <div class="form-group">
                <label>Purpose</label>
                <input type="text" name="purpose" class="form-control"
                       placeholder="Enter purpose"
                       value="{{ old('purpose', $lead->purpose) }}">
            </div>

            <div class="form-group">
                <label>Remark</label>
                <input type="text" name="remark" class="form-control"
                       placeholder="Enter remark"
                       value="{{ old('remark', $lead->remark) }}">
            </div>
<div class="form-group">
    <label>Lead Date *</label>

    <div class="input-group">
        <input type="text"
               name="lead_date"
               id="lead_date"
               class="form-control"
               value="{{ old('lead_date', $lead->created_at ? $lead->created_at->format('Y-m-d H:i') : now()->format('Y-m-d H:i')) }}"
               required>

        <div class="input-group-append">
            <span class="input-group-text" id="calendar-icon" style="cursor:pointer;">
                <i class="fa fa-calendar"></i>
            </span>
        </div>
    </div>
</div>
            <!-- <div class="form-group">
                <label>Message</label>
                <input type="text" name="message" class="form-control"
                       placeholder="Enter message"
                       value="{{ old('message', $lead->message) }}">
            </div> -->

        </div>
    </div>

    <button type="submit" class="btn btn-success">Update Lead</button>
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
$(document).ready(function () {

    let fp = flatpickr("#lead_date", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        altInput: true,
        altFormat: "d/m/Y h:i K",
        defaultDate: document.getElementById("lead_date").value,
        allowInput: true,
    });

    // icon click
    $('#calendar-icon').on('click', function () {
        fp.open();
    });

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
            $('#city_country').select2({ dropdownParent: $('#addCityModal'), width: 'resolve', });
            $('#city_state').select2({ dropdownParent: $('#addCityModal'), width: 'resolve', });
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
        });  $(document).ready(function () {

            // Initialize Select2 for main form
            $('#country, #state, #city').select2({
               width: 'resolve',
                placeholder: 'Select option'
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
