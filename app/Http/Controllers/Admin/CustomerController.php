<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display a listing of registered customers (non-admins only),
     * showing pending customers first.
     */
    public function index(Request $request): View
    {
        $selectedStatus = $request->query('status');

        $query = User::where('is_admin', false);

        if ($selectedStatus && in_array($selectedStatus, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $selectedStatus);
        }

        // Show pending customers first, then ordered by latest
        $customers = $query
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'all' => User::where('is_admin', false)->count(),
            'pending' => User::where('is_admin', false)->where('status', 'pending')->count(),
            'approved' => User::where('is_admin', false)->where('status', 'approved')->count(),
            'rejected' => User::where('is_admin', false)->where('status', 'rejected')->count(),
        ];

        return view('admin.customers.index', [
            'customers' => $customers,
            'counts' => $counts,
            'selectedStatus' => $selectedStatus,
        ]);
    }

    /**
     * Approve a customer registration.
     * Sets status server-side to 'approved'. Never takes status from request input.
     * Prevents admins from modifying their own status or other admins' status.
     */
    public function approve(User $user): RedirectResponse
    {
        if ($user->id === Auth::id() || $user->is_admin) {
            abort(403, 'Administrators cannot change their own status or other administrators\' status.');
        }

        $user->status = 'approved';
        $user->save();

        return redirect()->back()->with('success', "Customer '{$user->name}' has been approved successfully.");
    }

    /**
     * Reject a customer registration.
     * Sets status server-side to 'rejected'. Never takes status from request input.
     * Prevents admins from modifying their own status or other admins' status.
     */
    public function reject(User $user): RedirectResponse
    {
        if ($user->id === Auth::id() || $user->is_admin) {
            abort(403, 'Administrators cannot change their own status or other administrators\' status.');
        }

        $user->status = 'rejected';
        $user->save();

        return redirect()->back()->with('success', "Customer '{$user->name}' registration has been rejected.");
    }
}
