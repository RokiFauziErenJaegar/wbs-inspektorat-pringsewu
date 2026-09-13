@extends('layouts.auth')

@section('judul', 'Masuk')

@section('konten')

    <div class="mb-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-ink-900 dark:text-white">Masuk ke Sistem</h1>
        <p class="mt-2 text-sm text-ink-500 dark:text-ink-400">
            Gunakan akun perangkat daerah Anda untuk menyampaikan dan memantau pengaduan.
        </p>
    </div>

    @if (session('sukses'))
        <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm font-medium text-emerald-800 dark:border-emerald-800/60 dark:bg-emerald-950/40 dark:text-emerald-200">
            <x-ikon nama="check-badge" class="mt-0.5 size-5 shrink-0"/>
            {{ session('sukses') }}
        </div>
    @endif

    @error('login')
        <div class="mb-6 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm font-medium text-rose-800 dark:border-rose-800/60 dark:bg-rose-950/40 dark:text-rose-200">
            <x-ikon nama="alert" class="mt-0.5 size-5 shrink-0"/>
            {{ $message }}
        </div>
    @enderror

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-input nama="login" label="Username atau Email" wajib ikon="user"
                 placeholder="misal: opd.disdikbud" autofocus autocomplete="username" />

        <div>
            <div class="mb-1.5 flex items-center justify-between">
                <label for="password" class="text-sm font-semibold text-ink-700 dark:text-ink-200">
                    Kata Sandi <span class="text-rose-500">*</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400">
                    Lupa kata sandi?
                </a>
            </div>

            <div class="relative" x-data="{ tampil: false }">
                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-11 place-items-center text-ink-400">
                    <x-ikon nama="lock" class="size-[18px]"/>
                </span>

                <input :type="tampil ? 'text' : 'password'" name="password" id="password" required
                       autocomplete="current-password" placeholder="••••••••"
                       class="field pr-11 pl-11 @error('password') field-error @enderror">

                <button type="button" @click="tampil = !tampil"
                        class="absolute inset-y-0 right-0 grid w-11 place-items-center text-ink-400 transition hover:text-ink-600"
                        aria-label="Tampilkan kata sandi">
                    <x-ikon nama="eye" class="size-[18px]"/>
                </button>
            </div>

            @error('password')
                <p class="mt-1.5 flex items-center gap-1 text-xs font-medium text-rose-600 dark:text-rose-400">
                    <x-ikon nama="alert" class="size-3.5"/> {{ $message }}
                </p>
            @enderror
        </div>

        <label class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-600 dark:text-ink-300">
            <input type="checkbox" name="remember" value="1"
                   class="size-4 rounded border-ink-300 text-brand-600 focus:ring-2 focus:ring-brand-500/30 dark:border-ink-600 dark:bg-ink-900">
            Ingat saya di perangkat ini
        </label>

        <button class="btn btn-primary btn-lg w-full">
            <x-ikon nama="logout" class="size-5 rotate-180"/> Masuk
        </button>
    </form>

    <div class="my-7 flex items-center gap-4">
        <span class="h-px flex-1 bg-ink-200 dark:bg-ink-800"></span>
        <span class="text-xs font-semibold text-ink-400">ATAU</span>
        <span class="h-px flex-1 bg-ink-200 dark:bg-ink-800"></span>
    </div>

    <div class="space-y-3">
        <a href="{{ route('register') }}" class="btn btn-outline w-full">
            <x-ikon nama="user-circle" class="size-4"/> Daftar Akun Baru
        </a>
        <a href="{{ route('lacak.index') }}" class="btn btn-ghost w-full">
            <x-ikon nama="search" class="size-4"/> Lacak Aduan Tanpa Masuk
        </a>
    </div>

    <p class="mt-8 flex items-start gap-2 rounded-xl bg-ink-50 px-4 py-3 text-xs/6 text-ink-500 dark:bg-ink-800/50 dark:text-ink-400">
        <x-ikon nama="lock" class="mt-0.5 size-4 shrink-0 text-brand-500"/>
        Jaga kerahasiaan akun Anda. Inspektorat tidak pernah meminta kata sandi melalui telepon maupun pesan.
    </p>

@endsection
