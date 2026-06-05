<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $order->transaction_id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        .receipt {
            background-color: #fff;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: left;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-sm { font-size: 12px; }
        .text-xs { font-size: 10px; }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
        }

        @media print {
            body {
                background-color: transparent;
                padding: 0;
            }
            .receipt {
                max-width: 100%;
                box-shadow: none;
                padding: 0;
            }
            @page {
                margin: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="receipt">
        <div class="text-center">
            <h2 style="margin:0;">QuickDine</h2>
            <p class="text-sm" style="margin:4px 0;">Jl. Contoh Restoran No. 123</p>
            <p class="text-sm" style="margin:4px 0;">Telp: 0812-3456-7890</p>
        </div>

        <div class="divider"></div>

        <div class="text-sm">
            <table>
                <tr>
                    <td>Waktu:</td>
                    <td class="text-right">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>ID TRX:</td>
                    <td class="text-right">{{ $order->transaction_id }}</td>
                </tr>
                <tr>
                    <td>Tipe:</td>
                    <td class="text-right">{{ $order->order_type == 'dine_in' ? 'Dine In' : 'Take Away' }}</td>
                </tr>
                @if($order->table)
                <tr>
                    <td>Meja:</td>
                    <td class="text-right">{{ $order->table->table_number }}</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="divider"></div>

        <div class="text-sm">
            <table>
                <tr class="font-bold">
                    <td style="width: 50%;">Item</td>
                    <td style="width: 15%; text-align: center;">Qty</td>
                    <td style="width: 35%;" class="text-right">Harga</td>
                </tr>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->menu->name ?? 'Menu Dihapus' }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        <div class="divider"></div>

        <div class="text-sm">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">{{ number_format($order->total_price - ($order->total_price / 1.1 * 0.1), 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Pajak (10%):</td>
                    <td class="text-right">{{ number_format($order->total_price / 1.1 * 0.1, 0, ',', '.') }}</td>
                </tr>
                <tr class="font-bold" style="font-size: 14px;">
                    <td style="padding-top: 8px;">TOTAL:</td>
                    <td class="text-right" style="padding-top: 8px;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="divider"></div>

        <div class="text-sm">
            <table>
                <tr>
                    <td>Pembayaran:</td>
                    <td class="text-right" style="text-transform: uppercase;">{{ $order->payment_method == 'cash' ? 'TUNAI/CASH' : ($order->payment_method == 'qris' ? 'QRIS' : $order->payment_method) }}</td>
                </tr>
                <tr>
                    <td>Status:</td>
                    <td class="text-right" style="text-transform: uppercase;">{{ $order->payment_status == 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}</td>
                </tr>
            </table>
        </div>

        <div class="divider" style="margin-top: 16px;"></div>

        <div class="text-center" style="margin-top: 16px;">
            <p class="font-bold text-sm" style="margin:4px 0;">Terima Kasih</p>
            <p class="text-xs" style="margin:4px 0;">Silakan datang kembali</p>
        </div>
    </div>

    <script>
        window.onafterprint = function() {
            // Optional: close the window after printing
        };
    </script>
</body>
</html>
