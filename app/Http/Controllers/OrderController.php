<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CustomerPoint;
use App\Models\Discount;
use App\Models\Cart;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function checkout()
    {
        $cart = auth()->user()->cart;
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Keranjang kosong.');
        }

        $items = $cart->items()->with('product')->get();
        $subtotal = $cart->getTotal();
        $discounts = Discount::where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->where(function ($q) {
                $q->whereNull('max_uses')->orWhereRaw('used_count < max_uses');
            })
            ->get();

        return view('checkout.index', [
            'cart' => $cart,
            'items' => $items,
            'subtotal' => $subtotal,
            'discounts' => $discounts,
        ]);
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'discount_code' => 'nullable|string|exists:discounts,code',
        ]);

        $cart = auth()->user()->cart;
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Keranjang kosong.');
        }

        $items = $cart->items()->with('product')->get();
        $subtotal = $cart->getTotal();
        $discountAmount = 0;

        if ($validated['discount_code']) {
            $discount = Discount::where('code', $validated['discount_code'])->first();
            if ($discount && $discount->isValid()) {
                $discountAmount = $discount->calculateDiscount($subtotal);
                $discount->increment('used_count');
            }
        }

        $total = max(0, $subtotal - $discountAmount);

        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => Order::generateOrderNumber(),
            'status' => 'pending',
            'subtotal' => $subtotal,
            'discount' => $discountAmount,
            'total' => $total,
            'payment_method' => 'gopay',
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);

            $item->product->decrement('stock', $item->quantity);
        }

        $cart->items()->delete();

        return redirect()->route('orders.show', $order)->with('success', 'Pesanan dibuat. Lanjutkan pembayaran.');
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id() && !auth()->user()->isAdmin() && !auth()->user()->isCashier()) {
            abort(403);
        }

        $items = $order->items()->with('product')->get();
        $snapToken = null;

        if ($order->status === 'pending' && config('midtrans.server_key') && config('midtrans.server_key') !== 'SB-Mid-server-xxxx') {
            try {
                $midtrans = new MidtransService();
                $snapToken = $midtrans->createPayment($order);
            } catch (\Exception $e) {
                // Midtrans error fallback
            }
        }

        return view('orders.show', ['order' => $order, 'items' => $items, 'snapToken' => $snapToken]);
    }

    public function myOrders()
    {
        $orders = auth()->user()->orders()->latest()->paginate(10);
        return view('orders.my', ['orders' => $orders]);
    }

    public function cashierShow(Order $order)
    {
        $items = $order->items()->with('product')->get();
        return view('cashier.orders.show', ['order' => $order, 'items' => $items]);
    }

    public function confirmPayment(Request $request, Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Pesanan sudah diproses.');
        }

        $order->update(['status' => 'paid', 'payment_method' => 'cash']);

        $pointsEarned = (int)($order->total * 0.01);
        $order->update(['points_earned' => $pointsEarned]);

        CustomerPoint::create([
            'user_id' => $order->user_id,
            'type' => 'earn',
            'points' => $pointsEarned,
            'description' => 'Pembelian ' . $order->order_number,
            'order_id' => $order->id,
            'expired_at' => now()->addYear(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Pembayaran dikonfirmasi. Pesanan berhasil.');
    }

    public function cashierOrders(Request $request)
    {
        $orders = Order::with('user')->latest()->get();
        if ($request->wantsJson()) {
            return response()->json($orders);
        }
        return view('cashier.orders.index', ['orders' => $orders]);
    }

    public function points()
    {
        $user = auth()->user();
        $balance = $user->getPointBalance();
        $history = $user->points()->latest()->paginate(20);
        return view('points.index', ['balance' => $balance, 'history' => $history]);
    }

    public function paymentCallback(Request $request)
    {
        $orderId = $request->input('order_id');
        $status = $request->input('status');
        $reference = $request->input('reference');

        $order = Order::where('order_number', $orderId)->first();
        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        if ($status === 'completed' || $status === 'settlement') {
            $order->update(['status' => 'paid', 'payment_reference' => $reference]);

            $pointsEarned = (int)($order->total * 0.01);
            $order->update(['points_earned' => $pointsEarned]);

            CustomerPoint::create([
                'user_id' => $order->user_id,
                'type' => 'earn',
                'points' => $pointsEarned,
                'description' => 'Pembelian ' . $order->order_number,
                'order_id' => $order->id,
                'expired_at' => now()->addYear(),
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    public function apiIndex()
    {
        $orders = auth()->user()->orders()->with('items.product')->latest()->paginate(10);
        return response()->json($orders);
    }

    public function apiShow(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $order->load(['items.product', 'pointLogs']);
        return response()->json($order);
    }

    public function apiPoints()
    {
        $user = auth()->user();
        $balance = $user->getPointBalance();
        $history = $user->points()->latest()->get();
        return response()->json([
            'balance' => $balance,
            'history' => $history,
        ]);
    }

    public function apiValidateDiscount(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:discounts,code',
            'subtotal' => 'required|numeric',
        ]);

        $discount = Discount::where('code', $request->code)->first();
        if (!$discount || !$discount->isValid() || $request->subtotal < $discount->min_order) {
            return response()->json(['message' => 'Kode diskon tidak valid atau syarat tidak terpenuhi'], 422);
        }

        return response()->json(['discount_amount' => $discount->calculateDiscount($request->subtotal)]);
    }

    public function apiCheckout(Request $request)
    {
        $validated = $request->validate([
            'discount_code' => 'nullable|string|exists:discounts,code',
        ]);

        $cart = auth()->user()->cart;
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Keranjang kosong'], 422);
        }

        $items = $cart->items()->with('product')->get();
        $subtotal = $cart->getTotal();
        $discountAmount = 0;

        if ($request->discount_code) {
            $discount = Discount::where('code', $request->discount_code)->first();
            if ($discount && $discount->isValid()) {
                $discountAmount = $discount->calculateDiscount($subtotal);
                $discount->increment('used_count');
            }
        }

        $total = max(0, $subtotal - $discountAmount);

        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => Order::generateOrderNumber(),
            'status' => 'pending',
            'subtotal' => $subtotal,
            'discount' => $discountAmount,
            'total' => $total,
            'payment_method' => 'gopay',
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
            $item->product->decrement('stock', $item->quantity);
        }

        $cart->items()->delete();

        $snapToken = null;
        if (config('midtrans.server_key') && config('midtrans.server_key') !== 'SB-Mid-server-xxxx') {
            try {
                $midtrans = new MidtransService();
                $snapToken = $midtrans->createPayment($order);
            } catch (\Exception $e) {
                // Ignore midtrans error in dev
            }
        }

        return response()->json([
            'success' => true,
            'order' => $order,
            'snap_token' => $snapToken,
        ]);
    }
}
