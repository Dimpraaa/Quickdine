@extends('layouts.admin')
@section('title', 'Manajemen Akun - QuickDine')
@section('header_title', 'Manajemen Akun')

@section('content')
<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-black text-secondary tracking-tight">Manajemen Akun</h1>
        <p class="text-secondary/80 font-medium mt-1">Kelola data staf operasional (Admin & Crew) QuickDine.</p>
    </div>
    <button onclick="openAddModal()" class="bg-primary hover:bg-primaryDark text-white px-5 py-3 rounded-xl font-bold transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
        <i class="fas fa-user-plus"></i> Tambah Akun
    </button>
</header>

<div class="bg-white rounded-2xl shadow-sm border border-borderColor overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs font-bold text-secondary/80 uppercase tracking-wider bg-bgLight border-b border-borderColor">
                    <th class="px-8 py-4">Info Pengguna</th>
                    <th class="px-8 py-4">Peran (Role)</th>
                    <th class="px-8 py-4">Tanggal Dibuat</th>
                    <th class="px-8 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-borderColor/50">
                @foreach($users as $user)
                <tr class="hover:bg-bgLight transition-colors">
                    <td class="px-8 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white border border-borderColor flex items-center justify-center text-primary font-bold text-lg shadow-sm">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-sm text-secondary flex items-center gap-2">
                                    {{ $user->name }}
                                    @if($user->id === auth()->id())
                                    <span class="bg-primary/10 text-primaryDark text-[9px] px-2 py-0.5 rounded border border-primary/20 uppercase tracking-wider">Anda</span>
                                    @endif
                                </p>
                                <p class="text-xs font-bold text-secondary/60 mt-0.5">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-4">
                        @php
                        $roleBadge = match($user->role) {
                        'admin' => 'bg-red-50 text-red-700 border-red-200',
                        'staff' => 'bg-blue-50 text-blue-700 border-blue-200',
                        default => 'bg-bgLight text-secondary/70 border-borderColor',
                        };
                        @endphp
                        <span class="px-3 py-1 rounded border text-[10px] font-bold uppercase tracking-wider {{ $roleBadge }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-8 py-4 text-sm font-bold text-secondary">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    <td class="px-8 py-4">
                        <div class="flex justify-center items-center gap-3">
                            <button onclick="openEditModal({{ json_encode($user) }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Akun">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus akun ini secara permanen?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Akun">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
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
    <div class="bg-white rounded-3xl w-full max-w-md p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-black text-secondary">Tambah Akun Baru</h2>
            <button onclick="closeAddModal()" class="text-secondary/40 hover:text-secondary"><i class="fas fa-times text-xl"></i></button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-bold text-secondary mb-2">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-secondary mb-2">Alamat Email</label>
                    <input type="email" name="email" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-secondary mb-2">Kata Sandi Baru</label>
                    <input type="password" name="password" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary" required minlength="8" placeholder="Minimal 8 karakter">
                </div>
                <div>
                    <label class="block text-sm font-bold text-secondary mb-2">Peran Akses (Role)</label>
                    <select name="role" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary">
                        <option value="staff">Kru / Dapur (Staff)</option>
                        <option value="admin">Administrator (Admin)</option>
                    </select>
                </div>
            </div>
            <div class="mt-8 flex gap-4">
                <button type="button" onclick="closeAddModal()" class="flex-1 py-3 border border-borderColor text-secondary font-bold rounded-xl hover:bg-bgLight transition-colors">Batal</button>
                <button type="submit" class="flex-1 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primaryDark shadow-lg shadow-primary/30 transition-colors">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-secondary/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-md p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-black text-secondary">Edit Akun</h2>
            <button onclick="closeEditModal()" class="text-secondary/40 hover:text-secondary"><i class="fas fa-times text-xl"></i></button>
        </div>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-bold text-secondary mb-2">Nama Lengkap</label>
                    <input type="text" name="name" id="edit_name" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-secondary mb-2">Alamat Email</label>
                    <input type="email" name="email" id="edit_email" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-secondary mb-2">Peran Akses (Role)</label>
                    <select name="role" id="edit_role" class="w-full p-3 bg-bgLight border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary">
                        <option value="admin">Administrator (Admin)</option>
                        <option value="staff">Kru / Dapur (Staff)</option>
                    </select>
                </div>
                <div class="p-4 bg-bgLight border border-borderColor rounded-xl mt-4">
                    <label class="block text-sm font-bold text-secondary mb-2">Ubah Kata Sandi</label>
                    <input type="password" name="password" class="w-full p-3 bg-white border border-borderColor rounded-xl focus:ring-2 focus:ring-primary focus:outline-none font-semibold text-secondary" placeholder="Kosongkan jika tidak diubah" minlength="8">
                    <p class="text-xs font-bold text-secondary/60 mt-2"><i class="fas fa-info-circle text-primary mr-1"></i> Biarkan kosong jika hanya ingin mengubah data lain.</p>
                </div>
            </div>
            <div class="mt-8 flex gap-4">
                <button type="button" onclick="closeEditModal()" class="flex-1 py-3 border border-borderColor text-secondary font-bold rounded-xl hover:bg-bgLight transition-colors">Batal</button>
                <button type="submit" class="flex-1 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-600/30 transition-colors">Simpan Perubahan</button>
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

    function openEditModal(user) {
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_role').value = user.role;
        document.getElementById('editForm').action = `/admin/users/${user.id}`;
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