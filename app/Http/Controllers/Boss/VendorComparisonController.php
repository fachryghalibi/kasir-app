<?php

namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\ProductVendorPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorComparisonController extends Controller
{
    /**
     * Show vendor comparison form
     */
    public function index()
    {
        $products = Product::active()->orderBy('name')->get();
        
        return view('boss.vendor-comparison.index', compact('products'));
    }

    /**
     * Show comparison result
     */
    public function compare(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        // Get all vendor prices dalam range
        $vendorPrices = ProductVendorPrice::with('vendor')
            ->where('product_id', $product->id)
            ->inDateRange($startDate, $endDate)
            ->orderBy('effective_from')
            ->get();

        if ($vendorPrices->isEmpty()) {
            return back()->with('error', 'Tidak ada data harga vendor pada periode tersebut.');
        }

        // Hitung statistik
        $cheapestPrice = $vendorPrices->min('purchase_price');
        $cheapestVendor = $vendorPrices->where('purchase_price', $cheapestPrice)->first();
        $averagePrice = $vendorPrices->avg('purchase_price');

        // Group by vendor untuk chart
        $pricesByVendor = $vendorPrices->groupBy('vendor_id')->map(function ($prices) {
            return [
                'vendor' => $prices->first()->vendor,
                'prices' => $prices->map(function ($price) {
                    return [
                        'date' => $price->effective_from->format('Y-m-d'),
                        'price' => $price->purchase_price,
                    ];
                }),
                'min_price' => $prices->min('purchase_price'),
                'avg_price' => $prices->avg('purchase_price'),
                'count' => $prices->count(),
            ];
        });

        // Data untuk chart
        $chartData = $vendorPrices->map(function ($price) {
            return [
                'date' => $price->effective_from->format('d M Y'),
                'vendor' => $price->vendor->name,
                'price' => $price->purchase_price,
            ];
        });

        return view('boss.vendor-comparison.result', compact(
            'product',
            'startDate',
            'endDate',
            'vendorPrices',
            'cheapestPrice',
            'cheapestVendor',
            'averagePrice',
            'pricesByVendor',
            'chartData'
        ));
    }
}