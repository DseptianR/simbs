<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stok sampah di gudang per kategori
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->unique()->constrained('kategori_sampah')->cascadeOnDelete();
            $table->decimal('stok', 10, 3)->default(0); // stok dalam kg
            $table->timestamps();
        });

        // Catatan penjualan sampah ke pengepul
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_penjualan')->unique(); // JUAL-20250101-001
            $table->foreignId('kategori_id')->constrained('kategori_sampah')->restrictOnDelete();
            $table->foreignId('operator_id')->constrained('users')->restrictOnDelete();
            $table->decimal('berat', 10, 3);
            $table->decimal('harga_jual_per_kg', 10, 2);
            $table->decimal('total_pendapatan', 12, 2);
            $table->string('nama_pengepul')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan');
        Schema::dropIfExists('inventaris');
    }
};
