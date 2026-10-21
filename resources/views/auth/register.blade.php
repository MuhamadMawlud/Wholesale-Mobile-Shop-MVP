@extends('layouts.app')

@section('title', 'Wholesale Customer Registration')

@section('content')
    <div class="form-card">
        <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--navy);">
            Wholesale Customer Registration
        </h1>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.75rem;">
            Apply for a business wholesale account. All new registrations are reviewed and approved by an administrator before wholesale prices and ordering become accessible.
        </p>

        @if (session('status'))
            <div class="alert alert-info">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <!-- Contact Name -->
            <div class="form-group">
                <label for="name" class="form-label">Contact Name *</label>
                <input type="text"
                       name="name"
                       id="name"
                       class="form-input @error('name') is-invalid @enderror"
                       value="{{ old('name') }}"
                       required
                       autofocus>
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">Business Email Address *</label>
                <input type="email"
                       name="email"
                       id="email"
                       class="form-input @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       required>
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Company / Business Name -->
            <div class="form-group">
                <label for="company_name" class="form-label">Company / Business Name</label>
                <input type="text"
                       name="company_name"
                       id="company_name"
                       class="form-input @error('company_name') is-invalid @enderror"
                       value="{{ old('company_name') }}">
                @error('company_name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Phone Number -->
            <div class="form-group">
                <label for="phone" class="form-label">Contact Phone Number</label>
                <input type="tel"
                       name="phone"
                       id="phone"
                       class="form-input @error('phone') is-invalid @enderror"
                       value="{{ old('phone') }}">
                @error('phone')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">Password * (minimum 8 characters)</label>
                <input type="password"
                       name="password"
                       id="password"
                       class="form-input @error('password') is-invalid @enderror"
                       required>
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password *</label>
                <input type="password"
                       name="password_confirmation"
                       id="password_confirmation"
                       class="form-input"
                       required>
            </div>

            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn-primary">
                    Submit Wholesale Registration
                </button>
            </div>
        </form>
    </div>
@endsection
