<div class="card mb-3 shadow-sm">
    <div class="card-body">

        <h5>{{ $customer->name }}</h5>

        <p class="mb-1">
            📞 {{ $customer->full_primary_mobile }}
        </p>

        <p class="mb-1">
            📧 {{ $customer->email ?? '—' }}
        </p>

        <p class="mb-1">
            📍 {{ $customer->address ?? '—' }}
        </p>

        <p class="mb-1">
            GST: {{ $customer->gst ?? '—' }}
        </p>

    </div>
</div>
<div class="row text-center mb-3">

    <div class="col-md-3">
        <div class="card p-2 bg-light">
            <h6>Leads</h6>
            <strong>{{ $summary['totalLeads'] }}</strong>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-2 bg-light">
            <h6>Quotations</h6>
            <strong>{{ $summary['totalQuotations'] }}</strong>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-2 bg-light">
            <h6>Orders</h6>
            <strong>{{ $summary['totalOrders'] }}</strong>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-2 bg-light">
            <h6>Payments</h6>
            <strong>{{ $summary['totalPayments'] }}</strong>
        </div>
    </div>

</div>