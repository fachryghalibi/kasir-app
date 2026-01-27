<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display transaction history
     */
    public function index(Request $request)
    {
        $query = Transaction::with('items.product')
            ->where('user_id', auth()->id())
            ->latest();

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->paginate(20);

        return view('pos.history', compact('transactions'));
    }

    /**
     * Display transaction detail
     */
    public function show(Transaction $transaction)
    {
        // Check authorization
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini.');
        }

        $transaction->load('items.product', 'user');

        return view('pos.show', compact('transaction'));
    }

    /**
     * Print receipt
     * PERBAIKAN: Menggunakan $id integer bukan model binding
     */
    public function receipt($id)
    {
        // Find transaction manually
        $transaction = Transaction::with(['items.product', 'user'])
            ->findOrFail($id);
        
        // Check authorization - hanya kasir yang buat transaksi yang bisa print
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini.');
        }

        return view('pos.receipt', compact('transaction'));
    }
}