<?php

namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Models\CashFlow;
use App\Models\CashFlowCategory;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashFlowController extends Controller
{
    /**
     * Display cash flow dashboard
     */
    public function index(Request $request)
{
    $query = CashFlow::with(['category', 'creator', 'vendor']);

    // ✅ HAPUS filter approved default, tampilkan semua status
    // $query->approved(); // ← HAPUS INI

    // ✅ TAMBAH filter by approval status
    if ($request->filled('approval_status')) {
        $query->where('approval_status', $request->approval_status);
    } else {
        // Default: tampilkan approved dan rejected, sembunyikan pending
        // Atau bisa tampilkan semua jika mau
        // $query->whereIn('approval_status', ['approved', 'rejected']);
        
        // Atau tampilkan semua termasuk pending:
        // (tidak perlu filter)
    }

    // Filter by type
    if ($request->filled('type')) {
        $query->type($request->type);
    }

    // Filter by source
    if ($request->filled('source')) {
        $query->source($request->source);
    }

    // Filter by category
    if ($request->filled('category_id')) {
        $query->byCategory($request->category_id);
    }

    // Filter by date range
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->dateRange($request->start_date, $request->end_date);
    } elseif ($request->filled('period')) {
        // Quick filters
        switch ($request->period) {
            case 'today':
                $query->today();
                break;
            case 'this_month':
                $query->thisMonth();
                break;
            case 'this_year':
                $query->thisYear();
                break;
        }
    } else {
        // Default: this month
        $query->thisMonth();
    }

    // Get cash flows with pagination
    $cashFlows = $query->latest('transaction_date')
        ->latest('id')
        ->paginate(50)
        ->appends($request->query()); // ✅ Preserve query parameters

    // ✅ Calculate summary - hanya yang approved
    $summary = $this->calculateSummary($request);

    // Get filter options
    $categories = CashFlowCategory::active()->ordered()->get();
    $vendors = Vendor::active()->orderBy('name')->get();

    // Count pending reviews
    $pendingCount = CashFlow::pending()->count();

    return view('boss.cash-flows.index', compact(
        'cashFlows',
        'summary',
        'categories',
        'vendors',
        'pendingCount'
    ));
}

    /**
     * Display pending cash flows for review
     */
    public function pending(Request $request)
    {
        $query = CashFlow::with(['category', 'creator', 'vendor'])
            ->pending();

        // Filter by type
        if ($request->filled('type')) {
            $query->type($request->type);
        }

        // Get pending cash flows
        $cashFlows = $query->latest('created_at')->paginate(20);

        // Calculate stats for ALL pending (not just current page)
        $stats = CashFlow::pending()
            ->selectRaw('
                COUNT(*) as total_count,
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expense
            ')
            ->first();

        return view('boss.cash-flows.pending', compact('cashFlows', 'stats'));
    }

    /**
     * Calculate cash flow summary
     */
    private function calculateSummary(Request $request)
    {
        $query = CashFlow::approved();

        // Apply same filters as index
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        } elseif ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->today();
                    break;
                case 'this_month':
                    $query->thisMonth();
                    break;
                case 'this_year':
                    $query->thisYear();
                    break;
            }
        } else {
            $query->thisMonth();
        }

        $totalIncome = (clone $query)->income()->sum('amount');
        $totalExpense = (clone $query)->expense()->sum('amount');
        $netCashFlow = $totalIncome - $totalExpense;

        // Breakdown by source
        $incomeBySource = (clone $query)->income()
            ->select('source', DB::raw('SUM(amount) as total'))
            ->groupBy('source')
            ->get()
            ->pluck('total', 'source');

        $expenseBySource = (clone $query)->expense()
            ->select('source', DB::raw('SUM(amount) as total'))
            ->groupBy('source')
            ->get()
            ->pluck('total', 'source');

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_cash_flow' => $netCashFlow,
            'income_by_source' => $incomeBySource,
            'expense_by_source' => $expenseBySource,
        ];
    }

    /**
     * Show create form for manual cash flow
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'expense'); // Default: expense
        
        $categories = CashFlowCategory::active()
            ->type($type)
            ->ordered()
            ->get();
        
        $vendors = Vendor::active()->orderBy('name')->get();

        // Get threshold for UI display
        $threshold = config('cashflow.approval_threshold', 5000000);

        return view('boss.cash-flows.create', compact('type', 'categories', 'vendors', 'threshold'));
    }

    /**
     * Store manual cash flow
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'cash_flow_category_id' => 'required|exists:cash_flow_categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:1000',
            'transaction_date' => 'required|date',
            'payment_method' => 'nullable|in:cash,bank_transfer,debit_card,credit_card,qris,other',
            'receipt_number' => 'nullable|string|max:100',
            'vendor_id' => 'nullable|exists:vendors,id',
            'require_approval' => 'boolean',
        ], [
            'type.required' => 'Tipe cash flow wajib dipilih',
            'cash_flow_category_id.required' => 'Kategori wajib dipilih',
            'amount.required' => 'Jumlah wajib diisi',
            'amount.min' => 'Jumlah harus lebih dari 0',
            'description.required' => 'Keterangan wajib diisi',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi',
        ]);

        // Prepare data
        $validated['source'] = 'manual';
        $validated['created_by'] = auth()->id();
        
        // SINGLE BOSS APPROVAL LOGIC
        $threshold = config('cashflow.approval_threshold', 5000000);
        $autoApprove = config('cashflow.auto_approve_small_transactions', true);
        
        // Cek apakah butuh review
        $needsReview = false;
        $reviewReason = '';
        
        // 1. Transaksi besar (di atas threshold)
        if ($validated['amount'] >= $threshold) {
            $needsReview = true;
            $reviewReason = 'Nominal besar (≥ Rp ' . number_format($threshold, 0, ',', '.') . ')';
        }
        
        // 2. Manual request review
        if ($request->boolean('require_approval')) {
            $needsReview = true;
            if (empty($reviewReason)) {
                $reviewReason = 'Ditandai untuk review manual';
            }
        }
        
        // Set approval status
        if ($needsReview) {
            $validated['approval_status'] = 'pending';
            $validated['approval_notes'] = $reviewReason;
            $message = 'Cash flow disimpan untuk review. Alasan: ' . $reviewReason;
        } else {
            // Auto approve
            $validated['approval_status'] = 'approved';
            $validated['approved_by'] = auth()->id();
            $validated['approved_at'] = now();
            $message = 'Cash flow berhasil ditambahkan dan disetujui!';
        }

        // Create cash flow
        $cashFlow = CashFlow::create($validated);

        return redirect()->route('boss.cash-flows.index')
            ->with('success', $message);
    }

    /**
     * Display cash flow details
     */
    public function show(CashFlow $cashFlow)
    {
        $cashFlow->load([
            'category',
            'creator',
            'approver',
            'vendor',
            'reference'
        ]);

        return view('boss.cash-flows.show', compact('cashFlow'));
    }

    /**
     * Show edit form (only for manual cash flows)
     */
    public function edit(CashFlow $cashFlow)
    {
        // Only manual cash flows can be edited
        if (!$cashFlow->canBeEdited()) {
            return redirect()->route('boss.cash-flows.index')
                ->with('error', 'Cash flow ini tidak dapat diedit!');
        }

        $categories = CashFlowCategory::active()
            ->type($cashFlow->type)
            ->ordered()
            ->get();
        
        $vendors = Vendor::active()->orderBy('name')->get();

        return view('boss.cash-flows.edit', compact('cashFlow', 'categories', 'vendors'));
    }

    /**
     * Update manual cash flow
     */
    public function update(Request $request, CashFlow $cashFlow)
    {
        // Only manual cash flows can be edited
        if (!$cashFlow->canBeEdited()) {
            return redirect()->route('boss.cash-flows.index')
                ->with('error', 'Cash flow ini tidak dapat diedit!');
        }

        $validated = $request->validate([
            'cash_flow_category_id' => 'required|exists:cash_flow_categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:1000',
            'transaction_date' => 'required|date',
            'payment_method' => 'nullable|in:cash,bank_transfer,debit_card,credit_card,qris,other',
            'receipt_number' => 'nullable|string|max:100',
            'vendor_id' => 'nullable|exists:vendors,id',
        ]);

        $cashFlow->update($validated);

        return redirect()->route('boss.cash-flows.index')
            ->with('success', 'Cash flow berhasil diupdate!');
    }

    /**
     * Delete cash flow (only manual)
     */
    public function destroy(CashFlow $cashFlow)
    {
        if (!$cashFlow->canBeDeleted()) {
            return redirect()->route('boss.cash-flows.index')
                ->with('error', 'Cash flow ini tidak dapat dihapus!');
        }

        $cashFlow->delete();

        return redirect()->route('boss.cash-flows.index')
            ->with('success', 'Cash flow berhasil dihapus!');
    }

    /**
     * Approve pending cash flow
     */
    public function approve(CashFlow $cashFlow)
    {
        if (!$cashFlow->canBeApproved()) {
            return redirect()->back()
                ->with('error', 'Cash flow ini tidak dapat diapprove!');
        }

        $cashFlow->update([
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('boss.cash-flows.pending')
            ->with('success', 'Cash flow berhasil disetujui!');
    }

    /**
     * Bulk approve multiple cash flows
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'cash_flow_ids' => 'required|array|min:1',
            'cash_flow_ids.*' => 'exists:cash_flows,id',
        ], [
            'cash_flow_ids.required' => 'Pilih minimal satu transaksi',
            'cash_flow_ids.min' => 'Pilih minimal satu transaksi',
        ]);

        $count = CashFlow::whereIn('id', $validated['cash_flow_ids'])
            ->where('approval_status', 'pending')
            ->update([
                'approval_status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

        return redirect()->route('boss.cash-flows.pending')
            ->with('success', "{$count} transaksi berhasil disetujui!");
    }

    /**
     * Reject pending cash flow
     */
    public function reject(Request $request, CashFlow $cashFlow)
    {
        if (!$cashFlow->canBeApproved()) {
            return redirect()->back()
                ->with('error', 'Cash flow ini tidak dapat direject!');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi',
        ]);

        $cashFlow->update([
            'approval_status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->route('boss.cash-flows.pending')
            ->with('success', 'Cash flow berhasil ditolak!');
    }

    /**
     * Export cash flow report (akan diimplementasi nanti)
     */
    public function export(Request $request)
    {
        // TODO: Implement export to Excel/PDF
        return redirect()->back()
            ->with('info', 'Export feature coming soon!');
    }

    /**
     * Cash flow statistics/chart data (untuk dashboard)
     */
    public function statistics(Request $request)
    {
        $period = $request->get('period', 'this_month');
        
        $query = CashFlow::approved();
        
        switch ($period) {
            case 'today':
                $query->today();
                break;
            case 'this_month':
                $query->thisMonth();
                break;
            case 'this_year':
                $query->thisYear();
                break;
        }

        // Daily data for charts
        $dailyData = $query->select(
                'transaction_date',
                'type',
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('transaction_date', 'type')
            ->orderBy('transaction_date')
            ->get()
            ->groupBy('transaction_date');

        // Format for charts
        $chartData = [
            'dates' => [],
            'income' => [],
            'expense' => [],
        ];

        foreach ($dailyData as $date => $items) {
            $chartData['dates'][] = $date;
            $chartData['income'][] = $items->where('type', 'income')->sum('total');
            $chartData['expense'][] = $items->where('type', 'expense')->sum('total');
        }

        return response()->json($chartData);
    }
}