<?php

namespace Database\Seeders;

use App\Models\Opd;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $inspektorat = Opd::where('singkatan', 'INSPEKTORAT')->first();

        User::updateOrCreate(['username' => 'admin'], [
            'name' => 'Administrator Inspektorat',
            'email' => 'admin@pringsewukab.go.id',
            'password' => 'admin12345',
            'role' => User::ROLE_ADMIN,
            'opd_id' => $inspektorat?->id,
            'nip' => '198203152006041003',
            'jabatan' => 'Inspektur Kabupaten Pringsewu',
            'telepon' => '081234567890',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $petugas = [
            ['petugas1', 'Rahmawati Dewi, S.E., M.Ak.', 'Auditor Madya — Irban Wilayah I'],
            ['petugas2', 'Ahmad Fauzan, S.H.', 'Auditor Muda — Irban Wilayah II'],
            ['petugas3', 'Siti Nurhaliza, S.Sos.', 'P2UPD — Irban Wilayah III'],
        ];

        foreach ($petugas as $i => [$username, $nama, $jabatan]) {
            User::updateOrCreate(['username' => $username], [
                'name' => $nama,
                'email' => $username.'@pringsewukab.go.id',
                'password' => 'petugas12345',
                'role' => User::ROLE_PETUGAS,
                'opd_id' => $inspektorat?->id,
                'nip' => '19870'.($i + 1).'102010012'.($i + 1).'00',
                'jabatan' => $jabatan,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        $pelapor = [
            ['opd.disdikbud', 'Budi Santoso, S.Pd.', 'DISDIKBUD', 'Kepala Sub Bagian Umum dan Kepegawaian'],
            ['opd.dinkes', 'dr. Maya Puspita', 'DINKES', 'Kepala Seksi Pelayanan Kesehatan'],
            ['opd.pupr', 'Ir. Hendra Gunawan', 'PUPR', 'Kepala Bidang Bina Marga'],
            ['opd.dpmptsp', 'Rina Marlina, S.A.P.', 'DPMPTSP', 'Analis Perizinan'],
            ['opd.kecpringsewu', 'Agus Setiawan, S.I.P.', 'KEC. PRINGSEWU', 'Sekretaris Kecamatan'],
        ];

        foreach ($pelapor as [$username, $nama, $singkatan, $jabatan]) {
            User::updateOrCreate(['username' => $username], [
                'name' => $nama,
                'email' => str_replace('.', '_', $username).'@pringsewukab.go.id',
                'password' => 'pelapor12345',
                'role' => User::ROLE_PELAPOR,
                'opd_id' => Opd::where('singkatan', $singkatan)->value('id'),
                'jabatan' => $jabatan,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }
    }
}
