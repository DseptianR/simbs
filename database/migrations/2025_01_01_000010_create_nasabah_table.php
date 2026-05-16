<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nasabah', function (Blueprint $table) {
            $table->id();
            $table->string('no_rekening')->unique(); // contoh: BS-001
            $table->string('nama');
            $table->string('nik', 16)->unique()->nullable();
            $table->string('no_hp', 15)->nullable();
            $table->text('alamat')->nullable();
            $table->string('pin'); // PIN 6 digit (disimpan sebagai bcrypt hash)
            $table->decimal('saldo', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            // Operator yang mendaftarkan nasabah ini
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nasabah');
    }
};
