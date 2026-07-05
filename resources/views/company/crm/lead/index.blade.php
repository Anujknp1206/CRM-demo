@extends('company.layouts.master')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
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
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-teal">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">{{$label}}</h3>
                            <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                                @can('leads')
                                    <a href="{{ route('leads.create', ['company' => $company->id]) }}">
                                        <button class="btn btn-default btn-sm">
                                            <i class="fa fa-plus"></i> Add Lead
                                        </button>
                                    </a>
                                @endcan
                                <a href="{{ route('company.dashboard', ['company' => $company->id]) }}"
                                    class="btn btn-sm btn-success">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 align-items-end">

                                <!-- From Date -->
                                <div class="col-md-3">
                                    <label class="form-label text-muted">From Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        <input type="date" id="from_date" class="form-control shadow-sm"
                                            placeholder="DD/MM/YYYY">
                                    </div>
                                </div>

                                <!-- To Date -->
                                <div class="col-md-3">
                                    <label class="form-label text-muted">To Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        <input type="date" id="to_date" class="form-control shadow-sm"
                                            placeholder="DD/MM/YYYY">
                                    </div>
                                </div>

                                <!-- Search -->
                                <div class="col-md-3">
                                    <label>Search Lead</label>
                                    <input type="text" id="lead_search" class="form-control"
                                        placeholder="Search Name / Mobile Number">
                                </div>

                                <!-- Buttons -->
                                <div class="col-md-3 d-flex gap-2 mt-4">
                                    <button id="filter" class="btn btn-success w-50 shadow-sm">
                                        <i class="fa fa-filter"></i> Search
                                    </button>
                                    <button id="reset" class="btn btn-secondary w-50 shadow-sm">
                                        <i class="fa fa-undo"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div id="loader" style="display:none; text-align:center; padding:20px;">
                                <i class="fa fa-spinner fa-spin" style="font-size:28px; color:#17a2b8;"></i>
                                <p>Loading data...</p>
                            </div>
                            <div class="table-responsive">

                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date Created</th>
                                                <th>Lead Code</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Email</th>
                                                <th>Address</th>
                                                <th>Purpose</th>
                                                <th>Last Action</th>
                                                <th>Next Action Date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="leadrows">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="followupModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header bg-teal"
                    style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
                    <h5 class="modal-title">
                        Add Follow Up - <span id="followupCustomerName">—</span>
                    </h5>


                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <form id="followupForm" action="{{ route('followups.store', ['company' => $company->id]) }}" method="POST"
                    autocomplete="off">
                    @csrf

                    <input type="hidden" name="lead_id" id="followup_lead_id">
                    <div class="modal-body">

                        {{-- Previous Action --}}
                        <div class="alert alert-info"
                            style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
                            <div class="alert alert-info" id="previousFollowupBox"
                                style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
                                Loading previous follow-up...
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Next Action Date *</label>

                                    <div class="input-group">
                                        <input type="text" id="nextactionDate" name="nextactionDate" class="form-control"
                                            placeholder="DD/MM/YYYY" required>

                                        <div class="input-group-append">
                                            <span class="input-group-text" id="calendarIcon" style="cursor:pointer;">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <small class="text-danger">Date can't be editable further.</small>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Select Action *</label>
                                    <select name="selectAction" class="form-control" required>
                                        @foreach ($actions as $act)
                                            <option value="{{ $act->id }}">{{ $act->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="describeAction" class="form-control" rows="4"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            Save Follow Up
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
@push('styles')
    <style>
        .gap-2 {
            gap: 10px;
        }

        .card-body {
            padding: 10px 5px !important;
        }

        .btn-primary,
        .btn-secondary {
            border-radius: 6px;
            height: 40px;
        }

        table td {
            white-space: normal !important;
            word-break: break-word;
            max-width: 200px;
        }
    </style>
    <!-- Daterange picker -->
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
    <script src="{{url('/')}}/admin/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script>
        function initDatePicker(selector) {

            // 🔴 FORCE remove native date behavior
            const input = document.querySelector(selector);
            input.type = "text";

            const picker = flatpickr(input, {
                dateFormat: "Y-m-d",   // backend
                altInput: true,        // UI input
                altFormat: "d/m/Y",    // visible format
                allowInput: true,
                clickOpens: true
            });

            // 🟢 Open calendar on icon click
            input.closest('.input-group')
                .querySelector('.input-group-text')
                .addEventListener('click', () => picker.open());

            return picker;
        }

        const fromPicker = initDatePicker("#from_date");
        const toPicker = initDatePicker("#to_date");
    </script>
    <script>
        $(document).ready(function () {

            // --- FIX DATE INPUTS (Clears internal value also) ---
            $('#from_date, #to_date').on("change keyup input", function () {
                if (!this.value || this.value === "") {
                    $(this).val(null);
                }
            });

            loadLeads();
        });

        function loadLeads() {
            let params = {};

            let search = $('#lead_search').val().trim();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            // SEND ONLY NON-EMPTY VALUES
            if (search !== "") params.search = search;
            if (from_date !== "") params.from_date = from_date;
            if (to_date !== "") params.to_date = to_date;

            // SHOW LOADER
            $("#loader").show();
            $("#example1").hide();
            $.ajax({
                url: "{{ route('leads.data', ['company' => $company->id]) }}",
                type: "GET",
                data: params,

                success: function (response) {

                    if ($.fn.DataTable.isDataTable('#example1')) {
                        let dt = $('#example1').DataTable();
                        if ($.isFunction(dt.destroy)) {
                            dt.clear().draw(false);
                            dt.destroy();
                        }
                    }
                    $('#leadrows').html(response);
                    if ($("#leadrows").find("tr").length === 0 || $("#leadrows").find("tr td").length === 1) {
                        $("#loader").hide();
                        $("#example1").show();
                        return;
                    }
                    $('#example1').DataTable({
                        responsive: true,
                        autoWidth: false,
                        lengthChange: false,
                        paging: false,
                        searching: true,
                        ordering: false,
                        info: false,

                        dom: '<"d-flex justify-content-between align-items-center"Bf>rt',

                        buttons: [
                            {
                                extend: 'colvis',
                                text: 'Column visibility'
                            }
                        ]
                    });

                    // HIDE LOADER & SHOW TABLE
                    $("#loader").hide();
                    $("#example1").show();
                },
                error: function () {
                    $("#loader").hide();
                    $("#example1").show();
                }

            });
        }
        let typingTimer;

        $('#lead_search').on('keyup', function () {

            clearTimeout(typingTimer);

            typingTimer = setTimeout(function () {
                loadLeads();
            }, 500); // wait 500ms after typing stops
        });


        // Filter Button
        $('#filter').click(function () {
            loadLeads();
        });

        // Reset Button
        $('#reset').click(function () {
            $('#lead_search').val(null).trigger('change');
            $('#from_date').val(null); fromPicker.clear();
            toPicker.clear();
            $('#to_date').val(null);

            loadLeads();
        });
    </script>

    <script>
        $(document).on('click', '.delete-confirm', function (e) {
            e.preventDefault();
            const $el = $(this);
            const itemName = $el.data('name') || 'this item';

            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete ${itemName}. This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
                focusCancel: true,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const $form = $el.closest('form');
                    if ($form.length) {
                        $form.trigger('submit');
                        return;
                    }
                    const href = $el.attr('href');
                    if (href) window.location.href = href;
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Cancelled',
                        text: 'Your item is safe.',
                        icon: 'info',
                        timer: 1400,
                        showConfirmButton: false
                    });
                }
            });
        });
    </script>
    <script> $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
    </script>
    <script>
        let followupPicker;

        $(document).on('click', '.open-followup-modal', function () {

            const leadId = $(this).data('lead-id');
            const customer = $(this).data('customer');

            $('#followup_lead_id').val(leadId);
            $('#followupCustomerName').text(customer);
            $('#previousFollowupBox').html('Loading previous follow-up...');

            $.get(
                "{{ url('company/' . $company->id . '/followups/lead') }}/" + leadId,
                function (res) {

                    // Previous follow-up info
                    if (res.last_action) {
                        $('#previousFollowupBox').html(`
                                                                                                <strong>Last Action :</strong> ${res.last_action}<br>
                                                                                                <strong>Next Action Date :</strong> ${res.next_date}
                                                                                            `);
                    } else {
                        $('#previousFollowupBox').html('No previous follow-up found.');
                    }

                    // 🔥 SET MIN DATE DYNAMICALLY
                    if (!followupPicker) {
                        followupPicker = flatpickr('#nextactionDate', {
                            dateFormat: 'Y-m-d',
                            altInput: true,
                            altFormat: 'd/m/Y',
                            minDate: res.min_date
                        });
                    } else {
                        followupPicker.set('minDate', res.min_date);
                        followupPicker.clear();
                    }
                }
            );

            $('#followupModal').modal('show');
        });

    </script>
    <script>
        $('#followupForm').on('submit', function (e) {
            e.preventDefault(); // 🚫 STOP normal submit

            const form = $(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function (res) {

                    if (res.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        });

                        // Close modal
                        $('#followupModal').modal('hide');

                        // Reset form
                        form.trigger('reset');

                        // 🔄 Reload leads so Last Action / Next Date updates
                        loadLeads();
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message ?? 'Something went wrong'
                    });
                }
            });
        });
    </script>

@endpush