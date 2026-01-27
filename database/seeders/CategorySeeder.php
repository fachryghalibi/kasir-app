<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Makanan',
                'description' => 'Produk makanan dan snack',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Minuman',
                'description' => 'Produk minuman kemasan',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Kebutuhan Dapur',
                'description' => 'Bumbu, minyak, dan kebutuhan memasak',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Perlengkapan Mandi',
                'description' => 'Sabun, shampo, dan toiletries',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Elektronik',
                'description' => 'Peralatan elektronik rumah tangga',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Alat Tulis',
                'description' => 'Buku, pulpen, dan perlengkapan sekolah',
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('✅ Categories created successfully!');
    }
}