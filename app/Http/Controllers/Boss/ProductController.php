<?php

namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
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
            if ($request->stock_status === 'low') {
                $query->lowStock();
            } elseif ($request->stock_status === 'out') {
                $query->where('stock', 0);
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
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
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
            
            'vendors' => 'required|array|min:1',
            'vendors.*.vendor_id' => 'required|exists:vendors,id',
            'vendors.*.quantity' => 'required|integer|min:1',
            'vendors.*.vendor_price' => 'required|numeric|min:0',
            'vendors.*.effective_from' => 'required|date',
            'vendors.*.notes' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama produk wajib diisi',
            'purchase_price.required' => 'Harga beli wajib diisi',
            'selling_price.required' => 'Harga jual wajib diisi',
            'vendors.required' => 'Minimal 1 vendor harus ditambahkan',
            'vendors.*.vendor_id.required' => 'Vendor wajib dipilih',
            'vendors.*.quantity.required' => 'Jumlah wajib diisi',
            'vendors.*.vendor_price.required' => 'Harga vendor wajib diisi',
            'vendors.*.effective_from.required' => 'Tanggal mulai berlaku wajib diisi',
        ]);

        // Cek: Update existing product atau create new
        if ($request->filled('product_id')) {
            // UPDATE EXISTING PRODUCT (Tambah Stock)
            $product = Product::findOrFail($request->product_id);
            
            // Update data product (kategori, harga, dll bisa diubah)
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

            $message = 'Stock produk berhasil ditambahkan!';
            
        } else {
            // CREATE NEW PRODUCT
            $validated['created_by'] = auth()->id();
            $validated['is_active'] = $request->boolean('is_active', true);
            $validated['is_featured'] = $request->boolean('is_featured', false);
            $validated['stock'] = 0; // Initial stock = 0, akan di-increment dari vendor
            
            $product = Product::create($validated);
            
            $message = 'Produk baru berhasil ditambahkan!';
        }

        // Process vendors - SELALU BUAT RECORD BARU (HISTORY)
        $totalStock = 0;
        
        foreach ($request->vendors as $vendorData) {
            // SELALU buat record baru, tidak update yang lama
            $product->vendorPrices()->create([
                'vendor_id' => $vendorData['vendor_id'],
                'purchase_price' => $vendorData['vendor_price'],
                'quantity' => $vendorData['quantity'],
                'effective_from' => $vendorData['effective_from'],
                'effective_to' => null, // Semua record tetap NULL (active)
                'notes' => $vendorData['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);
            
            $totalStock += $vendorData['quantity'];
        }

        // Update total stock product
        $product->increment('stock', $totalStock);

        return redirect()->route('boss.products.index')
            ->with('success', $message . ' Total stock bertambah: +' . $totalStock . ' ' . $product->unit);
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

        $validated['updated_by'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');

        $product->update($validated);

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
}