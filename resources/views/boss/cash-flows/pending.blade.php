@extends('layouts.app')

@section('title', 'Review Cash Flow')

@section('page-title', 'Review Cash Flow')
@section('page-description', 'Transaksi yang perlu direview dan disetujui')

@section('content')
<div class="space-y-6">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Header Actions --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('boss.cash-flows.index') }}" 
                class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        @if($cashFlows->total() > 0)
        <button type="button" onclick="approveSelected()" 
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Setujui Terpilih
        </button>
        @endif
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats->total_count ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Pemasukan</p>
                    <p class="text-2xl font-bold text-green-600">
                        Rp {{ number_format($stats->total_income ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Pengeluaran</p>
                    <p class="text-2xl font-bold text-red-600">
                        Rp {{ number_format($stats->total_expense ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Cash Flows List --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Transaksi Pending Review</h3>
        </div>

        @if($cashFlows->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-600 mb-2">Tidak ada transaksi yang perlu direview</p>
                <p class="text-sm text-gray-500">Semua transaksi sudah disetujui</p>
            </div>
        @else
            {{-- FORM BULK APPROVE - TERPISAH --}}
            <form id="bulkApproveForm" action="{{ route('boss.cash-flows.bulk-approve') }}" method="POST" style="display: none;">
                @csrf
            </form>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alasan Review</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($cashFlows as $cashFlow)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                {{-- ✅ GUNAKAN ID untuk bulk approve (karena bulk pakai ID) --}}
                                <input type="checkbox" 
                                    value="{{ $cashFlow->id }}" 
                                    class="cash-flow-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    data-id="{{ $cashFlow->id }}">
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $cashFlow->transaction_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $cashFlow->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $cashFlow->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $cashFlow->category->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" title="{{ $cashFlow->description }}">
                                {{ $cashFlow->description }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-right
                                {{ $cashFlow->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format($cashFlow->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $cashFlow->approval_notes ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    {{-- ✅ GUNAKAN UUID untuk single approve --}}
                                    <button type="button"
                                        onclick="approveSingle('{{ $cashFlow->uuid }}')"
                                        class="px-3 py-1 bg-green-100 hover:bg-green-200 text-green-700 rounded text-xs font-medium transition-colors">
                                        Setujui
                                    </button>
                                    
                                    {{-- ✅ GUNAKAN UUID untuk reject --}}
                                    <button type="button" 
                                        onclick="showRejectModal('{{ $cashFlow->uuid }}')"
                                        class="px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded text-xs font-medium transition-colors">
                                        Tolak
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($cashFlows->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $cashFlows->links() }}
            </div>
            @endif
        @endif
    </div>
</div>

{{-- FORM APPROVE SINGLE (Hidden) --}}
<form id="approveSingleForm" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
</form>

{{-- Reject Modal --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Tolak Transaksi</h3>
        </div>
        <form id="rejectForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="p-6">
                <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea name="rejection_reason" id="rejection_reason" rows="4" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Jelaskan alasan penolakan..."></textarea>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeRejectModal()"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    Tolak Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Select All Checkbox
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.cash-flow-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// APPROVE SELECTED (Bulk) - Pakai ID
function approveSelected() {
    const checkboxes = document.querySelectorAll('.cash-flow-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Pilih minimal satu transaksi untuk disetujui');
        return;
    }
    
    if (!confirm(`Setujui ${checkboxes.length} transaksi yang dipilih?`)) {
        return;
    }

    const form = document.getElementById('bulkApproveForm');
    form.querySelectorAll('input[name="cash_flow_ids[]"]').forEach(input => input.remove());
    
    checkboxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'cash_flow_ids[]';
        input.value = checkbox.value; // ID
        form.appendChild(input);
    });
    
    form.submit();
}

// APPROVE SINGLE - Pakai UUID
function approveSingle(uuid) {
    if (!confirm('Setujui transaksi ini?')) {
        return;
    }

    const form = document.getElementById('approveSingleForm');
    const baseUrl = "{{ url('boss/cash-flows') }}";
    form.action = `${baseUrl}/${uuid}/approve`;
    
    console.log('Approve URL:', form.action);
    form.submit();
}

// REJECT - Pakai UUID
function showRejectModal(uuid) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const baseUrl = "{{ url('boss/cash-flows') }}";
    form.action = `${baseUrl}/${uuid}/reject`;
    
    console.log('Reject URL:', form.action);
    modal.classList.remove('hidden');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
    document.getElementById('rejection_reason').value = '';
}

document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRejectModal();
    }
});
</script>
@endpush
@endsection