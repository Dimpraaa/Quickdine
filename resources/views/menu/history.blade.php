<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Riwayat Pesanan - QuickDine</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#B27C44',
                        primaryDark: '#8B5A2B',
                        secondary: '#352214',
                        bgLight: '#FDFBF7',
                    }
                }
            }
        }
    </script>
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* CSS Khusus Cetak Struk per Pesanan */
        @media print {
            body * {
                visibility: hidden;
            }

            .print-active,
            .print-active * {
                visibility: visible;
            }

            .print-active {
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm;
                /* Standar lebar kertas printer thermal kasir */
                padding: 10px;
                margin: 0;
                box-shadow: none !important;
                border: none !important;
                background-color: white !important;
                color: black !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body class="bg-bgLight font-sans antialiased text-secondary pb-10 selection:bg-primary selection:text-white">

    <!-- HEADER -->
    <header class="max-w-md mx-auto bg-secondary sticky top-0 z-10 shadow-md rounded-b-2xl overflow-hidden shrink-0 no-print">
        <div class="px-4 py-4 flex justify-between items-center gap-2">
            <div class="flex items-center gap-3">
                <a href="javascript:history.back()" class="text-[#FDFBF7] hover:text-white w-8 h-8 flex justify-center items-center rounded-full bg-white/10 border border-white/20 transition-colors">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <div class="bg-primary w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-primary/30">
                    <i class="fas fa-history text-white text-lg"></i>
                </div>
                <div class="flex flex-col justify-center">
                    <h1 class="text-base font-black text-white tracking-tight leading-tight">Riwayat</h1>
                    <p class="text-[10px] text-white/60 font-medium tracking-widest uppercase">Pesanan Anda</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <div class="bg-primary border border-primaryDark text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                    Meja {{ $table_number }}
                </div>
                <button id="call-waiter-btn" onclick="callWaiter()" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-3 py-1.5 rounded-xl flex items-center justify-center transition-all shadow-sm active:scale-95" title="Panggil Pelayan">
                    <i class="fas fa-bell text-sm"></i>
                    <span id="call-waiter-text" class="text-[10px] ml-1.5 font-bold whitespace-nowrap">Panggil Pelayan</span>
                </button>
            </div>
        </div>
    </header>

    <div class="max-w-md mx-auto p-4 space-y-4 no-print">

        @forelse($orders as $order)
        <!-- KARTU RIWAYAT PESANAN -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EAE3D9]">
            <div class="flex justify-between items-start mb-4 border-b border-[#EAE3D9] pb-3">
                <div>
                    <p class="text-xs text-[#A69C94] font-mono mb-0.5 font-bold">ID: {{ $order->transaction_id }}</p>
                    <p class="text-sm font-black text-secondary">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>

                @if($order->status == 'pending')
                <span class="bg-[#FDFBF7] text-primary px-2.5 py-1 rounded text-xs font-bold uppercase tracking-wider border border-[#EAE3D9]">Menunggu</span>
                @elseif($order->status == 'preparing')
                <span class="bg-primary/10 text-primaryDark px-2.5 py-1 rounded text-xs font-bold uppercase tracking-wider border border-primary/20">Dimasak</span>
                @elseif($order->status == 'cancelled')
                <span class="bg-red-50 text-red-600 px-2.5 py-1 rounded text-xs font-bold uppercase tracking-wider border border-red-100">Batal</span>
                @else
                <span class="bg-[#352214] text-[#FDFBF7] px-2.5 py-1 rounded text-xs font-bold uppercase tracking-wider border border-[#352214]">Selesai</span>
                @endif
            </div>

            <div class="space-y-2 mb-4">
                @foreach($order->items->take(3) as $item)
                <div class="flex justify-between text-sm text-[#8C837C]">
                    <span><strong class="text-secondary">{{ $item->quantity }}x</strong> {{ $item->menu->name }}</span>
                </div>
                @endforeach

                @if($order->items->count() > 3)
                <div class="mt-2">
                    <button onclick="toggleItems({{ $order->id }})" class="text-xs text-primary hover:text-primaryDark font-semibold flex items-center gap-1 transition-colors outline-none">
                        <span id="text-toggle-{{ $order->id }}">Lihat {{ $order->items->count() - 3 }} item lainnya</span>
                        <i class="fas fa-chevron-down transition-transform duration-300" id="icon-toggle-{{ $order->id }}"></i>
                    </button>

                    <div id="hidden-items-{{ $order->id }}" class="hidden space-y-2 mt-2 pt-2 border-t border-[#EAE3D9] transition-all">
                        @foreach($order->items->skip(3) as $item)
                        <div class="flex justify-between text-sm text-[#8C837C]">
                            <span><strong class="text-secondary">{{ $item->quantity }}x</strong> {{ $item->menu->name }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="flex justify-between items-center pt-3 border-t border-[#EAE3D9]">
                <div>
                    <p class="text-[10px] text-[#A69C94] font-bold uppercase tracking-wider">Total Bayar</p>
                    <p class="font-black text-secondary text-lg">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>

                <button onclick="openReceiptModal({{ $order->id }})" class="bg-secondary hover:bg-[#20150F] text-white px-4 py-2 rounded-xl text-xs font-bold transition-colors shadow-sm flex items-center gap-2 uppercase tracking-wide">
                    <i class="fas fa-file-invoice"></i> Lihat Struk
                </button>
            </div>

            @if(!in_array($order->status, ['pending', 'preparing', 'cancelled']))
            <div class="mt-4 pt-3 border-t border-[#EAE3D9]">
                @if($order->rating)
                    <div class="bg-[#FDFBF7] rounded-xl p-3 border border-[#EAE3D9]">
                        <div class="flex items-center gap-1 mb-1 text-yellow-400 text-xs">
                            @for($i=1; $i<=5; $i++)
                                <i class="fas fa-star {{ $i <= $order->rating ? '' : 'text-[#EAE3D9]' }}"></i>
                            @endfor
                            <span class="text-[#8C837C] ml-2 font-bold text-[10px] uppercase tracking-wider">Ulasan Anda</span>
                        </div>
                        <p class="text-xs text-secondary font-medium italic">"{{ $order->review }}"</p>
                    </div>
                @else
                    <form action="{{ route('order.review', ['transaction_id' => $order->transaction_id]) }}" method="POST" class="bg-[#FDFBF7] rounded-xl p-3 border border-[#EAE3D9]">
                        @csrf
                        <p class="text-xs font-bold text-secondary mb-2">Gimana pesanan ini?</p>
                        <div class="flex gap-1 mb-2">
                            <input type="hidden" name="rating" id="rating-{{ $order->id }}" value="5">
                            @for($i=1; $i<=5; $i++)
                                <button type="button" onclick="setRating({{ $order->id }}, {{ $i }})" class="star-btn-{{ $order->id }} text-yellow-400 text-sm focus:outline-none transition-colors active:scale-90">
                                    <i class="fas fa-star"></i>
                                </button>
                            @endfor
                        </div>
                        <textarea name="review" rows="2" placeholder="Tulis ulasan Anda (opsional)..." class="w-full text-xs p-2 rounded-xl border border-[#EAE3D9] bg-white text-secondary mb-2 focus:ring-1 focus:ring-primary focus:border-primary outline-none resize-none transition-colors"></textarea>
                        <button type="submit" class="bg-primary hover:bg-primaryDark text-white text-xs font-bold px-3 py-2 rounded-xl shadow-sm w-full transition-colors uppercase tracking-wide">Kirim Ulasan</button>
                    </form>
                @endif
            </div>
            @endif
        </div>

        <!-- MODAL STRUK CETAK (Per Pesanan) -->
        <div id="receipt-modal-{{ $order->id }}" class="fixed inset-0 bg-black/70 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm no-print transition-opacity opacity-0 duration-300">
            <div class="bg-white w-full max-w-sm rounded-2xl overflow-hidden flex flex-col max-h-[90vh] shadow-2xl transform scale-95 transition-transform duration-300 receipt-panel">

                <!-- Modal Header -->
                <div class="bg-secondary p-4 flex justify-between items-center shrink-0">
                    <h3 class="font-bold text-white tracking-wide">E-Receipt Transaksi</h3>
                    <button onclick="closeReceipt({{ $order->id }})" class="w-8 h-8 flex items-center justify-center bg-white/10 text-white rounded-full hover:bg-white/20 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Area Scroll Struk -->
                <div class="p-6 overflow-y-auto bg-gray-200 flex justify-center no-scrollbar">

                    <div id="receipt-area-{{ $order->id }}" class="receipt-content bg-white p-5 shadow-sm w-[80mm] text-black font-sans relative border border-gray-300">
                        <div class="absolute top-0 left-0 w-full h-1 bg-gray-800"></div>

                        <div class="text-center mb-4 mt-2">
                            <h2 class="text-xl font-extrabold text-black tracking-widest uppercase">QUICKDINE</h2>
                            <p class="text-[11px] text-gray-600 mt-1">Jl. Teknologi No. 123, Jakarta</p>
                            <p class="text-[11px] text-gray-600">Telp: 0812-3456-7890</p>
                        </div>

                        <div class="border-b-2 border-dashed border-gray-400 pb-3 mb-3 text-[11px] text-gray-800 font-mono">
                            <div class="flex justify-between mb-1">
                                <span>Tgl: {{ $order->created_at->format('d/m/Y H:i') }}</span>
                                @if($order->order_type == 'take_away')
                                <span class="font-extrabold">TAKE AWAY</span>
                                @else
                                <span>Meja: {{ $order->table->table_number ?? '-' }}</span>
                                @endif
                            </div>
                            <div class="flex justify-between">
                                <span>ID:</span>
                                <span>{{ $order->transaction_id }}</span>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-gray-400 pb-3 mb-3 text-[12px] text-gray-800 font-mono">
                            <table class="w-full text-left">
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="py-1.5 align-top">
                                        <div class="font-bold">{{ $item->menu->name }}</div>
                                        <div class="text-[10px] text-gray-600">{{ $item->quantity }} x {{ number_format($item->price_at_order, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="py-1.5 text-right align-bottom font-bold">
                                        {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div>

                        <div class="text-[12px] text-gray-800 font-mono mt-3 border-t-2 border-dashed border-gray-400 pt-3">
                            @php
                            $subtotal = $order->items->sum('subtotal');
                            $tax = $subtotal * 0.10;
                            @endphp
                            <div class="flex justify-between mb-1">
                                <span>Subtotal:</span>
                                <span>{{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span>Pajak (10%):</span>
                                <span>{{ number_format($tax, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between font-extrabold text-sm border-t-2 border-gray-800 pt-2 mt-1">
                                <span>TOTAL:</span>
                                <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-[11px] mt-2 border-t border-dashed border-gray-300 pt-2">
                                <span class="text-gray-600">Metode Pembayaran</span>
                                <span class="font-bold text-gray-900">
                                    {{ $order->payment_method == 'cash' ? 'Tunai / Cash' : ($order->payment_method == 'qris' ? 'Transfer/QRIS' : strtoupper($order->payment_method)) }}
                                </span>
                            </div>
                        </div>

                        <div class="text-center mt-8 mb-2 text-[10px] text-gray-500 font-mono">
                            <p>Terima kasih atas kunjungan Anda!</p>
                            <p>-- QuickDine System --</p>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer Aksi -->
                <div class="p-4 border-t border-[#EAE3D9] bg-[#FDFBF7]">
                    <button onclick="window.open('{{ route('order.receipt', $order->transaction_id) }}', 'CetakStruk', 'width=400,height=600')" class="w-full bg-primary hover:bg-primaryDark text-white py-3.5 rounded-xl font-bold transition-colors shadow-md flex items-center justify-center gap-2 uppercase tracking-wide text-xs">
                        <i class="fas fa-file-pdf"></i> Unduh Struk (PDF)
                    </button>
                </div>
            </div>
        </div>

        @empty
        <div class="bg-white rounded-2xl p-10 text-center shadow-sm border border-[#EAE3D9] mt-10">
            <i class="fas fa-receipt text-5xl text-[#EAE3D9] mb-4"></i>
            <h3 class="text-lg font-black text-secondary">Belum ada pesanan</h3>
            @guest
            <p class="text-[#8C837C] text-sm mt-2 mb-4 font-medium">Anda belum membuat pesanan apapun di sesi ini.</p>
            <p class="text-[10px] text-[#A69C94] mb-6">Sudah punya akun? Login untuk melihat riwayat pesanan sebelumnya.</p>
            <div class="flex gap-3 justify-center">
                <a href="{{ route('menu.index', ['table_number' => $table_number]) }}" class="bg-primary hover:bg-primaryDark text-white font-bold py-3 px-6 rounded-xl transition-colors shadow-md text-xs uppercase tracking-wide">
                    Lihat Menu
                </a>
                <a href="{{ route('login') }}" class="bg-secondary hover:bg-[#20150F] text-white font-bold py-3 px-6 rounded-xl transition-colors shadow-md flex items-center gap-1 text-xs uppercase tracking-wide">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </a>
            </div>
            @else
            <p class="text-[#8C837C] text-sm mt-2 mb-6 font-medium">Anda belum membuat pesanan apapun.</p>
            <a href="{{ route('menu.index', ['table_number' => $table_number]) }}" class="bg-primary hover:bg-primaryDark text-white font-bold py-3 px-6 rounded-xl transition-colors shadow-md text-xs uppercase tracking-wide inline-block">
                Lihat Menu
            </a>
            @endguest
        </div>
        @endforelse
    </div>

    <!-- Script area -->

    <script>
        window.showToast = function(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 left-1/2 transform -translate-x-1/2 z-[9999] px-5 py-3 rounded-2xl shadow-xl text-sm font-bold flex items-center gap-3 transition-all duration-500 opacity-0 -translate-y-4 bg-white border border-[#EAE3D9] text-secondary w-max max-w-[92vw] md:max-w-md`;
            
            let iconHtml = '';
            if (type === 'error') iconHtml = '<i class="fas fa-exclamation-circle text-red-500 text-lg shrink-0"></i>';
            else if (type === 'warning') iconHtml = '<i class="fas fa-exclamation-triangle text-amber-500 text-lg shrink-0"></i>';
            else if (type === 'info') iconHtml = '<i class="fas fa-bell text-primary text-lg shrink-0"></i>';
            else iconHtml = '<i class="fas fa-check-circle text-primary text-lg shrink-0"></i>';
            
            toast.innerHTML = `${iconHtml} <span class="leading-snug">${message}</span>`;
            document.body.appendChild(toast);
            
            setTimeout(() => toast.classList.remove('opacity-0', '-translate-y-4'), 10);
            setTimeout(() => {
                toast.classList.add('opacity-0', '-translate-y-4');
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        };

        let waiterCooldown = 0;
        let waiterInterval;

        window.callWaiter = async function() {
            if (waiterCooldown > 0) return;

            const btn = document.getElementById('call-waiter-btn');
            const text = document.getElementById('call-waiter-text');
            const tableNumber = '{{ $table_number ?? 0 }}';
            
            try {
                const response = await fetch('/call-waiter', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ table_number: tableNumber })
                });
                
                const data = await response.json();
                if (data.success) {
                    showToast('Pelayan segera menuju meja Anda.', 'info');
                    
                    waiterCooldown = 60;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                    
                    waiterInterval = setInterval(() => {
                        waiterCooldown--;
                        text.innerText = `Tunggu ${waiterCooldown}s`;
                        
                        if (waiterCooldown <= 0) {
                            clearInterval(waiterInterval);
                            btn.classList.remove('opacity-50', 'cursor-not-allowed');
                            text.innerText = 'Panggil Pelayan';
                        }
                    }, 1000);
                }
            } catch (error) {
                showToast('Gagal memanggil pelayan. Periksa koneksi Anda.', 'error');
            }
        };

        function openReceiptModal(id) {
            const modal = document.getElementById('receipt-modal-' + id);
            const panel = modal.querySelector('.receipt-panel');
            modal.classList.remove('hidden');
            // trigger reflow
            void modal.offsetWidth;
            modal.classList.remove('opacity-0');
            panel.classList.remove('scale-95');
            panel.classList.add('scale-100');
        }

        function setRating(orderId, rating) {
            document.getElementById('rating-' + orderId).value = rating;
            const stars = document.querySelectorAll('.star-btn-' + orderId);
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.replace('text-gray-300', 'text-yellow-400');
                } else {
                    star.classList.replace('text-yellow-400', 'text-gray-300');
                }
            });
        }

        function closeReceipt(id) {
            const modal = document.getElementById('receipt-modal-' + id);
            const panel = modal.querySelector('.receipt-panel');

            modal.classList.add('opacity-0');
            panel.classList.remove('scale-100');
            panel.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function printReceipt(id) {
            document.querySelectorAll('.receipt-content').forEach(el => {
                el.classList.remove('print-active');
            });

            const targetArea = document.getElementById('receipt-area-' + id);
            targetArea.classList.add('print-active');

            window.print();
        }

        function toggleItems(id) {
            const hiddenItems = document.getElementById('hidden-items-' + id);
            const iconToggle = document.getElementById('icon-toggle-' + id);
            const textToggle = document.getElementById('text-toggle-' + id);

            // Ambil total item tersembunyi dari elemen data atau secara manual via PHP render
            // Karena kita butuh angka, kita bisa deteksi apakah dia sedang terbuka atau tertutup
            if (hiddenItems.classList.contains('hidden')) {
                hiddenItems.classList.remove('hidden');
                iconToggle.classList.add('rotate-180'); // Putar ikon panah ke atas
                textToggle.innerText = 'Tutup daftar item';
            } else {
                hiddenItems.classList.add('hidden');
                iconToggle.classList.remove('rotate-180'); // Putar ikon panah ke bawah kembali
                // Mengembalikan teks awal (mengambil dari panjang child node di dalam container)
                const sisaItem = hiddenItems.querySelectorAll('div').length;
                textToggle.innerText = `Lihat ${sisaItem} item lainnya`;
            }
        }
    </script>
</body>

</html>