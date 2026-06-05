<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(!in_array($order->status, ['served', 'cancelled']))
    <meta http-equiv="refresh" content="10">
    @endif
    <title>Status Pesanan - QuickDine</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
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
    </style>
</head>

<body class="bg-bgLight font-sans antialiased text-secondary pb-10 selection:bg-primary selection:text-white">

    <header class="max-w-md mx-auto bg-secondary sticky top-0 z-50 shadow-md rounded-b-2xl overflow-hidden shrink-0">
        <div class="px-4 py-4 flex justify-between items-center gap-2">
            <div class="flex items-center gap-3">
                <div class="bg-primary w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-primary/30">
                    <i class="fas fa-mug-hot text-white text-lg"></i>
                </div>
                <div class="flex flex-col justify-center">
                    <h1 class="text-base font-black text-white tracking-tight leading-tight">Status Pesanan</h1>
                </div>
            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                <div class="bg-primary border border-primaryDark text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                    @if($order->order_type == 'take_away')
                    <i class="fas fa-shopping-bag mr-1 text-[10px]"></i> Take Away
                    @else
                    Meja {{ $order->table->table_number ?? '-' }}
                    @endif
                </div>
                <button id="call-waiter-btn" onclick="callWaiter()" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-3 py-1.5 rounded-xl flex items-center justify-center transition-all shadow-sm active:scale-95" title="Panggil Pelayan">
                    <i class="fas fa-bell text-sm"></i>
                    <span id="call-waiter-text" class="text-[10px] ml-1.5 font-bold whitespace-nowrap">Panggil Pelayan</span>
                </button>
            </div>
        </div>
    </header>

    <div class="max-w-md mx-auto p-4 space-y-5">

        @if($order->status == 'cancelled')
        <div class="bg-red-50 rounded-2xl p-6 shadow-sm border border-red-100 text-center">
            <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-times-circle text-4xl text-red-500"></i>
            </div>
            <h2 class="text-xl font-bold text-red-700 mb-2">Pesanan Dibatalkan</h2>
            <p class="text-sm text-red-600">Pesanan Anda telah dibatalkan. Silakan kembali ke menu utama jika ingin membuat pesanan baru.</p>
        </div>
        @elseif($order->payment_status == 'unpaid')
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#EAE3D9] text-center">
            @if($order->payment_method == 'cash')
                <div class="animate-fade-in">
                    <h2 class="text-xl font-black text-secondary mb-1">Tunjukkan ke Kasir</h2>
                    <p class="text-xs text-[#8C837C] mb-6">Pindai QR code ini di kasir untuk mempercepat pembayaran.</p>
                    
                    <div class="bg-[#FDFBF7] border border-[#EAE3D9] rounded-3xl p-5 mx-auto max-w-[260px] relative overflow-hidden shadow-inner flex flex-col items-center">
                        <!-- Ornamen desain tiket -->
                        <div class="absolute -left-4 top-1/2 -translate-y-1/2 w-8 h-8 bg-white rounded-full border-r border-[#EAE3D9]"></div>
                        <div class="absolute -right-4 top-1/2 -translate-y-1/2 w-8 h-8 bg-white rounded-full border-l border-[#EAE3D9]"></div>
                        
                        <div class="bg-white p-3 rounded-2xl border border-[#EAE3D9] shadow-sm mb-4 w-full">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ $order->transaction_id }}&color=352214&bgcolor=FFFFFF" alt="QR Code" class="w-full h-auto aspect-square rounded-xl">
                        </div>
                        
                        <p class="text-[10px] text-[#A69C94] uppercase tracking-widest font-bold mb-1">ID Transaksi</p>
                        <p class="font-mono text-lg font-black text-secondary tracking-widest">{{ $order->transaction_id }}</p>
                    </div>

                    <div class="mt-6 flex justify-between items-center bg-[#FDFBF7] border border-[#EAE3D9] rounded-2xl p-4 shadow-sm">
                        <div class="text-left">
                            <p class="text-[10px] text-[#A69C94] font-bold uppercase tracking-widest mb-0.5">Total Tagihan</p>
                            <p class="font-black text-primary text-2xl">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-primary/10 w-12 h-12 rounded-full flex items-center justify-center shrink-0 border border-primary/20">
                            <i class="fas fa-money-bill-wave text-primary text-xl"></i>
                        </div>
                    </div>
                </div>
            @else
                <i class="fas fa-clock text-5xl text-primary mb-4"></i>
                <h2 class="text-xl font-black text-secondary">Menunggu Transfer</h2>
                <div class="bg-[#FDFBF7] border border-[#EAE3D9] rounded-xl p-4 mt-4 text-left">
                    <p class="text-sm text-[#8C837C]">Anda telah memilih metode pembayaran Virtual Account/E-Wallet. Pesanan Anda akan otomatis diproses setelah transfer/pembayaran berhasil dilakukan.</p>
                </div>
                <form id="cancel-reorder-form" action="{{ route('order.cancelAndReorder', $order->transaction_id) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="button" onclick="document.getElementById('cancel-modal').classList.remove('hidden')" class="w-full bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-bold py-3 rounded-xl transition-colors text-xs uppercase tracking-wide">
                        <i class="fas fa-undo mr-1"></i> Batalkan & Ubah Pembayaran
                    </button>
                </form>

                <!-- Custom Confirm Modal -->
                <div id="cancel-modal" class="hidden fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
                    <div class="bg-white rounded-3xl p-6 w-full max-w-sm shadow-2xl transform transition-all text-center border border-[#EAE3D9]">
                        <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-red-50">
                            <i class="fas fa-exclamation-triangle text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-black text-secondary mb-2">Ubah Pembayaran?</h3>
                        <p class="text-sm text-[#8C837C] mb-6">Pesanan ini akan dibatalkan, namun isi keranjang Anda akan dipulihkan agar bisa melakukan pembayaran ulang.</p>
                        <div class="flex gap-3">
                            <button onclick="document.getElementById('cancel-modal').classList.add('hidden')" class="flex-1 bg-[#FDFBF7] hover:bg-[#EAE3D9] text-secondary font-bold py-3 rounded-xl border border-[#EAE3D9] transition-colors text-xs uppercase tracking-wide">Tutup</button>
                            <button onclick="document.getElementById('cancel-reorder-form').submit()" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-3 rounded-xl shadow-lg shadow-red-500/30 transition-colors text-xs uppercase tracking-wide">Ya, Batalkan</button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @else
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#EAE3D9]">
            @php
            $status = $order->status;
            $step2 = in_array($status, ['preparing', 'served']);
            $step3 = $status == 'served';
            @endphp
            
            @if($status == 'pending')
            <div class="text-center py-4 border-b border-[#EAE3D9] mb-6">
                <div class="w-16 h-16 mx-auto bg-[#FDFBF7] border border-[#EAE3D9] rounded-full flex items-center justify-center mb-4 animate-pop-in shadow-sm">
                    <svg class="w-7 h-7 text-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 018.625 8.625M10.125 2.25A3.375 3.375 0 0113.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 013.375 3.375M9 15l2.25 2.25L15 12" />
                    </svg>
                </div>
                <h2 class="text-xl font-black text-secondary mb-2 tracking-tight">Sip, Pesanan Masuk!</h2>
                <p class="text-sm text-[#8C837C]">Pesanan kamu udah masuk antrean nih, tunggu sebentar ya!</p>
            </div>
            @elseif($status == 'preparing')
            <div class="text-center py-4 border-b border-[#EAE3D9] mb-6">
                <div class="w-16 h-16 mx-auto bg-[#FDFBF7] border border-[#EAE3D9] rounded-full flex items-center justify-center mb-4 animate-pop-in shadow-sm">
                    <svg class="w-7 h-7 text-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z" />
                    </svg>
                </div>
                <h2 class="text-xl font-black text-secondary mb-2 tracking-tight">Sedang Diproses!</h2>
                <p class="text-sm text-[#8C837C]">Pesanan kamu lagi dibuat nih, sabar yaa!</p>
            </div>
            @elseif($step3)
            <div class="text-center py-4 border-b border-[#EAE3D9] mb-6">
                <div class="w-16 h-16 mx-auto bg-[#FDFBF7] border border-[#EAE3D9] rounded-full flex items-center justify-center mb-4 animate-pop-in shadow-sm">
                    @if($order->order_type == 'take_away')
                    <svg class="w-7 h-7 text-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm5.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    @else
                    <svg class="w-7 h-7 text-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                    </svg>
                    @endif
                </div>
                <h2 class="text-xl font-black text-secondary mb-2 tracking-tight">
                    {{ $order->order_type == 'take_away' ? 'Pesanan Siap Diambil!' : 'Pesanan Segera Diantar!' }}
                </h2>
                <p class="text-sm text-[#8C837C]">
                    {{ $order->order_type == 'take_away' ? 'Pesanan kamu udah siap diambil nih, semoga jadi mood booster hari ini!' : 'Pesanan kamu lagi OTW ke meja kamu nih, enjoy!' }}
                </p>
            </div>
            @endif

            <style>
                @keyframes sweep-line {
                    0% { transform: translateX(-100%); }
                    100% { transform: translateX(100%); }
                }
                .animate-sweep-line {
                    animation: sweep-line 1.5s infinite linear;
                }
                @keyframes pop-in {
                    0% { transform: scale(0.5); opacity: 0; }
                    60% { transform: scale(1.15); opacity: 1; }
                    100% { transform: scale(1); opacity: 1; }
                }
                .animate-pop-in {
                    animation: pop-in 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
                }
            </style>

            <div class="w-full mt-4 mb-10 px-1">
                <div class="flex items-center justify-between w-full relative">

                    <!-- Step 1 -->
                    <div class="relative flex flex-col items-center z-20 shrink-0">
                        <div class="w-10 h-10 rounded-full border-[3px] border-white flex items-center justify-center bg-primary text-white shadow-md relative transition-colors duration-500">
                            <i class="fas fa-receipt text-xs"></i>
                        </div>
                        <span class="absolute top-12 left-1/2 -translate-x-1/2 text-[9px] font-black text-primary uppercase tracking-wider text-center w-max min-w-[5rem]">Diterima</span>
                    </div>

                    <!-- Line 1 -->
                    <div class="flex-1 h-1.5 mx-1 rounded-full overflow-hidden flex bg-[#EAE3D9]">
                        @if($step3)
                        <div class="h-full w-full bg-primary transition-colors duration-500"></div>
                        @elseif($step2)
                        <div class="h-full w-full bg-primary transition-colors duration-500"></div>
                        @else
                        <div class="h-full w-full relative overflow-hidden bg-[#FDFBF7] flex-1">
                            <div class="absolute inset-0 bg-gradient-to-r from-[#FDFBF7] via-primary/50 to-[#FDFBF7] animate-sweep-line opacity-90"></div>
                        </div>
                        @endif
                    </div>

                    <!-- Step 2 -->
                    <div class="relative flex flex-col items-center z-20 shrink-0">
                        @php
                        $step2Bg = $step3 ? 'bg-primary text-white border-white' : ($step2 ? 'bg-primary text-white border-white' : 'bg-[#FDFBF7] text-[#D0C8BF] border-[#EAE3D9]');
                        $step2Text = $step3 ? 'text-primary' : ($step2 ? 'text-primary' : 'text-[#A69C94]');
                        @endphp
                        <div class="w-10 h-10 rounded-full border-[3px] flex items-center justify-center {{ $step2Bg }} shadow-sm relative transition-colors duration-500">
                            <i class="fas fa-fire text-xs"></i>
                        </div>
                        <span class="absolute top-12 left-1/2 -translate-x-1/2 text-[9px] font-black {{ $step2Text }} uppercase tracking-wider text-center w-max min-w-[5rem] transition-colors duration-500">Diproses</span>
                    </div>

                    <!-- Line 2 -->
                    <div class="flex-1 h-1.5 mx-1 rounded-full overflow-hidden flex bg-[#EAE3D9]">
                        @if($step3)
                        <div class="h-full w-full bg-primary transition-colors duration-500"></div>
                        @elseif($step2)
                        <div class="h-full w-full relative overflow-hidden bg-[#FDFBF7] flex-1">
                            <div class="absolute inset-0 bg-gradient-to-r from-[#FDFBF7] via-primary/50 to-[#FDFBF7] animate-sweep-line opacity-90"></div>
                        </div>
                        @endif
                    </div>

                    <!-- Step 3 -->
                    <div class="relative flex flex-col items-center z-20 shrink-0">
                        @php
                        $step3Bg = $step3 ? 'bg-primary text-white border-white' : 'bg-[#FDFBF7] text-[#D0C8BF] border-[#EAE3D9]';
                        $step3Text = $step3 ? 'text-primary' : 'text-[#A69C94]';
                        @endphp
                        <div class="w-10 h-10 rounded-full border-[3px] flex items-center justify-center {{ $step3Bg }} shadow-sm relative transition-colors duration-500">
                            <i class="fas {{ $order->order_type == 'take_away' ? 'fa-shopping-bag' : 'fa-check-double' }} text-xs"></i>
                        </div>
                        <span class="absolute top-12 left-1/2 -translate-x-1/2 text-[9px] font-black {{ $step3Text }} uppercase tracking-wider text-center w-max min-w-[5rem] leading-tight transition-colors duration-500">
                            @if($order->order_type == 'take_away')
                            Siap<br>Diambil
                            @else
                            Disajikan
                            @endif
                        </span>
                    </div>

                </div>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-[#EAE3D9] overflow-hidden">
            <div class="bg-[#FDFBF7] p-4 border-b border-[#EAE3D9] flex justify-between items-center">
                <h3 class="font-black text-secondary tracking-tight text-sm">Ringkasan Pesanan</h3>
                <span class="text-[10px] font-mono text-[#8C837C] bg-white px-2 py-1 border border-[#EAE3D9] rounded-md font-bold">ID: {{ substr($order->transaction_id, -6) }}</span>
            </div>
            <div class="p-4 space-y-3">
                @foreach($order->items as $item)
                <div class="flex justify-between items-center text-sm">
                    <div class="flex gap-2">
                        <span class="font-bold bg-[#FDFBF7] border border-[#EAE3D9] px-2 py-0.5 rounded text-xs text-secondary">{{ $item->quantity }}x</span>
                        <span class="font-semibold text-secondary">{{ $item->menu->name }}</span>
                    </div>
                    <span class="font-bold text-secondary">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            <div class="bg-[#FDFBF7] p-4 border-t border-[#EAE3D9]">
                <div class="flex justify-between items-center font-black text-secondary text-lg">
                    <span>Total Bayar</span>
                    <span class="text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-[10px] mt-3 text-[#8C837C]">
                    <span class="font-bold uppercase tracking-widest">Metode Pembayaran</span>
                    <span class="font-bold px-2 py-1 rounded uppercase tracking-wider border {{ $order->payment_status == 'paid' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' }}">
                        {{ $order->payment_method == 'qris' ? 'TRANSFER/QRIS' : strtoupper($order->payment_method ?? 'TUNAI') }}
                        ({{ $order->payment_status == 'paid' ? 'LUNAS' : 'BELUM BAYAR' }})
                    </span>
                </div>
            </div>
        </div>

        @if($order->status == 'served' || $order->status == 'completed')
        <div class="bg-white rounded-2xl shadow-sm border border-[#EAE3D9] overflow-hidden p-5">
            @if($order->rating)
                <div class="text-center">
                    <h3 class="font-black text-secondary tracking-tight text-base mb-1">Terima kasih atas ulasanmu!</h3>
                    <p class="text-[10px] text-[#8C837C] mb-3">Masukan dari kamu bakal jadi bahan evaluasi kita buat pelayanan yang lebih mantap lagi.</p>
                    <div class="flex justify-center gap-1 mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $order->rating ? 'text-yellow-400' : 'text-[#EAE3D9]' }} text-xl"></i>
                        @endfor
                    </div>
                    @if($order->review)
                    <p class="text-sm text-[#8C837C] bg-[#FDFBF7] p-3 rounded-xl border border-[#EAE3D9] italic">"{{ $order->review }}"</p>
                    @endif
                </div>
            @else
                <div class="text-center mb-4">
                    <h3 class="font-black text-secondary tracking-tight text-base mb-1">Gimana hidangan hari ini?</h3>
                    <p class="text-[10px] text-[#8C837C]">Bantu kami jadi lebih baik dengan memberi rating!</p>
                </div>
                <form id="review-form" action="{{ route('order.review', ['transaction_id' => $order->transaction_id]) }}" method="POST">
                    @csrf
                    <div class="flex justify-center gap-2 mb-4" id="star-rating">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" data-rating="{{ $i }}" class="star-btn text-[#EAE3D9] transition-colors active:scale-90">
                                <i class="fas fa-star text-3xl"></i>
                            </button>
                        @endfor
                        <input type="hidden" name="rating" id="rating-input" required>
                    </div>
                    <div class="mb-4">
                        <textarea name="review" rows="2" placeholder="Tulis pengalamanmu di sini (opsional)..." class="w-full text-xs bg-[#FCF9F2] text-secondary border border-[#EAE3D9] rounded-xl px-3 py-2.5 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"></textarea>
                    </div>
                    <button type="submit" id="submit-review-btn" disabled class="w-full bg-primary hover:bg-primaryDark text-white font-bold py-3 rounded-xl transition-colors shadow-lg shadow-primary/20 disabled:opacity-50 disabled:cursor-not-allowed text-xs uppercase tracking-wide">
                        <i class="fas fa-paper-plane mr-1 text-[10px]"></i> Kirim Ulasan
                    </button>
                </form>
            @endif
        </div>
        @endif

        <div class="space-y-3">
            @php
            $fallbackTable = session('current_table');
            if (!$fallbackTable) {
                $fallbackTable = $order->table ? $order->table->table_number : '0';
            }
            if (empty($fallbackTable)) {
                $fallbackTable = '0';
            }
            @endphp

            <a href="/history/{{ $fallbackTable }}" class="w-full bg-secondary hover:bg-[#20150F] text-white font-bold py-3.5 rounded-xl flex justify-center items-center gap-2 shadow-lg active:scale-95 transition-all text-xs uppercase tracking-wide">
                <i class="fas fa-history text-[10px]"></i> Riwayat Pesanan
            </a>

            @guest
            <p class="text-[10px] text-center text-[#A69C94] font-medium italic mt-2">
                Punya akun? <a href="{{ route('login') }}" class="text-primary font-bold underline">Masuk di sini</a> untuk menyimpan riwayat permanen.
            </p>
            @endguest

            <a href="{{ route('menu.index', $fallbackTable) }}" class="w-full bg-white text-secondary font-bold py-3.5 rounded-xl flex justify-center items-center gap-2 border border-[#EAE3D9] shadow-sm hover:bg-[#FDFBF7] active:scale-95 transition-all text-xs uppercase tracking-wide">
                <i class="fas fa-plus text-[10px]"></i> Pesan Menu Lain
            </a>
        </div>
    </div>

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

        // (Confetti removed as requested)

        let waiterCooldown = 0;
        let waiterInterval;

        window.callWaiter = async function() {
            if (waiterCooldown > 0) return;

            const btn = document.getElementById('call-waiter-btn');
            const text = document.getElementById('call-waiter-text');
            const tableNumber = '{{ $fallbackTable ?? 0 }}';
            
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

        // Star Rating Logic
        document.addEventListener('DOMContentLoaded', () => {
            const stars = document.querySelectorAll('.star-btn');
            const ratingInput = document.getElementById('rating-input');
            const submitBtn = document.getElementById('submit-review-btn');
            const reviewForm = document.getElementById('review-form');

            if(stars.length > 0) {
                stars.forEach(star => {
                    star.addEventListener('click', function() {
                        const rating = this.dataset.rating;
                        ratingInput.value = rating;
                        submitBtn.disabled = false;
                        
                        stars.forEach(s => {
                            if(parseInt(s.dataset.rating) <= parseInt(rating)) {
                                s.classList.remove('text-[#EAE3D9]');
                                s.classList.add('text-yellow-400');
                            } else {
                                s.classList.add('text-[#EAE3D9]');
                                s.classList.remove('text-yellow-400');
                            }
                        });
                    });
                });
            }

            if(reviewForm) {
                reviewForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1 text-[10px]"></i> Mengirim...';
                    
                    try {
                        const formData = new FormData(this);
                        const response = await fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await response.json();
                        
                        if (data.success) {
                            showToast(data.message, 'success');
                            
                            // Replace form with success message visually
                            const container = this.closest('.bg-white.rounded-2xl.shadow-sm');
                            const reviewText = formData.get('review') ? `<p class="text-sm text-[#8C837C] bg-[#FDFBF7] p-3 rounded-xl border border-[#EAE3D9] italic">"${formData.get('review')}"</p>` : '';
                            
                            let starsHtml = '';
                            for(let i=1; i<=5; i++) {
                                starsHtml += `<i class="fas fa-star ${i <= parseInt(formData.get('rating')) ? 'text-yellow-400' : 'text-[#EAE3D9]'} text-xl"></i>`;
                            }
                            
                            container.innerHTML = `
                                <div class="text-center">
                                    <h3 class="font-black text-secondary tracking-tight text-base mb-1">Terima kasih atas ulasanmu!</h3>
                                    <p class="text-[10px] text-[#8C837C] mb-3">Masukan dari kamu bakal jadi bahan evaluasi kita buat pelayanan yang lebih mantap lagi.</p>
                                    <div class="flex justify-center gap-1 mb-3">
                                        ${starsHtml}
                                    </div>
                                    ${reviewText}
                                </div>
                            `;
                        } else {
                            showToast('Terjadi kesalahan.', 'error');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-1 text-[10px]"></i> Kirim Ulasan';
                        }
                    } catch(err) {
                        showToast('Gagal mengirim ulasan.', 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-1 text-[10px]"></i> Kirim Ulasan';
                    }
                });
            }
        });
    </script>
</body>

</html>