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
        // Cek mode input: vendor mode jika ada data vendors yang valid
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

        // PENTING: Auto-detect existing product by name jika tidak di-set manual
        $existingProduct = null;
        
        if ($request->filled('product_id')) {
            // User explicitly selected existing product
            $existingProduct = Product::findOrFail($request->product_id);
        } else {
            // User TIDAK klik autocomplete, cek apakah produk dengan nama yang sama sudah ada
            $existingProduct = Product::where('name', $validated['name'])->first();
        }

        // Cek: Update existing product atau create new
        if ($existingProduct) {
            // UPDATE EXISTING PRODUCT (Tambah Stock)
            $product = $existingProduct;
            
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

            $message = 'Stock produk "' . $product->name . '" berhasil ditambahkan!';
            
        } else {
            // CREATE NEW PRODUCT
            $validated['created_by'] = auth()->id();
            $validated['is_active'] = $request->boolean('is_active', true);
            $validated['is_featured'] = $request->boolean('is_featured', false);
            
            // Generate unique slug
            $validated['slug'] = $this->generateUniqueSlug($validated['name']);
            
            // Stock dari validated (sudah diisi dari manual atau vendor mode)
            $product = Product::create($validated);
            
            $message = 'Produk baru "' . $product->name . '" berhasil ditambahkan!';
        }

        // Process vendors - HANYA jika vendor mode aktif
        if ($isVendorMode) {
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

            // Update total stock product (jika ada tambahan dari vendor)
            if ($totalStock > 0) {
                $product->increment('stock', $totalStock);
                $message .= ' Total stock bertambah: +' . $totalStock . ' ' . $product->unit;
            }
        } else {
            // Mode manual: langsung update stock dari input
            // Stock sudah di-set di $validated['stock'], tapi untuk existing product perlu di-increment
            if ($existingProduct) {
                $addedStock = $validated['stock'];
                $product->increment('stock', $addedStock);
                $message .= ' Total stock bertambah: +' . $addedStock . ' ' . $product->unit;
            }
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

        $validated['updated_by'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        
        // Update slug if name changed
        if ($validated['name'] !== $product->name) {
            $validated['slug'] = $this->generateUniqueSlug($validated['name'], $product->id);
        }

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