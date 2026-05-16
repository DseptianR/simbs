<?php

namespace Database\Seeders;

use App\Models\Nasabah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NasabahSeeder extends Seeder
{
    public function run(): void
    {
        $nasabah = [
            [
                'no_rekening' => 'BS-001',
                'nama'        => 'Budi Santoso',
                'nik'         => '3201010101800001',
                'no_hp'       => '081234567890',
                'alamat'      => 'Jl. Mawar No. 10, RT 01/RW 02, Kelurahan Sukamaju',
                'pin'         => Hash::make('123456'),
                'saldo'       => 75000,
                'operator_id' => 1,
            ],
            [
                'no_rekening' => 'BS-002',
                'nama'        => 'Siti Rahayu',
                'nik'         => '3201010101850002',
                'no_hp'       => '082345678901',
                'alamat'      => 'Jl. Melati No. 5, RT 03/RW 01, Kelurahan Harapan',
                'pin'         => Hash::make('123456'),
                'saldo'       => 120000,
                'operator_id' => 1,
            ],
            [
                'no_rekening' => 'BS-003',
                'nama'        => 'Ahmad Fauzi',
                'nik'         => '3201010101900003',
                'no_hp'       => '083456789012',
                'alamat'      => 'Jl. Kenanga No. 22, RT 02/RW 04, Kelurahan Sejahtera',
                'pin'         => Hash::make('123456'),
                'saldo'       => 45000,
                'operator_id' => 2,
            ],
        ];

        foreach ($nasabah as $data) {
            Nasabah::create($data);
        }
    }
}
