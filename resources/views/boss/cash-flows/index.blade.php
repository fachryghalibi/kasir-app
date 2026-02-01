@extends('layouts.app')

@section('title', 'Cash Flow Management')

@section('page-title', 'Cash Flow Management')
@section('page-description', 'Kelola pemasukan dan pengeluaran toko')

@section('content')
<div class="space-y-6">
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        {{-- Total Income --}}
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Pemasukan</p>
                    <h3 class="text-3xl font-bold mt-2">{{ number_format($summary['total_income'], 0, ',', '.') }}</h3>
                    <p class="text-green-100 text-xs mt-1">Periode ini</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Expense --}}
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Total Pengeluaran</p>
                    <h3 class="text-3xl font-bold mt-2">{{ number_format($summary['total_expense'], 0, ',', '.') }}</h3>
                    <p class="text-red-100 text-xs mt-1">Periode ini</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- ✅ NEW: Total Adjustments --}}
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm font-medium">Total Penyesuaian</p>
                    <h3 class="text-3xl font-bold mt-2">{{ number_format($summary['total_adjustment'] ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-amber-100 text-xs mt-1">Koreksi inventory</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
            </div>
        </div>

       {{-- Net Cash Flow --}}
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Net Cash Flow</p>
                    <h3 class="text-3xl font-bold mt-2">{{ number_format($summary['net_cash_flow'], 0, ',', '.') }}</h3>
                    <p class="text-blue-100 text-xs mt-1">
                        {{ $summary['net_cash_flow'] >= 0 ? 'Surplus' : 'Defisit' }}
                        <span class="opacity-75">• Approved Only</span>
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters & Actions --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            {{-- Filters --}}
            <form method="GET" class="flex flex-wrap gap-3 flex-1">
                {{-- Period Quick Filter --}}
                <select name="period" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="this.form.submit()">
                    <option value="">Pilih Periode</option>
                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                </select>

                {{-- Status Approval Filter --}}
                <select name="approval_status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>✅ Disetujui</option>
                    <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                    <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>❌ Ditolak</option>
                </select>

                {{-- Type Filter --}}
                <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="this.form.submit()">
                    <option value="">Semua Tipe</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                </select>

                {{-- ✅ UPDATED: Source Filter dengan Adjustment --}}
                <select name="source" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="this.form.submit()">
                    <option value="">Semua Sumber</option>
                    <option value="sale" {{ request('source') == 'sale' ? 'selected' : '' }}>Penjualan</option>
                    <option value="purchase" {{ request('source') == 'purchase' ? 'selected' : '' }}>Pembelian</option>
                    <option value="adjustment" {{ request('source') == 'adjustment' ? 'selected' : '' }}>Penyesuaian</option>
                    <option value="manual" {{ request('source') == 'manual' ? 'selected' : '' }}>Manual</option>
                    <option value="refund" {{ request('source') == 'refund' ? 'selected' : '' }}>Refund</option>
                </select>

                {{-- Category Filter --}}
                <select name="category_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                @if(request()->hasAny(['period', 'type', 'source', 'category_id', 'start_date', 'end_date', 'approval_status']))
                    <a href="{{ route('boss.cash-flows.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Reset Filter
                    </a>
                @endif
            </form>

            {{-- Actions --}}
            <div class="flex gap-3">
                {{-- Link ke Pending Review --}}
                <a href="{{ route('boss.cash-flows.pending') }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors flex items-center relative">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Review
                    @if($pendingCount > 0)
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
                
                <a href="{{ route('boss.cash-flows.create') }}?type=income" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Pemasukan
                </a>
                <a href="{{ route('boss.cash-flows.create') }}?type=expense" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                    Pengeluaran
                </a>
            </div>
        </div>
    </div>

    {{-- Cash Flow Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori/Sumber</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
    @forelse($cashFlows as $cashFlow)
        <tr class="hover:bg-gray-50 {{ $cashFlow->approval_status === 'rejected' ? 'bg-red-50' : '' }} {{ $cashFlow->source === 'adjustment' ? 'bg-amber-50' : '' }}">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ $cashFlow->transaction_date->format('d M Y') }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                @if($cashFlow->source === 'adjustment')
                    {{-- ✅ BADGE ORANYE untuk Adjustment --}}
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                        Penyesuaian
                    </span>
                @else
                    {{-- Badge normal untuk income/expense --}}
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $cashFlow->getTypeBadgeColor() }}-100 text-{{ $cashFlow->getTypeBadgeColor() }}-800">
                        {{ $cashFlow->getTypeLabel() }}
                    </span>
                @endif
            </td>
            <td class="px-6 py-4 text-sm">
                <div class="font-medium text-gray-900">{{ $cashFlow->category_name }}</div>
                <div class="text-xs text-gray-500">{{ $cashFlow->getSourceLabel() }}</div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600">
                <div class="max-w-xs truncate">{{ $cashFlow->description }}</div>
                {{-- Show rejection reason if rejected --}}
                @if($cashFlow->approval_status === 'rejected' && $cashFlow->rejection_reason)
                    <div class="mt-1 text-xs text-red-600 italic">
                        ❌ Ditolak: {{ $cashFlow->rejection_reason }}
                    </div>
                @endif
                {{-- Show approval notes if pending --}}
                @if($cashFlow->approval_status === 'pending' && $cashFlow->approval_notes)
                    <div class="mt-1 text-xs text-yellow-600 italic">
                        ⏳ {{ $cashFlow->approval_notes }}
                    </div>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
                @if($cashFlow->source === 'adjustment')
                    {{-- ✅ WARNA ORANYE untuk Adjustment --}}
                    <span class="text-sm font-bold text-amber-600">
                        {{ $cashFlow->amount >= 0 ? '-' : '+' }} Rp {{ number_format(abs($cashFlow->amount), 0, ',', '.') }}
                    </span>
                    <span class="block text-xs text-amber-500 mt-1">Koreksi</span>
                @else
                    {{-- Warna normal untuk income/expense --}}
                    <span class="text-sm font-bold text-{{ $cashFlow->type === 'income' ? 'green' : 'red' }}-600">
                        {{ $cashFlow->type === 'income' ? '+' : '-' }} Rp {{ number_format(abs($cashFlow->amount), 0, ',', '.') }}
                    </span>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $cashFlow->getApprovalBadgeColor() }}-100 text-{{ $cashFlow->getApprovalBadgeColor() }}-800">
                    {{ $cashFlow->getApprovalStatusLabel() }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                <div class="flex items-center justify-center gap-2">
                    <a href="{{ route('boss.cash-flows.show', $cashFlow) }}" class="text-blue-600 hover:text-blue-900" title="Detail">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>

                    {{-- Show approve button for pending items --}}
                    @if($cashFlow->canBeApproved())
                        <a href="{{ route('boss.cash-flows.pending') }}" class="text-green-600 hover:text-green-900" title="Review & Approve">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </a>
                    @endif

                    @if($cashFlow->canBeEdited())
                        <a href="{{ route('boss.cash-flows.edit', $cashFlow) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                    @endif

                    @if($cashFlow->canBeDeleted())
                        <form action="{{ route('boss.cash-flows.destroy', $cashFlow) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus cash flow ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada cash flow</h3>
                <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan pemasukan atau pengeluaran.</p>
            </td>
        </tr>
    @endforelse
</tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($cashFlows->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $cashFlows->links() }}
            </div>
        @endif
    </div>
</div>
@endsection