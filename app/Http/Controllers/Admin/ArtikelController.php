<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ArtikelController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.artikel.index', [
            'artikel' => Artikel::query()
                ->with('penulis')
                ->when($request->string('q')->toString(), fn ($q, $kata) => $q->where('judul', 'like', "%{$kata}%"))
                ->latest()
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.artikel.form', ['artikel' => new Artikel(['is_published' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        $data['user_id'] = $request->user()->id;

        if ($berkas = $request->file('gambar')) {
            $data['gambar'] = $berkas->store('artikel', 'public');
        }

        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?? now()) : null;

        Artikel::create($data);

        return redirect()->route('admin.artikel.index')->with('sukses', 'Berita berhasil dipublikasikan.');
    }

    public function edit(Artikel $artikel): View
    {
        return view('admin.artikel.form', ['artikel' => $artikel]);
    }

    public function update(Request $request, Artikel $artikel): RedirectResponse
    {
        $data = $this->validasi($request);

        if ($berkas = $request->file('gambar')) {
            if ($artikel->gambar) {
                Storage::disk('public')->delete($artikel->gambar);
            }
            $data['gambar'] = $berkas->store('artikel', 'public');
        }

        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?? $artikel->published_at ?? now()) : null;

        $artikel->update($data);

        return redirect()->route('admin.artikel.index')->with('sukses', 'Berita diperbarui.');
    }

    public function destroy(Artikel $artikel): RedirectResponse
    {
        if ($artikel->gambar) {
            Storage::disk('public')->delete($artikel->gambar);
        }

        $artikel->delete();

        return back()->with('sukses', 'Berita dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'ringkasan' => ['nullable', 'string', 'max:300'],
            'konten' => ['required', 'string', 'max:50000'],
            'sumber' => ['nullable', 'string', 'max:150'],
            'gambar' => ['nullable', 'image', 'max:4096'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ], [], ['konten' => 'isi berita']);
    }
}
