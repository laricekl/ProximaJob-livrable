@props([
  'icon',
  'label',
  'color' => 'text-outline',
  'hover' => 'hover:bg-surface-container-low',
  'tag' => 'button',
  'href' => null,
  'type' => 'submit',
])

@if ($tag === 'a')
  <a href="{{ $href }}" title="{{ $label }}" aria-label="{{ $label }}" {{ $attributes->merge(['class' => "inline-flex h-11 w-11 items-center justify-center rounded-lg transition-colors {$hover}"]) }}>
    <span class="material-symbols-outlined text-lg {{ $color }}">{{ $icon }}</span>
  </a>
@else
  <button type="{{ $type }}" title="{{ $label }}" aria-label="{{ $label }}" {{ $attributes->merge(['class' => "inline-flex h-11 w-11 items-center justify-center rounded-lg transition-colors {$hover}"]) }}>
    <span class="material-symbols-outlined text-lg {{ $color }}">{{ $icon }}</span>
  </button>
@endif
