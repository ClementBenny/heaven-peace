@extends('layouts.public')

@section('title', 'Our Produce — Farm Direct')

@section('content')

<style>
    :root {
        /* Ensuring the variable fallback for safety */
        --nav-height: 80px; 
    }

    .produce-hero {
        background: var(--umber);
        padding: 80px 64px 64px;
        position: relative;
        overflow: hidden;
    }
    .produce-hero::before {
        content: '';
        position: absolute;
        right: -80px; top: -80px;
        width: 400px; height: 400px;
        border-radius: 50%;
        background: rgba(128,128,0,0.12);
        pointer-events: none;
    }
    .produce-hero::after {
        content: '';
        position: absolute;
        left: 40%; bottom: -120px;
        width: 280px; height: 280px;
        border-radius: 50%;
        background: rgba(196,164,132,0.08);
        pointer-events: none;
    }

    .filter-bar {
        background: var(--champagne);
        padding: 14px 64px;
        border-bottom: 1px solid rgba(196,164,132,0.25);
        display: flex; gap: 8px; flex-wrap: wrap;
        position: sticky; 
        top: 80px; /* Adjust this to match your exact Navbar height */
        z-index: 10;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .filter-btn {
        font-size: 11px; letter-spacing: 0.1em;
        text-transform: uppercase;
        padding: 7px 18px; border-radius: 999px;
        text-decoration: none;
        transition: background 0.2s, color 0.2s;
    }
    .filter-btn.active {
        background: var(--umber); color: var(--ivory);
        border: 1px solid var(--umber);
    }
    .filter-btn.inactive {
        background: transparent; color: var(--mauve);
        border: 1px solid rgba(196,164,132,0.4);
    }
    .filter-btn.inactive:hover {
        border-color: var(--umber); color: var(--umber);
    }

    /* Smaller, 4-column grid */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        background: var(--ivory);
    }

    .product-cell {
        border-right: 1px solid rgba(196,164,132,0.2);
        border-bottom: 1px solid rgba(196,164,132,0.2);
        display: flex; flex-direction: column;
        position: relative;
        overflow: hidden;
        transition: background 0.3s;
    }
    
    /* Clean up borders for 4-column layout */
    .product-cell:nth-child(4n) { border-right: none; }

    .product-cell:hover { background: rgba(247,231,206,0.4); }

    .product-cell-img {
        width: 100%; 
        aspect-ratio: 1/1;
        overflow: hidden; 
        background: var(--champagne);
    }
    .product-cell-img img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.6s ease;
    }
    .product-cell:hover .product-cell-img img { transform: scale(1.05); }

    .product-cell-body { 
        padding: 20px; 
        flex: 1; 
        display: flex; 
        flex-direction: column; 
    }

    .product-cell-num {
        font-family: 'Cormorant Garamond', serif;
        font-size: 11px; color: rgba(196,164,132,0.6);
        margin-bottom: 8px;
    }
    .product-name {
        font-family: 'Cormorant Garamond', serif;
        font-size: 20px; font-weight: 600;
        color: var(--umber); line-height: 1.2; margin-bottom: 8px;
    }
    .product-desc {
        font-size: 12px; color: var(--mauve);
        line-height: 1.5; flex: 1; margin-bottom: 16px;
    }
    .product-footer {
        display: flex; align-items: center;
        justify-content: space-between; gap: 8px;
        margin-top: auto;
    }
    .product-price {
        font-family: 'Cormorant Garamond', serif;
        font-size: 18px; font-weight: 600; color: var(--umber);
    }
    .product-unit { font-size: 11px; color: var(--mauve); display: block; }
    
    .order-btn {
        font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase;
        padding: 8px 16px; border-radius: 999px;
        background: var(--umber); color: var(--ivory);
        text-decoration: none; border: 1px solid var(--umber);
        transition: all 0.2s;
    }
    .order-btn:hover { background: var(--olive); border-color: var(--olive); }

    .category-label {
        font-size: 9px; letter-spacing: 0.12em;
        text-transform: uppercase; color: var(--olive);
        margin-bottom: 6px; display: block;
    }

    .empty-state {
        text-align: center; padding: 100px 0;
        background: var(--ivory);
    }

    /* Responsive adjustments */
    @media (max-width: 1100px) {
        .product-grid { grid-template-columns: repeat(3, 1fr); }
        .product-cell:nth-child(4n) { border-right: 1px solid rgba(196,164,132,0.2); }
        .product-cell:nth-child(3n) { border-right: none; }
    }
    @media (max-width: 768px) {
        .produce-hero { padding: 60px 24px 48px; }
        .filter-bar { padding: 14px 24px; top: 70px; } /* Likely shorter nav on mobile */
        .product-grid { grid-template-columns: 1fr 1fr; }
        .product-cell:nth-child(3n) { border-right: 1px solid rgba(196,164,132,0.2); }
        .product-cell:nth-child(2n) { border-right: none; }
    }
    @media (max-width: 480px) {
        .product-grid { grid-template-columns: 1fr; }
        .product-cell { border-right: none !important; }
    }
</style>

<div style="padding-top: 80px;">

    {{-- Hero Section --}}
    <div class="produce-hero">
        <div class="section-label" style="color: var(--mauve);">What we grow</div>
        <h1 class="section-title" style="color: var(--champagne); margin-bottom: 12px; max-width: 520px;">
            Grown with care,<br><em>harvested for you</em>
        </h1>
        <p style="font-size: 15px; color: var(--mauve); max-width: 400px; position: relative; z-index: 1;">
            Every item below comes straight from our Kerala farm — chemical-free, in season, and packed fresh.
        </p>
    </div>

    {{-- Category filter --}}
    @if($categories->isNotEmpty())
    <div class="filter-bar">
        <a href="{{ route('produce') }}" class="filter-btn {{ !isset($selectedCategory) ? 'active' : 'inactive' }}">All</a>
        @foreach($categories as $cat)
        <a href="{{ route('produce', ['category' => $cat->id]) }}"
           class="filter-btn {{ isset($selectedCategory) && $selectedCategory->id === $cat->id ? 'active' : 'inactive' }}">
            {{ $cat->name }}
        </a>
        @endforeach
    </div>
    @endif

    {{-- Products Grid --}}
    @if($products->isEmpty())
        <div class="empty-state">
            <div style="font-size: 48px; margin-bottom: 16px;">🌱</div>
            <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 24px; color: var(--umber); margin-bottom: 8px;">Nothing here yet</h3>
            <a href="{{ route('produce') }}" style="font-size: 13px; color: var(--mauve);">View all produce →</a>
        </div>
    @else
        <div class="product-grid">
            @foreach($products as $i => $product)
            <div class="product-cell">
                <div class="product-cell-img">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                    @else
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:40px;background:var(--champagne);">🥬</div>
                    @endif
                </div>
                <div class="product-cell-body">
                    <span class="product-cell-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    @if($product->category)
                        <span class="category-label">{{ $product->category->name }}</span>
                    @endif
                    <h3 class="product-name">{{ $product->name }}</h3>
                    <p class="product-desc">{{ $product->description }}</p>
                    <div class="product-footer">
                        <div>
                            <span class="product-price">₹{{ number_format($product->price, 2) }}</span>
                            <span class="product-unit">per {{ $product->unit }}</span>
                        </div>
                        <a href="{{ route('login') }}" class="order-btn">Order</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>

@endsection