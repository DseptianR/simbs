<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class NasabahController extends Controller
{
    public function index(Request $request)
    {
        $query = Nasabah::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qb) use ($q) {
                $qb->where('nama', 'like', "%$q%")
                   ->orWhere('no_rekening', 'like', "%$q%")
                   ->orWhere('no_hp', 'like', "%$q%");
            });
        }

        $nasabah = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total'    => Nasabah::count(),
            'aktif'    => Nasabah::where('is_active', true)->count(),
            'nonaktif' => Nasabah::where('is_active', false)->count(),
            'baru'     => Nasabah::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)->count(),
        ];

        return view('operator.manajemen-nasabah', compact('nasabah', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'   => ['required', 'string', 'max:100'],
            'no_hp'  => ['required', 'string', 'max:15'],
            'nik'    => ['nullable', 'digits:16', 'unique:nasabah,nik'],
            'alamat' => ['nullable', 'string'],
            'pin'    => ['required', 'digits:6'],
        ], [
            'nama.required'  => 'Nama lengkap wajib diisi.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'nik.digits'     => 'NIK harus 16 digit.',
            'nik.unique'     => 'NIK sudah terdaftar.',
            'pin.required'   => 'PIN wajib diisi.',
            'pin.digits'     => 'PIN harus 6 digit angka.',
        ]);

        // Generate nomor rekening otomatis
        $lastNo = Nasabah::max('id') ?? 0;
        $noRek  = 'BS-' . str_pad($lastNo + 1, 3, '0', STR_PAD_LEFT);

        Nasabah::create([
            'no_rekening' => $noRek,
            'nama'        => $request->nama,
            'no_hp'       => $request->no_hp,
            'nik'         => $request->nik ?: null,
            'alamat'      => $request->alamat,
            'pin'         => Hash::make($request->pin),
            'saldo'       => 0,
            'is_active'   => true,
            'operator_id' => Auth::id(),
        ]);

        // Notifikasi ke semua operator
        Notifikasi::untukSemuaOperator([
            'judul' => 'Nasabah Baru Terdaftar',
            'pesan' => "Nasabah baru " . $request->nama . " telah terdaftar dengan No. Rekening " . $noRek . ".",
            'ikon'  => '👤',
            'tipe'  => 'info',
            'url'   => '/operator/manajemen-nasabah',
        ]);

        return redirect()->route('operator.manajemen-nasabah')
            ->with('success', "Nasabah berhasil didaftarkan dengan No. Rekening: $noRek");
    }

    public function show(Nasabah $nasabah)
    {
        $nasabah->load(['setoran.detail.kategori', 'penarikan']);
        return view('operator.nasabah-detail', compact('nasabah'));
    }

    public function toggleStatus(Nasabah $nasabah)
    {
        $nasabah->update(['is_active' => ! $nasabah->is_active]);
        $status = $nasabah->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Nasabah {$nasabah->nama} berhasil $status.");
    }

    public function destroy(Nasabah $nasabah)
    {
        if ($nasabah->saldo > 0) {
            return back()->with('error', 'Tidak dapat menghapus nasabah yang masih memiliki saldo.');
        }
        $nasabah->delete();
        return back()->with('success', 'Nasabah berhasil dihapus.');
    }

    /** AJAX: cari nasabah untuk form setoran */
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $results = Nasabah::where('is_active', true)
            ->where(function ($qb) use ($q) {
                $qb->where('nama', 'like', "%$q%")
                   ->orWhere('no_rekening', 'like', "%$q%");
            })
            ->select('id', 'no_rekening', 'nama', 'saldo')
            ->take(10)
            ->get();

        return response()->json($results);
    }
}
