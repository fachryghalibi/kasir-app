@extends('layouts.app')

@section('title', 'Hasil Perbandingan Vendor')

@section('content')
<div class="container-fluid px-6 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('boss.vendor-comparison.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-800">Hasil Perbandingan Vendor</h1>
                <p class="text-gray-600 mt-1">
                    Produk: <span class="font-semibold">{{ $product->name }}</span> | 
                    Periode: <span class="font-semibold">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</span> - 
                    <span class="font-semibold">{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
                </p>
            </div>
            <button onclick="window.print()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold">Harga Termurah</h3>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold mb-2">Rp {{ number_format($cheapestPrice, 0, ',', '.') }}</p>
            <p class="text-green-100 text-sm">{{ $cheapestVendor->vendor->name }}</p>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold">Harga Rata-rata</h3>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold mb-2">Rp {{ number_format($averagePrice, 0, ',', '.') }}</p>
            <p class="text-blue-100 text-sm">Dari {{ $vendorPrices->count() }} data harga</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold">Total Vendor</h3>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold mb-2">{{ $pricesByVendor->count() }}</p>
            <p class="text-purple-100 text-sm">Vendor dibandingkan</p>
        </div>
    </div>

    <!-- Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Grafik Perbandingan Harga Vendor</h3>
        <div class="h-96">
            <canvas id="priceComparisonChart"></canvas>
        </div>
    </div>

    <!-- Vendor Statistics -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-xl font-bold text-gray-800">Statistik Per Vendor</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Vendor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Harga Termurah</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Harga Rata-rata</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Jumlah Update</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Ranking</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php 
                        $sortedVendors = $pricesByVendor->sortBy('min_price');
                        $rank = 1;
                    @endphp
                    @foreach($sortedVendors as $vendorData)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                    {{ substr($vendorData['vendor']->name, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <p class="font-semibold text-gray-800">{{ $vendorData['vendor']->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $vendorData['vendor']->code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-lg font-bold text-gray-800">
                                Rp {{ number_format($vendorData['min_price'], 0, ',', '.') }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-700 font-medium">
                                Rp {{ number_format($vendorData['avg_price'], 0, ',', '.') }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">
                                {{ $vendorData['count'] }} kali
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($rank === 1)
                            <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-bold">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                #{{ $rank }} Terbaik
                            </span>
                            @else
                            <span class="text-gray-600 font-semibold text-lg">#{{ $rank }}</span>
                            @endif
                        </td>
                    </tr>
                    @php $rank++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detailed Price History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-xl font-bold text-gray-800">Detail Histori Harga</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Vendor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Periode Berlaku</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($vendorPrices->sortBy('effective_from') as $vp)
                    <tr class="hover:bg-gray-50 transition-colors {{ $vp->purchase_price === $cheapestPrice ? 'bg-green-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm font-medium text-gray-800">{{ $vp->effective_from->format('d M Y') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                                    {{ substr($vp->vendor->name, 0, 1) }}
                                </div>
                                <span class="ml-2 font-medium text-gray-800">{{ $vp->vendor->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <p class="text-lg font-bold text-gray-800">
                                    Rp {{ number_format($vp->purchase_price, 0, ',', '.') }}
                                </p>
                                @if($vp->purchase_price === $cheapestPrice)
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">
                                    TERMURAH
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $vp->effective_from->format('d M Y') }} - 
                            {{ $vp->effective_to ? $vp->effective_to->format('d M Y') : 'Sekarang' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $vp->notes ?: '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('priceComparisonChart').getContext('2d');
    
    // Prepare data for chart
    const chartData = @json($chartData);
    
    // Group by vendor
    const vendorData = {};
    chartData.forEach(item => {
        if (!vendorData[item.vendor]) {
            vendorData[item.vendor] = {
                label: item.vendor,
                data: [],
                borderColor: getRandomColor(),
                backgroundColor: 'transparent',
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7
            };
        }
        vendorData[item.vendor].data.push({
            x: item.date,
            y: item.price
        });
    });

    const datasets = Object.values(vendorData);

    new Chart(ctx, {
        type: 'line',
        data: {
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 12,
                            weight: 'bold'
                        },
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + 
                                   context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    type: 'category',
                    title: {
                        display: true,
                        text: 'Tanggal',
                        font: {
                            weight: 'bold'
                        }
                    },
                    grid: {
                        display: false
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Harga (Rp)',
                        font: {
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
});

function getRandomColor() {
    const colors = [
        'rgb(59, 130, 246)',  // blue
        'rgb(168, 85, 247)',  // purple
        'rgb(236, 72, 153)',  // pink
        'rgb(34, 197, 94)',   // green
        'rgb(251, 146, 60)',  // orange
        'rgb(239, 68, 68)',   // red
        'rgb(20, 184, 166)',  // teal
        'rgb(245, 158, 11)',  // amber
    ];
    return colors[Math.floor(Math.random() * colors.length)];
}
</script>
@endpush
@endsection
