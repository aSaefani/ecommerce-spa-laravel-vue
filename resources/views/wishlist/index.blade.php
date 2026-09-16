@extends('layouts.app')

@section('title', 'Wishlist')

@section('content')
<h2 class="mb-4">Wishlist Saya</h2>

@if($wishlists->isEmpty())
    <div class="alert alert-info">
        Wishlist kosong. <a href="{{ route('products.index') }}">Belanja sekarang</a>
    </div>
@else
    <div class="row">
        @foreach($wishlists as $item)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($item->product->image)
                        <img src="{{ asset('storage/' . $item->product->image) }}" class="card-img-top" alt="{{ $item->product->name }}" style="height: 250px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 250px;">
                            <i class="bi bi-image text-white" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $item->product->name }}</h5>
                        <p class="card-text text-muted">{{ $item->product->category->name }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0">Rp{{ number_format($item->product->price, 0, ',', '.') }}</span>
                            <span class="badge @if($item->product->isInStock()) bg-success @else bg-danger @endif">
                                @if($item->product->isInStock()) Stok: {{ $item->product->stock }} @else Habis @endif
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <form action="{{ route('cart.add') }}" method="POST" class="d-inline w-100">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">Tambah ke Keranjang</button>
                        </form>
                        <form action="{{ route('wishlist.toggle', $item->product) }}" method="POST" class="d-inline w-100">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $wishlists->links() }}
@endif
@endsection
