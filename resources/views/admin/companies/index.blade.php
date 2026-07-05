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
                        <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">List</a></li>
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
                                @can('add company')
                                    <a href="{{ route('companies.create') }}">
                                        <button class="btn btn-default btn-sm">
                                            <i class="fa fa-plus"></i> Add Company
                                        </button>
                                    </a>
                                @endcan
                                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @php $i = 1; @endphp
                                @foreach ($companies as $company)
                                    <div class="col-md-4 mb-4">
                                        <div class="card shadow-sm position-relative h-100 p-1 bg-gray-light">

                                            {{-- IMAGE BOX --}}
                                            <div class="position-relative" style="height: 200px;">
                                                <img src="{{ asset('admin/uploads/company.jfif') }}" class="card-img-top"
                                                    alt="Company Image"
                                                    style="width: 100%; height: auto; max-height: 200px; object-fit: cover;">
                                                {{-- ACTION BUTTONS --}}
                                                @can('manage company status')
                                                    <div class="position-absolute" style="top: 10px; left: 0px; z-index: 10;">
                                                        <div class="d-flex flex-column align-items-end" style="gap:8px;">
                                                            <button onclick="toggleCompanyStatus('{{ $company->id }}')"
                                                                type="button"
                                                                class="btn btn-sm btn-light shadow border rounded-circle d-flex align-items-center justify-content-center"
                                                                title="Change Status" style="width: 36px; height: 36px;">

                                                                <i id="company_icon_{{ $company->id }}"
                                                                    class="fa fa-power-off {{ $company->status ? 'text-success' : 'text-danger' }}"></i>
                                                            </button>

                                                        </div>

                                                    </div>
                                                @endcan
                                                @can('manage company status')

                                                    <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                                                        <div class="d-flex flex-column align-items-end" style="gap:8px;">

                                                            {{-- Edit --}}
                                                            <a href="{{ route('companies.edit', $company->id) }}"
                                                                class="btn btn-sm btn-light shadow border rounded-circle d-flex align-items-center justify-content-center"
                                                                style="width: 36px; height: 36px;" title="Edit Company">
                                                                <i class="fa fa-edit text-success"></i>
                                                            </a>

                                                        </div>
                                                    </div>
                                                @endcan
                                            </div>

                                            {{-- CARD BODY --}}
                                            <div class="card-body d-flex flex-column">

                                                <h5 class="card-title mb-1">
                                                    {{ $company->company_name }}
                                                </h5>

                                                <p class="card-text text-muted mb-1">
                                                    <strong>Email:</strong> {{ $company->email }}
                                                </p>

                                                <p class="card-text text-muted mb-1">
                                                    <strong>Mobile:</strong> {{ $company->full_mobile }}
                                                </p>
                                                <p class="card-text text-muted mb-1">
                                                    <strong>Location:</strong>
                                                    {{ $company->address }}
                                                <div class="d-flex flex-wrap mt-auto">
                                                    <!-- @can('delete company')
                                                                                                        <div class="mr-2 mb-2" style="width: 48%;">
                                                                                                            <form action="{{ route('companies.destroy', $company->id) }}"
                                                                                                                method="POST">
                                                                                                                @csrf
                                                                                                                @method('DELETE')

                                                                                                                <button type="submit" class="btn btn-danger w-100 delete-confirm">
                                                                                                                    Delete Company
                                                                                                                </button>
                                                                                                            </form>
                                                                                                        </div>
                                                                                                    @endcan -->

                                                    @can('enter company')
                                                        <div class="mr-2 mb-2" style="width: 48%;">

                                                            @if($company->status == 1)
                                                                {{-- Active Company → Allow Enter --}}
                                                                <a title="Company Dashboard"
                                                                    href="{{ route('company.dashboard', $company->id) }}"
                                                                    class="btn btn-success w-100">
                                                                    Enter Company
                                                                </a>
                                                            @else
                                                                {{-- Inactive Company → Disabled --}}
                                                                <button class="btn btn-secondary w-100" disabled
                                                                    title="This company is inactive.">
                                                                    Enter Company
                                                                </button>
                                                            @endif

                                                        </div>
                                                    @endcan



                                                    @can('assign users')
                                                        <div class="mb-2" style="width: 48%;">
                                                            <a title="Assign User To Company"
                                                                href="{{ route('company.assignUsers', $company->id) }}"
                                                                class="btn btn-info w-100 text-center">
                                                                Assign Users
                                                            </a>
                                                        </div>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @php $i++; @endphp
                                @endforeach
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
@push('scripts')
    <script>

        function toggleCompanyStatus(id) {

            $.ajax({
                url: "{{ route('company.changeStatus') }}",
                method: "POST",
                data: {
                    company_id: id,
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {

                    let badge = $("#company_status_" + id);
                    let icon = $("#company_icon_" + id);

                    if (res.newFlag == 1) {
                        // Update badge
                        badge.removeClass('bg-danger').addClass('bg-success').text('ON');

                        // Update icon color
                        icon.removeClass('text-danger').addClass('text-success');

                        Swal.fire("Activated!", "Company is now active.", "success");
                    } else {
                        // Update badge
                        badge.removeClass('bg-success').addClass('bg-danger').text('OFF');

                        // Update icon color
                        icon.removeClass('text-success').addClass('text-danger');

                        Swal.fire("Deactivated!", "Company is now inactive.", "warning");
                    }
                }
            });
        }
        $(document).on('click', '.delete-confirm', function (e) {
            e.preventDefault();
            const $el = $(this);

            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete . This action cannot be undone.`,
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
@endpush