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

        // Create permissions
        Permission::create(['name' => 'mengelola user']);
        Permission::create(['name' => 'mengelola role']);
        Permission::create(['name' => 'mengelola kategori']);
        Permission::create(['name' => 'mengelola produk']);
        Permission::create(['name' => 'mengelola transaksi stok']);
        Permission::create(['name' => 'melihat produk']);
        Permission::create(['name' => 'melihat transaksi stok']);
        Permission::create(['name' => 'mencetak laporan']);

        // Create roles and assign permissions
        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo([
            'mengelola user',
            'mengelola role',
            'mengelola kategori',
        ]);

        // Create role for staff
        $staff = Role::create(['name' => 'Akuntan']);
        $staff->givePermissionTo([
            'mengelola produk',
            'melihat transaksi stok',
            'mencetak laporan',
        ]);

        $visitor = Role::create(['name' => 'Karyawan Gudang']);
        $visitor->givePermissionTo([
            'melihat produk',
            'mengelola transaksi stok',
        ]);
    }
}
