@extends('layouts.admin')
@section('title', 'QuickDine - Ulasan Pelanggan')
@section('header_title', 'Ulasan Pelanggan')

@section('content')
<div class="mb-10">
    <h1 class="text-3xl font-black text-secondary tracking-tight">Ulasan Pelanggan</h1>
    <p class="text-secondary/80 font-medium mt-1">Lihat semua umpan balik dan penilaian dari pelanggan Anda.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
    <div class="dashboard-card bg-white p-7 rounded-2xl border border-borderColor shadow-sm flex flex-col justify-center">
        <div class="flex items-center gap-4 mb-2">
            <i class="fas fa-star text-4xl text-yellow-400"></i>
            <h3 class="text-4xl font-black text-secondary">{{ number_format($averageRating, 1) }} <span class="text-lg text-secondary/60">/ 5.0</span></h3>
        </div>
        <p class="text-sm font-bold text-secondary/60 uppercase tracking-wider">Rata-rata Penilaian</p>
    </div>
    
    <div class="dashboard-card bg-white p-7 rounded-2xl border border-borderColor shadow-sm flex flex-col justify-center">
        <div class="flex items-center gap-4 mb-2">
            <i class="fas fa-comment-dots text-4xl text-primary"></i>
            <h3 class="text-4xl font-black text-secondary">{{ $totalReviews }}</h3>
        </div>
        <p class="text-sm font-bold text-secondary/60 uppercase tracking-wider">Total Ulasan</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-borderColor shadow-sm overflow-hidden">
    <div class="px-8 py-6 border-b border-borderColor flex justify-between items-center bg-bgLight">
        <h2 class="text-lg font-black text-secondary">Daftar Ulasan</h2>
        
        <form action="{{ route('admin.reviews.index') }}" method="GET" class="flex gap-3">
            <select name="rating" onchange="this.form.submit()" class="border border-borderColor text-sm rounded-lg focus:ring-primary focus:border-primary p-2 font-bold text-secondary bg-white shadow-sm outline-none">
                <option value="">Semua Bintang</option>
                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Bintang (Sempurna)</option>
                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Bintang (Sangat Baik)</option>
                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Bintang (Cukup)</option>
                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Bintang (Kurang)</option>
                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Bintang (Buruk)</option>
            </select>

            <select name="sort" onchange="this.form.submit()" class="border border-borderColor text-sm rounded-lg focus:ring-primary focus:border-primary p-2 font-bold text-secondary bg-white shadow-sm outline-none">
                <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Terlama</option>
            </select>
        </form>
    </div>
    <div class="p-8">
        @forelse($reviews as $review)
        <div class="mb-6 pb-6 border-b border-borderColor last:mb-0 last:pb-0 last:border-0">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-secondary">Pelanggan (ID Transaksi: {{ $review->transaction_id }})</p>
                        <p class="text-[11px] text-secondary/60 font-medium">{{ $review->updated_at->format('d M Y, H:i') }}</p>
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
    @if($reviews->hasPages())
    <div class="px-8 py-4 border-t border-borderColor bg-bgLight">
        {{ $reviews->links() }}
    </div>
    @endif
</div>
@endsection