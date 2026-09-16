@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<h2 class="mb-4">Checkout</h2>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Item Pembelian</h5>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
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
                                    <td>Rp{{ number_format($item->product->price, 0, ',', '.') }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>Rp{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Kode Diskon</h5>
                <form action="{{ route('checkout.process') }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="discount_code" class="form-control" placeholder="Masukkan kode diskon (opsional)">
                        <button type="submit" class="btn btn-primary">Proses Pesanan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card sticky-top">
            <div class="card-body">
                <h5 class="card-title">Ringkasan Pesanan</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span>Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Diskon:</span>
                    <span id="discount-text">Rp0</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <strong>Total:</strong>
                    <strong id="total-text">Rp{{ number_format($subtotal, 0, ',', '.') }}</strong>
                </div>
                <div class="alert alert-info small mb-3">
                    <strong>Poin Reward:</strong> Anda akan mendapat ~{{ number_format($subtotal * 0.01, 0, ',', '.') }} poin
                </div>
                <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100">Kembali ke Keranjang</a>
            </div>
        </div>
    </div>
</div>
@endsection
