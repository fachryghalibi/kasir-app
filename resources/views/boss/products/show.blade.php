@extends('layouts.app')

@section('title', $product->name . ' - POS System')

@section('page-title', 'Detail Produk')
@section('page-description', 'Informasi lengkap tentang produk')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <a href="{{ route('boss.products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar Produk
            </a>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('boss.products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Produk
            </a>
            <form action="{{ route('boss.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Produk
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Image -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden mb-4">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Status Badges -->
                <div class="flex flex-wrap gap-2 justify-center">
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                        {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                    @if($product->stock <= 0)
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                            Stok Habis
                        </span>
                    @elseif($product->stock <= 10)
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Stok Rendah
                        </span>
                    @else
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                            Stok Tersedia
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                
                @if($product->category)
                    <div class="mb-4">
                        <a href="{{ route('boss.categories.show', $product->category) }}" 
                           class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            {{ $product->category->name }}
                        </a>
                    </div>
                @endif

                @if($product->description)
                    <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>
                @endif
            </div>

            <!-- Pricing Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Harga</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Harga Beli</label>
                        <div class="text-2xl font-bold text-gray-900">
                            Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Harga Jual</label>
                        <div class="text-2xl font-bold text-blue-600">
                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="md:col-span-2 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Margin Keuntungan</span>
                            <div class="text-right">
                                <div class="text-lg font-bold text-green-600">
                                    Rp {{ number_format($product->selling_price - $product->purchase_price, 0, ',', '.') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $product->purchase_price > 0 ? number_format((($product->selling_price - $product->purchase_price) / $product->purchase_price) * 100, 2) : 0 }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock & SKU Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Inventori</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Stok Tersedia</label>
                        <div class="text-3xl font-bold {{ $product->stock <= 0 ? 'text-red-600' : ($product->stock <= 10 ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ $product->stock }} unit
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">SKU (Stock Keeping Unit)</label>
                        <div class="text-2xl font-mono font-bold text-gray-900">
                            {{ $product->sku }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Tambahan</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dibuat pada</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $product->created_at->format('d M Y, H:i') }} WIB
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Terakhir diubah</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $product->updated_at->format('d M Y, H:i') }} WIB
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nilai Total Stok</dt>
                        <dd class="mt-1 text-sm font-bold text-gray-900">
                            Rp {{ number_format($product->stock * $product->purchase_price, 0, ',', '.') }}
                            <span class="text-xs text-gray-500">(harga beli)</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Potensi Pendapatan</dt>
                        <dd class="mt-1 text-sm font-bold text-blue-600">
                            Rp {{ number_format($product->stock * $product->selling_price, 0, ',', '.') }}
                            <span class="text-xs text-gray-500">(harga jual)</span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection