@extends('company.layouts.master')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $title }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                href="{{ route('company.dashboard', ['company' => $company->id]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">{{ $label }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card card-teal">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">{{$label}}</h3>
                <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                    @can('add employee')
                        <button class="btn btn-default btn-sm" onclick="openCreateEmployee()">
                            <i class="fa fa-plus"></i> Add Employee
                        </button>
                    @endcan
                    <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>User ID</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="employeeTable">
                        @forelse($employees as $key => $e)
                            <tr id="employee-row-{{ $e->id }}">
                                <td>{{ $key + 1 }}</td>
                                <td class="emp-name">{{ $e->first_name }} {{ $e->last_name }}</td>
                                <td class="emp-userid">{{ $e->user_id }}</td>
                                <td class="emp-dept">{{ optional($e->department)->name }}</td>
                                <td class="emp-status">
                                    <span class="badge toggle-status 
                                                    {{ $e->status ? 'badge-success' : 'badge-danger' }}" data-id="{{ $e->id }}"
                                        style="cursor:pointer;">

                                        {{ $e->status ? 'Enabled' : 'Disabled' }}

                                    </span>
                                </td>
                                <td>
                                    @can('edit employee')
                                        <button class="btn btn-sm edit-employee" data-id="{{ $e->id }}" title="Edit Employee"><i
                                                class="fa fa-edit text-green"></i></button>
                                    @endcan
                                    @can('delete employee')
                                        <button class="btn btn-sm delete-employee" data-id="{{ $e->id }}" title="Delete Employee"><i
                                                class="fa fa-trash text-red"></i></button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr id="no-employee-row">
                                <td colspan="6" class="text-center">😢 No employees found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="modal fade" id="employeeModal" tabindex="-1" data-backdrop="static" data-keyboard="false">

        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h5 class="modal-title" id="employeeModalTitle">Add Employee</h5>
                    <button class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form id="employeeForm" autocomplete="off">
                    @csrf
                    <input type="hidden" id="employee_id">
                    <div class="modal-body">
                        <div id="employeeAccordion">
                            <div class="card card-outline">
                                <div class="card-header" data-toggle="collapse" data-target="#collapsePersonal"
                                    style="cursor:pointer;background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
                                    <h3 class="card-title text-white">Personal Information</h3>
                                </div>
                                <div id="collapsePersonal" class="collapse show" data-parent="#employeeAccordion">
                                    <div class="card-body row">
                                        <div class="col-md-4 form-group">
                                            <label>First Name *</label>
                                            <input type="text" id="emp_first_name" class="form-control" required
                                                placeholder="Enter First Name">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>Middle Name</label>
                                            <input type="text" id="emp_middle_name" class="form-control"
                                                placeholder="Enter Middle Name">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>Last Name *</label>
                                            <input type="text" id="emp_last_name" class="form-control" required
                                                placeholder="Enter Last Name">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>Father's Name</label>
                                            <input type="text" id="emp_father_name" class="form-control"
                                                placeholder="Enter Father's Name">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>Email</label>
                                            <input type="email" id="emp_email" class="form-control"
                                                placeholder="example@company.com">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>Mobile *</label>
                                            <input type="tel" id="emp_mobile" class="form-control" pattern="[0-9]{10}"
                                                maxlength="10" required placeholder="Enter 10-digit Mobile Number">

                                        </div>
                                        <div class="col-md-4 form-group"><label>Previous Company</label><input type="text"
                                                id="emp_previous_company" class="form-control"
                                                placeholder="Enter Previous Company"></div>
                                        <div class="col-md-4 form-group"><label>Experience (Years)</label><input
                                                type="number" id="emp_experience_years" class="form-control"
                                                placeholder="Enter Experience (in Years)"></div>
                                        <div class="col-md-4 form-group"><label>Any Reference</label><input type="text"
                                                id="reference_name" class="form-control" placeholder="Enter Reference Name">
                                        </div>

                                        <div class="col-md-12 form-group"><label>Address</label><textarea id="emp_address"
                                                class="form-control" rows="2" placeholder="Enter Full Address"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-outline">
                                <div class="card-header collapsed" data-toggle="collapse" data-target="#collapseLocation"
                                    style="cursor:pointer;background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
                                    <h3 class="card-title text-white">Location & Department</h3>
                                </div>
                                <div id="collapseLocation" class="collapse" data-parent="#employeeAccordion">
                                    <div class="row" style="min-height: 1px;padding: 1rem 1.25rem;">
                                        <div class="col form-group">
                                            <label>Country</label>
                                            <div class="input-group">
                                                <select id="emp_country" class="form-control select2" required>
                                                    <option value="">Select Country</option>
                                                    @foreach($countries as $c)
                                                        <option value="{{$c->id}}" {{ $c->id == 101 ? 'selected' : '' }}>
                                                            {{$c->name}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append"><button type="button"
                                                        class="btn btn-outline-success" data-toggle="modal"
                                                        data-target="#addCountryModal">+</button></div>
                                            </div>
                                        </div>
                                        <div class="col form-group">
                                            <label>State</label>
                                            <div class="input-group">
                                                <select id="emp_state" class="form-control select2" required>
                                                    <option value="">Select State</option>
                                                </select>
                                                <div class="input-group-append"><button type="button"
                                                        class="btn btn-outline-success" data-toggle="modal"
                                                        data-target="#addStateModal">+</button></div>
                                            </div>
                                        </div>
                                        <div class="col form-group">
                                            <label>City</label>
                                            <div class="input-group">
                                                <select id="emp_city" class="form-control select2" required>
                                                    <option value="">Select City</option>
                                                </select>
                                                <div class="input-group-append"><button type="button"
                                                        class="btn btn-outline-success" data-toggle="modal"
                                                        data-target="#addCityModal">+</button></div>
                                            </div>
                                        </div>
                                        <div class="col form-group">
                                            <label>Pincode</label>
                                            <div class="input-group">
                                                <input type="text" id="emp_pincode" class="form-control"
                                                    placeholder="Enter pincode">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row" style="min-height: 1px;padding: 0 1.25rem;">
                                        <div class="col-md-6 form-group">
                                            <label>Department</label>
                                            <div class="input-group">
                                                <select id="emp_department" class="form-control select2" required>
                                                    <option value="">Select Department</option>
                                                    @foreach($departments as $d) <option value="{{$d->id}}">{{$d->name}}
                                                    </option> @endforeach
                                                </select>
                                                <div class="input-group-append"><button type="button"
                                                        class="btn btn-outline-success" data-toggle="modal"
                                                        data-target="#departmentModal">+</button></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>Joining Date</label>
                                            <div class="input-group" style="cursor:pointer;">
                                                <input type="text" id="emp_joining_date" class="form-control"
                                                    placeholder="DD/MM/YYYY" required>
                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="card card-outline">
                                <div class="card-header collapsed" data-toggle="collapse" data-target="#collapseOffice"
                                    style="cursor:pointer;background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
                                    <h3 class="card-title text-white">Office & Login Details</h3>
                                </div>
                                <div id="collapseOffice" class="collapse" data-parent="#employeeAccordion">
                                    <div class="card-body row">
                                        <div class="col-md-4 form-group">
                                            <label>User ID</label>
                                            <input type="text" id="emp_user_id" class="form-control bg-light"
                                                placeholder="Auto-generated">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>Password</label>
                                            <div class="input-group">
                                                <input type="password" id="emp_password" class="form-control bg-light"
                                                    readonly>
                                                <span class="input-group-text" id="togglePassword" style="cursor:pointer">
                                                    <i class="fa fa-eye"></i>
                                                </span>
                                            </div>

                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>Status</label>
                                            <div class="custom-control custom-switch mt-2">
                                                <input type="checkbox" class="custom-control-input" id="emp_status_toggle"
                                                    checked>
                                                <label class="custom-control-label" for="emp_status_toggle"
                                                    id="status_label">Enabled</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-outline ">
                                <div class="card-header collapsed" data-toggle="collapse" data-target="#collapseBank"
                                    style="cursor:pointer;background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
                                    <h3 class="card-title text-white">Bank Information</h3>
                                </div>
                                <div id="collapseBank" class="collapse" data-parent="#employeeAccordion">
                                    <div class="card-body row">
                                        <div class="col-md-6 form-group">
                                            <label>Account Holder Name</label>
                                            <input type="text" id="emp_account_name" class="form-control"
                                                placeholder="Name as per Bank Record">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>Account No.</label>
                                            <input type="text" id="emp_account_no" class="form-control"
                                                placeholder="Enter Account Number">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Bank Name</label>
                                            <input type="text" id="emp_bank_name" class="form-control"
                                                placeholder="e.g. HDFC Bank">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>IFSC Code</label>
                                            <input type="text" id="emp_ifsc_code" class="form-control"
                                                placeholder="HDFC0001234">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>PAN No.</label>
                                            <input type="text" id="emp_pan" class="form-control" placeholder="ABCDE1234F">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Branch Name</label>
                                            <input type="text" id="branch_name" class="form-control"
                                                placeholder="Enter Branch Name ">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success px-5">Save Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="adminPasswordModal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title">
                        <i class="fa fa-shield mr-1"></i>
                        Admin Verification Required
                    </h5>
                    <button class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <!-- Explanation -->
                    <!-- Explanation -->
                    <div class="alert alert-warning small">
                        <strong>Why is this required?</strong><br>
                        You are trying to view an employee’s login password.
                        For security reasons, only a verified admin can perform this action.
                    </div>

                    <div class="alert alert-info small">
                        <strong>What will happen next?</strong>
                        <ul class="mb-0 pl-3">
                            <li>Your admin password will be verified</li>
                            <li>
                                <strong>The employee password will be reset automatically</strong>
                            </li>
                            <li>
                                A new password will be revealed to you
                            </li>
                            <li>
                                The previous password will no longer work
                            </li>
                            <li>
                                This action may be logged for security purposes
                            </li>
                        </ul>
                    </div>


                    <!-- Inputs -->
                    <input type="password" id="admin_password" class="form-control" placeholder="Enter your admin password">
                    <p class="text-muted small mt-2">
                        ⚠️ Once confirmed, the employee must use the new password to log in.
                    </p>
                    <input type="hidden" id="password_employee_id">
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger btn-sm" id="confirmAdminPassword">
                        <i class="fa fa-unlock"></i> Verify & Reveal
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- Include your mini-modals (addCountryModal, addStateModal, etc.) here --}}
@endsection
@push('styles')
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/summernote/summernote-bs4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{url('/')}}/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const companyInitials = "{{ $company->initials() }}";
        let nextId = "{{ $employees->max('id') + 1 }}";
        function initSelect2(parentModal, width = '100%') {
            $(parentModal).find('.select2').each(function () {

                // prevent double init
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }

                $(this).select2({
                    width: width,
                    dropdownParent: $(parentModal)
                });
            });
        }


        let joiningDatePicker;
        $(document).ready(function () {


            function initDatePicker(selector) {

                // 🔴 FORCE remove native date behavior
                const input = document.querySelector(selector);

                   const picker = flatpickr(input, {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            allowInput: true,
            clickOpens: true,
            defaultDate: "today"
        });

                // 🟢 Open calendar on icon click
                input.closest('.input-group')
                    .querySelector('.input-group-text')
                    .addEventListener('click', () => picker.open());

                return picker;
            }
            joiningDatePicker = initDatePicker("#emp_joining_date");
            // Real-time User ID Generation
            $('#emp_first_name').on('keyup', function () {
                let namePart = $(this).val().trim().replace(/\s+/g, '').toLowerCase();
                if (namePart !== "") {
                    $('#emp_user_id').val(companyInitials + namePart + nextId);
                } else {
                    $('#emp_user_id').val('');
                }
            });
            // Status Toggle Switch Label
            $('#emp_status_toggle').on('change', function () {
                $('#status_label').text($(this).is(':checked') ? 'Enabled' : 'Disabled');

            });

            // Dependent Dropdowns (Country -> State -> City)
            $('#emp_country').on('change', function () {
                let countryId = $(this).val();
                $('#emp_state').html('<option>Loading...</option>');
                $.get("{{ route('getStates') }}", { country_id: countryId }, function (data) {
                    let options = '<option value="">Select State</option>';
                    data.forEach(s => { options += `<option value="${s.id}">${s.name}</option>`; });
                    $('#emp_state').html(options).trigger('change');
                });
            });
            $('#emp_state').on('change', function () {
                let stateId = $(this).val();
                $('#emp_city').html('<option>Loading...</option>');
                $.get("{{ route('getCities') }}", { state_id: stateId }, function (data) {
                    let options = '<option value="">Select City</option>';
                    data.forEach(c => { options += `<option value="${c.id}">${c.name}</option>`; });
                    $('#emp_city').html(options).trigger('change');
                });
            }); if ($('#emp_country').val() == '101') {
                $('#emp_country').trigger('change');
            }
            // Auto-load states for default country (101)
            loadStates(101);
            $('#employeeModal').on('shown.bs.modal', function () {
                initSelect2('#employeeModal', '80%');
            });


            $('#addCountryModal').on('shown.bs.modal', function () {
                initSelect2('#addCountryModal');
            });

            $('#addStateModal').on('shown.bs.modal', function () {
                initSelect2('#addStateModal');
            });

            $('#addCityModal').on('shown.bs.modal', function () {
                initSelect2('#addCityModal');
            });
        });


        function openCreateEmployee() {
            isEditMode = false;
            $('#employeeForm')[0].reset();
            $('#employee_id').val('');
            const password = generatePassword();
            $('#emp_password').val(password).attr('readonly', true);

            $('#emp_status_toggle').prop('checked', true).trigger('change');
            $('#employeeModalTitle').text('Add New Employee');
            if (joiningDatePicker) {
                joiningDatePicker.clear();
            }
            // Reset Accordion to first section
            $('.collapse').collapse('hide');
            $('#collapsePersonal').collapse('show');

            $('#employeeModal').modal('show');
        }
        function generatePassword() {
            return Math.random().toString(36).slice(-8).toUpperCase();
        }
        function formatDate(dateString) {
            if (!dateString) return "-";
            let d = new Date(dateString);
            let day = ("0" + d.getDate()).slice(-2);
            let month = ("0" + (d.getMonth() + 1)).slice(-2);
            let year = d.getFullYear();
            return `${day}/${month}/${year}`;
        }
        // Submit Employee Form
        $('#employeeForm').on('submit', function (e) {
            e.preventDefault();

            let id = $('#employee_id').val();
            let url = id
                ? "{{ route('employees.update', [$company->id, ':id']) }}".replace(':id', id)
                : "{{ route('employees.store', $company->id) }}";

            let method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: {
                    _token: "{{ csrf_token() }}",

                    // personal
                    first_name: $('#emp_first_name').val(),
                    middle_name: $('#emp_middle_name').val(),
                    last_name: $('#emp_last_name').val(),
                    father_name: $('#emp_father_name').val(),
                    email: $('#emp_email').val(),
                    mobile: $('#emp_mobile').val(),
                    address: $('#emp_address').val(),
                    previous_company: $('#emp_previous_company').val(),
                    experience_years: $('#emp_experience_years').val(),
                    reference_name: $('#reference_name').val(),

                    // location
                    country_id: $('#emp_country').val(),
                    state_id: $('#emp_state').val(),
                    city_id: $('#emp_city').val(),
                    pincode: $('#emp_pincode').val(),

                    // office
                    department_id: $('#emp_department').val(),
                    joining_date: $('#emp_joining_date').val(),
                    user_id: $('#emp_user_id').val(),
                    password: $('#emp_password').val(),
                    status: $('#emp_status_toggle').is(':checked') ? 1 : 0,

                    // bank
                    bank_name: $('#emp_bank_name').val(),
                    account_no: $('#emp_account_no').val(),
                    account_holder: $('#emp_account_name').val(),
                    branch_name: $('#branch_name').val(),
                    ifsc_code: $('#emp_ifsc_code').val(),
                    pan: $('#emp_pan').val(),
                },
                success: function (res) {
                    $('#employeeModal').modal('hide');

                     Swal.fire({
        title: 'Success',
        text: res.message,
        icon: 'success'
    }).then(() => {
        location.reload(); // ✅ reload page
    });
                    if (!id) {
                        appendTableRow(res.employee);
                        $('#no-employee-row').remove();
                    } else {
                        updateTableRow(res.employee);
                    }
                }, error: function (xhr) {
                    console.error(xhr.responseText);
                    Swal.fire('Error', 'Something went wrong. Check console.', 'error');
                }

            });
        });


        function appendTableRow(emp) {
            let count = $('#employeeTable tr').length + 1;
            let row = `<tr id="employee-row-${emp.id}"><td>${count}</td><td>${emp.first_name} ${emp.last_name}</td><td>${emp.user_id}</td><td>${emp.department ? emp.department.name : ''}</td><td><span class="badge ${emp.status == true ? 'badge-success' : 'badge-danger'}">${emp.status ? 'Enabled' : 'Disabled'}</span></td><td>
                                                                        <button class="btn btn-sm edit-employee" data-id="${emp.id}"><i class="fa fa-edit text-green"></i></button>
                                                                        <button class="btn btn-sm delete-employee" data-id="${emp.id}"><i class="fa fa-trash text-red"></i></button></td>
                                                                        </tr>`;
            $('#employeeTable').append(row);
        }

        $(document).on('click', '.delete-employee', function () {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('employees.destroy', [$company->id, ':id']) }}".replace(':id', id),
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function () {
                            $(`#employee-row-${id}`).remove();
                            if ($('#employeeTable tr').length === 0) {
                                $('#employeeTable').html('<tr id="no-employee-row"><td colspan="6" class="text-center">😢 No employees found</td></tr>');
                            }
                        }
                    });
                }
            })
        });

        // Helper functions for loading States/Cities via AJAX
        function loadStates(countryId) {
            $.get("{{ route('getStates') }}", { country_id: countryId }, function (data) {
                let options = '<option value="">Select State</option>';
                data.forEach(s => options += `<option value="${s.id}">${s.name}</option>`);
                $('#emp_state').html(options).trigger('change');
            });

        }

        function loadCities(stateId) {
            $.get("{{ route('getCities') }}", { state_id: stateId }, function (data) {
                let options = '<option value="">Select City</option>';
                data.forEach(c => options += `<option value="${c.id}">${c.name}</option>`);
                $('#emp_city').html(options).trigger('change');
            });

        }
        $('#departmentForm').on('submit', function (e) {
            e.preventDefault();

            $.post("{{ route('departments.store', $company->id) }}", {
                _token: "{{ csrf_token() }}",
                name: $('#department_name').val()
            }, function (res) {

                let option = `<option value="${res.department.id}" selected>
                                                    ${res.department.name}
                                                    </option>`;

                $('#emp_department').append(option).trigger('change');

                $('#departmentModal').modal('hide');
                $('#departmentForm')[0].reset();
            });
        });
        $('#addCountryForm').on('submit', function (e) {
            e.preventDefault();

            $.post("{{ route('countries.store') }}", $(this).serialize(), function (country) {

                let option = `<option value="${country.id}">${country.name}</option>`;

                // employee modal
                $('#emp_country').append(option).val(country.id).trigger('change');

                // state modal
                $('#state_country').append(option).val(country.id).trigger('change');

                // city modal
                $('#city_country').append(option).val(country.id).trigger('change');

                $('#addCountryModal').modal('hide');
                $('#addCountryForm')[0].reset();
            });
        });

        $('#addStateForm').on('submit', function (e) {
            e.preventDefault();

            $.post("{{ route('states.store') }}", $(this).serialize(), function (state) {

                let option = `<option value="${state.id}">${state.name}</option>`;

                // employee modal state
                $('#emp_state').append(option).val(state.id).trigger('change');

                // city modal state
                $('#city_state').append(option).val(state.id).trigger('change');

                $('#addStateModal').modal('hide');
                $('#addStateForm')[0].reset();
            });
        });

        $('#addCityForm').on('submit', function (e) {
            e.preventDefault();

            $.post("{{ route('cities.store') }}", $(this).serialize(), function (city) {

                let option = `<option value="${city.id}">${city.name}</option>`;

                // employee modal city
                $('#emp_city').append(option).val(city.id).trigger('change');

                $('#addCityModal').modal('hide');
                $('#addCityForm')[0].reset();
            });
        });
        let isEditMode = false;

        $(document).on('click', '#togglePassword', function () {

            if (!isEditMode) {
                // CREATE MODE → normal toggle
                togglePasswordField();
                return;
            }

            // EDIT MODE → require admin password
            $('#password_employee_id').val($('#employee_id').val());
            $('#admin_password').val('');
            $('#adminPasswordModal').modal('show');
        });

        function togglePasswordField() {
            const input = $('#emp_password');
            const icon = $('#togglePassword i');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        }

        $(document).on('click', '.edit-employee', function () {
            const id = $(this).data('id');

            $.get(
                "{{ route('employees.show', [$company->id, ':id']) }}".replace(':id', id),
                function (emp) {

                    // set id
                    $('#employee_id').val(emp.id);

                    // personal
                    $('#emp_first_name').val(emp.first_name);
                    $('#emp_middle_name').val(emp.middle_name);
                    $('#emp_last_name').val(emp.last_name);
                    $('#emp_father_name').val(emp.father_name);
                    $('#emp_email').val(emp.email);
                    $('#emp_mobile').val(emp.mobile);
                    $('#emp_address').val(emp.address);
                    $('#emp_previous_company').val(emp.previous_company);
                    $('#emp_experience_years').val(emp.experience_years);
                    $('#reference_name').val(emp.reference_name);

                    // location
                    // country
                    $('#emp_country').val(emp.country_id).trigger('change');

                    // load states → then cities → then select
                    $.get("{{ route('getStates') }}", { country_id: emp.country_id }, function (states) {

                        let stateOptions = '<option value="">Select State</option>';
                        states.forEach(s => {
                            stateOptions += `<option value="${s.id}">${s.name}</option>`;
                        });

                        $('#emp_state').html(stateOptions).val(emp.state_id).trigger('change');

                        $.get("{{ route('getCities') }}", { state_id: emp.state_id }, function (cities) {

                            let cityOptions = '<option value="">Select City</option>';
                            cities.forEach(c => {
                                cityOptions += `<option value="${c.id}">${c.name}</option>`;
                            });

                            $('#emp_city').html(cityOptions).val(emp.city_id).trigger('change');
                        });
                    });

                    $('#emp_pincode').val(emp.pincode);

                    // office
                    $('#emp_department').val(emp.department_id).trigger('change');
                    $('#emp_user_id').val(emp.user_id);

                    if (joiningDatePicker && emp.joining_date) {
                        joiningDatePicker.setDate(emp.joining_date);
                    }

                    $('#emp_status_toggle')
                        .prop('checked', emp.status)
                        .trigger('change');

                    // password (do not reveal actual password)
                    $('#emp_password').val('********');

                    // modal title
                    $('#employeeModalTitle').text('Edit Employee');
                    isEditMode = true;
                    // open modal
                    $('#employeeModal').modal('show');
                }
            );
        });

        $('#confirmAdminPassword').on('click', function () {

            $.post("{{ route('employees.revealPassword', $company->id) }}", {
                _token: "{{ csrf_token() }}",
                admin_password: $('#admin_password').val(),
                employee_id: $('#password_employee_id').val()
            })
                .done(res => {
                    $('#adminPasswordModal').modal('hide');
                    $('#emp_password').val(res.password).attr('type', 'text');
                    $('#togglePassword i').removeClass('fa-eye').addClass('fa-eye-slash');
                })
                .fail(() => {
                    Swal.fire('Error', 'Admin password incorrect', 'error');
                });
        });

        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
        function updateTableRow(emp) {
            let row = $(`#employee-row-${emp.id}`);

            row.find('.emp-name').text(emp.first_name + ' ' + emp.last_name);
            row.find('.emp-userid').text(emp.user_id);
            row.find('.emp-dept').text(emp.department ? emp.department.name : '');

            let statusBadge = emp.status
                ? '<span class="badge badge-success">Enabled</span>'
                : '<span class="badge badge-danger">Disabled</span>';

            row.find('.emp-status').html(statusBadge);
        }
        $(document).on('click', '.toggle-status', function () {

            let badge = $(this);
            let employeeId = badge.data('id');

            $.ajax({
              url: "{{ route('employees.toggle.status',
['company'=>$company->id,'employee'=>'EMPLOYEE_ID']) }}"
.replace('EMPLOYEE_ID', employeeId),
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },

                success: function (res) {

                    if (res.status) {

                        badge
                            .removeClass('badge-danger')
                            .addClass('badge-success')
                            .text('Enabled');

                    } else {

                        badge
                            .removeClass('badge-success')
                            .addClass('badge-danger')
                            .text('Disabled');

                    }

                }

            });

        });
    </script>
@endpush