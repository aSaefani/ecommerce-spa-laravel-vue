@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">Laporan Stok</h1>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Stok Saat Ini</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $index => $product)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td>
                            @if($product->stock > 0)
                                <span class="badge bg-success">{{ $product->stock }}</span>
                            @elseif($product->stock == 0)
                                <span class="badge bg-warning">{{ $product->stock }}</span>
                            @else
                                <span class="badge bg-danger">{{ $product->stock }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data produk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
