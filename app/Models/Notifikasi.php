<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'penerima_type',
        'penerima_id',
        'judul',
        'pesan',
        'ikon',
        'tipe',
        'url',
        'dibaca_at',
    ];

    protected function casts(): array
    {
        return [
            'dibaca_at' => 'datetime',
        ];
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeBelumDibaca($query)
    {
        return $query->whereNull('dibaca_at');
    }

    public function scopeUntukOperator($query, int $id)
    {
        return $query->where('penerima_type', 'operator')->where('penerima_id', $id);
    }

    public function scopeUntukNasabah($query, int $id)
    {
        return $query->where('penerima_type', 'nasabah')->where('penerima_id', $id);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function sudahDibaca(): bool
    {
        return ! is_null($this->dibaca_at);
    }

    public function warnaTipe(): string
    {
        return match($this->tipe) {
            'success' => 'var(--green-100)',
            'warning' => 'var(--accent-light)',
            'danger'  => 'var(--red-light)',
            default   => 'var(--blue-light)',
        };
    }

    public function warnaIkon(): string
    {
        return match($this->tipe) {
            'success' => 'var(--green-700)',
            'warning' => '#92400e',
            'danger'  => 'var(--red)',
            default   => '#1d4ed8',
        };
    }

    // ── Static helpers ────────────────────────────────────────────────────────

    /** Kirim notifikasi ke semua operator */
    public static function untukSemuaOperator(array $data): void
    {
        $operators = User::where('is_active', true)->get();
        foreach ($operators as $op) {
            static::create(array_merge($data, [
                'penerima_type' => 'operator',
                'penerima_id'   => $op->id,
            ]));
        }
    }

    /** Kirim notifikasi ke satu nasabah */
    public static function untukNasabahId(int $nasabahId, array $data): void
    {
        static::create(array_merge($data, [
            'penerima_type' => 'nasabah',
            'penerima_id'   => $nasabahId,
        ]));
    }
}
