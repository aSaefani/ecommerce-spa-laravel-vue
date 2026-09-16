@extends('layouts.app')

@section('title', 'Poin Saya')

@section('content')
<h2 class="mb-4">Poin Reward</h2>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Poin</h6>
                <h2 class="text-primary">{{ $balance }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Riwayat Poin</h5>
        <div class="table-responsive">
            <table class="table">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Poin</th>
                        <th>Keterangan</th>
                        <th>Berlaku Sampai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
                            <td>
                                <span class="badge @if($log->type === 'earn') bg-success @elseif($log->type === 'redeem') bg-warning @else bg-danger @endif">
                                    {{ ucfirst($log->type) }}
                                </span>
                            </td>
                            <td>{{ $log->points }}</td>
                            <td>{{ $log->description }}</td>
                            <td>
                                @if($log->expired_at)
                                    {{ $log->expired_at->format('d-m-Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada riwayat poin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{ $history->links() }}
@endsection
