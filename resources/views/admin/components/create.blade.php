@extends('admin.layouts.master')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Add Component</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('components.index') }}">Components</a></li>
                        <li class="breadcrumb-item active">Add</li>
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
                    <div class="d-flex gap-2 ml-auto">
                        <a href="{{ route('components.index') }}" class="btn btn-sm btn-success">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('components.store') }}" method="POST" autocomplete="off">
                        @csrf

                        <div class="row">

                            <div class="col-md-4">
                                <label>Name of Component(English)</label>
                                <input type="text" name="name" placeholder="Enter Component Name In English"
                                    class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Name of Component(Hindi)</label>
                                <input type="text" name="hi_name" placeholder="Enter Component Name In Hindi"
                                    class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label>MOC Details</label>
                                <input type="text" name="moc" placeholder="Enter MOC Details" class="form-control" required>
                            </div>

                            <div class="col-md-4 mt-3">
                                <label>Size</label>
                                <input type="text" name="size" placeholder="Enter Component Size" class="form-control"
                                    required>
                            </div>

                            <div class="col-md-4 mt-3">
                                <label>Code (Auto Generated)</label>
                                <input type="text" name="code" class="form-control" readonly>
                            </div>

                            <div class="col-md-4 mt-3">
                                <label>Origin</label>
                                <select name="origin" class="form-control" required>
                                    <option value="">Select Origin</option>
                                    <option value="Self">Self</option>
                                    <option value="Outsource">Outsource</option>
                                </select>
                            </div>

                            <div class="col-md-6 mt-3">
                                <label>Description(English)</label>
                                <textarea name="description" placeholder="Enter Component Description In English"
                                    class="form-control summernote"></textarea>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label>Description(Hindi)</label>
                                <textarea name="hi_ description" placeholder="Enter Component Description In Hindi"
                                    class="form-control summernote"></textarea>
                            </div>

                        </div>

                        <button class="btn btn-success mt-3">Submit</button>

                    </form>
                </div>
            </div>

        </div>
    </section>

@endsection