<!DOCTYPE html>
<html lang="id" class="scroll-pt-24">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('deskripsi', 'Whistleblowing System Inspektorat Kabupaten Pringsewu — sarana pelaporan dugaan pelanggaran di lingkungan Pemerintah Kabupaten Pringsewu dengan jaminan kerahasiaan identitas pelapor.')">

    <title>@yield('judul', 'Beranda') — WBS Inspektorat Kabupaten Pringsewu</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">

@php
    $menu = [
        ['label' => 'Beranda', 'rute' => 'beranda'],
        ['label' => 'Cara Melapor', 'rute' => 'cara-melapor'],
        ['label' => 'Lacak Aduan', 'rute' => 'lacak.index'],
        ['label' => 'Berita', 'rute' => 'berita.index'],
        ['label' => 'FAQ', 'rute' => 'faq'],
    ];
@endphp

<a href="#konten" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[100] focus:rounded-lg focus:bg-brand-600 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
    Lompat ke konten utama
</a>

<header x-data="{ menuBuka: false, digulir: false }"
        @scroll.window="digulir = window.scrollY > 20"
        class="sticky top-0 z-40 transition-all duration-300"
        :class="digulir ? 'border-b border-ink-200/80 bg-white/85 backdrop-blur-xl shadow-[var(--shadow-soft)] dark:border-ink-800 dark:bg-ink-950/85' : 'bg-transparent'">

    <div class="mx-auto flex h-18 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <x-logo />

        <nav class="hidden items-center gap-1 lg:flex">
            @foreach ($menu as $item)
                @php $aktif = request()->routeIs($item['rute']) || ($item['rute'] === 'berita.index' && request()->routeIs('berita.*')); @endphp
                <a href="{{ route($item['rute']) }}"
                   class="relative rounded-lg px-3.5 py-2 text-sm font-semibold transition-colors {{ $aktif ? 'text-brand-700 dark:text-brand-400' : 'text-ink-600 hover:text-ink-900 dark:text-ink-300 dark:hover:text-white' }}">
                    {{ $item['label'] }}
                    @if ($aktif)
                        <span class="absolute inset-x-3.5 -bottom-0.5 h-0.5 rounded-full bg-brand-500"></span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            <button type="button" onclick="gantiTema()" class="btn btn-ghost size-10 !p-0" aria-label="Ganti tema">
                <x-ikon nama="sun" class="size-5 dark:hidden"/>
                <x-ikon nama="moon" class="hidden size-5 dark:block"/>
            </button>

            @auth
                <a href="{{ auth()->user()->isPelapor() ? route('pelapor.dashboard') : route('admin.dashboard') }}"
                   class="btn btn-primary">
                    <x-ikon nama="home" class="size-4"/>
                    <span class="hidden sm:inline">Dasbor</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline hidden sm:inline-flex">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <x-ikon nama="megaphone" class="size-4"/>
                    <span class="hidden sm:inline">Lapor Sekarang</span>
                    <span class="sm:hidden">Lapor</span>
                </a>
            @endauth

            <button type="button" @click="menuBuka = !menuBuka" class="btn btn-ghost size-10 !p-0 lg:hidden" aria-label="Menu">
                <x-ikon nama="menu" class="size-5" x-show="!menuBuka"/>
                <x-ikon nama="x" class="size-5" x-show="menuBuka" x-cloak/>
            </button>
        </div>
    </div>

    {{-- Menu seluler --}}
    <div x-show="menuBuka" x-collapse x-cloak class="border-t border-ink-200 bg-white lg:hidden dark:border-ink-800 dark:bg-ink-950">
        <nav class="mx-auto max-w-7xl space-y-1 px-4 py-4 sm:px-6">
            @foreach ($menu as $item)
                <a href="{{ route($item['rute']) }}"
                   class="sidebar-link {{ request()->routeIs($item['rute']) ? 'sidebar-link-active' : '' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
            @guest
                <a href="{{ route('login') }}" class="sidebar-link">Masuk ke Akun</a>
            @endguest
        </nav>
    </div>
</header>

<main id="konten">
    @if (session('sukses') || session('gagal') || session('info'))
        <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
            <x-flash />
        </div>
    @endif

    {{ $slot ?? '' }}
    @yield('konten')
</main>

<footer class="mt-24 border-t border-ink-200 bg-white dark:border-ink-800 dark:bg-ink-950">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[1.4fr_1fr_1fr_1.2fr]">
            <div>
                <x-logo />
                <p class="mt-4 max-w-sm text-sm/6 text-ink-500 dark:text-ink-400">
                    Sarana pelaporan dugaan pelanggaran di lingkungan Pemerintah Kabupaten Pringsewu.
                    Identitas pelapor dijamin kerahasiaannya sesuai peraturan perundang-undangan.
                </p>
                <div class="mt-5 flex items-center gap-2 rounded-xl border border-brand-200 bg-brand-50 px-3 py-2 text-xs font-semibold text-brand-700 dark:border-brand-800/60 dark:bg-brand-950/40 dark:text-brand-300">
                    <x-ikon nama="lock" class="size-4"/>
                    Terenkripsi &amp; dirahasiakan
                </div>
            </div>

            <div>
                <h4 class="text-sm font-bold text-ink-900 dark:text-white">Navigasi</h4>
                <ul class="mt-4 space-y-2.5 text-sm">
                    @foreach ($menu as $item)
                        <li>
                            <a href="{{ route($item['rute']) }}" class="text-ink-500 transition hover:text-brand-600 dark:text-ink-400 dark:hover:text-brand-400">
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-bold text-ink-900 dark:text-white">Layanan</h4>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('register') }}" class="text-ink-500 transition hover:text-brand-600 dark:text-ink-400 dark:hover:text-brand-400">Daftar Akun OPD</a></li>
                    <li><a href="{{ route('login') }}" class="text-ink-500 transition hover:text-brand-600 dark:text-ink-400 dark:hover:text-brand-400">Masuk Sistem</a></li>
                    <li><a href="{{ route('lacak.index') }}" class="text-ink-500 transition hover:text-brand-600 dark:text-ink-400 dark:hover:text-brand-400">Lacak Status Aduan</a></li>
                    <li><a href="{{ route('faq') }}" class="text-ink-500 transition hover:text-brand-600 dark:text-ink-400 dark:hover:text-brand-400">Pertanyaan Umum</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-bold text-ink-900 dark:text-white">Kontak Inspektorat</h4>
                <ul class="mt-4 space-y-3 text-sm text-ink-500 dark:text-ink-400">
                    <li class="flex gap-2.5">
                        <x-ikon nama="map-pin" class="mt-0.5 size-4 shrink-0 text-brand-500"/>
                        <span>Komplek Perkantoran Pemkab Pringsewu, Jl. Jenderal Sudirman, Pringsewu, Lampung 35373</span>
                    </li>
                    <li class="flex gap-2.5">
                        <x-ikon nama="phone" class="mt-0.5 size-4 shrink-0 text-brand-500"/>
                        <span>(0729) 700 xxx</span>
                    </li>
                    <li class="flex gap-2.5">
                        <x-ikon nama="mail" class="mt-0.5 size-4 shrink-0 text-brand-500"/>
                        <span>inspektorat@pringsewukab.go.id</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-3 border-t border-ink-200 pt-6 text-xs text-ink-500 sm:flex-row dark:border-ink-800 dark:text-ink-400">
            <p>&copy; {{ date('Y') }} Inspektorat Kabupaten Pringsewu. Seluruh hak cipta dilindungi.</p>
            <p>Whistleblowing System v1.0</p>
        </div>
    </div>
</footer>

</body>
</html>
