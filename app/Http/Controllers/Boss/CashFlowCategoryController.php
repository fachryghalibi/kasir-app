<?php

namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Models\CashFlowCategory;
use Illuminate\Http\Request;

class CashFlowCategoryController extends Controller
{
    /**
     * Display categories
     */
    public function index()
    {
        $categories = CashFlowCategory::withCount('cashFlows')
            ->ordered()
            ->paginate(20);

        return view('boss.cash-flow-categories.index', compact('categories'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('boss.cash-flow-categories.create');
    }

    /**
     * Store new category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        CashFlowCategory::create($validated);

        return redirect()->route('boss.cash-flow-categories.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Show edit form
     */
    public function edit(CashFlowCategory $cashFlowCategory)
    {
        return view('boss.cash-flow-categories.edit', compact('cashFlowCategory'));
    }

    /**
     * Update category
     */
    public function update(Request $request, CashFlowCategory $cashFlowCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $cashFlowCategory->update($validated);

        return redirect()->route('boss.cash-flow-categories.index')
            ->with('success', 'Kategori berhasil diupdate!');
    }

    /**
     * Delete category
     */
    public function destroy(CashFlowCategory $cashFlowCategory)
    {
        // Check if category has cash flows
        if ($cashFlowCategory->cashFlows()->exists()) {
            return redirect()->route('boss.cash-flow-categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena sudah digunakan!');
        }

        $cashFlowCategory->delete();

        return redirect()->route('boss.cash-flow-categories.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }
}