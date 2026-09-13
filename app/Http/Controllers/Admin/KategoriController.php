<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KategoriController extends Controller
{
    /** Pilihan ikon yang tersedia pada komponen <x-ikon>. */
    public const IKON = [
        'shield', 'scale', 'banknote', 'users', 'briefcase', 'building',
        'gift', 'document', 'gavel', 'alert', 'clipboard', 'lock',
    ];

    public const WARNA = ['emerald', 'teal', 'sky', 'indigo', 'violet', 'amber', 'orange', 'rose', 'slate'];

    public function index(): View
    {
        return view('admin.kategori.index', [
            'kategori' => Kategori::withCount('laporan')->urut()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.kategori.form', [
            'kategori' => new Kategori(['is_active' => true, 'icon' => 'shield', 'warna' => 'emerald']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Kategori::create($this->validasi($request));

        return redirect()->route('admin.kategori.index')->with('sukses', 'Kategori pengaduan ditambahkan.');
    }

    public function edit(Kategori $kategori): View
    {
        return view('admin.kategori.form', ['kategori' => $kategori]);
    }

    public function update(Request $request, Kategori $kategori): RedirectResponse
    {
        $kategori->update($this->validasi($request, $kategori));

        return redirect()->route('admin.kategori.index')->with('sukses', 'Kategori pengaduan diperbarui.');
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        if ($kategori->laporan()->exists()) {
            return back()->with('gagal', 'Kategori tidak dapat dihapus karena sudah digunakan pada pengaduan.');
        }

        $kategori->delete();

        return back()->with('sukses', 'Kategori dihapus.');
    }

    private function validasi(Request $request, ?Kategori $kategori = null): array
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'kode' => ['required', 'string', 'max:20', 'alpha_dash', Rule::unique('kategori', 'kode')->ignore($kategori?->id)],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'petunjuk' => ['nullable', 'string', 'max:1000'],
            'icon' => ['required', Rule::in(self::IKON)],
            'warna' => ['required', Rule::in(self::WARNA)],
            'urutan' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['urutan'] = $data['urutan'] ?? 0;

        return $data;
    }
}
