<?php

namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::withCount('productPrices')
            ->latest()
            ->paginate(20);

        return view('boss.vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('boss.vendors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'nullable|string|max:50|unique:vendors,code',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Vendor::create($validated);

        return redirect()->route('boss.vendors.index')
            ->with('success', 'Vendor berhasil ditambahkan!');
    }

    public function edit(Vendor $vendor)
    {
        return view('boss.vendors.edit', compact('vendor'));
    }

 public function show(Vendor $vendor)
{
    $vendor->load(['productPrices.product.category']);
    
    // Filter hanya harga yang masih berlaku (aktif)
    $productsByCategory = $vendor->productPrices()
        ->with(['product.category'])
        ->active() // Tambahkan scope active
        ->get()
        ->filter(function($productPrice) {
            return $productPrice->product !== null;
        })
        ->groupBy(function($productPrice) {
            if ($productPrice->product && $productPrice->product->category) {
                return $productPrice->product->category->name;
            }
            return 'Tanpa Kategori';
        });

    // Hitung total produk unik (bukan total harga)
    $totalUniqueProducts = $vendor->productPrices()
        ->active()
        ->distinct('product_id')
        ->count('product_id');

    return view('boss.vendors.show', compact('vendor', 'productsByCategory', 'totalUniqueProducts'));
}

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'nullable|string|max:50|unique:vendors,code,' . $vendor->id,
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $vendor->update($validated);

        return redirect()->route('boss.vendors.index')
            ->with('success', 'Vendor berhasil diupdate!');
    }

    public function destroy(Vendor $vendor)
    {
        // Check if vendor has price history
        if ($vendor->productPrices()->exists()) {
            return redirect()->route('boss.vendors.index')
                ->with('error', 'Vendor tidak dapat dihapus karena sudah ada histori harga!');
        }

        $vendor->delete();

        return redirect()->route('boss.vendors.index')
            ->with('success', 'Vendor berhasil dihapus!');
    }
}