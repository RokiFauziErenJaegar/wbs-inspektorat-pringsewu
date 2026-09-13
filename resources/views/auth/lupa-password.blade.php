@extends('layouts.auth')

@section('judul', 'Lupa Kata Sandi')

@section('konten')

    <div class="mb-8">
        <span class="grid size-14 place-items-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-500/25">
            <x-ikon nama="key" class="size-6"/>
        </span>
        <h1 class="mt-5 text-3xl font-extrabold tracking-tight text-ink-900 dark:text-white">Lupa Kata Sandi?</h1>
        <p class="mt-2 text-sm/6 text-ink-500 dark:text-ink-400">
            Masukkan alamat surel yang terdaftar. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
        </p>
    </div>

    <x-flash class="mb-6" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <x-input nama="email" label="Email Terdaftar" tipe="email" wajib ikon="mail"
                 placeholder="nama@pringsewukab.go.id" autofocus autocomplete="email" />

        <button class="btn btn-primary btn-lg w-full">
            <x-ikon nama="send" class="size-5"/> Kirim Tautan Pemulihan
        </button>
    </form>

    <a href="{{ route('login') }}" class="btn btn-ghost mt-6 w-full">
        <x-ikon nama="arrow-left" class="size-4"/> Kembali ke halaman masuk
    </a>

@endsection
