<?php

namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductTierPrice;
use Illuminate\Http\Request;

class ProductTierPriceController extends Controller
{
    /**
     * Show tier prices for a product
     */
    public function index(Product $product)
    {
        $tierPrices = $product->tierPrices()->orderBy('minimum_qty')->get();
        
        return view('boss.products.tier-prices', compact('product', 'tierPrices'));
    }

    /**
     * Store new tier price
     */
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'minimum_qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Check if tier with same minimum_qty already exists
        $exists = $product->tierPrices()
            ->where('minimum_qty', $validated['minimum_qty'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Tier dengan minimum quantity tersebut sudah ada!');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $product->tierPrices()->create($validated);

        return back()->with('success', 'Tier price berhasil ditambahkan!');
    }

    /**
     * Delete tier price
     */
    public function destroy(ProductTierPrice $tierPrice)
    {
        $tierPrice->delete();

        return back()->with('success', 'Tier price berhasil dihapus!');
    }
}