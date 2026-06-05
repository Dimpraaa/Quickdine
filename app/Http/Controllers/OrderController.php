<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use App\Services\MidtransService;

class OrderController extends Controller
{
    protected $orderService;
    protected $midtransService;

    public function __construct(OrderService $orderService, MidtransService $midtransService)
    {
        $this->orderService = $orderService;
        $this->midtransService = $midtransService;
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->orderService->createOrder($request->validated());

            broadcast(new \App\Events\KitchenUpdated());

            // Jika guest (belum login), simpan order ID ke session agar bisa lihat riwayat
            if (!auth()->check()) {
                $guestOrders = session('guest_order_ids', []);
                $guestOrders[] = $order->id;
                session(['guest_order_ids' => $guestOrders]);
            }

            // Hapus memori Pesan Lagi setelah pesanan berhasil dibuat
            session()->forget('restore_cart');

            $response = [
                'success' => true,
                'redirect_url' => route('order.success', ['transaction_id' => $order->transaction_id]),
                'transaction_id' => $order->transaction_id
            ];

            // Jika QRIS, generate Snap Token menggunakan MidtransService
            if ($request->payment_method == 'qris') {
                $response['snap_token'] = $this->midtransService->getSnapToken($order);
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function success($transaction_id)
    {
        $order = Order::with(['items.menu', 'table'])->where('transaction_id', $transaction_id)->firstOrFail();
        
        // Fallback: Jika masih unpaid dan menggunakan Midtrans, coba cek status terbarunya
        if ($order->payment_status == 'unpaid' && $order->payment_method == 'qris' && $order->transaction_id) {
            try {
                $status = $this->midtransService->getTransactionStatus($order->transaction_id);
                $paymentName = $this->midtransService->getPaymentMethodName($status, $order->payment_method);

                if ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture') {
                    $order->update([
                        'payment_status' => 'paid',
                        'payment_method' => $paymentName,
                        'status' => 'pending' // Masuk ke Antrean Baru
                    ]);
                    broadcast(new \App\Events\KitchenUpdated());
                } else if ($status->transaction_status == 'cancel' || $status->transaction_status == 'expire' || $status->transaction_status == 'deny') {
                    $order->update([
                        'status' => 'cancelled',
                        'payment_method' => $paymentName
                    ]);
                } else {
                    $order->update([
                        'payment_method' => $paymentName
                    ]);
                }
            } catch (\Exception $e) {
                // Abaikan error misal transaksi belum tercatat di Midtrans
            }
        }

        return view('menu.success', compact('order'));
    }

    public function history($table_number)
    {
        $table = null;
        if ($table_number !== 'TA') {
            $table = \App\Models\Table::where('table_number', $table_number)->firstOrFail();
        }

        // Cari order berdasarkan guest session ATAU dari meja ini dalam 24 jam terakhir
        $guestOrders = session('guest_order_ids', []);

        $orders = Order::with(['items.menu', 'table'])
            ->where(function ($query) use ($table, $guestOrders) {
                $hasCondition = false;

                // 1. Pesanan dari guest session
                if (!empty($guestOrders)) {
                    $query->orWhereIn('id', $guestOrders);
                    $hasCondition = true;
                }
                
                // 2. Pesanan dari user yang sedang login
                if (auth()->check()) {
                    $query->orWhere('user_id', auth()->id());
                    $hasCondition = true;
                }

                // 3. Pesanan dari meja ini dalam 24 jam terakhir (untuk dine-in)
                if ($table) {
                    $query->orWhere(function ($q) use ($table) {
                        $q->where('table_id', $table->id)
                            ->where('created_at', '>=', now()->subHours(24));
                    });
                    $hasCondition = true;
                }

                // Cegah kebocoran data: Jika tidak ada kondisi yang terpenuhi, query harus mengembalikan 0 hasil
                if (!$hasCondition) {
                    $query->whereRaw('1 = 0');
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('menu.history', compact('table', 'orders', 'table_number'));
    }

    public function autoCancel(Request $request)
    {
        \Log::info('Auto-cancel triggered', ['transaction_id' => $request->transaction_id]);
        $request->validate(['transaction_id' => 'required']);
        
        try {
            DB::beginTransaction();
            // Lock Order untuk mencegah race condition dengan Webhook
            $order = Order::lockForUpdate()->with('items')->where('transaction_id', $request->transaction_id)->first();
            
            if ($order && $order->status === 'pending' && $order->payment_status === 'unpaid') {
                // Return stock
                foreach ($order->items as $item) {
                    $menu = Menu::where('id', $item->menu_id)->first();
                    if ($menu) {
                        $menu->increment('stock', $item->quantity);
                    }
                }
                
                $order->update(['status' => 'cancelled']);
                
                DB::commit();
                \Log::info('Auto-cancel success', ['order_id' => $order->id]);
                
                broadcast(new \App\Events\KitchenUpdated());
                
                return response()->json(['success' => true]);
            }
            
            DB::rollBack();
            \Log::warning('Auto-cancel rejected', [
                'order_exists' => $order ? true : false,
                'status' => $order->status ?? 'null',
                'payment_status' => $order->payment_status ?? 'null'
            ]);
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan atau tidak bisa dibatalkan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Auto-cancel failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Gagal membatalkan pesanan.']);
        }
    }

    public function webhook(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        
        $signatureKey = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        if ($signatureKey !== $request->signature_key) {
            \Log::warning('Midtrans Webhook Invalid Signature', ['order_id' => $request->order_id]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        try {
            DB::beginTransaction();
            // Lock order untuk mencegah race condition (misal autoCancel jalan bersamaan dgn Webhook)
            $order = Order::lockForUpdate()->with('items')->where('transaction_id', $request->order_id)->first();
            
            if (!$order) {
                DB::rollBack();
                return response()->json(['message' => 'Order not found'], 404);
            }

            $transactionStatus = $request->transaction_status;
            $paymentName = $this->midtransService->getPaymentMethodName((object)$request->all(), $order->payment_method);

            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                if ($order->payment_status !== 'paid') {
                    $order->update([
                        'payment_status' => 'paid',
                        'payment_method' => $paymentName,
                        'status' => 'pending' // Masuk ke antrean dapur KDS
                    ]);
                    broadcast(new \App\Events\KitchenUpdated());
                }
            } else if ($transactionStatus == 'cancel' || $transactionStatus == 'expire' || $transactionStatus == 'deny') {
                if ($order->status !== 'cancelled') {
                    // Kembalikan stok
                    foreach ($order->items as $item) {
                        $menu = Menu::where('id', $item->menu_id)->first();
                        if ($menu) {
                            $menu->increment('stock', $item->quantity);
                        }
                    }
                    $order->update([
                        'status' => 'cancelled',
                        'payment_method' => $paymentName
                    ]);
                    broadcast(new \App\Events\KitchenUpdated());
                }
            } else {
                $order->update([
                    'payment_method' => $paymentName
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'OK']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Webhook failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function receipt($transaction_id)
    {
        $order = Order::with(['items.menu', 'table'])->where('transaction_id', $transaction_id)->firstOrFail();
        
        return view('menu.receipt', compact('order'));
    }

    public function downloadReceiptPdf($transaction_id)
    {
        $order = Order::with(['items.menu', 'table'])->where('transaction_id', $transaction_id)->firstOrFail();
        
        // Kalkulasi tinggi kertas dinamis berdasarkan jumlah item
        $baseHeight = 380; // Tinggi dasar (termasuk header, footer, dan info WiFi)
        $itemHeight = 25;  // Perkiraan tinggi per baris item
        $paperHeight = $baseHeight + ($order->items->count() * $itemHeight);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('menu.receipt', compact('order'))
                    ->setPaper([0, 0, 226.77, $paperHeight], 'portrait');
                    
        return $pdf->download('Struk_QuickDine_' . $transaction_id . '.pdf');
    }

    public function submitReview(Request $request, $transaction_id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500'
        ]);

        $order = Order::where('transaction_id', $transaction_id)->firstOrFail();
        
        $order->update([
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Terima kasih atas ulasan Anda!']);
        }

        return redirect()->back()->with('success', 'Terima kasih atas ulasan Anda!');
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
        ]);

        $order = Order::where('transaction_id', $request->transaction_id)->first();
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        try {
            $status = $this->midtransService->getTransactionStatus($order->transaction_id);
            $paymentName = $this->midtransService->getPaymentMethodName($status, $order->payment_method);

            if ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture') {
                $order->update([
                    'payment_status' => 'paid',
                    'payment_method' => $paymentName,
                    'status' => 'pending' // Masuk ke Antrean Baru terlebih dahulu
                ]);
                broadcast(new \App\Events\KitchenUpdated());
            } else {
                $order->update([
                    'payment_method' => $paymentName
                ]);
            }

            return response()->json([
                'success' => true,
                'payment_status' => $order->payment_status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function cancelAndReorder(Request $request, $transaction_id)
    {
        try {
            DB::beginTransaction();
            
            $order = Order::lockForUpdate()->with('items.menu')->where('transaction_id', $transaction_id)->firstOrFail();
            
            if ($order->status !== 'pending' || $order->payment_status !== 'unpaid') {
                DB::rollBack();
                return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan.');
            }
            
            // Reconstruct the cart array
            $restoreCart = [];
            foreach ($order->items as $item) {
                // Restore stock
                if ($item->menu) {
                    $item->menu->increment('stock', $item->quantity);
                    
                    $restoreCart[] = [
                        'id' => $item->menu_id,
                        'name' => $item->menu->name,
                        'price' => $item->menu->price,
                        'quantity' => $item->quantity,
                        'image' => $item->menu->image
                    ];
                }
            }
            
            $order->update(['status' => 'cancelled']);
            
            DB::commit();
            broadcast(new \App\Events\KitchenUpdated());
            
            // Set session restore_cart
            session(['restore_cart' => $restoreCart]);
            
            $tableNum = session('current_table', $order->table ? $order->table->table_number : '0');
            if (empty($tableNum)) $tableNum = '0';
            
            return redirect()->route('menu.index', $tableNum)->with('success', 'Pesanan dibatalkan. Silakan pilih pembayaran baru.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan pesanan.');
        }
    }
    public function reorder(Request $request, $transaction_id)
    {
        try {
            $order = Order::with('items.menu')->where('transaction_id', $transaction_id)->firstOrFail();
            
            // Siapkan keranjang dengan item dari pesanan lama
            $restoreCart = [];
            foreach ($order->items as $item) {
                // Hanya tambahkan jika menu masih ada dan stok tersedia
                if ($item->menu && $item->menu->stock > 0) {
                    // Gunakan harga saat ini (price), bukan price_at_order, agar sinkron dengan menu saat ini
                    $restoreCart[] = [
                        'id' => $item->menu_id,
                        'name' => $item->menu->name,
                        'price' => $item->menu->price,
                        'quantity' => $item->quantity,
                        'image' => $item->menu->image
                    ];
                }
            }
            
            if (empty($restoreCart)) {
                return redirect()->back()->with('error', 'Maaf, menu dari pesanan ini sudah tidak tersedia atau kehabisan stok.');
            }
            
            // Set session restore_cart agar diambil oleh halaman menu
            session(['restore_cart' => $restoreCart]);
            
            $tableNum = session('current_table', $order->table ? $order->table->table_number : '1');
            if (empty($tableNum)) $tableNum = '1';
            
            return redirect()->route('menu.index', $tableNum)->with('success', 'Menu dari pesanan ini telah dimasukkan ke keranjang Anda!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal melakukan pesan lagi.');
        }
    }
}
