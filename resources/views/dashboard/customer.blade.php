@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h2 class="mb-4">Selamat datang, {{ auth()->user()->name }}!</h2>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-muted">Saldo Poin</h6>
                <h2 class="text-primary">{{ $balance }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Pesanan</h6>
                <h2>{{ $recentOrders->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">Belanja Sekarang</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Pesanan Terakhir</h5>
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
                    @forelse($recentOrders as $order)
                        <tr>
                            <td><a href="{{ route('orders.show', $order) }}">{{ $order->order_number }}</a></td>
                            <td>Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge @if($order->status === 'paid') bg-success @elseif($order->status === 'pending') bg-warning @else bg-danger @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d-m-Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
