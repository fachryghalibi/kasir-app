@extends('layouts.app')

@section('title', 'Detail Karyawan - POS System')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('boss.employees.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Karyawan</h1>
                <p class="text-sm text-gray-600 mt-1">Informasi lengkap dan riwayat transaksi karyawan</p>
            </div>
        </div>
        <a href="{{ route('boss.employees.edit', $employee) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Data
        </a>
    </div>

    <!-- Employee Info Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-start gap-6">
            <!-- Avatar -->
            <div class="w-24 h-24 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-3xl flex-shrink-0">
                {{ substr($employee->name, 0, 1) }}
            </div>

            <!-- Info -->
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $employee->name }}</h2>
                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full 
                        {{ $employee->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Email -->
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="text-sm font-medium text-gray-900">{{ $employee->email }}</p>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">No. Telepon</p>
                            <p class="text-sm font-medium text-gray-900">{{ $employee->phone ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- UUID -->
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">UUID</p>
                            <p class="text-sm font-medium text-gray-900 font-mono">{{ $employee->uuid }}</p>
                        </div>
                    </div>

                    <!-- Join Date -->
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">Bergabung Sejak</p>
                            <p class="text-sm font-medium text-gray-900">{{ $employee->created_at->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Transaksi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Transaksi</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($overallStats['total_transactions']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Penjualan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Penjualan</p>
                    <p class="text-2xl font-bold text-gray-900">
                        Rp {{ number_format($overallStats['total_sales'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Hari Kerja -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Hari Kerja</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $overallStats['working_days'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">hari</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Riwayat Transaksi</h3>
            <p class="text-sm text-gray-600 mt-1">Semua transaksi yang dilakukan oleh {{ $employee->name }}</p>
        </div>

        <!-- Filter Section -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <form method="GET" action="{{ route('boss.employees.show', $employee) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search by Invoice -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Cari Invoice</label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="No. Invoice..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input 
                        type="date" 
                        name="date_from" 
                        value="{{ request('date_from') }}"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input 
                        type="date" 
                        name="date_to" 
                        value="{{ request('date_to') }}"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 text-sm rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'date_from', 'date_to']))
                        <a href="{{ route('boss.employees.show', $employee) }}" class="bg-gray-200 text-gray-700 px-4 py-2 text-sm rounded-lg hover:bg-gray-300 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Filtered Statistics -->
        @if(request()->hasAny(['search', 'date_from', 'date_to']))
        <div class="px-6 py-3 bg-blue-50 border-b border-blue-100">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-4">
                    <div>
                        <span class="text-gray-600">Hasil Filter:</span>
                        <span class="font-semibold text-gray-900">{{ number_format($stats['total_transactions']) }} Transaksi</span>
                    </div>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <div>
                        <span class="text-gray-600">Total:</span>
                        <span class="font-semibold text-green-600">Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}</span>
                    </div>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <div>
                        <span class="text-gray-600">Total Item:</span>
                        <span class="font-semibold text-gray-900">{{ number_format($stats['total_items']) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Transaction Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal & Waktu</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Diskon</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-blue-600">{{ $transaction->invoice_number }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $transaction->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $transaction->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm text-gray-900">{{ $transaction->items()->count() }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm text-gray-900">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm text-red-600">
                                    @if($transaction->discount_amount > 0)
                                        - Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-semibold text-gray-900">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Selesai
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button 
                                    onclick="showTransactionDetail({{ $transaction->id }})"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 text-xs font-medium rounded-lg hover:bg-blue-100 transition-colors"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-gray-500 font-medium">
                                        @if(request()->hasAny(['search', 'date_from', 'date_to']))
                                            Tidak ada transaksi yang sesuai dengan filter
                                        @else
                                            Belum ada transaksi
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div id="transactionDetailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-lg bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Detail Transaksi</h3>
                <p class="text-sm text-gray-600 mt-1">Daftar item yang dibeli dalam transaksi ini</p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div id="modalContent" class="mt-4">
            <!-- Loading State -->
            <div class="flex items-center justify-center py-12">
                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="ml-3 text-gray-600">Memuat data...</span>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showTransactionDetail(transactionId) {
    const modal = document.getElementById('transactionDetailModal');
    const modalContent = document.getElementById('modalContent');
    
    // Show modal
    modal.classList.remove('hidden');
    
    // Fetch transaction details
    fetch(`/boss/transactions/${transactionId}/items`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modalContent.innerHTML = generateModalContent(data.transaction, data.items);
            } else {
                modalContent.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-red-600">Gagal memuat data transaksi</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-red-600">Terjadi kesalahan saat memuat data</p>
                </div>
            `;
        });
}

function generateModalContent(transaction, items) {
    const itemsHtml = items.map((item, index) => `
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 text-sm text-gray-900">${index + 1}</td>
            <td class="px-4 py-3">
                <div class="text-sm font-medium text-gray-900">${item.product_name}</div>
                <div class="text-xs text-gray-500">SKU: ${item.product_sku}</div>
            </td>
            <td class="px-4 py-3 text-sm text-gray-900 text-center">${item.quantity}</td>
            <td class="px-4 py-3 text-sm text-gray-900 text-right">Rp ${formatNumber(item.price)}</td>
            <td class="px-4 py-3 text-sm text-gray-900 text-right">Rp ${formatNumber(item.subtotal)}</td>
        </tr>
    `).join('');

    return `
        <!-- Transaction Info -->
        <div class="bg-blue-50 rounded-lg p-4 mb-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-gray-600">Invoice</p>
                    <p class="text-sm font-semibold text-gray-900">${transaction.invoice_number}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600">Tanggal</p>
                    <p class="text-sm font-semibold text-gray-900">${transaction.date}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600">Kasir</p>
                    <p class="text-sm font-semibold text-gray-900">${transaction.cashier}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600">Metode Pembayaran</p>
                    <p class="text-sm font-semibold text-gray-900">${transaction.payment_method}</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    ${itemsHtml}
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="mt-4 bg-gray-50 rounded-lg p-4">
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium text-gray-900">Rp ${formatNumber(transaction.subtotal)}</span>
                </div>
                ${transaction.discount_amount > 0 ? `
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Diskon:</span>
                        <span class="font-medium text-red-600">- Rp ${formatNumber(transaction.discount_amount)}</span>
                    </div>
                ` : ''}
                ${transaction.tax_amount > 0 ? `
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Pajak (${transaction.tax_rate}%):</span>
                        <span class="font-medium text-gray-900">Rp ${formatNumber(transaction.tax_amount)}</span>
                    </div>
                ` : ''}
                <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-300">
                    <span class="text-gray-900">Total:</span>
                    <span class="text-blue-600">Rp ${formatNumber(transaction.total)}</span>
                </div>
            </div>
        </div>
    `;
}

function formatNumber(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

function closeModal() {
    document.getElementById('transactionDetailModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('transactionDetailModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
@endpush