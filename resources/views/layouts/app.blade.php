<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('judul', 'Dasbor') — WBS Inspektorat Pringsewu</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">

@php
    $pengguna = auth()->user();
    $pelapor = $pengguna->isPelapor();

    $notifikasi = $pengguna->notifikasi()->limit(6)->get();
    $belumDibaca = $pengguna->notifikasi()->whereNull('dibaca_at')->count();

    if ($pelapor) {
        $navigasi = [
            ['Ringkasan', [
                ['label' => 'Dasbor', 'ikon' => 'home', 'rute' => 'pelapor.dashboard'],
                ['label' => 'Pengaduan Saya', 'ikon' => 'list', 'rute' => 'pelapor.laporan.index',
                    'aktif' => ['pelapor.laporan.index', 'pelapor.laporan.show', 'pelapor.laporan.edit']],
            ]],
            ['Tindakan', [
                ['label' => 'Buat Pengaduan', 'ikon' => 'plus', 'rute' => 'pelapor.laporan.pilih-kategori',
                    'aktif' => ['pelapor.laporan.pilih-kategori', 'pelapor.laporan.create']],
                ['label' => 'Notifikasi', 'ikon' => 'bell', 'rute' => 'notifikasi.index', 'lencana' => $belumDibaca],
                ['label' => 'Profil Saya', 'ikon' => 'user-circle', 'rute' => 'profil.edit'],
            ]],
        ];
    } else {
        $aduanBaru = \App\Models\Laporan::where('status', \App\Models\Laporan::STATUS_TERKIRIM)
            ->when($pengguna->isPetugas(), fn ($q) => $q->where('petugas_id', $pengguna->id))
            ->count();

        $navigasi = [
            ['Penanganan', [
                ['label' => 'Dasbor', 'ikon' => 'home', 'rute' => 'admin.dashboard'],
                ['label' => 'Pengaduan Masuk', 'ikon' => 'inbox', 'rute' => 'admin.laporan.index', 'aktif' => 'admin.laporan.*', 'lencana' => $aduanBaru],
                ['label' => 'Statistik', 'ikon' => 'chart', 'rute' => 'admin.statistik'],
            ]],
        ];

        if ($pengguna->isAdmin()) {
            $navigasi[] = ['Master Data', [
                ['label' => 'Perangkat Daerah', 'ikon' => 'building', 'rute' => 'admin.opd.index', 'aktif' => 'admin.opd.*'],
                ['label' => 'Kategori Aduan', 'ikon' => 'tag', 'rute' => 'admin.kategori.index', 'aktif' => 'admin.kategori.*'],
                ['label' => 'Pengguna', 'ikon' => 'users', 'rute' => 'admin.user.index', 'aktif' => 'admin.user.*'],
                ['label' => 'Berita', 'ikon' => 'megaphone', 'rute' => 'admin.artikel.index', 'aktif' => 'admin.artikel.*'],
                ['label' => 'FAQ', 'ikon' => 'book-open', 'rute' => 'admin.faq.index', 'aktif' => 'admin.faq.*'],
            ]];
        }

        $navigasi[] = ['Akun', [
            ['label' => 'Notifikasi', 'ikon' => 'bell', 'rute' => 'notifikasi.index', 'lencana' => $belumDibaca],
            ['label' => 'Profil Saya', 'ikon' => 'user-circle', 'rute' => 'profil.edit'],
        ]];
    }
@endphp

<div x-data="{ sidebar: false }" class="min-h-screen lg:flex">

    {{-- Lapisan gelap untuk tampilan seluler --}}
    <div x-show="sidebar" x-cloak x-transition.opacity @click="sidebar = false"
         class="fixed inset-0 z-40 bg-ink-950/50 backdrop-blur-sm lg:hidden"></div>

    {{-- ------------------------------------------------------------- --}}
    {{-- Bilah samping                                                   --}}
    {{-- ------------------------------------------------------------- --}}
    <aside x-cloak
           :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 flex w-72 shrink-0 flex-col border-r border-ink-200 bg-white transition-transform duration-300 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 dark:border-ink-800 dark:bg-ink-900">

        <div class="flex h-18 items-center justify-between gap-2 border-b border-ink-100 px-5 dark:border-ink-800">
            <x-logo ukuran="size-9" />
            <button type="button" @click="sidebar = false" class="btn btn-ghost size-9 !p-0 lg:hidden" aria-label="Tutup menu">
                <x-ikon nama="x" class="size-5"/>
            </button>
        </div>

        <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
            @foreach ($navigasi as [$grup, $tautan])
                <div>
                    <p class="mb-2 px-3 text-[11px] font-bold tracking-widest text-ink-400 uppercase dark:text-ink-500">{{ $grup }}</p>
                    <div class="space-y-1">
                        @foreach ($tautan as $item)
                            @php $aktif = request()->routeIs(...(array) ($item['aktif'] ?? $item['rute'])); @endphp
                            <a href="{{ route($item['rute']) }}"
                               class="sidebar-link {{ $aktif ? 'sidebar-link-active' : '' }}">
                                <x-ikon :nama="$item['ikon']" class="size-[18px] shrink-0"/>
                                <span class="flex-1 truncate">{{ $item['label'] }}</span>
                                @if (($item['lencana'] ?? 0) > 0)
                                    <span class="grid min-w-5 place-items-center rounded-full px-1.5 py-0.5 text-[10px] font-bold {{ $aktif ? 'bg-white/25 text-white' : 'bg-rose-500 text-white' }}">
                                        {{ $item['lencana'] > 99 ? '99+' : $item['lencana'] }}
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        {{-- Kartu ajakan di kaki bilah samping --}}
        <div class="p-3">
            @if ($pelapor)
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800 p-4 text-white">
                    <span class="pointer-events-none absolute -top-6 -right-6 size-24 rounded-full bg-white/10"></span>
                    <x-ikon nama="megaphone" class="size-6"/>
                    <p class="mt-2 text-sm font-bold">Ada dugaan pelanggaran?</p>
                    <p class="mt-1 text-xs text-white/75">Laporkan sekarang, identitas Anda kami lindungi.</p>
                    <a href="{{ route('pelapor.laporan.pilih-kategori') }}" class="btn btn-gold btn-sm mt-3 w-full">
                        <x-ikon nama="plus" class="size-4"/> Buat Pengaduan
                    </a>
                </div>
            @else
                <a href="{{ route('beranda') }}" class="sidebar-link">
                    <x-ikon nama="external-link" class="size-[18px]"/> Lihat Situs Publik
                </a>
            @endif
        </div>
    </aside>

    {{-- ------------------------------------------------------------- --}}
    {{-- Area konten                                                     --}}
    {{-- ------------------------------------------------------------- --}}
    <div class="flex min-w-0 flex-1 flex-col">

        <header class="sticky top-0 z-30 border-b border-ink-200 bg-white/85 backdrop-blur-xl dark:border-ink-800 dark:bg-ink-950/85">
            <div class="flex h-18 items-center gap-3 px-4 sm:px-6">
                <button type="button" @click="sidebar = true" class="btn btn-ghost size-10 !p-0 lg:hidden" aria-label="Buka menu">
                    <x-ikon nama="menu" class="size-5"/>
                </button>

                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-lg font-extrabold tracking-tight text-ink-900 dark:text-white">
                        @yield('judul', 'Dasbor')
                    </h1>
                    @hasSection('subjudul')
                        <p class="truncate text-xs text-ink-500 dark:text-ink-400">@yield('subjudul')</p>
                    @endif
                </div>

                <button type="button" onclick="gantiTema()" class="btn btn-ghost size-10 !p-0" aria-label="Ganti tema">
                    <x-ikon nama="sun" class="size-5 dark:hidden"/>
                    <x-ikon nama="moon" class="hidden size-5 dark:block"/>
                </button>

                {{-- Notifikasi --}}
                <div x-data="{ buka: false }" class="relative">
                    <button type="button" @click="buka = !buka" class="btn btn-ghost relative size-10 !p-0" aria-label="Notifikasi">
                        <x-ikon nama="bell" class="size-5"/>
                        @if ($belumDibaca > 0)
                            <span class="absolute top-1.5 right-1.5 flex size-2.5">
                                <span class="absolute inline-flex size-full animate-ping rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex size-2.5 rounded-full bg-rose-500"></span>
                            </span>
                        @endif
                    </button>

                    <div x-show="buka" x-cloak @click.outside="buka = false"
                         x-transition:enter="transition duration-200 ease-out"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         class="card absolute right-0 mt-2 w-80 overflow-hidden shadow-[var(--shadow-lift)]">
                        <div class="flex items-center justify-between border-b border-ink-100 px-4 py-3 dark:border-ink-800">
                            <p class="text-sm font-bold text-ink-900 dark:text-white">Notifikasi</p>
                            @if ($belumDibaca > 0)
                                <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
                                    @csrf
                                    <button class="text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400">Tandai dibaca</button>
                                </form>
                            @endif
                        </div>

                        <div class="max-h-80 divide-y divide-ink-100 overflow-y-auto dark:divide-ink-800">
                            @forelse ($notifikasi as $notif)
                                <a href="{{ route('notifikasi.buka', $notif) }}"
                                   class="flex gap-3 px-4 py-3 transition hover:bg-ink-50 dark:hover:bg-ink-800/50 {{ $notif->dibaca_at ? '' : 'bg-brand-50/60 dark:bg-brand-950/25' }}">
                                    <span class="mt-0.5 grid size-8 shrink-0 place-items-center rounded-lg bg-brand-100 text-brand-700 dark:bg-brand-900/50 dark:text-brand-300">
                                        <x-ikon :nama="$notif->ikon" class="size-4"/>
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-semibold text-ink-800 dark:text-ink-100">{{ $notif->judul }}</span>
                                        <span class="mt-0.5 block line-clamp-2 text-xs text-ink-500 dark:text-ink-400">{{ $notif->pesan }}</span>
                                        <span class="mt-1 block text-[11px] text-ink-400">{{ $notif->created_at->diffForHumans() }}</span>
                                    </span>
                                </a>
                            @empty
                                <p class="px-4 py-10 text-center text-sm text-ink-500 dark:text-ink-400">Belum ada notifikasi.</p>
                            @endforelse
                        </div>

                        <a href="{{ route('notifikasi.index') }}" class="block border-t border-ink-100 px-4 py-3 text-center text-xs font-semibold text-brand-600 hover:bg-ink-50 dark:border-ink-800 dark:text-brand-400 dark:hover:bg-ink-800/50">
                            Lihat semua notifikasi
                        </a>
                    </div>
                </div>

                {{-- Menu pengguna --}}
                <div x-data="{ buka: false }" class="relative">
                    <button type="button" @click="buka = !buka" class="flex items-center gap-2.5 rounded-xl p-1 pr-2 transition hover:bg-ink-100 dark:hover:bg-ink-800">
                        @if ($pengguna->avatar_url)
                            <img src="{{ $pengguna->avatar_url }}" alt="" class="size-9 rounded-lg object-cover">
                        @else
                            <span class="grid size-9 place-items-center rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 text-xs font-bold text-white">
                                {{ $pengguna->inisial }}
                            </span>
                        @endif
                        <span class="hidden text-left sm:block">
                            <span class="block max-w-32 truncate text-sm font-semibold text-ink-900 dark:text-white">{{ $pengguna->name }}</span>
                            <span class="block text-[11px] text-ink-500 dark:text-ink-400">{{ $pengguna->role_label }}</span>
                        </span>
                        <x-ikon nama="chevron-down" class="hidden size-4 text-ink-400 sm:block"/>
                    </button>

                    <div x-show="buka" x-cloak @click.outside="buka = false"
                         x-transition:enter="transition duration-200 ease-out"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         class="card absolute right-0 mt-2 w-60 overflow-hidden p-1.5 shadow-[var(--shadow-lift)]">
                        <div class="border-b border-ink-100 px-3 py-2.5 dark:border-ink-800">
                            <p class="truncate text-sm font-bold text-ink-900 dark:text-white">{{ $pengguna->name }}</p>
                            <p class="truncate text-xs text-ink-500 dark:text-ink-400">{{ $pengguna->email }}</p>
                            @if ($pengguna->opd)
                                <p class="mt-1 truncate text-[11px] font-medium text-brand-600 dark:text-brand-400">{{ $pengguna->opd->nama }}</p>
                            @endif
                        </div>

                        <a href="{{ route('profil.edit') }}" class="sidebar-link mt-1.5">
                            <x-ikon nama="user-circle" class="size-[18px]"/> Profil Saya
                        </a>
                        <a href="{{ route('beranda') }}" class="sidebar-link">
                            <x-ikon nama="external-link" class="size-[18px]"/> Situs Publik
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-ink-100 pt-1.5 dark:border-ink-800">
                            @csrf
                            <button type="submit" class="sidebar-link w-full text-rose-600 hover:bg-rose-50 hover:text-rose-700 dark:text-rose-400 dark:hover:bg-rose-950/40">
                                <x-ikon nama="logout" class="size-[18px]"/> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 px-4 py-6 sm:px-6 sm:py-8">
            <div class="mx-auto max-w-7xl space-y-6">
                <x-flash />
                @yield('konten')
            </div>
        </main>

        <footer class="border-t border-ink-200 px-6 py-5 text-center text-xs text-ink-500 dark:border-ink-800 dark:text-ink-400">
            &copy; {{ date('Y') }} Inspektorat Kabupaten Pringsewu — Whistleblowing System v1.0
        </footer>
    </div>
</div>

</body>
</html>
