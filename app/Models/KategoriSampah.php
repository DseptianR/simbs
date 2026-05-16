<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriSampah extends Model
{
    use HasFactory;

    protected $table = 'kategori_sampah';

    protected $fillable = [
        'nama',
        'satuan',
        'harga_per_satuan',
        'harga_jual',
        'ikon',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'harga_per_satuan' => 'decimal:2',
            'harga_jual'       => 'decimal:2',
            'is_active'        => 'boolean',
        ];
    }

    // ── Relasi ───────────────────────────────────────────────────────────────

    public function detailSetoran()
    {
        return $this->hasMany(DetailSetoran::class, 'kategori_id');
    }

    public function inventaris()
    {
        return $this->hasOne(Inventaris::class, 'kategori_id');
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'kategori_id');
    }
}
