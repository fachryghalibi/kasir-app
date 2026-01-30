@extends('layouts.app')

@section('title', 'Kategori Cash Flow')

@section('page-title', 'Kategori Cash Flow')
@section('page-description', 'Kelola kategori pemasukan dan pengeluaran')

@section('content')
<div class="space-y-6">
    {{-- Actions --}}
    <div class="flex items-center justify-between">
        <div class="flex gap-3">
            <a href="{{ route('boss.cash-flow-categories.create') }}" 
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Kategori
            </a>
        </div>
    </div>

    {{-- Categories Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Income Categories --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 bg-green-50 border-b border-green-200">
                <h3 class="text-lg font-semibold text-green-900 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                    Kategori Pemasukan
                </h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($categories->where('type', 'income') as $category)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-3 flex-1">
                                {{-- Icon --}}
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center text-white"
                                    style="background-color: {{ $category->formatted_color }}">
                                    <i class="fas {{ $category->getIconWithDefault() }}"></i>
                                </div>
                                
                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-semibold text-gray-900">{{ $category->name }}</h4>
                                        @if(!$category->is_active)
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </div>
                                    @if($category->description)
                                        <p class="text-xs text-gray-600 mt-1">{{ $category->description }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $category->cash_flows_count }} transaksi
                                    </p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 ml-4">
                                <a href="{{ route('boss.cash-flow-categories.edit', $category) }}" 
                                    class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @if($category->cash_flows_count == 0)
                                    <form action="{{ route('boss.cash-flow-categories.destroy', $category) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Yakin hapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <p class="mt-2 text-sm">Belum ada kategori pemasukan</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Expense Categories --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                <h3 class="text-lg font-semibold text-red-900 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                    Kategori Pengeluaran
                </h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($categories->where('type', 'expense') as $category)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-3 flex-1">
                                {{-- Icon --}}
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center text-white"
                                    style="background-color: {{ $category->formatted_color }}">
                                    <i class="fas {{ $category->getIconWithDefault() }}"></i>
                                </div>
                                
                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-semibold text-gray-900">{{ $category->name }}</h4>
                                        @if(!$category->is_active)
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </div>
                                    @if($category->description)
                                        <p class="text-xs text-gray-600 mt-1">{{ $category->description }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $category->cash_flows_count }} transaksi
                                    </p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 ml-4">
                                <a href="{{ route('boss.cash-flow-categories.edit', $category) }}" 
                                    class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @if($category->cash_flows_count == 0)
                                    <form action="{{ route('boss.cash-flow-categories.destroy', $category) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Yakin hapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <p class="mt-2 text-sm">Belum ada kategori pengeluaran</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if($categories->hasPages())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-6 py-4">
            {{ $categories->links() }}
        </div>
    @endif
</div>

{{-- FontAwesome for icons --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush
@endsection