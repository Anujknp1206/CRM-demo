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
                        <form action="{{ route('setting.update', $data->id) }}" method="POST" enctype="multipart/form-data"
                            autocomplete="off">
                            @csrf
                            @method('put')
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
                                                <input type="email" name="email" id="email" placeholder="Email Id"
                                                    class="form-control" required value="{{$data->email}}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Contact No.</label>
                                            <div class="form-controls">
                                                <input type="text" name="mobile" id="mobile" maxlength="10"
                                                    pattern="[0-9]{10}" placeholder="Enter Contact No." class="form-control"
                                                    required value="{{$data->mobile}}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="name">GST No.</label>
                                            <div class="form-controls">
                                                <input type="text" name="gst_number" id="gst_number"
                                                    placeholder="Enter GST No." class="form-control" required
                                                    value="{{$data->gst_number}}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Company Logo</label>
                                            <div class="form-controls">
                                                <input type="file" name="logo" id="logo" class="form-control">
                                            </div>
                                            <span>
                                                  <img src="{{  $data->logo ? asset('admin/uploads/logo/' . $data->logo) : asset('admin/uploads/logo/nopreview.png') }}"
                                                    class="img img-thumbnail" style="width:30%;">


                                                <!-- <form action=""
                                                                            method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit">
                                                                                <i class="fa fa-trash text-red"></i>
                                                                            </button>
                                                                        </form> -->

                                            </span>
                                        </div>

                                    </div>
                                    <div class="col-md-6 col-sm-6 col-lg-6">
                                        <div class="form-group">
                                            <label>Tag Line</label>
                                            <input type="text" name="tag_line" class="form-control"
                                                value="{{ old('tag_line', $data->tag_line ?? '') }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Landline no.</label>
                                            <div class="form-controls">
                                                <input type="text" name="landline" id="landline"
                                                    placeholder="Enter Landline Number" maxlength="10" pattern="[0-9]{10}"
                                                    class="form-control" value="{{$data->landline}}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Website Address</label>
                                            <div class="form-controls">
                                                <input type="text" name="website" id="website"
                                                    placeholder="Enter Website Address  " class="form-control"
                                                    value="{{$data->website}}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea name="address"
                                                class="form-control">{{ old('address', $data->address ?? '') }}</textarea>
                                        </div>

                                        <!-- <div class="form-group">
                                                                                    <label for="name">Footer Logo</label>
                                                                                    <div class="form-controls">
                                                                                        <input type="file" name="footer_logo" id="footer_logo" class="form-control">
                                                                                    </div>
                                                                                    <span>
                                                                                        <img src="{{ asset('admin/uploads/logo/' . ($data->logo ?: 'nopreview.png')) }}"
                                                                                            class="img img-thumbnail" style="width:30%;">
                                                                                        <a href=""
                                                                                            class="delete-confirm">
                                                                                            <i class="fa fa-trash text-red"></i>
                                                                                        </a>
                                                                                    </span>
                                                                                </div> -->
                                        <!-- <div class="form-group">
                                            <label for="name">Authorized Signature</label>
                                            <div class="form-controls">
                                                <input type="file" name="auth_sign" id="auth_sign" class="form-control">
                                            </div>
                                            <span>
                                                <img src="{{  $data->auth_sign ? asset('logo/' . $data->auth_sign) : asset('admin/uploads/logo/nopreview.png') }}"
                                                    class="img img-thumbnail" style="width:30%;"> -->


                                                <!-- <form action=""
                                                                            method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit">
                                                                                <i class="fa fa-trash text-red"></i>
                                                                            </button>
                                                                        </form> -->
                                            <!-- </span>
                                        </div> -->

                                    </div>

                                    <div class="col-md-12 p-4">
                                        <div class="card-footer">
                                            <input type="submit" name="submit" id="submit"
                                                class="btn btn-success float-sm-right" value="Save Settings">
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.delete-confirm').forEach(button => {
                button.addEventListener('click', function (event) {
                    event.preventDefault();

                    let url = this.getAttribute('href');

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
                            window.location.href = url;   // ✅ redirect to delete route
                        }
                    });
                });
            });
        });
    </script>
@endpush