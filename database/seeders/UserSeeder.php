<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Operator 1
        User::create([
            'name'      => 'Operator Bank Sampah',
            'email'     => 'operator@banksampah.id',
            'password'  => Hash::make('operator123'),
            'is_active' => true,
        ]);

        // Operator 2
        User::create([
            'name'      => 'Siti Nurhaliza',
            'email'     => 'siti@banksampah.id',
            'password'  => Hash::make('operator123'),
            'is_active' => true,
        ]);
    }
}
