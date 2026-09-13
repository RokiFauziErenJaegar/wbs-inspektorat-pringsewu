<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        return view('admin.faq.index', [
            'faq' => Faq::urut()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.faq.form', ['faq' => new Faq(['is_active' => true, 'kelompok' => 'umum'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Faq::create($this->validasi($request));

        return redirect()->route('admin.faq.index')->with('sukses', 'FAQ ditambahkan.');
    }

    public function edit(Faq $faq): View
    {
        return view('admin.faq.form', ['faq' => $faq]);
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $faq->update($this->validasi($request));

        return redirect()->route('admin.faq.index')->with('sukses', 'FAQ diperbarui.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return back()->with('sukses', 'FAQ dihapus.');
    }

    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'pertanyaan' => ['required', 'string', 'max:250'],
            'jawaban' => ['required', 'string', 'max:5000'],
            'kelompok' => ['required', 'string', 'max:60'],
            'urutan' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['urutan'] = $data['urutan'] ?? 0;

        return $data;
    }
}
