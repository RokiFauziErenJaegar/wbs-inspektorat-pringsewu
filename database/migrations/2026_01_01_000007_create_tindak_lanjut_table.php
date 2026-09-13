<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tindak_lanjut', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporan')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('tipe', [
                'dibuat', 'dikirim', 'verifikasi', 'ditolak', 'disposisi',
                'progres', 'catatan', 'selesai', 'lampiran',
            ])->default('catatan');
            $table->string('judul');
            $table->text('catatan')->nullable();
            $table->string('status_lama', 30)->nullable();
            $table->string('status_baru', 30)->nullable();
            $table->boolean('is_internal')->default(false);
            $table->timestamps();

            $table->index(['laporan_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut');
    }
};
