<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->orderBy('name')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
            $validated = $request->validate([
                'name'        => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'price'       => ['required', 'numeric', 'min:0'],
                'bulk_price'  => ['nullable', 'numeric', 'min:0'],
                'unit'        => ['required', 'string', 'max:50'],
                'stock'       => ['required', 'integer', 'min:0'],
                'min_order_qty' => ['required', 'integer', 'min:1'], // Added validation
                'category_id' => ['nullable', 'exists:categories,id'],
                'is_active'   => ['boolean'],
                'image'       => ['nullable', 'image', 'max:2048'],
            ]);

            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('products', 'public');
            }

            $validated['is_active'] = $request->boolean('is_active');

            Product::create($validated);

            return redirect()->route('admin.products.index')
                ->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'bulk_price'  => ['nullable', 'numeric', 'min:0'],
            'unit'        => ['required', 'string', 'max:50'],
            'stock'       => ['required', 'integer', 'min:0'],
            'min_order_qty' => ['required', 'integer', 'min:1'], // Added validation
            'category_id' => ['nullable', 'exists:categories,id'],
            'is_active'   => ['boolean'],
            'image'       => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if one exists
            if ($product->image) {
                \Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        if ($product->orderItems()->exists()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Cannot delete "' . $product->name . '" — it has order history. Set it to inactive instead.');
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }
}