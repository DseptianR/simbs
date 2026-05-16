<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HistoriSetoranController extends Controller
{
    public function index(Request $request)
    {
        $nasabah = Auth::guard('nasabah')->user();

        $query = $nasabah->setoran()->with('detail.kategori')->latest();

        // Filter bulan
        if ($request->filled('bulan')) {
            [$tahun, $bulan] = explode('-', $request->bulan);
            $query->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun);
        }

        // Filter kategori
        if ($request->filled('kategori_id')) {
            $query->whereHas('detail', fn($q) =>
                $q->where('kategori_id', $request->kategori_id)
            );
        }

        $setoran = $query->paginate(10)->withQueryString();

        // Statistik keseluruhan
        $totalBerat  = $nasabah->setoran()->sum('total_berat');
        $totalNilai  = $nasabah->setoran()->sum('total_nilai');
        $jumlahSetor = $nasabah->setoran()->count();

        // Kategori terbanyak
        $terbanyak = DB::table('detail_setoran')
            ->join('setoran', 'detail_setoran.setoran_id', '=', 'setoran.id')
            ->join('kategori_sampah', 'detail_setoran.kategori_id', '=', 'kategori_sampah.id')
            ->where('setoran.nasabah_id', $nasabah->id)
            ->select('kategori_sampah.nama', DB::raw('SUM(detail_setoran.berat) as total'))
            ->groupBy('kategori_sampah.id', 'kategori_sampah.nama')
            ->orderByDesc('total')
            ->first();

        // Data chart 6 bulan per kategori
        $chartLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $chartLabels[] = now()->subMonths($i)->translatedFormat('M');
        }

        $topKategori = DB::table('detail_setoran')
            ->join('setoran', 'detail_setoran.setoran_id', '=', 'setoran.id')
            ->join('kategori_sampah', 'detail_setoran.kategori_id', '=', 'kategori_sampah.id')
            ->where('setoran.nasabah_id', $nasabah->id)
            ->select('kategori_sampah.id', 'kategori_sampah.nama')
            ->groupBy('kategori_sampah.id', 'kategori_sampah.nama')
            ->orderByDesc(DB::raw('SUM(detail_setoran.berat)'))
            ->take(3)
            ->get();

        $chartDatasets = [];
        $colors = ['rgba(34,135,79,.8)', 'rgba(59,130,246,.8)', 'rgba(245,158,11,.8)'];
        foreach ($topKategori as $idx => $kat) {
            $data = [];
            for ($i = 5; $i >= 0; $i--) {
                $bulan = now()->subMonths($i);
                $data[] = (float) DB::table('detail_setoran')
                    ->join('setoran', 'detail_setoran.setoran_id', '=', 'setoran.id')
                    ->where('setoran.nasabah_id', $nasabah->id)
                    ->where('detail_setoran.kategori_id', $kat->id)
                    ->whereMonth('setoran.created_at', $bulan->month)
                    ->whereYear('setoran.created_at', $bulan->year)
                    ->sum('detail_setoran.berat');
            }
            $chartDatasets[] = [
                'label'           => $kat->nama . ' (kg)',
                'data'            => $data,
                'backgroundColor' => $colors[$idx] ?? 'rgba(107,114,128,.8)',
                'borderRadius'    => 4,
            ];
        }

        // Daftar kategori untuk filter
        $kategoriList = DB::table('detail_setoran')
            ->join('setoran', 'detail_setoran.setoran_id', '=', 'setoran.id')
            ->join('kategori_sampah', 'detail_setoran.kategori_id', '=', 'kategori_sampah.id')
            ->where('setoran.nasabah_id', $nasabah->id)
            ->select('kategori_sampah.id', 'kategori_sampah.nama', 'kategori_sampah.ikon')
            ->groupBy('kategori_sampah.id', 'kategori_sampah.nama', 'kategori_sampah.ikon')
            ->get();

        return view('nasabah.histori-setoran', compact(
            'nasabah', 'setoran', 'totalBerat', 'totalNilai', 'jumlahSetor', 'terbanyak',
            'chartLabels', 'chartDatasets', 'kategoriList'
        ));
    }
}
