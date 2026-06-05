<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create new order using DB transaction and lockForUpdate
     */
    public function createOrder(array $validatedData)
    {
        return DB::transaction(function () use ($validatedData) {
            $table_id = null;

            if ($validatedData['order_type'] === 'dine_in') {
                $table = Table::where('table_number', $validatedData['table_number'])->first();
                if (!$table) {
                    throw new \Exception('Meja tidak ditemukan');
                }
                $table_id = $table->id;
            }

            $subtotal = 0;
            $verifiedCart = [];
            
            foreach ($validatedData['cart'] as $item) {
                // PESSIMISTIC LOCKING: lockForUpdate()
                $menu = Menu::lockForUpdate()->findOrFail($item['id']);
                
                if ($menu->stock < $item['qty']) {
                    throw new \Exception("Maaf, stok {$menu->name} tidak mencukupi. Sisa stok: {$menu->stock}");
                }

                $actualPrice = $menu->price;
                $subtotal += $actualPrice * $item['qty'];
                
                $verifiedCart[] = [
                    'menu' => $menu,
                    'qty' => $item['qty'],
                    'notes' => $item['notes'] ?? null,
                    'actual_price' => $actualPrice
                ];
            }
            
            $tax = $subtotal * 0.10;
            $total_price = $subtotal + $tax;

            $paymentStatus = 'unpaid';
            if ($validatedData['payment_method'] == 'cash') {
                $paymentStatus = 'unpaid';
            }

            $order = Order::create([
                'user_id'        => auth()->id(),
                'table_id'       => $table_id,
                'order_type'     => $validatedData['order_type'],
                'total_price'    => $total_price,
                'status'         => 'pending',
                'payment_status' => $paymentStatus,
                'payment_method' => $validatedData['payment_method'],
                'transaction_id' => 'TRX-' . time() . '-' . rand(100, 999),
            ]);

            foreach ($verifiedCart as $item) {
                $menu = $item['menu'];
                $menu->decrement('stock', $item['qty']);

                OrderItem::create([
                    'order_id'       => $order->id,
                    'menu_id'        => $menu->id,
                    'quantity'       => $item['qty'],
                    'price_at_order' => $item['actual_price'],
                    'subtotal'       => $item['actual_price'] * $item['qty'],
                    'notes'          => $item['notes'],
                ]);
            }

            return $order;
        });
    }
}
