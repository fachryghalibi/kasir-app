@extends('layouts.app')

@section('title', 'POS Kasir - POS System')

@section('page-title', 'POS Kasir')
@section('page-description', 'Sistem kasir untuk transaksi penjualan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- LEFT SIDE: Product List & Search -->
    <div class="lg:col-span-2 space-y-4">
        <!-- Search & Filter -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-200">
            <div class="flex flex-col md:flex-row gap-3">
                <!-- Search Input -->
                <div class="flex-1">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="search-product"
                            placeholder="Cari produk (nama, SKU, atau scan barcode)..."
                            class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            autofocus
                        >
                        <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Category Filter -->
                <select id="filter-category" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-semibold text-gray-900">Pilih Produk</h3>
            </div>
            <div id="product-list" class="grid grid-cols-2 md:grid-cols-3 gap-3 p-4 max-h-[600px] overflow-y-auto">
                @foreach($products as $product)
                    <div class="product-card border border-gray-200 rounded-lg p-3 hover:border-blue-500 hover:shadow-md transition-all cursor-pointer"
                         data-product-id="{{ $product->id }}"
                         data-product-name="{{ $product->name }}"
                         data-product-price="{{ $product->selling_price }}"
                         data-product-stock="{{ $product->stock }}"
                         data-category-id="{{ $product->category_id }}">
                        
                        <!-- Product Image Placeholder -->
                        <div class="bg-gray-100 rounded-lg h-24 flex items-center justify-center mb-2">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>

                        <!-- Product Info -->
                        <h4 class="font-medium text-sm text-gray-900 mb-1 line-clamp-2">{{ $product->name }}</h4>
                        <p class="text-xs text-gray-500 mb-2">Stok: {{ $product->stock }} {{ $product->unit }}</p>
                        <p class="text-blue-600 font-bold text-sm">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                        
                        @if($product->stock <= 0)
                            <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded">Habis</span>
                        @elseif($product->isLowStock())
                            <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded">Stok Rendah</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Shopping Cart & Checkout -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 sticky top-4">
            <!-- Cart Header -->
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Keranjang</h3>
                    <button id="clear-cart" class="text-red-600 hover:text-red-700 text-sm font-medium">
                        Hapus Semua
                    </button>
                </div>
            </div>

            <!-- Cart Items -->
            <div id="cart-items" class="p-4 max-h-[300px] overflow-y-auto space-y-3">
                <div id="empty-cart" class="text-center py-8 text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-sm">Keranjang kosong</p>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="p-4 border-t border-gray-200 space-y-3">
                <input 
                    type="text" 
                    id="customer-name"
                    placeholder="Nama Customer (Opsional)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                >
                <input 
                    type="text" 
                    id="customer-phone"
                    placeholder="No. HP Customer (Opsional)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                >
            </div>

            <!-- Discount -->
            <div class="p-4 border-t border-gray-200">
                <div class="flex gap-2">
                    <select id="discount-type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Tanpa Diskon</option>
                        <option value="percentage">Persentase (%)</option>
                        <option value="fixed">Nominal (Rp)</option>
                    </select>
                    <input 
                        type="number" 
                        id="discount-value"
                        placeholder="Nilai diskon"
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                        min="0"
                        disabled
                    >
                </div>
            </div>

            <!-- Summary -->
            <div class="p-4 border-t border-gray-200 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal</span>
                    <span id="subtotal" class="font-medium">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Diskon</span>
                    <span id="discount" class="font-medium text-red-600">- Rp 0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Pajak (11%)</span>
                    <span id="tax" class="font-medium">Rp 0</span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t pt-2">
                    <span>Total</span>
                    <span id="total" class="text-blue-600">Rp 0</span>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="p-4 border-t border-gray-200">
                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                <select id="payment-method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="cash">Cash / Tunai</option>
                    <option value="debit_card">Kartu Debit</option>
                    <option value="credit_card">Kartu Kredit</option>
                    <option value="qris">QRIS</option>
                    <option value="transfer">Transfer Bank</option>
                </select>
            </div>

            <!-- Cash Payment Input -->
            <div id="cash-payment" class="p-4 border-t border-gray-200 space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Bayar</label>
                    <input 
                        type="number" 
                        id="paid-amount"
                        placeholder="Masukkan jumlah bayar"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        min="0"
                    >
                </div>
                <div class="flex justify-between text-sm font-medium">
                    <span>Kembalian</span>
                    <span id="change" class="text-green-600">Rp 0</span>
                </div>
            </div>

            <!-- Checkout Button -->
            <div class="p-4 border-t border-gray-200">
                <button 
                    id="checkout-btn"
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                    disabled
                >
                    <span id="checkout-text">Checkout</span>
                    <span id="checkout-loading" class="hidden">
                        <svg class="animate-spin h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="success-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Transaksi Berhasil!</h3>
            <p class="text-gray-600 mb-1">Invoice: <span id="modal-invoice" class="font-mono font-semibold"></span></p>
            <p class="text-2xl font-bold text-blue-600 mb-1" id="modal-total"></p>
            <p class="text-gray-600 mb-6">Kembalian: <span id="modal-change" class="font-semibold text-green-600"></span></p>
            
            <div class="flex gap-3">
                <button id="print-receipt" class="flex-1 bg-gray-600 text-white py-3 rounded-lg font-semibold hover:bg-gray-700 transition-colors">
                    Print Struk
                </button>
                <button id="new-transaction" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // State management
    let cart = [];
    let products = @json($products);
    let lastTransactionId = null; // PERBAIKAN: Deklarasi variable di scope yang benar

    // DOM Elements
    const searchInput = document.getElementById('search-product');
    const filterCategory = document.getElementById('filter-category');
    const productList = document.getElementById('product-list');
    const cartItems = document.getElementById('cart-items');
    const emptyCart = document.getElementById('empty-cart');
    const clearCartBtn = document.getElementById('clear-cart');
    const discountType = document.getElementById('discount-type');
    const discountValue = document.getElementById('discount-value');
    const paymentMethod = document.getElementById('payment-method');
    const paidAmountInput = document.getElementById('paid-amount');
    const checkoutBtn = document.getElementById('checkout-btn');
    const successModal = document.getElementById('success-modal');

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Add click event to product cards
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', function() {
                const productId = parseInt(this.dataset.productId);
                const productName = this.dataset.productName;
                const productPrice = parseInt(this.dataset.productPrice);
                const productStock = parseInt(this.dataset.productStock);

                if (productStock <= 0) {
                    alert('Produk ini habis!');
                    return;
                }

                addToCart(productId, productName, productPrice, productStock);
            });
        });

        // Search product
        searchInput.addEventListener('input', filterProducts);
        filterCategory.addEventListener('change', filterProducts);

        // Discount
        discountType.addEventListener('change', function() {
            discountValue.disabled = !this.value;
            if (!this.value) discountValue.value = '';
            calculateTotals();
        });
        discountValue.addEventListener('input', calculateTotals);

        // Payment
        paymentMethod.addEventListener('change', toggleCashPayment);
        paidAmountInput.addEventListener('input', calculateChange);

        // Clear cart
        clearCartBtn.addEventListener('click', function() {
            if (confirm('Hapus semua item dari keranjang?')) {
                cart = [];
                renderCart();
            }
        });

        // Checkout
        checkoutBtn.addEventListener('click', processCheckout);

        // Modal buttons
        document.getElementById('new-transaction').addEventListener('click', function() {
            successModal.classList.add('hidden');
            cart = [];
            renderCart();
            document.getElementById('customer-name').value = '';
            document.getElementById('customer-phone').value = '';
            discountType.value = '';
            discountValue.value = '';
            paidAmountInput.value = '';
            lastTransactionId = null; // Reset transaction ID
        });

        document.getElementById('print-receipt').addEventListener('click', function() {
            if (lastTransactionId) {
                // Buka halaman print di tab baru menggunakan route yang sama seperti di history
                const receiptUrl = '{{ route("pos.receipt", ":id") }}'.replace(':id', lastTransactionId);
                const printWindow = window.open(receiptUrl, '_blank', 'width=400,height=600');
                
                // PERBAIKAN: Tambahkan fallback jika window.open gagal
                if (!printWindow) {
                    alert('Pop-up blocker mungkin menghalangi pembukaan struk. Silakan izinkan pop-up untuk situs ini.');
                    // Alternatif: buka di tab yang sama
                    window.location.href = receiptUrl;
                }
            } else {
                alert('Transaction ID tidak ditemukan!');
                console.error('lastTransactionId is null or undefined');
            }
        });
    });

    // Add to cart
    function addToCart(productId, productName, productPrice, productStock) {
        const existingItem = cart.find(item => item.product_id === productId);

        if (existingItem) {
            if (existingItem.quantity >= productStock) {
                alert(`Stok tidak cukup! Tersedia: ${productStock}`);
                return;
            }
            existingItem.quantity++;
        } else {
            cart.push({
                product_id: productId,
                name: productName,
                price: productPrice,
                quantity: 1,
                stock: productStock
            });
        }

        renderCart();
    }

    // Render cart
    function renderCart() {
        if (cart.length === 0) {
            cartItems.innerHTML = '<div id="empty-cart" class="text-center py-8 text-gray-400"><svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg><p class="text-sm">Keranjang kosong</p></div>';
            checkoutBtn.disabled = true;
            calculateTotals();
            return;
        }

        let html = '';
        cart.forEach((item, index) => {
            html += `
                <div class="flex items-center gap-3 border border-gray-200 rounded-lg p-3">
                    <div class="flex-1">
                        <h4 class="font-medium text-sm text-gray-900">${item.name}</h4>
                        <p class="text-xs text-gray-500">Rp ${formatNumber(item.price)} × ${item.quantity}</p>
                        <p class="text-sm font-semibold text-blue-600">Rp ${formatNumber(item.price * item.quantity)}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="updateQuantity(${index}, -1)" class="w-8 h-8 bg-gray-200 rounded hover:bg-gray-300 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        </button>
                        <span class="w-8 text-center font-semibold">${item.quantity}</span>
                        <button onclick="updateQuantity(${index}, 1)" class="w-8 h-8 bg-gray-200 rounded hover:bg-gray-300 flex items-center justify-center" ${item.quantity >= item.stock ? 'disabled' : ''}>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                    <button onclick="removeItem(${index})" class="text-red-600 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            `;
        });

        cartItems.innerHTML = html;
        checkoutBtn.disabled = false;
        calculateTotals();
    }

    // Update quantity
    function updateQuantity(index, delta) {
        cart[index].quantity += delta;
        if (cart[index].quantity <= 0) {
            cart.splice(index, 1);
        }
        renderCart();
    }

    // Remove item
    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    // Calculate totals
    function calculateTotals() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        
        let discountAmount = 0;
        const discType = discountType.value;
        const discVal = parseFloat(discountValue.value) || 0;

        if (discType === 'percentage') {
            discountAmount = (subtotal * discVal) / 100;
        } else if (discType === 'fixed') {
            discountAmount = discVal;
        }

        const afterDiscount = subtotal - discountAmount;
        const taxAmount = (afterDiscount * 11) / 100;
        const total = afterDiscount + taxAmount;

        document.getElementById('subtotal').textContent = 'Rp ' + formatNumber(subtotal);
        document.getElementById('discount').textContent = '- Rp ' + formatNumber(discountAmount);
        document.getElementById('tax').textContent = 'Rp ' + formatNumber(taxAmount);
        document.getElementById('total').textContent = 'Rp ' + formatNumber(total);

        calculateChange();
    }

    // Calculate change
    function calculateChange() {
        const total = parseInt(document.getElementById('total').textContent.replace(/[^0-9]/g, ''));
        const paid = parseFloat(paidAmountInput.value) || 0;
        const change = paid - total;

        document.getElementById('change').textContent = 'Rp ' + formatNumber(Math.max(0, change));
    }

    // Toggle cash payment
    function toggleCashPayment() {
        const method = paymentMethod.value;
        const cashPayment = document.getElementById('cash-payment');
        
        if (method === 'cash') {
            cashPayment.classList.remove('hidden');
        } else {
            cashPayment.classList.add('hidden');
            paidAmountInput.value = '';
            const total = parseInt(document.getElementById('total').textContent.replace(/[^0-9]/g, ''));
            paidAmountInput.value = total;
            calculateChange();
        }
    }

    // Filter products
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const categoryId = filterCategory.value;

        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.dataset.productName.toLowerCase();
            const catId = card.dataset.categoryId;
            
            const matchSearch = name.includes(searchTerm);
            const matchCategory = !categoryId || catId === categoryId;

            card.style.display = (matchSearch && matchCategory) ? 'block' : 'none';
        });
    }

    // Process checkout
    async function processCheckout() {
        if (cart.length === 0) {
            alert('Keranjang kosong!');
            return;
        }

        const total = parseInt(document.getElementById('total').textContent.replace(/[^0-9]/g, ''));
        const paidAmount = parseFloat(paidAmountInput.value) || 0;

        if (paymentMethod.value === 'cash' && paidAmount < total) {
            alert('Jumlah bayar kurang!');
            return;
        }

        const data = {
            items: cart.map(item => ({
                product_id: item.product_id,
                quantity: item.quantity,
                price: item.price
            })),
            customer_name: document.getElementById('customer-name').value,
            customer_phone: document.getElementById('customer-phone').value,
            payment_method: paymentMethod.value,
            paid_amount: paymentMethod.value === 'cash' ? paidAmount : total,
            discount_type: discountType.value || null,
            discount_value: parseFloat(discountValue.value) || 0,
        };

        // Show loading
        checkoutBtn.disabled = true;
        document.getElementById('checkout-text').classList.add('hidden');
        document.getElementById('checkout-loading').classList.remove('hidden');

        try {
            const response = await fetch('{{ route("pos.checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                // PERBAIKAN: Simpan transaction ID dari response
                lastTransactionId = result.data.id;
                
                console.log('Transaction ID:', lastTransactionId); // Debug log
                
                // Show success modal
                document.getElementById('modal-invoice').textContent = result.data.invoice_number;
                document.getElementById('modal-total').textContent = 'Rp ' + formatNumber(result.data.total);
                document.getElementById('modal-change').textContent = 'Rp ' + formatNumber(result.data.change);
                successModal.classList.remove('hidden');
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Checkout error:', error);
            alert('Terjadi kesalahan: ' + error.message);
        } finally {
            checkoutBtn.disabled = false;
            document.getElementById('checkout-text').classList.remove('hidden');
            document.getElementById('checkout-loading').classList.add('hidden');
        }
    }

    // Format number
    function formatNumber(num) {
        return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
</script>
@endpush