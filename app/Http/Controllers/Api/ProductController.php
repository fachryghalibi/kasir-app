<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Search products for POS
     */
    public function search(Request $request)
    {
        $keyword = $request->input('q');
        
        $products = Product::with('category')
            ->active()
            ->when($keyword, function ($query, $keyword) {
                $query->search($keyword);
            })
            ->limit(10)
            ->get();
        
        return response()->json([
            'data' => $products,
        ]);
    }

    /**
     * Get product by barcode
     */
    public function getByBarcode($barcode)
    {
        $product = Product::with('category')
            ->active()
            ->where('barcode', $barcode)
            ->first();
        
        if (!$product) {
            return response()->json([
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }
        
        return response()->json([
            'data' => $product,
        ]);
    }
}