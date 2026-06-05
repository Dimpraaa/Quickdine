@extends('layouts.admin')
@section('title', 'Manajemen Meja & QR - QuickDine')
@section('header_title', 'Manajemen Meja & QR')

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #print-area,
        #print-area * {
            visibility: visible;
        }

        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .no-print {
            display: none !important;
        }

        @page {
            margin: 0;
            size: auto;
        }
    }
</style>
@endpush

@section('content')
<div id="local-network-warning" class="no-print"></div>

<header class="flex justify-between items-center mb-8 no-print">
    <div>
        <h1 class="text-3xl font-black text-secondary tracking-tight">Manajemen Meja & QR</h1>
        <p class="text-secondary/80 font-medium mt-1">Kelola data meja dan cetak QR Code untuk pemesanan pelanggan.</p>
    </div>
    <button onclick="openAddModal()" class="bg-primary hover:bg-primaryDark text-white px-5 py-3 rounded-xl font-bold transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
        <i class="fas fa-plus"></i> Tambah Meja Baru
    </button>
</header>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 no-print">
    @foreach($tables as $table)
    @php
    $menuUrl = route('menu.index', ['table_number' => $table->table_number]);
    $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($menuUrl);
    @endphp

    <div class="bg-white rounded-2xl shadow-sm border border-borderColor overflow-hidden hover:-translate-y-1 hover:shadow-md transition-all duration-300 flex flex-col">

        <div class="bg-bgLight px-6 py-4 flex justify-between items-center border-b border-borderColor">
            <span class="text-secondary font-black text-lg">Meja {{ str_pad($table->table_number, 2, '0', STR_PAD_LEFT) }}</span>
            <span class="bg-emerald-100 text-emerald-700 border border-emerald-200 text-[10px] font-bold px-2.5 py-1 rounded uppercase tracking-wider">
                Aktif
            </span>
        </div>

        <div class="p-6 flex-1 flex flex-col items-center justify-center">
            <div class="bg-white p-2 rounded-xl shadow-sm border border-borderColor mb-4 hover:scale-105 transition-transform">
                <a href="{{ $menuUrl }}" target="_blank" title="Buka Halaman Menu">
                    <img src="{{ $qrImageUrl }}" alt="QR Meja {{ $table->table_number }}" class="w-32 h-32" id="qr-img-{{ $table->id }}">
                </a>
            </div>
            <p class="text-[10px] text-secondary/60 font-mono font-bold text-center break-all w-full bg-bgLight p-2 rounded-lg border border-borderColor">
                ID: {{ $table->qr_code_token }}
            </p>
        </div>

        <div class="p-4 border-t border-borderColor flex gap-3">
            <button onclick="printQRCode('{{ $table->table_number }}', '{{ $qrImageUrl }}')" class="flex-1 bg-white hover:bg-bgLight text-secondary font-bold py-2.5 rounded-xl text-sm transition-colors border border-borderColor shadow-sm flex items-center justify-center gap-2">
                <i class="fas fa-print text-secondary/60"></i> Cetak QR
            </button>

            <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST" onsubmit="return confirm('Hapus meja ini? Pastikan tidak ada pesanan aktif di meja ini.');" class="w-12">
                @csrf @method('DELETE')
                <button type="submit" class="w-full h-full bg-white hover:bg-red-50 text-red-600 rounded-xl transition-colors border border-borderColor hover:border-red-200 shadow-sm flex items-center justify-center" title="Hapus Meja">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

@if($tables->isEmpty())
<div class="text-center py-20 bg-white rounded-2xl border border-borderColor mt-6 no-print">
    <i class="fas fa-chair text-6xl text-secondary/30 mb-4"></i>
    <h2 class="text-xl font-black text-secondary">Belum Ada Meja</h2>
    <p class="text-secondary/60 font-medium mt-1">Silakan tambah meja baru untuk mulai mencetak QR Code.</p>
</div>
@endif

<!-- Modal Tambah -->
<div id="addModal" class="fixed inset-0 bg-secondary/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm no-print">
    <div class="bg-white rounded-3xl w-full max-w-sm p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-black text-secondary">Tambah Meja</h2>
            <button onclick="closeAddModal()" class="text-secondary/40 hover:text-secondary"><i class="fas fa-times text-xl"></i></button>
        </div>
        <form action="{{ route('admin.tables.store') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-bold text-secondary mb-2">Nomor Meja</label>
                <input type="number" name="table_number" min="1" class="w-full p-4 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none text-center text-2xl font-black text-secondary" required placeholder="Contoh: 1">
                <p class="text-xs font-bold text-secondary/60 mt-3 text-center"><i class="fas fa-qrcode text-primary mr-1"></i> QR Code akan dibuat otomatis.</p>
            </div>
            <div class="flex gap-4">
                <button type="button" onclick="closeAddModal()" class="flex-1 py-3 border border-borderColor text-secondary font-bold rounded-xl hover:bg-bgLight transition-colors">Batal</button>
                <button type="submit" class="flex-1 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primaryDark shadow-lg shadow-primary/30 transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Area Cetak -->
<div id="print-area" class="hidden">
    <div style="text-align: center; border: 2px dashed #352214; padding: 40px; border-radius: 20px; width: 400px; font-family: sans-serif; background: #FDFBF7;">
        <h1 style="font-size: 24px; font-weight: 900; margin-bottom: 5px; color: #B27C44;">QUICKDINE</h1>
        <p style="font-size: 14px; color: #352214; margin-bottom: 20px; font-weight: bold;">Scan QR di bawah ini untuk memesan</p>

        <img id="print-qr-image" src="" alt="QR" style="width: 250px; height: 250px; margin: 0 auto; border: 10px solid #fff; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

        <div style="margin-top: 20px; background: #352214; color: #FDFBF7; padding: 10px; border-radius: 10px;">
            <h2 style="font-size: 32px; font-weight: 900; margin: 0;">MEJA <span id="print-table-number"></span></h2>
        </div>
        <p style="font-size: 12px; color: #B27C44; margin-top: 15px; font-weight: bold;">Powered by QuickDine Self-Ordering</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    function printQRCode(tableNumber, qrUrl) {
        document.getElementById('print-table-number').innerText = tableNumber;
        document.getElementById('print-qr-image').src = qrUrl;
        document.getElementById('print-area').classList.remove('hidden');
        window.print();
        setTimeout(() => {
            document.getElementById('print-area').classList.add('hidden');
        }, 1000);
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('addModal')) closeAddModal();
    }

    // Warning jika diakses dari localhost
    document.addEventListener('DOMContentLoaded', function() {
        const hostname = window.location.hostname;
        if (hostname === '127.0.0.1' || hostname === 'localhost') {
            document.getElementById('local-network-warning').innerHTML = `
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-xl shadow-sm flex gap-3 no-print">
                    <i class="fas fa-exclamation-circle text-xl text-yellow-600 mt-0.5"></i>
                    <div>
                        <h4 class="font-bold mb-1">Testing QR Code di Handphone?</h4>
                        <p class="text-sm">Anda saat ini mengakses melalui <strong>${hostname}</strong>. Jika Anda scan QR ini menggunakan handphone, browser HP akan mencoba membuka localhost di HP itu sendiri (dan gagal).</p>
                        <p class="text-sm mt-1">Untuk testing di HP: Jalankan server dengan <code>php artisan serve --host=0.0.0.0</code> lalu buka halaman ini menggunakan IP WiFi komputer Anda (contoh: <code>192.168.1.5:8000</code>). Atau cukup <strong>klik gambar QR Code</strong> untuk membukanya di tab baru PC Anda.</p>
                    </div>
                </div>
            `;
        }
    });
</script>
@endpush