<?php

namespace Database\Seeders;

use App\Models\Opd;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OpdSeeder extends Seeder
{
    public function run(): void
    {
        $daftar = [
            // [nama, singkatan, jenis]
            ['Sekretariat Daerah Kabupaten Pringsewu', 'SETDA', 'sekretariat'],
            ['Sekretariat DPRD Kabupaten Pringsewu', 'SETWAN', 'sekretariat'],
            ['Inspektorat Kabupaten Pringsewu', 'INSPEKTORAT', 'inspektorat'],

            ['Dinas Pendidikan dan Kebudayaan', 'DISDIKBUD', 'dinas'],
            ['Dinas Kesehatan', 'DINKES', 'dinas'],
            ['Dinas Pekerjaan Umum dan Penataan Ruang', 'PUPR', 'dinas'],
            ['Dinas Perumahan, Kawasan Permukiman dan Pertanahan', 'PERKIMTAN', 'dinas'],
            ['Dinas Sosial', 'DINSOS', 'dinas'],
            ['Dinas Pemberdayaan Perempuan, Perlindungan Anak dan Keluarga Berencana', 'DP3AKB', 'dinas'],
            ['Dinas Ketahanan Pangan', 'DKP', 'dinas'],
            ['Dinas Lingkungan Hidup', 'DLH', 'dinas'],
            ['Dinas Kependudukan dan Pencatatan Sipil', 'DISDUKCAPIL', 'dinas'],
            ['Dinas Pemberdayaan Masyarakat dan Pekon', 'DPMP', 'dinas'],
            ['Dinas Perhubungan', 'DISHUB', 'dinas'],
            ['Dinas Komunikasi dan Informatika', 'DISKOMINFO', 'dinas'],
            ['Dinas Koperasi, UKM, Perindustrian dan Perdagangan', 'DISKOPERINDAG', 'dinas'],
            ['Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu', 'DPMPTSP', 'dinas'],
            ['Dinas Kepemudaan, Olahraga dan Pariwisata', 'DISPORAPAR', 'dinas'],
            ['Dinas Perpustakaan dan Kearsipan', 'DISPUSIP', 'dinas'],
            ['Dinas Pertanian', 'DISTAN', 'dinas'],
            ['Dinas Perikanan', 'DISKAN', 'dinas'],
            ['Dinas Tenaga Kerja', 'DISNAKER', 'dinas'],

            ['Badan Perencanaan Pembangunan Daerah', 'BAPPEDA', 'badan'],
            ['Badan Pengelolaan Keuangan dan Aset Daerah', 'BPKAD', 'badan'],
            ['Badan Pendapatan Daerah', 'BAPENDA', 'badan'],
            ['Badan Kepegawaian dan Pengembangan Sumber Daya Manusia', 'BKPSDM', 'badan'],
            ['Badan Penanggulangan Bencana Daerah', 'BPBD', 'badan'],
            ['Badan Kesatuan Bangsa dan Politik', 'KESBANGPOL', 'badan'],

            ['Satuan Polisi Pamong Praja dan Pemadam Kebakaran', 'SATPOL PP', 'satuan'],
            ['RSUD Pringsewu', 'RSUD', 'rsud'],

            ['Kecamatan Pringsewu', 'KEC. PRINGSEWU', 'kecamatan'],
            ['Kecamatan Gading Rejo', 'KEC. GADING REJO', 'kecamatan'],
            ['Kecamatan Ambarawa', 'KEC. AMBARAWA', 'kecamatan'],
            ['Kecamatan Pardasuka', 'KEC. PARDASUKA', 'kecamatan'],
            ['Kecamatan Pagelaran', 'KEC. PAGELARAN', 'kecamatan'],
            ['Kecamatan Pagelaran Utara', 'KEC. PAGELARAN UTARA', 'kecamatan'],
            ['Kecamatan Banyumas', 'KEC. BANYUMAS', 'kecamatan'],
            ['Kecamatan Adiluwih', 'KEC. ADILUWIH', 'kecamatan'],
            ['Kecamatan Sukoharjo', 'KEC. SUKOHARJO', 'kecamatan'],
        ];

        foreach ($daftar as [$nama, $singkatan, $jenis]) {
            Opd::updateOrCreate(
                ['slug' => Str::slug($nama)],
                [
                    'nama' => $nama,
                    'singkatan' => $singkatan,
                    'jenis' => $jenis,
                    'alamat' => 'Komplek Perkantoran Pemkab Pringsewu, Jl. Jenderal Sudirman, Pringsewu',
                    'email' => Str::slug($singkatan, '').'@pringsewukab.go.id',
                    'is_active' => true,
                ]
            );
        }
    }
}
