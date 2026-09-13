<?php

namespace Tests\Feature;

use App\Models\Artikel;
use App\Models\Laporan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Uji asap: memastikan seluruh halaman utama dapat dirender
 * untuk masing-masing peran pengguna.
 */
class HalamanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Diseed per pengujian agar tidak bergantung pada urutan berkas uji.
        $this->seed(DatabaseSeeder::class);
    }

    public function test_halaman_publik_dapat_diakses(): void
    {
        foreach (['/', '/cara-melapor', '/berita', '/faq', '/lacak', '/masuk', '/daftar', '/lupa-password'] as $alamat) {
            $this->get($alamat)->assertOk();
        }

        $artikel = Artikel::terbit()->first();
        $this->get(route('berita.show', $artikel))->assertOk();
    }

    public function test_pelacakan_aduan_menemukan_berkas(): void
    {
        $laporan = Laporan::terkirim()->first();

        $this->post('/lacak', [
            'nomor_tiket' => $laporan->nomor_tiket,
            'kode_akses' => $laporan->kode_akses,
        ])->assertOk()->assertSee($laporan->judul);
    }

    public function test_halaman_pelapor_dapat_diakses(): void
    {
        $pelapor = User::where('role', User::ROLE_PELAPOR)->first();
        $laporan = Laporan::where('user_id', $pelapor->id)->first();

        $this->actingAs($pelapor);

        $this->get(route('pelapor.dashboard'))->assertOk();
        $this->get(route('pelapor.laporan.index'))->assertOk();
        $this->get(route('pelapor.laporan.pilih-kategori'))->assertOk();
        $this->get(route('pelapor.laporan.create', ['kategori' => 'tindak-pidana-korupsi']))->assertOk();
        $this->get(route('profil.edit'))->assertOk();
        $this->get(route('notifikasi.index'))->assertOk();

        if ($laporan) {
            $this->get(route('pelapor.laporan.show', $laporan))->assertOk();
        }
    }

    public function test_halaman_inspektorat_dapat_diakses(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $laporan = Laporan::terkirim()->first();

        $this->actingAs($admin);

        foreach ([
            route('admin.dashboard'),
            route('admin.laporan.index'),
            route('admin.statistik'),
            route('admin.opd.index'),
            route('admin.opd.create'),
            route('admin.kategori.index'),
            route('admin.kategori.create'),
            route('admin.user.index'),
            route('admin.user.create'),
            route('admin.artikel.index'),
            route('admin.artikel.create'),
            route('admin.faq.index'),
            route('admin.faq.create'),
            route('admin.laporan.show', $laporan),
            route('admin.laporan.cetak', $laporan),
        ] as $alamat) {
            $this->get($alamat)->assertOk();
        }
    }

    public function test_pelapor_tidak_dapat_membuka_area_inspektorat(): void
    {
        $pelapor = User::where('role', User::ROLE_PELAPOR)->first();

        $this->actingAs($pelapor)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_petugas_tidak_dapat_membuka_master_data(): void
    {
        $petugas = User::where('role', User::ROLE_PETUGAS)->first();

        $this->actingAs($petugas)
            ->get(route('admin.opd.index'))
            ->assertForbidden();
    }

    public function test_ekspor_rekap_menghasilkan_csv(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $this->actingAs($admin)
            ->get(route('admin.statistik.ekspor'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
