<div class="table-responsive">

    <table class="table table-hover align-middle mb-0 order-report-table">

        <thead>

            <tr class="report-group-header">

                <th colspan="4">
                    ORDER INFO
                </th>

                <th colspan="2">
                    FINANCIALS
                </th>

                <th>
                    PAYMENT
                </th>

                <th>
                    PRODUCTION
                </th>

                <th>
                    STATUS
                </th>

            </tr>

            <tr class="report-column-header">

                <th>
                    Order No
                </th>

                <th>
                    Customer
                </th>

                <th>
                    Order Date
                </th>

                <th>
                    Delivery Date
                </th>

                <th>
                    Amount
                </th>

                <th>
                    Paid
                </th>

                <th>
                    Payment
                </th>

                <th>
                    Progress
                </th>

                <th>
                    Status
                </th>

            </tr>

        </thead>

        <tbody id="reportAccordion">

            @include(
                'company.crm.reports.partials.order_report_rows',
                ['orders' => $orders]
            )

        </tbody>

    </table>

</div>