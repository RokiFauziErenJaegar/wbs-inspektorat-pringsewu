@php
    $gaya = [
        'sukses' => ['ikon' => 'check-badge', 'kelas' => 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-800/60 dark:bg-emerald-950/40 dark:text-emerald-200'],
        'gagal' => ['ikon' => 'alert', 'kelas' => 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-800/60 dark:bg-rose-950/40 dark:text-rose-200'],
        'info' => ['ikon' => 'info', 'kelas' => 'border-sky-200 bg-sky-50 text-sky-800 dark:border-sky-800/60 dark:bg-sky-950/40 dark:text-sky-200'],
    ];

    // Galat "login" sudah ditampilkan tersendiri pada halaman masuk.
    $adaGalat = $errors->any() && ! $errors->has('login');
    $tampil = collect(array_keys($gaya))->filter(fn ($kunci) => session()->has($kunci));
@endphp

@if ($tampil->isNotEmpty() || $adaGalat)
    <div {{ $attributes->merge(['class' => 'space-y-3']) }}>
        @foreach ($tampil as $kunci)
            <div x-data="{ tampil: true }" x-show="tampil" x-transition.duration.300ms
                 class="flex items-start gap-3 rounded-2xl border px-4 py-3.5 text-sm font-medium shadow-[var(--shadow-soft)] {{ $gaya[$kunci]['kelas'] }}">
                <x-ikon :nama="$gaya[$kunci]['ikon']" class="mt-0.5 size-5 shrink-0"/>
                <p class="flex-1">{{ session($kunci) }}</p>
                <button type="button" @click="tampil = false" class="shrink-0 opacity-60 transition hover:opacity-100" aria-label="Tutup">
                    <x-ikon nama="x" class="size-4"/>
                </button>
            </div>
        @endforeach

        @if ($adaGalat)
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm dark:border-rose-800/60 dark:bg-rose-950/40">
                <div class="flex items-center gap-2 font-semibold text-rose-800 dark:text-rose-200">
                    <x-ikon nama="alert" class="size-5"/>
                    Terdapat {{ $errors->count() }} kesalahan pada isian Anda
                </div>
                <ul class="mt-2 list-disc space-y-1 pl-8 text-rose-700 dark:text-rose-300">
                    @foreach ($errors->unique() as $galat)
                        <li>{{ $galat }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
