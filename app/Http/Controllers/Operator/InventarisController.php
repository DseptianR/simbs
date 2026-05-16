<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Inventaris;
use App\Models\Penjualan;
use App\Models\KategoriSampah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventarisController extends Controller
{
    public function index(Request $request)
    {
        $inventaris = Inventaris::with('kategori')->get();

        $query = Penjualan::with(['kategori', 'operator'])->latest();

        if ($request->filled('bulan')) {
            [$tahun, $bulan] = explode('-', $request->bulan);
            $query->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun);
        } else {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        }

        $penjualan = $query->paginate(10)->withQueryString();

        $totalPendapatanBulanIni = Penjualan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_pendapatan');

        $kategori = KategoriSampah::where('is_active', true)->with('inventaris')->get();

        return view('operator.inventaris-penjualan', compact(
            'inventaris', 'penjualan', 'totalPendapatanBulanIni', 'kategori'
        ));
    }

    public function storePenjualan(Request $request)
    {
        $request->validate([
            'kategori_id'      => ['required', 'exists:kategori_sampah,id'],
            'nama_pengepul'    => ['required', 'string', 'max:100'],
            'tanggal'          => ['required', 'date'],
            'berat'            => ['required', 'numeric', 'min:0.01'],
            'harga_jual_per_kg'=> ['required', 'numeric', 'min:1'],
            'catatan'          => ['nullable', 'string'],
        ], [
            'kategori_id.required'       => 'Kategori wajib dipilih.',
            'nama_pengepul.required'     => 'Nama pengepul wajib diisi.',
            'berat.required'             => 'Berat wajib diisi.',
            'harga_jual_per_kg.required' => 'Harga jual wajib diisi.',
        ]);

        DB::transaction(function () use ($request) {
            $inventaris = Inventaris::where('kategori_id', $request->kategori_id)->firstOrFail();

            if ($inventaris->stok < $request->berat) {
                throw new \Exception('Stok tidak mencukupi. Stok tersedia: ' . $inventaris->stok . ' kg.');
            }

            $total = $request->berat * $request->harga_jual_per_kg;

            // Generate kode penjualan
            $kode = 'JUAL-' . now()->format('Ymd') . '-' . str_pad(
                Penjualan::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT
            );

            Penjualan::create([
                'kode_penjualan'    => $kode,
                'kategori_id'       => $request->kategori_id,
                'operator_id'       => Auth::id(),
                'berat'             => $request->berat,
                'harga_jual_per_kg' => $request->harga_jual_per_kg,
                'total_pendapatan'  => $total,
                'nama_pengepul'     => $request->nama_pengepul,
                'catatan'           => $request->catatan,
            ]);

            // Kurangi stok
            $inventaris->decrement('stok', $request->berat);
        });

        return redirect()->route('operator.inventaris-penjualan')
            ->with('success', 'Penjualan berhasil dicatat dan stok gudang telah diperbarui.');
    }
}
