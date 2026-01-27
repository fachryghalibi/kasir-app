<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Statistics untuk Boss
        if ($user->isBoss()) {
            $stats = [
                'total_sales_today' => Transaction::today()->completed()->sum('total') ?? 0,
                'total_transactions_today' => Transaction::today()->completed()->count() ?? 0,
                'total_sales_month' => Transaction::thisMonth()->completed()->sum('total') ?? 0,
                'total_transactions_month' => Transaction::thisMonth()->completed()->count() ?? 0,
                'low_stock_products' => Product::lowStock()->count() ?? 0,
                'total_products' => Product::active()->count() ?? 0,
                'total_employees' => User::employee()->active()->count() ?? 0,
            ];

            // Chart data - Penjualan 7 hari terakhir
            $salesChart = Transaction::completed()
                ->where('created_at', '>=', now()->subDays(7))
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Top selling products (30 hari terakhir)
            $topProducts = collect(); // Empty collection untuk saat ini
            
            try {
                $topProducts = DB::table('transaction_items')
                    ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                    ->join('products', 'transaction_items.product_id', '=', 'products.id')
                    ->where('transactions.status', 'completed')
                    ->where('transactions.created_at', '>=', now()->subDays(30))
                    ->select(
                        'products.name',
                        DB::raw('SUM(transaction_items.quantity) as total_sold'),
                        DB::raw('SUM(transaction_items.subtotal) as total_revenue')
                    )
                    ->groupBy('products.id', 'products.name')
                    ->orderByDesc('total_sold')
                    ->limit(5)
                    ->get();
            } catch (\Exception $e) {
                // Jika tabel kosong atau error, pakai empty collection
                $topProducts = collect();
            }

            // Recent transactions
            $recentTransactions = Transaction::with('user', 'items')
                ->latest()
                ->limit(10)
                ->get();

            // Low stock alerts
            $lowStockProducts = Product::with('category')
                ->lowStock()
                ->orderBy('stock', 'asc')
                ->limit(10)
                ->get();

            return view('dashboard.boss', compact(
                'stats',
                'salesChart',
                'topProducts',
                'recentTransactions',
                'lowStockProducts'
            ));
        }

        // Statistics untuk Employee (Kasir)
        $stats = [
            'my_sales_today' => Transaction::today()
                ->where('user_id', $user->id)
                ->completed()
                ->sum('total') ?? 0,
            'my_transactions_today' => Transaction::today()
                ->where('user_id', $user->id)
                ->completed()
                ->count() ?? 0,
            'my_sales_month' => Transaction::thisMonth()
                ->where('user_id', $user->id)
                ->completed()
                ->sum('total') ?? 0,
            'my_transactions_month' => Transaction::thisMonth()
                ->where('user_id', $user->id)
                ->completed()
                ->count() ?? 0,
        ];

        // Recent transactions oleh kasir ini
        $recentTransactions = Transaction::with('items')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.employee', compact('stats', 'recentTransactions'));
    }
}