<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $boss = User::where('role', 'boss')->first();
        
        $products = [
            // Makanan
            [
                'name' => 'Indomie Goreng',
                'category' => 'Makanan',
                'barcode' => '8992388102012',
                'purchase_price' => 2500,
                'selling_price' => 3500,
                'stock' => 100,
                'min_stock' => 20,
                'unit' => 'pcs',
                'description' => 'Mi instan rasa goreng',
            ],
            [
                'name' => 'Beras Premium 5kg',
                'category' => 'Makanan',
                'barcode' => '8992388102013',
                'purchase_price' => 65000,
                'selling_price' => 75000,
                'stock' => 50,
                'min_stock' => 10,
                'unit' => 'kg',
                'description' => 'Beras premium kualitas terbaik',
            ],
            [
                'name' => 'Telur Ayam 1kg',
                'category' => 'Makanan',
                'barcode' => '8992388102014',
                'purchase_price' => 25000,
                'selling_price' => 30000,
                'stock' => 30,
                'min_stock' => 10,
                'unit' => 'kg',
                'description' => 'Telur ayam negeri segar',
            ],
            
            // Minuman
            [
                'name' => 'Aqua 600ml',
                'category' => 'Minuman',
                'barcode' => '8992388102015',
                'purchase_price' => 3000,
                'selling_price' => 4000,
                'stock' => 200,
                'min_stock' => 50,
                'unit' => 'pcs',
                'description' => 'Air mineral kemasan',
            ],
            [
                'name' => 'Teh Botol Sosro',
                'category' => 'Minuman',
                'barcode' => '8992388102016',
                'purchase_price' => 4000,
                'selling_price' => 5500,
                'stock' => 150,
                'min_stock' => 30,
                'unit' => 'pcs',
                'description' => 'Teh kemasan botol',
            ],
            
            // Kebutuhan Dapur
            [
                'name' => 'Minyak Goreng 2L',
                'category' => 'Kebutuhan Dapur',
                'barcode' => '8992388102017',
                'purchase_price' => 28000,
                'selling_price' => 35000,
                'stock' => 40,
                'min_stock' => 10,
                'unit' => 'botol',
                'description' => 'Minyak goreng kemasan 2 liter',
            ],
            [
                'name' => 'Gula Pasir 1kg',
                'category' => 'Kebutuhan Dapur',
                'barcode' => '8992388102018',
                'purchase_price' => 12000,
                'selling_price' => 15000,
                'stock' => 60,
                'min_stock' => 15,
                'unit' => 'kg',
                'description' => 'Gula pasir kristal putih',
            ],
            
            // Perlengkapan Mandi
            [
                'name' => 'Sabun Lifebuoy',
                'category' => 'Perlengkapan Mandi',
                'barcode' => '8992388102019',
                'purchase_price' => 3500,
                'selling_price' => 5000,
                'stock' => 80,
                'min_stock' => 20,
                'unit' => 'pcs',
                'description' => 'Sabun mandi batangan',
            ],
            [
                'name' => 'Shampo Clear 170ml',
                'category' => 'Perlengkapan Mandi',
                'barcode' => '8992388102020',
                'purchase_price' => 15000,
                'selling_price' => 20000,
                'stock' => 50,
                'min_stock' => 15,
                'unit' => 'pcs',
                'description' => 'Shampo anti ketombe',
            ],
        ];

        foreach ($products as $productData) {
            $category = Category::where('name', $productData['category'])->first();
            
            Product::create([
                'name' => $productData['name'],
                'category_id' => $category?->id,
                'barcode' => $productData['barcode'],
                'purchase_price' => $productData['purchase_price'],
                'selling_price' => $productData['selling_price'],
                'stock' => $productData['stock'],
                'min_stock' => $productData['min_stock'],
                'unit' => $productData['unit'],
                'description' => $productData['description'],
                'is_active' => true,
                'created_by' => $boss->id,
            ]);
        }

        $this->command->info('✅ Products created successfully!');
    }
}