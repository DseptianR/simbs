<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Nasabah extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'nasabah';

    protected $fillable = [
        'no_rekening',
        'nama',
        'nik',
        'no_hp',
        'alamat',
        'pin',
        'saldo',
        'is_active',
        'operator_id',
    ];

    protected $hidden = [
        'pin',
    ];

    protected function casts(): array
    {
        return [
            'saldo'     => 'decimal:2',
            'is_active' => 'boolean',
            'pin'       => 'hashed',
        ];
    }

    // ── Relasi ───────────────────────────────────────────────────────────────

    /** Operator yang mendaftarkan nasabah ini */
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /** Semua setoran milik nasabah ini */
    public function setoran()
    {
        return $this->hasMany(Setoran::class, 'nasabah_id');
    }

    /** Semua permintaan penarikan milik nasabah ini */
    public function penarikan()
    {
        return $this->hasMany(Penarikan::class, 'nasabah_id');
    }
}
