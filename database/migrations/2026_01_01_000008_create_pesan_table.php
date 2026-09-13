<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('pengirim', ['pelapor', 'inspektorat'])->default('pelapor');
            $table->text('isi');
            $table->string('lampiran_path')->nullable();
            $table->string('lampiran_nama')->nullable();
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamps();

            $table->index(['laporan_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesan');
    }
};
