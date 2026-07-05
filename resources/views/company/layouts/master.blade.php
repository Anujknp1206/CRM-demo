@extends('layouts.master')

@section('navbar')
    @include('company.layouts.navbar')
@endsection
@section('footer')
    @include('company.layouts.footer')
@endsection
@push('styles')
    <style>
        #leadDetailsModal {
            z-index: 1070 !important;
        }

        .modal-backdrop.lead-backdrop {
            z-index: 1065 !important;
        }

        #descriptionModal {
            z-index: 1065 !important;
        }

        .dropdown-menu-lg {
            width: 320px !important;
            max-width: 320px;
        }

        .dropdown-item {
            white-space: normal !important;
            word-break: break-word;
            line-height: 1.4;
        }

        .dropdown-item strong {
            display: block;
            font-size: 14px;
        }

        .dropdown-item .text-sm {
            font-size: 12px;
            color: #6c757d;
        }

        /* Fix modal table wrapping */
        #leadDetailsModal table td,
        #leadDetailsModal table th {
            white-space: normal !important;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        @media (max-width: 576px) {
            #customerTabs {
                flex-wrap: nowrap;
                /* prevent wrapping */
                overflow-x: auto;
                /* enable horizontal scroll */
                overflow-y: hidden;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
                /* smooth scroll on mobile */
            }

            #customerTabs .nav-item {
                flex: 0 0 auto;
                /* prevent shrinking */
            }

            #customerTabs .nav-link {
                display: inline-block;
                padding: 8px 12px;
                font-size: 13px;
            }

            #customerNameTitle {
                font-size: 14px;
                /* adjust as needed */
            }
        }

        @media (max-width: 576px) {
            .table {
                font-size: 12px;
            }

            .table th {
                min-width: 140px;
                /* adjust as needed */
                white-space: nowrap;
            }

            .table th,
            .table td {
                white-space: nowrap;
                /* keeps columns clean */
            }
        }
    </style>
@endpush
@push('scripts')

    </script> <!-- DataTables Core JS -->
    <script src="{{ url('/') }}/admin/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ url('/') }}/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

    <!-- Responsive -->
    <script src="{{ url('/') }}/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ url('/') }}/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

    <!-- Buttons -->
    <script src="{{ url('/') }}/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{ url('/') }}/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>

    <!-- Buttons: Export Files -->
    <script src="{{ url('/') }}/admin/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="{{ url('/') }}/admin/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="{{ url('/') }}/admin/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

    <!-- Required for Excel/PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script>
        $(document).on('select2:open', function () {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });
        $(document).ready(function () {
           $('#example1').DataTable({
                responsive: true,
                autoWidth: false,

                paging: true,
                pageLength: 10,
                lengthChange: true,
                lengthMenu: [10, 25, 50, 100, -1],

                searching: true,
                info: true,
                ordering: true,

                dom: '<"d-flex justify-content-between align-items-center"Bf>rt<"d-flex justify-content-between mt-2"ip>',

                buttons: [
                    {
                        extend: 'colvis',
                        text: 'Column visibility'
                    }
                ]
            });
        });
    </script>
    <script>
        // 🚫 Block negative typing (keyboard)
        $(document).on('keydown', 'input[type="number"]', function (e) {

            // block minus key
            if (e.key === '-' || e.key === 'Minus') {
                e.preventDefault();
            }

            // block exponential (e, E)
            if (e.key === 'e' || e.key === 'E') {
                e.preventDefault();
            }
        });


        // 🚫 Fix pasted / manual negative values
        $(document).on('input', 'input[type="number"]', function () {

            let val = parseFloat(this.value);

            if (!isNaN(val) && val < 0) {
                this.value = 0; // or 1 if you want minimum 1
            }
        });


        // 🚫 Prevent arrow down going below 0
        $(document).on('change', 'input[type="number"]', function () {

            let val = parseFloat(this.value);

            if (!isNaN(val) && val < 0) {
                this.value = 0;
            }
        });
        function syncAccordionWithPrintType(animate = true) {
            const type = $('#orderPrintType').val();
            const action = animate ? 'show' : 'show';

            if (type === 'staff') {
                $('#staffFields').collapse(action);
                $('#customerFields').collapse('hide');
            }

            else if (type === 'customer') {
                $('#staffFields').collapse('hide');
                $('#customerFields').collapse(action);
            }

            else if (type === 'both') {
                $('#staffFields').collapse(action);
                $('#customerFields').collapse('hide'); // keep one open for smoothness
            }
        }

        $('#orderPrintType').on('change', function () {
            syncAccordionWithPrintType(true);
        });

        $('#orderPrintModal').on('shown.bs.modal', function () {
            syncAccordionWithPrintType(false);
        });
    </script>
    <script>
        function bindCheckAll(copy) {
            const allBox = $('#' + copy + 'CheckAll');
            const fields = $('.field-toggle[data-copy="' + copy + '"]');

            // when Check All clicked
            allBox.on('change', function () {
                fields.prop('checked', this.checked).trigger('change');
            });

            // when individual checkbox changed
            fields.on('change', function () {
                const total = fields.length;
                const checked = fields.filter(':checked').length;
                allBox.prop('checked', total === checked);
            });
        }

        $(document).ready(function () {
            bindCheckAll('staff');
            bindCheckAll('customer');
        });
    </script>
    <script>
        let CURRENT_CUSTOMER_ID = null;

        $(document).on('click', '.open-customer-360', function () {
            CURRENT_CUSTOMER_ID = $(this).data('customer-id');

            const name = $(this).data('customer-name') || 'Customer';
            const mobile = $(this).data('customer-mobile') || '---';

            $('#customerNameTitle').text(`${name} (${mobile})`);
            $('#customer360Modal').modal('show');

            loadCustomerTab('leads');
        });


        $(document).on('click', '#customerTabs .nav-link', function () {
            $('#customerTabs .nav-link').removeClass('active');
            $(this).addClass('active');

            loadCustomerTab($(this).data('tab'));
        });
        function loadCustomerTab(tab) {
            $('#customerTabContent').html(
                '<div class="text-center py-5"><i class="fa fa-spinner fa-spin"></i></div>'
            );

            let companyId = "{{ $company->id }}";

            let url = "{{ route('customer.360', [$company->id, 'CUSTOMER_ID', 'TYPE']) }}"
                .replace('CUSTOMER_ID', CURRENT_CUSTOMER_ID)
                .replace('TYPE', tab);

            $.get(url, function (html) {
                $('#customerTabContent').html(html);
            });
        }
    </script>
    <script>
        $(document).ready(function () {
            $('.summernote').summernote({
                height: 200,
                toolbar: [
                    ['style', ['bold', 'clear']],
                    ['font', ['fontsize']],   // enable font size
                    ['para', ['ul', 'ol']],
                    ['view', ['codeview']]
                ],
                fontSizes: ['8', '9', '10', '11', '12', '14', '18', '24', '36'], // optional sizes
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
        });
    </script>
    <script>
        $(document).on(
            "click",
            ".open-lead-modal, .open-quotation-modal, .open-order-modal, .open-payment-modal, .open-planning-modal",
            function () {
                const id = $(this).data("id");
                const isLead = $(this).hasClass("open-lead-modal");
                const isQuotation = $(this).hasClass("open-quotation-modal");
                const isOrder = $(this).hasClass("open-order-modal");
                const isPayment = $(this).hasClass("open-payment-modal");
                const isPlanning = $(this).hasClass("open-planning-modal");
                const modalTitle =
                    isLead ? "Lead Details"
                        : isQuotation ? "Quotation Details"
                            : isOrder ? "Order Details"
                                : isPayment ? "Payment Details"
                                    : "Job Card Details";
                $("#leadDetailsModal .modal-title").text(modalTitle);
                $("#lead-details-content").html("<p class='text-center'>Loading...</p>");
                $("#leadDetailsModal").modal("show");
                const url =
                    isLead
                        ? "{{ route('ajax.get.single.lead.details', ['company' => $company->id]) }}"
                        : isQuotation
                            ? "{{ route('ajax.get.quotation.details', ['company' => $company->id]) }}"
                            : isOrder
                                ? "{{ route('ajax.get.order.details', ['company' => $company->id]) }}"
                                : isPayment ? "{{ route('ajax.get.payment.details', ['company' => $company->id]) }}"
                                    : "{{ route('ajax.get.jobcard.details', ['company' => $company->id]) }}";
                $.ajax({
                    url: url,
                    type: "GET",
                    data: { id },

                    success: function (data) {

                        let html = "";

                        /* ================= LEAD ================= */
                        if (isLead) {

                            const countryCode = data.country?.phonecode
                                ? `+${data.country.phonecode}`
                                : '';

                            // ✅ Build multiple mobiles list
                            let mobilesHtml = '---';

                            if (Array.isArray(data.phones) && data.phones.length) {
                                mobilesHtml = data.phones
                                    .map(phone => `${countryCode} ${phone}`)
                                    .join('<br>');
                            }

                            html = `<div class="table-responsive">
                                                                                                                                <table class="table table-bordered">
                                                                                                                                    <tbody>
                                                                                                                                        <tr><th>Lead Code</th><td>${data.lead_code ?? '---'}</td></tr>
                                                                                                                                        <tr><th>Customer Name</th><td>${data.customerName ?? '---'}</td></tr>
                                                                                                                                        <tr>
                                                                                                                                            <th>Mobile</th>
                                                                                                                                            <td>${mobilesHtml}</td>
                                                                                                                                        </tr>
                                                                                                                                        <tr><th>Email</th><td>${data.email ?? '---'}</td></tr>
                                                                                                                                        <tr><th>GST Number</th><td>${data.gst ?? '---'}</td></tr>
                                                                                                                                        <tr><th>Country</th><td>${data.country?.name ?? '---'}</td></tr>
                                                                                                                                        <tr><th>State</th><td>${data.state?.name ?? '---'}</td></tr>
                                                                                                                                        <tr><th>City</th><td>${data.city?.name ?? '---'}</td></tr>
                                                                                                                                        <tr><th>Address</th><td>${data.address ?? '---'}</td></tr>
                                                                                                                                        <tr><th>Purpose</th><td>${data.purpose ?? '---'}</td></tr>
                                                                                                                                        <tr><th>Remark</th><td>${data.remark ?? '---'}</td></tr>
                                                                                                                                        <tr><th>Reference</th><td>${data.reference ?? '---'}</td></tr>
                                                                                                                                        <tr><th>Lead Date</th><td>${data.created_at ?? '---'}</td></tr>
                                                                                                                                    </tbody>
                                                                                                                                </table>
                                                                                                                                </div>
                                                                                                                            `;
                        }

                        /* ================= QUOTATION ================= */
                        else if (isQuotation) {
                            const countryCode = data.lead?.customer?.country?.phonecode
                                ? `+${data.lead.customer.country.phonecode}`
                                : '';
                            html = `<div class="table-responsive">
                                                                       <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>Lead Number</th>
                                                <td>${data.lead?.lead_code ?? '---'}</td>
                                            </tr>

                                            <tr>
                                                <th>Quotation Number</th>
                                                <td>${data.quote_number}</td>
                                            </tr>

                                            <tr>
                                                <th>PI Number</th>
                                                <td>${data.pi_number ?? '---'}</td>
                                            </tr>

                                            <tr>
                                                <th>Quote Date</th>
                                                <td>${formatDate(data.quote_date)}</td>
                                            </tr>

                                            <tr>
                                                <th>PI Date</th>
                                                <td>${formatDate(data.pi_date)}</td>
                                            </tr>

                                            <tr>
                                                <th>Customer Name</th>
                                                <td>${data.lead?.customer?.name ?? '---'}</td>
                                            </tr>

                                            <tr>
                                                <th>Contact Person</th>
                                                <td>${data.contact_person ?? '---'}</td>
                                            </tr>

                                            <tr>
                                                <th>Email</th>
                                                <td>${data.lead?.customer?.email ?? '---'}</td>
                                            </tr>

                                            <tr>
                                                <th>Mobile</th>
                                                <td>
                                                    ${data.lead?.customer?.primary_phone?.phone
                                    ? `${countryCode} ${data.lead.customer.primary_phone.phone}`
                                    : '---'
                                }
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>GST Number</th>
                                                <td>${data.lead?.customer?.gst ?? '---'}</td>
                                            </tr>

                                            <tr>
                                                <th>Remark</th>
                                                <td>${data.special_clause ?? '---'}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                                                            </div>
                                                <h5 class="mt-3 text-teal">Addresses</h5><table class="table table-bordered no-break"><tr>
                                                <th>Office Address</th>
                                                <td>${data.lead?.customer?.address ?? '---'}</td>
                                                </tr>
                                                <tr><th>Delivery Address</th><td>${data.delivery_address ?? '---'}</td></tr>
                                                </table>
                                                <h5 class="mt-3 text-teal">Lead & Users</h5>
                                                <table class="table table-bordered no-break"><tr>
                                                <th>Lead</th>
                                                <td>
                                                ${data.lead?.lead_code ?? '---'} – 
                                                                                                                    ${data.lead?.customer?.name ?? '---'}
                                                                                                                  </td>
                                                                                                                </tr>

                                                                                                                                                                                                                                                                                                                                                                <tr><th>Created By</th><td>${data.creator?.name ?? '---'}</td></tr>
                                                                                                                                                                                                                                                                                                                                                                <tr><th>Assigned To</th><td>${data.assigned_user?.name ?? '---'}</td></tr>
                                                                                                                                                                                                                                                                                                                                                            </table>

                                                                                                                                                                                                                                                                                                                                                            <h5 class="mt-3 text-teal">Quotation Items</h5>
                                            <div class="table-responsive">
                                                                                                                <div class="table-responsive">
                                                                                                                                                                                                                                                                                                                                                            <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>S.N</th>
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Unit Price</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${data.items.map((i, index) => `
                                                    <tr>
                                                        <td>${index + 1}</td>
                                                        <td>${i.machine?.name ?? i.component?.name ?? 'Custom Item'}</td>
                                                        <td>${i.description ?? '---'}</td>
                                                        <td>${i.quantity}</td>
                                                        <td>${i.unit_price}</td>
                                                        <td>${i.total_price}</td>
                                                    </tr>
                                                `).join("")}
                                            </tbody>
                                        </table>
                                    </div>
                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                            <h5 class="mt-3 text-teal">Totals</h5>
                                                                                                                                                                                                                                                                                                                                                            <table class="table table-bordered no-break">
                                                                                                                                                                                                                                                                                                                                                                <tr><th>Subtotal</th><td>${data.total_amount}</td></tr>
                                                                                                                                                                                                                                                                                                                                                                <tr><th>Discount</th><td>${data.discount}</td></tr>
                                                                                                                                                                                                                                                                                                                                                               <tr><th>Tax %</th><td>${data.tax ?? 0} %</td></tr>
                                                                                                                <tr><th>Tax Amount</th><td>${data.tax_amount ?? 0}</td></tr>
                                                                                                                <tr><th>Final Amount</th><td><strong>${data.final_amount ?? 0}</strong></td></tr>

                                                                                                                                                                                                                                                                                                                                                            </table>
                                                                                                                                                                                                                                                               <h5 class="mt-3 text-teal"><u>Terms & Conditions</u></h5>
                                                                                                        <div class="p-2 border"">
                                                                                                            ${data.terms_conditions
                                    ? data.terms_conditions
                                        .replace(/<p[^>]*>(\s|&nbsp;|<br\s*\/?>)*<\/p>/gi, '') // remove empty <p>
                                        .replace(/<br\s*\/?>/gi, '')                           // remove extra <br>
                                    : '---'
                                }
                                                                                                        </div>
                                                                                                        <h5 class="mt-3 text-teal">Attachments</h5>
                                        <div class="table-responsive">
                                                                    <table class="table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>#</th>
                                                                                <th>File</th>
                                                                                <th>Action</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            ${data.files && data.files.length
                                    ? data.files.map((f, i) => `
                                                                                    <tr>
                                                                                        <td>${i + 1}</td>
                                                                                        <td>${f.file_name}</td>
                                                                                        <td>
                                                                                            <a href="/admin/uploads/${f.file_path}" 
                                                                                               target="_blank" 
                                                                                               class="btn btn-sm btn-primary" style="height:auto";>
                                                                                               <i class="fa fa-eye"></i> View
                                                                                            </a>

                                                                                            <a href="/admin/uploads/${f.file_path}" 
                                                                                               download 
                                                                                               class="btn btn-sm btn-success">
                                                                                               <i class="fa fa-download"></i> Download
                                                                                            </a>
                                                                                        </td>
                                                                                    </tr>
                                                                                `).join("")
                                    : `<tr><td colspan="3" class="text-center">No files uploaded</td></tr>`
                                }
                                                                        </tbody>
                                                                    </table>
                                                                    </div>`;
                        } else if (isOrder) {
                            const countryCode = data.country?.phonecode
                                ? `+${data.country.phonecode}`
                                : '';
                            html = `
                                                                                                                                                                                                                                                                            <table class="table table-bordered">
                                                                                                                                                                                                                                                                                <tr><th>Order Number</th><td>${data.order_number}</td></tr>
                                                                                                                                                                                                                                                                              <tr><th>Order Date</th><td>${formatDate(data.order_date)}</td></tr>

                                                                                                                                                                                                                                                                                <tr><th>Status</th><td>${data.status}</td></tr>

                                                                                                                                                                                                                                                                            </table>

                                                                                                                                                                                                                                                                            <h5 class="mt-3 text-teal">Customer Details</h5>
                                                                                                                                                                                                                                                                            <table class="table table-bordered">
                                                                                                                                                                                                                                                                                <tr><th>Name</th><td>${data.customer_name}</td></tr>
                                                                                                                                                                                                                                                                                <tr><th>Contact Person</th><td>${data.contact_person ?? '---'}</td></tr>
                                                                                                                                                                                                                                                                                <tr><th>Email</th><td>${data.email ?? '---'}</td></tr>
                                                                                                                                                                                                                                                                               <tr>
                                                                                          <th>Mobile</th>
                                                                                          <td>
                                                                                            ${data.mobile
                                    ? `${countryCode} ${data.mobile}`
                                    : '---'
                                }
                                                                                          </td>
                                                                                        </tr>

                                                                                                                                                                                                                                                                                <tr><th>GST</th><td>${data.customer_gst ?? '---'}</td></tr>
                                                                                                                                                                                                                                                                            </table>

                                                                                                                                                                                                                                                                            <h5 class="mt-3 text-teal">Order Items</h5>

                                                                                                                <div class="table-responsive">
                                                                                                                                                                                                                                                                            <table class="table table-bordered">
                                                                                                                                                                                                                                                                                <thead>
                                                                                                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                                                                                                        <th>S.N</th>
                                                                                                                                                                                                                                                                                        <th>Item</th>
                                                                                                                                                                                                                                                                                        <th style="width:40%">Description</th>
                                                                                                                                                                                                                                                                                        <th>Qty</th>
                                                                                                                                                                                                                                                                                        <th>Unit Price</th>
                                                                                                                                                                                                                                                                                        <th>Total</th>
                                                                                                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                                                                                                </thead>
                                                                                                                                                                                                                                                                                <tbody>
                                                                                                                                                                                                                                                                                    ${data.items.map((i, index) => `
                                                                                                                                                                                                                                                                                        <tr> 
                                                                                                                                                                                                                                                                                            <td>${index + 1}</td>
                                                                                                                                                                                                                                                                                            <td>${i.machine?.name ?? i.component?.name ?? 'Item'}</td>
                                                                                                                                                                                                                                                                                            <td>${i.description ?? '---'}</td>
                                                                                                                                                                                                                                                                                            <td>${i.quantity}</td>
                                                                                                                                                                                                                                                                                            <td>${i.unit_price}</td>
                                                                                                                                                                                                                                                                                            <td>${i.total_price ?? (i.quantity * i.unit_price)}</td>
                                                                                                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                                                                                                    `).join("")}
                                                                                                                                                                                                                                                                                </tbody>
                                                                                                                                                                                                                                                                            </table>
                                                                                    </div>
                                                                                                                                                                                                                                                                            <h5 class="mt-3 text-teal">Totals</h5>
                                                                                                                                                                                                                                                                            <table class="table table-bordered">
                                                                                                                                                                                                                                                                                <tr><th>Subtotal</th><td>${data.total_amount}</td></tr>
                                                                                                                                                                                                                                                                                <tr><th>Discount</th><td>${data.discount}</td></tr>
                                                                                                                                                                                                                                                                                <tr><th>Tax</th><td>${data.tax}</td></tr>
                                                                                                                                                                                                                                                                                <tr><th>Final Amount</th><td><strong>${data.final_amount}</strong></td></tr>
                                                                                                                                                                                                                                                                            </table>

                                                                                                                                                                                                                                                                            <h5 class="mt-3 text-teal">Terms & Conditions</h5>
                                                                                                                                                                                                                                                                            <div class="p-2 border">
                                                                                                                                                                                                                                                                                ${data.terms_conditions
                                    ? data.terms_conditions.replace(/<p[^>]*>(\s|&nbsp;|<br\s*\/?>)*<\/p>/gi, "")
                                    : '---'}
                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                            <h5 class="mt-3 text-teal">Attachments</h5>

                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>File</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        ${data.files && data.files.length
                                    ? data.files.map((f, i) => `
                                                                                <tr>
                                                                                    <td>${i + 1}</td>
                                                                                    <td>${f.file_name}</td>
                                                                                    <td>
                                                                                        <a href="/admin/uploads/${f.file_path}" 
                                                                                           target="_blank"
                                                                                           class="btn btn-sm btn-primary">
                                                                                           <i class="fa fa-eye"></i> View
                                                                                        </a>

                                                                                        <a href="/admin/uploads/${f.file_path}" 
                                                                                           download
                                                                                           class="btn btn-sm btn-success">
                                                                                           <i class="fa fa-download"></i> Download
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                            `).join("")
                                    : `<tr><td colspan="3" class="text-center">No files uploaded</td></tr>`
                                }
                                                                    </tbody>
                                                                </table>
                                                                                                                                                                                                                                                                        `;
                        }
                        /* ================= PAYMENT ================= */
                        else if (isPayment) {

                            if (data.status === 'pending') {
                                const p = data.payment;

                                html = `
                                                                                                                <div class="table-responsive">
                                                                                                                                                        <table class="table table-bordered">
                                                                                                                                                            <tr><th>Payment No</th><td>${p.payment_number}</td></tr>
                                                                                                                                                            <tr><th>Date</th><td>${formatDate(p.payment_date)}</td></tr>
                                                                                                                                                            <tr>
                <th>Post Date</th>
                <td>
                    ${p.is_post_dated && p.post_date
                                        ? `<span class="text-danger">Post Dated: ${formatDate(p.post_date)}</span>`
                                        : '---'
                                    }
                </td>
            </tr>
                                                                                                                                                            <tr><th>Amount</th><td>${data.currency_symbol} ${p.amount}</td></tr>
                                                                                                                                                            <tr><th>Mode</th><td>${p.payment_mode}</td></tr>
                                                                                                                                                            <tr><th>Reference</th><td>${p.transaction_reference ?? '---'}</td></tr>
                                                                                                                                                            <tr><th>Note</th><td>${p.note ?? '---'}</td></tr>
                                                                                                                                                            <tr><th>Status</th><td><strong>Pending</strong></td></tr>
                                                                                                                                                        </table>
                                                                                                                                                        </div>
                                                                                                                                                    `;
                            }

                            if (data.status === 'completed') {
                                const payments = data.payments;

                                html = `
                                                                                                                                                        <h5 class="text-teal">All Payment Transactions</h5>

                                                                                                                                                        <table class="table table-bordered">
                                                                                                                                                            <thead>
                                                                                                                                                                <tr>
                                                                                                                                                                    <th>#</th>
                                                                                                                                                                    <th>Payment No</th>
                                                                                                                                                                    <th>Date</th>
                                                                                                                                                                    <th>Amount</th>
                                                                                                                                                                    <th>Mode</th>
                                                                                                                                                                    <th>Reference</th>
                                                                                                                                                                    <th>Status</th>
                                                                                                                                                                </tr>
                                                                                                                                                            </thead>
                                                                                                                                                            <tbody>
                                                                                                                                                                ${payments.map((p, i) => `
                                                                                                                                                                    <tr>
                                                                                                                                                                        <td>${i + 1}</td>
                                                                                                                                                                        <td>${p.payment_number}</td>
                                                                                                                                                                        <td>${formatDate(p.payment_date)}</td>
                                                                                                                                                                        <td>${data.currency_symbol} ${p.amount}</td>
                                                                                                                                                                        <td>${p.payment_mode}</td>
                                                                                                                                                                        <td>${p.transaction_reference ?? '---'}</td>
                                                                                                                                                                        <td><span class="badge badge-success">Completed</span></td>
                                                                                                                                                                    </tr>
                                                                                                                                                                `).join("")}
                                                                                                                                                            </tbody>
                                                                                                                                                        </table>
                                                                                                                                                    `;
                            }
                        }

                        else if (isPlanning) {

                            html = `
                                                                <table class="table table-bordered">
                                                                    <tr><th>PO Number</th><td>${data.po_number}</td></tr>
                                                                    <tr><th>Order</th><td>${data.order?.order_number ?? '---'}</td></tr>
                                                                    <tr><th>Department</th><td>${data.department?.name ?? '---'}</td></tr>
                                                                    <tr><th>Incharge</th>
                                                                        <td>
                                                                            ${data.incharge
                                    ? data.incharge.first_name + ' ' + data.incharge.last_name
                                    : '---'}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                    <th>Checked By</th>
                                                    <td>
                                                        ${data.checkedBy
                                    ? data.checkedBy.first_name + ' ' + data.checkedBy.last_name
                                    : '---'}
                                                    </td>
                                                </tr>
                                                                    <tr><th>Priority</th><td>${data.priority}</td></tr>
                                                                    <tr><th>Shift</th><td>${data.shift}</td></tr>
                                                                    <tr><th>Delivery Date</th><td>${formatDate(data.delivery_date)}</td></tr>
                                                                    <tr><th>Status</th><td><span class="badge badge-info">${data.status}</span></td></tr>
                                                                    <tr><th>Remark</th><td>${data.remark ?? '---'}</td></tr>
                                                                    <tr><th>Terms</th><td style="white-space: pre-line;">${data.term ?? '---'}</td></tr>
                                                                </table>

                                                                <h5 class="mt-3 text-teal">Job Card Items</h5>

                                                                <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Item</th>
                                                                             <th style="width:40%">Description</th>
                                                                            <th>Specification</th>
                                                                            <th>Qty</th>
                                                                            <th>Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        ${data.items.map((i, index) => `
                                                                            <tr>
                                                                                <td>${index + 1}</td>
                                                                                <td>${i.order_item?.machine?.name || i.order_item?.component?.name || '-'}</td>
                                                                                <td>${i.description ?? '-'}</td>
                                                                                <td>${i.specs ?? '-'}</td>
                                                                                <td>${i.qty}</td>
                                                                                <td>
                                                                                    <span class="badge badge-${i.status === 'done' ? 'success' : i.status === 'working' ? 'warning' : 'secondary'}">
                                                                                        ${i.status}
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                        `).join("")}
                                                                    </tbody>
                                                                </table>
                                                                </div>
                                                            `;
                        }
                        $("#lead-details-content").html(html);
                    },

                    error: function () {
                        $("#lead-details-content").html(
                            "<p class='text-center text-danger'>Unable to load details.</p>"
                        );
                    }
                });
            }
        );

        /* ===== HELPER ===== */
        function formatDate(dateStr) {
            if (!dateStr) return '---';
            const d = new Date(dateStr);
            return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
        }
    </script>

@endpush