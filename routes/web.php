<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Auth\OperatorLoginController;
use App\Http\Controllers\Auth\NasabahLoginController;
use App\Http\Controllers\Auth\NasabahRegisterController;
use App\Http\Controllers\Operator\DashboardController;
use App\Http\Controllers\Operator\NasabahController;
use App\Http\Controllers\Operator\SetoranController;
use App\Http\Controllers\Operator\KategoriController;
use App\Http\Controllers\Operator\PenarikanController;
use App\Http\Controllers\Operator\InventarisController;
use App\Http\Controllers\Operator\LaporanController;
use App\Http\Controllers\Operator\PesanMasukController;
use App\Http\Controllers\Nasabah\DashboardNasabahController;
use App\Http\Controllers\Nasabah\SaldoNasabahController;
use App\Http\Controllers\Nasabah\HistoriSetoranController;
use App\Http\Controllers\Nasabah\ProfilNasabahController;
use App\Http\Controllers\NotifikasiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ── HALAMAN PUBLIK ────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang', [HomeController::class, 'about'])->name('about');
Route::get('/kontak', [HomeController::class, 'contact'])->name('contact');
Route::post('/kontak/kirim', [KontakController::class, 'kirim'])->name('kontak.kirim');
Route::post('/chatbot', [ChatbotController::class, 'chat'])->name('chatbot');


// ── AUTH OPERATOR / ADMIN ─────────────────────────────────────────────────────
Route::prefix('operator')->name('operator.')->group(function () {

    // Login (guest only)
    Route::get('/login', [OperatorLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [OperatorLoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [OperatorLoginController::class, 'logout'])->name('logout');

    // Halaman yang butuh login operator
    Route::middleware('auth.operator')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Transaksi Setoran
        Route::get('/transaksi-setoran', [SetoranController::class, 'index'])->name('transaksi-setoran');
        Route::post('/transaksi-setoran', [SetoranController::class, 'store'])->name('transaksi-setoran.store');
        Route::delete('/transaksi-setoran/{setoran}', [SetoranController::class, 'destroy'])->name('transaksi-setoran.destroy');

        // Manajemen Nasabah
        Route::get('/manajemen-nasabah', [NasabahController::class, 'index'])->name('manajemen-nasabah');
        Route::post('/manajemen-nasabah', [NasabahController::class, 'store'])->name('manajemen-nasabah.store');
        Route::get('/manajemen-nasabah/{nasabah}', [NasabahController::class, 'show'])->name('manajemen-nasabah.show');
        Route::patch('/manajemen-nasabah/{nasabah}/toggle', [NasabahController::class, 'toggleStatus'])->name('manajemen-nasabah.toggle');
        Route::delete('/manajemen-nasabah/{nasabah}', [NasabahController::class, 'destroy'])->name('manajemen-nasabah.destroy');
        Route::get('/api/nasabah/search', [NasabahController::class, 'search'])->name('api.nasabah.search');

        // Manajemen Harga / Kategori
        Route::get('/manajemen-harga', [KategoriController::class, 'index'])->name('manajemen-harga');
        Route::post('/manajemen-harga', [KategoriController::class, 'store'])->name('manajemen-harga.store');
        Route::put('/manajemen-harga/{kategori}', [KategoriController::class, 'update'])->name('manajemen-harga.update');
        Route::patch('/manajemen-harga/{kategori}/toggle', [KategoriController::class, 'toggleStatus'])->name('manajemen-harga.toggle');

        // Validasi Penarikan
        Route::get('/validasi-penarikan', [PenarikanController::class, 'index'])->name('validasi-penarikan');
        Route::post('/validasi-penarikan/{penarikan}/setujui', [PenarikanController::class, 'setujui'])->name('validasi-penarikan.setujui');
        Route::post('/validasi-penarikan/{penarikan}/tolak', [PenarikanController::class, 'tolak'])->name('validasi-penarikan.tolak');

        // Inventaris & Penjualan
        Route::get('/inventaris-penjualan', [InventarisController::class, 'index'])->name('inventaris-penjualan');
        Route::post('/inventaris-penjualan', [InventarisController::class, 'storePenjualan'])->name('inventaris-penjualan.store');

        // Laporan
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');

        // Pesan Masuk
        Route::get('/pesan-masuk', [PesanMasukController::class, 'index'])->name('pesan-masuk');
        Route::post('/pesan-masuk/{pesan}/baca', [PesanMasukController::class, 'tandaiBaca'])->name('pesan-masuk.baca');

        // Notifikasi operator
        Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi');
        Route::post('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'tandaiDibaca'])->name('notifikasi.baca');
        Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'tandaiSemuaDibaca'])->name('notifikasi.baca-semua');

    });
});


// ── AUTH NASABAH ──────────────────────────────────────────────────────────────
Route::prefix('nasabah')->name('nasabah.')->group(function () {

    // Login (guest only)
    Route::get('/login', [NasabahLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [NasabahLoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [NasabahLoginController::class, 'logout'])->name('logout');

    // Daftar nasabah baru
    Route::get('/daftar', [NasabahRegisterController::class, 'showForm'])->name('register');
    Route::post('/daftar', [NasabahRegisterController::class, 'register'])->name('register.post');

    // Halaman yang butuh login nasabah
    Route::middleware('auth.nasabah')->group(function () {

        Route::get('/dashboard', [DashboardNasabahController::class, 'index'])->name('dashboard');

        Route::get('/saldo', [SaldoNasabahController::class, 'index'])->name('saldo');
        Route::post('/saldo/tarik', [SaldoNasabahController::class, 'ajukanPenarikan'])->name('saldo.tarik');

        Route::get('/histori-setoran', [HistoriSetoranController::class, 'index'])->name('histori-setoran');

        Route::get('/profil', [ProfilNasabahController::class, 'index'])->name('profil');
        Route::post('/profil', [ProfilNasabahController::class, 'update'])->name('profil.update');
        Route::post('/profil/ganti-pin', [ProfilNasabahController::class, 'gantiPin'])->name('profil.ganti-pin');

        // Notifikasi nasabah
        Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi');
        Route::post('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'tandaiDibaca'])->name('notifikasi.baca');
        Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'tandaiSemuaDibaca'])->name('notifikasi.baca-semua');

    Route::post('/chatbot', [ChatbotController::class, 'chat']);

    });
});
