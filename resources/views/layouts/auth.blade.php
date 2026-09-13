<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('judul', 'Masuk') — WBS Inspektorat Pringsewu</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">

<div class="grid min-h-screen lg:grid-cols-[1.05fr_1fr]">

    {{-- Panel kiri: identitas & jaminan --}}
    <div class="relative hidden overflow-hidden bg-gradient-to-br from-brand-800 via-brand-900 to-ink-950 lg:flex lg:flex-col lg:justify-between lg:p-12">
        <div class="bg-grid absolute inset-0 opacity-25"></div>
        <div class="absolute -top-24 -right-24 size-96 rounded-full bg-brand-500/25 blur-3xl"></div>
        <div class="absolute -bottom-32 -left-20 size-96 rounded-full bg-gold-500/15 blur-3xl"></div>

        <div class="relative">
            <x-logo terang ukuran="size-11" />
        </div>

        <div class="relative max-w-md">
            <span class="badge bg-white/10 text-brand-200 ring-white/20">
                <x-ikon nama="shield-check" class="size-3.5"/> Kanal resmi Inspektorat
            </span>

            <h2 class="mt-6 text-4xl leading-tight font-extrabold text-white">
                Suara Anda menjaga <span class="teks-gradien">integritas</span> Kabupaten Pringsewu.
            </h2>

            <p class="mt-4 text-[15px]/7 text-white/70">
                Sampaikan dugaan pelanggaran secara aman melalui Whistleblowing System.
                Setiap laporan ditangani langsung oleh Inspektorat dengan menjunjung tinggi kerahasiaan pelapor.
            </p>

            <div class="mt-8 space-y-3.5">
                @foreach ([
                    ['lock', 'Identitas pelapor dijamin kerahasiaannya'],
                    ['activity', 'Status penanganan dapat dipantau kapan saja'],
                    ['gavel', 'Ditindaklanjuti sesuai peraturan yang berlaku'],
                ] as [$ikon, $teks])
                    <div class="flex items-center gap-3 text-sm text-white/85">
                        <span class="grid size-9 shrink-0 place-items-center rounded-xl bg-white/10 text-brand-200 ring-1 ring-white/15">
                            <x-ikon :nama="$ikon" class="size-[18px]"/>
                        </span>
                        {{ $teks }}
                    </div>
                @endforeach
            </div>
        </div>

        <p class="relative text-xs text-white/45">
            &copy; {{ date('Y') }} Inspektorat Kabupaten Pringsewu
        </p>
    </div>

    {{-- Panel kanan: formulir --}}
    <div class="flex flex-col">
        <div class="flex items-center justify-between px-6 pt-6 lg:justify-end lg:px-10">
            <span class="lg:hidden"><x-logo ukuran="size-9" /></span>
            <div class="flex items-center gap-2">
                <button type="button" onclick="gantiTema()" class="btn btn-ghost size-10 !p-0" aria-label="Ganti tema">
                    <x-ikon nama="sun" class="size-5 dark:hidden"/>
                    <x-ikon nama="moon" class="hidden size-5 dark:block"/>
                </button>
                <a href="{{ route('beranda') }}" class="btn btn-ghost btn-sm">
                    <x-ikon nama="arrow-left" class="size-4"/> Beranda
                </a>
            </div>
        </div>

        <div class="flex flex-1 items-center justify-center px-5 py-10 sm:px-8">
            <div class="w-full max-w-md animate-fade-up">
                @yield('konten')
            </div>
        </div>
    </div>
</div>

</body>
</html>
