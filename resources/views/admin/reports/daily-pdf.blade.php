<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            margin: 3px 0;
        }
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .summary-item {
            display: table-cell;
            width: 25%;
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
        }
        .summary-item h3 {
            font-size: 11px;
            margin-bottom: 8px;
            color: #666;
        }
        .summary-item .value {
            font-size: 16px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #333;
            color: #fff;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #333;
        }
        table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #999;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 100%;
                margin: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Ecommerce</h1>
            <p>Laporan Harian</p>
            <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
        </div>

        <div class="summary">
            <div class="summary-item">
                <h3>Total Pesanan</h3>
                <div class="value">{{ $totalOrders }}</div>
            </div>
            <div class="summary-item">
                <h3>Total Penjualan</h3>
                <div class="value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <h3>Total Diskon</h3>
                <div class="value">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <h3>Total Poin</h3>
                <div class="value">{{ $totalPoints }}</div>
            </div>
        </div>

        <h3 style="margin-bottom: 10px;">Detail Pesanan</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $index => $order)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->user->name ?? '-' }}</td>
                        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td>
                            @if($order->status === 'pending')
                                Tertunda
                            @elseif($order->status === 'paid')
                                Dibayar
                            @elseif($order->status === 'cancelled')
                                Dibatalkan
                            @else
                                {{ ucfirst($order->status) }}
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Tidak ada data pesanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <p>Laporan ini dicetak pada {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
