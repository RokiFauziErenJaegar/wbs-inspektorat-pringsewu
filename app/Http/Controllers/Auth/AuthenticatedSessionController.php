<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [], [
            'login' => 'username atau email',
        ]);

        $kunci = Str::transliterate(Str::lower($data['login']).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($kunci, 5)) {
            throw ValidationException::withMessages([
                'login' => 'Terlalu banyak percobaan masuk. Coba lagi dalam '
                    .RateLimiter::availableIn($kunci).' detik.',
            ]);
        }

        $kolom = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $kredensial = [
            $kolom => $data['login'],
            'password' => $data['password'],
        ];

        if (! Auth::attempt($kredensial, $request->boolean('remember'))) {
            RateLimiter::hit($kunci);

            throw ValidationException::withMessages([
                'login' => 'Username/email atau kata sandi tidak sesuai.',
            ]);
        }

        if (! $request->user()->is_active) {
            Auth::logout();

            throw ValidationException::withMessages([
                'login' => 'Akun Anda dinonaktifkan. Silakan hubungi Inspektorat.',
            ]);
        }

        RateLimiter::clear($kunci);
        $request->session()->regenerate();

        $request->user()->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        return redirect()->intended($this->berandaUntuk($request->user()->role))
            ->with('sukses', 'Selamat datang kembali, '.$request->user()->name.'!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda')->with('sukses', 'Anda telah keluar dari sistem.');
    }

    private function berandaUntuk(string $role): string
    {
        return $role === 'pelapor' ? route('pelapor.dashboard') : route('admin.dashboard');
    }
}
