@extends('company.layouts.master')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <h1>{{ $label }}</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card card-teal">

                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">{{ $label }}</h3>
                    <div class="d-flex align-items-center ml-auto" style="gap: 8px;">
                        <a href="{{ route('company.dashboard', ['company' => $company->id]) }}" class="btn btn-sm btn-success">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    <!-- LOADER -->
                    <div id="loader" style="display:none; text-align:center;">
                        <i class="fa fa-spinner fa-spin"></i>
                        <p>Loading PO...</p>
                    </div>

                    <table id="poTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>PO Code</th>
                                <th>Date</th>
                                <th>Supplier</th>
                                <th>Total</th>
                                <th>ReceivedQty</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="poRows"></tbody>
                    </table>

                </div>
            </div>
        </div>
    </section>
    <!-- VIEW -->
    <div class="modal fade" id="viewPoModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header text-white"
                    style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
                    <h4>PO Details</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="viewPoBody"></div>
            </div>
        </div>
    </div>

    <!-- EDIT -->
    <div class="modal fade" id="editPoModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header text-white"
                    style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
                    <h4>Edit PO</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="editPoBody"></div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .progress-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;

            /* ✅ FIX HERE */
            background: conic-gradient(#28a745 calc(var(--percent) * 1%),
                    #e5e5e5 0);

            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
            color: #333;
        }

        /* 🔥 FIX FOR 0% → SHOW FULL BORDER COLOR */
        .progress-circle[data-progress="0"] {
            background: var(--color);
            /* FULL RED/YELLOW/GREEN */
        }

        /* INNER CIRCLE */
        .progress-circle::before {
            content: '';
            width: 30px;
            height: 30px;
            background: #fff;
            border-radius: 50%;
            position: absolute;
        }

        .progress-circle span {
            position: relative;
            font-size: 10px;
            font-weight: bold;
        }
    </style>
@endpush
@push('scripts')
    <script src="{{url('/')}}/admin/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{url('/')}}/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

    <script src="{{url('/')}}/admin/plugins/select2/js/select2.full.min.js"></script>
    <script>
        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
        $(document).ready(function () {
            loadPos();
        });

        function loadPos() {

            $("#loader").show();
            $("#poTable").hide();

            $.ajax({
                url: "{{ route('pos.data', $company->id) }}",
                type: "GET",

                success: function (res) {

                    if ($.fn.DataTable.isDataTable('#poTable')) {
                        $('#poTable').DataTable().clear().destroy();
                    }

                    $('#poRows').html(res);

                    // 🔥 CHECK EMPTY USING CLASS
                    if ($('#poRows .no-data-row').length > 0) {

                        $("#loader").hide();
                        $("#poTable").show();

                        return; // ❌ DO NOT INIT DATATABLE
                    }

                    // ✅ INIT DATATABLE ONLY WHEN DATA EXISTS
                    $('#poTable').DataTable({
                        responsive: true,
                        autoWidth: false,
                        order: [[0, 'asc']]
                    });

                    $("#loader").hide();
                    $("#poTable").show();
                },

                error: function () {
                    $("#loader").hide();
                    $("#poTable").show();
                }
            });
        }
        // 👁 VIEW
        $(document).on('click', '.view-po', function () {

            let id = $(this).data('id');

            let url = "{{ route('po.view', [$company->id, ':id']) }}";
            url = url.replace(':id', id);

            $('#viewPoModal').modal('show');
            $('#viewPoBody').html('Loading...');

            $.get(url, function (res) {
                $('#viewPoBody').html(res);
            });
        });


        // ✏️ EDIT
        $(document).on('click', '.edit-po', function () {

            let id = $(this).data('id');

            let url = "{{ route('po.edit', [$company->id, ':id']) }}";
            url = url.replace(':id', id);

            $('#editPoModal').modal('show');
            $('#editPoBody').html('Loading...');

            $.get(url, function (res) {

                $('#editPoBody').html(res);

                // 🔥 INIT YOUR SUMMERNOTE CONFIG
                setTimeout(() => {

                    // destroy if already exists
                    $('.summernote').summernote('destroy');

                    $('.summernote').summernote({
                        height: 200,
                        toolbar: [
                            ['style', ['bold', 'clear']],
                            ['font', ['fontsize']],
                            ['para', ['ul', 'ol']],
                            ['view', ['codeview']]
                        ],
                        fontSizes: ['8', '9', '10', '11', '12', '14', '18', '24', '36'],
                        disableDragAndDrop: true,
                        callbacks: {
                            onPaste: function (e) {
                                e.preventDefault();
                                let text = (e.originalEvent || e).clipboardData.getData('text/plain');
                                document.execCommand('insertText', false, text);
                            },
                            onKeydown: function (e) {
                                if (e.keyCode === 13) {
                                    document.execCommand('insertLineBreak');
                                    e.preventDefault();
                                }
                            }
                        }
                    });

                }, 200);

            });
        });
        // 🔥 UPDATE (inside edit modal blade also works)
        $(document).on('submit', '#updatePoForm', function (e) {
            e.preventDefault();

            let id = $("input[name=po_id]").val();

            let url = "{{ route('po.update', [$company->id, ':id']) }}";
            url = url.replace(':id', id);

            // 🔥 SYNC SUMMERNOTE CONTENT
            $('.summernote').each(function () {
                $(this).val($(this).summernote('code'));
            });

            $.ajax({
                url: url,
                type: "POST",
                data: $(this).serialize(),
                success: function () {

                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'PO updated successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    $('#editPoModal').modal('hide');
                    loadPos();
                }
            });
        });
    </script>

    <script>
        $('#remark').summernote({
            height: 120
        }); function calculateTotals() {

            let subtotal = 0;

            $('#updatePoForm tbody tr').each(function () {

                let qty = parseFloat($(this).find('.qty').val()) || 0;
                let rate = parseFloat($(this).find('.rate').val()) || 0;

                let amount = qty * rate;

                $(this).find('.amount').val(amount.toFixed(2));

                subtotal += amount;
            });

            $('#subtotal').val(subtotal.toFixed(2));

            let discount = parseFloat($('#discount').val()) || 0;
            let tax = parseFloat($('#tax').val()) || 0;

            let afterDiscount = subtotal - discount;
            let taxAmount = (afterDiscount * tax) / 100;
            let finalTotal = afterDiscount + taxAmount;

            $('#tax_amount').val(taxAmount.toFixed(2));
            $('#final_total').val(finalTotal.toFixed(2));
        }

        // 🔥 triggers
        $(document).on('input', '.qty, .rate, #discount, #tax', calculateTotals);

        // 🔥 initial
        $(document).ready(calculateTotals);
    </script>
@endpush