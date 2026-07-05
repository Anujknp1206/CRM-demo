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
            <li class="breadcrumb-item"><a href="{{route('setting.index')}}">List</a></li>
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
                <a href="{{route('setting.index')}}" class="btn btn-sm btn-success">
                  <i class="fa fa-arrow-left"></i> Back
                </a>
              </div>
            </div>
            <!-- /.card-header -->
            <form action="{{ route('setting.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
              @csrf
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 col-sm-6 col-lg-6">
                    <div class="form-group">
                      <label>Company Name</label>
                      <input type="text" name="company_name" class="form-control"
                        value="{{ old('company_name', $data->company_name ?? '') }}">
                    </div>
                    <div class="form-group">
                      <label for="name">Email</label>
                      <div class="form-controls">
                        <input type="email" name="email" id="email" placeholder="Email Id" class="form-control" required>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="name">Contact No.</label>
                      <div class="form-controls">
                        <input type="number" name="mobile" id="mobile" maxlength="10" pattern="[0-9]{10}"
                          placeholder="Enter Contact No." class="form-control" required>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="name">Company Logo</label>
                      <div class="form-controls">
                        <input type="file" name="logo" id="logo" class="form-control" required>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-sm-6 col-lg-6">
                    <div class="form-group">
                      <label for="name">Landline no.</label>
                      <div class="form-controls">
                        <input type="text" name="landline" id="landline" placeholder="Enter Landline Number"
                          maxlength="10" pattern="[0-9]{10}" class="form-control">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="name">Footer Logo</label>
                      <div class="form-controls">
                        <input type="file" name="footer_logo" id="footer_logo" class="form-control" required>
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Address</label>
                      <textarea name="address" class="form-control">{{ old('address', $data->address ?? '') }}</textarea>
                    </div>

                  </div>
                </div>
                <div class="col-md-12 p-4">
                  <div class="card-footer">
                    <input type="submit" name="submit" id="submit" class="btn btn-success float-sm-right"
                      value="Save Settings">
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