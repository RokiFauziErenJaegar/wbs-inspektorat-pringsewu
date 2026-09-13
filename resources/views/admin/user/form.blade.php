@extends('layouts.app')

@section('judul', $user->exists ? 'Ubah Pengguna' : 'Tambah Pengguna')

@section('konten')

    <div>
        <a href="{{ route('admin.user.index') }}" class="btn btn-ghost btn-sm">
            <x-ikon nama="arrow-left" class="size-4"/> Kembali ke daftar pengguna
        </a>
    </div>

    <form method="POST" action="{{ $user->exists ? route('admin.user.update', $user) : route('admin.user.store') }}"
          x-data="{ peran: '{{ old('role', $user->role ?? 'petugas') }}' }"
          class="card mx-auto max-w-3xl p-6 sm:p-7">
        @csrf
        @if ($user->exists) @method('PUT') @endif

        <div class="flex items-center gap-3 border-b border-ink-100 pb-4 dark:border-ink-800">
            <span class="grid size-10 place-items-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                <x-ikon nama="user-circle" class="size-5"/>
            </span>
            <div>
                <h3 class="text-base font-bold text-ink-900 dark:text-white">Data Pengguna</h3>
                <p class="text-xs text-ink-500 dark:text-ink-400">
                    {{ $user->exists ? 'Kosongkan kata sandi bila tidak ingin menggantinya' : 'Akun dapat langsung digunakan setelah disimpan' }}
                </p>
            </div>
        </div>

        <div class="mt-6 space-y-5">
            <x-input nama="name" label="Nama Lengkap" wajib :nilai="$user->name" />

            <div class="grid gap-5 sm:grid-cols-2">
                <x-input nama="username" label="Username" wajib :nilai="$user->username" bantuan="Minimal 4 karakter" />
                <x-input nama="email" label="Email" tipe="email" wajib :nilai="$user->email" />
            </div>

            <div>
                <label class="label">Peran <span class="text-rose-500">*</span></label>
                <div class="grid gap-3 sm:grid-cols-3">
                    @foreach ([
                        ['admin', 'Administrator', 'Akses penuh termasuk master data', 'shield-check'],
                        ['petugas', 'Petugas', 'Menangani berkas yang didisposisikan', 'briefcase'],
                        ['pelapor', 'Pelapor OPD', 'Menyampaikan pengaduan', 'megaphone'],
                    ] as [$nilai, $label, $ket, $ikon])
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="{{ $nilai }}" x-model="peran" class="peer sr-only"
                                   @checked(old('role', $user->role ?? 'petugas') === $nilai)>
                            <span class="block rounded-xl border border-ink-200 p-4 transition peer-checked:border-brand-500 peer-checked:bg-brand-50 hover:border-brand-300 dark:border-ink-700 dark:peer-checked:bg-brand-950/30">
                                <x-ikon :nama="$ikon" class="size-5 text-brand-600 dark:text-brand-400"/>
                                <span class="mt-2 block text-sm font-bold text-ink-900 dark:text-white">{{ $label }}</span>
                                <span class="mt-0.5 block text-xs/5 text-ink-500 dark:text-ink-400">{{ $ket }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('role')
                    <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="opd_id" class="label">
                    Organisasi Perangkat Daerah
                    <span class="text-rose-500" x-show="peran === 'pelapor'">*</span>
                </label>

                <div class="relative">
                    <select name="opd_id" id="opd_id" class="field appearance-none pr-10 @error('opd_id') field-error @enderror">
                        <option value="">— Tidak ditentukan —</option>
                        @foreach ($opdList as $item)
                            <option value="{{ $item->id }}" @selected(old('opd_id', $user->opd_id) == $item->id)>{{ $item->nama }}</option>
                        @endforeach
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-0 grid w-10 place-items-center text-ink-400">
                        <x-ikon nama="chevron-down" class="size-4"/>
                    </span>
                </div>

                @error('opd_id')
                    <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @else
                    <p class="help" x-text="peran === 'pelapor' ? 'Wajib diisi untuk peran pelapor.' : 'Opsional untuk administrator dan petugas.'"></p>
                @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
                <x-input nama="nip" label="NIP" :nilai="$user->nip" />
                <x-input nama="jabatan" label="Jabatan" :nilai="$user->jabatan" />
                <x-input nama="telepon" label="Telepon" :nilai="$user->telepon" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <x-input nama="password" label="{{ $user->exists ? 'Kata Sandi Baru' : 'Kata Sandi' }}"
                         tipe="password" :wajib="! $user->exists" autocomplete="new-password"
                         bantuan="Minimal 8 karakter, huruf dan angka" />
                <x-input nama="password_confirmation" label="Ulangi Kata Sandi"
                         tipe="password" :wajib="! $user->exists" autocomplete="new-password" />
            </div>

            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-ink-200 p-4 transition hover:border-brand-300 hover:bg-brand-50/50 dark:border-ink-700 dark:hover:border-brand-700 dark:hover:bg-brand-950/20">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active ?? true))
                       class="mt-0.5 size-4 rounded border-ink-300 text-brand-600 focus:ring-2 focus:ring-brand-500/30 dark:border-ink-600 dark:bg-ink-900">
                <span>
                    <span class="block text-sm font-bold text-ink-900 dark:text-white">Akun aktif</span>
                    <span class="mt-0.5 block text-xs text-ink-500 dark:text-ink-400">Akun nonaktif tidak dapat masuk ke sistem.</span>
                </span>
            </label>
        </div>

        <div class="mt-7 flex justify-end gap-3 border-t border-ink-100 pt-5 dark:border-ink-800">
            <a href="{{ route('admin.user.index') }}" class="btn btn-outline">Batal</a>
            <button class="btn btn-primary"><x-ikon nama="check" class="size-4"/> Simpan</button>
        </div>
    </form>

@endsection
