<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opd', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('singkatan', 60)->nullable();
            $table->string('slug')->unique();
            $table->enum('jenis', ['sekretariat', 'dinas', 'badan', 'inspektorat', 'kecamatan', 'rsud', 'satuan', 'lainnya'])
                ->default('dinas');
            $table->string('alamat')->nullable();
            $table->string('telepon', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('kepala')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['jenis', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opd');
    }
};
