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
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('permissions.index')}}">List</a></li>
            <li class="breadcrumb-item active">{{$label}}</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="card card-teal">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title">{{$label}}</h3>
          <div class="d-flex gap-2 ml-auto">
            <a href="{{route('permissions.index')}}" class="btn btn-sm btn-success">
              <i class="fa fa-arrow-left"></i> Back
            </a>
          </div>
        </div>
        <div class="card-body">
          <form action="{{ route('permissions.store') }}" method="POST" autocomplete="off">
            @csrf

            <div class="row">

              <!-- GROUP SELECT -->
              <div class="col-md-6">
                <div class="form-group">
                  <label>Permission Group</label>

                  <select name="group_selector" id="group_selector" class="form-control select2"
                    onchange="toggleGroupInput(this.value)">
                    <option value="">Select Group</option>

                    @foreach($groups as $group)
                      <option value="{{ $group }}">{{ $group }}</option>
                    @endforeach

                    <option value="__new__">➕ Create New Group</option>
                  </select>
                </div>

                <!-- NEW GROUP INPUT -->
                <div class="form-group d-none" id="new_group_wrapper">
                  <label>New Group Name</label>
                  <input type="text" name="group_name" class="form-control" placeholder="e.g. Banner Management">
                </div>
              </div>

              <!-- PERMISSION NAME -->
              <div class="col-md-6">
                <div class="form-group">
                  <label>Permission Name</label>
                  <input type="text" name="name" class="form-control" placeholder="e.g. list banner" required>
                </div>
              </div>

            </div>

            <button type="submit" class="btn btn-success mt-3">
              <i class="fa fa-save"></i> Save Permission
            </button>
          </form>

        </div>
      </div>
    </div>
  </section>
@endsection
@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    function toggleGroupInput(value) {
      const newGroup = document.getElementById('new_group_wrapper');
      const existingGroup = document.getElementById('existing_group');

      if (value === '__new__') {
        newGroup.classList.remove('d-none');
        if (existingGroup) existingGroup.remove();
      } else {
        newGroup.classList.add('d-none');

        if (existingGroup) {
          existingGroup.value = value;
        } else {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'group_name';
          input.id = 'existing_group';
          input.value = value;
          document.querySelector('form').appendChild(input);
        }
      }
    }
  </script>
  <script>
    $(document).ready(function () {
      $('.select2').select2({
        width: '100%',
        placeholder: "Select Permission Group",
      });
    });
    $(document).on('select2:open', function () {
      document.querySelector('.select2-container--open .select2-search__field').focus();
    });
  </script>
@endpush