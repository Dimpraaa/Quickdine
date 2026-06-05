<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KDS - Crew Station</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    @vite(['resources/js/app.js'])
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
                        darkBg: '#1A110C',
                        cardBg: '#271A11',
                        borderColor: '#3D281A',
                        textDim: '#A69C94'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @keyframes pulse-red {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
            }

            50% {
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            }
        }

        .order-overdue {
            animation: pulse-red 2s infinite;
            border: 2px solid #ef4444 !important;
        }

        .kds-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .kds-scroll::-webkit-scrollbar-track {
            background: rgba(26, 17, 12, 0.5);
            border-radius: 10px;
        }

        .kds-scroll::-webkit-scrollbar-thumb {
            background: rgba(61, 40, 26, 0.8);
            border-radius: 10px;
        }

        .kds-scroll::-webkit-scrollbar-thumb:hover {
            background: #B27C44;
        }

        /* Toast Notification */
        @keyframes toast-in {
            0% { transform: translateX(100%); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
        @keyframes toast-out {
            0% { transform: translateX(0); opacity: 1; }
            100% { transform: translateX(100%); opacity: 0; }
        }
        .toast-enter { animation: toast-in 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .toast-exit { animation: toast-out 0.3s ease-in forwards; }

        /* Empty State */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }
        .empty-float { animation: float 3s ease-in-out infinite; }
    </style>
</head>

<body class="bg-darkBg text-slate-200 h-screen flex flex-col overflow-hidden">

    <nav class="bg-secondary/90 backdrop-blur-md border-b border-borderColor px-6 py-4 flex justify-between items-center shrink-0 z-10 shadow-lg">
        <div class="flex items-center gap-5">
            <div class="w-12 h-12 bg-gradient-to-br from-primary to-primaryDark rounded-xl flex items-center justify-center font-black text-white text-2xl shadow-lg">
                <i class="fas fa-mug-hot text-xl"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-white font-black text-2xl tracking-tight leading-none mb-1">QuickDine <span class="text-primary font-bold text-sm tracking-widest uppercase ml-2">KDS</span></span>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span id="live-clock" class="text-[11px] font-mono text-textDim tracking-wider">Memuat...</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="flex items-center gap-6 bg-cardBg p-2.5 rounded-2xl border border-borderColor shadow-inner">
                <div class="flex gap-4 border-r border-borderColor pr-6 pl-2">
                    <div class="flex flex-col items-center justify-center px-2">
                        <p class="text-[9px] text-textDim font-bold uppercase tracking-widest mb-1">Tagihan</p>
                        <p id="count-unpaid" class="text-xl font-black text-red-500 leading-none">{{ $orders->where('payment_status', 'unpaid')->whereNotIn('status', ['cancelled', 'served'])->count() }}</p>
                    </div>
                    <div class="w-px bg-borderColor"></div>
                    <div class="flex flex-col items-center justify-center px-2">
                        <p class="text-[9px] text-textDim font-bold uppercase tracking-widest mb-1">Antrean</p>
                        <p id="count-pending" class="text-xl font-black text-blue-400 leading-none">{{ $orders->where('payment_status', 'paid')->where('status', 'pending')->count() }}</p>
                    </div>
                    <div class="w-px bg-borderColor"></div>
                    <div class="flex flex-col items-center justify-center px-2">
                        <p class="text-[9px] text-textDim font-bold uppercase tracking-widest mb-1">Proses</p>
                        <p id="count-preparing" class="text-xl font-black text-emerald-500 leading-none">{{ $orders->where('status', 'preparing')->count() }}</p>
                    </div>
                    <div class="w-px bg-borderColor"></div>
                    <div class="flex flex-col items-center justify-center px-2">
                        <p class="text-[9px] text-textDim font-bold uppercase tracking-widest mb-1">Batal</p>
                        <p id="count-cancelled" class="text-xl font-black text-primary leading-none">{{ $orders->where('status', 'cancelled')->count() }}</p>
                    </div>
                </div>
                <button onclick="location.reload()" class="w-12 h-12 rounded-xl bg-secondary text-textDim hover:bg-primary hover:text-white border border-borderColor flex items-center justify-center transition-all shadow-sm" title="Refresh Manual">
                    <i class="fas fa-sync-alt text-lg"></i>
                </button>
            </div>

            {{-- Staff Info & Logout --}}
            <div class="flex items-center gap-3 bg-cardBg p-2.5 rounded-2xl border border-borderColor shadow-inner">
                <div class="flex flex-col items-end px-2">
                    <p class="text-[9px] text-textDim font-bold uppercase tracking-widest">Operator</p>
                    <p class="text-sm font-bold text-white leading-tight">{{ auth()->user()->name }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all" title="Keluar">
                        <i class="fas fa-power-off text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- FIX 1: Toast Notification --}}
    @if(session('success') || session('error'))
    <div id="kds-toast" class="fixed top-6 right-6 z-[60] toast-enter">
        <div class="flex items-start gap-3 px-5 py-4 rounded-2xl shadow-2xl border max-w-sm
            {{ session('success') ? 'bg-cardBg border-emerald-500' : 'bg-cardBg border-red-500' }}">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5
                {{ session('success') ? 'bg-emerald-500/20' : 'bg-red-500/20' }}">
                <i class="fas {{ session('success') ? 'fa-check-circle text-emerald-400' : 'fa-exclamation-circle text-red-400' }} text-sm"></i>
            </div>
            <div class="flex-1">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-0.5
                    {{ session('success') ? 'text-emerald-400' : 'text-red-400' }}">
                    {{ session('success') ? 'Berhasil' : 'Gagal' }}
                </p>
                <p class="text-sm font-semibold text-white leading-snug">{{ session('success') ?? session('error') }}</p>
            </div>
            <button onclick="dismissToast()" class="text-textDim hover:text-white transition-colors shrink-0 mt-0.5">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    </div>
    @endif

    @php
        $billingOrders = $orders->where('payment_status', 'unpaid')->whereNotIn('status', ['cancelled', 'served']);
        $cookingOrders = $orders->where('payment_status', 'paid')->where('status', 'pending');
        $servingOrders = $orders->where('status', 'preparing');
        $completedOrders = $orders->whereIn('status', ['served', 'cancelled'])->filter(function ($order) {
            // Sembunyikan pesanan auto-cancel dari kolom Riwayat
            if ($order->status === 'cancelled' && $order->payment_status === 'unpaid') {
                return false;
            }
            return true;
        });
    @endphp

    <main class="flex-1 flex flex-col overflow-hidden bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-secondary to-darkBg">

        {{-- PANEL PANGGILAN PELAYAN --}}
        @if($waiterCalls->count() > 0)
        <div class="px-5 pt-5 flex gap-4 overflow-x-auto no-scrollbar shrink-0">
            @foreach($waiterCalls as $call)
            <div class="bg-cardBg border-l-4 border-l-primary border-y border-r border-borderColor rounded-xl p-3 flex items-center justify-between min-w-[250px]">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-red-500/10 rounded-lg flex items-center justify-center text-red-400">
                        <i class="fas fa-bell text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-sm leading-none">Meja {{ $call->table_number }}</h3>
                        <p class="text-textDim text-[10px] mt-0.5">Panggilan Bantuan</p>
                    </div>
                </div>
                <form action="{{ route('kitchen.resolveWaiter') }}" method="POST">
                    @csrf
                    <input type="hidden" name="table_number" value="{{ $call->table_number }}">
                    <button type="submit" class="bg-secondary border border-borderColor hover:bg-primary text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5 ml-4">
                        <i class="fas fa-check text-[10px] text-emerald-400"></i> Selesai
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @endif

        {{-- GRID UTAMA --}}
        <div class="flex-1 p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 overflow-hidden">

            {{-- KOLOM 1: Menunggu Bayar --}}
        <div class="flex flex-col h-full bg-cardBg/40 rounded-2xl border border-borderColor overflow-hidden shadow-xl">
            <div class="bg-gradient-to-r from-red-900 to-red-800 border-b border-borderColor px-5 py-3.5 flex justify-between items-center z-10">
                <h2 class="font-bold text-sm tracking-wider text-white flex items-center gap-2">
                    <div class="p-1.5 bg-black/20 rounded-lg"><i class="fas fa-wallet text-xs"></i></div> Menunggu Bayar
                </h2>
                <span class="text-[10px] bg-black/30 px-2.5 py-1 rounded-full font-bold text-white uppercase">Urgent</span>
            </div>
            <div class="flex-1 p-3 overflow-y-auto space-y-3 kds-scroll">
                @forelse($billingOrders as $order)
                @include('dashboard.partials.order-card', ['order' => $order, 'type' => 'billing'])
                @empty
                <div class="flex flex-col items-center justify-center h-full opacity-40 py-10">
                    <div class="empty-float">
                        <i class="fas fa-wallet text-4xl text-red-500/50 mb-4"></i>
                    </div>
                    <p class="text-xs font-bold text-textDim uppercase tracking-widest">Tidak ada tagihan</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- KOLOM 2: Antrean Baru --}}
        <div class="flex flex-col h-full bg-cardBg/40 rounded-2xl border border-borderColor overflow-hidden shadow-xl relative">
            <div class="bg-gradient-to-r from-blue-900 to-blue-800 border-b border-borderColor px-5 py-3.5 flex justify-between items-center z-10">
                <h2 class="font-bold text-sm tracking-wider text-white flex items-center gap-2">
                    <div class="p-1.5 bg-black/20 rounded-lg"><i class="fas fa-list-ul text-xs"></i></div> Antrean Baru
                </h2>
                <span class="text-[10px] bg-black/30 px-2.5 py-1 rounded-full font-bold text-white uppercase">Ready</span>
            </div>
            <div class="flex-1 p-3 overflow-y-auto space-y-3 kds-scroll relative z-10">
                @forelse($cookingOrders as $order)
                @include('dashboard.partials.order-card', ['order' => $order, 'type' => 'cooking'])
                @empty
                <div class="flex flex-col items-center justify-center h-full opacity-40 py-10">
                    <div class="empty-float">
                        <i class="fas fa-list-ul text-4xl text-blue-500/50 mb-4"></i>
                    </div>
                    <p class="text-xs font-bold text-textDim uppercase tracking-widest">Antrean kosong</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- KOLOM 3: Sedang Diproses --}}
        <div class="flex flex-col h-full bg-cardBg/40 rounded-2xl border border-borderColor overflow-hidden shadow-xl">
            <div class="bg-gradient-to-r from-emerald-900 to-emerald-800 border-b border-borderColor px-5 py-3.5 flex justify-between items-center z-10">
                <h2 class="font-bold text-sm tracking-wider text-white flex items-center gap-2">
                    <div class="p-1.5 bg-black/20 rounded-lg"><i class="fas fa-fire text-xs"></i></div> Sedang Diproses
                </h2>
                <span class="text-[10px] bg-black/30 px-2.5 py-1 rounded-full font-bold text-white uppercase">Cooking</span>
            </div>
            <div class="flex-1 p-3 overflow-y-auto space-y-3 kds-scroll relative z-10">
                @forelse($servingOrders as $order)
                @include('dashboard.partials.order-card', ['order' => $order, 'type' => 'serving'])
                @empty
                <div class="flex flex-col items-center justify-center h-full opacity-40 py-10">
                    <div class="empty-float">
                        <i class="fas fa-fire text-4xl text-emerald-500/50 mb-4"></i>
                    </div>
                    <p class="text-xs font-bold text-textDim uppercase tracking-widest">Tidak ada proses</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- KOLOM 4: Selesai / Batal --}}
        <div class="flex flex-col h-full bg-cardBg/20 rounded-2xl border border-borderColor/50 overflow-hidden opacity-80 hover:opacity-100 transition-all">
            <div class="bg-gradient-to-r from-secondary to-cardBg border-b border-borderColor px-5 py-3.5 flex justify-between items-center z-10">
                <h2 class="font-bold text-sm tracking-wider text-slate-200 flex items-center gap-2">
                    <div class="p-1.5 bg-black/20 rounded-lg"><i class="fas fa-check-double text-xs"></i></div> Selesai / Batal
                </h2>
                <span class="text-[10px] bg-black/30 px-2.5 py-1 rounded-full font-bold text-textDim">Hari Ini</span>
            </div>
            <div class="flex-1 p-3 overflow-y-auto space-y-3 kds-scroll bg-black/20">
                @if($completedOrders->isEmpty())
                <div class="flex flex-col items-center justify-center h-full opacity-40 py-10">
                    <div class="empty-float">
                        <i class="fas fa-check-double text-4xl text-slate-600 mb-4"></i>
                    </div>
                    <p class="text-xs font-bold text-textDim uppercase tracking-widest">Belum ada hari ini</p>
                </div>
                @else
                @foreach($completedOrders as $order)
                @include('dashboard.partials.order-card', ['order' => $order, 'type' => $order->status === 'served' ? 'completed' : 'cancelled'])
                @endforeach
                @endif
            </div>
        </div>

        <div id="cash-payment-modal" class="fixed inset-0 bg-black/80 z-50 hidden opacity-0 transition-opacity duration-300 items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-cardBg w-full max-w-md rounded-3xl overflow-hidden shadow-2xl border border-borderColor transform scale-95 transition-transform duration-300 flex flex-col" id="cash-payment-panel">
                <div class="p-5 border-b border-borderColor bg-secondary flex justify-between items-center">
                    <h3 class="text-lg font-black text-white">Konfirmasi Kasir (Tunai)</h3>
                    <button onclick="closeCashModal()" class="text-textDim hover:text-white w-8 h-8 flex items-center justify-center rounded-full hover:bg-primary transition-colors"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-6 flex-1 flex flex-col">
                    <div class="text-center mb-5">
                        <p class="text-[10px] font-bold text-textDim uppercase tracking-widest mb-1">Total Tagihan</p>
                        <p class="text-4xl font-black text-primary" id="modal-total-price">Rp 0</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-xs font-bold text-textDim mb-2">Nominal Cepat:</p>
                        <div class="grid grid-cols-2 gap-2" id="nominal-buttons"></div>
                    </div>

                    <div class="mb-5 relative">
                        <label class="text-xs font-bold text-textDim mb-2 block">Ketik Nominal Manual:</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-textDim">Rp</span>
                            <input type="number" id="manual-cash-input" class="w-full bg-secondary border border-borderColor text-white rounded-xl pl-11 pr-4 py-3 font-bold text-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all" placeholder="0" oninput="calculateChange()">
                        </div>
                    </div>

                    <div class="bg-secondary border border-borderColor rounded-xl p-4 mb-5 shadow-inner transition-colors" id="change-box">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-textDim uppercase tracking-wider">Kembalian:</span>
                            <span class="text-2xl font-black text-textDim" id="modal-change-amount">Rp 0</span>
                        </div>
                    </div>

                    <form id="cash-payment-form" method="POST" action="">
                        @csrf
                        <button type="button" id="btn-process-payment" onclick="submitCashPayment()" class="w-full bg-secondary text-textDim hover:bg-primary hover:text-white font-black py-3.5 rounded-xl flex items-center justify-center gap-2 cursor-not-allowed uppercase tracking-wider text-sm transition-all">
                            <i class="fas fa-lock"></i> Selesaikan Transaksi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- FIX 3: Styled Cancel Confirmation Modal --}}
        <div id="cancel-modal" class="fixed inset-0 bg-black/80 z-50 hidden opacity-0 transition-opacity duration-300 items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-cardBg w-full max-w-sm rounded-3xl overflow-hidden shadow-2xl border border-borderColor transform scale-95 transition-transform duration-300" id="cancel-panel">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 rounded-full bg-red-500/10 border-2 border-red-500/30 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-2xl text-red-400"></i>
                    </div>
                    <h3 class="text-lg font-black text-white mb-2">Batalkan Pesanan?</h3>
                    <p class="text-sm text-textDim mb-1">Pesanan <span id="cancel-order-id" class="font-bold text-primary"></span> akan dibatalkan.</p>
                    <p class="text-xs text-textDim">Stok menu akan otomatis dikembalikan.</p>
                </div>
                <div class="px-6 pb-6 flex gap-3">
                    <button onclick="closeCancelModal()" class="flex-1 bg-secondary hover:bg-primary text-textDim hover:text-white font-bold py-3 rounded-xl transition-all border border-borderColor">
                        Kembali
                    </button>
                    <form id="cancel-form" method="POST" action="" class="flex-1">
                        @csrf
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" onclick="this.innerHTML='<i class=\'fas fa-circle-notch fa-spin\'></i> Proses...'; this.disabled=true; this.closest('form').submit();" class="w-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-red-500/20">
                            <i class="fas fa-trash-alt mr-1.5"></i> Ya, Batalkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <audio id="notifSound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <script>
        // ==========================================
        // FIX 1: Toast Auto-Dismiss
        // ==========================================
        function dismissToast() {
            const toast = document.getElementById('kds-toast');
            if (toast) {
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-exit');
                setTimeout(() => toast.remove(), 300);
            }
        }
        // Auto-dismiss after 4 seconds
        setTimeout(dismissToast, 4000);

        // ==========================================
        // FIX 3: Styled Cancel Modal
        // ==========================================
        window.openCancelModal = function(actionUrl, orderId) {
            document.getElementById('cancel-form').action = actionUrl;
            document.getElementById('cancel-order-id').innerText = '#' + orderId;

            const modal = document.getElementById('cancel-modal');
            const panel = document.getElementById('cancel-panel');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100');
                panel.classList.remove('scale-95');
                panel.classList.add('scale-100');
            }, 30);
        };

        window.closeCancelModal = function() {
            const modal = document.getElementById('cancel-modal');
            const panel = document.getElementById('cancel-panel');
            modal.classList.remove('opacity-100');
            panel.classList.remove('scale-100');
            panel.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        };

        // ==========================================
        // FIX 4: Button Loading State
        // ==========================================
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form.kds-action-form').forEach(form => {
                form.addEventListener('submit', function() {
                    const btn = this.querySelector('button[type="submit"]');
                    if (btn) {
                        btn.disabled = true;
                        btn.classList.add('opacity-60', 'pointer-events-none');
                        const icon = btn.querySelector('i');
                        if (icon) {
                            icon.className = 'fas fa-circle-notch fa-spin mr-1.5';
                        }
                    }
                });
            });
        });

        // ==========================================
        // Clock
        // ==========================================
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('live-clock').innerHTML = `<i class="far fa-clock mr-1"></i> ${timeStr} WIB`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        let currentOrderTotal = 0;
        let selectedCash = 0;
        let isModalOpen = false;
        let pendingRefresh = false;

        const formatRp = (num) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(num);

        window.openCashModal = function(actionUrl, total) {
            isModalOpen = true;
            currentOrderTotal = parseInt(total) || 0;

            document.getElementById('cash-payment-form').action = actionUrl;
            document.getElementById('modal-total-price').innerText = formatRp(currentOrderTotal);
            document.getElementById('manual-cash-input').value = '';
            document.getElementById('modal-change-amount').innerText = 'Rp 0';
            document.getElementById('change-box').classList.remove('border-red-500/50', 'bg-red-500/10');

            let nominals = [currentOrderTotal];
            if (currentOrderTotal < 20000) nominals.push(20000);
            if (currentOrderTotal < 50000) nominals.push(50000);
            if (currentOrderTotal < 100000) nominals.push(100000);

            const next50k = Math.ceil(currentOrderTotal / 50000) * 50000;
            if (next50k > currentOrderTotal && !nominals.includes(next50k)) nominals.push(next50k);

            const uniqueNominals = [...new Set(nominals)].sort((a, b) => a - b);
            const btnContainer = document.getElementById('nominal-buttons');
            btnContainer.innerHTML = '';

            uniqueNominals.forEach(nom => {
                const isPas = nom === currentOrderTotal;
                btnContainer.innerHTML += `
                <button type="button" onclick="setCashAmount(${nom})" class="bg-secondary hover:bg-primary border ${isPas ? 'border-primary text-primary' : 'border-borderColor text-textDim'} font-bold py-2 px-3 rounded-xl transition-all flex flex-col items-center justify-center">
                    ${formatRp(nom)}
                </button>`;
            });

            validatePayment();

            const modal = document.getElementById('cash-payment-modal');
            const panel = document.getElementById('cash-payment-panel');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100');
                panel.classList.remove('scale-95');
                panel.classList.add('scale-100');
            }, 30);
        };

        window.closeCashModal = function() {
            const modal = document.getElementById('cash-payment-modal');
            const panel = document.getElementById('cash-payment-panel');

            modal.classList.remove('opacity-100');
            panel.classList.remove('scale-100');
            panel.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                isModalOpen = false;
                if (pendingRefresh) location.reload();
            }, 300);
        };

        window.setCashAmount = function(amount) {
            document.getElementById('manual-cash-input').value = amount;
            calculateChange();
        };

        window.calculateChange = function() {
            const inputVal = document.getElementById('manual-cash-input').value;
            selectedCash = parseInt(inputVal) || 0;
            const changeDisplay = document.getElementById('modal-change-amount');
            const changeBox = document.getElementById('change-box');

            if (selectedCash >= currentOrderTotal) {
                const change = selectedCash - currentOrderTotal;
                changeDisplay.innerText = formatRp(change);
                changeDisplay.className = 'text-3xl font-black text-emerald-400 tracking-tight';
                changeBox.classList.remove('border-red-500/50', 'bg-red-500/10');
            } else if (selectedCash > 0) {
                changeDisplay.innerText = 'Uang Kurang';
                changeDisplay.className = 'text-xl font-black text-red-400';
                changeBox.classList.add('border-red-500/50', 'bg-red-500/10');
            } else {
                changeDisplay.innerText = 'Rp 0';
                changeDisplay.className = 'text-2xl font-black text-textDim';
                changeBox.classList.remove('border-red-500/50', 'bg-red-500/10');
            }
            validatePayment();
        };

        function validatePayment() {
            const btn = document.getElementById('btn-process-payment');
            if (selectedCash >= currentOrderTotal) {
                btn.classList.remove('bg-secondary', 'text-textDim', 'cursor-not-allowed', 'hover:bg-primary', 'hover:text-white');
                btn.classList.add('bg-gradient-to-r', 'from-emerald-600', 'to-emerald-500', 'text-white', 'shadow-lg');
                btn.innerHTML = '<i class="fas fa-check-circle"></i> Selesaikan Transaksi';
                btn.disabled = false;
            } else {
                btn.classList.add('bg-secondary', 'text-textDim', 'cursor-not-allowed', 'hover:bg-primary', 'hover:text-white');
                btn.classList.remove('bg-gradient-to-r', 'from-emerald-600', 'to-emerald-500', 'text-white', 'shadow-lg');
                btn.innerHTML = '<i class="fas fa-lock"></i> Selesaikan Transaksi';
                btn.disabled = true;
            }
        }

        window.submitCashPayment = function() {
            if (selectedCash >= currentOrderTotal) {
                document.getElementById('btn-process-payment').innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memproses...';
                document.getElementById('cash-payment-form').submit();
            }
        };

        // Smart Auto-Refresh dengan Laravel Echo (WebSockets)
        // Tidak lagi menggunakan polling interval setiap 8 detik

        document.addEventListener('DOMContentLoaded', function () {
            if (window.Echo) {
                window.Echo.channel('kitchen')
                    .listen('KitchenUpdated', (e) => {
                        console.log('KitchenUpdated event received!');
                        if (!isModalOpen) {
                            setTimeout(() => location.reload(), 500);
                        } else {
                            pendingRefresh = true;
                        }
                    });
            } else {
                console.warn('Laravel Echo is not initialized. Make sure reverb server is running and vite is built.');
            }
        });
    </script>
</body>

</html>