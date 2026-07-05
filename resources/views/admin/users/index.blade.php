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
                @can('add user')
                  <a href="{{ route('users.create') }}">
                    <button class="btn btn-block btn-default btn-sm"><i class="fa fa-plus"></i> Add
                      User</button>
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
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Created By</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @if(!empty($users))
                    @php $i = 1; @endphp
                    @foreach ($users as $user)
                      <tr>
                        <td>{{$i}}</td>
                        <td>
                          <img class="img img-thumbnail"
                            src="{{ $user->photo ? asset('admin/uploads/user/' . $user->photo) : asset('admin/uploads/user/user.jpeg') }}"
                            style="width:30%;">
                        </td>
                        <td>{{ $user->name }}</td>

                        <td>{{ $user->creator ? $user->creator->name : 'N/A' }}</td>
                        <td>{{ $user->getRoleNames()->first() }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->full_mobile }}</td>
                        <td>
                          @can('edit user')
                            @if(Auth::user()->can('edit user') || Auth::user()->hasRole('Super Admin'))
                              @if(!$user->hasRole('Super Admin')) {{-- Prevent editing Super Admin --}}
                                <a title="Edit User" href="{{ route('users.edit', $user->id) }}" style="margin-right: 8px;">
                                  <i class="fa fa-edit text-green"></i>
                                </a>
                              @endif
                            @endif
                          @endcan

                          @can('delete user')
                            @if(Auth::user()->can('delete user') || Auth::user()->hasRole('Super Admin'))
                              @if(!$user->hasRole('Super Admin')) {{-- Prevent deleting Super Admin --}}
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="delete-confirm"
                                  autocomplete="off" style="display:inline;">
                                  @csrf
                                  @method('DELETE')
                                  <button title="Delete User" type="submit"
                                    style="border: none; background: none; cursor: pointer; margin-right: 8px;">
                                    <i class="fa fa-trash text-red"></i>
                                  </button>
                                </form>
                              @endif
                            @endif
                          @endcan
                          {{-- Manage Permissions Icon --}}
                          @if(Auth::user()->hasRole('Super Admin') || Auth::user()->can('manage permissions'))

                            <a href="{{ route('users.permissions', $user->id) }}" title="Manage Permissions"
                              style="font-size:20px; margin-right:8px;">
                              <i class="fa fa-shield"></i>
                            </a>
                          @endif
                        </td>
                      </tr>
                      @php $i++;@endphp
                    @endforeach
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