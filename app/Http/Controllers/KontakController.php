<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\PesanKontak;
use Illuminate\Http\Request;

class KontakController extends Controller
{
    public function kirim(Request $request)
    {
        $request->validate([
            'nama'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'subjek'  => ['required', 'string', 'max:150'],
            'pesan'   => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'nama.required'   => 'Nama wajib diisi.',
            'email.required'  => 'Email wajib diisi.',
            'email.email'     => 'Format email tidak valid.',
            'subjek.required' => 'Subjek wajib diisi.',
            'pesan.required'  => 'Pesan wajib diisi.',
            'pesan.min'       => 'Pesan minimal 10 karakter.',
        ]);

        PesanKontak::create([
            'nama'   => $request->nama,
            'email'  => $request->email,
            'subjek' => $request->subjek,
            'pesan'  => $request->pesan,
        ]);

        // Notifikasi ke semua operator
        Notifikasi::untukSemuaOperator([
            'judul' => 'Pesan Kontak Baru',
            'pesan' => $request->nama . " (" . $request->email . ") mengirim pesan: \"" . \Str::limit($request->subjek, 50) . "\"",
            'ikon'  => '✉️',
            'tipe'  => 'info',
            'url'   => '/operator/pesan-masuk',
        ]);

        return back()->with('success', 'Pesan Anda berhasil dikirim! Kami akan segera menghubungi Anda.');
    }
}
