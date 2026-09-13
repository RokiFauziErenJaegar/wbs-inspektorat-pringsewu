@extends('layouts.auth')

@section('judul', 'Atur Ulang Kata Sandi')

@section('konten')

    <div class="mb-8">
        <span class="grid size-14 place-items-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-500/25">
            <x-ikon nama="lock" class="size-6"/>
        </span>
        <h1 class="mt-5 text-3xl font-extrabold tracking-tight text-ink-900 dark:text-white">Atur Ulang Kata Sandi</h1>
        <p class="mt-2 text-sm/6 text-ink-500 dark:text-ink-400">
            Buat kata sandi baru untuk akun Anda. Gunakan kombinasi yang kuat dan tidak mudah ditebak.
        </p>
    </div>

    <x-flash class="mb-6" />

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <x-input nama="email" label="Email" tipe="email" wajib ikon="mail" :nilai="$email" autocomplete="email" />

        <div x-data="{ tampil: false }">
            <label for="password" class="label">Kata Sandi Baru <span class="text-rose-500">*</span></label>
            <div class="relative">
                <input :type="tampil ? 'text' : 'password'" name="password" id="password" required
                       autocomplete="new-password" placeholder="••••••••"
                       class="field pr-11 @error('password') field-error @enderror">
                <button type="button" @click="tampil = !tampil"
                        class="absolute inset-y-0 right-0 grid w-11 place-items-center text-ink-400 transition hover:text-ink-600"
                        aria-label="Tampilkan kata sandi">
                    <x-ikon nama="eye" class="size-[18px]"/>
                </button>
            </div>
            <p class="help">Minimal 8 karakter, berisi huruf dan angka</p>
        </div>

        <div>
            <label for="password_confirmation" class="label">Ulangi Kata Sandi Baru <span class="text-rose-500">*</span></label>
            <input type="password" name="password_confirmation" id="password_confirmation" required
                   autocomplete="new-password" placeholder="••••••••" class="field">
        </div>

        <button class="btn btn-primary btn-lg w-full">
            <x-ikon nama="check" class="size-5"/> Simpan Kata Sandi Baru
        </button>
    </form>

@endsection
