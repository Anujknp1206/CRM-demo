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

                @can('add permissions')
                  <a href="{{ route('permissions.create') }}" class="btn btn-sm btn-default border-0"
                    style="background:#ffffff; color:#000;">
                    <i class="fa fa-plus"></i> Add Permission
                  </a>
                @endcan

                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-success">
                  <i class="fa fa-arrow-left"></i> Back
                </a>

              </div>
            </div>
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th width="5%">#</th>
                    <th width="65%">Permissions</th>
                    <th width="30%">Action</th>
                  </tr>
                </thead>

                <tbody>
                  @php $i = 1; @endphp

                  @foreach($permissions as $group => $groupPermissions)

                    {{-- GROUP HEADER ROW --}}
                    <tr class="bg-light">
                      <td colspan="3">
                        <strong>Group Name: {{ $group }}</strong>
                      </td>
                    </tr>

                    {{-- GROUP PERMISSIONS --}}
                    @foreach($groupPermissions as $permission)
                      <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $permission->name }}</td>
                        <td>
                          @can('edit permissions')
                            <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-sm text-success"
                              title="Edit Permission">
                              <i class="fa fa-edit"></i>
                            </a>
                          @endcan
                          @can('delete permissions')
                            <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST"
                              class="d-inline delete-confirm">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-sm text-danger" title="Delete Permission">
                                <i class="fa fa-trash"></i>
                              </button>
                            </form>
                          @endcan
                        </td>
                      </tr>
                    @endforeach

                  @endforeach
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>
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
@push('styles')
  <style>
    .btn-admin-white {
      background: #ffffff !important;
      color: #000 !important;
      border: 1px solid #dee2e6 !important;
    }

    .btn-admin-white:hover {
      background: #f8f9fa !important;
    }
  </style>
@endpush