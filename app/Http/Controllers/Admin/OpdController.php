<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OpdController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.opd.index', [
            'opd' => Opd::query()
                ->withCount(['users', 'laporan'])
                ->when($request->string('q')->toString(), fn ($q, $kata) => $q->where('nama', 'like', "%{$kata}%")
                    ->orWhere('singkatan', 'like', "%{$kata}%"))
                ->when($request->string('jenis')->toString(), fn ($q, $j) => $q->where('jenis', $j))
                ->orderBy('nama')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.opd.form', ['opd' => new Opd(['is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Opd::create($this->validasi($request));

        return redirect()->route('admin.opd.index')->with('sukses', 'OPD berhasil ditambahkan.');
    }

    public function edit(Opd $opd): View
    {
        return view('admin.opd.form', ['opd' => $opd]);
    }

    public function update(Request $request, Opd $opd): RedirectResponse
    {
        $opd->update($this->validasi($request, $opd));

        return redirect()->route('admin.opd.index')->with('sukses', 'Data OPD diperbarui.');
    }

    public function destroy(Opd $opd): RedirectResponse
    {
        if ($opd->users()->exists() || $opd->laporan()->exists()) {
            return back()->with('gagal', 'OPD tidak dapat dihapus karena masih memiliki pengguna atau pengaduan terkait.');
        }

        $opd->delete();

        return back()->with('sukses', 'OPD dihapus.');
    }

    private function validasi(Request $request, ?Opd $opd = null): array
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:180', Rule::unique('opd', 'nama')->ignore($opd?->id)],
            'singkatan' => ['nullable', 'string', 'max:60'],
            'jenis' => ['required', Rule::in(['sekretariat', 'dinas', 'badan', 'inspektorat', 'kecamatan', 'rsud', 'satuan', 'lainnya'])],
            'alamat' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'kepala' => ['nullable', 'string', 'max:150'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
