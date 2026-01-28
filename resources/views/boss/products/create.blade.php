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

        <form action="{{ route('boss.products.store') }}" method="POST" class="p-6 space-y-6" id="productForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Produk dengan Autocomplete -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Produk <span class="text-red-500">*</span>
                    </label>
                    
                    <!-- Hidden input untuk product_id (jika produk sudah ada) -->
                    <input type="hidden" name="product_id" id="product_id" value="">
                    
                    <!-- Input dengan autocomplete -->
                    <div class="relative">
                        <input 
                            type="text" 
                            name="name" 
                            id="product_name"
                            value="{{ old('name') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                            placeholder="Ketik nama produk..."
                            autocomplete="off"
                            required
                        >
                        
                        <!-- Dropdown autocomplete results -->
                        <div id="autocomplete_results" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                            <!-- Results akan muncul di sini -->
                        </div>
                        
                        <!-- Loading indicator -->
                        <div id="autocomplete_loading" class="absolute right-3 top-3 hidden">
                            <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Info badge -->
                    <div id="product_status_badge" class="hidden mt-2">
                        <!-- Badge akan muncul di sini -->
                    </div>
                    
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="category_id" id="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
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
                    <select name="unit" id="unit" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('unit') border-red-500 @enderror" required>
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
                        id="sku"
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
                        id="barcode"
                        value="{{ old('barcode') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('barcode') border-red-500 @enderror"
                    >
                    @error('barcode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga Jual -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Harga Jual (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="selling_price" 
                        id="selling_price"
                        value="{{ old('selling_price') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('selling_price') border-red-500 @enderror"
                        min="0"
                        placeholder="Contoh: 15000"
                        required
                    >
                    @error('selling_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Minimum Stok -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Minimum Stok <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="min_stock" 
                        id="min_stock"
                        value="{{ old('min_stock', 5) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('min_stock') border-red-500 @enderror"
                        min="0"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">Sistem akan memberi notifikasi jika stock di bawah angka ini</p>
                    @error('min_stock')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea 
                        name="description" 
                        id="description"
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Deskripsi produk..."
                    >{{ old('description') }}</textarea>
                </div>

                <!-- ======== MODE SELECTOR ======== -->
                <div class="md:col-span-2 border-t pt-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Metode Input Stock & Harga Beli</h4>
                    
                    <!-- Mode Toggle -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                        <div class="flex items-center gap-6">
                            <label class="flex items-center cursor-pointer group">
                                <input 
                                    type="radio" 
                                    name="input_mode" 
                                    value="manual" 
                                    id="mode_manual"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                    checked
                                >
                                <div class="ml-3">
                                    <span class="text-sm font-semibold text-gray-900 group-hover:text-blue-600">Input Manual</span>
                                    <p class="text-xs text-gray-500 mt-0.5">Langsung isi stock dan harga beli</p>
                                </div>
                            </label>
                            
                            <label class="flex items-center cursor-pointer group">
                                <input 
                                    type="radio" 
                                    name="input_mode" 
                                    value="vendor" 
                                    id="mode_vendor"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                >
                                <div class="ml-3">
                                    <span class="text-sm font-semibold text-gray-900 group-hover:text-blue-600">Dari Vendor</span>
                                    <p class="text-xs text-gray-500 mt-0.5">Stock otomatis dari vendor (tracking lengkap)</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- ======== MANUAL MODE ======== -->
                    <div id="manualInputSection" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Harga Beli Manual -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Harga Beli (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    name="purchase_price_manual" 
                                    id="purchase_price_manual"
                                    value="{{ old('purchase_price_manual') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    min="0"
                                    placeholder="Contoh: 10000"
                                >
                            </div>

                            <!-- Stock Manual -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Stok Awal <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    name="stock_manual" 
                                    id="stock_manual"
                                    value="{{ old('stock_manual', 0) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    min="0"
                                    placeholder="Contoh: 100"
                                >
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <div class="flex gap-2">
                                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-sm text-yellow-800">
                                    <strong>Catatan:</strong> Mode manual tidak akan tracking harga per vendor. Gunakan mode "Dari Vendor" untuk tracking lengkap.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- ======== VENDOR MODE ======== -->
                    <div id="vendorInputSection" class="hidden space-y-4">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-base font-semibold text-gray-800">Vendor & Harga Supplier</h4>
                                <p class="text-sm text-gray-600 mt-1">Tambahkan minimal 1 vendor</p>
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

                        <!-- Info Box -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div class="text-sm text-blue-800">
                                    <p class="font-medium mb-1">Cara kerja mode vendor:</p>
                                    <ul class="list-disc list-inside space-y-1 text-blue-700">
                                        <li><strong>Stock otomatis</strong> = Total qty dari semua vendor</li>
                                        <li><strong>Harga beli</strong> = Rata-rata harga dari semua vendor</li>
                                        <li>Tracking harga terbaik per vendor</li>
                                    </ul>
                                </div>
                            </div>
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
                            <p class="text-gray-400 text-sm mt-1">Klik tombol "Tambah Vendor" untuk mulai</p>
                        </div>

                        <!-- Summary Box (hidden initially) -->
                        <div id="vendorSummary" class="hidden mt-4 bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg p-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Total Stock</p>
                                    <p class="text-2xl font-bold text-green-600" id="totalStock">0</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Rata-rata Harga Beli</p>
                                    <p class="text-2xl font-bold text-blue-600" id="avgPrice">Rp 0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden inputs untuk backend -->
                    <input type="hidden" name="stock" id="stock_final" value="0">
                    <input type="hidden" name="purchase_price" id="purchase_price_final" value="0">
                </div>
                <!-- ======== END MODE SECTION ======== -->

                <!-- Status -->
                <div class="md:col-span-2 space-y-4 border-t pt-6">
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
    let isExistingProduct = false;
    let searchTimeout = null;
    let currentMode = 'manual';
    
    const vendorContainer = document.getElementById('vendorContainer');
    const emptyState = document.getElementById('emptyVendorState');
    const addVendorBtn = document.getElementById('addVendorBtn');
    const productNameInput = document.getElementById('product_name');
    const productIdInput = document.getElementById('product_id');
    const autocompleteResults = document.getElementById('autocomplete_results');
    const autocompleteLoading = document.getElementById('autocomplete_loading');
    const statusBadge = document.getElementById('product_status_badge');
    const vendorSummary = document.getElementById('vendorSummary');
    const totalStockEl = document.getElementById('totalStock');
    const avgPriceEl = document.getElementById('avgPrice');
    const productForm = document.getElementById('productForm');
    
    // Mode sections
    const manualInputSection = document.getElementById('manualInputSection');
    const vendorInputSection = document.getElementById('vendorInputSection');
    const modeManual = document.getElementById('mode_manual');
    const modeVendor = document.getElementById('mode_vendor');
    
    // Final inputs
    const stockFinal = document.getElementById('stock_final');
    const purchasePriceFinal = document.getElementById('purchase_price_final');
    
    // Manual inputs
    const stockManual = document.getElementById('stock_manual');
    const purchasePriceManual = document.getElementById('purchase_price_manual');

    // Vendor data dari backend
    const vendors = @json($vendors);

    // Fields yang akan readonly untuk existing product
    const readonlyFields = ['product_name', 'sku', 'barcode', 'unit'];

    // Toggle mode
    function toggleMode(mode) {
        currentMode = mode;
        
        if (mode === 'manual') {
            manualInputSection.classList.remove('hidden');
            vendorInputSection.classList.add('hidden');
            
            // Set required untuk manual
            stockManual.required = true;
            purchasePriceManual.required = true;
            
            // Update final values dari manual
            updateFinalValuesFromManual();
        } else {
            manualInputSection.classList.add('hidden');
            vendorInputSection.classList.remove('hidden');
            
            // Remove required dari manual
            stockManual.required = false;
            purchasePriceManual.required = false;
            
            // Update final values dari vendor
            calculateVendorData();
        }
    }

    // Update final values dari manual input
    function updateFinalValuesFromManual() {
        if (currentMode === 'manual') {
            stockFinal.value = stockManual.value || 0;
            purchasePriceFinal.value = purchasePriceManual.value || 0;
        }
    }

    // Event listeners untuk mode toggle
    modeManual.addEventListener('change', () => toggleMode('manual'));
    modeVendor.addEventListener('change', () => toggleMode('vendor'));
    
    // Event listeners untuk manual input
    stockManual.addEventListener('input', updateFinalValuesFromManual);
    purchasePriceManual.addEventListener('input', updateFinalValuesFromManual);

    // Toggle empty state
    function toggleEmptyState() {
        const hasVendors = vendorContainer.children.length > 0;
        emptyState.style.display = hasVendors ? 'none' : 'block';
        vendorSummary.classList.toggle('hidden', !hasVendors);
    }

    // Calculate total stock dan rata-rata harga
    function calculateVendorData() {
        if (currentMode !== 'vendor') return;
        
        let totalStock = 0;
        let totalPrice = 0;
        let vendorCount = 0;
        
        document.querySelectorAll('.vendor-qty-input').forEach(input => {
            const qty = parseInt(input.value) || 0;
            totalStock += qty;
        });
        
        document.querySelectorAll('.vendor-price-input').forEach(input => {
            const price = parseInt(input.value) || 0;
            if (price > 0) {
                totalPrice += price;
                vendorCount++;
            }
        });
        
        const avgPrice = vendorCount > 0 ? Math.round(totalPrice / vendorCount) : 0;
        
        // Update final inputs
        stockFinal.value = totalStock;
        purchasePriceFinal.value = avgPrice;
        
        // Update summary display
        totalStockEl.textContent = totalStock;
        avgPriceEl.textContent = 'Rp ' + avgPrice.toLocaleString('id-ID');
    }

    // Form validation sebelum submit
    productForm.addEventListener('submit', function(e) {
        if (currentMode === 'vendor') {
            const vendorCount = vendorContainer.children.length;
            
            if (vendorCount === 0) {
                e.preventDefault();
                alert('⚠️ Mode vendor aktif. Wajib menambahkan minimal 1 vendor!');
                addVendorBtn.focus();
                addVendorBtn.classList.add('animate-bounce');
                setTimeout(() => {
                    addVendorBtn.classList.remove('animate-bounce');
                }, 1000);
                return false;
            }
        } else {
            // Validasi manual mode
            if (!stockManual.value || !purchasePriceManual.value) {
                e.preventDefault();
                alert('⚠️ Mohon lengkapi Stock Awal dan Harga Beli!');
                return false;
            }
        }
    });

    // Search products (autocomplete)
    function searchProducts(query) {
        if (query.length < 2) {
            autocompleteResults.classList.add('hidden');
            return;
        }

        autocompleteLoading.classList.remove('hidden');

        fetch(`{{ route('boss.products.search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(products => {
                autocompleteLoading.classList.add('hidden');
                displayAutocompleteResults(products, query);
            })
            .catch(error => {
                console.error('Search error:', error);
                autocompleteLoading.classList.add('hidden');
            });
    }

    // Display autocomplete results
    function displayAutocompleteResults(products, query) {
        if (products.length === 0) {
            autocompleteResults.innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    <p class="font-medium">Produk tidak ditemukan</p>
                    <p class="text-sm mt-1">Akan dibuat sebagai produk baru: <strong>${query}</strong></p>
                </div>
            `;
            autocompleteResults.classList.remove('hidden');
            return;
        }

        let html = '';
        products.forEach(product => {
            html += `
                <div class="autocomplete-item p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0" data-product='${JSON.stringify(product)}'>
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">${product.name}</p>
                            <div class="flex items-center gap-3 mt-1 text-xs text-gray-600">
                                <span class="bg-gray-100 px-2 py-0.5 rounded">SKU: ${product.sku || '-'}</span>
                                <span>Stock: ${product.stock} ${product.unit}</span>
                                <span>${product.vendors.length} vendor</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-500">Harga Jual</span>
                            <p class="font-semibold text-blue-600">Rp ${parseInt(product.selling_price).toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                </div>
            `;
        });

        autocompleteResults.innerHTML = html;
        autocompleteResults.classList.remove('hidden');

        // Add click event to each item
        document.querySelectorAll('.autocomplete-item').forEach(item => {
            item.addEventListener('click', function() {
                const product = JSON.parse(this.dataset.product);
                selectExistingProduct(product);
            });
        });
    }

    // Select existing product
    function selectExistingProduct(product) {
        isExistingProduct = true;
        productIdInput.value = product.id;
        
        // Fill form dengan data produk
        document.getElementById('product_name').value = product.name;
        document.getElementById('category_id').value = product.category_id || '';
        document.getElementById('sku').value = product.sku || '';
        document.getElementById('barcode').value = product.barcode || '';
        document.getElementById('selling_price').value = product.selling_price;
        document.getElementById('min_stock').value = product.min_stock;
        document.getElementById('unit').value = product.unit;
        document.getElementById('description').value = product.description || '';

        // Set readonly untuk beberapa field
        readonlyFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.readOnly = true;
                field.classList.add('bg-gray-100', 'cursor-not-allowed');
            }
        });

        // Show status badge
        statusBadge.innerHTML = `
            <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 px-4 py-2 rounded-lg">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">Menambah stock untuk produk existing</span>
                <button type="button" onclick="resetToNewProduct()" class="ml-2 text-blue-800 hover:text-blue-900 underline text-sm">
                    Buat produk baru
                </button>
            </div>
        `;
        statusBadge.classList.remove('hidden');

        // Hide autocomplete
        autocompleteResults.classList.add('hidden');

        // Update section title
        document.querySelector('.bg-gray-50 h3').textContent = 'Tambah Stock Produk: ' + product.name;
    }

    // Reset to new product
    window.resetToNewProduct = function() {
        isExistingProduct = false;
        productIdInput.value = '';
        
        // Clear readonly
        readonlyFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.readOnly = false;
                field.classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
        });

        // Clear form
        document.getElementById('product_name').value = '';
        document.getElementById('product_name').focus();
        
        // Hide badge
        statusBadge.classList.add('hidden');
        
        // Reset title
        document.querySelector('.bg-gray-50 h3').textContent = 'Informasi Produk';
        
        // Reset calculations
        if (currentMode === 'vendor') {
            calculateVendorData();
        } else {
            updateFinalValuesFromManual();
        }
    };

    // Add vendor row
    function addVendorRow() {
        const today = new Date().toISOString().split('T')[0];
        
        const vendorRow = document.createElement('div');
        vendorRow.className = 'vendor-item bg-white border border-gray-200 rounded-lg p-4';
        vendorRow.dataset.index = vendorIndex;
        
        vendorRow.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Vendor <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="vendors[${vendorIndex}][vendor_id]" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                        >
                            <option value="">Pilih Vendor</option>
                            ${vendors.map(v => `<option value="${v.id}">${v.name} (${v.code})</option>`).join('')}
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jumlah (Qty) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="vendors[${vendorIndex}][quantity]" 
                            class="vendor-qty-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                            min="1"
                            placeholder="0"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Harga Beli (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="vendors[${vendorIndex}][vendor_price]" 
                            class="vendor-price-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                            min="0"
                            placeholder="0"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Berlaku Sejak <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="vendors[${vendorIndex}][effective_from]" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                            value="${today}"
                        >
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                        <input 
                            type="text" 
                            name="vendors[${vendorIndex}][notes]" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                            placeholder="Contoh: Harga promo bulan ini"
                        >
                    </div>
                </div>

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

        // Add event listeners
        const qtyInput = vendorRow.querySelector('.vendor-qty-input');
        const priceInput = vendorRow.querySelector('.vendor-price-input');
        
        qtyInput.addEventListener('input', calculateVendorData);
        priceInput.addEventListener('input', calculateVendorData);

        vendorRow.querySelector('.remove-vendor-btn').addEventListener('click', function() {
            vendorRow.remove();
            toggleEmptyState();
            calculateVendorData();
        });
        
        calculateVendorData();
    }

    // Event listeners
    addVendorBtn.addEventListener('click', addVendorRow);

    // Autocomplete on product name input
    productNameInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            autocompleteResults.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            searchProducts(query);
        }, 300);
    });

    // Close autocomplete when clicking outside
    document.addEventListener('click', function(e) {
        if (!productNameInput.contains(e.target) && !autocompleteResults.contains(e.target)) {
            autocompleteResults.classList.add('hidden');
        }
    });

    // Initialize
    toggleEmptyState();
    toggleMode('manual'); // Default mode
});
</script>
@endpush