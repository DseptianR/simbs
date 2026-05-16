<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilNasabahController extends Controller
{
    public function index()
    {
        $nasabah = Auth::guard('nasabah')->user();

        $totalBerat  = $nasabah->setoran()->sum('total_berat');
        $jumlahSetor = $nasabah->setoran()->count();
        $setoranTerakhir = $nasabah->setoran()->latest()->first();

        return view('nasabah.profil', compact('nasabah', 'totalBerat', 'jumlahSetor', 'setoranTerakhir'));
    }

    public function update(Request $request)
    {
        $nasabah = Auth::guard('nasabah')->user();

        $request->validate([
            'nama'   => ['required', 'string', 'max:100'],
            'no_hp'  => ['required', 'string', 'max:15'],
            'alamat' => ['nullable', 'string'],
        ], [
            'nama.required'  => 'Nama lengkap wajib diisi.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
        ]);

        $nasabah->update([
            'nama'   => $request->nama,
            'no_hp'  => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function gantiPin(Request $request)
    {
        $nasabah = Auth::guard('nasabah')->user();

        $request->validate([
            'pin_lama'    => ['required', 'digits:6'],
            'pin_baru'    => ['required', 'digits:6'],
            'pin_konfirm' => ['required', 'same:pin_baru'],
        ], [
            'pin_lama.required'    => 'PIN lama wajib diisi.',
            'pin_lama.digits'      => 'PIN harus 6 digit.',
            'pin_baru.required'    => 'PIN baru wajib diisi.',
            'pin_baru.digits'      => 'PIN baru harus 6 digit.',
            'pin_konfirm.same'     => 'Konfirmasi PIN tidak cocok.',
        ]);

        if (! Hash::check($request->pin_lama, $nasabah->pin)) {
            return back()->withErrors(['pin_lama' => 'PIN lama yang Anda masukkan salah.']);
        }

        if ($request->pin_lama === $request->pin_baru) {
            return back()->withErrors(['pin_baru' => 'PIN baru tidak boleh sama dengan PIN lama.']);
        }

        $nasabah->update(['pin' => Hash::make($request->pin_baru)]);

        return back()->with('success_pin', 'PIN berhasil diubah.');
    }
}
