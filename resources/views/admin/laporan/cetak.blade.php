<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $laporan->nomor_tiket }} — Berkas Pengaduan WBS</title>

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 32px;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            font-size: 12px;
            line-height: 1.65;
            color: #14202f;
            background: #fff;
        }
        .lembar { max-width: 820px; margin: 0 auto; }
        .kop {
            display: flex; align-items: center; gap: 16px;
            border-bottom: 3px double #0f684b; padding-bottom: 16px;
        }
        .kop h1 { margin: 0; font-size: 17px; letter-spacing: .3px; }
        .kop p { margin: 2px 0 0; font-size: 11px; color: #4c5b7a; }
        .lambang {
            width: 56px; height: 56px; flex: none; border-radius: 12px;
            background: linear-gradient(135deg, #1fa271, #0e4434);
            color: #fff; display: grid; place-items: center;
            font-weight: 800; font-size: 15px; letter-spacing: .5px;
        }
        h2.tajuk {
            margin: 24px 0 4px; font-size: 15px; text-align: center;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .nomor { text-align: center; font-size: 12px; color: #4c5b7a; margin: 0 0 22px; }
        h3 {
            margin: 22px 0 8px; font-size: 12px; text-transform: uppercase;
            letter-spacing: .8px; color: #0f684b;
            border-bottom: 1px solid #d4d9e3; padding-bottom: 5px;
        }
        table { width: 100%; border-collapse: collapse; }
        table.data td { padding: 5px 0; vertical-align: top; }
        table.data td:first-child { width: 190px; color: #4c5b7a; }
        table.data td:nth-child(2) { width: 14px; }
        table.kisi { border: 1px solid #d4d9e3; margin-top: 6px; }
        table.kisi th, table.kisi td { border: 1px solid #d4d9e3; padding: 7px 9px; text-align: left; }
        table.kisi th { background: #f2f5f8; font-size: 11px; text-transform: uppercase; letter-spacing: .4px; }
        .uraian {
            white-space: pre-line; text-align: justify;
            background: #f7f9fb; border-left: 3px solid #1fa271;
            padding: 12px 14px; border-radius: 0 6px 6px 0;
        }
        .lencana {
            display: inline-block; padding: 3px 10px; border-radius: 999px;
            font-size: 10px; font-weight: 700; background: #e7f4ee; color: #0f684b;
        }
        ol.riwayat { margin: 6px 0 0; padding-left: 18px; }
        ol.riwayat li { margin-bottom: 9px; }
        ol.riwayat .waktu { color: #617394; font-size: 10.5px; }
        .ttd { margin-top: 44px; display: flex; justify-content: flex-end; }
        .ttd div { width: 260px; text-align: center; }
        .ttd .garis { margin-top: 62px; border-top: 1px solid #14202f; padding-top: 5px; }
        footer {
            margin-top: 34px; border-top: 1px solid #d4d9e3; padding-top: 10px;
            font-size: 10px; color: #8192ae; display: flex; justify-content: space-between;
        }
        .cetak-tombol {
            position: fixed; top: 16px; right: 16px; display: flex; gap: 8px;
        }
        .cetak-tombol button, .cetak-tombol a {
            padding: 9px 16px; border-radius: 10px; border: 0; cursor: pointer;
            font-size: 12px; font-weight: 700; text-decoration: none;
            font-family: inherit;
        }
        .cetak-tombol button { background: #12825b; color: #fff; }
        .cetak-tombol a { background: #eceef2; color: #374054; }
        @media print {
            body { padding: 0; }
            .cetak-tombol { display: none; }
            h3 { break-after: avoid; }
        }
    </style>
</head>
<body>

<div class="cetak-tombol">
    <button onclick="window.print()">Cetak / Simpan PDF</button>
    <a href="{{ route('admin.laporan.show', $laporan) }}">Kembali</a>
</div>

<div class="lembar">

    <div class="kop">
        <div class="lambang">WBS</div>
        <div>
            <h1>INSPEKTORAT KABUPATEN PRINGSEWU</h1>
            <p>Whistleblowing System &middot; Komplek Perkantoran Pemkab Pringsewu, Jl. Jenderal Sudirman, Pringsewu, Lampung 35373</p>
            <p>Telepon (0729) 700 xxx &middot; inspektorat@pringsewukab.go.id</p>
        </div>
    </div>

    <h2 class="tajuk">Berkas Pengaduan Whistleblowing System</h2>
    <p class="nomor">Nomor Tiket: <strong>{{ $laporan->nomor_tiket }}</strong></p>

    <h3>A. Identitas Pengaduan</h3>
    <table class="data">
        <tr><td>Judul Pengaduan</td><td>:</td><td><strong>{{ $laporan->judul }}</strong></td></tr>
        <tr><td>Kategori</td><td>:</td><td>{{ $laporan->kategori->nama }} ({{ $laporan->kategori->kode }})</td></tr>
        <tr><td>Status</td><td>:</td><td><span class="lencana">{{ $laporan->status_label }}</span></td></tr>
        <tr><td>Prioritas</td><td>:</td><td>{{ $laporan->prioritas_label }}</td></tr>
        <tr><td>Tanggal Pengaduan</td><td>:</td><td>{{ $laporan->submitted_at?->translatedFormat('d F Y, H:i') }} WIB</td></tr>
        <tr><td>OPD Terlapor</td><td>:</td><td>{{ $laporan->opd?->nama ?? 'Tidak disebutkan' }}</td></tr>
        <tr><td>Lokasi Kejadian</td><td>:</td><td>{{ $laporan->lokasi_kejadian ?: 'Tidak disebutkan' }}</td></tr>
        <tr><td>Tanggal Kejadian</td><td>:</td><td>{{ $laporan->tanggal_kejadian?->translatedFormat('d F Y') ?? 'Tidak disebutkan' }}</td></tr>
        <tr>
            <td>Perkiraan Kerugian</td><td>:</td>
            <td>{{ $laporan->nilai_kerugian ? 'Rp '.number_format((float) $laporan->nilai_kerugian, 0, ',', '.') : 'Tidak ada' }}</td>
        </tr>
    </table>

    <h3>B. Identitas Pelapor</h3>
    @if ($laporan->is_anonymous)
        <p><em>Pelapor memilih menyampaikan pengaduan secara anonim. Identitas dirahasiakan sesuai ketentuan perlindungan pelapor.</em></p>
    @else
        <table class="data">
            <tr><td>Nama</td><td>:</td><td>{{ $laporan->user?->name }}</td></tr>
            <tr><td>NIP</td><td>:</td><td>{{ $laporan->user?->nip ?: '—' }}</td></tr>
            <tr><td>Jabatan</td><td>:</td><td>{{ $laporan->user?->jabatan ?: '—' }}</td></tr>
            <tr><td>Perangkat Daerah</td><td>:</td><td>{{ $laporan->user?->opd?->nama ?? '—' }}</td></tr>
            <tr><td>Email</td><td>:</td><td>{{ $laporan->user?->email }}</td></tr>
            <tr><td>Telepon</td><td>:</td><td>{{ $laporan->user?->telepon ?: '—' }}</td></tr>
        </table>
    @endif

    <h3>C. Uraian Pengaduan</h3>
    <div class="uraian">{{ $laporan->uraian }}</div>

    <h3>D. Pihak yang Diduga Terlibat</h3>
    <table class="kisi">
        <thead>
            <tr><th style="width:32px">No</th><th>Nama</th><th>Jabatan</th><th>Instansi</th><th>Klasifikasi</th></tr>
        </thead>
        <tbody>
            @forelse ($laporan->terlapor as $i => $pihak)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $pihak->nama }}</td>
                    <td>{{ $pihak->jabatan ?: '—' }}</td>
                    <td>{{ $pihak->instansi ?: '—' }}</td>
                    <td>{{ $pihak->klasifikasi_label }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>E. Lampiran</h3>
    <table class="kisi">
        <thead>
            <tr><th style="width:32px">No</th><th>Nama Berkas</th><th style="width:90px">Ukuran</th><th>Keterangan</th></tr>
        </thead>
        <tbody>
            @forelse ($laporan->lampiran as $i => $berkas)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $berkas->nama_file }}</td>
                    <td>{{ $berkas->ukuran_terbaca }}</td>
                    <td>{{ $berkas->keterangan ?: ($berkas->is_internal ? 'Dokumen internal' : '—') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center">Tidak ada lampiran</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>F. Riwayat Penanganan</h3>
    <ol class="riwayat">
        @foreach ($laporan->tindakLanjut->sortBy('created_at') as $riwayat)
            <li>
                <strong>{{ $riwayat->judul }}</strong>
                @if ($riwayat->is_internal) <em>(internal)</em> @endif
                <br>
                @if ($riwayat->catatan)
                    {{ $riwayat->catatan }}<br>
                @endif
                <span class="waktu">
                    {{ $riwayat->user?->name ?? 'Sistem' }} &middot;
                    {{ $riwayat->created_at->translatedFormat('d F Y, H:i') }} WIB
                </span>
            </li>
        @endforeach
    </ol>

    @if ($laporan->kesimpulan || $laporan->alasan_penolakan)
        <h3>G. Hasil Penanganan</h3>
        @if ($laporan->kesimpulan)
            <table class="data">
                <tr><td>Kesimpulan</td><td>:</td><td style="text-align:justify">{{ $laporan->kesimpulan }}</td></tr>
                @if ($laporan->rekomendasi)
                    <tr><td>Rekomendasi</td><td>:</td><td style="text-align:justify">{{ $laporan->rekomendasi }}</td></tr>
                @endif
                <tr><td>Tanggal Selesai</td><td>:</td><td>{{ $laporan->selesai_at?->translatedFormat('d F Y') }}</td></tr>
            </table>
        @else
            <table class="data">
                <tr><td>Alasan Penolakan</td><td>:</td><td style="text-align:justify">{{ $laporan->alasan_penolakan }}</td></tr>
            </table>
        @endif
    @endif

    <div class="ttd">
        <div>
            <p style="margin:0">Pringsewu, {{ now()->translatedFormat('d F Y') }}</p>
            <p style="margin:0">Petugas Penanggung Jawab,</p>
            <div class="garis">
                <strong>{{ $laporan->petugas?->name ?? '……………………………………' }}</strong><br>
                <span style="font-size:11px">{{ $laporan->petugas?->jabatan ?? 'Inspektorat Kabupaten Pringsewu' }}</span>
            </div>
        </div>
    </div>

    <footer>
        <span>Dicetak oleh {{ auth()->user()->name }} pada {{ now()->translatedFormat('d F Y, H:i') }} WIB</span>
        <span>Dokumen bersifat rahasia &middot; WBS Inspektorat Kabupaten Pringsewu</span>
    </footer>
</div>

</body>
</html>
