<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ── Relasi ───────────────────────────────────────────────────────────────

    public function nasabahTerdaftar()
    {
        return $this->hasMany(Nasabah::class, 'operator_id');
    }

    public function setoran()
    {
        return $this->hasMany(Setoran::class, 'operator_id');
    }

    public function penarikanDivalidasi()
    {
        return $this->hasMany(Penarikan::class, 'divalidasi_oleh');
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'operator_id');
    }
}
