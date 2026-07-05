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
                                @can('add action')
                                    <a href="{{ route('actions.create', ['company' => $company->id]) }}">
                                        <button class="btn btn-default btn-sm">
                                            <i class="fa fa-plus"></i> Add Action
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
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S.N.</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php $i = 1; @endphp
                                    @foreach ($actions as $action)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $action->name }}</td>

                                            <td>
                                                @can('edit action')
                                                    <a
                                                        href="{{ route('actions.edit', ['company' => $company->id, 'action' => $action->id]) }}">
                                                        <i class="fa fa-edit text-green" title="Edit Action"></i>
                                                    </a>
                                                @endcan
                                                @can('delete action')
                                                    <form
                                                        action="{{ route('actions.destroy', ['company' => $company->id, 'action' => $action->id]) }}"
                                                        method="POST" class="delete-form" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" style="background:none; border:none;"
                                                            class="delete-confirm" title="Delete Action">
                                                            <i class="fa fa-trash text-red"></i>
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
    </section>

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