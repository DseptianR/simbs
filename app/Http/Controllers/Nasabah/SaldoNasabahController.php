<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\Penarikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Notifikasi;

class SaldoNasabahController extends Controller
{
    public function index()
    {
        $nasabah = Auth::guard('nasabah')->user();

        // Mutasi: gabungkan setoran (masuk) dan penarikan disetujui (keluar)
        $setoran = $nasabah->setoran()
            ->with('detail.kategori')
            ->latest()
            ->get()
            ->map(fn($s) => [
                'tipe'      => 'masuk',
                'ikon'      => '⚖️',
                'keterangan'=> 'Setoran Sampah',
                'detail'    => $s->detail->map(fn($d) => $d->kategori->nama)->join(', '),
                'jumlah'    => $s->total_nilai,
                'tanggal'   => $s->created_at,
            ]);

        $penarikan = $nasabah->penarikan()
            ->latest()
            ->get()
            ->map(fn($p) => [
                'tipe'      => $p->status === 'disetujui' ? 'keluar' : 'pending',
                'ikon'      => $p->status === 'disetujui' ? '💸' : '⏳',
                'keterangan'=> 'Penarikan Saldo',
                'detail'    => 'Status: ' . ucfirst($p->status),
                'jumlah'    => $p->jumlah,
                'tanggal'   => $p->created_at,
            ]);

        $mutasi = $setoran->concat($penarikan)
            ->sortByDesc('tanggal')
            ->take(20)
            ->values();

        $totalMasuk  = $nasabah->setoran()->sum('total_nilai');
        $totalKeluar = $nasabah->penarikan()->where('status', 'disetujui')->sum('jumlah');

        // Penarikan pending terakhir
        $penarikanPending = $nasabah->penarikan()
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('nasabah.saldo', compact(
            'nasabah', 'mutasi', 'totalMasuk', 'totalKeluar', 'penarikanPending'
        ));
    }

    public function ajukanPenarikan(Request $request)
    {
        $nasabah = Auth::guard('nasabah')->user();

        $request->validate([
            'jumlah' => ['required', 'numeric', 'min:10000'],
            'pin'    => ['required', 'digits:6'],
            'catatan'=> ['nullable', 'string', 'max:200'],
        ], [
            'jumlah.required' => 'Jumlah penarikan wajib diisi.',
            'jumlah.min'      => 'Minimal penarikan Rp 10.000.',
            'pin.required'    => 'PIN konfirmasi wajib diisi.',
            'pin.digits'      => 'PIN harus 6 digit.',
        ]);

        // Verifikasi PIN
        if (! Hash::check($request->pin, $nasabah->pin)) {
            return back()->withErrors(['pin' => 'PIN yang Anda masukkan salah.'])->withInput();
        }

        if ($request->jumlah > $nasabah->saldo) {
            return back()->withErrors(['jumlah' => 'Jumlah penarikan melebihi saldo tersedia.'])->withInput();
        }

        // Cek apakah ada penarikan pending
        if ($nasabah->penarikan()->where('status', 'pending')->exists()) {
            return back()->with('error', 'Anda masih memiliki permintaan penarikan yang sedang diproses.');
        }

        $kode = 'WD-' . now()->format('Ymd') . '-' . str_pad(
            Penarikan::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT
        );

        Penarikan::create([
            'kode_penarikan'  => $kode,
            'nasabah_id'      => $nasabah->id,
            'jumlah'          => $request->jumlah,
            'status'          => 'pending',
            'catatan_nasabah' => $request->catatan,
        ]);

        // Notifikasi ke semua operator
        Notifikasi::untukSemuaOperator([
            'judul' => 'Permintaan Penarikan Baru',
            'pesan' => $nasabah->nama . " (" . $nasabah->no_rekening . ") mengajukan penarikan sebesar Rp " . number_format($request->jumlah, 0, ',', '.') . ". Segera validasi.",
            'ikon'  => '💸',
            'tipe'  => 'warning',
            'url'   => '/operator/validasi-penarikan',
        ]);

        return redirect()->route('nasabah.saldo')
            ->with('success', 'Permintaan penarikan Rp ' . number_format($request->jumlah, 0, ',', '.') . ' berhasil diajukan. Akan diproses dalam 1×24 jam kerja.');
    }
}
