<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setoran extends Model
{
    use HasFactory;

    protected $table = 'setoran';

    protected $fillable = [
        'kode_setoran',
        'nasabah_id',
        'operator_id',
        'total_berat',
        'total_nilai',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'total_berat' => 'decimal:3',
            'total_nilai' => 'decimal:2',
        ];
    }

    // ── Relasi ───────────────────────────────────────────────────────────────

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class, 'nasabah_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function detail()
    {
        return $this->hasMany(DetailSetoran::class, 'setoran_id');
    }
}
