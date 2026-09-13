# WBS — Whistleblowing System Inspektorat Kabupaten Pringsewu

Aplikasi pelaporan dugaan pelanggaran (whistleblowing) di lingkungan Pemerintah Kabupaten
Pringsewu. Setiap Organisasi Perangkat Daerah (OPD) mendaftarkan akun, masuk ke sistem,
lalu menyampaikan pengaduan. Seluruh pengaduan masuk ke dasbor Inspektorat untuk
diverifikasi, didisposisikan, ditindaklanjuti, hingga terbit kesimpulan dan rekomendasi.

Dibangun dengan **Laravel 13**, **Tailwind CSS v4**, **Alpine.js**, dan **Chart.js**.

---

## Alur Sistem

```
OPD daftar akun  ─►  masuk  ─►  pilih kategori  ─►  isi formulir  ─►  kirim
                                                                        │
                                                                        ▼
                                              ┌─────────  Dasbor Inspektorat  ─────────┐
                                              │                                        │
                                     Menunggu Verifikasi ─► Sedang Diverifikasi         │
                                              │                     │                  │
                                              │            ┌────────┴────────┐         │
                                              │            ▼                 ▼         │
                                              │      Dalam Tindak Lanjut   Ditolak      │
                                              │       (disposisi petugas)              │
                                              │            │                           │
                                              │            ▼                           │
                                              └──────►  Selesai  ◄─────────────────────┘
                                                     (kesimpulan + rekomendasi)
```

Pelapor memantau setiap perubahan status melalui garis waktu pengaduan, notifikasi,
serta kanal pesan dua arah dengan Inspektorat.

---

## Peran Pengguna

| Peran | Hak akses |
|---|---|
| **Pelapor (OPD)** | Mendaftar mandiri, membuat draf & mengirim pengaduan, mengunggah lampiran, memantau status, berkirim pesan dengan Inspektorat |
| **Petugas Inspektorat** | Menangani berkas yang didisposisikan kepadanya: verifikasi, catat perkembangan, unggah dokumen internal, selesaikan berkas |
| **Administrator Inspektorat** | Seluruh kewenangan petugas + disposisi, buka kembali berkas, statistik penuh, dan seluruh master data |

---

## Fitur

**Halaman publik**
- Beranda dengan statistik langsung, ruang lingkup pengaduan, alur sistem, dan dasar hukum
- Panduan **Cara Melapor** lengkap dengan kriteria pengaduan yang dapat/tidak dapat diproses
- **Lacak Aduan** publik memakai nomor tiket + kode akses (tanpa perlu masuk)
- Berita/publikasi dan FAQ terkelompok
- Mode terang & gelap, responsif hingga layar ponsel

**Area pelapor**
- Dasbor ringkasan + grafik tren dan komposisi status
- Formulir pengaduan dua langkah: pilih kategori → isi rincian
- Baris dinamis pihak terlapor, multi-lampiran (maks. 50 MB/berkas, 10 berkas)
- Simpan sebagai draf, kirim, ubah/hapus draf
- Opsi pengaduan **anonim**
- Garis waktu penanganan, pesan dua arah, notifikasi

**Area Inspektorat**
- Dasbor: antrean verifikasi, berkas prioritas, jatuh tempo, beban kerja petugas
- Daftar pengaduan dengan penyaring status, kategori, OPD, prioritas, petugas, rentang tanggal
- Aksi: verifikasi, terima & disposisi, tolak, catat perkembangan (publik/internal),
  selesaikan, buka kembali, ubah prioritas
- Unggah dokumen internal (tidak terlihat pelapor)
- Cetak berkas pengaduan (format resmi, siap PDF)
- Statistik & rekapitulasi tahunan + ekspor CSV
- Master data: OPD, kategori, pengguna, berita, FAQ

---

## Kebutuhan Sistem

- PHP 8.2+ (ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`, `zip`)
- MySQL / MariaDB 10.4+
- Composer 2.x
- Node.js 20+ (hanya untuk membangun aset)

---

## Instalasi

```bash
# 1. Dependensi
composer install
npm install

# 2. Konfigurasi
cp .env.example .env
php artisan key:generate
```

Sesuaikan koneksi basis data pada `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wbs_pringsewu
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# 3. Buat basis data lalu jalankan migrasi + data awal
php artisan migrate --seed

# 4. Tautan penyimpanan berkas (lampiran, avatar, gambar berita)
php artisan storage:link

# 5. Bangun aset front-end
npm run build      # produksi
# npm run dev      # pengembangan (hot reload)

# 6. Jalankan
php artisan serve
```

Buka `http://localhost:8000`.

### Menjalankan di XAMPP

Arahkan *DocumentRoot* virtual host ke folder **`public/`** pada proyek ini,
bukan ke folder akarnya.

---

## Akun Bawaan

| Peran | Username | Kata Sandi |
|---|---|---|
| Administrator | `admin` | `admin12345` |
| Petugas | `petugas1`, `petugas2`, `petugas3` | `petugas12345` |
| Pelapor OPD | `opd.disdikbud`, `opd.dinkes`, `opd.pupr`, `opd.dpmptsp`, `opd.kecpringsewu` | `pelapor12345` |

Masuk dapat menggunakan **username maupun email**.

> **Ganti seluruh kata sandi bawaan sebelum digunakan di lingkungan produksi.**

---

## Data Awal

Seeder mengisi 39 OPD Kabupaten Pringsewu, 9 kategori pengaduan, akun contoh,
11 FAQ, 4 berita, serta 14 pengaduan contoh yang tersebar pada seluruh status
agar dasbor dan statistik langsung terlihat berisi.

Untuk mengosongkan data pengaduan contoh dan memulai bersih:

```bash
php artisan migrate:fresh
php artisan db:seed --class=OpdSeeder
php artisan db:seed --class=KategoriSeeder
php artisan db:seed --class=UserSeeder
```

---

## Pengujian

```bash
php artisan test
```

Berisi uji asap seluruh halaman per peran serta uji alur penuh pengaduan
(pendaftaran → kirim → verifikasi → disposisi → tindak lanjut → selesai),
termasuk pemeriksaan hak akses antar peran dan kerahasiaan pengaduan anonim.

Pengujian memakai basis data terpisah `wbs_pringsewu_test` (lihat `phpunit.xml`).

---

## Struktur Penting

```
app/
├── Http/Controllers/
│   ├── Admin/        Dasbor, laporan, statistik, master data Inspektorat
│   ├── Auth/         Masuk, daftar, pemulihan kata sandi
│   ├── Pelapor/      Dasbor dan pengaduan milik OPD
│   └── PublicController.php
├── Models/           Laporan, Terlapor, Lampiran, TindakLanjut, Pesan, Opd, Kategori, …
└── Services/
    └── LaporanService.php    Pencatatan garis waktu + notifikasi

resources/views/
├── components/       Komponen Blade (ikon, input, modal, kartu statistik, …)
├── layouts/          publik, app (dasbor), auth
├── publik/           Beranda, cara melapor, berita, FAQ, lacak
├── pelapor/          Dasbor & pengaduan
└── admin/            Dasbor, laporan, statistik, master data
```

---

## Catatan Keamanan

- Identitas pelapor hanya terbuka bagi petugas berwenang; opsi anonim menyembunyikannya sepenuhnya
- Catatan internal Inspektorat tidak pernah ditampilkan kepada pelapor
- Petugas hanya dapat membuka berkas yang didisposisikan kepadanya
- Pembatasan laju (5 percobaan) pada halaman masuk
- Lampiran disimpan di `storage/app/public` dan diunduh melalui rute terautentikasi

---

&copy; Inspektorat Kabupaten Pringsewu — Whistleblowing System v1.0
