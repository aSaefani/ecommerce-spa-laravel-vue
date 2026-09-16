<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryLog;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->paginate(15);
        return view('products.index', ['products' => $products]);
    }

    public function show(Product $product)
    {
        return view('products.show', ['product' => $product]);
    }

    public function adminIndex()
    {
        $products = Product::with('category')->paginate(15);
        return view('admin.products.index', ['products' => $products]);
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            ...$validated,
            'image' => $imagePath,
        ]);

        InventoryLog::create([
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => $validated['stock'],
            'description' => 'Stok awal',
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambah.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', ['product' => $product, 'categories' => $categories]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $oldStock = $product->stock;
        $newStock = $validated['stock'];

        if ($newStock !== $oldStock) {
            $diff = $newStock - $oldStock;
            InventoryLog::create([
                'product_id' => $product->id,
                'type' => $diff > 0 ? 'in' : 'out',
                'quantity' => abs($diff),
                'description' => 'Penyesuaian stok',
            ]);
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diubah.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function apiIndex(Request $request)
    {
        $query = Product::with(['category'])->withCount('reviews')->withAvg('reviews', 'rating');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('in_stock') && $request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        switch ($request->get('sort', 'newest')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'rating':
                $query->orderByDesc('reviews_avg_rating');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        if ($request->boolean('all')) {
            return response()->json($query->get());
        }

        $perPage = $request->get('per_page', 12);
        return response()->json($query->paginate($perPage));
    }

    public function apiShow(Product $product)
    {
        $product->load(['category', 'reviews.user']);
        $product->reviews_avg_rating = $product->reviews()->avg('rating') ?? 0;
        $product->reviews_count = $product->reviews()->count();
        return response()->json($product);
    }

    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            ...$validated,
            'image' => $imagePath,
        ]);

        InventoryLog::create([
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => $validated['stock'],
            'description' => 'Stok awal admin API',
        ]);

        return response()->json($product->load('category'), 201);
    }

    public function apiUpdate(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $diff = $validated['stock'] - $product->stock;
        if ($diff !== 0) {
            InventoryLog::create([
                'product_id' => $product->id,
                'type' => $diff > 0 ? 'in' : 'out',
                'quantity' => abs($diff),
                'description' => 'Penyesuaian stok admin API',
            ]);
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);
        return response()->json($product->load('category'));
    }

    public function apiDestroy(Product $product)
    {
        $product->delete();
        return response()->json(['success' => true]);
    }
}
