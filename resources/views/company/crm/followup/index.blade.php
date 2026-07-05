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
                        <li class="breadcrumb-item active"><a
                                href="{{ route('leads.index', ['company' => $company->id]) }}"> Lead List</a></li>
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
                            <h3 class="card-title">{{ $label }} - {{ $lead->customerName }}</h3>
                            <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                                @can('add followup')
                                    @if($lead->status == 'new')
                                        <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#followupModal">
                                            <i class="fa fa-plus"></i> Add Followup
                                        </button>
                                    @endif
                                @endcan
                                <a href="{{ route('leads.index', ['company' => $company->id]) }}"
                                    class="btn btn-sm btn-success">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>S.N.</th>
                                            <th>Next Action Date</th>
                                            <th>Action</th>
                                            <th>Description</th>
                                            <th>Managed By</th>
                                            <th>Operations</th>
                                        </tr>
                                    </thead>
                                    <tbody id="followupTableBody">
                                        @php $i = 1; @endphp
                                        @foreach ($lead->followups as $f)
                                                                        <tr>
                                                                            <td>{{ $i++ ?? '----'}}</td>
                                                                            <td>{{ $f->nextactionDate? $f->nextactionDate->format('d/m/Y'): 'No Followup Needed'}}</td>
                                                                            <td>{{ $f->action->name ?? '----' }}</td>
                                                                            <td>{{ $f->describeAction ?? '----'}}</td>
                                                                            <td>{{ $f->manager->name ?? '----' }}</td>
                                                                            <td>
                                                                                @can('edit followup')
                                                                                    <a href="javascript:void(0)" class="edit-followup" data-id="{{ $f->id }}"
                                                                                        data-date="{{ $f->nextactionDate }}" data-action="{{ $f->action_id }}"
                                                                                        data-desc="{{ $f->describeAction }}">
                                                                                        <i class="fa fa-edit text-green"></i>
                                                                                    </a>
                                                                                @endcan
                                                                                @can('delete followup')
                                                                                    <form
                                                                                        action="{{ route('followups.destroy', ['followup' => $f->id, 'company' => $company->id]) }}"
                                                                                        method="POST" style="display:inline;">
                                                                                        @csrf
                                                                                        @method('DELETE')
                                                                                        <button type="submit" class="delete-confirm"
                                                                                            style="background:none;border:none;">
                                                                                            <i class="fa fa-trash text-red ml-2"></i>
                                                                                        </button>
                                                                                    </form>
                                                                                @endcan
                                                                            </td>
                                                                        </tr>
                                        @endforeach
                                    </tbody>

                                </table>
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
                    <h5 class="modal-title">Add Follow Up - {{ $lead->customerName }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <form id="followupForm" action="{{ route('followups.store', ['company' => $company->id]) }}" method="POST"
                    autocomplete="off">
                    @csrf

                    <div class="modal-body">

                        {{-- Previous Action --}}
                        <div class="alert alert-info"
                            style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
                            @if($lead->latestFollowup)
                                <strong>Last Action : </strong> {{ $lead->latestFollowup->action->name ?? 'N/A' }} <br>
                                <strong>Next Action Date :
                                </strong>{{ $lead->latestFollowup->nextactionDate?->format('d/m/Y') }}
                            @else
                                No previous follow-up found.
                            @endif
                        </div>

                        <input type="hidden" name="lead_id" value="{{ $lead->id }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Next Action Date *</label>

                                    @php
                                        $minDate = $lead->latestFollowup
                                            ? \Carbon\Carbon::parse($lead->latestFollowup->nextactionDate)->format('Y-m-d')
                                            : $lead->created_at->format('Y-m-d');
                                    @endphp

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
    <div class="modal fade" id="editFollowupModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-teal"
                    style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
                    <h5 class="modal-title">Edit Follow
                        Up</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <form id="editFollowupForm" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="edit_followup_id">

                    <div class="modal-body">

                        <div class="row">

                            <div class="col-md-6">
                                <label>Next Action Date *</label>


                                <input type="text" name="nextactionDate" id="nextactionDate" class="form-control"
                                    id="edit_nextactionDate" placeholder="DD/MM/YYYY" disabled>
                            </div>

                            <div class="col-md-6">
                                <label>Select Action *</label>
                                <select name="selectAction" id="edit_selectAction" class="form-control" required>
                                    @foreach ($actions as $act)
                                        <option value="{{ $act->id }}">{{ $act->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 mt-2">
                                <label>Description</label>
                                <textarea name="describeAction" id="edit_describeAction" class="form-control"
                                    rows="4"></textarea>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            Update Follow Up
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

@endsection
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const nextActionPicker = flatpickr("#nextactionDate", {
            displayFormat: "d/m/Y", // user sees
            dateFormat: "Y-m-d",    // backend receives
            minDate: "{{ $minDate }}",
            allowInput: false
        });

        // Open calendar when icon is clicked
        document.getElementById('calendarIcon').addEventListener('click', function () {
            nextActionPicker.open();
        });
    </script>



    <script>
        const SweetToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    </script>
    <script>
        $(document).ready(function () {

            $(document).on('submit', '#followupForm', function (e) {
                e.preventDefault();

                let form = $(this);
                let btn = form.find('button[type="submit"]');

                btn.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: form.attr('action'),
                    type: "POST",
                    data: form.serialize(),
                    success: function (response) {



                        if (!response.status) {
                            toastr.error('Failed to save follow-up');
                            btn.prop('disabled', false).text('Save Follow Up');
                            return;
                        }

                        SweetToast.fire({
                            icon: 'success',
                            title: response.message
                        });

                        let f = response.data;

                        let rowCount = $('#followupTableBody tr').length + 1;

                        let row = `
                                                                                                                                                                <tr>
                                                                                                                                                                    <td>${rowCount}</td>
                                                                                                                                                                    <td>
                                                                                                    {{ $f->nextactionDate
        ? $f->nextactionDate->format('d/m/Y')
        : 'No Followup Needed'
                                                                                                                                                                                                                                                                                                                                    }}
                                                                                                </td>
                                                                                                                                                                    <td>${f.action}</td>
                                                                                                                                                                    <td>${f.description}</td>
                                                                                                                                                                    <td>${f.manager}</td>
                                                                                                                                                                    <td>
                                                                                                    @can('edit followup')
                                                                                                        <a href="javascript:void(0)" class="edit-followup" data-id="{{ $f->id }}"
                                                                                                            data-date="{{ $f->nextactionDate }}" data-action="{{ $f->action_id }}"
                                                                                                            data-desc="{{ $f->describeAction }}">
                                                                                                            <i class="fa fa-edit text-green"></i>
                                                                                                        </a>

                                                                                                    @endcan
                                                                                                    @can('delete followup')
                                                                                                        <form
                                                                                                            action="{{ route('followups.destroy', ['followup' => $f->id, 'company' => $company->id]) }}"
                                                                                                            method="POST" style="display:inline;">
                                                                                                            @csrf
                                                                                                            @method('DELETE')
                                                                                                            <button type="submit" class="delete-confirm"
                                                                                                                style="background:none;border:none;">
                                                                                                                <i class="fa fa-trash text-red ml-2"></i>
                                                                                                            </button>
                                                                                                        </form>
                                                                                                    @endcan
                                                                                                </td>
                                                                                                                                                                </tr>
                                                                                                                                                            `;

                        $('#followupTableBody').prepend(row);

                        form[0].reset();
                        $('#followupModal').modal('hide');

                        btn.prop('disabled', false).text('Save Follow Up');
                        location.reload();
                    },
                    error: function (xhr) {

                        console.error(xhr); // DEBUG

                        btn.prop('disabled', false).text('Save Follow Up');

                        if (xhr.status === 422) {
                            let msg = '';
                            $.each(xhr.responseJSON.errors, function (key, val) {
                                msg += val[0] + '<br>';
                            });
                            SweetToast.fire({
                                icon: 'error',
                                html: msg
                            });
                        } else {
                            SweetToast.fire({
                                icon: 'error',
                                title: 'Something went wrong. Please try again.'
                            });
                        }
                    }
                });
            });

        });
    </script>
    <script>
        $(document).on('click', '.edit-followup', function () {

            $('#edit_followup_id').val($(this).data('id'));
            $('#edit_nextactionDate').val($(this).data('date'));
            $('#edit_selectAction').val($(this).data('action'));
            $('#edit_describeAction').val($(this).data('desc'));

            $('#editFollowupModal').modal('show');
        });
    </script>
    <script>
        $('#editFollowupForm').on('submit', function (e) {
            e.preventDefault();

            let id = $('#edit_followup_id').val();

            $.ajax({
                url: "{{ url('company/' . $company->id . '/followups') }}/" + id,
                method: "POST",
                data: $(this).serialize(),
                success: function (response) {

                    SweetToast.fire({
                        icon: 'success',
                        title: response.message
                    });

                    let f = response.data;

                    let row = $('a[data-id="' + id + '"]').closest('tr');

                    row.find('td:eq(1)').text(f.nextactionDate);
                    row.find('td:eq(2)').text(f.action);
                    row.find('td:eq(3)').text(f.description);
                    row.find('td:eq(4)').text(f.manager);

                    $('#editFollowupModal').modal('hide');
                },
                error: function (xhr) {
                    SweetToast.fire({
                        icon: 'error',
                        title: 'Failed to update follow-up'
                    });
                }
            });
        });
    </script>

@endpush