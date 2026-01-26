<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsolidatedOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_id',
        'customer_name',
        'customer_email',
        'product_id',
        'product_name',
        'sku',
        'quantity',
        'item_price',
        'line_total',
        'order_date',
        'order_status',
        'order_total',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'item_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'order_total' => 'decimal:2',
    ];
}
