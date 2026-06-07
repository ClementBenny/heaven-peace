<?php

namespace App\Http\Controllers\Wholesale;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private string $cartKey = 'wholesale_cart';

    public function index()
    {
        $cart = session($this->cartKey, []);
        $products = collect();
        $total = 0;

        if (!empty($cart)) {
            $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
            foreach ($cart as $productId => $quantity) {
                if ($products->has($productId)) {
                    $total += $products[$productId]->bulk_price * $quantity;
                }
            }
        }

        return view('wholesale.cart', compact('cart', 'products', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = \App\Models\Product::findOrFail($request->product_id);
        $cart = session($this->cartKey, []);
        $productId = $request->product_id;
        $cartQty = $cart[$productId] ?? 0;

        if (($cartQty + $request->quantity) > $product->stock) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available.',
                ]);
            }
            return back()->with('error', 'Not enough stock available.');
        }

        $cart[$productId] = $cartQty + $request->quantity;
        session([$this->cartKey => $cart]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'cart_count' => array_sum($cart),
            ]);
        }

        return back()->with('success', 'Item added to cart.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $cart = session($this->cartKey, []);
        $cart[$request->product_id] = $request->quantity;
        session([$this->cartKey => $cart]);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = session($this->cartKey, []);
        unset($cart[$request->product_id]);
        session([$this->cartKey => $cart]);

        return back()->with('success', 'Item removed.');
    }

    public function checkout()
    {
        $cart = session($this->cartKey, []);

        if (empty($cart)) {
            return redirect()->route('wholesale.index')->with('error', 'Your cart is empty.');
        }

        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            if ($products->has($productId)) {
                $total += $products[$productId]->bulk_price * $quantity;
            }
        }

        return view('wholesale.checkout', compact('cart', 'products', 'total'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'delivery_address' => 'required|string|max:500',
            'notes'            => 'nullable|string|max:500',
        ]);

        $cart = session($this->cartKey, []);

        if (empty($cart)) {
            return redirect()->route('wholesale.index')->with('error', 'Your cart is empty.');
        }

        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            if ($products->has($productId)) {
                $total += $products[$productId]->bulk_price * $quantity;
            }
        }

        $order = Order::create([
            'user_id'          => auth()->id(),
            'status'           => 'pending',
            'total'            => $total,
            'delivery_address' => $request->delivery_address,
            'notes'            => $request->notes,
        ]);

        foreach ($cart as $productId => $quantity) {
            if ($products->has($productId)) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                    'unit_price' => $products[$productId]->bulk_price,
                ]);
                $products[$productId]->decrement('stock', $quantity);
            }
        }

        session()->forget($this->cartKey);

        return redirect()->route('wholesale.orders')->with('success', 'Order placed successfully.');
    }
}