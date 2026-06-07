<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // The cart is stored in the session as an array:
    // ['cart' => [ product_id => quantity, ... ]]

    public function index()
    {
        $cart     = session('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->get();
        $total    = $products->sum(fn($p) => $p->price * $cart[$p->id]);

        return view('shop.cart', compact('cart', 'products', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = session('cart', []);
        $id   = $request->product_id;

        $cart[$id] = ($cart[$id] ?? 0) + $request->quantity;

        session(['cart' => $cart]);

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
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = session('cart', []);
        $cart[$request->product_id] = $request->quantity;
        session(['cart' => $cart]);

        return redirect()->route('shop.cart');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $cart = session('cart', []);
        unset($cart[$request->product_id]);
        session(['cart' => $cart]);

        return redirect()->route('shop.cart')
            ->with('success', 'Item removed.');
    }

    public function checkout()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.cart')
                ->with('error', 'Your cart is empty.');
        }

        $products = Product::whereIn('id', array_keys($cart))->get();
        $total    = $products->sum(fn($p) => $p->price * $cart[$p->id]);

        return view('shop.checkout', compact('cart', 'products', 'total'));
    }

    public function placeOrder(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.index');
        }

        $validated = $request->validate([
            'delivery_address' => ['required', 'string', 'max:500'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $products = Product::whereIn('id', array_keys($cart))->get();

        // Create the order
        $order = Order::create([
            'user_id'          => auth()->id(),
            'status'           => 'pending',
            'total'            => 0,
            'delivery_address' => $validated['delivery_address'],
            'notes'            => $validated['notes'] ?? null,
        ]);

        
        // Create each line item and accumulate total
        $total = 0;
        foreach ($products as $product) {
            $qty = $cart[$product->id];
            $order->items()->create([
                'product_id' => $product->id,
                'quantity'   => $qty,
                'unit_price' => $product->price,
            ]);
            $total += $qty * $product->price;
            $product->decrement('stock', $qty);
        }

        $order->update(['total' => $total]);

        // Clear the cart
        session()->forget('cart');

        return redirect()->route('shop.orders')
            ->with('success', 'Order placed! We will confirm it shortly.');
    }
}