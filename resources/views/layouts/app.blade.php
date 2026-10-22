<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Wholesale Mobile Shop') — B2B Wholesale Platform</title>
    <style>
        :root {
            --bg-color: #f8fafc;
            --surface-color: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --primary: #0284c7;
            --primary-hover: #0369a1;
            --navy: #0f172a;
            --success: #15803d;
            --success-bg: #dcfce7;
            --danger: #b91c1c;
            --danger-bg: #fee2e2;
            --badge-bg: #f1f5f9;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: var(--surface-color);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .navbar {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0.85rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
            color: var(--navy);
            font-weight: 700;
            font-size: 1.15rem;
        }

        .brand-badge {
            font-size: 0.68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 0.15rem 0.45rem;
            border-radius: 4px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.92rem;
            font-weight: 500;
            transition: color 0.15s ease;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--primary);
        }

        main {
            flex: 1;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        .hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border-radius: 12px;
            padding: 2.25rem 2rem;
            margin-bottom: 2rem;
        }

        .hero h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .hero p {
            color: #94a3b8;
            font-size: 1rem;
            max-width: 720px;
        }

        .category-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }

        .filter-btn {
            display: inline-block;
            text-decoration: none;
            padding: 0.45rem 0.9rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 500;
            background-color: var(--surface-color);
            color: var(--text-muted);
            border: 1px solid var(--border-color);
            transition: all 0.15s ease;
        }

        .filter-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .filter-btn.active {
            background-color: var(--navy);
            color: #ffffff;
            border-color: var(--navy);
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .product-card {
            background-color: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .product-image-box {
            background: #f1f5f9;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid var(--border-color);
            color: #94a3b8;
        }

        .product-image-box svg {
            width: 56px;
            height: 56px;
        }

        .product-body {
            padding: 1.25rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .category-tag {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--primary);
            margin-bottom: 0.35rem;
        }

        .product-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.5rem;
            line-height: 1.35;
        }

        .product-title a {
            text-decoration: none;
            color: inherit;
        }

        .product-title a:hover {
            color: var(--primary);
        }

        .product-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-footer {
            margin-top: auto;
            border-top: 1px solid var(--border-color);
            padding-top: 0.9rem;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
        }

        .badge-stock {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--success);
            background-color: var(--success-bg);
            padding: 0.2rem 0.6rem;
            border-radius: 9999px;
            width: fit-content;
        }

        .badge-out-of-stock {
            display: inline-flex;
            align-items: center;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--danger);
            background-color: var(--danger-bg);
            padding: 0.2rem 0.6rem;
            border-radius: 9999px;
            width: fit-content;
        }

        .price-shield-notice {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            color: #475569;
            padding: 0.45rem 0.65rem;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 500;
            text-align: center;
        }

        .price-box {
            display: flex;
            align-items: baseline;
            gap: 0.45rem;
            flex-wrap: wrap;
        }

        .price-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .price-amount {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--navy);
        }

        .price-amount-large {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--navy);
            letter-spacing: -0.02em;
        }

        .price-login-link {
            color: var(--primary);
            font-weight: 600;
            text-decoration: underline;
        }

        .price-login-link:hover {
            color: var(--primary-hover);
        }

        .btn-view {
            display: block;
            text-align: center;
            text-decoration: none;
            background-color: var(--navy);
            color: #ffffff;
            padding: 0.55rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: background-color 0.15s ease;
        }

        .btn-view:hover {
            background-color: var(--primary-hover);
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.88rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }

        .btn-back:hover {
            color: var(--primary);
        }

        .product-detail-card {
            background-color: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 2rem;
            display: grid;
            grid-template-columns: 1fr 1.25fr;
            gap: 2.5rem;
        }

        @media (max-width: 768px) {
            .product-detail-card {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }

        .detail-image-box {
            background: #f1f5f9;
            border-radius: 8px;
            min-height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            border: 1px solid var(--border-color);
        }

        .detail-image-box svg {
            width: 96px;
            height: 96px;
        }

        .specs-box {
            margin-top: 1.5rem;
            background-color: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.25rem;
        }

        .specs-box h3 {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 0.75rem;
        }

        .specs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            font-size: 0.85rem;
        }

        .spec-item {
            display: flex;
            flex-direction: column;
        }

        .spec-label {
            color: var(--text-muted);
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .spec-value {
            font-weight: 600;
            color: var(--text-main);
        }

        .form-card {
            background-color: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 2.25rem;
            max-width: 560px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.88rem;
            font-weight: 600;
            margin-bottom: 0.35rem;
            color: var(--text-main);
        }

        .form-input {
            width: 100%;
            padding: 0.65rem 0.85rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.95rem;
            color: var(--text-main);
            background-color: #ffffff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        .form-input.is-invalid {
            border-color: var(--danger);
        }

        .form-error {
            color: var(--danger);
            font-size: 0.8rem;
            margin-top: 0.35rem;
        }

        .btn-primary {
            display: inline-block;
            width: 100%;
            text-align: center;
            text-decoration: none;
            background-color: var(--navy);
            color: #ffffff;
            padding: 0.75rem 1.25rem;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert-success {
            background-color: var(--success-bg);
            color: var(--success);
            border: 1px solid #bbf7d0;
        }

        .alert-info {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        footer {
            background-color: var(--surface-color);
            border-top: 1px solid var(--border-color);
            padding: 1.5rem;
            text-align: center;
            font-size: 0.82rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="{{ route('catalog.index') }}" class="brand">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                    <line x1="12" y1="18" x2="12.01" y2="18"></line>
                </svg>
                <span>Wholesale Mobile Shop</span>
                <span class="brand-badge">B2B Portal</span>
            </a>
            <ul class="nav-links">
                <li><a href="{{ route('catalog.index') }}" class="{{ request()->routeIs('catalog.*') ? 'active' : '' }}">Catalog</a></li>
                @auth
                    @if (auth()->user()->is_admin)
                        <li><a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">Customers</a></li>
                    @endif
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.88rem; color: var(--navy); font-weight: 600;">
                        <span>{{ auth()->user()->name }}</span>
                        @if (auth()->user()->is_admin)
                            <span class="brand-badge" style="background-color: #fef3c7; color: #b45309;">Admin</span>
                        @else
                            <span class="brand-badge" style="background-color: #dcfce7; color: #15803d;">Approved</span>
                        @endif
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: var(--text-muted); font-size: 0.92rem; font-weight: 500; cursor: pointer; padding: 0; font-family: inherit;">
                                Logout
                            </button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Login</a></li>
                    <li><a href="{{ route('register') }}" class="{{ request()->routeIs('register*') ? 'active' : '' }}">Register</a></li>
                @endauth
            </ul>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} Wholesale Mobile Shop MVP — Academic Demonstration Platform</p>
    </footer>
</body>
</html>
