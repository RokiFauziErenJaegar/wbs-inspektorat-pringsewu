<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\Pelapor;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicController::class, 'beranda'])->name('beranda');
Route::get('/cara-melapor', [PublicController::class, 'caraMelapor'])->name('cara-melapor');
Route::get('/berita', [PublicController::class, 'berita'])->name('berita.index');
Route::get('/berita/{artikel}', [PublicController::class, 'beritaDetail'])->name('berita.show');
Route::get('/faq', [PublicController::class, 'faq'])->name('faq');
Route::get('/lacak', [PublicController::class, 'lacak'])->name('lacak.index');
Route::post('/lacak', [PublicController::class, 'lacakCari'])->name('lacak.cari');

/*
|--------------------------------------------------------------------------
| Autentikasi
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/masuk', [AuthenticatedSessionController::class, 'store']);

    Route::get('/daftar', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/daftar', [RegisteredUserController::class, 'store']);

    Route::get('/lupa-password', [PasswordController::class, 'requestForm'])->name('password.request');
    Route::post('/lupa-password', [PasswordController::class, 'sendLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordController::class, 'reset'])->name('password.update');
});

Route::post('/keluar', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Area terautentikasi (semua peran)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [ProfilController::class, 'updatePassword'])->name('profil.password');

    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('/notifikasi/{notifikasi}', [NotifikasiController::class, 'buka'])->name('notifikasi.buka');
    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');
});

/*
|--------------------------------------------------------------------------
| Area pelapor (OPD)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:pelapor'])->prefix('pelapor')->name('pelapor.')->group(function () {
    Route::get('/', Pelapor\DashboardController::class)->name('dashboard');

    Route::prefix('pengaduan')->name('laporan.')->group(function () {
        Route::get('/', [Pelapor\LaporanController::class, 'index'])->name('index');
        Route::get('/pilih-kategori', [Pelapor\LaporanController::class, 'pilihKategori'])->name('pilih-kategori');
        Route::get('/baru', [Pelapor\LaporanController::class, 'create'])->name('create');
        Route::post('/', [Pelapor\LaporanController::class, 'store'])->name('store');
        Route::get('/{laporan}', [Pelapor\LaporanController::class, 'show'])->name('show');
        Route::get('/{laporan}/ubah', [Pelapor\LaporanController::class, 'edit'])->name('edit');
        Route::put('/{laporan}', [Pelapor\LaporanController::class, 'update'])->name('update');
        Route::delete('/{laporan}', [Pelapor\LaporanController::class, 'destroy'])->name('destroy');
        Route::post('/{laporan}/kirim', [Pelapor\LaporanController::class, 'kirim'])->name('kirim');
        Route::post('/{laporan}/pesan', [Pelapor\PesanController::class, 'store'])->name('pesan');
        Route::get('/{laporan}/lampiran/{lampiran}', [Pelapor\LaporanController::class, 'unduhLampiran'])->name('lampiran.unduh');
        Route::delete('/{laporan}/lampiran/{lampiran}', [Pelapor\LaporanController::class, 'hapusLampiran'])->name('lampiran.hapus');
    });
});

/*
|--------------------------------------------------------------------------
| Area Inspektorat (admin & petugas)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin,petugas'])->prefix('inspektorat')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    Route::prefix('pengaduan')->name('laporan.')->group(function () {
        Route::get('/', [Admin\LaporanController::class, 'index'])->name('index');
        Route::get('/{laporan}', [Admin\LaporanController::class, 'show'])->name('show');
        Route::get('/{laporan}/cetak', [Admin\LaporanController::class, 'cetak'])->name('cetak');
        Route::post('/{laporan}/verifikasi', [Admin\LaporanController::class, 'verifikasi'])->name('verifikasi');
        Route::post('/{laporan}/terima', [Admin\LaporanController::class, 'terima'])->name('terima');
        Route::post('/{laporan}/tolak', [Admin\LaporanController::class, 'tolak'])->name('tolak');
        Route::post('/{laporan}/disposisi', [Admin\LaporanController::class, 'disposisi'])->name('disposisi');
        Route::post('/{laporan}/progres', [Admin\LaporanController::class, 'progres'])->name('progres');
        Route::post('/{laporan}/selesai', [Admin\LaporanController::class, 'selesai'])->name('selesai');
        Route::post('/{laporan}/buka-kembali', [Admin\LaporanController::class, 'bukaKembali'])->name('buka-kembali');
        Route::post('/{laporan}/prioritas', [Admin\LaporanController::class, 'prioritas'])->name('prioritas');
        Route::post('/{laporan}/pesan', [Admin\PesanController::class, 'store'])->name('pesan');
        Route::post('/{laporan}/lampiran', [Admin\LaporanController::class, 'unggahLampiran'])->name('lampiran.unggah');
        Route::get('/{laporan}/lampiran/{lampiran}', [Admin\LaporanController::class, 'unduhLampiran'])->name('lampiran.unduh');
    });

    Route::get('/statistik', [Admin\StatistikController::class, 'index'])->name('statistik');
    Route::get('/statistik/ekspor', [Admin\StatistikController::class, 'ekspor'])->name('statistik.ekspor');

    // Master data hanya untuk administrator.
    Route::middleware('role:admin')->group(function () {
        Route::resource('opd', Admin\OpdController::class)->except('show');
        Route::resource('kategori', Admin\KategoriController::class)->except('show');
        Route::resource('artikel', Admin\ArtikelController::class)->except('show');
        Route::resource('faq', Admin\FaqController::class)->except('show')->parameters(['faq' => 'faq']);
        Route::resource('user', Admin\UserController::class)->except('show');
        Route::post('/user/{user}/toggle', [Admin\UserController::class, 'toggle'])->name('user.toggle');
    });
});
