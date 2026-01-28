<aside class="fixed top-0 left-0 h-screen w-64 bg-gray-900 text-white flex flex-col z-40">
    <!-- Logo -->
    <div class="p-6 border-b border-gray-700">
        <h1 class="text-2xl font-bold text-blue-400">🛒 Toko Anya</h1>
        <p class="text-xs text-gray-400 mt-1">{{ auth()->user()->role === 'boss' ? 'Boss Panel' : 'Kasir Panel' }}</p>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-1 px-3">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
            </li>

            @if(auth()->user()->isBoss())
                <!-- Boss Menu -->
                <li class="pt-4">
                    <span class="px-4 text-xs font-semibold text-gray-500 uppercase">Management</span>
                </li>
                
                <li>
                    <a href="{{ route('boss.products.index') }}" 
                    class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('boss.products.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span>Produk</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('boss.categories.index') }}" 
                    class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('boss.categories.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span>Kategori</span>
                    </a>
                </li>

                <li>
                    <div class="flex items-center px-4 py-3 text-gray-500 cursor-not-allowed opacity-60">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Karyawan</span>
                        <span class="ml-auto text-xs bg-gray-700 px-2 py-1 rounded">Soon</span>
                    </div>
                </li>

                <li class="pt-4">
                    <span class="px-4 text-xs font-semibold text-gray-500 uppercase">Laporan</span>
                </li>

                <li>
                    <div class="flex items-center px-4 py-3 text-gray-500 cursor-not-allowed opacity-60">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Laporan Penjualan</span>
                        <span class="ml-auto text-xs bg-gray-700 px-2 py-1 rounded">Soon</span>
                    </div>
                </li>

                <li>
                    <div class="flex items-center px-4 py-3 text-gray-500 cursor-not-allowed opacity-60">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Persetujuan Stok</span>
                        <span class="ml-auto text-xs bg-gray-700 px-2 py-1 rounded">Soon</span>
                    </div>
                </li>

                <li>
                    <a href="{{ route('boss.vendors.index') }}" 
                    class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('boss.vendors.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Vendor/Supplier</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('boss.vendor-comparison.index') }}" 
                    class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('boss.vendor-comparison.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Perbandingan Vendor</span>
                    </a>
                </li>
            @else
                <!-- Employee Menu -->
                <li class="pt-4">
                    <span class="px-4 text-xs font-semibold text-gray-500 uppercase">Transaksi</span>
                </li>

                <li>
                    <a href="{{ route('pos.index') }}" 
                    class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('pos.index') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>POS Kasir</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('pos.history') }}" 
                    class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('pos.history*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Riwayat Transaksi</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>

    <!-- User Info -->
    <div class="p-4 border-t border-gray-700">
        <div class="flex items-center">
            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400">{{ ucfirst(auth()->user()->role) }}</p>
            </div>
        </div>
    </div>
</aside>