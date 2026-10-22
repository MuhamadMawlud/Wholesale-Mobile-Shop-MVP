@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <a href="{{ route('catalog.index') }}" class="btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Back to Product Catalog
    </a>

    <div class="product-detail-card">
        <div class="detail-image-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                <line x1="12" y1="18" x2="12.01" y2="18"></line>
            </svg>
        </div>

        <div>
            <span class="category-tag">{{ $product->category->name }}</span>
            <h1 style="font-size: 1.65rem; font-weight: 700; margin: 0.25rem 0 0.85rem; line-height: 1.3;">
                {{ $product->name }}
            </h1>

            <div style="margin-bottom: 1.25rem;">
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
            </div>

            @if ($canSeePrices && $product->price !== null)
                <div class="price-box" style="margin-bottom: 1.25rem; align-items: baseline;">
                    <span class="price-label">Wholesale Price:</span>
                    <span class="price-amount-large">{{ $product->formattedPrice() }}</span>
                    <span style="font-size: 0.82rem; color: var(--success); font-weight: 600; background-color: var(--success-bg); padding: 0.2rem 0.6rem; border-radius: 9999px; margin-left: 0.5rem;">
                        Approved Wholesale Pricing
                    </span>
                </div>
            @else
                <div class="price-shield-notice" style="text-align: left; padding: 0.85rem 1rem; margin-bottom: 1.5rem; font-size: 0.88rem;">
                    <strong>Wholesale Pricing Shielded:</strong> Wholesale tiered pricing and volume discounts are reserved for verified business accounts.
                    @guest
                        <a href="{{ route('login') }}" class="price-login-link">Log in</a> with an approved account to see wholesale prices.
                    @else
                        Log in with an approved account to see wholesale prices.
                    @endguest
                </div>
            @endif

            <div style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 0.95rem; font-weight: 600; color: var(--navy); margin-bottom: 0.4rem;">
                    Product Description
                </h3>
                <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">
                    {{ $product->description }}
                </p>
            </div>

            <div class="specs-box">
                <h3>Wholesale Supply Specifications</h3>
                <div class="specs-grid">
                    <div class="spec-item">
                        <span class="spec-label">Category</span>
                        <span class="spec-value">{{ $product->category->name }}</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Condition</span>
                        <span class="spec-value">Brand New / Factory Sealed</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Supply Type</span>
                        <span class="spec-value">Master Carton Distribution</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Stock Status</span>
                        <span class="spec-value">{{ $product->stock > 0 ? 'Available for Bulk Order' : 'Currently Depleted' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
