<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Lampiran;
use App\Models\Laporan;
use App\Models\Opd;
use App\Models\User;
use App\Services\LaporanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function __construct(private readonly LaporanService $service) {}

    public function index(Request $request): View
    {
        $laporan = $this->lingkup($request)
            ->with(['kategori', 'user.opd', 'opd', 'petugas'])
            ->when($request->string('status')->toString(), fn ($q, $s) => $q->where('status', $s))
            ->when($request->string('kategori')->toString(), fn ($q, $k) => $q->where('kategori_id', $k))
            ->when($request->string('opd')->toString(), fn ($q, $o) => $q->where('opd_id', $o))
            ->when($request->string('prioritas')->toString(), fn ($q, $p) => $q->where('prioritas', $p))
            ->when($request->string('petugas')->toString(), function ($q, $p) {
                return $p === 'belum' ? $q->whereNull('petugas_id') : $q->where('petugas_id', $p);
            })
            ->when($request->date('dari'), fn ($q, $d) => $q->whereDate('submitted_at', '>=', $d))
            ->when($request->date('sampai'), fn ($q, $d) => $q->whereDate('submitted_at', '<=', $d))
            ->cari($request->string('q')->toString())
            ->orderByRaw("FIELD(prioritas,'mendesak','tinggi','sedang','rendah')")
            ->latest('submitted_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.laporan.index', [
            'laporan' => $laporan,
            'kategoriList' => Kategori::urut()->get(),
            'opdList' => Opd::orderBy('nama')->get(),
            'petugasList' => User::where('role', User::ROLE_PETUGAS)->orderBy('name')->get(),
        ]);
    }

    public function show(Request $request, Laporan $laporan): View
    {
        $this->pastikanBerwenang($request, $laporan);

        $laporan->load([
            'kategori', 'opd', 'user.opd', 'terlapor', 'petugas', 'verifikator',
            'lampiran.pengunggah', 'tindakLanjut.user', 'pesan.user',
        ]);

        if ($laporan->unread_admin > 0) {
            $laporan->pesan()->where('pengirim', 'pelapor')->whereNull('dibaca_at')->update(['dibaca_at' => now()]);
            $laporan->update(['unread_admin' => 0]);
        }

        return view('admin.laporan.show', [
            'laporan' => $laporan,
            'petugasList' => User::where('role', User::ROLE_PETUGAS)->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /** Tandai berkas mulai diverifikasi oleh Inspektorat. */
    public function verifikasi(Request $request, Laporan $laporan): RedirectResponse
    {
        $this->pastikanBerwenang($request, $laporan);
        abort_unless($laporan->status === Laporan::STATUS_TERKIRIM, 403);

        $lama = $laporan->status;
        $laporan->update([
            'status' => Laporan::STATUS_VERIFIKASI,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        $this->service->catat(
            $laporan, 'verifikasi', 'Pengaduan sedang diverifikasi',
            'Inspektorat sedang memeriksa kelengkapan dan kelayakan materi pengaduan.',
            $lama, $laporan->status,
        );

        $this->service->notifikasiPelapor(
            $laporan, 'verifikasi', 'Pengaduan sedang diverifikasi',
            "{$laporan->nomor_tiket} sedang diperiksa kelengkapannya oleh Inspektorat.",
        );

        return back()->with('sukses', 'Status diperbarui menjadi Sedang Diverifikasi.');
    }

    /** Terima berkas dan lanjutkan ke tahap tindak lanjut. */
    public function terima(Request $request, Laporan $laporan): RedirectResponse
    {
        $this->pastikanBerwenang($request, $laporan);
        abort_if($laporan->isDraft(), 403);

        $data = $request->validate([
            'petugas_id' => ['nullable', Rule::exists('users', 'id')->where('role', User::ROLE_PETUGAS)],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
            'prioritas' => ['required', Rule::in(array_keys(Laporan::PRIORITAS))],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ], [], ['petugas_id' => 'petugas penanggung jawab']);

        $lama = $laporan->status;

        $laporan->update([
            'status' => Laporan::STATUS_DIPROSES,
            'prioritas' => $data['prioritas'],
            'petugas_id' => $data['petugas_id'] ?? $laporan->petugas_id,
            'deadline' => $data['deadline'] ?? $laporan->deadline,
            'verified_by' => $laporan->verified_by ?? $request->user()->id,
            'verified_at' => $laporan->verified_at ?? now(),
        ]);

        $petugas = $laporan->fresh()->petugas;

        $this->service->catat(
            $laporan, 'disposisi',
            $petugas ? "Didisposisikan kepada {$petugas->name}" : 'Pengaduan diterima untuk ditindaklanjuti',
            $data['catatan'] ?? null, $lama, $laporan->status,
        );

        $this->service->notifikasiPelapor(
            $laporan, 'progres', 'Pengaduan Anda mulai ditindaklanjuti',
            "{$laporan->nomor_tiket} telah diterima dan sedang dalam proses penanganan Inspektorat.",
        );

        if ($petugas) {
            $this->service->notifikasiInspektorat(
                $laporan, 'disposisi', 'Disposisi pengaduan',
                "Anda ditugaskan menangani {$laporan->nomor_tiket}.", $petugas,
            );
        }

        return back()->with('sukses', 'Pengaduan diterima dan masuk tahap tindak lanjut.');
    }

    public function tolak(Request $request, Laporan $laporan): RedirectResponse
    {
        $this->pastikanBerwenang($request, $laporan);
        abort_if($laporan->isDraft(), 403);

        $data = $request->validate([
            'alasan_penolakan' => ['required', 'string', 'min:20', 'max:2000'],
        ], [
            'alasan_penolakan.min' => 'Jelaskan alasan penolakan minimal 20 karakter.',
        ], ['alasan_penolakan' => 'alasan penolakan']);

        $lama = $laporan->status;

        $laporan->update([
            'status' => Laporan::STATUS_DITOLAK,
            'alasan_penolakan' => $data['alasan_penolakan'],
            'selesai_at' => now(),
            'verified_by' => $laporan->verified_by ?? $request->user()->id,
            'verified_at' => $laporan->verified_at ?? now(),
        ]);

        $this->service->catat(
            $laporan, 'ditolak', 'Pengaduan tidak dapat ditindaklanjuti',
            $data['alasan_penolakan'], $lama, $laporan->status,
        );

        $this->service->notifikasiPelapor(
            $laporan, 'ditolak', 'Pengaduan tidak dapat ditindaklanjuti',
            "{$laporan->nomor_tiket} ditolak. Lihat alasan lengkapnya pada rincian pengaduan.",
        );

        return back()->with('sukses', 'Pengaduan ditandai tidak dapat ditindaklanjuti.');
    }

    public function disposisi(Request $request, Laporan $laporan): RedirectResponse
    {
        $this->pastikanBerwenang($request, $laporan);
        abort_unless($request->user()->isAdmin(), 403, 'Hanya administrator yang dapat melakukan disposisi.');

        $data = $request->validate([
            'petugas_id' => ['required', Rule::exists('users', 'id')->where('role', User::ROLE_PETUGAS)],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ], [], ['petugas_id' => 'petugas penanggung jawab']);

        $laporan->update([
            'petugas_id' => $data['petugas_id'],
            'deadline' => $data['deadline'] ?? $laporan->deadline,
            'status' => $laporan->status === Laporan::STATUS_TERKIRIM ? Laporan::STATUS_VERIFIKASI : $laporan->status,
        ]);

        $petugas = User::find($data['petugas_id']);

        $this->service->catat(
            $laporan, 'disposisi', "Didisposisikan kepada {$petugas->name}",
            $data['catatan'] ?? null, null, null, true,
        );

        $this->service->notifikasiInspektorat(
            $laporan, 'disposisi', 'Disposisi pengaduan',
            "Anda ditugaskan menangani {$laporan->nomor_tiket}.", $petugas,
        );

        return back()->with('sukses', "Pengaduan didisposisikan kepada {$petugas->name}.");
    }

    /** Catat perkembangan penanganan. */
    public function progres(Request $request, Laporan $laporan): RedirectResponse
    {
        $this->pastikanBerwenang($request, $laporan);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'catatan' => ['required', 'string', 'max:5000'],
            'is_internal' => ['nullable', 'boolean'],
        ], [], ['judul' => 'judul perkembangan', 'catatan' => 'uraian perkembangan']);

        $internal = $request->boolean('is_internal');

        $this->service->catat(
            $laporan, 'progres', $data['judul'], $data['catatan'], null, null, $internal,
        );

        if (! $internal) {
            $this->service->notifikasiPelapor(
                $laporan, 'progres', 'Perkembangan penanganan pengaduan',
                "{$laporan->nomor_tiket}: {$data['judul']}",
            );
        }

        return back()->with('sukses', 'Perkembangan penanganan dicatat.');
    }

    public function selesai(Request $request, Laporan $laporan): RedirectResponse
    {
        $this->pastikanBerwenang($request, $laporan);
        abort_if($laporan->isDraft(), 403);

        $data = $request->validate([
            'kesimpulan' => ['required', 'string', 'min:20', 'max:5000'],
            'rekomendasi' => ['nullable', 'string', 'max:5000'],
        ], [
            'kesimpulan.min' => 'Kesimpulan minimal 20 karakter.',
        ]);

        $lama = $laporan->status;

        $laporan->update([
            'status' => Laporan::STATUS_SELESAI,
            'kesimpulan' => $data['kesimpulan'],
            'rekomendasi' => $data['rekomendasi'] ?? null,
            'selesai_at' => now(),
        ]);

        $this->service->catat(
            $laporan, 'selesai', 'Penanganan pengaduan selesai',
            $data['kesimpulan'], $lama, $laporan->status,
        );

        $this->service->notifikasiPelapor(
            $laporan, 'selesai', 'Penanganan pengaduan selesai',
            "{$laporan->nomor_tiket} telah selesai ditangani. Lihat kesimpulan pada rincian pengaduan.",
        );

        return back()->with('sukses', 'Pengaduan ditandai selesai.');
    }

    public function bukaKembali(Request $request, Laporan $laporan): RedirectResponse
    {
        $this->pastikanBerwenang($request, $laporan);
        abort_unless($request->user()->isAdmin() && $laporan->isSelesai(), 403);

        $data = $request->validate([
            'catatan' => ['required', 'string', 'min:10', 'max:2000'],
        ], [], ['catatan' => 'alasan membuka kembali']);

        $lama = $laporan->status;

        $laporan->update([
            'status' => Laporan::STATUS_DIPROSES,
            'selesai_at' => null,
        ]);

        $this->service->catat(
            $laporan, 'progres', 'Pengaduan dibuka kembali',
            $data['catatan'], $lama, $laporan->status,
        );

        $this->service->notifikasiPelapor(
            $laporan, 'progres', 'Pengaduan dibuka kembali',
            "{$laporan->nomor_tiket} kembali ditindaklanjuti oleh Inspektorat.",
        );

        return back()->with('sukses', 'Pengaduan dibuka kembali.');
    }

    public function prioritas(Request $request, Laporan $laporan): RedirectResponse
    {
        $this->pastikanBerwenang($request, $laporan);

        $data = $request->validate([
            'prioritas' => ['required', Rule::in(array_keys(Laporan::PRIORITAS))],
        ]);

        $laporan->update($data);

        return back()->with('sukses', 'Prioritas diperbarui menjadi '.Laporan::PRIORITAS[$data['prioritas']].'.');
    }

    public function unggahLampiran(Request $request, Laporan $laporan): RedirectResponse
    {
        $this->pastikanBerwenang($request, $laporan);

        $data = $request->validate([
            'file' => ['required', 'file', 'extensions:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip,rar', 'max:51200'],
            'keterangan' => ['nullable', 'string', 'max:200'],
        ], [], ['file' => 'dokumen']);

        $berkas = $request->file('file');
        $path = $berkas->store("lampiran/{$laporan->id}", 'public');

        $laporan->lampiran()->create([
            'uploaded_by' => $request->user()->id,
            'nama_file' => $berkas->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $berkas->getClientMimeType(),
            'ukuran' => $berkas->getSize(),
            'keterangan' => $data['keterangan'] ?? null,
            'is_internal' => true,
        ]);

        $this->service->catat(
            $laporan, 'lampiran', 'Dokumen internal ditambahkan',
            $berkas->getClientOriginalName(), null, null, true,
        );

        return back()->with('sukses', 'Dokumen berhasil diunggah.');
    }

    public function unduhLampiran(Request $request, Laporan $laporan, Lampiran $lampiran): StreamedResponse
    {
        $this->pastikanBerwenang($request, $laporan);
        abort_unless($lampiran->laporan_id === $laporan->id, 404);

        return Storage::disk('public')->download($lampiran->path, $lampiran->nama_file);
    }

    public function cetak(Request $request, Laporan $laporan): View
    {
        $this->pastikanBerwenang($request, $laporan);

        $laporan->load(['kategori', 'opd', 'user.opd', 'terlapor', 'petugas', 'tindakLanjut.user', 'lampiran']);

        return view('admin.laporan.cetak', ['laporan' => $laporan]);
    }

    // ------------------------------------------------------------------

    /** Petugas hanya boleh menyentuh berkas yang didisposisikan kepadanya. */
    private function lingkup(Request $request)
    {
        return Laporan::query()
            ->terkirim()
            ->when($request->user()->isPetugas(), fn ($q) => $q->where('petugas_id', $request->user()->id));
    }

    private function pastikanBerwenang(Request $request, Laporan $laporan): void
    {
        abort_if($laporan->isDraft(), 404);

        if ($request->user()->isPetugas() && $laporan->petugas_id !== $request->user()->id) {
            abort(403, 'Pengaduan ini tidak didisposisikan kepada Anda.');
        }
    }
}
