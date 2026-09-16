<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ecommerce')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --primary: #FF6B35; --dark: #1a1a1a; }
        body { background: #f8f9fa; }
        .navbar { background: var(--dark) !important; }
        .btn-primary { background: var(--primary) !important; border: none; }
        .btn-primary:hover { background: #e55a2b !important; }
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .badge-primary { background: var(--primary) !important; }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">Ecommerce</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">Produk</a></li>
                    @auth
                        @if(auth()->user()->isCustomer())
                            <li class="nav-item"><a class="nav-link" href="{{ route('wishlist.index') }}"><i class="bi bi-heart"></i> Wishlist</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}"><i class="bi bi-cart"></i> Keranjang</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('orders.my') }}">Pesanan</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('points') }}">Poin</a></li>
                        @endif
                        @if(auth()->user()->isCashier())
                            <li class="nav-item"><a class="nav-link" href="{{ route('cashier.dashboard') }}">Dashboard Kasir</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('cashier.orders') }}">Pesanan</a></li>
                        @endif
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Admin</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.categories.index') }}">Kategori</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.products.index') }}">Produk</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.users') }}">Pengguna</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.reports.daily') }}">Laporan</a></li>
                                </ul>
                            </li>
                        @endif
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="nav-link border-0 bg-transparent">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <p>&copy; 2026 Ecommerce. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
