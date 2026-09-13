@props([
    'nama',
    'label' => null,
    'tipe' => 'text',
    'nilai' => null,
    'wajib' => false,
    'bantuan' => null,
    'ikon' => null,
])

@php $galat = $errors->first($nama); @endphp

<div>
    @if ($label)
        <label for="{{ $nama }}" class="label">
            {{ $label }}
            @if ($wajib)<span class="text-rose-500">*</span>@endif
        </label>
    @endif

    <div class="relative">
        @if ($ikon)
            <span class="pointer-events-none absolute inset-y-0 left-0 grid w-11 place-items-center text-ink-400">
                <x-ikon :nama="$ikon" class="size-[18px]"/>
            </span>
        @endif

        <input type="{{ $tipe }}" name="{{ $nama }}" id="{{ $nama }}"
               value="{{ old($nama, $nilai) }}"
               @if ($wajib) required @endif
               {{ $attributes->merge(['class' => 'field '.($ikon ? 'pl-11 ' : '').($galat ? 'field-error' : '')]) }}>
    </div>

    @if ($galat)
        <p class="mt-1.5 flex items-center gap-1 text-xs font-medium text-rose-600 dark:text-rose-400">
            <x-ikon nama="alert" class="size-3.5"/> {{ $galat }}
        </p>
    @elseif ($bantuan)
        <p class="help">{{ $bantuan }}</p>
    @endif
</div>
