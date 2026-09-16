<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $logs = InventoryLog::with('product')->latest()->paginate(20);
        return view('admin.inventory.index', ['logs' => $logs]);
    }

    public function stockReport()
    {
        $products = Product::with('inventoryLogs')->get();
        return view('admin.inventory.stock-report', ['products' => $products]);
    }

    public function apiLogs()
    {
        $logs = InventoryLog::with('product')->latest()->limit(50)->get();
        return response()->json($logs);
    }

    public function apiStockReport()
    {
        $products = Product::with('category')
            ->withSum(['inventoryLogs as stock_in' => fn($q) => $q->where('type', 'in')], 'quantity')
            ->withSum(['inventoryLogs as stock_out' => fn($q) => $q->where('type', 'out')], 'quantity')
            ->get();
        return response()->json($products);
    }
}
