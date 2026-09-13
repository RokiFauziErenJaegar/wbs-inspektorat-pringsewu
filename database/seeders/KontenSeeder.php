<?php

namespace Database\Seeders;

use App\Models\Artikel;
use App\Models\Faq;
use App\Models\User;
use Illuminate\Database\Seeder;

class KontenSeeder extends Seeder
{
    public function run(): void
    {
        $faq = [
            ['umum', 'Apa itu Whistleblowing System (WBS)?', 'WBS adalah aplikasi yang disediakan Inspektorat Kabupaten Pringsewu bagi pegawai maupun masyarakat untuk melaporkan dugaan pelanggaran yang terjadi di lingkungan Pemerintah Kabupaten Pringsewu. Laporan disampaikan secara daring dan ditangani langsung oleh Inspektorat.'],
            ['umum', 'Siapa yang dapat menyampaikan pengaduan?', 'Seluruh Organisasi Perangkat Daerah (OPD) di lingkungan Pemerintah Kabupaten Pringsewu melalui pegawainya, serta masyarakat yang memiliki informasi mengenai dugaan pelanggaran. Pelapor perlu mendaftarkan akun terlebih dahulu agar status pengaduan dapat dipantau.'],
            ['umum', 'Apa saja yang dapat dilaporkan melalui WBS?', 'Dugaan tindak pidana korupsi, gratifikasi dan suap, pungutan liar, penyimpangan pengadaan barang dan jasa, penyalahgunaan wewenang, pelanggaran disiplin ASN, buruknya pelayanan publik, serta benturan kepentingan.'],

            ['kerahasiaan', 'Apakah identitas saya dirahasiakan?', 'Ya. Identitas pelapor hanya dapat diakses oleh petugas Inspektorat yang berwenang menangani pengaduan dan dilindungi sesuai peraturan perundang-undangan. Anda juga dapat memilih opsi anonim ketika mengirim pengaduan.'],
            ['kerahasiaan', 'Apa konsekuensi memilih pengaduan anonim?', 'Dengan memilih anonim, identitas Anda tidak ditampilkan kepada petugas. Namun Inspektorat menjadi terbatas dalam meminta klarifikasi atau data tambahan, sehingga proses pendalaman materi aduan dapat berjalan lebih lambat.'],
            ['kerahasiaan', 'Apakah saya akan dilindungi dari tindakan balasan?', 'Inspektorat menjamin tidak ada tindakan balasan terhadap pelapor yang beritikad baik. Bila Anda mengalami intimidasi setelah melapor, segera sampaikan melalui fitur pesan pada pengaduan Anda.'],

            ['proses', 'Berapa lama pengaduan saya diproses?', 'Verifikasi awal dilakukan paling lambat 5 hari kerja sejak pengaduan diterima. Bila memenuhi syarat, pengaduan dilanjutkan ke tahap penanganan dengan jangka waktu menyesuaikan tingkat kerumitan kasus.'],
            ['proses', 'Bagaimana cara memantau status pengaduan?', 'Masuk ke akun Anda lalu buka menu Pengaduan Saya. Setiap perubahan status dan catatan perkembangan dari Inspektorat akan tampil pada garis waktu pengaduan. Anda juga dapat menggunakan menu Lacak Aduan dengan nomor tiket dan kode akses.'],
            ['proses', 'Apa yang membuat pengaduan ditolak?', 'Pengaduan dapat ditolak apabila tidak memuat informasi yang jelas, tidak disertai data pendukung yang memadai, bukan kewenangan Inspektorat, atau telah ditangani oleh instansi penegak hukum lain.'],

            ['teknis', 'Dokumen apa saja yang sebaiknya dilampirkan?', 'Lampirkan bukti pendukung seperti dokumen kontrak, kuitansi, foto, tangkapan layar percakapan, rekaman, atau dokumen lain yang relevan. Format yang didukung antara lain PDF, DOC, XLS, PPT, JPG, PNG, ZIP, RAR, MP3, dan MP4 dengan ukuran maksimal 50 MB per berkas.'],
            ['teknis', 'Saya lupa kata sandi, bagaimana?', 'Gunakan tautan Lupa kata sandi pada halaman masuk. Sistem akan mengirimkan tautan pengaturan ulang ke alamat surel yang terdaftar.'],
        ];

        foreach ($faq as $urutan => [$kelompok, $pertanyaan, $jawaban]) {
            Faq::updateOrCreate(
                ['pertanyaan' => $pertanyaan],
                ['jawaban' => $jawaban, 'kelompok' => $kelompok, 'urutan' => $urutan + 1, 'is_active' => true]
            );
        }

        $penulis = User::where('role', User::ROLE_ADMIN)->first();

        $artikel = [
            [
                'judul' => 'Inspektorat Pringsewu Resmi Luncurkan Whistleblowing System',
                'ringkasan' => 'Inspektorat Kabupaten Pringsewu meluncurkan kanal pelaporan daring untuk memperkuat pengawasan internal dan mempercepat penanganan dugaan pelanggaran.',
                'konten' => '<p>Inspektorat Kabupaten Pringsewu resmi meluncurkan <strong>Whistleblowing System (WBS)</strong> sebagai kanal resmi pelaporan dugaan pelanggaran di lingkungan Pemerintah Kabupaten Pringsewu. Peluncuran ini menjadi bagian dari upaya penguatan pengawasan internal serta perwujudan tata kelola pemerintahan yang bersih dan akuntabel.</p><p>Melalui sistem ini, seluruh Organisasi Perangkat Daerah (OPD) dapat menyampaikan pengaduan secara daring kapan saja. Setiap pengaduan yang masuk langsung tercatat pada dasbor Inspektorat untuk kemudian diverifikasi dan ditindaklanjuti sesuai ketentuan.</p><h2>Jaminan Kerahasiaan Pelapor</h2><p>Inspektorat menegaskan bahwa identitas pelapor dijamin kerahasiaannya. Pelapor juga diberi opsi menyampaikan pengaduan secara anonim apabila merasa perlu.</p><p>"Kami ingin setiap pegawai merasa aman ketika menyampaikan informasi mengenai dugaan penyimpangan. Tidak boleh ada tindakan balasan terhadap pelapor yang beritikad baik," ujar Inspektur Kabupaten Pringsewu.</p><h2>Alur Penanganan</h2><p>Pengaduan yang masuk akan melalui tahapan verifikasi awal, penelaahan materi, penugasan kepada tim auditor, hingga penyusunan kesimpulan dan rekomendasi. Seluruh tahapan dapat dipantau langsung oleh pelapor melalui akunnya masing-masing.</p>',
            ],
            [
                'judul' => 'Enam Hal yang Perlu Disiapkan Sebelum Menyampaikan Pengaduan',
                'ringkasan' => 'Pengaduan yang lengkap mempercepat proses penanganan. Berikut hal-hal yang sebaiknya Anda siapkan sebelum mengirim laporan melalui WBS.',
                'konten' => '<p>Kualitas sebuah pengaduan sangat menentukan kecepatan penanganannya. Inspektorat Kabupaten Pringsewu merangkum enam hal yang sebaiknya disiapkan pelapor.</p><ol><li><strong>Kronologi kejadian</strong> — uraikan peristiwa secara runut mulai dari waktu, tempat, hingga rangkaian kejadiannya.</li><li><strong>Identitas pihak terlapor</strong> — cantumkan nama, jabatan, dan unit kerja pihak yang diduga terlibat.</li><li><strong>Bukti pendukung</strong> — siapkan dokumen, foto, tangkapan layar, atau rekaman yang relevan.</li><li><strong>Perkiraan nilai kerugian</strong> — khusus untuk dugaan kerugian keuangan daerah.</li><li><strong>Lokasi kejadian</strong> — sebutkan unit kerja atau lokasi spesifik terjadinya peristiwa.</li><li><strong>Data kontak yang aktif</strong> — agar Inspektorat dapat meminta klarifikasi bila diperlukan.</li></ol><p>Pengaduan yang tidak disertai informasi memadai berpotensi tidak dapat ditindaklanjuti.</p>',
            ],
            [
                'judul' => 'Mengenal Perbedaan Gratifikasi, Suap, dan Pungutan Liar',
                'ringkasan' => 'Tiga istilah ini kerap tertukar. Memahami perbedaannya membantu pegawai mengenali potensi pelanggaran di lingkungan kerja.',
                'konten' => '<p>Dalam praktik pengawasan internal, tiga istilah berikut sering dianggap sama padahal memiliki karakteristik yang berbeda.</p><h2>Gratifikasi</h2><p>Pemberian dalam arti luas kepada pegawai negeri yang berhubungan dengan jabatannya, baik berupa uang, barang, fasilitas, maupun perjalanan wisata. Gratifikasi wajib dilaporkan dalam jangka waktu yang ditentukan.</p><h2>Suap</h2><p>Pemberian yang disertai kesepakatan atau maksud tertentu agar penerima melakukan atau tidak melakukan sesuatu yang bertentangan dengan kewajibannya.</p><h2>Pungutan Liar</h2><p>Penarikan biaya di luar ketentuan resmi atas suatu pelayanan publik, misalnya pengurusan izin, administrasi kependudukan, atau layanan pendidikan.</p><blockquote>Ketiganya termasuk dalam ruang lingkup pengaduan yang dapat disampaikan melalui WBS Inspektorat Kabupaten Pringsewu.</blockquote>',
            ],
            [
                'judul' => 'Inspektorat Perkuat Sinergi Pengawasan dengan Seluruh OPD',
                'ringkasan' => 'Rapat koordinasi pengawasan internal menegaskan komitmen bersama seluruh perangkat daerah dalam pencegahan penyimpangan.',
                'konten' => '<p>Inspektorat Kabupaten Pringsewu menggelar rapat koordinasi pengawasan bersama seluruh Organisasi Perangkat Daerah. Kegiatan ini menjadi wadah penyamaan persepsi mengenai pencegahan penyimpangan serta pemanfaatan Whistleblowing System.</p><p>Dalam kesempatan tersebut, seluruh OPD didorong untuk mendaftarkan operator pada aplikasi WBS agar pelaporan dugaan pelanggaran dapat berjalan cepat dan terdokumentasi dengan baik.</p><h2>Fokus Pengawasan</h2><ul><li>Belanja modal dan pengadaan barang/jasa</li><li>Pengelolaan aset daerah</li><li>Penyaluran bantuan sosial</li><li>Kualitas pelayanan publik</li></ul><p>Inspektorat berkomitmen menindaklanjuti setiap pengaduan yang memenuhi syarat secara objektif dan profesional.</p>',
            ],
        ];

        foreach ($artikel as $i => $baris) {
            Artikel::updateOrCreate(
                ['judul' => $baris['judul']],
                $baris + [
                    'user_id' => $penulis?->id,
                    'is_published' => true,
                    'published_at' => now()->subDays(($i + 1) * 6),
                    'sumber' => 'Humas Inspektorat Kabupaten Pringsewu',
                ]
            );
        }
    }
}
