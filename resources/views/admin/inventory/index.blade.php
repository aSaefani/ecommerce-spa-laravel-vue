@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">Log Inventaris</h1>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Deskripsi</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                    <tr>
                        <td>{{ $logs->firstItem() + $index }}</td>
                        <td>{{ $log->product->name ?? '-' }}</td>
                        <td>
                            @if($log->type === 'in' || $log->type === 'masuk')
                                <span class="badge bg-success">Masuk</span>
                            @else
                                <span class="badge bg-danger">Keluar</span>
                            @endif
                        </td>
                        <td>{{ $log->quantity }}</td>
                        <td>{{ $log->description }}</td>
                        <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data log inventaris.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $logs->links() }}
    </div>
</div>
@endsection
