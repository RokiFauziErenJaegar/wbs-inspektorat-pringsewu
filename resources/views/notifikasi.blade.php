@extends('layouts.app')

@section('judul', 'Notifikasi')
@section('subjudul', 'Pemberitahuan terkait pengaduan Anda')

@section('konten')

    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-ink-500 dark:text-ink-400">
            Total <strong class="text-ink-900 dark:text-white">{{ $notifikasi->total() }}</strong> notifikasi
        </p>

        @if ($notifikasi->whereNull('dibaca_at')->count() > 0)
            <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
                @csrf
                <button class="btn btn-outline btn-sm">
                    <x-ikon nama="check" class="size-3.5"/> Tandai semua sudah dibaca
                </button>
            </form>
        @endif
    </div>

    <div class="card overflow-hidden">
        @if ($notifikasi->isEmpty())
            <x-kosong ikon="bell" judul="Belum ada notifikasi"
                      pesan="Pemberitahuan mengenai perkembangan pengaduan akan muncul di sini." />
        @else
            <div class="divide-y divide-ink-100 dark:divide-ink-800">
                @foreach ($notifikasi as $item)
                    <a href="{{ route('notifikasi.buka', $item) }}"
                       class="flex gap-4 px-5 py-4 transition hover:bg-ink-50 dark:hover:bg-ink-800/40 {{ $item->dibaca_at ? '' : 'bg-brand-50/50 dark:bg-brand-950/20' }}">

                        <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-white">
                            <x-ikon :nama="$item->ikon" class="size-5"/>
                        </span>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="truncate text-sm font-bold text-ink-900 dark:text-white">{{ $item->judul }}</p>
                                @unless ($item->dibaca_at)
                                    <span class="size-2 shrink-0 rounded-full bg-rose-500"></span>
                                @endunless
                            </div>
                            <p class="mt-1 text-sm/6 text-ink-600 dark:text-ink-300">{{ $item->pesan }}</p>
                            <p class="mt-1.5 text-xs text-ink-400">
                                {{ $item->created_at->translatedFormat('d F Y, H:i') }} · {{ $item->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <x-ikon nama="chevron-right" class="mt-3 size-4 shrink-0 text-ink-400"/>
                    </a>
                @endforeach
            </div>

            <div class="border-t border-ink-100 px-5 py-4 dark:border-ink-800">{{ $notifikasi->links() }}</div>
        @endif
    </div>

@endsection
