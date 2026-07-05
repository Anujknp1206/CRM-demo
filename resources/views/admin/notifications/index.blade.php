@extends('admin.layouts.master')

@section('content')

    {{-- ===================== --}}
    {{-- Content Header --}}
    {{-- ===================== --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $label }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">{{$label}}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== --}}
    {{-- Main Content --}}
    {{-- ===================== --}}
    <section class="content">
        <div class="container-fluid">

            <div class="card card-teal">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Notification List</h3>

                    <div class="d-flex gap-2 ml-auto">

                        {{-- Back Button --}}
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-success">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>

                        {{-- Mark All Read Button --}}
                        @if(auth()->user()->unreadNotifications->count())
                            <form method="POST" action="{{ route('notifications.readAll') }}" autocomplete="off">
                                @csrf
                                <button class="btn btn-sm btn-success">
                                    <i class="fa fa-check"></i> Mark All as Read
                                </button>
                            </form>
                        @endif

                    </div>
                </div>

                <div class="card-body p-0">

                    @forelse($notifications as $n)
                        <div class="p-3 border-bottom d-flex align-items-start
                                                                    {{ $n->read_at ? '' : 'bg-light' }}">

                            <div class="mr-3 pt-1">
                                <i
                                    class="fa fa-bell
                                                                            {{ $n->read_at ? 'text-muted' : 'text-warning' }}"></i>
                            </div>

                            <div class="flex-fill">
                                <a href="{{ $n->data['url'] ?? '#' }}" class="text-dark"
                                    onclick="event.preventDefault();
                                                                           document.getElementById('read-{{ $n->id }}').submit();">

                                    <strong>{{ $n->data['title'] }}</strong>

                                    <div class="text-muted">
                                        {{ $n->data['message'] }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $n->created_at->diffForHumans() }}
                                    </small>
                                </a>

                                <form id="read-{{ $n->id }}" method="POST" autocomplete="off"
                                    action="{{ route('notifications.read', $n->id) }}">
                                    @csrf
                                </form>
                            </div>
                        </div>

                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="fa fa-bell-slash fa-2x mb-2 d-block"></i>
                            No notifications found
                        </div>
                    @endforelse

                </div>

                @if($notifications->hasPages())
                    <div class="card-footer clearfix">
                        {{ $notifications->links() }}
                    </div>
                @endif

            </div>

        </div>
    </section>

@endsection