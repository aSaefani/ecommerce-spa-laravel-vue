@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Detail Pesanan</h1>
        <a href="{{ route('cashier.orders.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Pesanan: {{ $order->order_number }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>Pelanggan:</strong> {{ $order->user->name ?? '-' }}</p>
                    <p><strong>Email:</strong> {{ $order->user->email ?? '-' }}</p>
                    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</p>
                    <p>
                        <strong>Status:</strong>
                        @if($order->status === 'pending')
                            <span class="badge bg-warning">Tertunda</span>
                        @elseif($order->status === 'paid')
                            <span class="badge bg-success">Dibayar</span>
                        @elseif($order->status === 'cancelled')
                            <span class="badge bg-danger">Dibatalkan</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Item Pesanan</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? '-' }}</td>
                                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada item pesanan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Ringkasan Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>Rp {{ number_format($order->subtotal ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Diskon:</span>
                        <span>Rp {{ number_format($order->discount ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Ongkir:</span>
                        <span>Rp {{ number_format($order->shipping ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
                    </div>

                    @if($order->status === 'pending')
                        <form action="{{ route('cashier.confirmPayment', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">Konfirmasi Pembayaran</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
