@extends('admin.layouts.master')

@section('content')

    {{-- PAGE HEADER --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Assign Users</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">Companies</a></li>
                        <li class="breadcrumb-item active">Assign Users</li>
                    </ol>
                </div>

            </div>
        </div>
    </section>


    {{-- MAIN CONTENT --}}
    <section class="content">
        <div class="container-fluid">

            {{-- CARD --}}
            <div class="card card-teal">

                {{-- HEADER --}}

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        Assign Users to: <strong>{{ $company->company_name }}</strong>
                    </h3>
                    <div class="d-flex gap-2 ml-auto">
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-success">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                {{-- BODY --}}
                <div class="card-body">

                    {{-- ASSIGN USERS FORM --}}
                    <form action="{{ route('company.assignUsers.store', $company->id) }}" method="POST" autocomplete="off">
                        @csrf

                        <div class="form-group">
                            <label><strong>Select Users</strong></label>
                            <select name="user_ids[]" class="form-control select2" multiple required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $company->users->contains($user->id) ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success mt-3">Save Assignment</button>
                    </form>

                    <hr>

                    {{-- ASSIGNED USERS TABLE --}}
                    <h4>Assigned Users List</h4>

                    <table class="table table-bordered table-striped mt-3">
                        <thead>
                            <tr>
                                <th>S.N.</th>
                                <th>User</th>
                                <th>Email</th>
                                <th width="90">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php $i = 1; @endphp
                            @foreach($company->users as $user)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>

                                    <td>
                                        @can('remove users')
                                            <form action="{{ route('company.removeUser', [$company->id, $user->id]) }}"
                                                method="POST" style="display:inline;" autocomplete="off">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="delete-confirm" style="background: none; border: none;"
                                                    title="Remove User">
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
    </section>

@endsection

@push('scripts')
    <script>
        $(function () {
            $('.select2').select2();
        });
    </script>
@endpush