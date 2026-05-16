<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penarikan extends Model
{
    use HasFactory;

    protected $table = 'penarikan';

    protected $fillable = [
        'kode_penarikan',
        'nasabah_id',
        'jumlah',
        'status',
        'catatan_nasabah',
        'catatan_operator',
        'divalidasi_oleh',
        'divalidasi_at',
    ];

    protected function casts(): array
    {
        return [
            'jumlah'        => 'decimal:2',
            'divalidasi_at' => 'datetime',
        ];
    }

    // ── Relasi ───────────────────────────────────────────────────────────────

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class, 'nasabah_id');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDisetujui(): bool
    {
        return $this->status === 'disetujui';
    }
}
