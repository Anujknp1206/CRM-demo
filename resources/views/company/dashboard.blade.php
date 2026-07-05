@extends('company.layouts.master')
@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <h2 class="mb-0 font-weight-bold">{{ $company->company_name }} Dashboard</h2>
            <small>Real-time CRM Insights</small>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                @can('view lead')
                    <div class="col-md-4 mb-3">
                        <div class="dashboard-card position-relative">
                            {{-- CLICKABLE CARD --}}
                            <a href="{{ route('leads.index', $company->id) }}" class="stretched-link">
                            </a>
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6>Total Leads</h6>
                                    <h2>
                                        {{ $totalLeads ?? '-' }}
                                    </h2>
                                </div>
                                <div class="d-flex flex-column align-items-center">
                                    {{-- PLUS BUTTON --}}
                                    @can('leads')
                                        <a href="{{ route('leads.create', $company->id) }}" class="small-btn mb-4">
                                            +
                                        </a>
                                    @endcan
                                    {{-- ICON --}}
                                    <i class="fa fa-users fa-2x lead-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
                @can('view quotation')
                    <div class="col-md-4 mb-3">
                        <div class="dashboard-card position-relative text-white">
                            <a href="{{ route('quotations.index', $company->id) }}" class="stretched-link">
                            </a>
                            <div class="d-flex justify-content-between align-items-start">

                                <div>

                                    <h6 class="mb-2">
                                        Total Quotations
                                    </h6>

                                    <h2 class="mb-3">
                                        {{ $totalQuotes ?? '-' }}
                                    </h2>
                                </div>



                                <div class="d-flex flex-column align-items-center">
                                    @can('quotation')
                                        <a href="{{ route('quotations.create', $company->id) }}" class="small-btn mb-4">
                                            +
                                        </a>
                                    @endcan
                                    <i class="fa fa-file-text fa-2x lead-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan

                @can('view order')
                    <div class="col-md-4 mb-3">
                        <div class="dashboard-card position-relative text-white">
                            <a href="{{ route('orders.index', $company->id) }}" class="stretched-link">
                            </a>
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-2">
                                        Total Orders
                                    </h6>
                                    <h2 class="mb-3">
                                        {{ $totalOrders ?? '-' }}
                                    </h2>
                                </div>
                                <div class="d-flex flex-column align-items-center">
                                    @can('orders')
                                        <a href="{{ route('orders.create', $company->id) }}" class="small-btn mb-4">
                                            +
                                        </a>
                                    @endcan
                                    <i class="fa fa-shopping-cart fa-2x lead-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
            <div class="row mt-2">
                {{-- ================= BOM ================= --}}
                <div class="col mb-3">

                    <div class="dashboard-card position-relative text-white">
                        @can('bom')
                            <a href="{{ route('boms.index', $company->id) }}" class="stretched-link"></a>
                        @endcan
                        <div class="d-flex justify-content-between align-items-start">

                            <div>

                                <h6>Total BOM</h6>

                                <h2>{{ $totalBoms }}</h2>
                            </div>


                            <div class="d-flex flex-column align-items-center">
                                @can('add bom')
                                    <a href="{{ route('boms.create', $company->id) }}" class="small-btn mb-4">
                                        +
                                    </a>
                                @endcan
                                <i class="fa fa-cogs fa-2x lead-icon"></i>

                            </div>

                        </div>

                    </div>

                </div>
                {{-- ================= ISSUES ================= --}}
                <div class="col mb-3">

                    <div class="dashboard-card position-relative text-white">
                        @can('issues')
                            <a href="{{ route('issues.index', $company->id) }}" class="stretched-link"></a>
                        @endcan
                        <div class="d-flex justify-content-between align-items-start">

                            <div>

                                <h6>Issues Assigned to Employees</h6>

                                <h2>{{ $totalIssues }}</h2>
                            </div>


                            <div class="d-flex flex-column align-items-center">
                                @can('add issue')
                                    <a href="{{ route('issues.create', $company->id) }}" class="small-btn mb-4">
                                        +
                                    </a>
                                @endcan
                                <i class="fa fa-inbox fa-2x"></i>

                            </div>

                        </div>

                    </div>

                </div>
                {{-- ================= RFI ================= --}}
                <div class="col mb-3">

                    <div class="dashboard-card position-relative text-white">
                        @can('view rfi')
                            <a href="{{ route('rfis.index', $company->id) }}" class="stretched-link"></a>
                        @endcan
                        <div class="d-flex justify-content-between align-items-start">

                            <div>

                                <h6>Total RFI</h6>

                                <h2>{{ $totalRfis }}</h2>
                            </div>


                            <div class="d-flex flex-column align-items-center">
                                @can('add rfi')
                                    <a href="{{ route('rfis.create', $company->id) }}" class="small-btn mb-4">
                                        +
                                    </a>
                                @endcan
                                <i class="fa fa-file-o fa-2x lead-icon"></i>

                            </div>

                        </div>

                    </div>

                </div>
                {{-- ================= PURCHASE ORDERS ================= --}}
                <div class="col mb-3">

                    <div class="dashboard-card position-relative text-white">
                        @can('view post order')
                            <a href="{{ route('pos.index', $company->id) }}" class="stretched-link"></a>
                        @endcan
                        <div class="d-flex justify-content-between align-items-start">

                            <div>

                                <h6>Purchase Orders</h6>

                                <h2>{{ $totalPo }}</h2>
                            </div>


                            <div class="d-flex flex-column align-items-center">
                                @can('view post order')
                                    <a href="{{ route('pos.index', $company->id) }}" class="small-btn mb-4">
                                        +
                                    </a>
                                @endcan
                                <i class="fa fa-truck fa-2x lead-icon"></i>

                            </div>

                        </div>

                    </div>

                </div>
                <div class="col mb-3">

                    <div class="dashboard-card border-info position-relative">

                        @can('stockin')
                            <a href="{{ route('stocks.index', $company->id) }}" class="stretched-link"></a>
                        @endcan


                        <div class="d-flex justify-content-between align-items-start">

                            <div>


                                <h6>GRN</h6>
                                <h2>{{ $totalGrns }}</h2>
                            </div>



                            <div class="d-flex flex-column align-items-center">

                                @can('stockin')
                                    <a href="{{ route('stocks.index', $company->id) }}" class="small-btn mb-4">
                                        +
                                    </a>
                                @endcan

                                <i class="fa fa-download fa-2x lead-icon"></i>

                            </div>

                        </div>

                    </div>

                </div>
            </div>
            <div class="row mt-2">
                {{-- ================= CRITICAL ALERTS ================= --}}
                <div class="col-md-3 mb-3">

                    <div class="dashboard-card border-danger position-relative">

                        @can('stocks')
                            <a href="{{ route('stocks.index', $company->id) }}" class="stretched-link"></a>
                        @endcan

                        <div class="d-flex justify-content-between align-items-start">

                            <div>

                                <h5 class="mb-3">
                                    Critical Alerts
                                </h5>

                                <small>
                                    Low Stock Alerts:
                                    <b>{{ $criticalLowStock }}</b>
                                </small>

                                <br>

                                <small>
                                    Out Of Stock:
                                    <b>{{ $outOfStockAlerts }}</b>
                                </small>

                                <br>

                                <small>
                                    Overdue BOMs:
                                    <b>{{ $overdueBomAlerts }}</b>
                                </small>

                            </div>



                            <div class="d-flex flex-column align-items-center">

                                <i class="fa fa-exclamation-triangle fa-2x lead-icon"></i>

                            </div>

                        </div>

                    </div>

                </div>
                <div class="col-md-6 mb-3">

                    <div class="dashboard-card border-success position-relative">

                        @can('payments')
                            <a href="{{ route('payments.index', $company->id) }}" class="stretched-link"></a>
                        @endcan


                        <div class="d-flex justify-content-between align-items-start">

                            <div>

                                <h5 class="mb-3">
                                    Payment Summary
                                </h5>


                                <small>
                                    Total Receivable:
                                    <b>
                                        ₹ {{ number_format($orderValue, 2) }}
                                    </b>
                                </small>

                                <br>


                                <small>
                                    Payment Received:
                                    <b>
                                        ₹ {{ number_format($paidAmount, 2) }}
                                    </b>
                                </small>

                                <br>


                                <small>
                                    Payment Outstanding:
                                    <b>
                                        ₹ {{ number_format($dueAmount, 2) }}
                                    </b>
                                </small>

                            </div>



                            <div class="d-flex flex-column align-items-center">

                                @can('add payment')
                                    <a href="{{ route('payments.index', $company->id) }}" class="small-btn mb-4">
                                        +
                                    </a>
                                @endcan

                                <i class="fa fa-money fa-2x lead-icon"></i>

                            </div>

                        </div>

                    </div>

                </div>
                {{-- ================= PROCUREMENT ALERTS ================= --}}
                <div class="col-md-3 mb-3">

                    <div class="dashboard-card border-warning position-relative">

                        @can('manage rfi')
                            <a href="{{ route('rfis.index', $company->id) }}" class="stretched-link"></a>
                        @endcan

                        <div class="d-flex justify-content-between align-items-start">

                            <div>

                                <h5 class="mb-3">
                                    Procurement Alerts
                                </h5>

                                <small>
                                    Pending RFIs:
                                    <b>{{ $pendingApprovalRfis }}</b>
                                </small>

                                <br>

                                <small>
                                    Delayed POs:
                                    <b>{{ $delayedPos }}</b>
                                </small>

                                <br>

                                <small>
                                    Partial Receipts:
                                    <b>{{ $partialReceiptsPending }}</b>
                                </small>

                            </div>



                            <div class="d-flex flex-column align-items-center">

                                <i class="fa fa-bell fa-2x lead-icon"></i>

                            </div>

                        </div>

                    </div>

                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="dashboard-card position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <div>
                                <h5 class="mb-1 text-white">
                                    Production Tracker
                                </h5>

                                <small style="color:#9fb3c8;">
                                    Latest 5 Production Orders
                                </small>
                            </div>

                        </div>
                        <div id="productionAccordion">
                            @foreach($latestOrders as $order)
                                @php
                                    $orderCollapseId = 'orderCollapse' . $order->id;
                                    $stages = \App\Models\ProductionStage::where('company_id', $company->id)
                                        ->where(function ($q) use ($order) {
                                            $q->whereNull('order_id')
                                                ->orWhere('order_id', $order->id);
                                        })
                                        ->orderBy('sort_order')
                                        ->get()
                                        ->unique('name');
                                @endphp
                                {{-- ORDER --}}
                                <div class="mb-3 rounded overflow-hidden"
                                    style="background: rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
                                    <div class="accordion-toggle w-100 d-flex justify-content-between align-items-center"
                                        data-toggle="collapse" data-target="#{{ $orderCollapseId }}" aria-expanded="false"
                                        style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72);cursor:pointer !important;padding:18px;position:relative;z-index:9;">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="font-weight-bold text-white">
                                                    {{ $order->order_number }}
                                                </div>
                                                <small style="color:#9fb3c8;">
                                                    {{ $order->customer_name }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-weight-bold text-success">
                                                {{ round($order->progress_percent, 2) }}%
                                            </div>
                                            <small style="color:#9fb3c8;">
                                                Order Progress
                                            </small>
                                        </div>
                                    </div>
                                    <div id="{{ $orderCollapseId }}" class="collapse" data-parent="#productionAccordion">
                                        <div class="px-3 py-2">
                                            @foreach($order->items as $item)
                                                @php
                                                    $parts = $item->bomItems->pluck('part')->filter()->unique('id');
                                                    $totalWeight = $parts->sum('weightage');
                                                    $itemProgress = 0;
                                                    if ($totalWeight > 0) {
                                                        foreach ($parts as $part) {
                                                            $partProgress =
                                                                $part->getProgressForOrderItem(
                                                                    $item->id
                                                                );
                                                            $itemProgress += (
                                                                $partProgress *
                                                                ($part->weightage ?? 0)
                                                            );
                                                        }
                                                        $itemProgress =
                                                            round(
                                                                $itemProgress / $totalWeight,
                                                                2
                                                            );
                                                    }
                                                @endphp
                                                @php
                                                    /*
                                                    -----------------------------------------
                                                    DYNAMIC COLOR LOGIC
                                                    -----------------------------------------
                                                    */
                                                    if ($itemProgress < 40) {
                                                        /*
                                                        RED → ORANGE
                                                        */
                                                        if ($itemProgress < 10) {
                                                            $progressColor = '#ff1a1a';
                                                        } elseif ($itemProgress < 20) {
                                                            $progressColor = '#ff4d4d';
                                                        } elseif ($itemProgress < 30) {
                                                            $progressColor = '#ff704d';
                                                        } else {
                                                            $progressColor = '#ff944d';
                                                        }
                                                    } elseif ($itemProgress < 70) {
                                                        /*
                                                        YELLOW RANGE
                                                        */
                                                        if ($itemProgress < 50) {
                                                            $progressColor = '#ffc107';
                                                        } elseif ($itemProgress < 60) {
                                                            $progressColor = '#ffd54f';
                                                        } else {
                                                            $progressColor = '#ffe082';
                                                        }
                                                    } else {
                                                        /*
                                                        GREEN RANGE
                                                        */
                                                        if ($itemProgress < 80) {
                                                            $progressColor = '#66bb6a';
                                                        } elseif ($itemProgress < 90) {
                                                            $progressColor = '#43a047';
                                                        } else {
                                                            $progressColor = '#2e7d32';
                                                        }
                                                    }
                                                @endphp
                                                <div class="d-flex justify-content-between align-items-center mb-3 px-3 py-3 rounded"
                                                    style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72);border:1px solid rgba(255,255,255,0.06);">
                                                    {{-- ITEM NAME --}}
                                                    <div>
                                                        <div class="font-weight-bold text-white">{{ $item->item_name }}</div>
                                                    </div>
                                                    {{-- ITEM PROGRESS --}}
                                                    <div style="min-width:180px;">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <small class="text-light">Progress</small>
                                                            <small class="font-weight-bold"
                                                                style="color: {{ $progressColor }};">{{ $itemProgress }}%</small>
                                                        </div>
                                                        <div class="progress" style="height:8px;background:rgba(255,255,255,0.08);">
                                                            <div class="progress-bar"
                                                                style="width: {{ $itemProgress }}%;background: {{ $progressColor }};transition: all .3s ease;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @if($order->status != 'dispatched')

                                            @can('enter production')
                                                <div class="text-right mb-2 mr-1">
                                                    <a href="{{ route('orders.production.detail', ['company' => $company->id, 'order' => $order->id]) }}"
                                                        class="btn btn-sm px-4 py-2 production-action-btn"
                                                        style="z-index:9 !important;cursor: pointer!important;background:#00c853;color:#fff;border-radius:30px;font-weight:600;box-shadow:0 0 12px rgba(0,200,83,0.35);transition:all .3s ease;">
                                                        <i class="fa fa-cogs mr-1"></i>
                                                        Manage Production
                                                    </a>
                                                </div>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
    @push('styles')
        <style>
            .accordion-toggle {
                position: relative;
                z-index: 1;
            }

            .production-action-btn {
                position: relative;
                z-index: 99999 !important;
                pointer-events: auto !important;
            }

            /* ===== PAGE BACKGROUND ===== */
            .content-wrapper {
                background: linear-gradient(135deg, #081a2d, #0f3057);
                min-height: 100vh;
                color: #e5e7eb;
            }

            /* ===== DASHBOARD CARD SCROLLBAR ===== */

            .scrollable-table::-webkit-scrollbar {
                width: 10px;
            }

            .scrollable-table::-webkit-scrollbar-track {
                background: #081a2d;
            }

            .scrollable-table::-webkit-scrollbar-thumb {
                background: linear-gradient(to bottom,
                        #081a2d 0%,
                        #0f3057 60%,
                        #1b4f72 100%);
                border-radius: 8px;
                border: 2px solid #081a2d;
            }

            .scrollable-table::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(to bottom,
                        #0f3057,
                        #1b4f72);
            }

            .scrollable-table {
                max-height: 220px;
                overflow-y: auto;
            }

            .dashboard-card {
                color: #ffffff !important;
            }

            .dashboard-card p,
            .dashboard-card i {
                color: #ffffff !important;
            }

            /* ===== HEADERS ===== */
            .content-header h2 {
                color: #ffffff;
            }

            .content-header small {
                color: #9fb3c8;
            }

            .wrapper {
                background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%) !important;
            }

            /* ===== DASHBOARD CARDS ===== */
            .dashboard-card {
                border-radius: 16px;
                padding: 22px;
                color: #ffffff;
                position: relative;
                background: linear-gradient(135deg, #0f3057, #1b4f72);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.45);
                transition: all 0.35s ease;
                overflow: hidden;
            }

            .dashboard-card::after {
                content: '';
                position: absolute;
                inset: 0;
                background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.12), transparent 60%);
            }

            .dashboard-card:hover {
                transform: translateY(-6px) scale(1.01);
                box-shadow: 0 30px 60px rgba(0, 0, 0, 0.65);
            }

            .dashboard-card h3 {
                font-size: 34px;
                font-weight: 700;
            }

            .dashboard-card p {
                color: #dbeafe;
                margin-bottom: 0;
            }

            .dashboard-card i {
                position: absolute;
                right: 18px;
                bottom: 28px;
                opacity: 0.25;
                color: white;
            }

            /* ===== ADD (+) BUTTON ===== */
            .small-btn {
                position: absolute;
                right: 14px;
                top: 14px;
                background: rgba(255, 255, 255, 0.18);
                border-radius: 50%;
                padding: 6px 14px;
                color: #fff;
                font-size: 22px;
                text-decoration: none;
                transition: all 0.3s ease;
                z-index: 2;
            }

            .small-btn:hover {
                background: rgba(255, 255, 255, 0.35);
                transform: rotate(90deg);
            }

            /* ===== CARDS ===== */
            .card {
                background: rgba(15, 48, 87, 0.95);
                border-radius: 16px;
                border: none;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.45);
                color: #e5e7eb;
            }

            .card-header {
                background: transparent;
                border-bottom: 1px solid rgba(255, 255, 255, 0.12);
                font-weight: 600;
                color: #ffffff;
            }

            /* ===== TABLES ===== */
            .table {
                color: #e5e7eb;
            }

            .table th,
            .table td {
                border: 1px solid #dee2e626 !important;
            }

            .table thead th {
                background: #0f172a;
                color: #ffffff;
                border: none;
            }

            .table tbody tr {
                background: rgba(255, 255, 255, 0.02);
                transition: background 0.25s ease;
            }

            .table tbody tr:hover {
                background: rgba(255, 255, 255, 0.06);
            }

            /* ===== BADGES ===== */
            .badge-info {
                background: linear-gradient(135deg, #2563eb, #38bdf8);
            }

            /* ===== BUTTONS ===== */
            .btn-success {
                background: linear-gradient(135deg, #16a34a, #22c55e);
                border: none;
            }

            /* ===== CHART CANVAS ===== */
            canvas {
                filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.4));
            }
        </style>
    @endpush