<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_sampah', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('satuan')->default('kg');
            $table->decimal('harga_per_satuan', 10, 2);   // harga beli dari nasabah
            $table->decimal('harga_jual', 10, 2)->default(0); // harga jual ke pengepul
            $table->string('ikon')->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_sampah');
    }
};
