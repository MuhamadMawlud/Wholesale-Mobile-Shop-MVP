@extends('layouts.app')

@section('title', 'Wholesale Customer Login')

@section('content')
    <div class="form-card">
        <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--navy);">
            Wholesale Customer Login
        </h1>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.75rem;">
            Sign in with your approved wholesale account. Newly registered accounts must be approved by an administrator before accessing wholesale services.
        </p>

        @if (session('status'))
            <div class="alert alert-info">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">Business Email Address *</label>
                <input type="email"
                       name="email"
                       id="email"
                       class="form-input @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       required
                       autofocus>
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">Password *</label>
                <input type="password"
                       name="password"
                       id="password"
                       class="form-input @error('password') is-invalid @enderror"
                       required>
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" name="remember" id="remember" value="1">
                <label for="remember" style="font-size: 0.88rem; color: var(--text-muted); cursor: pointer;">
                    Remember my business session
                </label>
            </div>

            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn-primary">
                    Log In to Wholesale Portal
                </button>
            </div>

            <div style="margin-top: 1.25rem; text-align: center; font-size: 0.88rem; color: var(--text-muted);">
                Need a wholesale account?
                <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                    Apply for registration
                </a>
            </div>
        </form>
    </div>
@endsection
