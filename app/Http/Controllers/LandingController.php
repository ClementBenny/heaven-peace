<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing');
    }
    
    public function produce(Request $request)
    {
        $categories = \App\Models\Category::whereHas('products', fn($q) => $q->where('stock', '>', 0))->get();

        $query = \App\Models\Product::with('category')->where('stock', '>', 0)->orderBy('name');

        $selectedCategory = null;
        if ($request->filled('category')) {
            $selectedCategory = \App\Models\Category::findOrFail($request->category);
            $query->where('category_id', $selectedCategory->id);
        }

        $products = $query->get();

        return view('produce', compact('products', 'categories', 'selectedCategory'));
    }
}
