<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function toggle(Product $product)
    {
        $wishlist = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return back()->with('success', 'Dihapus dari wishlist.');
        }

        Wishlist::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
        ]);

        return back()->with('success', 'Ditambah ke wishlist.');
    }

    public function index()
    {
        $wishlists = auth()->user()->wishlists()->with('product')->paginate(15);
        return view('wishlist.index', ['wishlists' => $wishlists]);
    }

    public function apiIndex()
    {
        $wishlists = Wishlist::with('product')->where('user_id', auth()->id())->get();
        return response()->json($wishlists);
    }

    public function apiToggle(Product $product)
    {
        $wishlist = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['status' => 'removed']);
        }

        Wishlist::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
        ]);

        return response()->json(['status' => 'added']);
    }

    public function apiDestroy(Wishlist $wishlist)
    {
        if ($wishlist->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $wishlist->delete();
        return response()->json(['success' => true]);
    }
}
