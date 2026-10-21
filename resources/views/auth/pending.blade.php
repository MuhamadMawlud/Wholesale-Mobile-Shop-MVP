@extends('layouts.app')

@section('title', 'Registration Pending Approval')

@section('content')
    <div class="form-card" style="text-align: center;">
        <div style="width: 56px; height: 56px; background-color: #e0f2fe; color: #0284c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div>

        <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--navy);">
            Registration Submitted
        </h1>

        <div class="alert alert-info" style="text-align: left; margin: 1.25rem 0;">
            <strong>Status: Pending Approval</strong><br>
            Your wholesale business application has been submitted and is currently waiting for administrator review and approval.
        </div>

        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 2rem;">
            Once an administrator verifies and approves your business account, you will be able to log in, view wholesale unit prices, add products to cart, and place bulk orders.
        </p>

        <a href="{{ route('catalog.index') }}" class="btn-primary" style="display: block;">
            Return to Product Catalog
        </a>
    </div>
@endsection
