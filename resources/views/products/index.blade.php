@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>Produk</h2>
    </div>
    <div class="col-md-6">
        <form action="{{ route('products.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Cari produk...">
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>
    </div>
</div>

<div class="row">
    @forelse($products as $product)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 250px; object-fit: cover;">
                @else
                    <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 250px;">
                        <i class="bi bi-image text-white" style="font-size: 3rem;"></i>
                    </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text text-muted">{{ $product->category->name }}</p>
                    <p class="card-text">{{ Str::limit($product->description, 100) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 mb-0">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                        <span class="badge @if($product->isInStock()) bg-success @else bg-danger @endif">
                            @if($product->isInStock()) Stok: {{ $product->stock }} @else Habis @endif
                        </span>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('products.show', $product) }}" class="btn btn-primary w-100">Lihat Detail</a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <p class="text-center text-muted">Tidak ada produk.</p>
        </div>
    @endforelse
</div>

{{ $products->links() }}
@endsection
