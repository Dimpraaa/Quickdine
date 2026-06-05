<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_id',
        'quantity',
        'price_at_order',
        'notes',
        'subtotal'
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class)->withTrashed()->withDefault([
            'name' => '[Menu Dihapus]',
        ]);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
