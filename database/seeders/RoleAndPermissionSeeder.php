<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions if they don't exist
        $permissions = [
            'mengelola user',
            'mengelola role',
            'mengelola kategori',
            'mengelola produk',
            'mengelola transaksi stok',
            'melihat produk',
            'melihat transaksi stok',
            'mencetak laporan',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions([
            'mengelola user',
            'mengelola role',
            'mengelola kategori',
            'mengelola produk',
            'mengelola transaksi stok',
            'melihat produk',
            'melihat transaksi stok',
            'mencetak laporan',
        ]);

        // Create role for Akuntan
        $akuntan = Role::firstOrCreate(['name' => 'Akuntan']);
        $akuntan->syncPermissions([
            'mengelola produk',
            'melihat produk',
            'melihat transaksi stok',
            'mencetak laporan',
        ]);

        // Create role for Karyawan Gudang
        $karyawanGudang = Role::firstOrCreate(['name' => 'Karyawan Gudang']);
        $karyawanGudang->syncPermissions([
            'melihat produk',
            'mengelola transaksi stok',
            'melihat transaksi stok',
        ]);
    }
}
