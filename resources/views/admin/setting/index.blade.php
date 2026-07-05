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
              <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                @can('add setting')
                  <a href="{{ route('setting.create') }}">
                    <button class="btn btn-block btn-default btn-sm"><i class="fa fa-plus"></i> Add
                      Setting</button>
                  </a>
                @endcan
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-success">
                  <i class="fa fa-arrow-left"></i> Back
                </a>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Logo</th>
                      <!-- <th>Auth Sign</th> -->
                      <th>Company</th>
                      <th>Tagline</th>
                      <th>Address</th>
                      <th>Email</th>
                      <th>Contact No.</th>
                      <th>Website</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @if($data)
                      <tr>
                        <td><img
                            src="{{ $data->logo ? asset('admin/uploads/logo/' . $data->logo) : asset('admin/uploads/logo/nopreview.png') }}"
                            class="img" width="200" height="80" loading="lazy" decoding="async"
                            alt="{{ $data->company_name}}"></td>
                        <!-- <td><img
                                      src="{{  $data->auth_sign ? asset('logo/' . $data->auth_sign) : asset('admin/uploads/logo/nopreview.png') }}"
                                        class="img" style="width:100px;"></td> -->
                        <td>{{ $data->company_name}}</td>
                        <td>{{ $data->tag_line}}</td>
                        <td>{{ $data->address}}</td>

                        <td>{{$data->email}}</td>
                        <td>{{$data->mobile}},{{$data->landline}}</td>
                        <td>{{$data->website}}</td>
                        <td>
                          @can('edit setting')
                            <a title="Edit Settings" href="{{route('setting.edit', $data->id)}}"><i
                                class="fa fa-edit text-green"></i></a>
                          @endcan
                        </td>
                      </tr>
                    @endif
                    </tfoot>
                </table>

            </div>
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
  <!-- DataTables Core -->
  <link rel="stylesheet" href="{{ url('/') }}/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="{{ url('/') }}/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

  <!-- Buttons Extension -->
  <link rel="stylesheet" href="{{ url('/') }}/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <style>
    /* Row containing Buttons + Search */
    .dataTables_wrapper .row:first-child {
      padding: 10px 0 !important;
      /* Increase padding */
      margin-bottom: 5px !important;
      /* Reduce space under row */
    }

    /* Buttons styling – Increase padding inside buttons */
    .dataTables_wrapper .dt-buttons .btn {
      padding: 6px 10px !important;
    }

    /* Search box styling */
    .dataTables_wrapper .dataTables_filter input {
      padding: 4px 8px !important;
      /* Slightly more padding */
      margin-left: 5px !important;
      /* Adjust spacing next to "Search:" label */
    }
  </style>
@endpush
