<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->isAdmin()) {
            return $this->adminDash();
        } elseif (auth()->user()->isCashier()) {
            return $this->cashierDash();
        }

        $user = auth()->user();
        $recentOrders = $user->orders()->latest()->limit(5)->get();
        $balance = $user->getPointBalance();

        return view('dashboard.customer', [
            'recentOrders' => $recentOrders,
            'balance' => $balance,
        ]);
    }

    public function adminDash()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'paid')->sum('total');
        $totalCustomers = User::where('role', 'customer')->count();
        $totalProducts = Product::count();

        $dailyRevenue = Order::where('status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total');

        $recentOrders = Order::latest()->limit(10)->get();

        return view('dashboard.admin', [
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'totalCustomers' => $totalCustomers,
            'totalProducts' => $totalProducts,
            'dailyRevenue' => $dailyRevenue,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function cashierDash()
    {
        $pendingOrders = Order::where('status', 'pending')->count();
        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::where('status', 'paid')->whereDate('created_at', today())->sum('total');

        return view('dashboard.cashier', [
            'pendingOrders' => $pendingOrders,
            'todayOrders' => $todayOrders,
            'todayRevenue' => $todayRevenue,
        ]);
    }

    public function users()
    {
        $users = User::where('role', '!=', 'admin')->paginate(20);
        return view('admin.users.index', ['users' => $users]);
    }

    public function toggleUserStatus(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Tidak bisa menonaktifkan admin.');
        }

        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'Status pengguna diperbarui.');
    }

    public function apiCustomerDash()
    {
        $user = auth()->user();
        $orders = $user->orders()->latest()->limit(5)->get();

        return response()->json([
            'balance' => $user->getPointBalance(),
            'totalOrders' => $user->orders()->count(),
            'totalSpent' => $user->orders()->where('status', 'paid')->sum('total'),
            'recentOrders' => $orders,
        ]);
    }

    public function apiAdminDash()
    {
        return response()->json([
            'totalOrders' => Order::count(),
            'totalRevenue' => Order::where('status', 'paid')->sum('total'),
            'totalCustomers' => User::where('role', 'customer')->count(),
            'totalProducts' => Product::count(),
            'dailyRevenue' => Order::where('status', 'paid')->whereDate('created_at', today())->sum('total'),
            'recentOrders' => Order::with('user')->latest()->limit(10)->get(),
        ]);
    }

    public function apiUsers()
    {
        $users = User::where('role', '!=', 'admin')->latest()->get();
        return response()->json($users);
    }

    public function apiToggleUser(User $user)
    {
        if ($user->isAdmin()) {
            return response()->json(['error' => 'Cannot toggle admin'], 403);
        }
        $user->update(['is_active' => !$user->is_active]);
        return response()->json($user);
    }

    public function apiCashierDash()
    {
        return response()->json([
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'todayOrders' => Order::whereDate('created_at', today())->count(),
            'todayRevenue' => Order::where('status', 'paid')->whereDate('created_at', today())->sum('total'),
            'recentOrders' => Order::with('user')->latest()->limit(10)->get(),
        ]);
    }
}
