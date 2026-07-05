@extends('admin.layouts.master')

@section('content')

  <div class="content-header">
    <div class="container-fluid">
      <h2 class="mb-0 font-weight-bold">{{ $title }}</h2>
      <small>System Overview & Administration</small>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">
      <div class="row">

        {{-- USERS --}}
        <div class="col-md-3 mb-3">
          <div class="dashboard-card position-relative">
            @can('add user')
              <a href="{{ route('users.create') }}" class="small-btn">+</a>
            @endcan
            <h3>{{ $summary['users'] }}</h3>
            <p>Total Users</p>

            <a href="{{ route('users.index') }}" class="stretched-link"></a>
            <i class="fa fa-user fa-2x"></i>
          </div>
        </div>

        {{-- COMPANIES --}}
        <div class="col-md-3 mb-3">
          <div class="dashboard-card position-relative">
            @can('add company')

              <a href="{{ route('companies.create') }}" class="small-btn">+</a>
            @endcan
            <h3>{{ $summary['companies'] }}</h3>
            <p>Total Companies</p>

            <a href="{{ route('companies.index') }}" class="stretched-link"></a>
            <i class="fa fa-building fa-2x"></i>
          </div>
        </div>

        {{-- MACHINES --}}
        <div class="col-md-3 mb-3">
          <div class="dashboard-card position-relative">
            @can('add machines')
              <a href="{{ route('machines.create') }}" class="small-btn">+</a>
            @endcan
            <h3 style="color:white;">{{ $summary['machines'] }}</h3>
            <p>Total Machines</p>

            <a href="{{ route('machines.index') }}" class="stretched-link"></a>
            <i class="fa fa-cogs fa-2x"></i>
          </div>
        </div>

        {{-- COMPONENTS --}}
        <div class="col-md-3 mb-3">
          <div class="dashboard-card position-relative">
            @can('add components')
              <a href="{{ route('components.create') }}" class="small-btn">+</a>
            @endcan
            <h3>{{ $summary['components'] }}</h3>
            <p>Total Components</p>

            <a href="{{ route('components.index') }}" class="stretched-link"></a>
            <i class="fa fa-puzzle-piece fa-2x"></i>
          </div>
        </div>

      </div>
    </div>
  </section>
@endsection

@push('styles')
  <style>
    /* ===== PAGE BACKGROUND ===== */
    .content-wrapper {
      background: linear-gradient(135deg, #081a2d, #0f3057);
      min-height: 100vh;
      color: #e5e7eb;
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
      min-height: 130px;
      padding: 22px;
      box-sizing: border-box;
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
      bottom: 18px;
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