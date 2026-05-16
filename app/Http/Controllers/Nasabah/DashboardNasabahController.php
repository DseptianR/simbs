<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardNasabahController extends Controller
{
    public function index()
    {
        $nasabah = Auth::guard('nasabah')->user();

        // Statistik keseluruhan
        $totalBerat  = $nasabah->setoran()->sum('total_berat');
        $totalNilai  = $nasabah->setoran()->sum('total_nilai');
        $jumlahSetor = $nasabah->setoran()->count();
        $totalCair   = $nasabah->penarikan()
                        ->where('status', 'disetujui')
                        ->sum('jumlah');

        // Setoran bulan ini
        $beratBulanIni = $nasabah->setoran()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_berat');

        $nilaibulanIni = $nasabah->setoran()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_nilai');

        $jumlahBulanIni = $nasabah->setoran()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // 5 setoran terbaru
        $setoranTerbaru = $nasabah->setoran()
            ->with('detail.kategori')
            ->latest()
            ->take(5)
            ->get();

        // Komposisi per kategori
        $komposisi = DB::table('detail_setoran')
            ->join('setoran', 'detail_setoran.setoran_id', '=', 'setoran.id')
            ->join('kategori_sampah', 'detail_setoran.kategori_id', '=', 'kategori_sampah.id')
            ->where('setoran.nasabah_id', $nasabah->id)
            ->select(
                'kategori_sampah.nama',
                'kategori_sampah.ikon',
                DB::raw('SUM(detail_setoran.berat) as total_berat')
            )
            ->groupBy('kategori_sampah.id', 'kategori_sampah.nama', 'kategori_sampah.ikon')
            ->orderByDesc('total_berat')
            ->get();

        // Data chart 6 bulan terakhir
        $chartLabels = [];
        $chartBerat  = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $chartLabels[] = $bulan->translatedFormat('M');
            $chartBerat[]  = (float) $nasabah->setoran()
                ->whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->sum('total_berat');
        }

        return view('nasabah.dashboard', compact(
            'nasabah', 'totalBerat', 'totalNilai', 'jumlahSetor', 'totalCair',
            'beratBulanIni', 'nilaibulanIni', 'jumlahBulanIni',
            'setoranTerbaru', 'komposisi', 'chartLabels', 'chartBerat'
        ));
    }
}
