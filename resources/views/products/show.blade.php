@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="row">
    <div class="col-md-6">
        @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded" alt="{{ $product->name }}">
        @else
            <div class="bg-secondary d-flex align-items-center justify-content-center rounded" style="height: 400px;">
                <i class="bi bi-image text-white" style="font-size: 5rem;"></i>
            </div>
        @endif
    </div>
    <div class="col-md-6">
        <h2>{{ $product->name }}</h2>
        <p class="text-muted">{{ $product->category->name }}</p>
        <h3 class="text-primary">Rp{{ number_format($product->price, 0, ',', '.') }}</h3>
        
        <div class="my-3">
            <span class="badge @if($product->isInStock()) bg-success @else bg-danger @endif" style="font-size: 1rem;">
                @if($product->isInStock()) Stok: {{ $product->stock }} @else Habis @endif
            </span>
        </div>

        <p class="mb-4">{{ $product->description }}</p>

        @if($product->isInStock())
            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="mb-3">
                    <label for="quantity" class="form-label">Jumlah</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock }}" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100">Tambah ke Keranjang</button>
            </form>
        @else
            <button class="btn btn-secondary btn-lg w-100" disabled>Stok Habis</button>
        @endif

        @if(auth()->check() && auth()->user()->isCustomer())
            <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="mb-3">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100">
                    <i class="bi bi-heart"></i> Tambah / Hapus Wishlist
                </button>
            </form>
        @endif

        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary mt-3 w-100">Kembali</a>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-6">
        <h4>Ulasan Pelanggan ({{ $product->getReviewCount() }})</h4>
        <div class="mb-3">
            <span class="badge bg-warning text-dark" style="font-size: 1.1rem;">
                ★ {{ number_format($product->getAverageRating(), 1) }} / 5.0
            </span>
        </div>

        @forelse($product->reviews()->latest()->get() as $review)
            <div class="card mb-2">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $review->user->name }}</strong>
                        <span class="text-warning">★ {{ $review->rating }}/5</span>
                    </div>
                    <p class="mb-0 text-muted small mt-1">{{ $review->comment }}</p>
                </div>
            </div>
        @empty
            <p class="text-muted">Belum ada ulasan.</p>
        @endforelse
    </div>

    @if(auth()->check() && auth()->user()->isCustomer())
        <div class="col-md-6">
            <h4>Beri Ulasan</h4>
            <form action="{{ route('reviews.store', $product) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Rating</label>
                    <select name="rating" class="form-select" required>
                        <option value="5">5 - Sangat Puas</option>
                        <option value="4">4 - Puas</option>
                        <option value="3">3 - Cukup</option>
                        <option value="2">2 - Kurang</option>
                        <option value="1">1 - Kecewa</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Komentar</label>
                    <textarea name="comment" class="form-control" rows="3" placeholder="Tulis pengalaman belanja..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
            </form>
        </div>
    @endif
</div>
@endsection
