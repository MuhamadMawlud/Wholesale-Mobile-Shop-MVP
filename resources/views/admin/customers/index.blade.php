@extends('layouts.app')

@section('title', 'Admin Customer Management')

@section('content')
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
            <div>
                <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--navy); margin-bottom: 0.25rem;">
                    Customer Registrations
                </h1>
                <p style="color: var(--text-muted); font-size: 0.95rem;">
                    Manage wholesale business applications. Pending applicants must be approved before they can log in.
                </p>
            </div>
            <div>
                <span class="brand-badge" style="background-color: #fef3c7; color: #b45309; font-size: 0.82rem; padding: 0.35rem 0.75rem;">
                    Admin Panel
                </span>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Status Tabs -->
    <div class="category-filters" style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin.customers.index') }}"
           class="filter-btn {{ empty($selectedStatus) ? 'active' : '' }}">
            All Customers ({{ $counts['all'] }})
        </a>
        <a href="{{ route('admin.customers.index', ['status' => 'pending']) }}"
           class="filter-btn {{ $selectedStatus === 'pending' ? 'active' : '' }}"
           style="{{ $selectedStatus === 'pending' ? '' : ($counts['pending'] > 0 ? 'border-color: #f59e0b; color: #b45309; font-weight: 600;' : '') }}">
            Pending ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('admin.customers.index', ['status' => 'approved']) }}"
           class="filter-btn {{ $selectedStatus === 'approved' ? 'active' : '' }}">
            Approved ({{ $counts['approved'] }})
        </a>
        <a href="{{ route('admin.customers.index', ['status' => 'rejected']) }}"
           class="filter-btn {{ $selectedStatus === 'rejected' ? 'active' : '' }}">
            Rejected ({{ $counts['rejected'] }})
        </a>
    </div>

    <!-- Customers Table Card -->
    <div style="background-color: var(--surface-color); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        @if ($customers->isEmpty())
            <div style="padding: 3rem 1.5rem; text-align: center; color: var(--text-muted);">
                <p style="font-size: 1rem;">No customers found for the selected filter.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                    <thead>
                        <tr style="background-color: #f8fafc; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 0.85rem 1.25rem;">Contact Name</th>
                            <th style="padding: 0.85rem 1.25rem;">Company Name</th>
                            <th style="padding: 0.85rem 1.25rem;">Email Address</th>
                            <th style="padding: 0.85rem 1.25rem;">Phone</th>
                            <th style="padding: 0.85rem 1.25rem;">Status</th>
                            <th style="padding: 0.85rem 1.25rem;">Registered</th>
                            <th style="padding: 0.85rem 1.25rem; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 1rem 1.25rem; font-weight: 600; color: var(--text-main);">
                                    {{ $customer->name }}
                                </td>
                                <td style="padding: 1rem 1.25rem; color: var(--text-main);">
                                    {{ $customer->company_name ?? '—' }}
                                </td>
                                <td style="padding: 1rem 1.25rem; color: var(--text-muted);">
                                    {{ $customer->email }}
                                </td>
                                <td style="padding: 1rem 1.25rem; color: var(--text-muted);">
                                    {{ $customer->phone ?? '—' }}
                                </td>
                                <td style="padding: 1rem 1.25rem;">
                                    @if ($customer->isPending())
                                        <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background-color: #fef3c7; color: #b45309;">
                                            Pending
                                        </span>
                                    @elseif ($customer->isApproved())
                                        <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background-color: #dcfce7; color: #15803d;">
                                            Approved
                                        </span>
                                    @elseif ($customer->isRejected())
                                        <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background-color: #fee2e2; color: #b91c1c;">
                                            Rejected
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1.25rem; color: var(--text-muted); font-size: 0.82rem;">
                                    {{ $customer->created_at->format('M d, Y') }}
                                </td>
                                <td style="padding: 1rem 1.25rem; text-align: right; white-space: nowrap;">
                                    <div style="display: inline-flex; align-items: center; gap: 0.4rem;">
                                        @if ($customer->isPending() || $customer->isRejected())
                                            <form method="POST" action="{{ route('admin.customers.approve', $customer) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit"
                                                        style="background-color: #15803d; color: #ffffff; border: none; padding: 0.4rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                                                    Approve
                                                </button>
                                            </form>
                                        @endif

                                        @if ($customer->isPending() || $customer->isApproved())
                                            <form method="POST" action="{{ route('admin.customers.reject', $customer) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit"
                                                        style="background-color: #b91c1c; color: #ffffff; border: none; padding: 0.4rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                                                    Reject
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($customers->hasPages())
                <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--border-color);">
                    {{ $customers->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
