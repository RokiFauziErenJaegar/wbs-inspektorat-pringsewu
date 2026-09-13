<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terlapor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporan')->cascadeOnDelete();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('instansi')->nullable();
            $table->enum('klasifikasi', [
                'pejabat_struktural', 'pejabat_fungsional', 'pelaksana',
                'pppk', 'honorer', 'pihak_ketiga', 'lainnya',
            ])->default('lainnya');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terlapor');
    }
};
