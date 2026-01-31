<?php

namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\CashFlow;
use App\Models\CashFlowCategory;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'out') {
                // Stock habis (0)
                $query->where('stock', 0);
            } elseif ($request->stock_status === 'low') {
                // Stock rendah (menggunakan scope lowStock())
                $query->lowStock();
            } elseif ($request->stock_status === 'normal') {
                // Stock normal (stock > min_stock)
                $query->where('stock', '>', \DB::raw('min_stock'));
        }
}

        $products = $query->latest()->paginate(20);
        $categories = Category::active()->orderBy('name')->get();

        return view('boss.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $vendors = \App\Models\Vendor::active()->orderBy('name')->get();
        
        return view('boss.products.create', compact('categories', 'vendors'));
    }

    /**
     * Search products for autocomplete (AJAX)
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            // Minimum 2 characters
            if (strlen($query) < 2) {
                return response()->json([]);
            }

            $products = Product::where('name', 'LIKE', "%{$query}%")
                ->orWhere('sku', 'LIKE', "%{$query}%")
                ->orWhere('barcode', 'LIKE', "%{$query}%")
                ->with(['category', 'vendorPrices.vendor'])
                ->limit(10)
                ->get()
                ->map(function($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'barcode' => $product->barcode,
                        'unit' => $product->unit,
                        'stock' => $product->stock,
                        'purchase_price' => $product->purchase_price,
                        'selling_price' => $product->selling_price,
                        'min_stock' => $product->min_stock,
                        'category_id' => $product->category_id,
                        'description' => $product->description,
                        'vendors' => $product->vendorPrices->map(function($vp) {
                            return [
                                'vendor_id' => $vp->vendor_id,
                                'vendor_name' => $vp->vendor->name ?? '-',
                                'price' => $vp->purchase_price,
                                'quantity' => $vp->quantity,
                                'effective_from' => $vp->effective_from,
                            ];
                        })
                    ];
                });

            return response()->json($products);
            
        } catch (\Exception $e) {
            \Log::error('Product search error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat mencari produk',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique slug for product
     */
    private function generateUniqueSlug($name, $excludeId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        // Check if slug exists
        while (true) {
            $query = Product::where('slug', $slug);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            if (!$query->exists()) {
                break;
            }
            
            // Append counter to make it unique
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        // Cek mode input
        $isVendorMode = $request->has('vendors') && is_array($request->vendors) && count($request->vendors) > 0;
        
        // Base validation rules
        $rules = [
            'product_id' => 'nullable|exists:products,id',
            'name' => 'required|string|max:200',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|string|max:50',
            'barcode' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'stock' => 'required|integer|min:0',
        ];
        
        $messages = [
            'name.required' => 'Nama produk wajib diisi',
            'purchase_price.required' => 'Harga beli wajib diisi',
            'selling_price.required' => 'Harga jual wajib diisi',
            'stock.required' => 'Stock wajib diisi',
        ];
        
        // Jika vendor mode, tambahkan validasi vendor
        if ($isVendorMode) {
            $rules['vendors'] = 'required|array|min:1';
            $rules['vendors.*.vendor_id'] = 'required|exists:vendors,id';
            $rules['vendors.*.quantity'] = 'required|integer|min:1';
            $rules['vendors.*.vendor_price'] = 'required|numeric|min:0';
            $rules['vendors.*.effective_from'] = 'required|date';
            $rules['vendors.*.notes'] = 'nullable|string|max:500';
            
            $messages['vendors.required'] = 'Minimal 1 vendor harus ditambahkan';
            $messages['vendors.*.vendor_id.required'] = 'Vendor wajib dipilih';
            $messages['vendors.*.quantity.required'] = 'Jumlah wajib diisi';
            $messages['vendors.*.vendor_price.required'] = 'Harga vendor wajib diisi';
            $messages['vendors.*.effective_from.required'] = 'Tanggal mulai berlaku wajib diisi';
        }
        
        $validated = $request->validate($rules, $messages);

        // ===== AUTO-DETECT EXISTING PRODUCT =====
        $existingProduct = null;
        
        if ($request->filled('product_id')) {
            $existingProduct = Product::findOrFail($request->product_id);
        } else {
            $existingProduct = Product::where('name', $validated['name'])->first();
        }

        // ✅ SIMPAN stock_before untuk stock log
        $stockBefore = $existingProduct ? $existingProduct->stock : 0;
        
        // ===== VARIABLE UNTUK TRACKING =====
        $addedStock = $validated['stock'];
        $isNewProduct = !$existingProduct;

        // ===== CREATE OR UPDATE PRODUCT =====
        if ($existingProduct) {
            $product = $existingProduct;
            
            $product->update([
                'category_id' => $validated['category_id'],
                'purchase_price' => $validated['purchase_price'],
                'selling_price' => $validated['selling_price'],
                'min_stock' => $validated['min_stock'],
                'description' => $validated['description'] ?? $product->description,
                'is_active' => $request->boolean('is_active', true),
                'is_featured' => $request->boolean('is_featured', false),
                'updated_by' => auth()->id(),
            ]);

            $product->increment('stock', $addedStock);
            
            $message = 'Stock produk "' . $product->name . '" berhasil ditambahkan (+' . $addedStock . ' ' . $product->unit . ')';
            
        } else {
            $validated['created_by'] = auth()->id();
            $validated['is_active'] = $request->boolean('is_active', true);
            $validated['is_featured'] = $request->boolean('is_featured', false);
            $validated['slug'] = $this->generateUniqueSlug($validated['name']);
            
            $product = Product::create($validated);
            
            $message = 'Produk baru "' . $product->name . '" berhasil ditambahkan!';
        }

        // ===== SIMPAN VENDOR PRICES & CREATE CASH FLOW =====
        if ($isVendorMode && $request->has('vendors')) {
            foreach ($request->vendors as $vendorData) {
                // ✅ Simpan vendor price DENGAN quantity
                $vendorPrice = $product->vendorPrices()->create([
                    'vendor_id' => $vendorData['vendor_id'],
                    'purchase_price' => $vendorData['vendor_price'],
                    'quantity' => $vendorData['quantity'],
                    'effective_from' => $vendorData['effective_from'],
                    'effective_to' => null,
                    'notes' => $vendorData['notes'] ?? null,
                    'created_by' => auth()->id(),
                ]);
                
                // ✅ CREATE CASH FLOW untuk setiap vendor
                $vendorPrice->createCashFlow();
            }
            
            $vendorCount = count($request->vendors);
            $message .= " (Tracking harga dari {$vendorCount} vendor)";
        } else {
            // ===== MODE MANUAL: CREATE CASH FLOW LANGSUNG =====
            $category = CashFlowCategory::firstOrCreate(
                [
                    'name' => 'Pembelian Stock',
                    'type' => 'expense',
                ],
                [
                    'description' => 'Pengeluaran untuk pembelian stock produk',
                    'is_active' => true,
                    'sort_order' => 0,
                ]
            );

            $totalExpense = $addedStock * $validated['purchase_price'];

            CashFlow::create([
                'type' => 'expense',
                'source' => 'purchase',
                'cash_flow_category_id' => $category->id,
                'amount' => $totalExpense,
                'description' => sprintf(
                    'Pembelian stock manual: %s (%d %s) @ Rp %s',
                    $product->name,
                    $addedStock,
                    $product->unit,
                    number_format($validated['purchase_price'], 0, ',', '.')
                ),
                'reference_type' => Product::class,
                'reference_id' => $product->id,
                'transaction_date' => now()->format('Y-m-d'),
                'payment_method' => null,
                'receipt_number' => null,
                'vendor_id' => null,
                'created_by' => auth()->id(),
                'approval_status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        }

        // ===== CREATE STOCK LOG =====
        if (class_exists(StockLog::class)) {
            $product->refresh();
            $stockAfter = $product->stock;
            
            StockLog::create([
                'product_id' => $product->id,
                'type' => 'in',
                'quantity' => $addedStock,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'notes' => $isVendorMode 
                    ? 'Stock awal dari ' . count($request->vendors ?? []) . ' vendor' 
                    : 'Input stock manual',
                'user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('boss.products.index')
            ->with('success', $message);
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        $product->load('category', 'stockLogs.user', 'creator', 'updater');
        
        return view('boss.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->orderBy('name')->get();
        
        return view('boss.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|string|max:50|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $product->id,
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        // ✅ TRACK STOCK CHANGES
        $oldStock = $product->stock;
        $newStock = $validated['stock'];
        $stockDifference = $newStock - $oldStock;
        
        // ✅ TRACK PRICE CHANGES
        $oldPurchasePrice = $product->purchase_price;
        $newPurchasePrice = $validated['purchase_price'];

        $validated['updated_by'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        
        // Update slug if name changed
        if ($validated['name'] !== $product->name) {
            $validated['slug'] = $this->generateUniqueSlug($validated['name'], $product->id);
        }

        $product->update($validated);

        // ===== HANDLE STOCK ADJUSTMENT CASH FLOW =====
        if ($stockDifference != 0) {
            $this->handleStockAdjustmentCashFlow(
                $product, 
                $oldStock, 
                $newStock, 
                $stockDifference,
                $newPurchasePrice
            );
        }

        // ===== HANDLE PRICE CHANGE =====
        if ($oldPurchasePrice != $newPurchasePrice && $newStock > 0) {
            // Hitung selisih total value
            $oldTotalValue = $newStock * $oldPurchasePrice;
            $newTotalValue = $newStock * $newPurchasePrice;
            $valueDifference = $newTotalValue - $oldTotalValue;
            
            if ($valueDifference != 0) {
                $this->createPriceAdjustmentCashFlow(
                    $product,
                    $valueDifference,
                    $oldPurchasePrice,
                    $newPurchasePrice,
                    $newStock
                );
            }
        }

        return redirect()->route('boss.products.index')
            ->with('success', 'Produk berhasil diupdate!');
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        // Check if product has transactions
        if ($product->transactionItems()->exists()) {
            return redirect()->route('boss.products.index')
                ->with('error', 'Produk tidak dapat dihapus karena sudah ada transaksi!');
        }

        $product->delete();

        return redirect()->route('boss.products.index')
            ->with('success', 'Produk berhasil dihapus!');
    }

    /**
     * Handle stock adjustment cash flow
     * ✅ LOGIC YANG BENAR:
     * - Stock bertambah = Pengeluaran bertambah (expense positif)
     * - Stock berkurang = Pengeluaran berkurang (expense negatif)
     */
    private function handleStockAdjustmentCashFlow(
        Product $product, 
        int $oldStock, 
        int $newStock, 
        int $stockDifference,
        int $purchasePrice
    )
    {
        // ✅ SEMUA PENYESUAIAN STOCK ADALAH EXPENSE
        $category = CashFlowCategory::firstOrCreate(
            [
                'name' => 'Penyesuaian Stock',
                'type' => 'expense',
            ],
            [
                'description' => 'Penyesuaian/koreksi stock produk (penambahan/pengurangan pengeluaran)',
                'is_active' => true,
                'sort_order' => 99,
            ]
        );

        // Calculate adjustment amount (bisa positif atau negatif)
        // Positif = pengeluaran bertambah (stock naik)
        // Negatif = pengeluaran berkurang (stock turun)
        $adjustmentAmount = $stockDifference * $purchasePrice;

        // Determine description
        if ($stockDifference > 0) {
            $description = sprintf(
                'Penyesuaian stock: %s (tambah %d %s, dari %d → %d) @ Rp %s [Pengeluaran +Rp %s]',
                $product->name,
                abs($stockDifference),
                $product->unit,
                $oldStock,
                $newStock,
                number_format($purchasePrice, 0, ',', '.'),
                number_format(abs($adjustmentAmount), 0, ',', '.')
            );
        } else {
            $description = sprintf(
                'Penyesuaian stock: %s (kurang %d %s, dari %d → %d) @ Rp %s [Pengeluaran -Rp %s]',
                $product->name,
                abs($stockDifference),
                $product->unit,
                $oldStock,
                $newStock,
                number_format($purchasePrice, 0, ',', '.'),
                number_format(abs($adjustmentAmount), 0, ',', '.')
            );
        }

        // Create adjustment cash flow
        // ✅ GUNAKAN amount DENGAN TANDA (positif/negatif)
        CashFlow::create([
            'type' => 'expense',
            'source' => 'adjustment',
            'cash_flow_category_id' => $category->id,
            'amount' => $adjustmentAmount, // Bisa positif atau negatif
            'description' => $description,
            'reference_type' => Product::class,
            'reference_id' => $product->id,
            'transaction_date' => now()->format('Y-m-d'),
            'payment_method' => null,
            'receipt_number' => null,
            'vendor_id' => null,
            'created_by' => auth()->id(),
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Create stock log
        if (class_exists(StockLog::class)) {
            StockLog::create([
                'product_id' => $product->id,
                'type' => $stockDifference > 0 ? 'in' : 'out',
                'quantity' => abs($stockDifference),
                'stock_before' => $oldStock,
                'stock_after' => $newStock,
                'notes' => 'Penyesuaian manual via edit produk',
                'user_id' => auth()->id(),
            ]);
        }
    }

    /**
     * Handle price adjustment cash flow
     * ✅ LOGIC YANG BENAR:
     * - Harga naik = Pengeluaran bertambah (expense positif)
     * - Harga turun = Pengeluaran berkurang (expense negatif)
     */
    private function createPriceAdjustmentCashFlow(
        Product $product,
        int $valueDifference,
        int $oldPrice,
        int $newPrice,
        int $currentStock
    )
    {
        // ✅ SEMUA PENYESUAIAN HARGA ADALAH EXPENSE
        $category = CashFlowCategory::firstOrCreate(
            [
                'name' => 'Penyesuaian Harga Beli',
                'type' => 'expense',
            ],
            [
                'description' => 'Penyesuaian harga beli produk (penambahan/pengurangan pengeluaran)',
                'is_active' => true,
                'sort_order' => 98,
            ]
        );

        $isIncrease = $valueDifference > 0;
        
        $description = sprintf(
            'Penyesuaian harga: %s (stock: %d %s, harga %s dari Rp %s → Rp %s) [Pengeluaran %sRp %s]',
            $product->name,
            $currentStock,
            $product->unit,
            $isIncrease ? 'naik' : 'turun',
            number_format($oldPrice, 0, ',', '.'),
            number_format($newPrice, 0, ',', '.'),
            $isIncrease ? '+' : '-',
            number_format(abs($valueDifference), 0, ',', '.')
        );

        // ✅ GUNAKAN amount DENGAN TANDA (positif/negatif)
        CashFlow::create([
            'type' => 'expense',
            'source' => 'adjustment',
            'cash_flow_category_id' => $category->id,
            'amount' => $valueDifference, // Bisa positif atau negatif
            'description' => $description,
            'reference_type' => Product::class,
            'reference_id' => $product->id,
            'transaction_date' => now()->format('Y-m-d'),
            'payment_method' => null,
            'receipt_number' => null,
            'vendor_id' => null,
            'created_by' => auth()->id(),
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
    }
}