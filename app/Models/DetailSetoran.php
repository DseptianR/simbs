<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailSetoran extends Model
{
    use HasFactory;

    protected $table = 'detail_setoran';

    protected $fillable = [
        'setoran_id',
        'kategori_id',
        'berat',
        'harga_satuan',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'berat'        => 'decimal:3',
            'harga_satuan' => 'decimal:2',
            'subtotal'     => 'decimal:2',
        ];
    }

    // ── Relasi ───────────────────────────────────────────────────────────────

    public function setoran()
    {
        return $this->belongsTo(Setoran::class, 'setoran_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriSampah::class, 'kategori_id');
    }
}
