<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        Review::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            ...$validated,
        ]);

        return back()->with('success', 'Review berhasil ditambah.');
    }

    public function destroy(Review $review)
    {
        if ($review->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $review->delete();
        return back()->with('success', 'Review berhasil dihapus.');
    }

    public function apiIndex(Product $product)
    {
        $reviews = Review::with('user')->where('product_id', $product->id)->get();
        return response()->json($reviews);
    }

    public function apiStore(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $review = Review::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            ...$validated,
        ]);

        return response()->json($review);
    }

    public function apiDestroy(Review $review)
    {
        if ($review->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $review->delete();
        return response()->json(['success' => true]);
    }
}
