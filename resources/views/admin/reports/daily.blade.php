@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">Laporan Harian</h1>

    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('admin.reports.daily') }}" method="GET" class="row g-2">
                <div class="col-auto">
                    <input type="date" name="date" class="form-control" value="{{ $date }}" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.reports.daily.pdf', ['date' => $date]) }}" class="btn btn-danger" target="_blank">Download PDF</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Pesanan</h5>
                    <h3>{{ $totalOrders }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Penjualan</h5>
                    <h3>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Diskon</h5>
                    <h3>Rp {{ number_format($totalDiscount, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Poin</h5>
                    <h3>{{ $totalPoints }}</h3>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mb-3">Detail Pesanan</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nomor Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $index => $order)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->user->name ?? '-' }}</td>
                        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td>
                            @if($order->status === 'pending')
                                <span class="badge bg-warning">Tertunda</span>
                            @elseif($order->status === 'paid')
                                <span class="badge bg-success">Dibayar</span>
                            @elseif($order->status === 'cancelled')
                                <span class="badge bg-danger">Dibatalkan</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data pesanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
