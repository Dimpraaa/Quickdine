@extends('layouts.admin')
@section('title', 'QuickDine - Laporan Penjualan')
@section('header_title', 'Laporan Penjualan')

@push('styles')
<style>
    /* Custom Pagination Styling */
    nav[role="navigation"] a,
    nav[role="navigation"] span[aria-disabled="true"] span {
        background-color: #ffffff !important;
        color: #352214 !important;
        border-color: #EAE3D9 !important;
        transition: all 0.2s ease;
    }

    nav[role="navigation"] a:hover {
        background-color: #FDFBF7 !important;
        color: #B27C44 !important;
    }

    nav[role="navigation"] span[aria-current="page"] span {
        background-color: #352214 !important;
        color: #ffffff !important;
        border-color: #352214 !important;
    }

    nav[role="navigation"] a:focus,
    nav[role="navigation"] span:focus {
        outline: none !important;
        box-shadow: none !important;
    }
</style>
@endpush

@section('content')
<div class="mb-10">
    <h1 class="text-3xl font-black text-secondary tracking-tight">Riwayat Transaksi</h1>
</div>

<div class="bg-white p-6 rounded-2xl border border-borderColor shadow-sm mb-8">
    <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs font-bold text-secondary/60 uppercase mb-2">Mulai</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="border border-borderColor rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary outline-none text-sm text-secondary">
        </div>
        <div>
            <label class="block text-xs font-bold text-secondary/60 uppercase mb-2">Sampai</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="border border-borderColor rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary outline-none text-sm text-secondary">
        </div>
        <div>
            <label class="block text-xs font-bold text-secondary/60 uppercase mb-2">Tipe</label>
            <select name="order_type" class="border border-borderColor rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary outline-none bg-white text-sm text-secondary">
                <option value="">Semua Tipe</option>
                <option value="dine_in" {{ $orderType == 'dine_in' ? 'selected' : '' }}>Dine In</option>
                <option value="take_away" {{ $orderType == 'take_away' ? 'selected' : '' }}>Take Away</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-secondary/60 uppercase mb-2">Metode</label>
            <select name="payment_method" class="border border-borderColor rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary outline-none bg-white text-sm text-secondary">
                <option value="">Semua Metode</option>
                <option value="cash" {{ $paymentMethod == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="qris" {{ $paymentMethod == 'qris' ? 'selected' : '' }}>QRIS</option>
            </select>
        </div>
        <button type="submit" class="bg-secondary text-white px-6 py-2 rounded-lg font-bold hover:bg-secondary/90 transition-all shadow-sm text-sm">
            <i class="fas fa-filter mr-2"></i> Filter
        </button>
        <a href="{{ route('admin.reports.export', request()->query()) }}" class="bg-emerald-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-emerald-700 transition-all shadow-sm text-sm">
            <i class="fas fa-file-excel mr-2"></i> Excel
        </a>
        <a href="{{ route('admin.reports.pdf', request()->query()) }}" class="bg-red-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-red-700 transition-all shadow-sm text-sm">
            <i class="fas fa-file-pdf mr-2"></i> PDF
        </a>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
    <div class="dashboard-card bg-white p-7 rounded-2xl border border-borderColor shadow-sm flex flex-col justify-between">
        <div>
            <p class="text-xs font-bold text-secondary/60 uppercase tracking-wider mb-1">Pendapatan Periode Ini</p>
            <h3 class="text-2xl font-black text-secondary">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
        </div>
        <div class="mt-4">
            <p class="text-xs font-bold text-secondary/60 uppercase tracking-wider mb-1">Total Transaksi</p>
            <h3 class="text-2xl font-black text-secondary">{{ $totalOrders }} <span class="text-sm font-bold text-secondary/80 ml-1">Pesanan</span></h3>
        </div>
    </div>
    
    <div class="dashboard-card bg-white p-4 rounded-2xl border border-borderColor shadow-sm">
        <p class="text-xs font-bold text-secondary/60 uppercase tracking-wider mb-2">Tren Pendapatan</p>
        <div class="relative h-48">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-borderColor shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs font-bold text-secondary/80 uppercase tracking-wider bg-bgLight border-b border-borderColor">
                    <th class="px-8 py-4">No.</th>
                    <th class="px-8 py-4">ID Transaksi</th>
                    <th class="px-8 py-4">Waktu</th>
                    <th class="px-8 py-4">Pesanan</th>
                    <th class="px-8 py-4">Tipe</th>
                    <th class="px-8 py-4">Metode</th>
                    <th class="px-8 py-4 text-right">Total</th>
                    <th class="px-8 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-borderColor/50">
                @forelse($orders as $order)
                <tr class="hover:bg-bgLight transition-colors">
                    <td class="px-8 py-5 text-sm font-bold text-secondary/60">
                        {{ (($orders->currentPage() - 1) * $orders->perPage()) + $loop->iteration }}
                    </td>
                    <td class="px-8 py-5 text-sm font-bold text-secondary">{{ $order->transaction_id }}</td>
                    <td class="px-8 py-5">
                        <p class="text-sm font-bold text-secondary">{{ $order->created_at->format('d M Y') }}</p>
                        <p class="text-[10px] font-bold text-secondary/60 uppercase">{{ $order->created_at->format('H:i') }} WIB</p>
                    </td>

                    <td class="px-8 py-5">
                        <div class="text-xs text-secondary/80 space-y-1">
                            @foreach($order->items as $item)
                            <p class="font-medium">• {{ $item->menu->name ?? 'Menu Dihapus' }} <span class="text-secondary/50">x{{ $item->quantity }}</span></p>
                            @endforeach
                        </div>
                    </td>

                    <td class="px-8 py-5 text-sm">
                        @if($order->order_type == 'take_away' || !$order->table)
                        <span class="text-[10px] font-bold text-primaryDark bg-primary/10 px-2 py-1 rounded-md border border-primary/20">TAKE AWAY</span>
                        @else
                        <span class="text-[10px] font-bold text-secondary bg-white px-2 py-1 rounded-md border border-borderColor">MEJA {{ $order->table->table_number }}</span>
                        @endif
                    </td>
                    <td class="px-8 py-5 text-sm font-bold text-secondary/80 uppercase">{{ $order->payment_method }}</td>
                    <td class="px-8 py-5 text-right font-black text-secondary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td class="px-8 py-5 text-center">
                        <button onclick="window.open('{{ route('order.receipt', $order->id) }}', 'CetakStruk', 'width=400,height=600')" class="bg-white border border-borderColor text-secondary/60 hover:text-primary hover:bg-bgLight p-2 rounded-lg transition-colors">
                            <i class="fas fa-print"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center text-secondary/40">
                            <i class="fas fa-file-invoice text-4xl mb-3"></i>
                            <p class="text-sm font-bold uppercase tracking-wider">Tidak ada data untuk periode ini</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-8 py-6 bg-bgLight border-t border-borderColor">
        {{ $orders->appends(request()->query())->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const chartDates = @json($chartDates);
        const chartRevenues = @json($chartRevenues);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartDates,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: chartRevenues,
                    borderColor: '#B27C44',
                    backgroundColor: 'rgba(178, 124, 68, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#B27C44',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#B27C44',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000) + 'M';
                                } else if (value >= 1000) {
                                    return (value / 1000) + 'k';
                                }
                                return value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush