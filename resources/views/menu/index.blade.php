<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QuickDine - Menu</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

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
        /* Custom Premium Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
            margin-block: 4px; /* Memberi jarak agar tidak menabrak batas atas/bawah */
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #EAE3D9;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #D0C8BF;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-bgLight font-sans antialiased text-secondary selection:bg-primary selection:text-white">

    <div id="app-data" class="hidden" data-menus="{{ json_encode($menus) }}" data-table="{{ $table_number }}"></div>

    <div class="max-w-md mx-auto bg-bgLight min-h-screen relative shadow-2xl pb-32 flex flex-col">

        <header class="bg-secondary sticky top-0 z-10 shadow-md rounded-b-2xl overflow-hidden shrink-0">
            <div class="px-4 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="bg-primary w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-primary/30">
                        <i class="fas fa-mug-hot text-white text-lg"></i>
                    </div>
                    <div class="flex flex-col justify-center">
                        <h1 class="text-base font-black text-white tracking-tight leading-tight">QuickDine</h1>
                        @auth
                        @php
                        $firstName = explode(' ', auth()->user()->name)[0];
                        @endphp
                        <p class="text-[10px] text-primary font-bold tracking-wide">Halo, {{ $firstName }}!</p>
                        @endauth
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="bg-primary border border-primaryDark text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        Meja {{ $table_number }}
                    </div>

                    @guest
                    <a href="{{ route('order.history', ['table_number' => $table_number]) }}" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-3 py-1 rounded-full text-xs font-bold transition-all flex items-center gap-1 active:scale-95 shadow-sm">
                        <i class="fas fa-history text-[10px]"></i> Riwayat
                    </a>

                    <a href="{{ route('login') }}" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-3 py-1 rounded-full text-xs font-bold transition-all flex items-center gap-1 active:scale-95 shadow-sm">
                        <i class="fas fa-user text-[10px]"></i> Masuk
                    </a>
                    @else
                    <a href="{{ route('order.history', ['table_number' => $table_number]) }}" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-3 py-1 rounded-full text-xs font-bold transition-all flex items-center gap-1 active:scale-95 shadow-sm">
                        <i class="fas fa-history text-[10px]"></i> Riwayat
                    </a>

                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                    <div class="bg-amber-500/20 text-amber-300 border border-amber-500/30 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-1">
                        <i class="fas fa-user-shield"></i> Staf
                    </div>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500/10 hover:bg-red-500/30 text-red-400 border border-red-500/20 w-7 h-7 flex items-center justify-center rounded-full transition-all active:scale-95" title="Keluar Akun">
                            <i class="fas fa-power-off text-[10px]"></i>
                        </button>
                    </form>
                    @endguest
                </div>
            </div>

            <div class="px-4 pb-4">
                <div class="flex gap-2 relative">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-primary/70 text-sm"></i>
                        <input type="text" id="search-input" placeholder="Cari menu favoritmu..." class="w-full bg-black/20 text-white border border-white/5 text-sm rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-1 focus:ring-primary/50 transition-all placeholder-white/30 font-medium shadow-inner">
                    </div>
                    <button id="call-waiter-btn" onclick="callWaiter()" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-3 py-2 rounded-xl flex items-center justify-center transition-all shadow-sm active:scale-95" title="Panggil Pelayan">
                        <i class="fas fa-bell text-sm"></i>
                        <span id="call-waiter-text" class="text-[10px] ml-1.5 font-bold whitespace-nowrap">Panggil Pelayan</span>
                    </button>
                </div>
            </div>

            <div id="category-list" class="flex overflow-x-auto no-scrollbar space-x-2 px-4 py-3 bg-black/10 border-t border-white/5 shadow-inner">
                <button onclick="filterCategory('all', this)" class="category-btn whitespace-nowrap px-4 py-1.5 bg-primary text-white border border-transparent rounded-full text-xs font-bold transition-all shadow-sm shadow-primary/20">Semua</button>
                @foreach($categories as $category)
                <button onclick="filterCategory({{ $category->id }}, this)" class="category-btn whitespace-nowrap px-4 py-1.5 bg-white/5 text-white/60 border border-white/5 rounded-full text-xs font-semibold hover:bg-white/10 transition-all">{{ $category->name }}</button>
                @endforeach
            </div>
        </header>

        <main class="p-4 space-y-4 flex-1 pb-32" id="menu-container">
        </main>

        <div id="cart-bar" class="fixed bottom-0 max-w-md w-full bg-white border-t border-[#EAE3D9] p-4 transform translate-y-full transition-transform duration-300 z-20 shadow-[0_-10px_20px_rgba(53,34,20,0.05)] rounded-t-3xl">
            <div class="flex justify-between items-center mb-3 px-2">
                <div class="flex flex-col">
                    <span class="text-[10px] text-[#A69C94] font-bold uppercase tracking-wider">Estimasi Total</span>
                    <span id="cart-total-price" class="text-xl font-black text-secondary tracking-tight">Rp 0</span>
                </div>
                <div class="bg-[#FDFBF7] px-3 py-1.5 rounded-lg border border-[#EAE3D9] flex items-center gap-2">
                    <i class="fas fa-shopping-bag text-primary text-xs"></i>
                    <span id="cart-item-count" class="text-xs font-bold text-primary">0 Item</span>
                </div>
            </div>
            <button onclick="openCheckoutModal()" class="w-full bg-primary hover:bg-primaryDark text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-primary/30 flex justify-center items-center gap-2 active:scale-95 text-sm uppercase tracking-wide">
                <span>Konfirmasi Pesanan</span>
                <i class="fas fa-arrow-right text-xs"></i>
            </button>
        </div>

        <!-- Confirm Modal -->
        <div id="confirm-modal" class="fixed inset-0 z-[60] hidden">
            <div class="absolute inset-0 bg-secondary/60 backdrop-blur-sm transition-opacity opacity-0" id="confirm-backdrop" onclick="closeConfirmModal()"></div>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-sm w-full opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" id="confirm-panel">
                    <div class="bg-white px-6 py-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                            <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-xl leading-6 font-black text-secondary mb-2" id="confirm-title">Hapus Item</h3>
                        <p class="text-sm text-[#8C837C] font-medium" id="confirm-message">Apakah Anda yakin ingin menghapus item ini?</p>
                    </div>
                    <div class="bg-[#FDFBF7] px-6 py-4 flex gap-3">
                        <button type="button" onclick="closeConfirmModal()" class="flex-1 bg-white border-2 border-[#EAE3D9] text-[#8C837C] font-bold rounded-xl px-4 py-3 hover:bg-[#FDFBF7] transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#EAE3D9]">Batal</button>
                        <button type="button" id="confirm-btn" class="flex-1 bg-red-600 border-2 border-red-600 text-white font-bold rounded-xl px-4 py-3 hover:bg-red-700 hover:border-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600">Ya, Hapus</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="checkout-modal" class="fixed inset-0 bg-secondary/80 z-50 hidden opacity-0 transition-opacity duration-300 items-end sm:items-center justify-center backdrop-blur-sm">
            <div class="bg-white w-full max-w-md rounded-t-[2.5rem] sm:rounded-[2.5rem] p-6 h-[85vh] flex flex-col transform translate-y-full transition-transform duration-500 shadow-2xl" id="checkout-panel">
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            try {
                const appData = document.getElementById('app-data');
                const rawMenus = JSON.parse(appData.dataset.menus || '[]');
                const menus = Array.isArray(rawMenus) ? rawMenus : Object.values(rawMenus);
                const tableNumber = appData.dataset.table;
                let cart = {};
                let selectedPayment = 'qris';
                let selectedOrderType = 'dine_in'; // State baru untuk tipe pesanan
                let currentCategoryId = 'all';
                let searchQuery = '';
                let qrisTimeout = null;

                const formatRp = (number) => {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        maximumFractionDigits: 0
                    }).format(number || 0);
                };

                const escapeHtml = (unsafe) => {
                    return (unsafe || '').toString()
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;");
                };

                document.getElementById('search-input').addEventListener('input', (e) => {
                    searchQuery = e.target.value.toLowerCase();
                    renderMenu();
                });

                window.filterCategory = function(id, btn) {
                    currentCategoryId = id;
                    
                    // Reset all buttons to inactive state
                    document.querySelectorAll('.category-btn').forEach(b => {
                        b.className = 'category-btn whitespace-nowrap px-4 py-1.5 rounded-full text-xs transition-all bg-white/5 text-white/60 border border-white/5 font-semibold hover:bg-white/10';
                    });
                    
                    // Set clicked button to active state
                    btn.className = 'category-btn whitespace-nowrap px-4 py-1.5 rounded-full text-xs transition-all bg-primary text-white border border-transparent font-bold shadow-sm shadow-primary/20';
                    
                    renderMenu();
                };

                function renderMenu() {
                    const container = document.getElementById('menu-container');
                    container.innerHTML = '';

                    const filteredMenus = menus.filter(m => {
                        const matchCategory = currentCategoryId === 'all' || m.category_id == currentCategoryId;
                        const matchSearch = m.name.toLowerCase().includes(searchQuery) || (m.description && m.description.toLowerCase().includes(searchQuery));
                        return matchCategory && matchSearch;
                    });

                    if (filteredMenus.length === 0) {
                        container.innerHTML = `
                            <div class="text-center text-[#A69C94] mt-20 flex flex-col items-center">
                                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-4 border border-[#EAE3D9]">
                                    <i class="fas fa-search text-3xl text-[#D0C8BF]"></i>
                                </div>
                                <p class="font-bold text-secondary">Menu tidak ditemukan.</p>
                                <p class="text-xs text-[#A69C94] mt-1">Coba kata kunci pencarian yang lain.</p>
                            </div>`;
                        return;
                    }

                    filteredMenus.forEach(menu => {
                        const isSoldOut = parseInt(menu.stock) <= 0;
                        const isLowStock = !isSoldOut && parseInt(menu.stock) <= 5;
                        const qty = cart[menu.id] ? cart[menu.id].qty : 0;
                        const price = parseFloat(menu.price) || 0;

                        container.innerHTML += `
                            <div class="flex gap-4 p-3 bg-white border border-[#EAE3D9] rounded-2xl shadow-sm transition-all ${isSoldOut ? 'opacity-70' : 'hover:shadow-md'} relative overflow-hidden group">
                                
                                <div class="relative shrink-0">
                                    <img src="${menu.image_url || 'https://via.placeholder.com/200'}" alt="${menu.name}" class="w-24 h-24 object-cover rounded-xl shadow-sm ${isSoldOut ? 'grayscale' : 'group-hover:scale-105 transition-transform duration-500'} border border-[#F3EFE9]">
                                    ${isSoldOut ? `
                                    <div class="absolute inset-0 bg-black/50 rounded-xl flex items-center justify-center backdrop-blur-[1px]">
                                        <span class="bg-red-600 text-white text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded shadow-lg">Habis</span>
                                    </div>
                                    ` : ''}
                                </div>
                                
                                <div class="flex-1 flex flex-col justify-between py-1 pr-1">
                                    <div>
                                        <h3 class="font-bold ${isSoldOut ? 'text-[#8C837C]' : 'text-secondary'} leading-tight mb-1 text-[15px]">
                                            ${menu.name}
                                            ${isLowStock ? `<span class="ml-1 inline-block bg-red-100 text-red-600 text-[9px] px-1.5 py-0.5 rounded animate-pulse uppercase tracking-wider font-black border border-red-200 align-middle">Sisa ${menu.stock}</span>` : ''}
                                        </h3>
                                        <p class="text-xs text-[#8C837C] line-clamp-2 leading-relaxed">${menu.description || ''}</p>
                                    </div>
                                    <div class="flex justify-between items-center mt-3">
                                        <span class="font-black ${isSoldOut ? 'text-[#A69C94] line-through' : 'text-primary'} text-sm">${formatRp(price)}</span>
                                        
                                        ${isSoldOut ? `
                                            <span class="text-[9px] font-black text-red-500 bg-red-50 px-2 py-1 rounded-md border border-red-100 uppercase tracking-widest">Sold Out</span>
                                        ` : (qty > 0 ? `
                                            <div class="flex items-center gap-3 bg-[#FDFBF7] rounded-lg p-1 border border-[#EAE3D9] shadow-sm">
                                                <button onclick="updateQty(${menu.id}, -1)" class="w-7 h-7 flex justify-center items-center bg-white text-secondary hover:text-primary rounded-md shadow-sm active:scale-90 transition-all border border-[#F3EFE9]"><i class="fas fa-minus text-[10px]"></i></button>
                                                <span class="font-bold text-sm w-4 text-center text-secondary">${qty}</span>
                                                <button onclick="updateQty(${menu.id}, 1)" class="w-7 h-7 flex justify-center items-center bg-primary text-white rounded-md shadow-sm active:scale-90 transition-all"><i class="fas fa-plus text-[10px]"></i></button>
                                            </div>
                                        ` : `
                                            <button onclick="updateQty(${menu.id}, 1)" class="bg-[#FDFBF7] text-primary hover:bg-primary hover:text-white w-8 h-8 rounded-full flex justify-center items-center transition-all active:scale-90 font-bold border border-[#EAE3D9]">
                                                <i class="fas fa-plus text-sm"></i>
                                            </button>
                                        `)}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }

                let pendingConfirmAction = null;

                window.showConfirmModal = function(title, message, onConfirm) {
                    document.getElementById('confirm-title').innerText = title;
                    document.getElementById('confirm-message').innerText = message;
                    pendingConfirmAction = onConfirm;
                    
                    const modal = document.getElementById('confirm-modal');
                    const backdrop = document.getElementById('confirm-backdrop');
                    const panel = document.getElementById('confirm-panel');
                    
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        backdrop.classList.remove('opacity-0');
                        panel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
                        panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
                    }, 10);
                };

                window.closeConfirmModal = function() {
                    const modal = document.getElementById('confirm-modal');
                    const backdrop = document.getElementById('confirm-backdrop');
                    const panel = document.getElementById('confirm-panel');
                    
                    backdrop.classList.add('opacity-0');
                    panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
                    panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
                    
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        pendingConfirmAction = null;
                    }, 300);
                };

                document.getElementById('confirm-btn')?.addEventListener('click', () => {
                    if (pendingConfirmAction) pendingConfirmAction();
                    closeConfirmModal();
                });

                window.updateQty = function(id, change) {
                    const menu = menus.find(m => m.id === id);
                    if (change > 0) {
                        const currentQty = cart[id] ? cart[id].qty : 0;
                        if (currentQty + change > parseInt(menu.stock)) {
                            showToast(`Maaf, menu ${menu.name} saat ini hanya tersisa ${menu.stock} porsi.`, 'warning');
                            return;
                        }
                    }
                    if (!cart[id]) {
                        if (change > 0) cart[id] = {
                            ...menu,
                            qty: 1,
                            price: parseFloat(menu.price),
                            notes: ''
                        };
                    } else {
                        cart[id].qty += change;
                        if (cart[id].qty <= 0) {
                            cart[id].qty -= change; // kembalikan ke qty sebelumnya sementara
                            showConfirmModal(
                                'Hapus Item',
                                `Apakah Anda yakin ingin menghapus ${cart[id].name} dari pesanan?`,
                                function() {
                                    delete cart[id];
                                    showToast('Item dihapus dari pesanan');
                                    renderMenu();
                                    updateCartUI();
                                    const modal = document.getElementById('checkout-modal');
                                    if (!modal.classList.contains('hidden')) {
                                        openCheckoutModal();
                                    }
                                }
                            );
                            return; // Keluar tanpa update UI agar keranjang tidak berkedip
                        }
                    }
                    renderMenu();
                    updateCartUI();
                };

                window.updateNote = function(id, note) {
                    if (cart[id]) cart[id].notes = note;
                };

                function updateCartUI() {
                    const cartBar = document.getElementById('cart-bar');
                    let totalItems = 0;
                    let totalPrice = 0;
                    for (let id in cart) {
                        totalItems += cart[id].qty;
                        totalPrice += cart[id].price * cart[id].qty;
                    }
                    document.getElementById('cart-item-count').innerText = `${totalItems} Item`;
                    document.getElementById('cart-total-price').innerText = formatRp(totalPrice);
                    if (totalItems > 0) {
                        cartBar.classList.remove('translate-y-full');
                    } else {
                        cartBar.classList.add('translate-y-full');
                        closeCheckoutModal();
                    }
                }

                window.selectPayment = function(method) {
                    selectedPayment = method;
                    openCheckoutModal();
                };

                window.selectOrderType = function(type) {
                    selectedOrderType = type;
                    openCheckoutModal();
                };

                window.openCheckoutModal = function() {
                    if (qrisTimeout) clearTimeout(qrisTimeout);
                    const modal = document.getElementById('checkout-modal');
                    const panel = document.getElementById('checkout-panel');
                    
                    // Simpan posisi scroll sebelum di-render ulang
                    let currentScroll = 0;
                    const scrollContainer = document.getElementById('checkout-scroll-container');
                    if (scrollContainer) {
                        currentScroll = scrollContainer.scrollTop;
                    }
                    
                    // Simpan elemen input yang sedang fokus agar keyboard tidak tertutup
                    let focusedId = null;
                    if (document.activeElement && document.activeElement.tagName === 'INPUT') {
                        focusedId = document.activeElement.id;
                    }
                    
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        modal.classList.add('opacity-100', 'flex');
                        panel.classList.remove('translate-y-full');
                    }, 10);

                    let subtotal = 0;
                    let itemsHtml = '';
                    for (let id in cart) {
                        const item = cart[id];
                        subtotal += item.price * item.qty;
                        itemsHtml += `
                            <div class="bg-white p-4 rounded-2xl shadow-sm border border-[#EAE3D9] mb-3 relative group">
                                <div class="flex gap-4 items-center">
                                    <img src="${escapeHtml(item.image_url) || 'https://via.placeholder.com/100'}" alt="${escapeHtml(item.name)}" onerror="this.src='https://via.placeholder.com/100?text=Menu'" class="w-16 h-16 object-cover rounded-xl shadow-sm border border-[#F3EFE9]">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-secondary text-sm leading-tight mb-1 pr-6 line-clamp-2">${escapeHtml(item.name)}</h4>
                                        <span class="text-xs text-primary font-bold inline-block">${formatRp(item.price)}</span>
                                    </div>
                                    <div class="flex flex-col items-end gap-2 shrink-0">
                                        <span class="font-black text-secondary text-sm">${formatRp(item.price * item.qty)}</span>
                                        <div class="flex items-center gap-2 bg-[#FDFBF7] rounded-lg p-1 border border-[#EAE3D9] shadow-sm">
                                            <button onclick="updateQty(${item.id}, -1); openCheckoutModal();" class="w-6 h-6 flex justify-center items-center bg-white text-secondary hover:text-primary rounded-md shadow-sm active:scale-90 transition-all border border-[#F3EFE9]"><i class="fas fa-minus text-[9px]"></i></button>
                                            <span class="font-bold text-xs w-4 text-center text-secondary">${item.qty}</span>
                                            <button onclick="updateQty(${item.id}, 1); openCheckoutModal();" class="w-6 h-6 flex justify-center items-center bg-primary text-white rounded-md shadow-sm active:scale-90 transition-all"><i class="fas fa-plus text-[9px]"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 relative">
                                    <i class="fas fa-pen absolute left-3 top-1/2 transform -translate-y-1/2 text-[#D0C8BF] text-[10px]"></i>
                                    <input type="text" placeholder="Tambahkan catatan opsional..." class="w-full text-xs bg-[#FDFBF7] border border-[#EAE3D9] rounded-xl px-3 pl-8 py-2.5 focus:outline-none focus:ring-1 focus:ring-primary/30 focus:border-primary/50 transition-all font-medium text-secondary placeholder-[#A69C94]" id="note-${item.id}" value="${escapeHtml(item.notes || '')}" oninput="updateNote(${item.id}, this.value)">
                                </div>
                            </div>
`;
                    }

                    const tax = subtotal * 0.10;
                    const grandTotal = subtotal + tax;

                    panel.innerHTML = `
                        <div class="flex justify-between items-center mb-4 pb-3 border-b border-[#EAE3D9] px-2 pt-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center text-primary">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <h2 class="text-xl font-black text-secondary tracking-tight">Rincian Pesanan</h2>
                            </div>
                            <button onclick="closeCheckoutModal()" class="text-[#A69C94] w-8 h-8 bg-[#FDFBF7] border border-[#EAE3D9] rounded-full flex items-center justify-center hover:bg-[#EAE3D9] hover:text-secondary transition-colors shadow-sm"><i class="fas fa-times text-sm"></i></button>
                        </div>
                        
                        <div id="checkout-scroll-container" class="flex-1 overflow-y-auto space-y-1 custom-scrollbar pr-2 pl-1 pb-1 mb-2">
                            ${itemsHtml}
                        </div>
                        
                        <div class="mt-2 pt-4 border-t border-[#EAE3D9] px-1">
                            <!-- Options Container -->
                            <div class="flex gap-4 mb-4">
                                <!-- Order Type -->
                                <div class="flex-1">
                                    <h3 class="text-[10px] font-black text-[#A69C94] mb-2 uppercase tracking-widest flex items-center gap-1.5"><i class="fas fa-utensils"></i> Tipe Pesanan</h3>
                                    <div class="flex flex-col gap-2">
                                        <div id="type-dine_in" onclick="selectOrderType('dine_in')" class="border-2 ${selectedOrderType === 'dine_in' ? 'border-primary bg-[#FDFBF7]' : 'border-[#EAE3D9] bg-white hover:bg-[#FDFBF7]'} rounded-xl py-2.5 px-3 flex items-center gap-3 cursor-pointer transition-all shadow-sm">
                                            <div class="w-6 h-6 rounded-full ${selectedOrderType === 'dine_in' ? 'bg-primary text-white' : 'bg-[#EAE3D9] text-white'} flex items-center justify-center text-[10px] transition-colors"><i class="fas fa-check"></i></div>
                                            <span class="font-bold text-xs text-secondary">Dine In</span>
                                        </div>
                                        <div id="type-take_away" onclick="selectOrderType('take_away')" class="border-2 ${selectedOrderType === 'take_away' ? 'border-primary bg-[#FDFBF7]' : 'border-[#EAE3D9] bg-white hover:bg-[#FDFBF7]'} rounded-xl py-2.5 px-3 flex items-center gap-3 cursor-pointer transition-all shadow-sm">
                                            <div class="w-6 h-6 rounded-full ${selectedOrderType === 'take_away' ? 'bg-primary text-white' : 'bg-[#EAE3D9] text-white'} flex items-center justify-center text-[10px] transition-colors"><i class="fas fa-check"></i></div>
                                            <span class="font-bold text-xs text-secondary">Take Away</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Method -->
                                <div class="flex-1">
                                    <h3 class="text-[10px] font-black text-[#A69C94] mb-2 uppercase tracking-widest flex items-center gap-1.5"><i class="fas fa-wallet"></i> Pembayaran</h3>
                                    <div class="flex flex-col gap-2">
                                        <div id="method-qris" onclick="selectPayment('qris')" class="border-2 ${selectedPayment === 'qris' ? 'border-primary bg-[#FDFBF7]' : 'border-[#EAE3D9] bg-white'} rounded-xl py-2.5 px-3 flex items-center gap-3 cursor-pointer transition-all shadow-sm">
                                            <div class="w-6 h-6 rounded-full ${selectedPayment === 'qris' ? 'bg-primary text-white' : 'bg-[#EAE3D9] text-white'} flex items-center justify-center text-[10px] transition-colors"><i class="fas fa-qrcode"></i></div>
                                            <span class="font-bold text-[11px] text-secondary leading-tight">Transfer / QRIS</span>
                                        </div>
                                        <div id="method-cash" onclick="selectPayment('cash')" class="border-2 ${selectedPayment === 'cash' ? 'border-primary bg-[#FDFBF7]' : 'border-[#EAE3D9] bg-white'} rounded-xl py-2.5 px-3 flex items-center gap-3 cursor-pointer transition-all shadow-sm">
                                            <div class="w-6 h-6 rounded-full ${selectedPayment === 'cash' ? 'bg-primary text-white' : 'bg-[#EAE3D9] text-white'} flex items-center justify-center text-[10px] transition-colors"><i class="fas fa-money-bill-wave"></i></div>
                                            <span class="font-bold text-[11px] text-secondary">Tunai</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Summary -->
                            <div class="bg-white p-4 rounded-2xl border border-[#EAE3D9] shadow-sm mb-4">
                                <div class="flex justify-between mb-2 text-xs font-semibold"><span class="text-[#8C837C]">Subtotal</span><span class="text-secondary">${formatRp(subtotal)}</span></div>
                                <div class="flex justify-between mb-3 text-xs font-semibold"><span class="text-[#8C837C]">Pajak (10%)</span><span class="text-secondary">${formatRp(tax)}</span></div>
                                <div class="flex justify-between pt-3 border-t border-dashed border-[#D0C8BF] items-center">
                                    <span class="font-black text-secondary text-sm">TOTAL PEMBAYARAN</span>
                                    <span class="font-black text-primary text-lg">${formatRp(grandTotal)}</span>
                                </div>
                            </div>

                            <button id="btn-checkout" onclick="processCheckout()" ${isProcessingCheckout ? 'disabled' : ''} class="w-full bg-secondary hover:bg-[#20150F] text-white font-bold py-4 rounded-2xl flex justify-center items-center gap-2 transition-all shadow-xl shadow-secondary/20 active:scale-95 uppercase tracking-wider text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <i id="btn-checkout-icon" class="${isProcessingCheckout ? 'fas fa-circle-notch fa-spin text-white' : 'fas fa-check-circle text-primary text-sm'}"></i> <span id="btn-checkout-text">${isProcessingCheckout ? 'Memproses...' : 'Bayar Sekarang'}</span>
                            </button>
                        </div>`;
                        
                    const newScrollContainer = document.getElementById('checkout-scroll-container');
                    if (newScrollContainer) {
                        newScrollContainer.scrollTop = currentScroll;
                    }
                    
                    // Kembalikan fokus ke input yang sedang diketik (jika ada)
                    if (focusedId) {
                        const el = document.getElementById(focusedId);
                        if (el) {
                            el.focus();
                            // Pindahkan kursor ke akhir teks
                            const val = el.value;
                            el.value = '';
                            el.value = val;
                        }
                    }
                };

                window.closeCheckoutModal = function() {
                    if (qrisTimeout) clearTimeout(qrisTimeout);
                    const modal = document.getElementById('checkout-modal');
                    const panel = document.getElementById('checkout-panel');
                    modal.classList.remove('opacity-100');
                    panel.classList.add('translate-y-full');
                    setTimeout(() => {
                        modal.classList.remove('flex');
                        modal.classList.add('hidden');
                    }, 500);
                };

                window.processCheckout = function() {
                    submitOrder(selectedPayment);
                };

                let waiterCooldown = 0;
                let waiterInterval;

                window.showToast = function(message, type = 'success') {
                    if (type === 'success-center') {
                        const modal = document.createElement('div');
                        modal.className = `fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm opacity-0 transition-opacity duration-300`;
                        
                        modal.innerHTML = `
                            <div class="bg-white rounded-[2rem] p-8 flex flex-col items-center justify-center shadow-2xl transform scale-90 transition-transform duration-500 gap-3 max-w-xs w-full border border-[#EAE3D9]">
                                <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mb-1">
                                    <i class="fas fa-check-circle text-5xl text-primary"></i>
                                </div>
                                <h3 class="text-lg font-black text-secondary text-center leading-snug">${message}</h3>
                            </div>
                        `;
                        
                        document.body.appendChild(modal);
                        
                        // Animate in
                        setTimeout(() => {
                            modal.classList.remove('opacity-0');
                            modal.querySelector('div').classList.remove('scale-90');
                            modal.querySelector('div').classList.add('scale-100');
                        }, 10);
                        
                        return; // Modal remains until page redirects
                    }

                    const toast = document.createElement('div');
                    toast.className = `fixed top-4 left-1/2 transform -translate-x-1/2 z-[9999] px-5 py-3 rounded-2xl shadow-xl text-sm font-bold flex items-center gap-3 transition-all duration-500 opacity-0 -translate-y-4 bg-white border border-[#EAE3D9] text-secondary w-max max-w-[92vw] md:max-w-md`;
                    
                    let iconHtml = '';
                    if (type === 'error') iconHtml = '<i class="fas fa-exclamation-circle text-primary text-lg shrink-0"></i>';
                    else if (type === 'warning') iconHtml = '<i class="fas fa-exclamation-triangle text-primary text-lg shrink-0"></i>';
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

                window.callWaiter = async function() {
                    if (waiterCooldown > 0) return;

                    const btn = document.getElementById('call-waiter-btn');
                    const text = document.getElementById('call-waiter-text');
                    
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
                            
                            // Start Cooldown 60s
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

                let isProcessingCheckout = false;

                window.submitOrder = async function(method) {
                    if (isProcessingCheckout) return;
                    isProcessingCheckout = true;

                    const btn = document.getElementById('btn-checkout');
                    if (btn) {
                        btn.disabled = true;
                        document.getElementById('btn-checkout-icon').className = 'fas fa-circle-notch fa-spin text-white';
                        document.getElementById('btn-checkout-text').innerText = 'Memproses...';
                    }

                    const payload = {
                        table_number: tableNumber,
                        payment_method: method,
                        order_type: selectedOrderType,
                        cart: Object.values(cart)
                    };

                    try {
                        const response = await fetch('/checkout', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(payload)
                        });
                        const data = await response.json();

                        if (data.success) {
                            if (method === 'qris' && data.snap_token) {
                                window.snap.pay(data.snap_token, {
                                    onSuccess: async function(result){
                                        showToast('Pembayaran Berhasil!<br>Tunggu sebentar...', 'success-center');
                                        try {
                                            await fetch('/payment/verify', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                                },
                                                body: JSON.stringify({ transaction_id: data.transaction_id })
                                            });
                                        } catch (e) {
                                            console.error('Verifikasi gagal', e);
                                        }
                                        setTimeout(() => window.location.href = data.redirect_url, 1500);
                                    },
                                    onPending: function(result){
                                        window.location.href = data.redirect_url;
                                    },
                                    onError: function(result){
                                        showToast("Pembayaran gagal!", 'error');
                                        if (btn) resetCheckoutBtn();
                                    },
                                    onClose: async function(){
                                        showToast('Pembayaran dihentikan. Pesanan tadi sudah dibatalkan otomatis ya!', 'error');
                                        try {
                                            await fetch('{{ route("order.autoCancel") }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                                },
                                                body: JSON.stringify({ transaction_id: data.transaction_id })
                                            });
                                        } catch (e) {
                                            console.error('Gagal membatalkan otomatis', e);
                                        }
                                        if (btn) resetCheckoutBtn();
                                    }
                                });
                            } else {
                                showToast('Pesanan Berhasil Dibuat!<br>Tunggu sebentar...', 'success-center');
                                setTimeout(() => window.location.href = data.redirect_url, 1500);
                            }
                        } else {
                            showToast(data.message || 'Maaf, pesanan gagal diproses.', 'error');
                            if (btn) resetCheckoutBtn();
                        }
                    } catch (error) {
                        console.error(error);
                        showToast('Terjadi kesalahan jaringan. Silakan periksa koneksi Anda.', 'error');
                        if (btn) resetCheckoutBtn();
                    }
                };

                function resetCheckoutBtn() {
                    isProcessingCheckout = false;
                    const btn = document.getElementById('btn-checkout');
                    if (btn) {
                        btn.disabled = false;
                        document.getElementById('btn-checkout-icon').className = 'fas fa-check-circle text-primary text-sm';
                        document.getElementById('btn-checkout-text').innerText = 'Bayar Sekarang';
                    }
                }

                // Tampilkan pesan sukses dari session jika ada
                @if(session('success'))
                    setTimeout(() => showToast('{{ session("success") }}', 'success'), 500);
                @endif
                
                // Tampilkan pesan error dari session jika ada
                @if(session('error'))
                    setTimeout(() => showToast('{{ session("error") }}', 'error'), 500);
                @endif
                
                // Cek apakah ada session restore_cart (Fitur Ubah Metode Pembayaran)
                let restoreCartData = @json(session('restore_cart', []));
                if (restoreCartData && restoreCartData.length > 0) {
                    restoreCartData.forEach(item => {
                        cart[item.id] = {
                            id: item.id,
                            name: item.name,
                            price: parseFloat(item.price),
                            qty: item.quantity,
                            notes: ''
                        };
                    });
                    updateCartUI();
                    // Buka modal otomatis
                    document.getElementById('cart-overlay').classList.remove('hidden');
                    document.getElementById('cart-modal').classList.remove('translate-y-full');
                }
                
                renderMenu();
            } catch (e) {
                console.error(e);
            }
        });
    </script>
</body>

</html>