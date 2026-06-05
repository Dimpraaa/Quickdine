@extends('layouts.admin')
@section('title', 'Manajemen Menu - QuickDine')
@section('header_title', 'Manajemen Menu')

@section('content')
<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-black text-secondary tracking-tight">Manajemen Menu</h1>
        <p class="text-secondary/80 font-medium mt-1">Kelola daftar hidangan, harga, dan ketersediaan stok.</p>
    </div>
    <button onclick="openAddModal()" class="bg-primary hover:bg-primaryDark text-white px-5 py-3 rounded-xl font-bold transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
        <i class="fas fa-plus"></i> Tambah Menu
    </button>
</header>

<div class="bg-white rounded-2xl shadow-sm border border-borderColor overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs font-bold text-secondary/80 uppercase tracking-wider bg-bgLight border-b border-borderColor">
                    <th class="px-8 py-4">Info Menu</th>
                    <th class="px-8 py-4">Kategori</th>
                    <th class="px-8 py-4">Harga</th>
                    <th class="px-8 py-4">Status Stok</th>
                    <th class="px-8 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-borderColor/50">
                @foreach($menus as $menu)
                <tr class="hover:bg-bgLight transition-colors {{ $menu->stock <= 0 ? 'bg-red-50/20' : '' }}">
                    <td class="px-8 py-4">
                        <div class="flex items-center gap-4">
                            <div class="relative shrink-0">
                                <img src="{{ $menu->image_url ?? 'https://via.placeholder.com/100' }}" class="w-14 h-14 rounded-xl object-cover border border-borderColor {{ $menu->stock <= 0 ? 'grayscale opacity-60' : '' }}">
                            </div>
                            <div>
                                <p class="font-bold text-sm {{ $menu->stock <= 0 ? 'text-secondary/50 line-through' : 'text-secondary' }}">{{ $menu->name }}</p>
                                <p class="text-xs font-semibold text-secondary/60 mt-1 truncate max-w-[200px]">{{ $menu->description }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-4">
                        <span class="bg-white text-secondary px-3 py-1 rounded border border-borderColor text-xs font-bold uppercase tracking-wider">
                            {{ $menu->category->name }}
                        </span>
                    </td>
                    <td class="px-8 py-4 text-sm font-black text-secondary">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>

                    <td class="px-8 py-4">
                        @if($menu->stock <= 0)
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded border border-red-200 text-[10px] font-bold uppercase tracking-wider">
                            Sold Out
                            </span>
                            @else
                            <span class="text-sm font-bold {{ $menu->stock <= 5 ? 'text-primary' : 'text-secondary' }}">
                                {{ $menu->stock }} porsi
                            </span>
                            @endif
                    </td>

                    <td class="px-8 py-4">
                        <div class="flex justify-center items-center gap-3">
                            <form action="{{ route('admin.menu.toggle', $menu->id) }}" method="POST" onsubmit="return confirm('Ubah ketersediaan menu ini?');">
                                @csrf
                                @method('PATCH')
                                @if($menu->stock > 0)
                                <button type="submit" class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors border border-transparent hover:border-primary/20" title="Tandai Habis (Sold Out)">
                                    <i class="fas fa-ban"></i>
                                </button>
                                @else
                                <button type="submit" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors border border-transparent hover:border-emerald-200" title="Tandai Tersedia">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                                @endif
                            </form>

                            <button onclick="openEditModal({{ json_encode($menu) }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Menu">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.menu.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Hapus menu ini secara permanen?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Menu">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div id="addModal" class="fixed inset-0 bg-secondary/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-lg p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-black text-secondary">Tambah Menu Baru</h2>
            <button onclick="closeAddModal()" class="text-secondary/40 hover:text-secondary"><i class="fas fa-times text-xl"></i></button>
        </div>
        <form action="{{ route('admin.menu.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-secondary mb-2">Nama Menu</label>
                    <input type="text" name="name" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-secondary mb-2">Kategori</label>
                    <select name="category_id" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary">
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-secondary mb-2">Harga (Rp)</label>
                    <input type="number" name="price" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-secondary mb-2">Stok Awal</label>
                    <input type="number" name="stock" value="50" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-secondary mb-2">URL Gambar</label>
                    <input type="url" name="image_url" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-secondary mb-2">Deskripsi Singkat</label>
                    <textarea name="description" rows="3" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary"></textarea>
                </div>
            </div>
            <div class="mt-8 flex gap-4">
                <button type="button" onclick="closeAddModal()" class="flex-1 py-3 border border-borderColor text-secondary font-bold rounded-xl hover:bg-bgLight transition-colors">Batal</button>
                <button type="submit" class="flex-1 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primaryDark transition-colors shadow-lg shadow-primary/30">Simpan Menu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-secondary/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-lg p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-black text-secondary">Edit Menu</h2>
            <button onclick="closeEditModal()" class="text-secondary/40 hover:text-secondary"><i class="fas fa-times text-xl"></i></button>
        </div>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-secondary mb-2">Nama Menu</label>
                    <input type="text" name="name" id="edit_name" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-secondary mb-2">Kategori</label>
                    <select name="category_id" id="edit_category_id" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary">
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-secondary mb-2">Harga (Rp)</label>
                    <input type="number" name="price" id="edit_price" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-secondary mb-2">Stok</label>
                    <input type="number" name="stock" id="edit_stock" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-secondary mb-2">URL Gambar</label>
                    <input type="url" name="image_url" id="edit_image_url" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-secondary mb-2">Deskripsi Singkat</label>
                    <textarea name="description" id="edit_description" rows="3" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary"></textarea>
                </div>
            </div>
            <div class="mt-8 flex gap-4">
                <button type="button" onclick="closeEditModal()" class="flex-1 py-3 border border-borderColor text-secondary font-bold rounded-xl hover:bg-bgLight transition-colors">Batal</button>
                <button type="submit" class="flex-1 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primaryDark transition-colors shadow-lg shadow-primary/30">Simpan Perubahan</button>
            </div>
        </form>
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

    function openEditModal(menu) {
        document.getElementById('edit_name').value = menu.name;
        document.getElementById('edit_category_id').value = menu.category_id;
        document.getElementById('edit_price').value = menu.price;
        document.getElementById('edit_stock').value = menu.stock;
        document.getElementById('edit_image_url').value = menu.image_url || '';
        document.getElementById('edit_description').value = menu.description || '';
        document.getElementById('editForm').action = `/admin/menu/${menu.id}`;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
    window.onclick = function(event) {
        if (event.target == document.getElementById('addModal')) closeAddModal();
        if (event.target == document.getElementById('editModal')) closeEditModal();
    }
</script>
@endpush