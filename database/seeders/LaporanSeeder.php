<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Opd;
use App\Models\User;
use Illuminate\Database\Seeder;

class LaporanSeeder extends Seeder
{
    public function run(): void
    {
        $pelapor = User::where('role', User::ROLE_PELAPOR)->get();
        $petugas = User::where('role', User::ROLE_PETUGAS)->get();
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $kategori = Kategori::pluck('id', 'kode');
        $opd = Opd::pluck('id', 'singkatan');

        if ($pelapor->isEmpty() || $petugas->isEmpty()) {
            return;
        }

        $contoh = [
            [
                'kode' => 'PBJ', 'opd' => 'PUPR', 'status' => Laporan::STATUS_DIPROSES, 'prioritas' => 'tinggi',
                'judul' => 'Dugaan pengaturan pemenang tender rehabilitasi jalan poros pekon',
                'uraian' => 'Pada proses pengadaan paket rehabilitasi jalan poros pekon tahun anggaran berjalan, terdapat indikasi pengaturan pemenang tender. Tiga penyedia yang mengikuti lelang diduga berada dalam satu kepemilikan dan mengajukan dokumen penawaran dengan format serta kesalahan pengetikan yang identik. Panitia pengadaan diduga mengetahui hal tersebut namun tetap meneruskan proses hingga penetapan pemenang. Nilai paket pekerjaan mencapai Rp2,4 miliar dan realisasi fisik di lapangan tidak sesuai dengan spesifikasi teknis dalam kontrak.',
                'kerugian' => 480000000, 'lokasi' => 'Bidang Bina Marga, Dinas PUPR Kabupaten Pringsewu',
                'terlapor' => [
                    ['Drs. Sujarwo', 'Pejabat Pembuat Komitmen', 'pejabat_struktural'],
                    ['CV Karya Mandiri Sejahtera', 'Penyedia Jasa Konstruksi', 'pihak_ketiga'],
                ],
                'hari' => 26,
            ],
            [
                'kode' => 'PUNGLI', 'opd' => 'DISDUKCAPIL', 'status' => Laporan::STATUS_SELESAI, 'prioritas' => 'sedang',
                'judul' => 'Pungutan tidak resmi pada pengurusan Kartu Keluarga',
                'uraian' => 'Warga yang mengurus perubahan Kartu Keluarga di loket pelayanan dimintai biaya sebesar Rp150.000 per dokumen dengan alasan biaya percepatan. Padahal sesuai ketentuan, seluruh layanan administrasi kependudukan tidak dipungut biaya. Praktik ini berlangsung berulang dan disaksikan beberapa pemohon lain pada hari yang sama. Terdapat bukti percakapan dan foto kuitansi tulis tangan tanpa kop resmi.',
                'kerugian' => null, 'lokasi' => 'Loket Pelayanan Disdukcapil Kabupaten Pringsewu',
                'terlapor' => [['Oknum Petugas Loket 3', 'Pelaksana', 'pelaksana']],
                'hari' => 74, 'selesaiHari' => 22,
                'kesimpulan' => 'Hasil pemeriksaan membuktikan adanya pungutan di luar ketentuan yang dilakukan oleh oknum petugas loket. Yang bersangkutan mengakui perbuatannya dan telah mengembalikan seluruh dana kepada pemohon.',
                'rekomendasi' => 'Menjatuhkan hukuman disiplin sesuai PP Nomor 94 Tahun 2021, memindahtugaskan yang bersangkutan dari unit pelayanan, serta memasang papan informasi bebas biaya pada seluruh loket.',
            ],
            [
                'kode' => 'GRATIFIKASI', 'opd' => 'DPMPTSP', 'status' => Laporan::STATUS_VERIFIKASI, 'prioritas' => 'sedang',
                'judul' => 'Dugaan penerimaan gratifikasi dalam penerbitan izin usaha',
                'uraian' => 'Terdapat informasi bahwa salah satu pejabat pada bidang perizinan menerima pemberian berupa uang tunai dari pemohon izin usaha pertambangan galian C. Pemberian diduga dilakukan agar proses penerbitan izin dipercepat dan sebagian persyaratan teknis tidak diperiksa secara ketat. Informasi diperoleh dari percakapan internal dan keterangan pemohon lain yang izinnya tertunda lama.',
                'kerugian' => 75000000, 'lokasi' => 'Bidang Perizinan DPMPTSP Kabupaten Pringsewu',
                'terlapor' => [['Hj. Sri Wahyuni, S.E.', 'Kepala Bidang Perizinan', 'pejabat_struktural']],
                'hari' => 9,
            ],
            [
                'kode' => 'KEPEGAWAIAN', 'opd' => 'DISDIKBUD', 'status' => Laporan::STATUS_TERKIRIM, 'prioritas' => 'sedang',
                'judul' => 'Absensi fiktif tenaga pendidik pada salah satu sekolah dasar',
                'uraian' => 'Seorang tenaga pendidik tercatat hadir penuh pada sistem presensi elektronik, namun berdasarkan pengamatan rekan kerja yang bersangkutan tidak pernah hadir mengajar selama kurang lebih tiga bulan terakhir. Diduga presensi dilakukan oleh pihak lain menggunakan perangkat yang sama. Kondisi ini berdampak pada terganggunya kegiatan belajar mengajar peserta didik.',
                'kerugian' => null, 'lokasi' => 'SD Negeri di wilayah Kecamatan Gading Rejo',
                'terlapor' => [['Oknum Guru Kelas V', 'Guru', 'pejabat_fungsional']],
                'hari' => 3,
            ],
            [
                'kode' => 'TPK', 'opd' => 'DINSOS', 'status' => Laporan::STATUS_DIPROSES, 'prioritas' => 'mendesak',
                'judul' => 'Dugaan penyunatan bantuan sosial bagi keluarga penerima manfaat',
                'uraian' => 'Penyaluran bantuan sosial kepada keluarga penerima manfaat diduga dipotong oleh oknum pendamping. Setiap penerima hanya memperoleh sebagian dari nominal yang seharusnya diterima dengan alasan biaya administrasi dan transportasi. Praktik ini dilaporkan terjadi pada beberapa pekon dan melibatkan lebih dari seratus penerima manfaat. Terdapat daftar penerima serta rekaman percakapan sebagai bukti awal.',
                'kerugian' => 128500000, 'lokasi' => 'Beberapa pekon di Kecamatan Pagelaran',
                'terlapor' => [
                    ['Oknum Pendamping Sosial', 'Tenaga Pendamping', 'honorer'],
                    ['Aparat Pekon', 'Perangkat Pekon', 'lainnya'],
                ],
                'hari' => 17,
            ],
            [
                'kode' => 'WEWENANG', 'opd' => 'SETDA', 'status' => Laporan::STATUS_DITOLAK, 'prioritas' => 'rendah',
                'judul' => 'Keberatan atas mutasi jabatan di lingkungan sekretariat',
                'uraian' => 'Pelapor menyampaikan keberatan atas keputusan mutasi jabatan yang dinilai tidak sesuai dengan kompetensi dan masa kerja. Pelapor merasa keputusan tersebut diambil tanpa pertimbangan objektif dan meminta agar dilakukan peninjauan ulang terhadap penempatan yang bersangkutan.',
                'kerugian' => null, 'lokasi' => 'Sekretariat Daerah Kabupaten Pringsewu',
                'terlapor' => [['Tim Penilai Kinerja', 'Baperjakat', 'pejabat_struktural']],
                'hari' => 45, 'selesaiHari' => 6,
                'alasan' => 'Materi pengaduan merupakan keberatan administrasi kepegawaian yang penyelesaiannya menjadi kewenangan BKPSDM melalui mekanisme keberatan sesuai ketentuan manajemen ASN, bukan objek pemeriksaan Inspektorat. Pelapor dipersilakan menempuh jalur tersebut.',
            ],
            [
                'kode' => 'PELAYANAN', 'opd' => 'RSUD', 'status' => Laporan::STATUS_SELESAI, 'prioritas' => 'sedang',
                'judul' => 'Pelayanan pasien BPJS yang berbelit di instalasi rawat jalan',
                'uraian' => 'Pasien peserta BPJS Kesehatan mengalami pelayanan yang berbelit pada instalasi rawat jalan. Pasien diminta kembali beberapa kali untuk melengkapi berkas yang sebenarnya telah diserahkan pada kunjungan sebelumnya. Selain itu terdapat perbedaan perlakuan antara pasien umum dan pasien peserta jaminan kesehatan dalam hal antrean pemeriksaan.',
                'kerugian' => null, 'lokasi' => 'Instalasi Rawat Jalan RSUD Pringsewu',
                'terlapor' => [['Unit Pendaftaran Rawat Jalan', 'Unit Kerja', 'lainnya']],
                'hari' => 96, 'selesaiHari' => 30,
                'kesimpulan' => 'Ditemukan kelemahan pada alur pendaftaran dan komunikasi persyaratan kepada pasien, namun tidak ditemukan unsur kesengajaan diskriminasi pelayanan.',
                'rekomendasi' => 'Menyusun ulang standar operasional prosedur pendaftaran, memasang alur layanan yang mudah dibaca, serta melaksanakan pelatihan pelayanan prima bagi petugas pendaftaran.',
            ],
            [
                'kode' => 'KEPENTINGAN', 'opd' => 'DISKOPERINDAG', 'status' => Laporan::STATUS_TERKIRIM, 'prioritas' => 'tinggi',
                'judul' => 'Dugaan benturan kepentingan pada penunjukan penyedia belanja hibah',
                'uraian' => 'Salah satu pejabat pengadaan diduga memiliki hubungan keluarga langsung dengan pemilik perusahaan yang ditunjuk sebagai penyedia pada belanja hibah peralatan usaha mikro. Penunjukan dilakukan tanpa mempertimbangkan penawaran pembanding dan harga satuan yang disepakati berada di atas harga pasar.',
                'kerugian' => 92000000, 'lokasi' => 'Bidang Koperasi dan UKM',
                'terlapor' => [['Pejabat Pengadaan', 'Pejabat Fungsional Pengadaan', 'pejabat_fungsional']],
                'hari' => 5, 'anonim' => true,
            ],
            [
                'kode' => 'TPK', 'opd' => 'DISTAN', 'status' => Laporan::STATUS_VERIFIKASI, 'prioritas' => 'tinggi',
                'judul' => 'Bantuan alat mesin pertanian tidak sampai kepada kelompok tani',
                'uraian' => 'Bantuan alat mesin pertanian yang dialokasikan bagi beberapa kelompok tani diduga tidak seluruhnya disalurkan. Berdasarkan berita acara serah terima, bantuan tercatat telah diterima, namun kelompok tani penerima menyatakan tidak pernah menerima alat tersebut. Terdapat dugaan pengalihan bantuan kepada pihak yang tidak berhak.',
                'kerugian' => 215000000, 'lokasi' => 'Kecamatan Banyumas dan Adiluwih',
                'terlapor' => [['Koordinator Penyuluh', 'Pejabat Fungsional', 'pejabat_fungsional']],
                'hari' => 12,
            ],
            [
                'kode' => 'LAINNYA', 'opd' => 'DLH', 'status' => Laporan::STATUS_DIPROSES, 'prioritas' => 'sedang',
                'judul' => 'Pengelolaan retribusi sampah yang tidak tercatat',
                'uraian' => 'Penerimaan retribusi pelayanan persampahan dari sejumlah pelaku usaha diduga tidak seluruhnya disetorkan ke kas daerah. Pembayaran dilakukan secara tunai tanpa disertai bukti setor resmi, sehingga selisih penerimaan tidak dapat ditelusuri. Kondisi ini telah berlangsung cukup lama tanpa koreksi dari unit pengawasan internal.',
                'kerugian' => 47500000, 'lokasi' => 'Bidang Pengelolaan Sampah DLH',
                'terlapor' => [['Petugas Pemungut Retribusi', 'Pelaksana', 'pelaksana']],
                'hari' => 34,
            ],
            [
                'kode' => 'PBJ', 'opd' => 'DINKES', 'status' => Laporan::STATUS_TERKIRIM, 'prioritas' => 'mendesak',
                'judul' => 'Pengadaan obat dan bahan habis pakai tidak sesuai spesifikasi',
                'uraian' => 'Pengadaan obat dan bahan medis habis pakai untuk beberapa puskesmas diduga tidak sesuai dengan spesifikasi kontrak. Sebagian barang yang diterima memiliki masa kedaluwarsa pendek dan merek berbeda dari yang tercantum dalam dokumen kontrak. Pemeriksaan penerimaan barang diduga dilakukan tanpa verifikasi fisik yang memadai.',
                'kerugian' => 156000000, 'lokasi' => 'Gudang Farmasi Dinas Kesehatan',
                'terlapor' => [
                    ['Panitia Penerima Hasil Pekerjaan', 'Tim Teknis', 'pejabat_fungsional'],
                    ['PT Sumber Medika Utama', 'Penyedia', 'pihak_ketiga'],
                ],
                'hari' => 1,
            ],
            [
                'kode' => 'PELAYANAN', 'opd' => 'KEC. PRINGSEWU', 'status' => Laporan::STATUS_SELESAI, 'prioritas' => 'rendah',
                'judul' => 'Keterlambatan penerbitan surat keterangan domisili',
                'uraian' => 'Pemohon surat keterangan domisili mengalami keterlambatan penerbitan hingga lebih dari dua minggu tanpa penjelasan yang memadai. Padahal berdasarkan standar pelayanan, dokumen tersebut seharusnya selesai dalam waktu tiga hari kerja. Pemohon harus datang berulang kali ke kantor kecamatan tanpa kepastian.',
                'kerugian' => null, 'lokasi' => 'Kantor Kecamatan Pringsewu',
                'terlapor' => [['Seksi Pemerintahan', 'Unit Kerja', 'lainnya']],
                'hari' => 120, 'selesaiHari' => 18,
                'kesimpulan' => 'Keterlambatan disebabkan penumpukan berkas dan tidak adanya petugas pengganti saat pejabat penanda tangan berhalangan hadir.',
                'rekomendasi' => 'Menetapkan pejabat pengganti penanda tangan dokumen serta menerapkan buku kendali penyelesaian berkas untuk memantau ketepatan waktu layanan.',
            ],
            [
                'kode' => 'WEWENANG', 'opd' => 'PERKIMTAN', 'status' => Laporan::STATUS_DIPROSES, 'prioritas' => 'tinggi',
                'judul' => 'Penggunaan kendaraan dinas untuk kepentingan pribadi',
                'uraian' => 'Sejumlah kendaraan dinas operasional diduga digunakan untuk kepentingan pribadi di luar hari dan jam kerja, termasuk untuk perjalanan luar daerah yang tidak berkaitan dengan tugas kedinasan. Biaya bahan bakar tetap dibebankan pada anggaran operasional perangkat daerah.',
                'kerugian' => 28000000, 'lokasi' => 'Dinas Perumahan, Kawasan Permukiman dan Pertanahan',
                'terlapor' => [['Oknum Pejabat Struktural', 'Kepala Bidang', 'pejabat_struktural']],
                'hari' => 21, 'anonim' => true,
            ],
            [
                'kode' => 'GRATIFIKASI', 'opd' => 'BKPSDM', 'status' => Laporan::STATUS_TERKIRIM, 'prioritas' => 'tinggi',
                'judul' => 'Dugaan praktik percaloan dalam pengurusan kenaikan pangkat',
                'uraian' => 'Beredar informasi mengenai adanya pihak yang menawarkan jasa percepatan pengurusan berkas kenaikan pangkat dengan imbalan sejumlah uang. Penawaran disampaikan melalui pesan pribadi kepada beberapa pegawai dengan menyebut memiliki akses langsung ke petugas pengelola kepegawaian. Terdapat tangkapan layar percakapan sebagai bukti awal.',
                'kerugian' => null, 'lokasi' => 'Bidang Mutasi dan Kepangkatan BKPSDM',
                'terlapor' => [['Oknum yang mengaku pegawai BKPSDM', 'Tidak diketahui', 'lainnya']],
                'hari' => 2,
            ],
        ];

        foreach ($contoh as $i => $baris) {
            $penulis = $pelapor[$i % $pelapor->count()];
            $dibuat = now()->subDays($baris['hari']);

            $laporan = Laporan::updateOrCreate(
                ['judul' => $baris['judul']],
                [
                    'nomor_tiket' => 'WBS-'.$dibuat->format('Ymd').'-'.str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                    'kode_akses' => Laporan::buatKodeAkses(),
                    'user_id' => $penulis->id,
                    'kategori_id' => $kategori[$baris['kode']],
                    'opd_id' => $opd[$baris['opd']] ?? null,
                    'uraian' => $baris['uraian'],
                    'lokasi_kejadian' => $baris['lokasi'],
                    'tanggal_kejadian' => $dibuat->copy()->subDays(rand(5, 40)),
                    'nilai_kerugian' => $baris['kerugian'],
                    'is_anonymous' => $baris['anonim'] ?? false,
                    'status' => $baris['status'],
                    'prioritas' => $baris['prioritas'],
                    'submitted_at' => $dibuat,
                    'created_at' => $dibuat,
                    'updated_at' => $dibuat,
                ]
            );

            $laporan->terlapor()->delete();
            foreach ($baris['terlapor'] as [$nama, $jabatan, $klasifikasi]) {
                $laporan->terlapor()->create([
                    'nama' => $nama,
                    'jabatan' => $jabatan,
                    'instansi' => $baris['opd'],
                    'klasifikasi' => $klasifikasi,
                ]);
            }

            $laporan->tindakLanjut()->delete();
            $this->bangunRiwayat($laporan, $baris, $dibuat, $admin, $petugas[$i % $petugas->count()]);
        }
    }

    /** Susun garis waktu penanganan agar data contoh terasa nyata. */
    private function bangunRiwayat(Laporan $laporan, array $baris, $dibuat, ?User $admin, User $petugas): void
    {
        $catat = function (string $tipe, string $judul, ?string $catatan, $waktu, ?User $oleh, bool $internal = false) use ($laporan) {
            $laporan->tindakLanjut()->create([
                'user_id' => $oleh?->id,
                'tipe' => $tipe,
                'judul' => $judul,
                'catatan' => $catatan,
                'is_internal' => $internal,
                'created_at' => $waktu,
                'updated_at' => $waktu,
            ]);
        };

        $catat('dikirim', 'Pengaduan dikirim ke Inspektorat',
            'Pengaduan menunggu verifikasi awal oleh Inspektorat Kabupaten Pringsewu.',
            $dibuat, $laporan->user);

        if ($laporan->status === Laporan::STATUS_TERKIRIM) {
            return;
        }

        $waktuVerifikasi = $dibuat->copy()->addDays(2);
        $catat('verifikasi', 'Pengaduan sedang diverifikasi',
            'Inspektorat sedang memeriksa kelengkapan dan kelayakan materi pengaduan.',
            $waktuVerifikasi, $admin);

        $laporan->forceFill(['verified_by' => $admin?->id, 'verified_at' => $waktuVerifikasi])->save();

        if ($laporan->status === Laporan::STATUS_VERIFIKASI) {
            return;
        }

        if ($laporan->status === Laporan::STATUS_DITOLAK) {
            $waktu = $dibuat->copy()->addDays($baris['selesaiHari'] ?? 7);
            $catat('ditolak', 'Pengaduan tidak dapat ditindaklanjuti', $baris['alasan'] ?? null, $waktu, $admin);
            $laporan->forceFill(['alasan_penolakan' => $baris['alasan'] ?? null, 'selesai_at' => $waktu])->save();

            return;
        }

        $waktuDisposisi = $dibuat->copy()->addDays(4);
        $catat('disposisi', "Didisposisikan kepada {$petugas->name}",
            'Penanganan dilaksanakan sesuai Program Kerja Pengawasan Tahunan.',
            $waktuDisposisi, $admin);

        $laporan->forceFill([
            'petugas_id' => $petugas->id,
            'deadline' => $waktuDisposisi->copy()->addDays(30)->toDateString(),
        ])->save();

        $catat('progres', 'Pengumpulan bahan dan keterangan',
            'Tim telah melaksanakan permintaan data pendukung kepada perangkat daerah terkait dan menjadwalkan permintaan keterangan.',
            $dibuat->copy()->addDays(9), $petugas);

        if ($laporan->status === Laporan::STATUS_DIPROSES) {
            $catat('catatan', 'Catatan internal tim pemeriksa',
                'Perlu konfirmasi silang dengan dokumen pertanggungjawaban tahun anggaran sebelumnya.',
                $dibuat->copy()->addDays(12), $petugas, true);

            return;
        }

        $waktuSelesai = $dibuat->copy()->addDays($baris['selesaiHari'] ?? 25);
        $catat('selesai', 'Penanganan pengaduan selesai', $baris['kesimpulan'] ?? null, $waktuSelesai, $petugas);

        $laporan->forceFill([
            'kesimpulan' => $baris['kesimpulan'] ?? null,
            'rekomendasi' => $baris['rekomendasi'] ?? null,
            'selesai_at' => $waktuSelesai,
        ])->save();
    }
}
