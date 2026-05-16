<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $guard = $request->route()->getPrefix() === '/nasabah' ? 'nasabah' : 'web';
        $user  = Auth::guard($guard)->user();

        $query = Notifikasi::query();

        if ($guard === 'nasabah') {
            $query->untukNasabah($user->id);
        } else {
            $query->untukOperator($user->id);
        }

        $notifikasi = $query->latest()->paginate(20);

        return view('notifikasi.index', compact('notifikasi', 'guard'));
    }

    public function tandaiDibaca(Request $request, Notifikasi $notifikasi)
    {
        $guard = $request->route()->getPrefix() === '/nasabah' ? 'nasabah' : 'web';
        $user  = Auth::guard($guard)->user();

        // Pastikan notifikasi milik user yang login
        $penerima_type = $guard === 'nasabah' ? 'nasabah' : 'operator';
        if ($notifikasi->penerima_type !== $penerima_type || $notifikasi->penerima_id !== $user->id) {
            abort(403);
        }

        $notifikasi->update(['dibaca_at' => now()]);

        if ($notifikasi->url) {
            return redirect($notifikasi->url);
        }

        return back();
    }

    public function tandaiSemuaDibaca(Request $request)
    {
        $guard = $request->route()->getPrefix() === '/nasabah' ? 'nasabah' : 'web';
        $user  = Auth::guard($guard)->user();

        if ($guard === 'nasabah') {
            Notifikasi::untukNasabah($user->id)->belumDibaca()->update(['dibaca_at' => now()]);
        } else {
            Notifikasi::untukOperator($user->id)->belumDibaca()->update(['dibaca_at' => now()]);
        }

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
