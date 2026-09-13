<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $daftar = [
            [
                'kode' => 'TPK',
                'nama' => 'Tindak Pidana Korupsi',
                'icon' => 'gavel',
                'warna' => 'rose',
                'deskripsi' => 'Dugaan korupsi, penggelapan, mark-up anggaran, atau perbuatan yang merugikan keuangan daerah.',
                'petunjuk' => 'Jelaskan kronologi kejadian, perkiraan nilai kerugian negara, serta pihak yang diduga terlibat selengkap mungkin.',
            ],
            [
                'kode' => 'GRATIFIKASI',
                'nama' => 'Gratifikasi dan Suap',
                'icon' => 'gift',
                'warna' => 'amber',
                'deskripsi' => 'Pemberian hadiah, uang, fasilitas, atau janji yang berkaitan dengan jabatan pegawai.',
                'petunjuk' => 'Sebutkan bentuk pemberian, nilainya, waktu penerimaan, serta kaitannya dengan jabatan penerima.',
            ],
            [
                'kode' => 'PUNGLI',
                'nama' => 'Pungutan Liar',
                'icon' => 'banknote',
                'warna' => 'orange',
                'deskripsi' => 'Pungutan di luar ketentuan pada pelayanan perizinan, kepegawaian, pendidikan, atau layanan publik lainnya.',
                'petunjuk' => 'Cantumkan jenis layanan, besaran pungutan, lokasi, dan bukti pendukung bila tersedia.',
            ],
            [
                'kode' => 'PBJ',
                'nama' => 'Pengadaan Barang dan Jasa',
                'icon' => 'clipboard',
                'warna' => 'indigo',
                'deskripsi' => 'Persekongkolan tender, pengaturan pemenang, spesifikasi fiktif, atau pekerjaan tidak sesuai kontrak.',
                'petunjuk' => 'Sertakan nama paket pekerjaan, tahun anggaran, nilai kontrak, dan penyedia yang terlibat.',
            ],
            [
                'kode' => 'WEWENANG',
                'nama' => 'Penyalahgunaan Wewenang',
                'icon' => 'scale',
                'warna' => 'violet',
                'deskripsi' => 'Penggunaan jabatan di luar kewenangan, intervensi keputusan, atau pemanfaatan aset daerah untuk kepentingan pribadi.',
                'petunjuk' => 'Uraikan bentuk penyalahgunaan wewenang beserta dampak yang ditimbulkan.',
            ],
            [
                'kode' => 'KEPEGAWAIAN',
                'nama' => 'Kepegawaian dan Disiplin ASN',
                'icon' => 'users',
                'warna' => 'sky',
                'deskripsi' => 'Pelanggaran disiplin, absensi fiktif, jual beli jabatan, atau perbuatan tidak etis pegawai.',
                'petunjuk' => 'Sebutkan nama dan jabatan pegawai yang dilaporkan serta bentuk pelanggarannya.',
            ],
            [
                'kode' => 'PELAYANAN',
                'nama' => 'Pelayanan Publik',
                'icon' => 'building',
                'warna' => 'teal',
                'deskripsi' => 'Pelayanan yang berbelit, diskriminatif, tidak sesuai standar, atau tidak transparan.',
                'petunjuk' => 'Jelaskan jenis layanan, unit kerja, waktu kejadian, dan kronologi pelayanan yang Anda alami.',
            ],
            [
                'kode' => 'KEPENTINGAN',
                'nama' => 'Benturan Kepentingan',
                'icon' => 'briefcase',
                'warna' => 'emerald',
                'deskripsi' => 'Rangkap jabatan, hubungan keluarga, atau kepentingan pribadi yang memengaruhi keputusan jabatan.',
                'petunjuk' => 'Uraikan hubungan atau kepentingan yang berpotensi memengaruhi objektivitas keputusan.',
            ],
            [
                'kode' => 'LAINNYA',
                'nama' => 'Pelanggaran Lainnya',
                'icon' => 'document',
                'warna' => 'slate',
                'deskripsi' => 'Dugaan pelanggaran lain di lingkungan Pemerintah Kabupaten Pringsewu yang belum tercakup kategori di atas.',
                'petunjuk' => 'Uraikan kejadian selengkap mungkin agar Inspektorat dapat menentukan klasifikasi yang tepat.',
            ],
        ];

        foreach ($daftar as $urutan => $baris) {
            Kategori::updateOrCreate(
                ['kode' => $baris['kode']],
                $baris + ['urutan' => $urutan + 1, 'is_active' => true]
            );
        }
    }
}
