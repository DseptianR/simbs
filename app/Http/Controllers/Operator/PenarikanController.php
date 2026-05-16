<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Penarikan;
use App\Models\Nasabah;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenarikanController extends Controller
{
    public function index(Request $request)
    {
        $pending = Penarikan::with('nasabah')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $query = Penarikan::with(['nasabah', 'validator'])->latest();

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        $riwayat = $query->whereIn('status', ['disetujui', 'ditolak'])
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'pending'   => Penarikan::where('status', 'pending')->count(),
            'disetujui' => Penarikan::where('status', 'disetujui')
                            ->whereMonth('created_at', now()->month)->count(),
            'total_cair'=> Penarikan::where('status', 'disetujui')
                            ->whereMonth('created_at', now()->month)->sum('jumlah'),
            'ditolak'   => Penarikan::where('status', 'ditolak')
                            ->whereMonth('created_at', now()->month)->count(),
        ];

        return view('operator.validasi-penarikan', compact('pending', 'riwayat', 'stats'));
    }

    public function setujui(Request $request, Penarikan $penarikan)
    {
        if ($penarikan->status !== 'pending') {
            return back()->with('error', 'Penarikan ini sudah diproses.');
        }

        DB::transaction(function () use ($penarikan, $request) {
            $nasabah = $penarikan->nasabah;

            if ($nasabah->saldo < $penarikan->jumlah) {
                throw new \Exception('Saldo nasabah tidak mencukupi.');
            }

            $nasabah->decrement('saldo', $penarikan->jumlah);

            $penarikan->update([
                'status'            => 'disetujui',
                'catatan_operator'  => $request->catatan_operator,
                'divalidasi_oleh'   => Auth::id(),
                'divalidasi_at'     => now(),
            ]);

            // Notifikasi ke nasabah
            Notifikasi::untukNasabahId($penarikan->nasabah_id, [
                'judul' => 'Penarikan Disetujui ✅',
                'pesan' => "Permintaan penarikan Anda sebesar Rp " . number_format($penarikan->jumlah, 0, ',', '.') . " telah disetujui. Silakan ambil di kantor.",
                'ikon'  => '💸',
                'tipe'  => 'success',
                'url'   => '/nasabah/saldo',
            ]);
        });

        return back()->with('success', "Penarikan {$penarikan->nasabah->nama} sebesar Rp " .
            number_format($penarikan->jumlah, 0, ',', '.') . " berhasil disetujui.");
    }

    public function tolak(Request $request, Penarikan $penarikan)
    {
        if ($penarikan->status !== 'pending') {
            return back()->with('error', 'Penarikan ini sudah diproses.');
        }

        $penarikan->update([
            'status'           => 'ditolak',
            'catatan_operator' => $request->catatan_operator,
            'divalidasi_oleh'  => Auth::id(),
            'divalidasi_at'    => now(),
        ]);

        // Notifikasi ke nasabah
        Notifikasi::untukNasabahId($penarikan->nasabah_id, [
            'judul' => 'Penarikan Ditolak',
            'pesan' => "Permintaan penarikan Anda sebesar Rp " . number_format($penarikan->jumlah, 0, ',', '.') . " ditolak." . ($request->catatan_operator ? " Alasan: " . $request->catatan_operator : ""),
            'ikon'  => '❌',
            'tipe'  => 'danger',
            'url'   => '/nasabah/saldo',
        ]);

        return back()->with('success', "Penarikan {$penarikan->nasabah->nama} berhasil ditolak.");
    }
}
