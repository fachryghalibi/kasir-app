@extends('layouts.app')

@section('title', 'Detail Cash Flow')

@section('page-title', 'Detail Cash Flow')
@section('page-description', 'Informasi lengkap cash flow')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Actions --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('boss.cash-flows.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>

        <div class="flex gap-2">
            @if($cashFlow->canBeApproved())
                <form action="{{ route('boss.cash-flows.approve', $cashFlow) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        Approve
                    </button>
                </form>
                <button onclick="document.getElementById('rejectModal').classList.remove('hidden')" 
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    Reject
                </button>
            @endif

            @if($cashFlow->canBeEdited())
                <a href="{{ route('boss.cash-flows.edit', $cashFlow) }}" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Edit
                </a>
            @endif

            @if($cashFlow->canBeDeleted())
                <form action="{{ route('boss.cash-flows.destroy', $cashFlow) }}" method="POST" class="inline" 
                    onsubmit="return confirm('Yakin hapus cash flow ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                        Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Main Info Card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        {{-- Header with Type Badge --}}
        <div class="px-6 py-4 bg-{{ $cashFlow->getTypeBadgeColor() }}-50 border-b border-{{ $cashFlow->getTypeBadgeColor() }}-200">
            <div class="flex items-center justify-between">
                <div>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-{{ $cashFlow->getTypeBadgeColor() }}-100 text-{{ $cashFlow->getTypeBadgeColor() }}-800">
                        {{ $cashFlow->getTypeLabel() }}
                    </span>
                    <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-{{ $cashFlow->getApprovalBadgeColor() }}-100 text-{{ $cashFlow->getApprovalBadgeColor() }}-800">
                        {{ $cashFlow->getApprovalStatusLabel() }}
                    </span>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Total Amount</p>
                    <p class="text-3xl font-bold text-{{ $cashFlow->type === 'income' ? 'green' : 'red' }}-600">
                        {{ $cashFlow->type === 'income' ? '+' : '-' }} {{ $cashFlow->formatted_amount }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Details --}}
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Kategori</p>
                    <p class="font-semibold text-gray-900">{{ $cashFlow->category_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Sumber</p>
                    <p class="font-semibold text-gray-900">{{ $cashFlow->getSourceLabel() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Tanggal Transaksi</p>
                    <p class="font-semibold text-gray-900">{{ $cashFlow->transaction_date->format('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Metode Pembayaran</p>
                    <p class="font-semibold text-gray-900">{{ $cashFlow->getPaymentMethodLabel() }}</p>
                </div>
                @if($cashFlow->receipt_number)
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Nomor Kwitansi</p>
                        <p class="font-semibold text-gray-900">{{ $cashFlow->receipt_number }}</p>
                    </div>
                @endif
                @if($cashFlow->vendor)
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Vendor</p>
                        <p class="font-semibold text-gray-900">{{ $cashFlow->vendor->name }}</p>
                    </div>
                @endif
            </div>

            {{-- Description --}}
            <div class="pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600 mb-2">Keterangan</p>
                <p class="text-gray-900">{{ $cashFlow->description }}</p>
            </div>

            {{-- Reference Info --}}
            @if($cashFlow->reference)
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">Referensi</p>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-900">
                            <span class="font-semibold">{{ class_basename($cashFlow->reference_type) }}</span>
                            @if($cashFlow->reference_type === 'App\Models\Transaction')
                                - Invoice: {{ $cashFlow->reference->invoice_number }}
                            @elseif($cashFlow->reference_type === 'App\Models\ProductVendorPrice')
                                - Pembelian: {{ $cashFlow->reference->product->name }}
                            @endif
                        </p>
                    </div>
                </div>
            @endif

            {{-- Approval Info --}}
            @if($cashFlow->approval_status === 'rejected' && $cashFlow->rejection_reason)
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">Alasan Penolakan</p>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm text-red-900">{{ $cashFlow->rejection_reason }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Footer Info --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-600">Dibuat oleh</p>
                <p class="font-semibold text-gray-900">{{ $cashFlow->creator->name }}</p>
                <p class="text-xs text-gray-500">{{ $cashFlow->created_at->format('d M Y H:i') }}</p>
            </div>
            @if($cashFlow->approver)
                <div>
                    <p class="text-gray-600">{{ $cashFlow->approval_status === 'approved' ? 'Disetujui' : 'Ditolak' }} oleh</p>
                    <p class="font-semibold text-gray-900">{{ $cashFlow->approver->name }}</p>
                    <p class="text-xs text-gray-500">{{ $cashFlow->approved_at->format('d M Y H:i') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Reject Cash Flow</h3>
        </div>
        <form action="{{ route('boss.cash-flows.reject', $cashFlow) }}" method="POST" class="p-6">
            @csrf
            <div class="mb-4">
                <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea name="rejection_reason" id="rejection_reason" rows="3" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    placeholder="Jelaskan alasan penolakan..."></textarea>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" 
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>
@endsection