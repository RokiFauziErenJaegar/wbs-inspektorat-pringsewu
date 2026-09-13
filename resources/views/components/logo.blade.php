@props(['teks' => true, 'terang' => false, 'ukuran' => 'size-10'])

<a href="{{ route('beranda') }}" {{ $attributes->merge(['class' => 'group inline-flex items-center gap-3']) }}>
    <span class="relative {{ $ukuran }} shrink-0">
        <svg viewBox="0 0 48 48" class="size-full drop-shadow-sm" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="logo-perisai" x1="6" y1="3" x2="42" y2="45" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#42bd8c"/>
                    <stop offset="0.55" stop-color="#12825b"/>
                    <stop offset="1" stop-color="#0e4434"/>
                </linearGradient>
            </defs>
            {{-- Perisai: lambang perlindungan bagi pelapor --}}
            <path d="M24 3.5 41 9.5v12.9c0 10.4-7.1 18.9-17 21.6-9.9-2.7-17-11.2-17-21.6V9.5L24 3.5Z"
                  fill="url(#logo-perisai)"/>
            <path d="M24 3.5 41 9.5v12.9c0 10.4-7.1 18.9-17 21.6-9.9-2.7-17-11.2-17-21.6V9.5L24 3.5Z"
                  stroke="#f7b027" stroke-width="1.3" stroke-opacity="0.5"/>
            {{-- Peluit sebagai simbol whistleblowing --}}
            <path d="M17.5 21.5h9.2l6.8-3.4v10.4l-6.8-3.4h-1.1v2.2a4.6 4.6 0 1 1-9.2 0v-5.8Z"
                  fill="#fff" fill-opacity="0.95"/>
            <circle cx="22.1" cy="27.3" r="1.9" fill="#12825b"/>
        </svg>
        <span class="absolute -inset-1 -z-10 rounded-full bg-brand-500/25 opacity-0 blur-lg transition-opacity duration-300 group-hover:opacity-100"></span>
    </span>

    @if ($teks)
        <span class="leading-tight">
            <span class="block text-[15px] font-extrabold tracking-tight {{ $terang ? 'text-white' : 'text-ink-900 dark:text-white' }}">
                WBS Pringsewu
            </span>
            <span class="block text-[11px] font-medium {{ $terang ? 'text-white/70' : 'text-ink-500 dark:text-ink-400' }}">
                Inspektorat Kabupaten Pringsewu
            </span>
        </span>
    @endif
</a>
