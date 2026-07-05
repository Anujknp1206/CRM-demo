@extends('admin.layouts.master')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Machines</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Machines</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card card-teal">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{$label}}</h3>
                    <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                        @can('add machines')
                            <a href="{{ route('machines.create') }}">
                                <button class="btn btn-default btn-sm ">
                                    <i class="fa fa-plus"></i> Add Machine
                                </button>
                            </a>
                        @endcan
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-success">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="example1">
                        <thead>
                            <tr>
                                <th>S.N.</th>
                                <th>Name (English)</th>
                                <th>Name(Hindi)</th>
                                <th>MOC</th>
                                <th>Size</th>
                                <th>Code</th>
                                <th>Origin</th>
                                <th>Description(English)</th>
                                <th>Description(Hindi)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @foreach ($machines as $machine)
                                <tr>
                                    <td>{{ $i++ ?? '-----'}}</td>
                                    <td>{{ $machine->name ?? '-----' }}</td>
                                    <td>{{ $machine->hi_name ?? '-----' }}</td>
                                    <td>{{ $machine->moc ?? '-----' }}</td>
                                    <td>{{ $machine->size ?? '-----' }}</td>
                                    <td>{{ $machine->code ?? '-----' }}</td>
                                    <td>{{ $machine->origin ?? '-----' }}</td>
                                    <td>{!! $machine->description ?? '-----' !!}</td>
                                    <td>{!! $machine->hi_description ?? '-----' !!}</td>

                                    <td>
                                        @can('edit machines')
                                            <a title="Edit Machine" href="{{ route('machines.edit', $machine->id) }}">
                                                <i class="fa fa-edit text-green"></i>
                                            </a>
                                        @endcan
                                        @can('delete machines')
                                            <form action="{{ route('machines.destroy', $machine->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button title="Delete Machine" type="submit" class="delete-confirm"
                                                    style="border: none; background: none;">
                                                    <i class="fa fa-trash text-red"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                    </table>
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