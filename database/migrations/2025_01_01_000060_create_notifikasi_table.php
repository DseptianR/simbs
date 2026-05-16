<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            // Penerima: bisa operator (users) atau nasabah
            $table->string('penerima_type'); // 'operator' | 'nasabah'
            $table->unsignedBigInteger('penerima_id');
            $table->string('judul');
            $table->text('pesan');
            $table->string('ikon')->default('🔔');
            // tipe: info | success | warning | danger
            $table->enum('tipe', ['info', 'success', 'warning', 'danger'])->default('info');
            $table->string('url')->nullable(); // link saat diklik
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamps();

            $table->index(['penerima_type', 'penerima_id', 'dibaca_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
