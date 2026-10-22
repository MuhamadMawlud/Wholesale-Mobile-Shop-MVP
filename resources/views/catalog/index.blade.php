@extends('layouts.app')

@section('title', 'Wholesale Product Catalog')

@section('content')
    <div class="hero">
        <h1>B2B Wholesale Mobile & Accessories Catalog</h1>
        <p>Direct distributor supply of mobile smartphones, charging solutions, and protective accessories. Wholesale tiered pricing is shielded and accessible exclusively to approved accounts.</p>
    </div>

    <!-- Category Filters -->
    <div class="category-filters">
        <a href="{{ route('catalog.index') }}" class="filter-btn {{ empty($selectedCategory) ? 'active' : '' }}">
            All Categories
        </a>
        @foreach ($categories as $category)
            <a href="{{ route('catalog.index', ['category' => $category->slug]) }}"
               class="filter-btn {{ $selectedCategory === $category->slug ? 'active' : '' }}">
                {{ $category->name }} ({{ $category->products_count }})
            </a>
        @endforeach
    </div>

    <!-- Products Grid -->
    @if ($products->isEmpty())
        <div class="specs-box" style="text-align: center; padding: 3rem 1rem;">
            <p style="color: var(--text-muted); font-size: 1.05rem;">No products found in this category.</p>
        </div>
    @else
        <div class="product-grid">
            @foreach ($products as $product)
                <div class="product-card">
                    <div class="product-image-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                            <line x1="12" y1="18" x2="12.01" y2="18"></line>
                        </svg>
                    </div>
                    <div class="product-body">
                        <span class="category-tag">{{ $product->category->name }}</span>
                        <h2 class="product-title">
                            <a href="{{ route('catalog.show', $product->slug) }}">{{ $product->name }}</a>
                        </h2>
                        <p class="product-desc">{{ $product->description }}</p>
                        <div class="product-footer">
                            @if ($product->stock > 0)
                                <span class="badge-stock">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                        <circle cx="12" cy="12" r="10"></circle>
                                    </svg>
                                    In Stock ({{ $product->stock }} available)
                                </span>
                            @else
                                <span class="badge-out-of-stock">
                                    Out of Stock
                                </span>
                            @endif

                            @if ($canSeePrices && $product->price !== null)
                                <div class="price-box">
                                    <span class="price-label">Wholesale:</span>
                                    <span class="price-amount">{{ $product->formattedPrice() }}</span>
                                </div>
                            @else
                                <div class="price-shield-notice">
                                    Wholesale Price: Available to approved customers.
                                    @guest
                                        <a href="{{ route('login') }}" class="price-login-link">Log in</a> with an approved account to see wholesale prices.
                                    @else
                                        Log in with an approved account to see wholesale prices.
                                    @endguest
                                </div>
                            @endif

                            <a href="{{ route('catalog.show', $product->slug) }}" class="btn-view">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
