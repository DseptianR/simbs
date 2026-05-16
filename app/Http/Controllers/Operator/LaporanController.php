<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Setoran;
use App\Models\Penarikan;
use App\Models\Penjualan;
use App\Models\KategoriSampah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Tentukan rentang tanggal
        $dari   = $request->filled('dari')   ? $request->dari   : now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->filled('sampai') ? $request->sampai : now()->format('Y-m-d');

        // Ringkasan
        $totalMasuk = Setoran::whereBetween('created_at', [$dari, $sampai . ' 23:59:59'])
            ->sum('total_berat');

        $totalTerjual = Penjualan::whereBetween('created_at', [$dari, $sampai . ' 23:59:59'])
            ->sum('berat');

        $pendapatan = Penjualan::whereBetween('created_at', [$dari, $sampai . ' 23:59:59'])
            ->sum('total_pendapatan');

        $dibayarNasabah = Setoran::whereBetween('created_at', [$dari, $sampai . ' 23:59:59'])
            ->sum('total_nilai');

        // Rekap setoran
        $setoranList = Setoran::with(['nasabah', 'detail.kategori'])
            ->whereBetween('created_at', [$dari, $sampai . ' 23:59:59'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Rekap per kategori
        $rekapKategori = DB::table('detail_setoran')
            ->join('setoran', 'detail_setoran.setoran_id', '=', 'setoran.id')
            ->join('kategori_sampah', 'detail_setoran.kategori_id', '=', 'kategori_sampah.id')
            ->whereBetween('setoran.created_at', [$dari, $sampai . ' 23:59:59'])
            ->select(
                'kategori_sampah.nama',
                'kategori_sampah.ikon',
                DB::raw('SUM(detail_setoran.berat) as total_berat'),
                DB::raw('SUM(detail_setoran.subtotal) as total_nilai')
            )
            ->groupBy('kategori_sampah.id', 'kategori_sampah.nama', 'kategori_sampah.ikon')
            ->orderByDesc('total_berat')
            ->get();

        // Rekap penjualan per kategori
        $rekapPenjualan = DB::table('penjualan')
            ->join('kategori_sampah', 'penjualan.kategori_id', '=', 'kategori_sampah.id')
            ->whereBetween('penjualan.created_at', [$dari, $sampai . ' 23:59:59'])
            ->select(
                'kategori_sampah.nama',
                'kategori_sampah.ikon',
                DB::raw('SUM(penjualan.berat) as total_berat'),
                DB::raw('SUM(penjualan.total_pendapatan) as total_pendapatan')
            )
            ->groupBy('kategori_sampah.id', 'kategori_sampah.nama', 'kategori_sampah.ikon')
            ->orderByDesc('total_pendapatan')
            ->get();

        // Data chart mingguan (4 minggu dalam rentang)
        $chartLabels  = [];
        $chartMasuk   = [];
        $chartKeluar  = [];
        $chartPemasukan  = [];
        $chartPengeluaran = [];

        for ($i = 3; $i >= 0; $i--) {
            $mulai = now()->subWeeks($i)->startOfWeek()->format('Y-m-d');
            $akhir = now()->subWeeks($i)->endOfWeek()->format('Y-m-d');
            $chartLabels[]     = 'Minggu ' . (4 - $i);
            $chartMasuk[]      = (float) Setoran::whereBetween('created_at', [$mulai, $akhir . ' 23:59:59'])->sum('total_berat');
            $chartKeluar[]     = (float) Penjualan::whereBetween('created_at', [$mulai, $akhir . ' 23:59:59'])->sum('berat');
            $chartPemasukan[]  = (float) Penjualan::whereBetween('created_at', [$mulai, $akhir . ' 23:59:59'])->sum('total_pendapatan');
            $chartPengeluaran[]= (float) Setoran::whereBetween('created_at', [$mulai, $akhir . ' 23:59:59'])->sum('total_nilai');
        }

        return view('operator.laporan', compact(
            'dari', 'sampai',
            'totalMasuk', 'totalTerjual', 'pendapatan', 'dibayarNasabah',
            'setoranList', 'rekapKategori', 'rekapPenjualan',
            'chartLabels', 'chartMasuk', 'chartKeluar', 'chartPemasukan', 'chartPengeluaran'
        ));
    }
}
