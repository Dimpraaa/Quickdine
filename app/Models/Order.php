<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'table_id',
        'order_type',
        'total_price',
        'status',
        'payment_status',
        'payment_method',
        'transaction_id',
        'rating',
        'review'
    ];

    public function table()
    {
        return $this->belongsTo(Table::class)->withDefault([
            'table_number' => '?',
        ]);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
