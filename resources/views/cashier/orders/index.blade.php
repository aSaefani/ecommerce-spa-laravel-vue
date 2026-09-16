@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">Daftar Pesanan</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

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
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $index => $order)
                    <tr>
                        <td>{{ $orders->firstItem() + $index }}</td>
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
                        <td>
                            <a href="{{ route('cashier.orders.show', $order) }}" class="btn btn-sm btn-info">Lihat</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data pesanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $orders->links() }}
    </div>
</div>
@endsection
