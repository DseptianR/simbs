<?php

namespace Database\Seeders;

use App\Models\KategoriSampah;
use App\Models\Inventaris;
use Illuminate\Database\Seeder;

class KategoriSampahSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['nama' => 'Plastik',  'satuan' => 'kg', 'harga_per_satuan' => 2000, 'harga_jual' => 2500, 'ikon' => '🧴', 'deskripsi' => 'Botol, gelas, ember, tas kresek, dan berbagai produk plastik lainnya'],
            ['nama' => 'Kertas',   'satuan' => 'kg', 'harga_per_satuan' => 1500, 'harga_jual' => 2000, 'ikon' => '📰', 'deskripsi' => 'Koran, majalah, buku, dan kertas bekas lainnya yang masih bersih'],
            ['nama' => 'Kardus',   'satuan' => 'kg', 'harga_per_satuan' => 1200, 'harga_jual' => 1800, 'ikon' => '📦', 'deskripsi' => 'Dus bekas, karton, dan bahan kemasan berbahan dasar kertas tebal'],
            ['nama' => 'Logam',    'satuan' => 'kg', 'harga_per_satuan' => 8000, 'harga_jual' => 10000,'ikon' => '🔩', 'deskripsi' => 'Kaleng aluminium, besi, tembaga, dan berbagai jenis logam bekas'],
            ['nama' => 'Kaca',     'satuan' => 'kg', 'harga_per_satuan' => 500,  'harga_jual' => 800,  'ikon' => '🫙', 'deskripsi' => 'Botol kaca, pecahan kaca, dan produk kaca bekas lainnya'],
            ['nama' => 'Elektronik','satuan'=> 'kg', 'harga_per_satuan' => 5000, 'harga_jual' => 7000, 'ikon' => '📱', 'deskripsi' => 'Perangkat elektronik bekas, kabel, dan komponen elektronik'],
        ];

        foreach ($kategori as $data) {
            $k = KategoriSampah::create($data);
            // Buat record inventaris kosong untuk setiap kategori
            Inventaris::create(['kategori_id' => $k->id, 'stok' => 0]);
        }
    }
}
