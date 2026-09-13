@extends('layouts.auth')

@section('judul', 'Daftar Akun')

@section('konten')

    <div class="mb-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-ink-900 dark:text-white">Daftar Akun OPD</h1>
        <p class="mt-2 text-sm text-ink-500 dark:text-ink-400">
            Lengkapi data berikut untuk membuat akun pelapor. Satu akun mewakili satu pegawai perangkat daerah.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 dark:border-rose-800/60 dark:bg-rose-950/40">
            <p class="flex items-center gap-2 text-sm font-semibold text-rose-800 dark:text-rose-200">
                <x-ikon nama="alert" class="size-5"/> Periksa kembali isian Anda
            </p>
            <ul class="mt-2 list-disc space-y-1 pl-8 text-xs text-rose-700 dark:text-rose-300">
                @foreach ($errors->unique() as $galat)
                    <li>{{ $galat }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <x-input nama="name" label="Nama Lengkap" wajib ikon="user"
                 placeholder="Nama sesuai data kepegawaian" autofocus />

        <x-select nama="opd_id" label="Organisasi Perangkat Daerah" wajib
                  kosong="— Pilih OPD tempat Anda bertugas —"
                  :pilihan="$opdList->pluck('nama', 'id')" />

        <div class="grid gap-5 sm:grid-cols-2">
            <x-input nama="username" label="Username" wajib ikon="user-circle"
                     placeholder="huruf, angka, titik" bantuan="Minimal 4 karakter" autocomplete="username" />
            <x-input nama="telepon" label="Nomor Telepon" tipe="tel" ikon="phone" placeholder="08xx-xxxx-xxxx" />
        </div>

        <x-input nama="email" label="Email Aktif" tipe="email" wajib ikon="mail"
                 placeholder="nama@pringsewukab.go.id"
                 bantuan="Digunakan untuk pemberitahuan dan pemulihan kata sandi" autocomplete="email" />

        <div class="grid gap-5 sm:grid-cols-2">
            <x-input nama="nip" label="NIP" ikon="clipboard" placeholder="Opsional" />
            <x-input nama="jabatan" label="Jabatan" ikon="briefcase" placeholder="Opsional" />
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div x-data="{ tampil: false }">
                <label for="password" class="label">Kata Sandi <span class="text-rose-500">*</span></label>
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
                <label for="password_confirmation" class="label">Ulangi Kata Sandi <span class="text-rose-500">*</span></label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       autocomplete="new-password" placeholder="••••••••" class="field">
            </div>
        </div>

        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-ink-200 p-4 transition hover:border-brand-300 hover:bg-brand-50/50 dark:border-ink-700 dark:hover:border-brand-700 dark:hover:bg-brand-950/20">
            <input type="checkbox" name="setuju" value="1" required @checked(old('setuju'))
                   class="mt-0.5 size-4 rounded border-ink-300 text-brand-600 focus:ring-2 focus:ring-brand-500/30 dark:border-ink-600 dark:bg-ink-900">
            <span class="text-xs/6 text-ink-600 dark:text-ink-300">
                Saya menyatakan data yang saya isikan benar dan bersedia bertanggung jawab atas kebenaran
                setiap pengaduan yang saya sampaikan melalui Whistleblowing System Inspektorat Kabupaten Pringsewu.
            </span>
        </label>

        <button class="btn btn-primary btn-lg w-full">
            <x-ikon nama="user-circle" class="size-5"/> Buat Akun
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-ink-500 dark:text-ink-400">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="font-bold text-brand-600 hover:underline dark:text-brand-400">Masuk di sini</a>
    </p>

@endsection
