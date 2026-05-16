<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Header transaksi setoran
        Schema::create('setoran', function (Blueprint $table) {
            $table->id();
            $table->string('kode_setoran')->unique(); // SET-20250101-001
            $table->foreignId('nasabah_id')->constrained('nasabah')->cascadeOnDelete();
            $table->foreignId('operator_id')->constrained('users')->restrictOnDelete();
            $table->decimal('total_berat', 10, 3)->default(0);  // total kg
            $table->decimal('total_nilai', 12, 2)->default(0);  // total rupiah
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Detail per kategori sampah dalam satu setoran
        Schema::create('detail_setoran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setoran_id')->constrained('setoran')->cascadeOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori_sampah')->restrictOnDelete();
            $table->decimal('berat', 10, 3);            // berat dalam kg
            $table->decimal('harga_satuan', 10, 2);     // harga saat transaksi (snapshot)
            $table->decimal('subtotal', 12, 2);         // berat × harga_satuan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_setoran');
        Schema::dropIfExists('setoran');
    }
};
