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
            display: flex;
            justify-content: center;
        }

        .receipt {
            background-color: #fff;
            width: 300px; /* Lebar standar printer thermal 80mm */
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-sm { font-size: 12px; }
        .text-xs { font-size: 10px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 16px; }
        .mt-4 { margin-top: 16px; }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .item-name { width: 60%; }
        .item-qty { width: 10%; text-align: center; }
        .item-total { width: 30%; text-align: right; }

        @media print {
            body {
                background-color: transparent;
                padding: 0;
                display: block;
            }
            .receipt {
                width: 100%;
                max-width: 300px;
                box-shadow: none;
                margin: 0 auto;
                padding: 0;
            }
            /* Hilangkan elemen browser saat print */
            @page {
                margin: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="receipt">
        <div class="text-center mb-4">
            <h2 style="margin:0;">QuickDine</h2>
            <p class="text-sm" style="margin:4px 0;">Jl. Contoh Restoran No. 123</p>
            <p class="text-sm" style="margin:4px 0;">Telp: 0812-3456-7890</p>
        </div>

        <div class="divider"></div>

        <div class="text-sm mb-2">
            <div style="display:flex; justify-content:space-between;">
                <span>Waktu:</span>
                <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span>ID TRX:</span>
                <span>{{ $order->transaction_id }}</span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span>Tipe:</span>
                <span>{{ $order->order_type == 'dine_in' ? 'Dine In' : 'Take Away' }}</span>
            </div>
            @if($order->table)
            <div style="display:flex; justify-content:space-between;">
                <span>Meja:</span>
                <span>{{ $order->table->table_number }}</span>
            </div>
            @endif
        </div>

        <div class="divider"></div>

        <div class="text-sm font-bold item-row" style="margin-bottom: 8px;">
            <div class="item-name">Item</div>
            <div class="item-qty">Qty</div>
            <div class="item-total">Harga</div>
        </div>

        <div class="text-sm">
            @foreach($order->items as $item)
            <div class="item-row">
                <div class="item-name">{{ $item->menu->name ?? 'Menu Dihapus' }}</div>
                <div class="item-qty">{{ $item->quantity }}</div>
                <div class="item-total">{{ number_format($item->subtotal, 0, ',', '.') }}</div>
            </div>
            @endforeach
        </div>

        <div class="divider"></div>

        <div class="text-sm">
            <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                <span>Subtotal:</span>
                <span>{{ number_format($order->total_price - ($order->total_price / 1.1 * 0.1), 0, ',', '.') }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                <span>Pajak (10%):</span>
                <span>{{ number_format($order->total_price / 1.1 * 0.1, 0, ',', '.') }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-top:8px; font-weight:bold; font-size:14px;">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="text-sm">
            <div style="display:flex; justify-content:space-between;">
                <span>Metode Pembayaran:</span>
                <span style="text-transform: uppercase;">{{ $order->payment_method == 'cash' ? 'TUNAI / CASH' : ($order->payment_method == 'qris' ? 'TRANSFER/QRIS' : $order->payment_method) }}</span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span>Status:</span>
                <span style="text-transform: uppercase;">{{ $order->payment_status == 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}</span>
            </div>
        </div>

        <div class="divider mt-4"></div>

        <div class="text-center mt-4">
            <p class="font-bold text-sm" style="margin:4px 0;">Terima Kasih</p>
            <p class="text-xs" style="margin:4px 0;">Silakan datang kembali</p>
        </div>
    </div>

    <!-- Script to close window after printing if opened as a popup -->
    <script>
        window.onafterprint = function() {
            // Optional: close the window after printing
            // window.close();
        };
    </script>
</body>
</html>
