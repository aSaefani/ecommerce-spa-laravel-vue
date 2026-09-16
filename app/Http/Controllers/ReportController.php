<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $date = $request->input('date', today()->toDateString());

        $orders = Order::where('status', 'paid')
            ->whereDate('created_at', $date)
            ->latest()
            ->get();

        $summary = [
            'date' => $date,
            'totalOrders' => $orders->count(),
            'totalRevenue' => $orders->sum('total'),
            'totalDiscount' => $orders->sum('discount'),
            'totalPoints' => $orders->sum('points_earned'),
            'orders' => $orders,
        ];

        return view('admin.reports.daily', $summary);
    }

    public function downloadDaily(Request $request)
    {
        $date = $request->input('date', today()->toDateString());

        $orders = Order::where('status', 'paid')
            ->whereDate('created_at', $date)
            ->latest()
            ->get();

        $summary = [
            'date' => $date,
            'totalOrders' => $orders->count(),
            'totalRevenue' => $orders->sum('total'),
            'totalDiscount' => $orders->sum('discount'),
            'totalPoints' => $orders->sum('points_earned'),
            'orders' => $orders,
        ];

        $pdf = Pdf::loadView('admin.reports.daily-pdf', $summary);
        return $pdf->download('laporan-' . $date . '.pdf');
    }
}
