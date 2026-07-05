<!-- /.content-wrapper -->
<footer class="main-footer d-flex justify-content-between align-items-center">

  <div>
    <strong>
      Copyright &copy; 2025

      <a href="{{ route('dashboard') }}" style="color:#fff;">

        {{ $settings->company_name ?? 'Demo' }}
        | {{ ucfirst(Auth::user()->name) }}

      </a>.
    </strong>

    All rights reserved.
  </div>

  <div class="text-right">

    <span class="mr-3">
      Version :
      <strong style="color:#fff;">
        v2.1.3
      </strong>
    </span>


  </div>

</footer>

<!-- /.control-sidebar -->
<!-- Password Verify Modal -->

<div class="modal fade" id="addCountryModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
  data-keyboard="false">
  <div class="modal-dialog" role="document">
    <form id="addCountryForm" autocomplete="off">
      @csrf
      <div class="modal-content">
        <div class="modal-header"
          style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
          <h5 class="modal-title text-white">Add Country</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Country Name</label>
            <input type="text" name="name" placeholder="Enter Country Name " class="form-control" required>
          </div>
          <div class="form-group">
            <label>Code</label>
            <input type="text" name="code" placeholder="Enter Country Code" class="form-control">
          </div>
          <div class="form-group">
            <label>Phone Code</label>
            <input type="text" name="phonecode" placeholder="Enter Country Phone Code" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save Country</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="modal fade" id="addStateModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
  data-keyboard="false">
  <div class="modal-dialog" role="document">
    <form id="addStateForm" autocomplete="off">
      @csrf
      <div class="modal-content">
        <div class="modal-header"
          style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
          <h5 class="modal-title text-white">Add State</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Country</label>
            <select name="country_id" id="state_country" class="form-control select2" required>
              <option value="">Select Country</option>
              @foreach($countries as $country)
                <option value="{{ $country->id }}">{{ $country->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>State Name</label>
            <input type="text" name="name" placeholder="Enter State Name " class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save State</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="modal fade" id="addCityModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
  data-keyboard="false">
  <div class="modal-dialog" role="document">
    <form id="addCityForm" autocomplete="off">
      @csrf
      <div class="modal-content">
        <div class="modal-header"
          style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
          <h5 class="modal-title text-white">Add City</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Country</label>
            <select name="country_id" id="city_country" class="form-control select2" required>
              <option value="">Select Country</option>
              @foreach($countries as $country)
                <option value="{{ $country->id }}">{{ $country->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>State</label>
            <select name="state_id" id="city_state" class="form-control select2" required>
              <option value="">Select State</option>
            </select>
          </div>
          <div class="form-group">
            <label>City Name</label>
            <input type="text" name="name" placeholder="Enter City Name " class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save City</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="modal fade" id="leadDetailsModal" tabindex="-1" aria-hidden="true" data-backdrop="static"
  data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-teal "
        style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
        <h5 class="modal-title">Lead Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">

        <div id="lead-details-content">
          <p class="text-center">Loading...</p>
        </div>

      </div>

    </div>
  </div>
</div>