<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    public function index(Request $request): View
    {
        return view('notifikasi', [
            'notifikasi' => Notifikasi::where('user_id', $request->user()->id)
                ->latest()
                ->paginate(20),
        ]);
    }

    public function buka(Request $request, Notifikasi $notifikasi): RedirectResponse
    {
        abort_unless($notifikasi->user_id === $request->user()->id, 403);

        $notifikasi->update(['dibaca_at' => now()]);

        return redirect($notifikasi->url ?: route('notifikasi.index'));
    }

    public function bacaSemua(Request $request): RedirectResponse
    {
        Notifikasi::where('user_id', $request->user()->id)
            ->whereNull('dibaca_at')
            ->update(['dibaca_at' => now()]);

        return back()->with('sukses', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
