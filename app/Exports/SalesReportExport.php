<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements FromQuery, WithHeadings, WithMapping
{
    protected $startDate, $endDate, $orderType, $paymentMethod;

    public function __construct($startDate, $endDate, $orderType = null, $paymentMethod = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->orderType = $orderType;
        $this->paymentMethod = $paymentMethod;
    }

    public function query()
    {
        // Gunakan logika yang sama persis dengan Controller
        $query = Order::query()
            ->with(['table', 'items.menu'])
            ->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->where('payment_status', 'paid');

        if ($this->orderType) {
            $query->where('order_type', $this->orderType);
        }

        if ($this->paymentMethod) {
            $query->where('payment_method', $this->paymentMethod);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['No.', 'ID Transaksi', 'Tanggal', 'Item Pesanan', 'Tipe', 'Metode', 'Total'];
    }

    public function map($order): array
    {
        static $no = 0;
        $no++;

        $itemsDetail = $order->items->map(function ($item) {
            return ($item->menu->name ?? 'Menu Dihapus') . " (x{$item->quantity})";
        })->implode(', ');

        return [
            $no,
            $order->transaction_id,
            $order->created_at->format('d-m-Y H:i'),
            $itemsDetail,
            $order->order_type == 'dine_in' ? 'Dine In' : 'Take Away',
            ucfirst($order->payment_method),
            $order->total_price,
        ];
    }
}
