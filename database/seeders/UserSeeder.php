<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole('Admin');

        $akuntan = User::factory()->create([
            'name' => 'Akuntan',
            'email' => 'akuntan@example.com',
        ]);
        $akuntan->assignRole('Akuntan');

        $karyawanGudang = User::factory()->create([
            'name' => 'Karyawan Gudang',
            'email' => 'karyawan@example.com',
        ]);
        $karyawanGudang->assignRole('Karyawan Gudang');

        $users = User::factory()->count(10)->create();
    }
}
