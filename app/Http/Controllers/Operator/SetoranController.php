<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Setoran;
use App\Models\DetailSetoran;
use App\Models\Nasabah;
use App\Models\KategoriSampah;
use App\Models\Inventaris;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetoranController extends Controller
{
    public function index(Request $request)
    {
        $query = Setoran::with(['nasabah', 'detail.kategori', 'operator'])
            ->latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->whereHas('nasabah', fn($qb) =>
                $qb->where('nama', 'like', "%$q%")
                   ->orWhere('no_rekening', 'like', "%$q%")
            );
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        } else {
            // Default: hari ini
            $query->whereDate('created_at', today());
        }

        $setoran  = $query->paginate(15)->withQueryString();
        $kategori = KategoriSampah::where('is_active', true)->get();

        $statsHariIni = [
            'jumlah' => Setoran::whereDate('created_at', today())->count(),
            'berat'  => Setoran::whereDate('created_at', today())->sum('total_berat'),
            'nilai'  => Setoran::whereDate('created_at', today())->sum('total_nilai'),
        ];

        return view('operator.transaksi-setoran', compact('setoran', 'kategori', 'statsHariIni'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nasabah_id'          => ['required', 'exists:nasabah,id'],
            'tanggal'             => ['required', 'date'],
            'catatan'             => ['nullable', 'string'],
            'items'               => ['required', 'array', 'min:1'],
            'items.*.kategori_id' => ['required', 'exists:kategori_sampah,id'],
            'items.*.berat'       => ['required', 'numeric', 'min:0.01'],
        ], [
            'nasabah_id.required' => 'Nasabah wajib dipilih.',
            'items.required'      => 'Minimal satu item sampah harus diisi.',
            'items.*.berat.min'   => 'Berat harus lebih dari 0.',
        ]);

        DB::transaction(function () use ($request) {
            $nasabah    = Nasabah::findOrFail($request->nasabah_id);
            $totalBerat = 0;
            $totalNilai = 0;
            $details    = [];

            foreach ($request->items as $item) {
                $kategori     = KategoriSampah::findOrFail($item['kategori_id']);
                $berat        = (float) $item['berat'];
                $harga        = $kategori->harga_per_satuan;
                $subtotal     = $berat * $harga;
                $totalBerat  += $berat;
                $totalNilai  += $subtotal;
                $details[]    = [
                    'kategori_id'  => $kategori->id,
                    'berat'        => $berat,
                    'harga_satuan' => $harga,
                    'subtotal'     => $subtotal,
                ];
            }

            // Generate kode setoran
            $kode = 'SET-' . now()->format('Ymd') . '-' . str_pad(
                Setoran::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT
            );

            $setoran = Setoran::create([
                'kode_setoran' => $kode,
                'nasabah_id'   => $nasabah->id,
                'operator_id'  => Auth::id(),
                'total_berat'  => $totalBerat,
                'total_nilai'  => $totalNilai,
                'catatan'      => $request->catatan,
            ]);

            foreach ($details as $d) {
                $setoran->detail()->create($d);

                // Tambah stok inventaris
                Inventaris::where('kategori_id', $d['kategori_id'])
                    ->increment('stok', $d['berat']);
            }

            // Tambah saldo nasabah
            $nasabah->increment('saldo', $totalNilai);

            // Kirim notifikasi ke nasabah
            Notifikasi::untukNasabahId($nasabah->id, [
                'judul' => 'Setoran Berhasil Dicatat',
                'pesan' => "Setoran sampah Anda sebesar " . number_format($totalBerat, 2) . " kg senilai Rp " . number_format($totalNilai, 0, ',', '.') . " telah dicatat. Saldo Anda bertambah.",
                'ikon'  => '⚖️',
                'tipe'  => 'success',
                'url'   => '/nasabah/histori-setoran',
            ]);
        });

        return redirect()->route('operator.transaksi-setoran')
            ->with('success', 'Transaksi setoran berhasil disimpan dan saldo nasabah telah diperbarui.');
    }

    public function destroy(Setoran $setoran)
    {
        DB::transaction(function () use ($setoran) {
            // Kurangi saldo nasabah
            $setoran->nasabah->decrement('saldo', $setoran->total_nilai);

            // Kurangi stok inventaris
            foreach ($setoran->detail as $d) {
                Inventaris::where('kategori_id', $d->kategori_id)
                    ->decrement('stok', $d->berat);
            }

            $setoran->delete();
        });

        return back()->with('success', 'Transaksi berhasil dihapus dan saldo nasabah telah disesuaikan.');
    }
}
