<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = auth()->user()->cart ?? Cart::create(['user_id' => auth()->id()]);
        $items = $cart->items()->with('product')->get();
        return view('cart.index', ['cart' => $cart, 'items' => $items]);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->stock < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Stok tidak cukup.']);
        }

        $cart = auth()->user()->cart ?? Cart::create(['user_id' => auth()->id()]);

        $cartItem = $cart->items()->where('product_id', $product->id)->first();
        if ($cartItem) {
            if ($product->stock < $cartItem->quantity + $validated['quantity']) {
                return back()->withErrors(['quantity' => 'Stok tidak cukup.']);
            }
            $cartItem->update(['quantity' => $cartItem->quantity + $validated['quantity']]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
            ]);
        }

        return back()->with('success', 'Produk ditambah ke keranjang.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($cartItem->product->stock < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Stok tidak cukup.']);
        }

        $cartItem->update($validated);
        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function remove(CartItem $cartItem)
    {
        $cartItem->delete();
        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function apiIndex()
    {
        $cart = auth()->user()->cart ?? Cart::create(['user_id' => auth()->id()]);
        $items = $cart->items()->with('product.category')->get();
        return response()->json([
            'cart_id' => $cart->id,
            'items' => $items,
            'total' => $cart->getTotal(),
            'count' => $items->sum('quantity'),
        ]);
    }

    public function apiAdd(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->stock < $validated['quantity']) {
            return response()->json(['error' => 'Stok tidak cukup'], 422);
        }

        $cart = auth()->user()->cart ?? Cart::create(['user_id' => auth()->id()]);

        $cartItem = $cart->items()->where('product_id', $product->id)->first();
        if ($cartItem) {
            if ($product->stock < $cartItem->quantity + $validated['quantity']) {
                return response()->json(['error' => 'Stok tidak cukup'], 422);
            }
            $cartItem->update(['quantity' => $cartItem->quantity + $validated['quantity']]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Produk ditambah ke keranjang']);
    }

    public function apiUpdate(Request $request, CartItem $cartItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($cartItem->product->stock < $validated['quantity']) {
            return response()->json(['error' => 'Stok tidak cukup'], 422);
        }

        $cartItem->update($validated);
        return response()->json(['success' => true]);
    }

    public function apiRemove(CartItem $cartItem)
    {
        if ($cartItem->cart->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cartItem->delete();
        return response()->json(['success' => true]);
    }
}
