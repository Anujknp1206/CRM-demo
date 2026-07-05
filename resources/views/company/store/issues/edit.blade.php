@extends('company.layouts.master')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>{{ $label }}</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">

                        <li class="breadcrumb-item">
                            <a href="{{ route('company.dashboard', $company) }}">
                                Dashboard
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ route('issues.index', $company) }}">
                                Issue List
                            </a>
                        </li>

                        <li class="breadcrumb-item active">
                            {{ $label }}
                        </li>

                    </ol>
                </div>

            </div>
        </div>
    </section>


    <section class="content">

        <div class="card card-teal">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">{{ $label }}</h3>

                <div class="ml-auto">

                    <a href="{{ route('issues.index', $company) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>


            <!-- UPDATE ROUTE -->
            <form id="issueForm" method="POST" action="{{ route('issues.update', [$company->id, $issue->id]) }}">

                @csrf
                @method('PUT')

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-4">
                            <label>Issue No</label>
                            <input type="text" name="issue_no" class="form-control" value="{{ $issue->issue_no }}" readonly>
                        </div>


                        <div class="col-md-4">
                            <label>Issue Date</label>

                            <div class="input-group">
                                <input type="text" id="issue_date" name="issue_date" class="form-control"
                                    value="{{ $issue->issue_date }}" readonly>

                                <button type="button" class="btn btn-outline-secondary date-trigger">
                                    <i class="fa fa-calendar"></i>
                                </button>
                            </div>

                        </div>


                        <div class="col-md-4">
                            <label>Issue Time</label>

                            <div class="input-group">
                                <input type="text" id="issue_time" name="issue_time" class="form-control"
                                    value="{{ $issue->issue_time }}" readonly>

                                <button type="button" class="btn btn-outline-secondary time-trigger">
                                    <i class="fa fa-clock"></i>
                                </button>
                            </div>

                        </div>

                    </div>


                    <hr>


                    <div class="row">

                        <div class="col-md-3">
                            <label>Employee</label>

                            <select name="employee_id" id="employee_id" class="form-control">

                                <option value="{{ $issue->employee_id }}" selected>
                                    {{ optional($issue->employee)->first_name }}
                                    {{ optional($issue->employee)->middle_name }}
                                    {{ optional($issue->employee)->last_name }}
                                </option>

                            </select>
                        </div>


                        <div class="col-md-3">
                            <label>Department</label>

                            <input type="text" id="department_name" class="form-control"
                                value="{{ optional($issue->department)->name }}" readonly>

                            <input type="hidden" name="department_id" id="department_id"
                                value="{{ $issue->department_id }}">
                        </div>


                        <div class="col-md-3">
                            <label>Order ID</label>

                            <input type="text" id="order_id" class="form-control"
                                value="{{ optional($issue->bom->order)->order_number }}" readonly>
                        </div>


                        <div class="col-md-3">
                            <label>BOM</label>

                            <select id="bom_id" class="form-control" disabled>

                                <option selected>
                                    {{ optional($issue->bom)->bom_number }}
                                </option>

                            </select>

                            <input type="hidden" name="bom_id" value="{{ $issue->bom_id }}">
                        </div>

                    </div>


                    <hr>

                    <h5>Issue Items</h5>

                    <div class="table-responsive">

                        <table class="table table-bordered">

                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Brand</th>
                                    <th>Condition</th>
                                    <th>Unit</th>
                                    <th>Location</th>
                                    <th>Requested Quantity</th>
                                    <th>Present Stock</th>
                                    <th>Issue Qty</th>
                                </tr>
                            </thead>

                            <tbody id="issueItemsBody">

                                @foreach($issue->items as $row)

                                    <tr>

                                        <td>
                                            {{ optional($row->item)->name }}

                                            <input type="hidden" name="bom_item_id[]" value="{{ $row->bom_item_id }}">

                                            <input type="hidden" name="item_id[]" value="{{ $row->item_id }}">
                                        </td>


                                        <td>
                                            {{ optional($row->brand)->name }}

                                            <input type="hidden" name="brand_id[]" value="{{ $row->brand_id }}">
                                        </td>


                                        <td>
                                            {{ optional($row->condition)->name }}

                                            <input type="hidden" name="condition_id[]" value="{{ $row->condition_id }}">
                                        </td>


                                        <td>
                                            {{ optional($row->unit)->name }}

                                            <input type="hidden" name="unit_id[]" value="{{ $row->unit_id }}">
                                        </td>


                                        <td>
                                            {{ optional($row->location)->name }}

                                            <input type="hidden" name="location_id[]" value="{{ $row->location_id }}">
                                        </td>


                                        <td>
                                            {{ $row->issued_qty + $row->pending_qty }}
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" value="{{ $row->available_stock }}"
                                                readonly>
                                        </td>

                                        <td>
                                            <input type="text" name="issue_qty[]" class="form-control"
                                                value="{{ $row->issued_qty }}" min="0" max="{{ $row->issued_qty + $row->pending_qty }}" oninput="if(this.value < 0) this.value = 1;">
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>


                <div class="card-footer">
                    <button class="btn btn-success">
                        Update Issue
                    </button>
                </div>

            </form>

        </div>

    </section>

@endsection