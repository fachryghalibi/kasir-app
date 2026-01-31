<?php

namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index()
    {
        $categories = Category::withCount('products')
            ->latest()
            ->paginate(20);

        return view('boss.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('boss.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'slug' => 'nullable|string|max:100|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ], [
            'name.required' => 'Nama kategori wajib diisi',
            'name.unique' => 'Nama kategori sudah digunakan',
            'slug.unique' => 'Slug sudah digunakan',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            
            // Check if generated slug exists, if yes add number suffix
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Category::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count;
                $count++;
            }
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        Category::create($validated);

        return redirect()->route('boss.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Display the specified category with its products
     */
    public function show(Category $category)
    {
        // Get products in this category
        $query = $category->products();

        // Apply search filter
        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if (request('status') === 'active') {
            $query->where('is_active', true);
        } elseif (request('status') === 'inactive') {
            $query->where('is_active', false);
        }

        // Apply stock filter
        if (request('stock') === 'in_stock') {
            $query->where('stock', '>', 10);
        } elseif (request('stock') === 'low_stock') {
            $query->where('stock', '>', 0)->where('stock', '<=', 10);
        } elseif (request('stock') === 'out_of_stock') {
            $query->where('stock', '<=', 0);
        }

        // Get paginated products
        $products = $query->orderBy('name')->paginate(12)->withQueryString();

        return view('boss.categories.show', compact('category', 'products'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('boss.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'slug' => 'nullable|string|max:100|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ], [
            'name.required' => 'Nama kategori wajib diisi',
            'name.unique' => 'Nama kategori sudah digunakan',
            'slug.unique' => 'Slug sudah digunakan',
        ]);

        // Generate slug if not provided or changed
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            
            // Check if generated slug exists (excluding current category)
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Category::where('slug', $validated['slug'])->where('id', '!=', $category->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count;
                $count++;
            }
        }

        // Prevent category from being its own parent
        if (isset($validated['parent_id']) && $validated['parent_id'] == $category->id) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Kategori tidak boleh menjadi parent dari dirinya sendiri!');
        }

        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        return redirect()->route('boss.categories.index')
            ->with('success', 'Kategori berhasil diupdate!');
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->exists()) {
            return redirect()->route('boss.categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih ada produk!');
        }

        // Check if category has children
        if ($category->children()->exists()) {
            return redirect()->route('boss.categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih ada sub-kategori!');
        }

        $category->delete();

        return redirect()->route('boss.categories.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }

    /**
     * Search categories (AJAX endpoint)
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        
        $categories = Category::where('name', 'like', "%{$search}%")
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'slug']);

        return response()->json($categories);
    }

    /**
     * Toggle category status
     */
    public function toggleStatus(Category $category)
    {
        $category->update([
            'is_active' => !$category->is_active
        ]);

        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Kategori berhasil {$status}!");
    }

    /**
     * Bulk delete categories
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $deletedCount = 0;
        $errors = [];

        foreach ($validated['category_ids'] as $categoryId) {
            $category = Category::find($categoryId);
            
            if (!$category) {
                continue;
            }

            // Check constraints
            if ($category->products()->exists()) {
                $errors[] = "Kategori '{$category->name}' masih memiliki produk";
                continue;
            }

            if ($category->children()->exists()) {
                $errors[] = "Kategori '{$category->name}' masih memiliki sub-kategori";
                continue;
            }

            $category->delete();
            $deletedCount++;
        }

        if ($deletedCount > 0) {
            $message = "{$deletedCount} kategori berhasil dihapus!";
            
            if (!empty($errors)) {
                $message .= ' Namun ada yang gagal: ' . implode(', ', $errors);
                return redirect()->route('boss.categories.index')
                    ->with('warning', $message);
            }

            return redirect()->route('boss.categories.index')
                ->with('success', $message);
        }

        return redirect()->route('boss.categories.index')
            ->with('error', 'Tidak ada kategori yang dapat dihapus. ' . implode(', ', $errors));
    }
}