@extends('company.layouts.master')

@section('content')

    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Create Part</h1>
                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-right">

                        <li class="breadcrumb-item">

                            <a href="{{ route('company.dashboard', ['company' => $company->id]) }}">
                                Dashboard
                            </a>

                        </li>

                        <li class="breadcrumb-item">

                            <a href="{{ route('parts.index', ['company' => $company->id]) }}">
                                Parts
                            </a>

                        </li>

                        <li class="breadcrumb-item active">
                            Edit Part
                        </li>

                    </ol>

                </div>

            </div>

        </div>

    </section>

    <section class="content">

        <div class="container-fluid">

            <form action="{{ route('parts.update', [$company->id, $part->id]) }}" method="POST">

                @csrf
                @method('PUT')

                <div class="row">

                    {{-- LEFT --}}
                    <div class="col-md-3">

                        <div class="card">

                            <div class="card-header text-white"
                                style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">

                                <h3 class="card-title">
                                    Edit Part Information
                                </h3>

                            </div>

                            <div class="card-body">

                                {{-- PART NAME ENGLISH --}}
                                <div class="form-group">

                                    <label>
                                        Part Name (English)
                                    </label>

                                    <input type="text" name="name" value="{{ old('name', $part->name) }}"
                                        placeholder="Enter part name (Example: Hydraulic Assembly)" class="form-control"
                                        required>

                                </div>


                                {{-- PART NAME HINDI --}}
                                <div class="form-group">

                                    <label>
                                        Part Name (Hindi)
                                    </label>

                                    <input type="text" name="hi_name" value="{{ old('hi_name', $part->hi_name) }}"
                                        placeholder="भाग का नाम दर्ज करें" class="form-control" required>

                                </div>


                                {{-- CODE --}}
                                <!-- <div class="form-group">

                                        <label>
                                            Part Code
                                        </label>

                                        <input type="text" name="code" value="{{ old('code', $part->code) }}"
                                            placeholder="Leave blank for auto-generated code" class="form-control">

                                    </div> -->


                                {{-- NOTES ENGLISH --}}
                                <!-- <div class="form-group">

                                        <label>
                                            Notes (English)
                                        </label>

                                        <textarea name="notes" rows="4" class="form-control summernote"
                                            placeholder="Enter additional notes, specifications, or assembly instructions for this part">{!! old('notes', $part->notes) !!}</textarea>

                                    </div>


                                    {{-- NOTES HINDI --}}
                                    <div class="form-group">

                                        <label>
                                            Notes (Hindi)
                                        </label>

                                        <textarea name="hi_notes" rows="4" class="form-control summernote"
                                            placeholder="भाग के लिए अतिरिक्त नोट्स दर्ज करें">{!! old('hi_notes', $part->hi_notes) !!}</textarea>

                                    </div> -->

                            </div>

                        </div>

                    </div>


                    {{-- RIGHT --}}
                    <div class="col-md-9">

                        <div class="card">

                            <div class="card-header text-white d-flex justify-content-between align-items-center"
                                style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">

                                <h3 class="card-title">
                                    Part Items
                                </h3>

                                <div class="ml-auto">

                                    <button type="button" class="btn btn-sm btn-default" id="addRow">

                                        <i class="fa fa-plus"></i>
                                        Add Item

                                    </button>
                                    <a href="{{ route('parts.index', ['company' => $company->id]) }}"
                                        class="btn btn-sm btn-success">
                                        <i class="fa fa-arrow-left"></i> Back
                                    </a>
                                </div>

                            </div>


                            <div class="card-body table-responsive">

                                <table class="table table-bordered" id="itemsTable">

                                    <thead>

                                        <tr>

                                            <th width="25%">
                                                Item
                                            </th>

                                            <th width="20%">
                                                Hindi Item Name
                                            </th>

                                            <th width="10%">
                                                Qty
                                            </th>

                                            <th width="25%">
                                                Notes
                                            </th>

                                            <th width="8%">
                                                Action
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        @forelse($part->items as $partItem)

                                            <tr>

                                                {{-- ITEM --}}
                                                <td>

                                                    <select name="item_id[]" class="form-control item-select" required>

                                                        <option value="{{ $partItem->item_id }}" selected>

                                                            {{ $partItem->item->name ?? '' }}
                                                            ({{ $partItem->item->code ?? '' }})

                                                        </option>

                                                    </select>

                                                </td>


                                                {{-- HINDI ITEM NAME --}}
                                                <td>

                                                    <input type="text" name="item_hi_name[]"
                                                        value="{{ $partItem->item->hi_name ?? '' }}"
                                                        placeholder="वस्तु का हिंदी नाम" class="form-control">

                                                </td>


                                                {{-- QTY --}}
                                                <td>

                                                    <input type="text" step="1" value="{{ $partItem->quantity }}"
                                                        name="quantity[]" class="form-control" required>

                                                </td>


                                                {{-- NOTES --}}
                                                <td>

                                                    <input type="text" name="item_notes[]" value="{{ $partItem->notes }}"
                                                        placeholder="Enter Notes" class="form-control mb-2">

                                                    <input type="text" name="hi_item_notes[]" value="{{ $partItem->hi_notes }}"
                                                        placeholder="नोट्स दर्ज करें" class="form-control">

                                                </td>


                                                {{-- REMOVE --}}
                                                <td class="text-center">

                                                    <button type="button" class="btn btn-danger btn-sm removeRow">

                                                        <i class="fa fa-trash"></i>

                                                    </button>

                                                </td>

                                            </tr>

                                        @empty

                                            <tr>

                                                {{-- ITEM --}}
                                                <td>

                                                    <select name="item_id[]" class="form-control item-select" required>
                                                    </select>

                                                </td>


                                                {{-- HINDI ITEM NAME --}}
                                                <td>

                                                    <input type="text" name="item_hi_name[]" placeholder="वस्तु का हिंदी नाम"
                                                        class="form-control">

                                                </td>


                                                {{-- QTY --}}
                                                <td>

                                                    <input type="text" step="1" value="1" name="quantity[]"
                                                        class="form-control" required>

                                                </td>


                                                {{-- NOTES --}}
                                                <td>

                                                    <input type="text" name="item_notes[]" placeholder="Enter Notes"
                                                        class="form-control mb-2">

                                                    <input type="text" name="hi_item_notes[]" placeholder="नोट्स दर्ज करें"
                                                        class="form-control">

                                                </td>


                                                {{-- REMOVE --}}
                                                <td class="text-center">

                                                    <button type="button" class="btn btn-danger btn-sm removeRow">

                                                        <i class="fa fa-trash"></i>

                                                    </button>

                                                </td>

                                            </tr>

                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- FOOTER --}}
                <div class="row">

                    <div class="col-12">

                        <div class="card">

                            <div class="card-body text-right">

                                <a href="{{ route('parts.index', ['company' => $company->id]) }}" class="btn btn-secondary">

                                    Back

                                </a>

                                <button type="submit" class="btn btn-success">

                                    <i class="fa fa-save"></i>
                                    Update Part

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
        /* =========================================
           ITEM AJAX SEARCH
        ========================================== */
        function initItemSelect(selectElement) {

            $(selectElement).select2({

                placeholder: 'Search Item by name or code',

                allowClear: false,

                width: '100%',

                ajax: {

                    url: "{{ route('parts.searchItems', ['company' => $company->id]) }}",

                    dataType: 'json',

                    delay: 250,

                    data: function (params) {

                        return {
                            search: params.term
                        };
                    },

                    processResults: function (data) {

                        return {
                            results: data
                        };
                    },

                    cache: true
                }
            });
        }


        /* =========================================
           DOCUMENT READY
        ========================================== */
        $(document).ready(function () {
            // INIT FIRST ITEM SELECT
            $('.item-select').each(function () {

                initItemSelect(this);

            });

        });


        /* =========================================
           ADD ROW
        ========================================== */
        $('#addRow').click(function () {

            let row = `
                <tr>

                    {{-- ITEM --}}
                    <td>

                        <select name="item_id[]"
                                class="form-control item-select"
                                required>
                        </select>

                    </td>


                    {{-- HINDI ITEM NAME --}}
                    <td>

                        <input type="text"
                               name="item_hi_name[]"
                               class="form-control"
                               placeholder="वस्तु का हिंदी नाम">

                    </td>


                    {{-- QTY --}}
                    <td>

                        <input type="text"
                               step="1"
                               min="1"
                               name="quantity[]"
                               class="form-control"
                               placeholder="Qty"
                               required>

                    </td>


                    {{-- NOTES --}}
                    <td>

                        <input type="text"
                               name="item_notes[]"
                               class="form-control mb-2"
                               placeholder="Enter item notes">

                        <input type="text"
                               name="hi_item_notes[]"
                               class="form-control"
                               placeholder="नोट्स दर्ज करें">

                    </td>


                    {{-- REMOVE --}}
                    <td class="text-center">

                        <button type="button"
                                class="btn btn-danger btn-sm removeRow">

                            <i class="fa fa-trash"></i>

                        </button>

                    </td>

                </tr>
            `;

            $('#itemsTable tbody').append(row);

            // INIT AJAX SELECT
            $('#itemsTable tbody tr:last .item-select').each(function () {

                initItemSelect(this);

            });

        });


        /* =========================================
           REMOVE ROW
        ========================================== */
        $(document).on('click', '.removeRow', function () {

            if ($('#itemsTable tbody tr').length > 1) {

                $(this).closest('tr').remove();

            } else {

                alert('At least one item is required.');

            }

        });

    </script>

@endpush