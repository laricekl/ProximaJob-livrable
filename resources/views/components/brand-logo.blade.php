@props([
  'iconClass' => 'w-8 md:w-10 h-auto',
  'textClass' => 'text-lg md:text-xl font-bold font-serif text-primary tracking-tight',
  'showText' => true,
])

@php
  $brandName = $siteSettings?->site_nom ?: 'ProximaJob';
  $brandImage = $siteSettings?->logo_url ?: asset('img/proxi-mark.png');
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 md:gap-2 whitespace-nowrap']) }} data-brand-logo aria-label="{{ $brandName }}">
  <img
    src="{{ $brandImage }}"
    alt=""
    class="{{ $iconClass }} shrink-0 object-contain"
    loading="eager"
    decoding="async"
    fetchpriority="high"
    data-brand-logo-image
  >
  @if ($showText)
    <span class="{{ $textClass }}">{{ $brandName }}</span>
  @endif
</span>
