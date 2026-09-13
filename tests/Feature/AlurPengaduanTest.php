<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Notifikasi;
use App\Models\Opd;
use App\Models\User;
use Database\Seeders\KategoriSeeder;
use Database\Seeders\OpdSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Menguji alur utama: OPD mendaftar, mengirim pengaduan,
 * lalu Inspektorat memverifikasi hingga menyelesaikannya.
 */
class AlurPengaduanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([OpdSeeder::class, KategoriSeeder::class, UserSeeder::class]);
    }

    public function test_opd_dapat_mendaftar_dan_langsung_masuk(): void
    {
        $opd = Opd::aktif()->first();

        $this->post('/daftar', [
            'name' => 'Wahyu Pratama',
            'username' => 'opd.uji',
            'email' => 'opd.uji@pringsewukab.go.id',
            'opd_id' => $opd->id,
            'jabatan' => 'Analis Kebijakan',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'setuju' => '1',
        ])->assertRedirect(route('pelapor.dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'username' => 'opd.uji',
            'role' => User::ROLE_PELAPOR,
            'opd_id' => $opd->id,
        ]);
    }

    public function test_pelapor_dapat_masuk_dengan_username_maupun_email(): void
    {
        $this->post('/masuk', ['login' => 'opd.dinkes', 'password' => 'pelapor12345'])
            ->assertRedirect(route('pelapor.dashboard'));
        $this->assertAuthenticated();

        $this->post('/keluar');

        $this->post('/masuk', ['login' => 'admin@pringsewukab.go.id', 'password' => 'admin12345'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_akun_nonaktif_ditolak_saat_masuk(): void
    {
        User::where('username', 'opd.dinkes')->update(['is_active' => false]);

        $this->post('/masuk', ['login' => 'opd.dinkes', 'password' => 'pelapor12345'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_draf_dapat_disimpan_lalu_dikirim(): void
    {
        $pelapor = User::where('role', User::ROLE_PELAPOR)->first();
        $kategori = Kategori::first();

        $this->actingAs($pelapor)->post(route('pelapor.laporan.store'), [
            'aksi' => 'draft',
            'kategori_id' => $kategori->id,
            'judul' => 'Draf pengaduan uji coba',
            'uraian' => 'Uraian singkat.',
        ])->assertRedirect();

        $laporan = Laporan::where('judul', 'Draf pengaduan uji coba')->firstOrFail();
        $this->assertSame(Laporan::STATUS_DRAFT, $laporan->status);
        $this->assertNull($laporan->submitted_at);

        // Draf tanpa pihak terlapor belum boleh dikirim.
        $this->actingAs($pelapor)
            ->post(route('pelapor.laporan.kirim', $laporan))
            ->assertSessionHas('gagal');

        $laporan->terlapor()->create(['nama' => 'Oknum Pegawai', 'klasifikasi' => 'pelaksana']);

        $this->actingAs($pelapor)
            ->post(route('pelapor.laporan.kirim', $laporan))
            ->assertSessionHas('sukses');

        $this->assertSame(Laporan::STATUS_TERKIRIM, $laporan->fresh()->status);
        $this->assertNotNull($laporan->fresh()->submitted_at);
    }

    public function test_pengiriman_menolak_isian_yang_belum_lengkap(): void
    {
        $pelapor = User::where('role', User::ROLE_PELAPOR)->first();
        $kategori = Kategori::first();

        $this->actingAs($pelapor)->post(route('pelapor.laporan.store'), [
            'aksi' => 'kirim',
            'kategori_id' => $kategori->id,
            'judul' => 'Terlalu singkat',
            'uraian' => 'Kurang dari lima puluh karakter.',
            'setuju' => '1',
        ])->assertSessionHasErrors(['uraian', 'terlapor']);

        $this->assertDatabaseCount('laporan', 0);
    }

    public function test_alur_penuh_dari_pengiriman_hingga_selesai(): void
    {
        Storage::fake('public');

        $pelapor = User::where('role', User::ROLE_PELAPOR)->first();
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $petugas = User::where('role', User::ROLE_PETUGAS)->first();
        $kategori = Kategori::where('kode', 'PBJ')->firstOrFail();
        $opd = Opd::where('singkatan', 'PUPR')->firstOrFail();

        // 1. Pelapor mengirim pengaduan lengkap dengan lampiran.
        $this->actingAs($pelapor)->post(route('pelapor.laporan.store'), [
            'aksi' => 'kirim',
            'kategori_id' => $kategori->id,
            'opd_id' => $opd->id,
            'judul' => 'Dugaan penyimpangan pekerjaan rehabilitasi gedung',
            'uraian' => str_repeat('Uraian kronologi kejadian yang cukup panjang dan rinci. ', 4),
            'lokasi_kejadian' => 'Bidang Cipta Karya',
            'tanggal_kejadian' => now()->subMonth()->toDateString(),
            'nilai_kerugian' => 125000000,
            'terlapor' => [
                ['nama' => 'Pejabat Pembuat Komitmen', 'jabatan' => 'PPK', 'klasifikasi' => 'pejabat_struktural'],
                ['nama' => '', 'jabatan' => '', 'klasifikasi' => 'lainnya'],
            ],
            'lampiran' => [
                ['file' => UploadedFile::fake()->create('kontrak.pdf', 120, 'application/pdf'), 'keterangan' => 'Salinan kontrak'],
            ],
            'setuju' => '1',
        ])->assertRedirect();

        $laporan = Laporan::firstOrFail();

        $this->assertSame(Laporan::STATUS_TERKIRIM, $laporan->status);
        $this->assertMatchesRegularExpression('/^WBS-\d{8}-\d{4}$/', $laporan->nomor_tiket);
        $this->assertCount(1, $laporan->terlapor, 'Baris terlapor kosong seharusnya diabaikan.');
        $this->assertCount(1, $laporan->lampiran);
        Storage::disk('public')->assertExists($laporan->lampiran->first()->path);

        // Administrator menerima notifikasi pengaduan baru.
        $this->assertDatabaseHas('notifikasi', ['user_id' => $admin->id, 'tipe' => 'laporan_baru']);

        // 2. Inspektorat memulai verifikasi.
        $this->actingAs($admin)
            ->post(route('admin.laporan.verifikasi', $laporan))
            ->assertSessionHas('sukses');

        $this->assertSame(Laporan::STATUS_VERIFIKASI, $laporan->fresh()->status);

        // 3. Diterima, diberi prioritas, dan didisposisikan kepada petugas.
        $this->actingAs($admin)->post(route('admin.laporan.terima', $laporan), [
            'prioritas' => 'tinggi',
            'petugas_id' => $petugas->id,
            'deadline' => now()->addDays(30)->toDateString(),
            'catatan' => 'Dalami dokumen kontrak dan hasil pemeriksaan fisik.',
        ])->assertSessionHas('sukses');

        $laporan->refresh();
        $this->assertSame(Laporan::STATUS_DIPROSES, $laporan->status);
        $this->assertSame($petugas->id, $laporan->petugas_id);
        $this->assertSame('tinggi', $laporan->prioritas);

        // 4. Petugas mencatat perkembangan (internal maupun publik).
        $this->actingAs($petugas)->post(route('admin.laporan.progres', $laporan), [
            'judul' => 'Permintaan keterangan kepada PPK',
            'catatan' => 'Undangan permintaan keterangan telah dikirimkan.',
        ])->assertSessionHas('sukses');

        $this->actingAs($petugas)->post(route('admin.laporan.progres', $laporan), [
            'judul' => 'Catatan internal tim',
            'catatan' => 'Perlu konfirmasi dengan konsultan pengawas.',
            'is_internal' => '1',
        ])->assertSessionHas('sukses');

        $this->assertSame(1, $laporan->tindakLanjut()->where('is_internal', true)->count());

        // Pelapor tidak boleh melihat catatan internal.
        $this->actingAs($pelapor)
            ->get(route('pelapor.laporan.show', $laporan))
            ->assertOk()
            ->assertSee('Permintaan keterangan kepada PPK')
            ->assertDontSee('Perlu konfirmasi dengan konsultan pengawas');

        // 5. Komunikasi dua arah.
        $this->actingAs($petugas)->post(route('admin.laporan.pesan', $laporan), [
            'isi' => 'Mohon kirimkan dokumen serah terima pekerjaan.',
        ])->assertSessionHas('sukses');

        $this->assertSame(1, $laporan->fresh()->unread_pelapor);

        $this->actingAs($pelapor)->post(route('pelapor.laporan.pesan', $laporan), [
            'isi' => 'Baik, dokumen menyusul minggu ini.',
        ])->assertSessionHas('sukses');

        $this->assertSame(1, $laporan->fresh()->unread_admin);

        // Membuka rincian menandai pesan sebagai sudah dibaca.
        $this->actingAs($pelapor)->get(route('pelapor.laporan.show', $laporan));
        $this->assertSame(0, $laporan->fresh()->unread_pelapor);

        // 6. Penanganan diselesaikan.
        $this->actingAs($petugas)->post(route('admin.laporan.selesai', $laporan), [
            'kesimpulan' => 'Terbukti terdapat kekurangan volume pekerjaan sebesar Rp98.000.000.',
            'rekomendasi' => 'Penyedia wajib menyetorkan kelebihan pembayaran ke kas daerah.',
        ])->assertSessionHas('sukses');

        $laporan->refresh();
        $this->assertSame(Laporan::STATUS_SELESAI, $laporan->status);
        $this->assertNotNull($laporan->selesai_at);
        $this->assertSame(100, $laporan->progres);

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $pelapor->id,
            'laporan_id' => $laporan->id,
            'tipe' => 'selesai',
        ]);

        // 7. Administrator dapat membuka kembali berkas.
        $this->actingAs($admin)->post(route('admin.laporan.buka-kembali', $laporan), [
            'catatan' => 'Ditemukan bukti baru yang perlu didalami.',
        ])->assertSessionHas('sukses');

        $this->assertSame(Laporan::STATUS_DIPROSES, $laporan->fresh()->status);
    }

    public function test_penolakan_mencatat_alasan_dan_memberi_tahu_pelapor(): void
    {
        $pelapor = User::where('role', User::ROLE_PELAPOR)->first();
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $laporan = $this->buatLaporanTerkirim($pelapor);

        $this->actingAs($admin)->post(route('admin.laporan.tolak', $laporan), [
            'alasan_penolakan' => 'Materi pengaduan merupakan kewenangan instansi lain di luar Inspektorat.',
        ])->assertSessionHas('sukses');

        $laporan->refresh();
        $this->assertSame(Laporan::STATUS_DITOLAK, $laporan->status);
        $this->assertNotNull($laporan->alasan_penolakan);

        $this->assertTrue(
            Notifikasi::where('user_id', $pelapor->id)->where('tipe', 'ditolak')->exists()
        );
    }

    public function test_petugas_hanya_dapat_membuka_berkas_yang_didisposisikan(): void
    {
        $pelapor = User::where('role', User::ROLE_PELAPOR)->first();
        [$petugasA, $petugasB] = User::where('role', User::ROLE_PETUGAS)->take(2)->get()->all();

        $laporan = $this->buatLaporanTerkirim($pelapor);
        $laporan->update(['petugas_id' => $petugasA->id, 'status' => Laporan::STATUS_DIPROSES]);

        $this->actingAs($petugasA)->get(route('admin.laporan.show', $laporan))->assertOk();
        $this->actingAs($petugasB)->get(route('admin.laporan.show', $laporan))->assertForbidden();
    }

    public function test_pelapor_tidak_dapat_membuka_pengaduan_milik_orang_lain(): void
    {
        $daftar = User::where('role', User::ROLE_PELAPOR)->take(2)->get();

        $laporan = $this->buatLaporanTerkirim($daftar[0]);

        $this->actingAs($daftar[1])
            ->get(route('pelapor.laporan.show', $laporan))
            ->assertForbidden();
    }

    public function test_pengaduan_anonim_menyembunyikan_identitas_pelapor(): void
    {
        $pelapor = User::where('role', User::ROLE_PELAPOR)->first();
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $laporan = $this->buatLaporanTerkirim($pelapor);
        $laporan->update(['is_anonymous' => true]);

        $this->assertSame('Pelapor Anonim', $laporan->fresh()->nama_pelapor);

        $this->actingAs($admin)
            ->get(route('admin.laporan.show', $laporan))
            ->assertOk()
            ->assertSee('Pelapor Anonim')
            ->assertDontSee($pelapor->email);

        // Inspektorat tidak dapat mengirim pesan pada pengaduan anonim.
        $this->actingAs($admin)
            ->post(route('admin.laporan.pesan', $laporan), ['isi' => 'Mohon klarifikasi.'])
            ->assertSessionHas('gagal');

        $this->assertDatabaseCount('pesan', 0);
    }

    public function test_pelacakan_publik_memerlukan_kode_akses_yang_benar(): void
    {
        $pelapor = User::where('role', User::ROLE_PELAPOR)->first();
        $laporan = $this->buatLaporanTerkirim($pelapor);

        $this->post('/lacak', [
            'nomor_tiket' => $laporan->nomor_tiket,
            'kode_akses' => 'SALAH123',
        ])->assertOk()->assertSee('Pengaduan tidak ditemukan');

        $this->post('/lacak', [
            'nomor_tiket' => $laporan->nomor_tiket,
            'kode_akses' => $laporan->kode_akses,
        ])->assertOk()->assertSee($laporan->judul);
    }

    public function test_nomor_tiket_bertambah_berurutan_pada_hari_yang_sama(): void
    {
        $pelapor = User::where('role', User::ROLE_PELAPOR)->first();

        $pertama = $this->buatLaporanTerkirim($pelapor);
        $kedua = $this->buatLaporanTerkirim($pelapor);

        $this->assertSame(
            (int) substr($pertama->nomor_tiket, -4) + 1,
            (int) substr($kedua->nomor_tiket, -4),
        );
    }

    // ------------------------------------------------------------------

    private function buatLaporanTerkirim(User $pelapor): Laporan
    {
        return Laporan::create([
            'nomor_tiket' => Laporan::buatNomorTiket(),
            'kode_akses' => Laporan::buatKodeAkses(),
            'user_id' => $pelapor->id,
            'kategori_id' => Kategori::first()->id,
            'judul' => 'Pengaduan uji '.uniqid(),
            'uraian' => str_repeat('Uraian pengaduan yang cukup panjang. ', 3),
            'status' => Laporan::STATUS_TERKIRIM,
            'submitted_at' => now(),
        ]);
    }
}
