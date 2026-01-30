<?php

namespace Database\Seeders;

use App\Models\CashFlowCategory;
use Illuminate\Database\Seeder;

class CashFlowCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // EXPENSE CATEGORIES
            [
                'name' => 'Iuran Sampah',
                'type' => 'expense',
                'description' => 'Pembayaran iuran sampah bulanan',
                'icon' => 'fa-trash',
                'color' => '#EF4444',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Listrik',
                'type' => 'expense',
                'description' => 'Pembayaran tagihan listrik',
                'icon' => 'fa-bolt',
                'color' => '#F59E0B',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Air',
                'type' => 'expense',
                'description' => 'Pembayaran tagihan air',
                'icon' => 'fa-water',
                'color' => '#3B82F6',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Gaji Karyawan',
                'type' => 'expense',
                'description' => 'Pembayaran gaji bulanan karyawan',
                'icon' => 'fa-money-bill',
                'color' => '#8B5CF6',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Sewa Toko',
                'type' => 'expense',
                'description' => 'Pembayaran sewa tempat usaha',
                'icon' => 'fa-store',
                'color' => '#EC4899',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Internet & Telepon',
                'type' => 'expense',
                'description' => 'Pembayaran tagihan internet dan telepon',
                'icon' => 'fa-wifi',
                'color' => '#14B8A6',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Perawatan & Perbaikan',
                'type' => 'expense',
                'description' => 'Biaya perawatan dan perbaikan aset',
                'icon' => 'fa-wrench',
                'color' => '#F97316',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Transportasi',
                'type' => 'expense',
                'description' => 'Biaya transportasi dan pengiriman',
                'icon' => 'fa-truck',
                'color' => '#06B6D4',
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Perlengkapan Kantor',
                'type' => 'expense',
                'description' => 'Pembelian perlengkapan kantor',
                'icon' => 'fa-box',
                'color' => '#6366F1',
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Marketing & Promosi',
                'type' => 'expense',
                'description' => 'Biaya marketing dan promosi',
                'icon' => 'fa-bullhorn',
                'color' => '#10B981',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Pajak & Administrasi',
                'type' => 'expense',
                'description' => 'Pembayaran pajak dan biaya administrasi',
                'icon' => 'fa-file-invoice',
                'color' => '#64748B',
                'sort_order' => 11,
                'is_active' => true,
            ],
            [
                'name' => 'Pengeluaran Lainnya',
                'type' => 'expense',
                'description' => 'Pengeluaran yang tidak termasuk kategori lain',
                'icon' => 'fa-ellipsis-h',
                'color' => '#94A3B8',
                'sort_order' => 12,
                'is_active' => true,
            ],

            // INCOME CATEGORIES
            [
                'name' => 'Modal Tambahan',
                'type' => 'income',
                'description' => 'Penambahan modal dari investor atau pemilik',
                'icon' => 'fa-hand-holding-usd',
                'color' => '#10B981',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Pinjaman',
                'type' => 'income',
                'description' => 'Dana dari pinjaman bank atau lembaga keuangan',
                'icon' => 'fa-university',
                'color' => '#3B82F6',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Pendapatan Lain-lain',
                'type' => 'income',
                'description' => 'Pendapatan di luar penjualan produk',
                'icon' => 'fa-coins',
                'color' => '#F59E0B',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            CashFlowCategory::create($category);
        }
    }
}