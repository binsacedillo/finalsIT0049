<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $reportingTimezone = config('pos.reporting_timezone', 'Asia/Manila');
        $reportNow = now($reportingTimezone);
        $dayStartUtc = $reportNow->copy()->startOfDay()->setTimezone('UTC');
        $dayEndUtc = $reportNow->copy()->endOfDay()->setTimezone('UTC');

        $salesTodayQuery = Sale::query()->whereBetween('created_at', [$dayStartUtc, $dayEndUtc]);
        $salesTodayTotal = (clone $salesTodayQuery)->sum('total_price');
        $transactionsToday = $salesTodayQuery->count();

        $activeProducts = Product::query()->where('is_active', true)->count();
        $outOfStockProducts = Product::query()
            ->where('is_active', true)
            ->where('stock_quantity', 0)
            ->count();
        $lowStockThreshold = max(0, (int) config('pos.low_stock_threshold', 5));
        $stockAlerts = Product::query()
            ->where('is_active', true)
            ->where('stock_quantity', '<=', $lowStockThreshold)
            ->orderBy('stock_quantity')
            ->orderBy('name')
            ->limit(5)
            ->get(['id', 'name', 'stock_quantity']);

        $recentSales = Sale::query()
            ->with(['product:id,name', 'customer:id,full_name', 'staff:id,full_name'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'activeProducts',
            'lowStockThreshold',
            'outOfStockProducts',
            'recentSales',
            'reportNow',
            'reportingTimezone',
            'salesTodayTotal',
            'stockAlerts',
            'transactionsToday',
        ));
    }
}
