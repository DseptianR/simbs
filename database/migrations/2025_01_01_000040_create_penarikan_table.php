<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penarikan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_penarikan')->unique(); // WD-20250101-001
            $table->foreignId('nasabah_id')->constrained('nasabah')->cascadeOnDelete();
            $table->decimal('jumlah', 12, 2);
            // status: pending | disetujui | ditolak
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_nasabah')->nullable();
            $table->text('catatan_operator')->nullable();
            // Operator yang memvalidasi
            $table->foreignId('divalidasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('divalidasi_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penarikan');
    }
};
