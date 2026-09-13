@props([
    'nama',
    'label' => null,
    'nilai' => null,
    'wajib' => false,
    'bantuan' => null,
    'pilihan' => [],
    'kosong' => null,
])

@php
    $galat = $errors->first($nama);
    $terpilih = old($nama, $nilai);
@endphp

<div>
    @if ($label)
        <label for="{{ $nama }}" class="label">
            {{ $label }}
            @if ($wajib)<span class="text-rose-500">*</span>@endif
        </label>
    @endif

    <div class="relative">
        <select name="{{ $nama }}" id="{{ $nama }}"
                @if ($wajib) required @endif
                {{ $attributes->merge(['class' => 'field appearance-none pr-10 '.($galat ? 'field-error' : '')]) }}>
            @if ($kosong !== null)
                <option value="">{{ $kosong }}</option>
            @endif

            @foreach ($pilihan as $kunci => $teks)
                <option value="{{ $kunci }}" @selected((string) $terpilih === (string) $kunci)>{{ $teks }}</option>
            @endforeach

            {{ $slot }}
        </select>

        <span class="pointer-events-none absolute inset-y-0 right-0 grid w-10 place-items-center text-ink-400">
            <x-ikon nama="chevron-down" class="size-4"/>
        </span>
    </div>

    @if ($galat)
        <p class="mt-1.5 flex items-center gap-1 text-xs font-medium text-rose-600 dark:text-rose-400">
            <x-ikon nama="alert" class="size-3.5"/> {{ $galat }}
        </p>
    @elseif ($bantuan)
        <p class="help">{{ $bantuan }}</p>
    @endif
</div>
