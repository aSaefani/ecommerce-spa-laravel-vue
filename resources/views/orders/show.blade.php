@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>{{ $order->order_number }}</h2>
        <p class="text-muted">{{ $order->created_at->format('d F Y H:i') }}</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Item Pesanan</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Ringkasan</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Diskon:</span>
                    <span>-Rp{{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <strong>Total:</strong>
                    <strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Poin Earned:</span>
                    <span>{{ $order->points_earned }}</span>
                </div>
                <hr>
                <div class="mb-3">
                    <strong>Status:</strong>
                    <span class="badge @if($order->status === 'paid') bg-success @elseif($order->status === 'pending') bg-warning @else bg-danger @endif" style="font-size: 1rem;">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                @if($order->status === 'pending')
                    <p class="text-muted small">Menunggu pembayaran. Silakan selesaikan pembayaran via GoPay.</p>
                    @if(isset($snapToken) && $snapToken)
                        <button id="pay-button" class="btn btn-primary w-100 mb-2">Bayar dengan GoPay</button>
                    @else
                        <div class="alert alert-secondary small">
                            <strong>Simulasi Sandbox:</strong>
                            <form action="{{ route('cashier.confirmPayment', $order) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success w-100">Simulasikan Sukses Bayar</button>
                            </form>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<a href="{{ route('orders.my') }}" class="btn btn-outline-secondary mt-3">Kembali</a>

@if(isset($snapToken) && $snapToken)
    @push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        document.getElementById('pay-button').onclick = function() {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    window.location.reload();
                },
                onPending: function(result) {
                    window.location.reload();
                },
                onError: function(result) {
                    alert('Pembayaran gagal!');
                }
            });
        };
    </script>
    @endpush
@endif
@endsection
