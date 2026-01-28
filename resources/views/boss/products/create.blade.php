@extends('layouts.app')

@section('title', 'Tambah Produk - POS System')

@section('page-title', 'Tambah Produk')
@section('page-description', 'Tambahkan produk baru ke inventory')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('boss.products.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Produk
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Informasi Produk</h3>
        </div>

        <form action="{{ route('boss.products.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Produk -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Produk <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Satuan <span class="text-red-500">*</span>
                    </label>
                    <select name="unit" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('unit') border-red-500 @enderror" required>
                        <option value="pcs" {{ old('unit') === 'pcs' ? 'selected' : '' }}>Pcs</option>
                        <option value="kg" {{ old('unit') === 'kg' ? 'selected' : '' }}>Kg</option>
                        <option value="liter" {{ old('unit') === 'liter' ? 'selected' : '' }}>Liter</option>
                        <option value="box" {{ old('unit') === 'box' ? 'selected' : '' }}>Box</option>
                        <option value="pack" {{ old('unit') === 'pack' ? 'selected' : '' }}>Pack</option>
                    </select>
                    @error('unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SKU -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SKU (Opsional)</label>
                    <input 
                        type="text" 
                        name="sku" 
                        value="{{ old('sku') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('sku') border-red-500 @enderror"
                        placeholder="Auto-generate jika kosong"
                    >
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Barcode -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Barcode (Opsional)</label>
                    <input 
                        type="text" 
                        name="barcode" 
                        value="{{ old('barcode') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('barcode') border-red-500 @enderror"
                    >
                    @error('barcode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga Beli -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Harga Beli (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="purchase_price" 
                        value="{{ old('purchase_price') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('purchase_price') border-red-500 @enderror"
                        min="0"
                        required
                    >
                    @error('purchase_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga Jual -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Harga Jual (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="selling_price" 
                        value="{{ old('selling_price') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('selling_price') border-red-500 @enderror"
                        min="0"
                        required
                    >
                    @error('selling_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stok -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Stok Awal <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="stock" 
                        value="{{ old('stock', 0) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('stock') border-red-500 @enderror"
                        min="0"
                        required
                    >
                    @error('stock')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Minimum Stok -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Minimum Stok <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="min_stock" 
                        value="{{ old('min_stock', 5) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('min_stock') border-red-500 @enderror"
                        min="0"
                        required
                    >
                    @error('min_stock')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea 
                        name="description" 
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >{{ old('description') }}</textarea>
                </div>

                <!-- ======== SECTION VENDOR (TAMBAHAN BARU) ======== -->
                <div class="md:col-span-2 border-t pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800">Vendor & Harga Supplier</h4>
                            <p class="text-sm text-gray-600 mt-1">Tambahkan vendor dan harga beli untuk tracking harga terbaik</p>
                        </div>
                        <button 
                            type="button" 
                            id="addVendorBtn"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tambah Vendor
                        </button>
                    </div>

                    <!-- Container untuk vendor items -->
                    <div id="vendorContainer" class="space-y-4">
                        <!-- Vendor items akan ditambahkan di sini via JavaScript -->
                    </div>

                    <!-- Empty state -->
                    <div id="emptyVendorState" class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p class="text-gray-500 font-medium">Belum ada vendor ditambahkan</p>
                        <p class="text-gray-400 text-sm mt-1">Klik tombol "Tambah Vendor" untuk menambahkan vendor</p>
                    </div>
                </div>
                <!-- ======== END SECTION VENDOR ======== -->

                <!-- Status -->
                <div class="md:col-span-2 space-y-4">

                <!-- Status -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            id="is_active"
                            value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <label for="is_active" class="ml-2 text-sm text-gray-700">Produk Aktif (Tampil di POS)</label>
                    </div>

                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="is_featured" 
                            id="is_featured"
                            value="1"
                            {{ old('is_featured') ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <label for="is_featured" class="ml-2 text-sm text-gray-700">Produk Unggulan</label>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t">
                <a href="{{ route('boss.products.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let vendorIndex = 0;
    const vendorContainer = document.getElementById('vendorContainer');
    const emptyState = document.getElementById('emptyVendorState');
    const addVendorBtn = document.getElementById('addVendorBtn');

    // Vendor data dari backend
    const vendors = @json($vendors);

    // Toggle empty state
    function toggleEmptyState() {
        const hasVendors = vendorContainer.children.length > 0;
        emptyState.style.display = hasVendors ? 'none' : 'block';
    }

    // Add vendor row
    function addVendorRow() {
        const today = new Date().toISOString().split('T')[0];
        
        const vendorRow = document.createElement('div');
        vendorRow.className = 'vendor-item bg-white border border-gray-200 rounded-lg p-4';
        vendorRow.dataset.index = vendorIndex;
        
        vendorRow.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Vendor Select -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Vendor <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="vendors[${vendorIndex}][vendor_id]" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                            required
                        >
                            <option value="">Pilih Vendor</option>
                            ${vendors.map(v => `<option value="${v.id}">${v.name} (${v.code})</option>`).join('')}
                        </select>
                    </div>

                    <!-- Harga Vendor -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Harga Beli (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="vendors[${vendorIndex}][vendor_price]" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                            min="0"
                            placeholder="0"
                            required
                        >
                    </div>

                    <!-- Tanggal Berlaku -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Berlaku Sejak <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="vendors[${vendorIndex}][effective_from]" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                            value="${today}"
                            required
                        >
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                        <input 
                            type="text" 
                            name="vendors[${vendorIndex}][notes]" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                            placeholder="Contoh: Harga promo bulan ini"
                        >
                    </div>
                </div>

                <!-- Remove Button -->
                <button 
                    type="button" 
                    class="remove-vendor-btn flex-shrink-0 bg-red-100 hover:bg-red-200 text-red-700 p-2 rounded-lg transition-colors mt-6"
                    title="Hapus Vendor"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        `;

        vendorContainer.appendChild(vendorRow);
        vendorIndex++;
        toggleEmptyState();

        // Add remove event listener
        vendorRow.querySelector('.remove-vendor-btn').addEventListener('click', function() {
            vendorRow.remove();
            toggleEmptyState();
        });
    }

    // Event listener untuk tombol tambah vendor
    addVendorBtn.addEventListener('click', addVendorRow);

    // Initialize
    toggleEmptyState();
});
</script>
@endpush