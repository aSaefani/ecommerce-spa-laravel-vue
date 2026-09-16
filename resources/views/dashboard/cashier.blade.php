@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
<h2 class="mb-4">Dashboard Kasir</h2>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-muted">Pesanan Pending</h6>
                <h2 class="text-warning">{{ $pendingOrders }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-muted">Pesanan Hari Ini</h6>
                <h2>{{ $todayOrders }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-muted">Pendapatan Hari Ini</h6>
                <h2 class="text-success">Rp{{ number_format($todayRevenue, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <a href="{{ route('cashier.orders') }}" class="btn btn-primary">Kelola Pesanan</a>
    </div>
</div>
@endsection
