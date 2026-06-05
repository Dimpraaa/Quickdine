@extends('layouts.admin')
@section('title', 'QuickDine - Admin Dashboard')

@section('content')
<div class="mb-10">
    <h1 class="text-3xl font-black text-secondary tracking-tight">Halo, {{ explode(' ', auth()->user()->name)[0] }}!</h1>
    <p class="text-secondary/80 font-medium mt-1">Laporan aktivitas restoran hari ini, {{ now()->translatedFormat('d F Y') }}.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <div class="dashboard-card bg-white p-7 rounded-2xl border border-borderColor shadow-sm">
        <div class="w-12 h-12 bg-primary/10 text-primary rounded-xl flex items-center justify-center text-xl mb-6 border border-primary/20">
            <i class="fas fa-wallet"></i>
        </div>
        <p class="text-xs font-bold text-secondary/60 uppercase tracking-wider mb-1">Pendapatan Hari Ini</p>
        <h3 class="text-2xl font-black text-secondary">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
    </div>

    <div class="dashboard-card bg-white p-7 rounded-2xl border border-borderColor shadow-sm">
        <div class="w-12 h-12 bg-primary/10 text-primary rounded-xl flex items-center justify-center text-xl mb-6 border border-primary/20">
            <i class="fas fa-check-circle"></i>
        </div>
        <p class="text-xs font-bold text-secondary/60 uppercase tracking-wider mb-1">Pesanan Selesai</p>
        <h3 class="text-2xl font-black text-secondary">{{ $completedOrdersCount }} <span class="text-sm font-bold text-secondary/80 ml-1">Pesanan</span></h3>
    </div>

    <div class="dashboard-card bg-white p-7 rounded-2xl border border-borderColor shadow-sm">
        <div class="w-12 h-12 bg-primary/10 text-primary rounded-xl flex items-center justify-center text-xl mb-6 border border-primary/20">
            <i class="fas fa-utensils"></i>
        </div>
        <p class="text-xs font-bold text-secondary/60 uppercase tracking-wider mb-1">Menu Tersedia</p>
        <h3 class="text-2xl font-black text-secondary">{{ $activeMenuCount }} <span class="text-sm font-bold text-secondary/80 ml-1">Item</span></h3>
    </div>
</div>

<!-- GRAFIK PENDAPATAN 7 HARI TERAKHIR -->
<div class="bg-white rounded-2xl border border-borderColor shadow-sm p-8 mb-12">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-black text-secondary">Grafik Pendapatan</h2>
            <p class="text-sm text-secondary/60 font-medium">7 Hari Terakhir</p>
        </div>
        <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>
    <div class="relative h-72 w-full">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<div class="bg-white rounded-2xl border border-borderColor shadow-sm overflow-hidden">
    <div class="px-8 py-6 border-b border-borderColor flex justify-between items-center bg-bgLight">
        <h2 class="text-lg font-black text-secondary">10 Transaksi Terakhir</h2>
        <span class="text-[10px] font-bold text-secondary/80 border border-borderColor px-2.5 py-1 rounded-md bg-white uppercase tracking-widest">Update Real-time</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs font-bold text-secondary/80 uppercase tracking-wider bg-bgLight border-b border-borderColor">
                    <th class="px-8 py-4">ID Transaksi</th>
                    <th class="px-8 py-4">Waktu</th>
                    <th class="px-8 py-4">Meja</th>
                    <th class="px-8 py-4">Status</th>
                    <th class="px-8 py-4 text-right">Total Bayar</th>
                    <th class="px-8 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-borderColor/50">
                @forelse($recentOrders as $order)
                <tr class="hover:bg-bgLight transition-colors">
                    <td class="px-8 py-5 text-sm font-bold text-secondary">
                        {{ $order->transaction_id }}
                    </td>
                    <td class="px-8 py-5">
                        <p class="text-sm font-bold text-secondary">{{ $order->created_at->format('H:i') }} WIB</p>
                        <p class="text-[10px] font-bold text-secondary/60 mt-0.5">{{ $order->created_at->diffForHumans() }}</p>
                    </td>
                    <td class="px-8 py-5">
                        @if($order->order_type == 'take_away' || !$order->table)
                        <span class="text-sm font-bold text-primaryDark bg-primary/10 px-2.5 py-1 rounded-lg border border-primary/20">
                            Take Away
                        </span>
                        @else
                        <span class="text-sm font-bold text-secondary bg-borderColor/30 px-2.5 py-1 rounded-lg border border-borderColor">
                            Meja {{ $order->table->table_number }}
                        </span>
                        @endif
                    </td>
                    <td class="px-8 py-5">
                        @php
                        $badge = match($order->status) {
                        'pending' => 'bg-red-50 text-red-700 border-red-200',
                        'preparing' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'served' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'cancelled' => 'bg-gray-100 text-gray-700 border-gray-300',
                        default => 'bg-bgLight text-secondary border-borderColor',
                        };
                        @endphp
                        <span class="px-3 py-1 rounded border text-[10px] font-bold uppercase tracking-wider {{ $badge }}">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-right font-black text-secondary">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </td>
                    <td class="px-8 py-5 text-center">
                        @if($order->status == 'pending' && $order->payment_status == 'unpaid')
                        <form action="{{ route('admin.order.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini dan mengembalikan stoknya?');">
                            @csrf
                            <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors border border-red-200 hover:border-red-600">
                                Batal
                            </button>
                        </form>
                        @else
                        <span class="text-borderColor">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center justify-center text-secondary/40">
                            <i class="fas fa-inbox text-4xl mb-3"></i>
                            <p class="text-sm font-bold uppercase tracking-wider">Belum ada pesanan masuk</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-8 bg-white rounded-2xl border border-borderColor shadow-sm overflow-hidden">
    <div class="px-8 py-6 border-b border-borderColor flex justify-between items-center bg-bgLight">
        <h2 class="text-lg font-black text-secondary">Ulasan Pelanggan Terbaru</h2>
        <span class="text-[10px] font-bold text-secondary/80 border border-borderColor px-2.5 py-1 rounded-md bg-white uppercase tracking-widest">Feedback</span>
    </div>
    <div class="p-8">
        @forelse($recentReviews as $review)
        <div class="mb-6 pb-6 border-b border-borderColor last:mb-0 last:pb-0 last:border-0">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-secondary">Pelanggan (ID: {{ substr($review->transaction_id, -6) }})</p>
                        <p class="text-[11px] text-secondary/60 font-medium">{{ $review->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex gap-1 text-yellow-400 text-sm">
                    @for($i=1; $i<=5; $i++)
                        <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-borderColor' }}"></i>
                    @endfor
                </div>
            </div>
            <p class="text-secondary/80 text-sm italic pl-13 ml-13">"{{ $review->review ?? 'Tidak ada teks ulasan.' }}"</p>
        </div>
        @empty
        <div class="text-center py-10 text-secondary/40">
            <i class="fas fa-comment-slash text-4xl mb-3"></i>
            <p class="text-sm font-bold uppercase tracking-wider">Belum ada ulasan</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // Gradient fill for Primary (#B27C44)
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(178, 124, 68, 0.2)'); // #B27C44 with opacity
        gradient.addColorStop(1, 'rgba(178, 124, 68, 0)');

        const labels = {!! json_encode($chartLabels) !!};
        const data = {!! json_encode($chartData) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: data,
                    borderColor: '#B27C44', // Primary
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#8B5A2B', // PrimaryDark
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // curve
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#352214',
                        titleColor: '#FDFBF7',
                        bodyColor: '#FDFBF7',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#EAE3D9', // borderColor
                            drawBorder: false
                        },
                        ticks: {
                            color: '#8C837C',
                            font: {
                                family: "'Inter', sans-serif",
                                size: 11
                            },
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'Jt';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000).toFixed(0) + 'Rb';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#8C837C',
                            font: {
                                family: "'Inter', sans-serif",
                                size: 11
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });
    });
</script>
@endpush