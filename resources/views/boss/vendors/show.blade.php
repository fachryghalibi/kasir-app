@extends('layouts.app')

@section('title', 'Detail Vendor - ' . $vendor->name)

@section('content')
<div class="container-fluid px-6 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('boss.vendors.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Detail Vendor</h1>
        </div>
        <p class="text-gray-600 ml-9">Informasi lengkap vendor dan produk yang tersedia</p>
    </div>

    <!-- Vendor Info Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="p-6">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                        {{ substr($vendor->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $vendor->name }}</h2>
                        <p class="text-gray-500 font-mono text-sm mt-1">{{ $vendor->code }}</p>
                        @if($vendor->is_active)
                        <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium mt-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Aktif
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm font-medium mt-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            Nonaktif
                        </span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('boss.vendors.edit', $vendor) }}" 
                   class="bg-amber-100 hover:bg-amber-200 text-amber-700 px-4 py-2 rounded-lg transition-colors flex items-center gap-2 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Contact Person -->
                <div class="flex items-start gap-3">
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Contact Person</p>
                        <p class="text-gray-800 font-semibold mt-1">{{ $vendor->contact_person ?: '-' }}</p>
                    </div>
                </div>

                <!-- Phone -->
                <div class="flex items-start gap-3">
                    <div class="bg-green-100 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Telepon</p>
                        @if($vendor->phone)
                        <a href="tel:{{ $vendor->phone }}" class="text-blue-600 hover:text-blue-700 font-semibold mt-1 block">
                            {{ $vendor->phone }}
                        </a>
                        @else
                        <p class="text-gray-800 font-semibold mt-1">-</p>
                        @endif
                    </div>
                </div>

                <!-- Email -->
                <div class="flex items-start gap-3">
                    <div class="bg-purple-100 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Email</p>
                        @if($vendor->email)
                        <a href="mailto:{{ $vendor->email }}" class="text-blue-600 hover:text-blue-700 font-semibold mt-1 block break-all">
                            {{ $vendor->email }}
                        </a>
                        @else
                        <p class="text-gray-800 font-semibold mt-1">-</p>
                        @endif
                    </div>
                </div>

                <!-- Total Products -->
<div class="flex items-start gap-3">
    <div class="bg-amber-100 p-2 rounded-lg">
        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
    </div>
    <div>
        <p class="text-sm text-gray-500 font-medium">Total Produk</p>
        <p class="text-gray-800 font-bold text-xl mt-1">{{ $totalUniqueProducts ?? $vendor->productPrices()->active()->distinct('product_id')->count('product_id') }}</p>
        <p class="text-xs text-gray-500 mt-0.5">Produk aktif</p>
    </div>
</div>
            </div>

            @if($vendor->address)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-start gap-3">
                    <div class="bg-red-100 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-500 font-medium mb-1">Alamat</p>
                        <p class="text-gray-800">{{ $vendor->address }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if($vendor->notes)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-start gap-3">
                    <div class="bg-indigo-100 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-500 font-medium mb-1">Catatan</p>
                        <p class="text-gray-800">{{ $vendor->notes }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Products Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Daftar Produk</h3>
            <p class="text-sm text-gray-600 mt-1">Produk yang tersedia dari vendor ini</p>
        </div>

        @if($productsByCategory->isEmpty())
        <div class="p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="text-gray-500 text-lg font-medium">Belum ada produk</p>
            <p class="text-gray-400 text-sm mt-1">Vendor ini belum memiliki produk yang terdaftar</p>
        </div>
        @else
        <div class="divide-y divide-gray-200">
            @foreach($productsByCategory as $categoryName => $products)
            <div class="p-6">
                <h4 class="text-md font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <span class="w-1 h-6 bg-blue-600 rounded"></span>
                    {{ $categoryName }}
                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs font-semibold ml-2">
                        {{ $products->count() }} produk
                    </span>
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($products as $productPrice)
<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between mb-3">
        <div class="flex-1">
            <h5 class="font-semibold text-gray-800">{{ $productPrice->product->name }}</h5>
            <p class="text-xs text-gray-500 mt-1">{{ $productPrice->product->sku }}</p>
        </div>
    </div>
    
    <div class="space-y-2">
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Harga Beli:</span>
            <span class="font-bold text-blue-600">Rp {{ number_format($productPrice->purchase_price, 0, ',', '.') }}</span>
        </div>
        
        @if($productPrice->effective_from)
        <div class="text-xs text-gray-500 pt-2 border-t border-gray-100 flex items-center justify-between">
            <span>Berlaku dari:</span>
            <span class="font-medium">{{ \Carbon\Carbon::parse($productPrice->effective_from)->format('d/m/Y') }}</span>
        </div>
        @endif
        
        @if($productPrice->effective_to)
        <div class="text-xs text-gray-500 flex items-center justify-between">
            <span>Berlaku sampai:</span>
            <span class="font-medium">{{ \Carbon\Carbon::parse($productPrice->effective_to)->format('d/m/Y') }}</span>
        </div>
        @else
        <div class="text-xs text-green-600 flex items-center gap-1">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium">Masih Berlaku</span>
        </div>
        @endif

        @if($productPrice->notes)
        <div class="text-xs text-gray-600 pt-2 border-t border-gray-100">
            <span class="font-medium">Catatan:</span> {{ $productPrice->notes }}
        </div>
        @endif
    </div>
</div>
@endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection