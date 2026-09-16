@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<h2 class="mb-4">Dashboard Admin</h2>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Pesanan</h6>
                <h2>{{ $totalOrders }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Pendapatan</h6>
                <h2 class="text-success">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Pelanggan</h6>
                <h2>{{ $totalCustomers }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Produk</h6>
                <h2>{{ $totalProducts }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center bg-primary text-white">
            <div class="card-body">
                <h6>Pendapatan Hari Ini</h6>
                <h2>Rp{{ number_format($dailyRevenue, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Pesanan Terbaru</h5>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead class="table-light">
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge @if($order->status === 'paid') bg-success @elseif($order->status === 'pending') bg-warning @else bg-danger @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
