@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="row align-items-center min-vh-50">
    <div class="col-md-6">
        <h1 class="display-4 fw-bold">Selamat Datang di Ecommerce</h1>
        <p class="lead text-muted">Belanja produk berkualitas dengan harga terbaik. Dapatkan poin reward untuk setiap pembelian.</p>
        <div class="gap-3 d-flex">
            @guest
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftar Sekarang</a>
                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">Login</a>
            @else
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">Belanja Sekarang</a>
            @endguest
        </div>
    </div>
    <div class="col-md-6">
        <div class="bg-light p-5 rounded">
            <h3 class="mb-4">Fitur Kami</h3>
            <ul class="list-group">
                <li class="list-group-item border-0"><i class="bi bi-check-circle text-success"></i> Produk berkualitas pilihan</li>
                <li class="list-group-item border-0"><i class="bi bi-check-circle text-success"></i> Sistem pembayaran GoPay aman</li>
                <li class="list-group-item border-0"><i class="bi bi-check-circle text-success"></i> Poin reward setiap pembelian</li>
                <li class="list-group-item border-0"><i class="bi bi-check-circle text-success"></i> Pelacakan pesanan real-time</li>
                <li class="list-group-item border-0"><i class="bi bi-check-circle text-success"></i> Layanan pelanggan 24/7</li>
            </ul>
        </div>
    </div>
</div>
@endsection
