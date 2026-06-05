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
                        if (cart[id].qty <= 0) delete cart[id];
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
                    const qris = document.getElementById('method-qris');
                    const cash = document.getElementById('method-cash');
                    const iconQris = document.getElementById('icon-qris');
                    const iconCash = document.getElementById('icon-cash');

                    // Menggunakan padding yang lebih kecil (py-2 px-3) dan rounded-xl
                    if (method === 'qris') {
                        qris.className = 'border-2 border-primary bg-[#FDFBF7] rounded-xl py-2 px-3 flex items-center justify-center gap-2 cursor-pointer transition-all shadow-sm';
                        cash.className = 'border-2 border-[#EAE3D9] bg-white hover:bg-[#FDFBF7] rounded-xl py-2 px-3 flex items-center justify-center gap-2 cursor-pointer transition-all';
                        iconQris.className = 'fas fa-qrcode text-primary text-base';
                        iconCash.className = 'fas fa-money-bill-wave text-[#D0C8BF] text-base';
                    } else {
                        cash.className = 'border-2 border-primary bg-[#FDFBF7] rounded-xl py-2 px-3 flex items-center justify-center gap-2 cursor-pointer transition-all shadow-sm';
                        qris.className = 'border-2 border-[#EAE3D9] bg-white hover:bg-[#FDFBF7] rounded-xl py-2 px-3 flex items-center justify-center gap-2 cursor-pointer transition-all';
                        iconCash.className = 'fas fa-money-bill-wave text-primary text-base';
                        iconQris.className = 'fas fa-qrcode text-[#D0C8BF] text-base';
                    }
                };

                window.selectOrderType = function(type) {
                    selectedOrderType = type;
                    const dineIn = document.getElementById('type-dine_in');
                    const takeAway = document.getElementById('type-take_away');
                    const iconDineIn = document.getElementById('icon-dine_in');
                    const iconTakeAway = document.getElementById('icon-take_away');

                    if (type === 'dine_in') {
                        dineIn.className = 'border-2 border-primary bg-[#FDFBF7] rounded-xl py-2 px-3 flex items-center justify-center gap-2 cursor-pointer transition-all shadow-sm';
                        takeAway.className = 'border-2 border-[#EAE3D9] bg-white hover:bg-[#FDFBF7] rounded-xl py-2 px-3 flex items-center justify-center gap-2 cursor-pointer transition-all';
                        iconDineIn.className = 'fas fa-utensils text-primary text-base';
                        iconTakeAway.className = 'fas fa-shopping-bag text-[#D0C8BF] text-base';
                    } else {
                        takeAway.className = 'border-2 border-primary bg-[#FDFBF7] rounded-xl py-2 px-3 flex items-center justify-center gap-2 cursor-pointer transition-all shadow-sm';
                        dineIn.className = 'border-2 border-[#EAE3D9] bg-white hover:bg-[#FDFBF7] rounded-xl py-2 px-3 flex items-center justify-center gap-2 cursor-pointer transition-all';
                        iconTakeAway.className = 'fas fa-shopping-bag text-primary text-base';
                        iconDineIn.className = 'fas fa-utensils text-[#D0C8BF] text-base';
                    }
                };

                window.openCheckoutModal = function() {
                    if (qrisTimeout) clearTimeout(qrisTimeout);
                    const modal = document.getElementById('checkout-modal');
                    const panel = document.getElementById('checkout-panel');
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
                            <div class="pb-4 border-b border-[#EAE3D9] last:border-0 relative">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex gap-3 items-start">
                                        <div class="bg-[#FDFBF7] border border-[#EAE3D9] w-7 h-7 rounded-lg flex items-center justify-center font-black text-xs text-secondary shrink-0">${item.qty}x</div>
                                        <div>
                                            <h4 class="font-bold text-secondary text-sm leading-tight">${item.name}</h4>
                                            <span class="text-xs text-primary font-bold mt-0.5 inline-block">${formatRp(item.price)}</span>
                                        </div>
                                    </div>
                                    <span class="font-black text-secondary text-sm shrink-0 ml-2">${formatRp(item.price * item.qty)}</span>
                                </div>
                                <div class="pl-10 pr-1">
                                    <div class="relative">
                                        <i class="fas fa-pen absolute left-3 top-1/2 transform -translate-y-1/2 text-[#D0C8BF] text-[10px]"></i>
                                        <input type="text" placeholder="Catatan opsional..." 
                                            class="w-full text-[11px] bg-[#FCF9F2] text-secondary border border-[#EAE3D9] rounded-lg pl-8 pr-3 py-2 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary focus:bg-white transition-colors"
                                            value="${item.notes || ''}"
                                            onchange="updateNote(${item.id}, this.value)">
                                    </div>
                                </div>
                            </div>`;
                    }

                    const tax = subtotal * 0.10;
                    const grandTotal = subtotal + tax;

                    panel.innerHTML = `
                        <div class="flex justify-between items-center mb-4 pb-3 border-b border-[#EAE3D9]">
                            <div>
                                <h2 class="text-xl font-black text-secondary tracking-tight">Rincian Pesanan</h2>
                            </div>
                            <button onclick="closeCheckoutModal()" class="text-[#A69C94] w-8 h-8 bg-[#FDFBF7] border border-[#EAE3D9] rounded-full flex items-center justify-center hover:bg-[#EAE3D9] hover:text-secondary transition-colors"><i class="fas fa-times text-sm"></i></button>
                        </div>
                        
                        <div class="flex-1 overflow-y-auto space-y-4 no-scrollbar pr-2 mb-2">
                            ${itemsHtml}
                        </div>
                        
                        <div class="mt-2 pt-3 border-t border-[#EAE3D9]">
                            <div class="mb-2.5">
                                <h3 class="text-[9px] font-black text-[#A69C94] mb-1.5 uppercase tracking-widest">Tipe Pesanan</h3>
                                <div class="grid grid-cols-2 gap-2">
                                    <div id="type-dine_in" onclick="selectOrderType('dine_in')" class="border-2 ${selectedOrderType === 'dine_in' ? 'border-primary bg-[#FDFBF7]' : 'border-[#EAE3D9] bg-white hover:bg-[#FDFBF7]'} rounded-xl py-2 px-3 flex items-center justify-center gap-2 cursor-pointer transition-all shadow-sm">
                                        <i id="icon-dine_in" class="fas fa-utensils ${selectedOrderType === 'dine_in' ? 'text-primary' : 'text-[#D0C8BF]'} text-base"></i>
                                        <span class="font-bold text-xs text-secondary">Dine In</span>
                                    </div>
                                    <div id="type-take_away" onclick="selectOrderType('take_away')" class="border-2 ${selectedOrderType === 'take_away' ? 'border-primary bg-[#FDFBF7]' : 'border-[#EAE3D9] bg-white hover:bg-[#FDFBF7]'} rounded-xl py-2 px-3 flex items-center justify-center gap-2 cursor-pointer transition-all shadow-sm">
                                        <i id="icon-take_away" class="fas fa-shopping-bag ${selectedOrderType === 'take_away' ? 'text-primary' : 'text-[#D0C8BF]'} text-base"></i>
                                        <span class="font-bold text-xs text-secondary">Take Away</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h3 class="text-[9px] font-black text-[#A69C94] mb-1.5 uppercase tracking-widest">Metode Pembayaran</h3>
                                <div class="grid grid-cols-2 gap-2">
                                    <div id="method-qris" onclick="selectPayment('qris')" class="border-2 ${selectedPayment === 'qris' ? 'border-primary bg-[#FDFBF7]' : 'border-[#EAE3D9] bg-white'} rounded-xl py-2 px-3 flex items-center justify-center gap-2 cursor-pointer transition-all shadow-sm">
                                        <i id="icon-qris" class="fas fa-qrcode ${selectedPayment === 'qris' ? 'text-primary' : 'text-[#D0C8BF]'} text-base"></i>
                                        <span class="font-bold text-[10px] text-secondary text-center leading-tight">Transfer Bank<br>/ QRIS</span>
                                    </div>
                                    <div id="method-cash" onclick="selectPayment('cash')" class="border-2 ${selectedPayment === 'cash' ? 'border-primary bg-[#FDFBF7]' : 'border-[#EAE3D9] bg-white'} rounded-xl py-2 px-3 flex items-center justify-center gap-2 cursor-pointer transition-all shadow-sm">
                                        <i id="icon-cash" class="fas fa-money-bill-wave ${selectedPayment === 'cash' ? 'text-primary' : 'text-[#D0C8BF]'} text-base"></i>
                                        <span class="font-bold text-xs text-secondary">Tunai</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-[#FCF9F2] p-3 rounded-xl border border-[#EAE3D9] mb-3">
                                <div class="flex justify-between mb-1 text-[11px] font-semibold"><span class="text-[#8C837C]">Subtotal</span><span class="text-secondary">${formatRp(subtotal)}</span></div>
                                <div class="flex justify-between mb-2 text-[11px] font-semibold"><span class="text-[#8C837C]">Pajak (10%)</span><span class="text-secondary">${formatRp(tax)}</span></div>
                                <div class="flex justify-between pt-2 border-t border-dashed border-[#D0C8BF] items-center">
                                    <span class="font-black text-secondary text-xs">TOTAL</span>
                                    <span class="font-black text-primary text-base">${formatRp(grandTotal)}</span>
                                </div>
                            </div>

                            <button id="btn-checkout" onclick="processCheckout()" class="w-full bg-secondary hover:bg-[#20150F] text-white font-bold py-3 rounded-xl flex justify-center items-center gap-2 transition-colors shadow-xl active:scale-95 uppercase tracking-wide text-xs disabled:opacity-50 disabled:cursor-not-allowed">
                                <i id="btn-checkout-icon" class="fas fa-check-circle text-primary text-sm"></i> <span id="btn-checkout-text">Bayar & Proses Pesanan</span>
                            </button>
                        </div>`;
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

                window.submitOrder = async function(method) {
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
                    const btn = document.getElementById('btn-checkout');
                    btn.disabled = false;
                    document.getElementById('btn-checkout-icon').className = 'fas fa-check-circle text-primary text-sm';
                    document.getElementById('btn-checkout-text').innerText = 'Bayar & Proses Pesanan';
                }

                renderMenu();
            } catch (e) {
                console.error(e);
            }
        });
    </script>
</body>

</html>