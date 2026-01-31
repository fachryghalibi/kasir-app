@extends('layouts.app')

@section('title', $category->name . ' - POS System')

@section('page-title', $category->name)
@section('page-description', $category->description ?: 'Daftar produk dalam kategori ini')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <a href="{{ route('boss.categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar Kategori
            </a>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('boss.categories.edit', $category) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Kategori
            </a>
            <a href="{{ route('boss.products.create', ['category_id' => $category->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Produk
            </a>
        </div>
    </div>

    <!-- Category Info Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h2>
                    @if($category->description)
                        <p class="text-gray-600 mt-1">{{ $category->description }}</p>
                    @endif
                    <div class="flex items-center gap-4 mt-3">
                        <span class="text-sm text-gray-500">
                            <span class="font-medium">Slug:</span> 
                            <code class="bg-gray-100 px-2 py-1 rounded">{{ $category->slug }}</code>
                        </span>
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full 
                            {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-blue-600">{{ $products->total() }}</div>
                <div class="text-sm text-gray-500">Total Produk</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('boss.categories.show', $category) }}" class="flex gap-4">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari produk..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="w-48">
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="w-48">
                <select name="stock" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Stok</option>
                    <option value="in_stock" {{ request('stock') == 'in_stock' ? 'selected' : '' }}>Tersedia</option>
                    <option value="low_stock" {{ request('stock') == 'low_stock' ? 'selected' : '' }}>Stok Rendah</option>
                    <option value="out_of_stock" {{ request('stock') == 'out_of_stock' ? 'selected' : '' }}>Habis</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'stock']))
                <a href="{{ route('boss.categories.show', $category) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Product Image -->
                    <div class="aspect-square bg-gray-100 relative">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-2 right-2">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded 
                                {{ $product->is_active ? 'bg-green-500 text-white' : 'bg-gray-500 text-white' }}">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>

                        <!-- Stock Badge -->
                        @if($product->stock <= 0)
                            <div class="absolute top-2 left-2">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-red-500 text-white">
                                    Habis
                                </span>
                            </div>
                        @elseif($product->stock <= 10)
                            <div class="absolute top-2 left-2">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-yellow-500 text-white">
                                    Stok Rendah
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2">{{ $product->name }}</h3>
                        
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-lg font-bold text-blue-600">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                            </span>
                            <span class="text-sm text-gray-500">
                                Stok: <span class="font-semibold {{ $product->stock <= 0 ? 'text-red-600' : ($product->stock <= 10 ? 'text-yellow-600' : 'text-green-600') }}">
                                    {{ $product->stock }}
                                </span>
                            </span>
                        </div>

                        @if($product->description)
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $product->description }}</p>
                        @endif

                        <div class="text-xs text-gray-500 mb-3">
                            <div>SKU: <span class="font-mono">{{ $product->sku }}</span></div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <a href="{{ route('boss.products.edit', $product) }}" 
                               class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                            <a href="{{ route('boss.products.show', $product) }}" 
                               class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-gray-600 text-white text-sm font-medium rounded hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                {{ $products->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12">
            <div class="flex flex-col items-center justify-center">
                <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <p class="text-gray-500 font-medium mb-2">
                    @if(request()->hasAny(['search', 'status', 'stock']))
                        Tidak ada produk yang sesuai dengan filter
                    @else
                        Belum ada produk dalam kategori ini
                    @endif
                </p>
                <p class="text-sm text-gray-400 mb-4">
                    @if(request()->hasAny(['search', 'status', 'stock']))
                        Coba ubah kriteria pencarian Anda
                    @else
                        Klik tombol "Tambah Produk" untuk menambahkan produk baru
                    @endif
                </p>
                @if(request()->hasAny(['search', 'status', 'stock']))
                    <a href="{{ route('boss.categories.show', $category) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Reset Filter
                    </a>
                @else
                    <a href="{{ route('boss.products.create', ['category_id' => $category->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Produk
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection