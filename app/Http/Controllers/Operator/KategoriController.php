<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\KategoriSampah;
use App\Models\Inventaris;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = KategoriSampah::with('inventaris')->get();
        return view('operator.manajemen-harga', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'             => ['required', 'string', 'max:50', 'unique:kategori_sampah,nama'],
            'deskripsi'        => ['nullable', 'string'],
            'harga_per_satuan' => ['required', 'numeric', 'min:1'],
            'harga_jual'       => ['required', 'numeric', 'min:1'],
            'ikon'             => ['nullable', 'string', 'max:10'],
        ], [
            'nama.required'             => 'Nama kategori wajib diisi.',
            'nama.unique'               => 'Kategori dengan nama ini sudah ada.',
            'harga_per_satuan.required' => 'Harga beli wajib diisi.',
            'harga_jual.required'       => 'Harga jual wajib diisi.',
        ]);

        $kategori = KategoriSampah::create([
            'nama'             => $request->nama,
            'deskripsi'        => $request->deskripsi,
            'harga_per_satuan' => $request->harga_per_satuan,
            'harga_jual'       => $request->harga_jual,
            'satuan'           => 'kg',
            'ikon'             => $request->ikon,
            'is_active'        => true,
        ]);

        // Buat record inventaris kosong
        Inventaris::create(['kategori_id' => $kategori->id, 'stok' => 0]);

        return redirect()->route('operator.manajemen-harga')
            ->with('success', "Kategori '{$kategori->nama}' berhasil ditambahkan.");
    }

    public function update(Request $request, KategoriSampah $kategori)
    {
        $request->validate([
            'nama'             => ['required', 'string', 'max:50', 'unique:kategori_sampah,nama,' . $kategori->id],
            'deskripsi'        => ['nullable', 'string'],
            'harga_per_satuan' => ['required', 'numeric', 'min:1'],
            'harga_jual'       => ['required', 'numeric', 'min:1'],
            'ikon'             => ['nullable', 'string', 'max:10'],
        ]);

        $kategori->update([
            'nama'             => $request->nama,
            'deskripsi'        => $request->deskripsi,
            'harga_per_satuan' => $request->harga_per_satuan,
            'harga_jual'       => $request->harga_jual,
            'ikon'             => $request->ikon,
        ]);

        return redirect()->route('operator.manajemen-harga')
            ->with('success', "Harga '{$kategori->nama}' berhasil diperbarui.");
    }

    public function toggleStatus(KategoriSampah $kategori)
    {
        $kategori->update(['is_active' => ! $kategori->is_active]);
        $status = $kategori->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Kategori '{$kategori->nama}' berhasil $status.");
    }
}
