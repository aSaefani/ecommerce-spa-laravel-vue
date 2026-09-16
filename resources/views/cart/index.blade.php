@extends('layouts.app')

@section('title', 'Keranjang')

@section('content')
<h2 class="mb-4">Keranjang Belanja</h2>

@if($items->isEmpty())
    <div class="alert alert-info">
        Keranjang Anda kosong. <a href="{{ route('products.index') }}">Belanja sekarang</a>
    </div>
@else
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>
                                        <a href="{{ route('products.show', $item->product) }}">{{ $item->product->name }}</a>
                                    </td>
                                    <td>Rp{{ number_format($item->product->price, 0, ',', '.') }}</td>
                                    <td>
                                        <form action="{{ route('cart.update', $item) }}" method="POST" class="d-flex gap-1">
                                            @csrf
                                            <input type="number" name="quantity" class="form-control" style="width: 60px;" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" required>
                                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                        </form>
                                    </td>
                                    <td>Rp{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</td>
                                    <td>
                                        <form action="{{ route('cart.remove', $item) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Ringkasan</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>Rp{{ number_format($cart->getTotal(), 0, ',', '.') }}</span>
                    </div>
                    <hr>
                    <a href="{{ route('checkout') }}" class="btn btn-primary w-100">Lanjut ke Checkout</a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">Belanja Lagi</a>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
