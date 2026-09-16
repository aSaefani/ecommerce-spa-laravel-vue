@extends('layouts.app')

@section('title', 'Pesanan')

@section('content')
<h2 class="mb-4">Pesanan Saya</h2>

<div class="table-responsive">
    <table class="table">
        <thead class="table-light">
            <tr>
                <th>No. Pesanan</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                    <td>Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge @if($order->status === 'paid') bg-success @elseif($order->status === 'pending') bg-warning @else bg-danger @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-primary">Lihat</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Belum ada pesanan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $orders->links() }}
@endsection
