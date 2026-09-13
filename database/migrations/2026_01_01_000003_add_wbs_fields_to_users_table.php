<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 60)->unique()->after('id');
            $table->foreignId('opd_id')->nullable()->after('name')->constrained('opd')->nullOnDelete();
            $table->string('nip', 30)->nullable()->after('opd_id');
            $table->string('jabatan')->nullable()->after('nip');
            $table->string('telepon', 30)->nullable()->after('jabatan');
            $table->string('alamat')->nullable()->after('telepon');
            $table->enum('role', ['admin', 'petugas', 'pelapor'])->default('pelapor')->after('password');
            $table->string('avatar')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('avatar');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->softDeletes();

            $table->index(['role', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['opd_id']);
            $table->dropIndex(['role', 'is_active']);
            $table->dropColumn([
                'username', 'opd_id', 'nip', 'jabatan', 'telepon', 'alamat',
                'role', 'avatar', 'is_active', 'last_login_at', 'last_login_ip', 'deleted_at',
            ]);
        });
    }
};
