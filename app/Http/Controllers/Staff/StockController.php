<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('stock')
            ->get();

        return view('staff.stock', compact('products'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $product->update(['stock' => $request->stock]);

        return redirect()->route('staff.stock')
            ->with('success', '"' . $product->name . '" stock updated to ' . $request->stock . '.');
    }
}