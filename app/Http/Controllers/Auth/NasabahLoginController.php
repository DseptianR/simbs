<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class NasabahLoginController extends Controller
{
    /** Tampilkan form login nasabah */
    public function showLoginForm()
    {
        if (Auth::guard('nasabah')->check()) {
            return redirect()->route('nasabah.dashboard');
        }

        return view('nasabah.login', ['title' => 'Login Nasabah']);
    }

    /** Proses login nasabah (no. rekening + PIN) */
    public function login(Request $request)
    {
        $request->validate([
            'no_rekening' => ['required', 'string'],
            'pin'         => ['required', 'digits:6'],
        ], [
            'no_rekening.required' => 'Nomor rekening wajib diisi.',
            'pin.required'         => 'PIN wajib diisi.',
            'pin.digits'           => 'PIN harus 6 digit angka.',
        ]);

        $nasabah = Nasabah::where('no_rekening', $request->no_rekening)->first();

        if (! $nasabah || ! Hash::check($request->pin, $nasabah->pin)) {
            return back()->withErrors([
                'no_rekening' => 'Nomor rekening atau PIN salah.',
            ])->withInput($request->only('no_rekening'));
        }

        if (! $nasabah->is_active) {
            return back()->withErrors([
                'no_rekening' => 'Akun Anda telah dinonaktifkan. Hubungi operator.',
            ])->withInput($request->only('no_rekening'));
        }

        Auth::guard('nasabah')->login($nasabah);
        $request->session()->regenerate();

        return redirect()->intended(route('nasabah.dashboard'));
    }

    /** Logout nasabah */
    public function logout(Request $request)
    {
        Auth::guard('nasabah')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
