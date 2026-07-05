<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="{{ route('dashboard') }}" class="nav-link" target="_blank">Home</a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="fa fa-bell"></i>

        @if(auth()->user()->unreadNotifications->count())
          <span class="badge badge-danger">
            {{ auth()->user()->unreadNotifications->count() }}
          </span>
        @endif
      </a>

      <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg">

        @if(auth()->user()->notifications->count() > 0)

          @foreach(auth()->user()->notifications->take(5) as $n)
            <a href="{{ $n->data['url'] ?? '#' }}" class="dropdown-item">
              <strong>{{ $n->data['title'] }}</strong>
              <div class="text-sm text-muted">
                {{ $n->data['message'] }}
              </div>
            </a>
            <div class="dropdown-divider"></div>
          @endforeach

          <a href="{{ route('notifications.index') }}" class="dropdown-item dropdown-footer">
            View All Notifications
          </a>

        @else
          <div class="dropdown-item text-center text-muted py-3">
            <i class="fa fa-bell-slash mb-2 d-block"></i>
            No notifications
          </div>
        @endif

      </div>
    </li>


    <!-- User Profile Dropdown -->
    <li class="nav-item dropdown user-menu">
      <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" style="color: white;">
        <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
      </a>

      <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

        <!-- User Header -->
        <li class="user-header" style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);
">
          <img src="{{ Auth::user()->photo
  ? asset('admin/uploads/user/' . Auth::user()->photo)
  : asset('admin/uploads/user/user.jpeg') }}" class="img-circle elevation-2" alt="User Image">

          <p style="color: white;">
            {{ Auth::user()->name }}
            <small style="color: white;">Joined:
              {{ \Carbon\Carbon::parse(Auth::user()->joining_date)->format('d M, Y') }}</small>
          </p>
        </li>

        <!-- Buttons Row -->
        <li class="user-footer d-flex justify-content-between"
          style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">

          <!-- Edit Profile -->
          <a href="{{ route('profile.index') }}" class="btn btn-success btn-flat w-50 mr-1">
            <i class="fas fa-user-edit"></i> Edit
          </a>

          <!-- Logout -->
          <a href="{{ route('logout') }}" class="btn btn-danger btn-flat w-50 ml-1">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
        </li>
      </ul>
    </li>


  </ul>
</nav>
<!-- /.navbar -->
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="#" class="brand-link">
    <img src="{{url('/')}}/admin/dist/img/AdminLTELogo.png" alt="AdminLTE Logo"
      class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">{{ucfirst(Auth::user()->name)}}</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Dashboard -->
        <li class="nav-item menu-open">
          <a href="{{route('dashboard')}}" class="nav-link active">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <!-- Master Settings -->
        @canany(['access settings', 'manage permissions', 'manage roles'])
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-cogs"></i>
              <p>
                Core Configurations
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">

              @can('access settings')
                <li class="nav-item">
                  <a href="{{ route('setting.index') }}" class="nav-link">
                    <i class="fas fa-sliders-h m-1"></i>
                    <p>Manage Settings</p>
                  </a>
                </li>
              @endcan

              @can('manage permissions')
                <li class="nav-item">
                  <a href="{{ route('permissions.index') }}" class="nav-link">
                    <i class="fas fa-key m-1"></i>
                    <p>Manage Permissions</p>
                  </a>
                </li>
              @endcan

              @can('manage roles')
                <li class="nav-item">
                  <a href="{{ route('roles.index') }}" class="nav-link">
                    <i class="fas fa-user-shield m-1"></i>
                    <p>Manage Roles</p>
                  </a>
                </li>
              @endcan

            </ul>
          </li>
        @endcanany

        @can('list user')
          <!-- Master User -->
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user"></i>
              <p>
                User Management
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @can('manage users')
                <li class="nav-item">
                  <a href="{{ route('users.index') }}" class="nav-link">
                    <i class="fas fa-user-cog m-1"></i>
                    <p>Manage Users</p>
                  </a>
                </li>
              @endcan
            </ul>
          </li>
        @endcan
        @can('list company')
          <!-- Master User -->
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-building"></i>
              <p>
                Company Management
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @can('manage company')
                <li class="nav-item">
                  <a href="{{ route('companies.index') }}" class="nav-link">
                    <i class="fas fa-city m-1"></i>
                    <p>Manage Companies</p>
                  </a>
                </li>
              @endcan
            </ul>
          </li>
        @endcan
        @can('asset management')
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-boxes"></i>
              <p>
                Asset Management
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">

              {{-- 🔧 MANAGE MACHINES --}}
              @can('manage machines')
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="fas fa-tools nav-icon"></i>
                    <p>
                      Manage Machines
                      <i class="fas fa-angle-left right"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    @can('machines')
                      <li class="nav-item">
                        <a href="{{ route('machines.index') }}" class="nav-link">
                          <i class="fas fa-cogs m-1"></i>
                          <p>Machines</p>
                        </a>
                      </li>
                    @endcan
                    @can('components')
                      <li class="nav-item">
                        <a href="{{ route('components.index') }}" class="nav-link">
                          <i class="fas fa-puzzle-piece m-1"></i>
                          <p>Components</p>
                        </a>
                      </li>
                    @endcan

                  </ul>
                </li>
              @endcan
            </ul>
          </li>
        @endcan
        <!-- Administration -->
        <li class="nav-header">Administration</li>
        <li class="nav-item">
          <a href="{{ route('logout') }}" class="nav-link">
            <i class="fas fa-sign-out-alt m-2 text-danger"></i>

            <p class="text">Logout</p>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->

  </div>
  <!-- /.sidebar -->
</aside>