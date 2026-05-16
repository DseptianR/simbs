<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\PesanKontak;

class PesanMasukController extends Controller
{
    public function index()
    {
        $pesan = PesanKontak::latest()->paginate(15);
        $belumDibaca = PesanKontak::where('sudah_dibaca', false)->count();

        return view('operator.pesan-masuk', compact('pesan', 'belumDibaca'));
    }

    public function tandaiBaca(PesanKontak $pesan)
    {
        $pesan->update([
            'sudah_dibaca' => true,
            'dibaca_at'    => now(),
        ]);

        return back()->with('success', 'Pesan ditandai sudah dibaca.');
    }
}
