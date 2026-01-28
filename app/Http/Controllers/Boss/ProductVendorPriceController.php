<?php

namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\ProductVendorPrice;
use Illuminate\Http\Request;

class ProductVendorPriceController extends Controller
{
    /**
     * Show vendor prices for a product
     */
    public function index(Product $product)
    {
        $vendorPrices = $product->vendorPrices()
            ->with('vendor', 'creator')
            ->latest()
            ->paginate(20);
        
        $vendors = Vendor::active()->orderBy('name')->get();
        
        return view('boss.products.vendor-prices', compact('product', 'vendorPrices', 'vendors'));
    }

    /**
     * Store new vendor price
     */
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'purchase_price' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'notes' => 'nullable|string',
        ]);

        // Auto-close previous active price dari vendor yang sama
        ProductVendorPrice::where('product_id', $product->id)
            ->where('vendor_id', $validated['vendor_id'])
            ->whereNull('effective_to')
            ->update(['effective_to' => now()->subDay()]);

        $validated['created_by'] = auth()->id();
        $product->vendorPrices()->create($validated);

        return back()->with('success', 'Harga vendor berhasil ditambahkan!');
    }
}