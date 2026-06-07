<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()
                        ->orders()
                        ->orderByDesc('created_at')
                        ->get();

        return view('shop.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        // Prevent customers from viewing other people's orders
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items.product');

        return view('shop.order-show', compact('order'));
    }

    public function cancel(Order $order)
{
    // Ownership check
    if ($order->user_id !== auth()->id()) {
        abort(403);
    }

    // Can only cancel before packed
    $cancellable = ['pending', 'confirmed', 'picking'];
    if (!in_array($order->status, $cancellable)) {
        return redirect()->route('shop.orders.show', $order)
            ->with('error', 'This order can no longer be cancelled.');
    }

    $order->update(['status' => 'cancelled']);

    foreach ($order->items as $item) {
        $item->product->increment('stock', $item->quantity);
    }

    return redirect()->route('shop.orders.show', $order)
        ->with('success', 'Your order has been cancelled.');
}
}