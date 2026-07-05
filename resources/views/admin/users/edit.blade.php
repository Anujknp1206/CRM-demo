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
            <form action="{{ route('users.update', $users->id) }}" method="POST" enctype="multipart/form-data"
              autocomplete="off">
              @csrf
              @method('put')
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 col-sm-6 col-lg-6">
                    <div class="form-group">
                      <label for="role">Choose Role</label>
                      <div class="form-controls">
                        <select class="form-control select2" name="role" id="role" required aria-label="Select Role">
                          <option value="" disabled selected>-- Please Choose Role --</option>
                          @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ (old('role', $users->roles->first()->name ?? '') == $role->name) ? 'selected' : '' }}>
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
                          required value="{{$users->email}}">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="joining_date">Joining Date</label>

                      <div class="input-group">
                        <input type="text" name="joining_date" id="joining_date" class="form-control" required readonly
                          value="{{ old('joining_date', $users->joining_date ? \Carbon\Carbon::parse($users->joining_date)->format('d-m-Y') : '') }}">

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
                        <input type="text" name="password" id="password" class="form-control" required
                          value="{{$users->core_password}}">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="name">Photograph</label>
                      <div class="form-controls">
                        <input type="file" name="photo" id="photo" class="form-control">
                      </div>
                    </div>
                    <span>
                      <img class="img img-thumbnail"
                        src="{{ $users->photo ? asset('admin/uploads/user/' . $users->photo) : asset('admin/uploads/user/user.jpeg') }}"
                        style="width:30%;" />
                      <a href="{{ route('user.deletePhoto', $users->id) }}" class="delete-confirm"
                        title="Delete Photograph">
                        <i class="fa fa-trash text-red"></i>
                      </a>
                    </span>
                  </div>
                  <div class="col-md-6 col-sm-6 col-lg-6">
                    <div class="form-group">
                      <label for="name">User Name:</label>
                      <div class="form-controls">
                        <input type="text" name="name" id="name" placeholder="User Name" class="form-control" required
                          value="{{$users->name}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="name">Mobile</label>
                      <div class="form-controls">
                        <input type="text" name="mobile" maxlength="10" placeholder="User Phone number"
                          class="form-control" required value="{{$users->mobile}}">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="name">Address</label>
                      <div class="form-controls">
                        <textarea name="address" id="address" class="form-control">{{$users->address}}</textarea>
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

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      document.querySelectorAll('.delete-confirm').forEach(button => {
        button.addEventListener('click', function (event) {
          event.preventDefault();

          let form = this.closest("form");

          Swal.fire({
            title: "Are you sure?",
            text: "This action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
          }).then((result) => {
            if (result.isConfirmed) {
              form.submit();

              // toastr after submit (optional UX)
              toastr.success("Deleted successfully");
            }
          });
        });
      });
    });
  </script>
@endpush