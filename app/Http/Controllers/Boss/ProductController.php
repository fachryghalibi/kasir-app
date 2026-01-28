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
        $vendors = \App\Models\Vendor::active()->orderBy('name')->get(); // Tambah ini
        
        return view('boss.products.create', compact('categories', 'vendors')); // Update ini
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:200',
        'category_id' => 'nullable|exists:categories,id',
        'sku' => 'nullable|string|max:50|unique:products,sku',
        'barcode' => 'nullable|string|max:50|unique:products,barcode',
        'description' => 'nullable|string',
        'purchase_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'min_stock' => 'required|integer|min:0',
        'unit' => 'required|string|max:20',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        
        // Validasi untuk vendor (TAMBAHAN BARU)
        'vendors' => 'nullable|array',
        'vendors.*.vendor_id' => 'required|exists:vendors,id',
        'vendors.*.vendor_price' => 'required|numeric|min:0',
        'vendors.*.effective_from' => 'required|date',
        'vendors.*.notes' => 'nullable|string|max:500',
    ], [
        'name.required' => 'Nama produk wajib diisi',
        'selling_price.required' => 'Harga jual wajib diisi',
        'stock.required' => 'Stok wajib diisi',
        'vendors.*.vendor_id.required' => 'Vendor wajib dipilih',
        'vendors.*.vendor_price.required' => 'Harga vendor wajib diisi',
        'vendors.*.effective_from.required' => 'Tanggal mulai berlaku wajib diisi',
    ]);

    $validated['created_by'] = auth()->id();
    $validated['is_active'] = $request->boolean('is_active', true);
    $validated['is_featured'] = $request->boolean('is_featured', false);

    // Create product
    $product = Product::create($validated);

    // Save vendor prices (TAMBAHAN BARU)
    if ($request->has('vendors') && is_array($request->vendors)) {
        foreach ($request->vendors as $vendorData) {
            $product->vendorPrices()->create([
                'vendor_id' => $vendorData['vendor_id'],
                'purchase_price' => $vendorData['vendor_price'],
                'effective_from' => $vendorData['effective_from'],
                'effective_to' => null, // Masih berlaku
                'notes' => $vendorData['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);
        }
    }

    return redirect()->route('boss.products.index')
        ->with('success', 'Produk berhasil ditambahkan dengan ' . count($request->vendors ?? []) . ' vendor!');
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