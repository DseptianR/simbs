<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $fillable = [
        'kode_penjualan',
        'kategori_id',
        'operator_id',
        'berat',
        'harga_jual_per_kg',
        'total_pendapatan',
        'nama_pengepul',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'berat'             => 'decimal:3',
            'harga_jual_per_kg' => 'decimal:2',
            'total_pendapatan'  => 'decimal:2',
        ];
    }

    // ── Relasi ───────────────────────────────────────────────────────────────

    public function kategori()
    {
        return $this->belongsTo(KategoriSampah::class, 'kategori_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
