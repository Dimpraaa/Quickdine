<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; padding: 0; font-size: 24px; }
        .header p { margin: 5px 0; color: #555; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { margin-top: 20px; width: 300px; float: right; }
        .summary-table { width: 100%; }
        .summary-table td { padding: 5px 0; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="header">
        <h1>QUICKDINE</h1>
        <p>Laporan Penjualan Restoran</p>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>ID Transaksi</th>
                <th>Tipe Pesanan</th>
                <th>Metode</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $index => $order)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $order->transaction_id }}</td>
                <td>{{ $order->order_type == 'dine_in' ? 'Dine In (Meja '.$order->table->table_number.')' : 'Take Away' }}</td>
                <td style="text-transform: uppercase;">{{ $order->payment_method }}</td>
                <td class="text-right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table class="summary-table">
            <tr>
                <td><strong>Total Transaksi:</strong></td>
                <td class="text-right">{{ $orders->count() }}</td>
            </tr>
            <tr>
                <td><strong>Total Pendapatan:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>
    <div class="clear"></div>

    <div style="margin-top: 50px; text-align: right; padding-right: 20px;">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
