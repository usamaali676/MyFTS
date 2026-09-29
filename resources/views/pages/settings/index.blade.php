@extends('layouts.dashboard')
@section('css')
    <style>
        .settings-card {
            transition: transform .15s ease, box-shadow .15s ease;
            text-decoration: none;
            display: block;
            height: 100%;
        }
        .settings-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, .12);
        }
        .settings-card .card-body {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .settings-card .settings-icon {
            width: 52px;
            height: 52px;
            min-width: 52px;
            border-radius: .75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            background: rgba(105, 108, 255, .12);
            color: #696cff;
        }
        .settings-card .settings-count {
            font-size: .8125rem;
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3 mb-1"><span class="text-muted fw-light">Settings /</span> All</h4>
            <p class="mb-4 text-muted">Manage the reference data used across leads, sales and invoices.</p>

            <div class="row g-4">
                @foreach($cards as $card)
                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route($card['route']) }}" class="card settings-card">
                            <div class="card-body">
                                <div class="settings-icon">
                                    <i class="mdi {{ $card['icon'] }}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 text-body">{{ $card['label'] }}</h6>
                                    <span class="text-muted settings-count">{{ $card['count'] }} {{ Str::plural('record', $card['count']) }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="content-backdrop fade"></div>
    </div>
@endsection
