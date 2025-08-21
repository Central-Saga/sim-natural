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
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'status' => 'active',
            ]
        );

        // $akuntan = User::factory()->create([
        //     'name' => 'Akuntan',
        //     'email' => 'akuntan@example.com',
        //     'status' => 'active',
        // ]);
        // $akuntan->assignRole('Akuntan');

        // $karyawanGudang = User::factory()->create([
        //     'name' => 'Karyawan Gudang',
        //     'email' => 'karyawan@example.com',
        //     'status' => 'active',
        // ]);
        // $karyawanGudang->assignRole('Karyawan Gudang');

        // $users = User::factory()->count(10)->create();
    }
}
