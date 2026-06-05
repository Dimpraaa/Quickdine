<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Support\Str;
use App\Exports\SalesReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    // ==================================
    // MODUL DASHBOARD RINGKASAN
    // ==================================
    public function index()
    {
        // 0. Pseudo-Cron: Bersihkan pesanan QRIS kadaluarsa (> 15 menit)
        $expiredOrders = Order::where('payment_method', 'qris')
            ->where('payment_status', 'unpaid')
            ->where('status', 'pending')
            ->where('created_at', '<', Carbon::now()->subMinutes(15))
            ->get();

        foreach ($expiredOrders as $order) {
            $order->update(['status' => 'cancelled']);
            foreach ($order->items as $item) {
                if ($item->menu) {
                    $item->menu->increment('stock', $item->quantity);
                }
            }
        }

        // 1. Total Pendapatan Hari Ini
        $todayRevenue = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_price');

        // 2. Pesanan Sukses Hari Ini
        $completedOrdersCount = Order::whereDate('created_at', today())
            ->where('status', 'served')
            ->count();

        // 3. Menu Tersedia
        $activeMenuCount = Menu::where('stock', '>', 0)->count();

        // 4. 10 Transaksi Terakhir
        $recentOrders = Order::with('table')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // 5. 5 Ulasan Terbaru
        $recentReviews = Order::whereNotNull('rating')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // 6. Data Grafik 7 Hari Terakhir
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->translatedFormat('d M');
            $revenue = Order::whereDate('created_at', $date->toDateString())
                ->where('payment_status', 'paid')
                ->sum('total_price');
            $chartData[] = $revenue;
        }

        return view('admin.dashboard', compact(
            'todayRevenue',
            'completedOrdersCount',
            'activeMenuCount',
            'recentOrders',
            'recentReviews',
            'chartLabels',
            'chartData'
        ));
    }

    // ==================================
    // MODUL MANAJEMEN MENU
    // ==================================
    public function menuIndex()
    {
        $menus = Menu::with('category')->latest()->get();
        $categories = Category::all();
        return view('admin.menu', compact('menus', 'categories'));
    }

    public function menuStore(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'image_url' => 'nullable|url'
        ]);

        Menu::create($request->all());
        return redirect()->back()->with('success', 'Menu baru berhasil ditambahkan!');
    }

    public function menuUpdate(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'image_url' => 'nullable|url'
        ]);

        Menu::findOrFail($id)->update($request->all());
        return redirect()->back()->with('success', 'Data menu berhasil diperbarui!');
    }

    public function menuDestroy($id)
    {
        Menu::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Menu berhasil dihapus!');
    }

    // FITUR BARU: TOGGLE SOLD OUT
    public function menuToggleStock($id)
    {
        $menu = Menu::findOrFail($id);

        if ($menu->stock > 0) {
            $menu->update(['stock' => 0]);
            return redirect()->back()->with('success', "Menu '{$menu->name}' berhasil ditandai sebagai HABIS (Sold Out).");
        } else {
            $menu->update(['stock' => 50]); // Default isi ulang
            return redirect()->back()->with('success', "Menu '{$menu->name}' kembali TERSEDIA dengan stok 50 porsi.");
        }
    }

    // ==================================
    // MODUL MANAJEMEN MEJA & QR CODE
    // ==================================
    public function tableIndex()
    {
        $tables = Table::orderBy('table_number', 'asc')->get();
        return view('admin.tables', compact('tables'));
    }

    public function tableStore(Request $request)
    {
        $request->validate([
            'table_number' => 'required|integer|unique:tables,table_number|min:1',
        ], [
            'table_number.unique' => 'Nomor meja tersebut sudah digunakan.',
        ]);

        Table::create([
            'table_number' => $request->table_number,
            'status' => 'available',
            'qr_code_token' => uniqid('qr_') . Str::random(5),
        ]);

        return redirect()->back()->with('success', 'Meja baru berhasil ditambahkan!');
    }

    public function tableDestroy($id)
    {
        Table::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data meja berhasil dihapus!');
    }

    // ==================================
    // MODUL MANAJEMEN AKUN (PENGGUNA STAF)
    // ==================================
    public function userIndex()
    {
        // PERBAIKAN: Gunakan 'staff', hapus 'crew'
        $users = User::whereIn('role', ['admin', 'staff'])
            ->orderBy('role', 'asc')
            ->latest()
            ->get();

        return view('admin.users', compact('users'));
    }

    public function userStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,staff' // PERBAIKAN: Gunakan 'staff'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->back()->with('success', 'Akun staf baru berhasil ditambahkan!');
    }

    public function userUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,staff' // PERBAIKAN: Gunakan 'staff'
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8';
        }

        $request->validate($rules);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Data akun staf berhasil diperbarui!');
    }

    public function userDestroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Aksi Ditolak: Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Akun staf berhasil dihapus permanen!');
    }

    public function reportIndex(Request $request)
    {
        $startDate = $request->get('start_date', now()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $orderType = $request->get('order_type');
        $paymentMethod = $request->get('payment_method');

        $query = Order::with(['table', 'items.menu'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('payment_status', 'paid');

        if ($orderType) {
            $query->where('order_type', $orderType);
        }
        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $totalRevenue = (clone $query)->sum('total_price');

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        $totalOrders = $orders->total();

        // Data Chart.js (Berdasarkan Filter Tanggal)
        $chartDates = [];
        $chartRevenues = [];
        
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Batasi rentang maksimal agar loop tidak terlalu berat jika user pilih 1 tahun (opsional, misalnya max 31 hari)
        // Tapi untuk kebutuhan ini kita asumsikan render per hari
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $chartDates[] = $date->format('d M');
            
            $revQuery = Order::whereDate('created_at', $date->toDateString())
                ->where('payment_status', 'paid');
                
            if ($orderType) {
                $revQuery->where('order_type', $orderType);
            }
            if ($paymentMethod) {
                $revQuery->where('payment_method', $paymentMethod);
            }
                
            $chartRevenues[] = $revQuery->sum('total_price');
        }

        return view('admin.reports', compact(
            'orders',
            'startDate',
            'endDate',
            'orderType',
            'paymentMethod',
            'totalRevenue',
            'totalOrders',
            'chartDates',
            'chartRevenues'
        ));
    }

    public function reportExport(Request $request)
    {
        $startDate = $request->get('start_date', now()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $orderType = $request->get('order_type');
        $paymentMethod = $request->get('payment_method');

        return Excel::download(
            new SalesReportExport($startDate, $endDate, $orderType, $paymentMethod),
            "laporan-penjualan-{$startDate}-sd-{$endDate}.xlsx"
        );
    }

    public function reportPdf(Request $request)
    {
        $startDate = $request->get('start_date', now()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $orderType = $request->get('order_type');
        $paymentMethod = $request->get('payment_method');

        $query = Order::with(['table', 'items.menu'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('payment_status', 'paid');

        if ($orderType) {
            $query->where('order_type', $orderType);
        }
        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $orders = $query->orderBy('created_at', 'asc')->get();
        $totalRevenue = $orders->sum('total_price');

        $pdf = Pdf::loadView('admin.report_pdf', compact(
            'orders', 'startDate', 'endDate', 'orderType', 'paymentMethod', 'totalRevenue'
        ));

        $fileName = 'Laporan_Penjualan_' . $startDate . '_to_' . $endDate . '.pdf';
        return $pdf->download($fileName);
    }

    // ==================================
    // MODUL ULASAN PELANGGAN
    // ==================================
    public function reviewIndex(Request $request)
    {
        $ratingFilter = $request->get('rating');
        $sortOrder = $request->get('sort', 'desc');

        $query = Order::whereNotNull('rating');

        if ($ratingFilter) {
            $query->where('rating', $ratingFilter);
        }

        $reviews = $query->orderBy('updated_at', $sortOrder)
            ->paginate(15)
            ->appends(request()->query());
            
        $averageRating = Order::whereNotNull('rating')->avg('rating');
        $totalReviews = Order::whereNotNull('rating')->count();

        return view('admin.reviews', compact('reviews', 'averageRating', 'totalReviews', 'ratingFilter', 'sortOrder'));
    }

    // ==================================
    // MODUL PEMBATALAN TRANSAKSI (MANUAL)
    // ==================================
    public function cancelOrder($id)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Mencegah Race Condition ganda (Admin 1 dan Admin 2 klik batal bersamaan)
            $order = Order::lockForUpdate()->with('items.menu')->findOrFail($id);

            if ($order->status == 'cancelled') {
                \Illuminate\Support\Facades\DB::rollBack();
                return back()->with('error', 'Pesanan ini sudah dibatalkan sebelumnya.');
            }

            if ($order->status == 'served') {
                \Illuminate\Support\Facades\DB::rollBack();
                return back()->with('error', 'Pesanan yang sudah selesai tidak bisa dibatalkan.');
            }

            // Kembalikan stok
            foreach ($order->items as $item) {
                if ($item->menu) {
                    $item->menu->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => 'cancelled']);

            \Illuminate\Support\Facades\DB::commit();

            return back()->with('success', 'Pesanan ' . $order->transaction_id . ' berhasil dibatalkan dan stok telah dikembalikan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }
}
