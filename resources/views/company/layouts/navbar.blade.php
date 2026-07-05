<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="nav-link" target="_blank">Home</a>
    </li>
    @can('employees')
      <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ route('attendance.index', $company) }}" class="nav-link">
          <p>Attendance</p>
        </a>
      </li>
    @endcan
    @can('machines')
      <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ route('machines.index') }}" class="nav-link">
          <p>Machines</p>
        </a>
      </li>
    @endcan
    @can('components')
      <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ route('components.index') }}" class="nav-link">
          <p>Components</p>
        </a>
      </li>
    @endcan
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
                {{ \Illuminate\Support\Str::limit($n->data['message'], 120) }}
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
      <a href="#" class="nav-link dropdown-toggle text-white" data-toggle="dropdown">
        <span class="d-none d-md-inline text-white">{{ Auth::user()->name }}</span>
      </a>

      <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

        <!-- User Header -->
        <li class="user-header" style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
          <img src=" {{ Auth::user()->photo
  ? asset('admin/uploads/user/' . Auth::user()->photo)
  : asset('admin/uploads/user/user.jpeg') }}" class="img-circle elevation-2" alt="User Image">

          <p class="text-white">
            {{ Auth::user()->name }}
            <small>Joined: {{ \Carbon\Carbon::parse(Auth::user()->joining_date)->format('d M, Y') }}</small>
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
  <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="brand-link">
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
          <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="nav-link active">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <!-- Master Settings -->
        @can('manage leads')
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-plus"></i>

              <p>
                Manage Leads
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

              @can('leads')
                <li class="nav-item">
                  <a href="{{ route('leads.index', ['company' => $company->id]) }}" class="nav-link">
                    <i class="fas fa-address-card nav-icon"></i>
                    <p>Leads</p>
                  </a>
                </li>
              @endcan
              @can('actions')
                <li class="nav-item">
                  <a href="{{ route('actions.index', ['company' => $company->id]) }}" class="nav-link">
                    <i class="fas fa-tasks nav-icon"></i>

                    <p>Manage Actions</p>
                  </a>
                </li>
              @endcan
            </ul>
          </li>
        @endcan
        @can('manage quotation')
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-file-invoice-dollar"></i>

              <p>
                Manage Quotation
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @can('quotation')
                <li class="nav-item">
                  <a href="{{ route('quotations.index', ['company' => $company->id]) }}" class="nav-link">
                    <i class="fas fa-file-invoice nav-icon"></i>

                    <p>Quotation</p>
                  </a>
                </li>
              @endcan
            </ul>
          </li>
        @endcan
        @can('manage payments')
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-credit-card"></i>
              <p>
                Manage Payments
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @can('payments')
                <li class="nav-item">
                  <a href="{{ route('payments.index', ['company' => $company->id]) }}" class="nav-link">
                    <i class="nav-icon fas fa-money-bill-wave"></i>
                    <p>Payments</p>
                  </a>
                </li>
              @endcan
            </ul>
          </li>
        @endcan
        @can('manage orders')
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-shopping-cart"></i>

              <p>
                Manage Orders
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @can('orders')
                <li class="nav-item">
                  <a href="{{ route('orders.index', ['company' => $company->id]) }}" class="nav-link">
                    <i class="nav-icon fas fa-box"></i>

                    <p>Orders</p>
                  </a>
                </li>
              @endcan
            </ul>
          </li>
        @endcan
        @can('manage bom')
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tasks"></i> {{-- Changed --}}
              <p>
                Manage BOM
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">
              @can('bom')
                <li class="nav-item">
                  <a href="{{ route('boms.index', ['company' => $company->id]) }}" class="nav-link">
                    <i class="nav-icon fas fa-clipboard-list"></i> {{-- Changed --}}
                    <p>BOM List</p>
                  </a>
                </li>
              @endcan
            </ul>
          </li>
        @endcan
        @can('manage rfi')
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-boxes"></i> {{-- Main Icon --}}
              <p>
                Manage RFI
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">

              {{-- RFI LIST --}}
              @can('view rfi')
                <li class="nav-item">
                  <a href="{{ route('rfis.index', ['company' => $company->id]) }}" class="nav-link">
                    <i class="nav-icon fas fa-clipboard-list"></i>
                    <p>RFI List</p>
                  </a>
                </li>
              @endcan
            </ul>
          </li>
        @endcan
        @can('manage po')
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-file-invoice"></i> {{-- PO Icon --}}
              <p>
                Manage PO
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">

              {{-- PO LIST --}}
              @can('view po')
                <li class="nav-item">
                  <a href="{{ route('pos.index', ['company' => $company->id]) }}" class="nav-link">
                    <i class="nav-icon fas fa-list"></i>
                    <p>PO List</p>
                  </a>
                </li>
              @endcan

            </ul>
          </li>
        @endcan
        @can('manage department_employees')
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>Employee Management <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">

              @can('departments')
                <li class="nav-item"><a href="{{ route('departments.index', $company) }}" class="nav-link">
                    <i class="fas fa-building nav-icon"></i>
                    <p>Departments</p>
                  </a></li>
              @endcan

              @can('employees')
                <li class="nav-item has-treeview">
                  <a href="#" class="nav-link">
                    <i class="fas fa-user nav-icon"></i>
                    <p>
                      Employees
                      <i class="right fas fa-angle-left"></i>
                    </p>
                  </a>

                  <ul class="nav nav-treeview">

                    {{-- Employee List --}}
                    <li class="nav-item">
                      <a href="{{ route('employees.index', $company) }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Employee List</p>
                      </a>
                    </li>

                    {{-- Attendance --}}
                    <li class="nav-item">
                      <a href="{{ route('attendance.index', $company) }}" class="nav-link">
                        <i class="far fa-calendar-check nav-icon"></i>
                        <p>Attendance</p>
                      </a>
                    </li>

                  </ul>
                </li>
              @endcan

            </ul>
          </li>
        @endcan
        @can('store management')
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-warehouse"></i>
              <p>
                Store Management
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">



              {{-- ================= ITEM MANAGEMENT ================= --}}
              <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-box-open"></i>
                  <p>Item Management <i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">

                  @can('categories')
                    <li class="nav-item"><a href="{{ route('categories.index', $company) }}" class="nav-link">
                        <i class="fas fa-tags nav-icon"></i>
                        <p>Categories</p>
                      </a></li>
                  @endcan

                  @can('subcategories')
                    <li class="nav-item"><a href="{{ route('subcategories.index', $company) }}" class="nav-link">
                        <i class="fas fa-sitemap nav-icon"></i>
                        <p>Sub Categories</p>
                      </a></li>
                  @endcan

                  @can('items')
                    <li class="nav-item"><a href="{{ route('items.index', $company) }}" class="nav-link">
                        <i class="fas fa-box nav-icon"></i>
                        <p>Items</p>
                      </a></li>
                  @endcan

                </ul>
              </li>

              {{-- ================= STOCK ================= --}}
              <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-warehouse"></i>
                  <p>Stock <i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                  @can('stocks')
                    <li class="nav-item">
                      <a href="{{ route('stocks.index', $company) }}" class="nav-link">
                        <i class="fas fa-chart-bar nav-icon"></i>
                        <p>Stock Overview</p>
                      </a>
                    </li>
                  @endcan

                  @can('stockin')
                    <li class="nav-item"><a href="{{ route('stock-ins.index', $company) }}" class="nav-link">
                        <i class="fas fa-arrow-down nav-icon"></i>
                        <p>Stock In</p>
                      </a></li>
                  @endcan

                  @can('issues')
                    <li class="nav-item"><a href="{{ route('issues.index', $company) }}" class="nav-link">
                        <i class="fas fa-truck-moving nav-icon"></i>
                        <p>Issue Stock</p>
                      </a></li>
                  @endcan

                </ul>
              </li>

              <!-- @can('projects')
                                                                        <li class="nav-item"><a href="{{ route('projects.index', $company) }}" class="nav-link">
                                                                            <i class="fas fa-project-diagram nav-icon"></i>
                                                                            <p>Projects</p>
                                                                          </a></li>
                                                                      @endcan -->

              @can('suppliers')
                <li class="nav-item"><a href="{{ route('suppliers.index', $company) }}" class="nav-link">
                    <i class="fas fa-truck-loading nav-icon"></i>
                    <p>Suppliers</p>
                  </a></li>
              @endcan


            </ul>
          </li>
        @endcan
        @can('parts')
          <li class="nav-item">
            <a href="{{ route('parts.index', $company) }}" class="nav-link">
              <i class="nav-icon fas fa-puzzle-piece"></i>
              <p>Parts</p>
            </a>
          </li>
        @endcan
        @can('recipes')
          <li class="nav-item">
            <a href="{{ route('recipes.index', $company) }}" class="nav-link">
              <i class="nav-icon fas fa-flask"></i>
              <p>Recipes</p>
            </a>
          </li>
        @endcan
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-database"></i>
            <p>Masters <i class="right fas fa-angle-left"></i></p>
          </a>
          <ul class="nav nav-treeview">
            @can('units')
              <li class="nav-item"><a href="{{ route('units.index', $company) }}" class="nav-link">
                  <i class="fas fa-ruler-combined nav-icon"></i>
                  <p>Units</p>
                </a></li>
            @endcan
            @can('brands')
              <li class="nav-item"><a href="{{ route('brands.index', $company) }}" class="nav-link">
                  <i class="fas fa-tags nav-icon"></i>
                  <p>Brands</p>
                </a></li>
            @endcan
            @can('conditions')
              <li class="nav-item"><a href="{{ route('conditions.index', $company) }}" class="nav-link">
                  <i class="fas fa-clipboard-check nav-icon"></i>
                  <p>Conditions</p>
                </a></li>
            @endcan
            @can('locations')
              <li class="nav-item"><a href="{{ route('locations.index', $company) }}" class="nav-link">
                  <i class="fas fa-map-marker-alt nav-icon"></i>
                  <p>Locations</p>
                </a></li>
            @endcan
            @can('priority')
              <li class="nav-item">
                <a href="{{ route('priorities.index', $company) }}" class="nav-link">
                  <i class="fas fa-exclamation-circle nav-icon"></i>
                  <p>Priority</p>
                </a>
              </li>
            @endcan
            @can('shifts')
              <li class="nav-item">
                <a href="{{ route('shifts.index', $company) }}" class="nav-link">
                  <i class="fas fa-clock nav-icon"></i>
                  <p>Shifts</p>
                </a>
              </li>
            @endcan
            @can('specifications')
              <li class="nav-item">
                <a href="{{ route('specifications.index', $company) }}" class="nav-link">
                  <i class="fas fa-cogs nav-icon"></i>
                  <p>Specifications</p>
                </a>
              </li>
            @endcan
          </ul>
        </li>
        @can('manage reports')
          <li class="nav-item has-treeview">

            <a href="#" class="nav-link">

              <i class="nav-icon fa fa-chart-line"></i>

              <p>
                Manage Reports
                <i class="right fas fa-angle-left"></i>
              </p>

            </a>

            <ul class="nav nav-treeview">

              @can('customer reporting')

                <!-- <li class="nav-item">

                                  <a href="{{ route('company.reports.index', $company->id) }}" class="nav-link">

                                    <i class="fa fa-users nav-icon"></i>

                                    <p>About Customers</p>

                                  </a>

                                </li> -->

              @endcan
              @can('order reporting')
                <li class="nav-item">
                  <a href="{{ route('company.reports.orders', $company->id) }}" class="nav-link">
                    <i class="nav-icon fas fa-shopping-cart"></i>
                    <p>Order Reports</p>
                  </a>
                </li>
              @endcan
            </ul>
          </li>
        @endcan
        <!-- Administration -->
        <li class="nav-header">Administration</li>
        <li class="nav-item">
          <a href="{{ route('dashboard') }}" class="nav-link">
            <i class="nav-icon fas fa-arrow-left text-danger"></i>
            <p>Back</p>
          </a>
        </li>

      </ul>
    </nav>
    <!-- /.sidebar-menu -->

  </div>
  <!-- /.sidebar -->
</aside>