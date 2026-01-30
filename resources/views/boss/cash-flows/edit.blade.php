@extends('layouts.app')

@section('title', 'Edit Cash Flow')

@section('page-title', 'Edit ' . ($cashFlow->type === 'income' ? 'Pemasukan' : 'Pengeluaran'))
@section('page-description', 'Update data cash flow')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Form Edit {{ $cashFlow->getTypeLabel() }}</h3>
        </div>

        <form action="{{ route('boss.cash-flows.update', $cashFlow) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Category --}}
            <div>
                <label for="cash_flow_category_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select name="cash_flow_category_id" id="cash_flow_category_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('cash_flow_category_id') border-red-500 @enderror">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('cash_flow_category_id', $cashFlow->cash_flow_category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('cash_flow_category_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Amount --}}
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Jumlah (Rp) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="amount" id="amount" value="{{ old('amount', $cashFlow->amount) }}" required min="0" step="1000"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('amount') border-red-500 @enderror">
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="3" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $cashFlow->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Transaction Date --}}
            <div>
                <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Transaksi <span class="text-red-500">*</span>
                </label>
                <input type="date" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', $cashFlow->transaction_date->format('Y-m-d')) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('transaction_date') border-red-500 @enderror">
                @error('transaction_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Payment Method --}}
            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                    Metode Pembayaran
                </label>
                <select name="payment_method" id="payment_method"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('payment_method') border-red-500 @enderror">
                    <option value="">Pilih Metode</option>
                    <option value="cash" {{ old('payment_method', $cashFlow->payment_method) == 'cash' ? 'selected' : '' }}>Tunai</option>
                    <option value="bank_transfer" {{ old('payment_method', $cashFlow->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="debit_card" {{ old('payment_method', $cashFlow->payment_method) == 'debit_card' ? 'selected' : '' }}>Kartu Debit</option>
                    <option value="credit_card" {{ old('payment_method', $cashFlow->payment_method) == 'credit_card' ? 'selected' : '' }}>Kartu Kredit</option>
                    <option value="qris" {{ old('payment_method', $cashFlow->payment_method) == 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="other" {{ old('payment_method', $cashFlow->payment_method) == 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('payment_method')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Receipt Number --}}
            <div>
                <label for="receipt_number" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Kwitansi/Invoice
                </label>
                <input type="text" name="receipt_number" id="receipt_number" value="{{ old('receipt_number', $cashFlow->receipt_number) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('receipt_number') border-red-500 @enderror">
                @error('receipt_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Vendor (only for expense) --}}
            @if($cashFlow->type === 'expense')
                <div>
                    <label for="vendor_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Vendor/Supplier (Opsional)
                    </label>
                    <select name="vendor_id" id="vendor_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('vendor_id') border-red-500 @enderror">
                        <option value="">Pilih Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_id', $cashFlow->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('vendor_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            {{-- Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('boss.cash-flows.index') }}" 
                    class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    Batal
                </a>
                <button type="submit" 
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Cash Flow
                </button>
            </div>
        </form>
    </div>
</div>
@endsection