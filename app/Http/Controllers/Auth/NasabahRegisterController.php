<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class NasabahRegisterController extends Controller
{
    public function showForm()
    {
        // Kalau sudah login, langsung ke dashboard
        if (Auth::guard('nasabah')->check()) {
            return redirect()->route('nasabah.dashboard');
        }

        return view('nasabah.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama'   => ['required', 'string', 'max:100'],
            'no_hp'  => ['required', 'string', 'max:15'],
            'nik'    => ['nullable', 'digits:16', 'unique:nasabah,nik'],
            'alamat' => ['nullable', 'string'],
            'pin'    => ['required', 'digits:6'],
            'pin_confirmation' => ['required', 'same:pin'],
        ], [
            'nama.required'             => 'Nama lengkap wajib diisi.',
            'no_hp.required'            => 'Nomor HP wajib diisi.',
            'nik.digits'                => 'NIK harus 16 digit angka.',
            'nik.unique'                => 'NIK sudah terdaftar.',
            'pin.required'              => 'PIN wajib diisi.',
            'pin.digits'                => 'PIN harus 6 digit angka.',
            'pin_confirmation.required' => 'Konfirmasi PIN wajib diisi.',
            'pin_confirmation.same'     => 'Konfirmasi PIN tidak cocok.',
        ]);

        // Generate nomor rekening otomatis
        $lastId = Nasabah::max('id') ?? 0;
        $noRek  = 'BS-' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);

        $nasabah = Nasabah::create([
            'no_rekening' => $noRek,
            'nama'        => $request->nama,
            'no_hp'       => $request->no_hp,
            'nik'         => $request->nik ?: null,
            'alamat'      => $request->alamat,
            'pin'         => Hash::make($request->pin),
            'saldo'       => 0,
            'is_active'   => true,
            'operator_id' => null,
        ]);

        // Notifikasi ke semua operator
        Notifikasi::untukSemuaOperator([
            'judul' => 'Pendaftaran Nasabah Baru',
            'pesan' => $nasabah->nama . " mendaftar mandiri dengan No. Rekening " . $noRek . ". Silakan verifikasi data.",
            'ikon'  => '👤',
            'tipe'  => 'info',
            'url'   => '/operator/manajemen-nasabah',
        ]);

        // Auto login setelah daftar
        Auth::guard('nasabah')->login($nasabah);
        $request->session()->regenerate();

        return redirect()->route('nasabah.dashboard')
            ->with('success', "Selamat datang, {$nasabah->nama}! Akun Anda berhasil dibuat dengan No. Rekening {$noRek}.");
    }
}
