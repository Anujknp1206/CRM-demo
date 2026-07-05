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
          <li class="breadcrumb-item"><a href="{{route('users.index')}}">List</a></li>
          <li class="breadcrumb-item active">{{$label}}</li>
        </ol>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
<!-- Content Wrapper. Contains page content -->
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="card card-teal">
      <div class="card-header d-flex justify-content-between align-items-center">
              <h3 class="card-title">{{$label}}</h3>
              <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
              
                <a href="{{route('users.index')}}" class="btn btn-sm btn-success">
                  <i class="fa fa-arrow-left"></i> Back
                </a>
              </div>
            </div>

      <div class="card-body">
        <h5 class="mb-3">
          Role: <strong>{{ $user->getRoleNames()->first() }}</strong> |
          User: <strong>{{ $user->name }}</strong>
        </h5>

        <form action="{{ route('users.update.permissions', $user->id) }}" method="POST" autocomplete="off">
          @csrf

          <!-- ensure empty submit -->
          <input type="hidden" name="permissions[]" value="">

          @foreach($permissions as $group => $groupPermissions)
            <div class="card mb-4 permission-group card-teal" id="group-{{ Str::slug($group) }}">
              <div class="card-header d-flex align-items-center">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox"
                         class="custom-control-input group-checkbox text-white"
                         id="group_{{ Str::slug($group) }}">
                  <label class="custom-control-label font-weight-bold text-white"
                         for="group_{{ Str::slug($group) }}">
                    {{ $group }}
                  </label>
                </div>
              </div>

              <div class="card-body">
                <div class="row">
                  @foreach($groupPermissions as $permission)
                    <div class="col-md-4 mb-2">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox"
                               class="custom-control-input permission-checkbox"
                               name="permissions[]"
                               value="{{ $permission->name }}"
                               id="perm{{ $permission->id }}"
                               {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="perm{{ $permission->id }}">
                          {{ $permission->name }}
                        </label>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          @endforeach

          <div class="text-right">
            <button type="submit" class="btn btn-success">
              <i class="fa fa-save"></i> Update Permissions
            </button>
          </div>
        </form>

        <div class="milestone-tracker">
  @foreach($permissions as $group => $groupPermissions)
    <a href="#group-{{ Str::slug($group) }}" class="milestone-item">
      <span class="dot"></span>
      <span class="label">{{ $group }}</span>
    </a>
  @endforeach
</div>

      </div>
    </div>
  </div>
</section>
@endsection
@push('styles')
<style>
  .milestone-tracker {
  cursor: move;
  user-select: none;
}

.milestone-tracker {
  position: fixed;
  top: 120px;
  right: 20px;
  width: 220px;
  background: #ffffff;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 15px 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  max-height: 70vh;
  overflow-y: auto;
  z-index: 1000;
}
html {
  scroll-behavior: smooth;
}

.milestone-item {
  display: flex;
  align-items: center;
  text-decoration: none;
  color: #6c757d;
  font-size: 13px;
  position: relative;
  padding-left: 10px;
}

.milestone-item::before {
  content: "";
  position: absolute;
  left: 16px;
  top: -18px;
  width: 2px;
  height: 18px;
  background: #ccc;
}

.milestone-item:first-child::before {
  display: none;
}

.milestone-item .dot {
  width: 14px;
  height: 14px;
  background: #ccc;
  border-radius: 50%;
  margin-right: 10px;
  flex-shrink: 0;
  transition: 0.3s;
}

.milestone-item.active .dot {
  background: #17a2b8;
  box-shadow: 0 0 0 4px rgba(23,162,184,0.25);
}

.milestone-item.active {
  color: #17a2b8;
  font-weight: bold;
}

.milestone-item:not(:last-child) {
  margin-bottom: 28px;
}

</style>
@endpush
@push('scripts')
<script>
(function () {
  const tracker = document.querySelector('.milestone-tracker');
  if (!tracker) return;

  let isDragging = false;
  let offsetX = 0;
  let offsetY = 0;

  tracker.addEventListener('mousedown', function (e) {
    isDragging = true;
    offsetX = e.clientX - tracker.getBoundingClientRect().left;
    offsetY = e.clientY - tracker.getBoundingClientRect().top;
    tracker.style.transition = 'none';
  });

  document.addEventListener('mousemove', function (e) {
    if (!isDragging) return;

    tracker.style.left = (e.clientX - offsetX) + 'px';
    tracker.style.top = (e.clientY - offsetY) + 'px';
    tracker.style.right = 'auto';   // remove fixed right
  });

  document.addEventListener('mouseup', function () {
    isDragging = false;
  });
})();
</script>
<script>
document.addEventListener('scroll', function () {

  const items = document.querySelectorAll('.milestone-item');

  items.forEach(link => {
    const section = document.querySelector(link.getAttribute('href'));
    if (!section) return;

    const rect = section.getBoundingClientRect();

    if (rect.top <= 150 && rect.bottom >= 150) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });

});
</script>



@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

  document.querySelectorAll('.permission-group').forEach(group => {

    const groupCheckbox = group.querySelector('.group-checkbox');
    const permissionCheckboxes = group.querySelectorAll('.permission-checkbox');

    // Initial state: if all permissions checked → group checked
    groupCheckbox.checked = [...permissionCheckboxes].every(cb => cb.checked);

    // When group checkbox changes
    groupCheckbox.addEventListener('change', function () {
      permissionCheckboxes.forEach(cb => cb.checked = this.checked);
    });

    // When any permission checkbox changes
    permissionCheckboxes.forEach(cb => {
      cb.addEventListener('change', function () {
        groupCheckbox.checked = [...permissionCheckboxes].every(cb => cb.checked);
      });
    });

  });

});
</script>
@endpush

