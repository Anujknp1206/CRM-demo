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
                @can('add role')
                  <a href="{{ route('roles.create') }}">
                    <button class="btn btn-block btn-default btn-sm"><i class="fa fa-plus"></i> Add
                      Role</button>
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
                    <th>S.N.</th>
                    <th>Name</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @php $i = 1; @endphp
                  @foreach ($roles as $role)
                    <tr>
                      <td>{{$i}}</td>
                      <td>{{ $role->name }}</td>
                      <td>
                        @can('edit role')
                          <a title="Edit Role" href="{{route('roles.edit', $role->id)}}"><i
                              class="fa fa-edit text-green"></i></a>
                        @endcan
                        @can('delete role')
                          @if ($role->id != 1)
                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="delete-form"
                              style="display:inline;">
                              @csrf
                              @method('DELETE')
                              <button title="Delete Role" type="submit" style="border: none; background: none; cursor: pointer;"
                                class="delete-confirm">
                                <i class="fa fa-trash text-red"></i>
                              </button>
                            </form>
                          @endif
                        @endcan
                      </td>
                    </tr>
                    @php $i++;@endphp
                  @endforeach
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