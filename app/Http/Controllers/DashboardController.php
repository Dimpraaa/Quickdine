<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Transisi status yang diizinkan (State Machine).
     * Key = status saat ini, Value = array status tujuan yang valid.
     */
    private const ALLOWED_TRANSITIONS = [
        'pending'   => ['preparing', 'cancelled'],
        'preparing' => ['served', 'cancelled'],
        'served'    => [],       // Final state, tidak bisa diubah
        'cancelled' => [],       // Final state, tidak bisa diubah
    ];

    public function kitchen()
    {
        // Pesanan aktif (pending, preparing) — tampilkan SEMUA tanpa filter tanggal
        $activeOrders = Order::with('items.menu', 'table')
            ->whereIn('status', ['pending', 'preparing'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Pesanan selesai (served, cancelled) — hanya HARI INI
        $completedOrders = Order::with('items.menu', 'table')
            ->whereIn('status', ['served', 'cancelled'])
            ->whereDate('created_at', today())
            ->orderBy('updated_at', 'desc')
            ->get();

        // Gabungkan untuk dikirim ke view
        $orders = $activeOrders->merge($completedOrders);

        // Meja yang memanggil pelayan
        $waiterCalls = \App\Models\Table::where('needs_waiter', true)->get();

        return view('dashboard.kitchen', compact('orders', 'waiterCalls'));
    }

    public function resolveWaiter(Request $request)
    {
        $request->validate(['table_number' => 'required']);
        $table = \App\Models\Table::where('table_number', $request->table_number)->first();
        if ($table) {
            $table->update(['needs_waiter' => false]);
            event(new \App\Events\KitchenUpdated()); // trigger refresh on KDS clients
        }
        return back()->with('success', 'Panggilan meja ' . $request->table_number . ' telah diselesaikan.');
    }

    /**
     * Endpoint JSON untuk smart auto-refresh KDS.
     * Mengembalikan jumlah pesanan per kolom tanpa reload full page.
     */
    public function kitchenCounts()
    {
        $unpaidCount = Order::where('payment_status', 'unpaid')
            ->whereNotIn('status', ['cancelled', 'served'])
            ->count();

        $pendingCount = Order::where('payment_status', 'paid')
            ->where('status', 'pending')
            ->count();

        $preparingCount = Order::where('status', 'preparing')->count();

        $servedTodayCount = Order::where('status', 'served')
            ->whereDate('created_at', today())
            ->count();

        $cancelledTodayCount = Order::where('status', 'cancelled')
            ->where('payment_status', '!=', 'unpaid') // Jangan hitung auto-cancel di metrik dapur
            ->whereDate('created_at', today())
            ->count();

        return response()->json([
            'unpaid'    => $unpaidCount,
            'pending'   => $pendingCount,
            'preparing' => $preparingCount,
            'served'    => $servedTodayCount,
            'cancelled' => $cancelledTodayCount,
            'total_active' => $unpaidCount + $pendingCount + $preparingCount,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,served,cancelled'
        ]);

        try {
            DB::beginTransaction();

            // Mencegah Race Condition (Staf 1 dan Staf 2 ngeklik Batal bersamaan)
            $order = Order::lockForUpdate()->with('items')->findOrFail($id);
            $newStatus = $request->status;

            // VALIDASI 1: Cek transisi status yang diizinkan (State Machine)
            $allowedNextStatuses = self::ALLOWED_TRANSITIONS[$order->status] ?? [];
            if (!in_array($newStatus, $allowedNextStatuses)) {
                DB::rollBack();
                return redirect()->back()->with('error',
                    "Status tidak dapat diubah dari '{$order->status}' ke '{$newStatus}'.");
            }

            // VALIDASI 2: Tolak perubahan ke 'preparing' jika belum bayar
            if ($newStatus === 'preparing' && $order->payment_status === 'unpaid') {
                DB::rollBack();
                return redirect()->back()->with('error',
                    'Pesanan belum dibayar. Konfirmasi pembayaran terlebih dahulu.');
            }

            // LOGIKA CANCEL: Kembalikan stok menu
            if ($newStatus === 'cancelled') {
                foreach ($order->items as $item) {
                    Menu::where('id', $item->menu_id)
                        ->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => $newStatus]);

            DB::commit();

            broadcast(new \App\Events\KitchenUpdated());

            $statusLabels = [
                'preparing' => 'Pesanan sedang dimasak.',
                'served'    => 'Pesanan telah disajikan.',
                'cancelled' => 'Pesanan dibatalkan. Stok menu telah dikembalikan.',
            ];

            return redirect()->back()->with('success',
                $statusLabels[$newStatus] ?? 'Status pesanan berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    public function confirmPayment($id)
    {
        $order = Order::findOrFail($id);

        if ($order->payment_status === 'unpaid') {
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'pending'
            ]);

            broadcast(new \App\Events\KitchenUpdated());

            return redirect()->back()->with('success', 'Pembayaran tunai diterima. Pesanan kini berada di Antrean Masak.');
        }

        return redirect()->back()->with('error', 'Pesanan ini sudah tercatat lunas.');
    }
}

