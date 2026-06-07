<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'status', 'total', 'delivery_address', 'notes',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    // An order belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // An order has many line items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}