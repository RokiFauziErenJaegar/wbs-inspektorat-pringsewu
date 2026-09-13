<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket', 30)->unique();
            $table->string('kode_akses', 16)->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori');
            $table->foreignId('opd_id')->nullable()->constrained('opd')->nullOnDelete();

            $table->string('judul');
            $table->longText('uraian');
            $table->string('lokasi_kejadian')->nullable();
            $table->date('tanggal_kejadian')->nullable();
            $table->decimal('nilai_kerugian', 18, 2)->nullable();

            $table->boolean('is_anonymous')->default(false);
            $table->enum('status', ['draft', 'terkirim', 'verifikasi', 'diproses', 'selesai', 'ditolak'])->default('draft');
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi', 'mendesak'])->default('sedang');

            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->date('deadline')->nullable();

            $table->text('alasan_penolakan')->nullable();
            $table->text('kesimpulan')->nullable();
            $table->text('rekomendasi')->nullable();

            $table->unsignedInteger('unread_pelapor')->default(0);
            $table->unsignedInteger('unread_admin')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
            $table->index(['kategori_id', 'status']);
            $table->index('petugas_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
