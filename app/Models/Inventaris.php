<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    use HasFactory;

    protected $table = 'inventaris';

    protected $fillable = [
        'kategori_id',
        'stok',
    ];

    protected function casts(): array
    {
        return [
            'stok' => 'decimal:3',
        ];
    }

    // ── Relasi ───────────────────────────────────────────────────────────────

    public function kategori()
    {
        return $this->belongsTo(KategoriSampah::class, 'kategori_id');
    }
}
