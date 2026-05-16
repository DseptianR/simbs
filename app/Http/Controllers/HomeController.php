<?php

namespace App\Http\Controllers;

use App\Models\KategoriSampah;
use App\Models\Nasabah;
use App\Models\Setoran;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Statistik real dari database
        $stats = [
            'sampah_terkumpul' => (int) \App\Models\Setoran::sum('total_berat'),
            'nasabah_aktif'    => Nasabah::where('is_active', true)->count(),
            'total_tabungan'   => round(Nasabah::sum('saldo') / 1000000, 1),
            'titik_pengumpulan'=> 12,
        ];

        return view('index', compact('stats'));
    }

    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }
}
