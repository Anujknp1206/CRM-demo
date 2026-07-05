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
            <li class="breadcrumb-item"><a href="{{route('users.index')}}">List</a></li>
            <li class="breadcrumb-item active">{{$label}}</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card card-teal">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h3 class="card-title">{{$label}}</h3>
              <div class="d-flex gap-2 ml-auto">
                <a href="{{route('users.index')}}" class="btn btn-sm btn-success">
                  <i class="fa fa-arrow-left"></i> Back
                </a>
              </div>
            </div>
            <!-- /.card-header -->
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
              @csrf
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 col-sm-6 col-lg-6">
                    <div class="form-group">
                      <label for="role">Choose Role</label>
                      <div class="form-controls">
                        <select class="form-control select2" name="role" id="role" required aria-label="Select Role">
                          <option value="" disabled selected>-- Please Choose Role --</option>
                          @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                              {{ $role->name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="name">Email</label>
                      <div class="form-controls">
                        <input type="email" name="email" id="email" placeholder="User Email Id" class="form-control"
                          value="{{ old('email') }}" required>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="joining_date">Joining Date</label>

                      <div class="input-group">
                        <input type="text" name="joining_date" id="joining_date" class="form-control" required
                          placeholder="DD/MM/YYYY" value="{{ old('joining_date') }}">

                        <div class="input-group-append">
                          <span class="input-group-text bg-white" id="calendarIcon" style="cursor:pointer">
                            <i class="fa fa-calendar"></i>
                          </span>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="name">Password</label>
                      <div class="form-controls">
                        <input type="text" name="password" id="password" value="{{ old('password') }}"
                          class="form-control" required>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="name">Photograph</label>
                      <div class="form-controls">
                        <input type="file" name="photo" id="photo" class="form-control">
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-sm-6 col-lg-6">
                    <div class="form-group">
                      <label for="name">User Name:</label>
                      <div class="form-controls">
                        <input type="text" name="name" value="{{ old('name') }}" id="name" placeholder="User Name"
                          class="form-control" required>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="name">Mobile</label>
                      <div class="form-controls">
                        <input type="tel" name="mobile" value="{{ old('mobile') }}" maxlength="10" minlength="10"
                          inputmode="numeric" pattern="[0-9]{10}" placeholder="User Phone number " class="form-control"
                          required>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="name">Address</label>
                      <div class="form-controls">
                        <textarea name="address" id="address" class="form-control">{{ old('address') }}</textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 p-4">
                    <div class="card-footer">
                      <input type="submit" name="submit" id="submit" class="btn btn-success float-sm-right"
                        value="Save User">
                    </div>
                  </div>
                </div>
              </div>
            </form>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
@endsection
@push('styles')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <script>
    const joiningPicker = flatpickr("#joining_date", {
      dateFormat: "d-m-Y",
      clickOpens: true,
      allowInput: true
    });

    document.getElementById('calendarIcon').addEventListener('click', function () {
      joiningPicker.open();
    });
  </script>

@endpush