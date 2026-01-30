<?php

namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees
     */
    public function index(Request $request)
    {
        $query = User::employee();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $employees = $query->latest()->paginate(20);

        return view('boss.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new employee
     */
    public function create()
    {
        return view('boss.employees.create');
    }

    /**
     * Store a newly created employee
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Nama karyawan wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $validated['role'] = 'employee';
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        User::create($validated);

        return redirect()->route('boss.employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan!');
    }

    /**
     * Display the specified employee
     */
    public function show(Request $request, User $employee)
    {
        // Pastikan yang ditampilkan adalah employee
        if (!$employee->isEmployee()) {
            abort(404);
        }

        // Query transaksi dengan filter
        $transactionsQuery = $employee->transactions();

        // Filter by date range
        if ($request->filled('date_from')) {
            $transactionsQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $transactionsQuery->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by invoice number
        if ($request->filled('search')) {
            $transactionsQuery->where('invoice_number', 'LIKE', '%' . $request->search . '%');
        }

        // Calculate statistics BEFORE pagination (untuk filtered data)
        // Clone query untuk menghindari konflik dengan pagination
        $filteredTransactions = clone $transactionsQuery;
        
        $stats = [
            'total_transactions' => $filteredTransactions->count(),
            'total_sales' => $filteredTransactions->sum('total') ?? 0,
            'total_items' => $filteredTransactions->get()->sum(function($transaction) {
                return $transaction->items()->sum('quantity');
            }),
        ];

        // Get paginated transactions AFTER calculating stats
        $transactions = $transactionsQuery->latest()->paginate(20);

        // Overall statistics (semua transaksi tanpa filter)
        $allTransactions = $employee->transactions();
        
        $overallStats = [
            'total_transactions' => $allTransactions->count(),
            'total_sales' => $allTransactions->sum('total') ?? 0,
            'working_days' => $employee->created_at->diffInDays(now()),
        ];

        return view('boss.employees.show', compact('employee', 'transactions', 'stats', 'overallStats'));
    }

    /**
     * Show the form for editing the specified employee
     */
    public function edit(User $employee)
    {
        // Pastikan yang diedit adalah employee
        if (!$employee->isEmployee()) {
            abort(404);
        }

        return view('boss.employees.edit', compact('employee'));
    }

    /**
     * Update the specified employee
     */
    public function update(Request $request, User $employee)
    {
        // Pastikan yang diupdate adalah employee
        if (!$employee->isEmployee()) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($employee->id)
            ],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Nama karyawan wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        $employee->update($validated);

        return redirect()->route('boss.employees.index')
            ->with('success', 'Data karyawan berhasil diupdate!');
    }

    /**
     * Get transaction items (untuk modal detail)
     */
    public function getTransactionItems($transactionId)
    {
        try {
            $transaction = \App\Models\Transaction::with(['items', 'user'])->findOrFail($transactionId);
            
            // Format payment method
            $paymentMethods = [
                'cash' => 'Tunai',
                'debit_card' => 'Kartu Debit',
                'credit_card' => 'Kartu Kredit',
                'qris' => 'QRIS',
                'transfer' => 'Transfer'
            ];
            
            return response()->json([
                'success' => true,
                'transaction' => [
                    'invoice_number' => $transaction->invoice_number,
                    'date' => $transaction->created_at->format('d M Y, H:i') . ' WIB',
                    'cashier' => $transaction->user->name,
                    'payment_method' => $paymentMethods[$transaction->payment_method] ?? $transaction->payment_method,
                    'subtotal' => $transaction->subtotal,
                    'discount_amount' => $transaction->discount_amount,
                    'tax_amount' => $transaction->tax_amount,
                    'tax_rate' => $transaction->tax_rate,
                    'total' => $transaction->total,
                ],
                'items' => $transaction->items->map(function($item) {
                    return [
                        'product_name' => $item->product_name,
                        'product_sku' => $item->product_sku,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'subtotal' => $item->subtotal,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Remove the specified employee (Soft Delete)
     */
    public function destroy(User $employee)
    {
        // Pastikan yang dihapus adalah employee
        if (!$employee->isEmployee()) {
            abort(404);
        }

        // Check if employee has transactions
        if ($employee->transactions()->exists()) {
            return redirect()->route('boss.employees.index')
                ->with('error', 'Karyawan tidak dapat dihapus karena memiliki riwayat transaksi!');
        }

        $employee->delete();

        return redirect()->route('boss.employees.index')
            ->with('success', 'Karyawan berhasil dihapus!');
    }
}