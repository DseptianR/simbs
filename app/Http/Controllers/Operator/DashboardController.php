<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use App\Models\Setoran;
use App\Models\Penarikan;
use App\Models\KategoriSampah;
use App\Models\Inventaris;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalNasabah   = Nasabah::count();
        $totalSaldo     = Nasabah::sum('saldo');
        $pendingPenarikan = Penarikan::where('status', 'pending')->count();

        // Setoran bulan ini
        $setoranBulanIni = Setoran::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_berat');

        // 5 setoran terbaru
        $setoranTerbaru = Setoran::with(['nasabah', 'detail.kategori'])
            ->latest()
            ->take(5)
            ->get();

        // Penarikan pending
        $penarikanPending = Penarikan::with('nasabah')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Harga & stok terkini
        $kategori = KategoriSampah::with('inventaris')
            ->where('is_active', true)
            ->get();

        // Data chart: volume setoran 6 bulan terakhir per kategori
        $chartLabels = [];
        $chartData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $chartLabels[] = $bulan->translatedFormat('M');
        }

        $topKategori = KategoriSampah::where('is_active', true)->take(3)->get();
        foreach ($topKategori as $kat) {
            $data = [];
            for ($i = 5; $i >= 0; $i--) {
                $bulan = now()->subMonths($i);
                $total = DB::table('detail_setoran')
                    ->join('setoran', 'detail_setoran.setoran_id', '=', 'setoran.id')
                    ->where('detail_setoran.kategori_id', $kat->id)
                    ->whereMonth('setoran.created_at', $bulan->month)
                    ->whereYear('setoran.created_at', $bulan->year)
                    ->sum('detail_setoran.berat');
                $data[] = round($total, 2);
            }
            $chartData[] = ['label' => $kat->nama, 'data' => $data];
        }

        // Komposisi sampah (donut)
        $komposisi = DB::table('detail_setoran')
            ->join('kategori_sampah', 'detail_setoran.kategori_id', '=', 'kategori_sampah.id')
            ->select('kategori_sampah.nama', DB::raw('SUM(detail_setoran.berat) as total'))
            ->groupBy('kategori_sampah.id', 'kategori_sampah.nama')
            ->orderByDesc('total')
            ->get();

        return view('operator.dashboard', compact(
            'totalNasabah', 'totalSaldo', 'pendingPenarikan',
            'setoranBulanIni', 'setoranTerbaru', 'penarikanPending',
            'kategori', 'chartLabels', 'chartData', 'komposisi'
        ));
    }
}
