<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'bulk_price',
        'unit',
        'stock',
        'min_order_qty', // Added here
        'is_active',
        'image',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }

    protected function casts(): array
    {
        return [
            'price'         => 'decimal:2',
            'bulk_price'    => 'decimal:2',
            'is_active'     => 'boolean',
            'stock'         => 'integer',
            'min_order_qty' => 'integer', // Added here
        ];
    }
}