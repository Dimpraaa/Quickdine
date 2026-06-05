<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'QuickDine - Admin Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
                        borderColor: '#EAE3D9'
                    }
                }
            }
        }
    </script>
    <style>
        /* Scrollbar tipis dan bersih */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .dashboard-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
    @stack('styles')
</head>

<body class="bg-bgLight text-secondary flex h-screen overflow-hidden antialiased font-sans">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white border-r border-borderColor flex flex-col shrink-0 hidden md:flex">
        <div class="h-20 flex items-center px-8 border-b border-borderColor">
            <div class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center font-bold text-white text-xl shadow-lg shadow-primary/30 mr-3">
                Q
            </div>
            <span class="font-black text-xl tracking-tight text-secondary">QuickDine</span>
        </div>

        <nav class="flex-1 px-6 py-8 space-y-2 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary font-bold' : 'text-secondary font-semibold hover:bg-bgLight' }}">
                <i class="fas fa-th-large w-5"></i> Dashboard
            </a>

            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.reports.index') ? 'bg-primary/10 text-primary font-bold' : 'text-secondary font-semibold hover:bg-bgLight' }}">
                <i class="fas fa-file-invoice-dollar w-5"></i> Laporan
            </a>

            <a href="{{ route('admin.reviews.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.reviews.index') ? 'bg-primary/10 text-primary font-bold' : 'text-secondary font-semibold hover:bg-bgLight' }}">
                <i class="fas fa-star w-5"></i> Ulasan
            </a>

            <a href="{{ route('admin.menu.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.menu.index') ? 'bg-primary/10 text-primary font-bold' : 'text-secondary font-semibold hover:bg-bgLight' }}">
                <i class="fas fa-utensils w-5"></i> Menu
            </a>

            <a href="{{ route('admin.tables.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.tables.index') ? 'bg-primary/10 text-primary font-bold' : 'text-secondary font-semibold hover:bg-bgLight' }}">
                <i class="fas fa-table w-5"></i> Meja
            </a>

            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.users.index') ? 'bg-primary/10 text-primary font-bold' : 'text-secondary font-semibold hover:bg-bgLight' }}">
                <i class="fas fa-user-group w-5"></i> Staff
            </a>

            <div class="pt-4 mt-4 border-t border-borderColor">
                <a href="{{ route('kitchen.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-secondary font-semibold hover:bg-bgLight transition-all">
                    <i class="fas fa-desktop w-5"></i> Crew Station
                </a>
            </div>
        </nav>

        <div class="p-6 border-t border-borderColor">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full text-secondary font-semibold hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- HEADER -->
        <header class="h-20 bg-white flex items-center justify-between px-10 shrink-0 border-b border-borderColor">
            <h2 class="text-sm font-black text-secondary uppercase tracking-widest">@yield('header_title', 'Admin Panel')</h2>

            <div class="flex items-center gap-4">
                <div class="text-right pr-4 border-r border-borderColor">
                    <p class="text-sm font-bold text-secondary leading-none">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-gray-500 font-bold mt-1 uppercase">{{ auth()->user()->role }}</p>
                </div>
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=FDFBF7&color=B27C44&bold=true" class="w-10 h-10 rounded-full border border-borderColor shadow-sm">
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1 p-10 overflow-y-auto">
            @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl font-semibold shadow-sm flex items-center gap-3">
                <i class="fas fa-check-circle text-lg"></i> {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl font-semibold shadow-sm flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-lg"></i> {{ session('error') }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>
