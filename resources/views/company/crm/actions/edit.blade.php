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
                        <li class="breadcrumb-item"><a
                                href="{{ route('actions.index', ['company' => $company->id]) }}">List</a></li>
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
                            <div class="d-flex gap-2 ml-auto">
                                <a href="{{ route('actions.index', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form
                                action="{{ route('actions.update', ['company' => $company->id, 'action' => $action->id]) }}"
                                method="POST" autocomplete="off">
                                @csrf
                                @method('PUT')

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name:</label>
                                            <input type="text" name="name" value="{{ $action->name }}" class="form-control"
                                                required>
                                        </div>
                                    </div>

                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success">
                                                Update Action
                                            </button>
                                        </div>

                                    </div>

                                </div>

                            </form>
                        </div>


                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection