@props([
    'nama',
    'label' => null,
    'nilai' => null,
    'wajib' => false,
    'bantuan' => null,
    'baris' => 5,
])

@php $galat = $errors->first($nama); @endphp

<div>
    @if ($label)
        <label for="{{ $nama }}" class="label">
            {{ $label }}
            @if ($wajib)<span class="text-rose-500">*</span>@endif
        </label>
    @endif

    <textarea name="{{ $nama }}" id="{{ $nama }}" rows="{{ $baris }}"
              @if ($wajib) required @endif
              {{ $attributes->merge(['class' => 'field resize-y '.($galat ? 'field-error' : '')]) }}>{{ old($nama, $nilai) }}</textarea>

    @if ($galat)
        <p class="mt-1.5 flex items-center gap-1 text-xs font-medium text-rose-600 dark:text-rose-400">
            <x-ikon nama="alert" class="size-3.5"/> {{ $galat }}
        </p>
    @elseif ($bantuan)
        <p class="help">{{ $bantuan }}</p>
    @endif
</div>
