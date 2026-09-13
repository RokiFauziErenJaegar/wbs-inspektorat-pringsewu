@extends('layouts.app')

@section('judul', 'Profil Saya')
@section('subjudul', 'Kelola data akun dan keamanan Anda')

@section('konten')

    <div class="grid gap-5 lg:grid-cols-3">

        {{-- Kartu identitas --}}
        <div class="card overflow-hidden">
            <div class="relative h-28 bg-gradient-to-br from-brand-600 to-brand-900">
                <div class="bg-grid absolute inset-0 opacity-25"></div>
            </div>

            <div class="-mt-12 px-6 pb-6 text-center">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt=""
                         class="mx-auto size-24 rounded-2xl border-4 border-white object-cover shadow-lg dark:border-ink-900">
                @else
                    <span class="mx-auto grid size-24 place-items-center rounded-2xl border-4 border-white bg-gradient-to-br from-brand-500 to-brand-700 text-2xl font-extrabold text-white shadow-lg dark:border-ink-900">
                        {{ $user->inisial }}
                    </span>
                @endif

                <h2 class="mt-4 text-lg font-extrabold text-ink-900 dark:text-white">{{ $user->name }}</h2>
                <p class="mt-0.5 text-sm text-ink-500 dark:text-ink-400">{{ '@'.$user->username }}</p>

                <span class="badge mt-3 bg-brand-50 text-brand-700 ring-brand-600/20 dark:bg-brand-950/50 dark:text-brand-300 dark:ring-brand-500/30">
                    <x-ikon nama="shield-check" class="size-3.5"/> {{ $user->role_label }}
                </span>

                <dl class="mt-6 space-y-3 border-t border-ink-100 pt-5 text-left text-xs dark:border-ink-800">
                    @foreach ([
                        ['building', 'OPD', $user->opd?->nama ?? '—'],
                        ['briefcase', 'Jabatan', $user->jabatan ?: '—'],
                        ['mail', 'Email', $user->email],
                        ['calendar', 'Bergabung', $user->created_at->translatedFormat('d F Y')],
                        ['clock', 'Terakhir masuk', $user->last_login_at?->diffForHumans() ?? 'Baru saja'],
                    ] as [$ikon, $label, $nilai])
                        <div class="flex items-start justify-between gap-3">
                            <dt class="flex shrink-0 items-center gap-1.5 text-ink-500 dark:text-ink-400">
                                <x-ikon :nama="$ikon" class="size-3.5"/> {{ $label }}
                            </dt>
                            <dd class="text-right font-semibold break-all text-ink-900 dark:text-white">{{ $nilai }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>

        {{-- Formulir --}}
        <div class="space-y-5 lg:col-span-2">

            <form method="POST" action="{{ route('profil.update') }}" enctype="multipart/form-data" class="card p-6 sm:p-7">
                @csrf @method('PUT')

                <div class="flex items-center gap-3 border-b border-ink-100 pb-4 dark:border-ink-800">
                    <span class="grid size-10 place-items-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                        <x-ikon nama="user-circle" class="size-5"/>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-ink-900 dark:text-white">Informasi Pribadi</h3>
                        <p class="text-xs text-ink-500 dark:text-ink-400">Perbarui data diri dan kontak Anda</p>
                    </div>
                </div>

                <div class="mt-6 space-y-5">
                    <x-input nama="name" label="Nama Lengkap" wajib :nilai="$user->name" />

                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-input nama="email" label="Email" tipe="email" wajib :nilai="$user->email" ikon="mail" />
                        <x-input nama="telepon" label="Telepon" :nilai="$user->telepon" ikon="phone" />
                    </div>

                    @if ($user->isPelapor())
                        <x-select nama="opd_id" label="Organisasi Perangkat Daerah" wajib
                                  :nilai="$user->opd_id" kosong="— Pilih OPD —"
                                  :pilihan="$opdList->pluck('nama', 'id')" />
                    @endif

                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-input nama="nip" label="NIP" :nilai="$user->nip" ikon="clipboard" />
                        <x-input nama="jabatan" label="Jabatan" :nilai="$user->jabatan" ikon="briefcase" />
                    </div>

                    <x-input nama="alamat" label="Alamat" :nilai="$user->alamat" ikon="map-pin" />

                    <div>
                        <label class="label">Foto Profil</label>
                        <input type="file" name="avatar" accept="image/*"
                               class="w-full text-sm text-ink-600 file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-brand-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-700 dark:text-ink-300">
                        <p class="help">JPG atau PNG · maksimal 2 MB</p>
                        @error('avatar')
                            <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-7 flex justify-end border-t border-ink-100 pt-5 dark:border-ink-800">
                    <button class="btn btn-primary"><x-ikon nama="check" class="size-4"/> Simpan Perubahan</button>
                </div>
            </form>

            <form method="POST" action="{{ route('profil.password') }}" class="card p-6 sm:p-7">
                @csrf @method('PUT')

                <div class="flex items-center gap-3 border-b border-ink-100 pb-4 dark:border-ink-800">
                    <span class="grid size-10 place-items-center rounded-xl bg-gold-100 text-gold-700 dark:bg-gold-900/40 dark:text-gold-300">
                        <x-ikon nama="lock" class="size-5"/>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-ink-900 dark:text-white">Keamanan Akun</h3>
                        <p class="text-xs text-ink-500 dark:text-ink-400">Gunakan kata sandi yang kuat dan unik</p>
                    </div>
                </div>

                <div class="mt-6 space-y-5">
                    <x-input nama="password_lama" label="Kata Sandi Saat Ini" tipe="password" wajib
                             autocomplete="current-password" />

                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-input nama="password" label="Kata Sandi Baru" tipe="password" wajib
                                 autocomplete="new-password" bantuan="Minimal 8 karakter, huruf dan angka" />
                        <x-input nama="password_confirmation" label="Ulangi Kata Sandi Baru" tipe="password" wajib
                                 autocomplete="new-password" />
                    </div>
                </div>

                <div class="mt-7 flex justify-end border-t border-ink-100 pt-5 dark:border-ink-800">
                    <button class="btn btn-primary"><x-ikon nama="key" class="size-4"/> Ganti Kata Sandi</button>
                </div>
            </form>
        </div>
    </div>

@endsection
