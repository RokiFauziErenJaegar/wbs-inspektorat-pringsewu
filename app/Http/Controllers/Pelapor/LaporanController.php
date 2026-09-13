<?php

namespace App\Http\Controllers\Pelapor;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Lampiran;
use App\Models\Laporan;
use App\Models\Opd;
use App\Models\Terlapor;
use App\Services\LaporanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    /** Ekstensi yang diterima sistem, mengikuti standar WBS pemerintah. */
    private const EKSTENSI = 'zip,rar,doc,docx,xls,xlsx,ppt,pptx,pdf,jpg,jpeg,png,gif,mp3,mp4,mov,3gp';

    private const MAKS_KB = 51200; // 50 MB per berkas

    public function __construct(private readonly LaporanService $service) {}

    public function index(Request $request): View
    {
        $laporan = Laporan::query()
            ->where('user_id', $request->user()->id)
            ->with(['kategori', 'opd'])
            ->when($request->string('status')->toString(), fn ($q, $s) => $q->where('status', $s))
            ->when($request->string('kategori')->toString(), fn ($q, $k) => $q->where('kategori_id', $k))
            ->cari($request->string('q')->toString())
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pelapor.laporan.index', [
            'laporan' => $laporan,
            'kategoriList' => Kategori::aktif()->urut()->get(),
        ]);
    }

    /** Langkah 1: pilih tujuan/kategori pengaduan. */
    public function pilihKategori(): View
    {
        return view('pelapor.laporan.pilih-kategori', [
            'kategori' => Kategori::aktif()->urut()->get(),
        ]);
    }

    /** Langkah 2: formulir pengaduan. */
    public function create(Request $request): View|RedirectResponse
    {
        $kategori = Kategori::aktif()->where('slug', $request->string('kategori')->toString())->first();

        if (! $kategori) {
            return redirect()->route('pelapor.laporan.pilih-kategori');
        }

        return view('pelapor.laporan.form', [
            'laporan' => new Laporan(['kategori_id' => $kategori->id]),
            'kategori' => $kategori,
            'opdList' => Opd::aktif()->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $kirim = $request->input('aksi') === 'kirim';
        $data = $this->validasi($request, $kirim);

        $laporan = DB::transaction(function () use ($request, $data, $kirim) {
            $laporan = Laporan::create([
                'nomor_tiket' => Laporan::buatNomorTiket(),
                'kode_akses' => Laporan::buatKodeAkses(),
                'user_id' => $request->user()->id,
                'kategori_id' => $data['kategori_id'],
                'opd_id' => $data['opd_id'] ?? null,
                'judul' => $data['judul'],
                'uraian' => $data['uraian'],
                'lokasi_kejadian' => $data['lokasi_kejadian'] ?? null,
                'tanggal_kejadian' => $data['tanggal_kejadian'] ?? null,
                'nilai_kerugian' => $data['nilai_kerugian'] ?? null,
                'is_anonymous' => $request->boolean('is_anonymous'),
                'status' => $kirim ? Laporan::STATUS_TERKIRIM : Laporan::STATUS_DRAFT,
                'submitted_at' => $kirim ? now() : null,
            ]);

            $this->simpanTerlapor($laporan, $request->input('terlapor', []));
            $this->simpanLampiran($request, $laporan);

            $this->service->catat($laporan, 'dibuat', 'Pengaduan dibuat', null, null, $laporan->status);

            if ($kirim) {
                $this->setelahDikirim($laporan);
            }

            return $laporan;
        });

        return redirect()->route('pelapor.laporan.show', $laporan)->with(
            'sukses',
            $kirim
                ? "Pengaduan berhasil dikirim dengan nomor tiket {$laporan->nomor_tiket}."
                : 'Pengaduan disimpan sebagai draf.'
        );
    }

    public function show(Request $request, Laporan $laporan): View
    {
        $this->pastikanMilikSendiri($request, $laporan);

        $laporan->load([
            'kategori', 'opd', 'terlapor', 'petugas',
            'lampiran' => fn ($q) => $q->where('is_internal', false),
            'tindakLanjut' => fn ($q) => $q->where('is_internal', false)->with('user'),
            'pesan.user',
        ]);

        // Tandai pesan dari Inspektorat sebagai sudah dibaca.
        if ($laporan->unread_pelapor > 0) {
            $laporan->pesan()->where('pengirim', 'inspektorat')->whereNull('dibaca_at')->update(['dibaca_at' => now()]);
            $laporan->update(['unread_pelapor' => 0]);
        }

        return view('pelapor.laporan.show', ['laporan' => $laporan]);
    }

    public function edit(Request $request, Laporan $laporan): View|RedirectResponse
    {
        $this->pastikanMilikSendiri($request, $laporan);

        if (! $laporan->bisaDiedit()) {
            return redirect()->route('pelapor.laporan.show', $laporan)
                ->with('gagal', 'Pengaduan yang sudah dikirim tidak dapat diubah.');
        }

        $laporan->load(['terlapor', 'lampiran']);

        return view('pelapor.laporan.form', [
            'laporan' => $laporan,
            'kategori' => $laporan->kategori,
            'opdList' => Opd::aktif()->orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Laporan $laporan): RedirectResponse
    {
        $this->pastikanMilikSendiri($request, $laporan);
        abort_unless($laporan->bisaDiedit(), 403, 'Pengaduan ini tidak dapat diubah lagi.');

        $kirim = $request->input('aksi') === 'kirim';
        $data = $this->validasi($request, $kirim);

        DB::transaction(function () use ($request, $data, $laporan, $kirim) {
            $laporan->update([
                'kategori_id' => $data['kategori_id'],
                'opd_id' => $data['opd_id'] ?? null,
                'judul' => $data['judul'],
                'uraian' => $data['uraian'],
                'lokasi_kejadian' => $data['lokasi_kejadian'] ?? null,
                'tanggal_kejadian' => $data['tanggal_kejadian'] ?? null,
                'nilai_kerugian' => $data['nilai_kerugian'] ?? null,
                'is_anonymous' => $request->boolean('is_anonymous'),
                'status' => $kirim ? Laporan::STATUS_TERKIRIM : Laporan::STATUS_DRAFT,
                'submitted_at' => $kirim ? now() : null,
            ]);

            $laporan->terlapor()->delete();
            $this->simpanTerlapor($laporan, $request->input('terlapor', []));
            $this->simpanLampiran($request, $laporan);

            if ($kirim) {
                $this->setelahDikirim($laporan);
            }
        });

        return redirect()->route('pelapor.laporan.show', $laporan)->with(
            'sukses',
            $kirim ? "Pengaduan berhasil dikirim dengan nomor tiket {$laporan->nomor_tiket}." : 'Draf pengaduan diperbarui.'
        );
    }

    /** Kirim draf yang sudah tersimpan tanpa membuka formulir. */
    public function kirim(Request $request, Laporan $laporan): RedirectResponse
    {
        $this->pastikanMilikSendiri($request, $laporan);
        abort_unless($laporan->isDraft(), 403);

        if ($laporan->terlapor()->count() === 0) {
            return back()->with('gagal', 'Lengkapi data pihak yang diduga terlibat sebelum mengirim.');
        }

        $laporan->update(['status' => Laporan::STATUS_TERKIRIM, 'submitted_at' => now()]);
        $this->setelahDikirim($laporan);

        return back()->with('sukses', "Pengaduan {$laporan->nomor_tiket} berhasil dikirim ke Inspektorat.");
    }

    public function destroy(Request $request, Laporan $laporan): RedirectResponse
    {
        $this->pastikanMilikSendiri($request, $laporan);
        abort_unless($laporan->isDraft(), 403, 'Hanya draf yang dapat dihapus.');

        $laporan->lampiran->each->delete();
        $laporan->forceDelete();

        return redirect()->route('pelapor.laporan.index')->with('sukses', 'Draf pengaduan dihapus.');
    }

    public function hapusLampiran(Request $request, Laporan $laporan, Lampiran $lampiran): RedirectResponse
    {
        $this->pastikanMilikSendiri($request, $laporan);
        abort_unless($laporan->isDraft() && $lampiran->laporan_id === $laporan->id, 403);

        $lampiran->delete();

        return back()->with('sukses', 'Lampiran dihapus.');
    }

    public function unduhLampiran(Request $request, Laporan $laporan, Lampiran $lampiran): StreamedResponse
    {
        $this->pastikanMilikSendiri($request, $laporan);
        abort_unless($lampiran->laporan_id === $laporan->id && ! $lampiran->is_internal, 403);

        return Storage::disk('public')->download($lampiran->path, $lampiran->nama_file);
    }

    // ------------------------------------------------------------------

    private function validasi(Request $request, bool $kirim): array
    {
        return $request->validate([
            'kategori_id' => ['required', Rule::exists('kategori', 'id')->where('is_active', true)],
            'judul' => ['required', 'string', 'max:200'],
            'uraian' => $kirim
                ? ['required', 'string', 'min:50', 'max:20000']
                : ['nullable', 'string', 'max:20000'],
            'opd_id' => ['nullable', Rule::exists('opd', 'id')],
            'lokasi_kejadian' => ['nullable', 'string', 'max:200'],
            'tanggal_kejadian' => ['nullable', 'date', 'before_or_equal:today'],
            'nilai_kerugian' => ['nullable', 'numeric', 'min:0', 'max:999999999999999'],
            'is_anonymous' => ['nullable', 'boolean'],

            'terlapor' => $kirim
                ? ['required', 'array', 'max:20', $this->aturanTerlapor()]
                : ['nullable', 'array', 'max:20'],
            'terlapor.*.nama' => ['nullable', 'string', 'max:150'],
            'terlapor.*.jabatan' => ['nullable', 'string', 'max:150'],
            'terlapor.*.instansi' => ['nullable', 'string', 'max:150'],
            'terlapor.*.klasifikasi' => ['nullable', Rule::in(array_keys(Terlapor::KLASIFIKASI))],

            'lampiran' => ['nullable', 'array', 'max:10'],
            'lampiran.*.file' => ['nullable', 'file', 'extensions:'.self::EKSTENSI, 'max:'.self::MAKS_KB],
            'lampiran.*.keterangan' => ['nullable', 'string', 'max:200'],

            'setuju' => [$kirim ? 'accepted' : 'nullable'],
        ], [
            'uraian.min' => 'Uraian pengaduan minimal 50 karakter agar dapat ditindaklanjuti.',
            'terlapor.required' => 'Isi minimal satu pihak yang diduga terlibat.',
            'setuju.accepted' => 'Anda harus menyetujui syarat dan ketentuan sebelum mengirim.',
            'lampiran.*.file.extensions' => 'Format berkas tidak didukung.',
            'lampiran.*.file.max' => 'Ukuran tiap berkas maksimal 50 MB.',
        ], [
            'kategori_id' => 'kategori pengaduan',
            'opd_id' => 'OPD terlapor',
        ]);
    }

    /** Saat dikirim, minimal satu baris pihak terlapor harus terisi namanya. */
    private function aturanTerlapor(): \Closure
    {
        return function (string $atribut, mixed $nilai, \Closure $gagal) {
            $terisi = collect(is_array($nilai) ? $nilai : [])
                ->filter(fn ($baris) => filled($baris['nama'] ?? null));

            if ($terisi->isEmpty()) {
                $gagal('Isi minimal satu pihak yang diduga terlibat.');
            }
        };
    }

    private function simpanTerlapor(Laporan $laporan, array $daftar): void
    {
        foreach ($daftar as $baris) {
            if (blank($baris['nama'] ?? null)) {
                continue;
            }

            $laporan->terlapor()->create([
                'nama' => $baris['nama'],
                'jabatan' => $baris['jabatan'] ?? null,
                'instansi' => $baris['instansi'] ?? null,
                'klasifikasi' => $baris['klasifikasi'] ?? 'lainnya',
            ]);
        }
    }

    private function simpanLampiran(Request $request, Laporan $laporan): void
    {
        foreach ($request->file('lampiran', []) as $indeks => $baris) {
            $berkas = $baris['file'] ?? null;

            if (! $berkas) {
                continue;
            }

            $path = $berkas->store("lampiran/{$laporan->id}", 'public');

            $laporan->lampiran()->create([
                'uploaded_by' => $request->user()->id,
                'nama_file' => $berkas->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $berkas->getClientMimeType(),
                'ukuran' => $berkas->getSize(),
                'keterangan' => $request->input("lampiran.{$indeks}.keterangan"),
            ]);
        }
    }

    private function setelahDikirim(Laporan $laporan): void
    {
        $this->service->catat(
            $laporan,
            'dikirim',
            'Pengaduan dikirim ke Inspektorat',
            'Pengaduan menunggu verifikasi awal oleh Inspektorat Kabupaten Pringsewu.',
            Laporan::STATUS_DRAFT,
            Laporan::STATUS_TERKIRIM,
        );

        $this->service->notifikasiInspektorat(
            $laporan,
            'laporan_baru',
            'Pengaduan baru masuk',
            "{$laporan->nomor_tiket} — {$laporan->judul}",
        );
    }

    private function pastikanMilikSendiri(Request $request, Laporan $laporan): void
    {
        abort_unless($laporan->user_id === $request->user()->id, 403, 'Pengaduan ini bukan milik Anda.');
    }
}
