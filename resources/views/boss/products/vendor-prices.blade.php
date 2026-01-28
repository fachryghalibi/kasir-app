@extends('layouts.app')

@section('title', 'Harga Vendor - ' . $product->name)

@section('content')
<div class="container-fluid px-6 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('boss.products.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-800">Harga Vendor</h1>
                <p class="text-gray-600 mt-1">Kelola harga dari berbagai vendor untuk: <span class="font-semibold">{{ $product->name }}</span></p>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg flex items-start">
        <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg flex items-start">
        <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Info & Add Form -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Product Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Produk</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">SKU</p>
                        <p class="font-mono text-sm font-medium">{{ $product->sku }}</p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Nama Produk</p>
                        <p class="font-semibold text-gray-800">{{ $product->name }}</p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Harga Jual</p>
                        <p class="text-xl font-bold text-green-600">{{ $product->formatted_selling_price }}</p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Harga Beli Saat Ini</p>
                        <p class="text-lg font-bold text-blue-600">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Add New Vendor Price Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h3 class="text-lg font-bold text-white">Tambah Harga Vendor</h3>
                </div>
                
                <form action="{{ route('boss.products.vendor-prices.store', $product) }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    
                    <div>
                        <label for="vendor_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Vendor <span class="text-red-500">*</span>
                        </label>
                        <select name="vendor_id" 
                                id="vendor_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('vendor_id') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Vendor</option>
                            @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('vendor_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="purchase_price" class="block text-sm font-semibold text-gray-700 mb-2">
                            Harga Beli <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="purchase_price" 
                               id="purchase_price" 
                               value="{{ old('purchase_price') }}"
                               min="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('purchase_price') border-red-500 @enderror" 
                               placeholder="50000"
                               required>
                        @error('purchase_price')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="effective_from" class="block text-sm font-semibold text-gray-700 mb-2">
                            Berlaku Dari <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="effective_from" 
                               id="effective_from" 
                               value="{{ old('effective_from', date('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('effective_from') border-red-500 @enderror"
                               required>
                        @error('effective_from')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="effective_to" class="block text-sm font-semibold text-gray-700 mb-2">
                            Berlaku Sampai <span class="text-gray-400 text-xs">(Opsional)</span>
                        </label>
                        <input type="date" 
                               name="effective_to" 
                               id="effective_to" 
                               value="{{ old('effective_to') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('effective_to') border-red-500 @enderror">
                        @error('effective_to')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ada batas waktu</p>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                            Catatan
                        </label>
                        <textarea name="notes" 
                                  id="notes" 
                                  rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror" 
                                  placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                        @error('notes')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Harga Vendor
                    </button>
                </form>
            </div>
        </div>

        <!-- Vendor Prices History -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-800">Histori Harga Vendor</h3>
                </div>

                @if($vendorPrices->isEmpty())
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <p class="text-gray-500 text-lg font-medium">Belum ada data harga vendor</p>
                    <p class="text-gray-400 text-sm mt-1">Tambahkan harga dari vendor untuk produk ini</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Vendor</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Periode</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Catatan</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Dibuat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($vendorPrices as $vp)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                                            {{ substr($vp->vendor->name, 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-semibold text-gray-800">{{ $vp->vendor->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $vp->vendor->code }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-lg font-bold text-gray-800">{{ $vp->formatted_price }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <p class="text-gray-700">
                                            <span class="font-medium">Dari:</span> {{ $vp->effective_from->format('d M Y') }}
                                        </p>
                                        <p class="text-gray-500 mt-1">
                                            <span class="font-medium">Sampai:</span> {{ $vp->effective_to ? $vp->effective_to->format('d M Y') : 'Tidak terbatas' }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($vp->isActive())
                                    <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Aktif
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm font-medium">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Expired
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($vp->notes)
                                    <p class="text-sm text-gray-600">{{ Str::limit($vp->notes, 50) }}</p>
                                    @else
                                    <span class="text-gray-400 text-sm">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">
                                        <p>{{ $vp->created_at->format('d M Y') }}</p>
                                        <p class="text-xs">{{ $vp->creator->name ?? 'System' }}</p>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($vendorPrices->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $vendorPrices->links() }}
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
