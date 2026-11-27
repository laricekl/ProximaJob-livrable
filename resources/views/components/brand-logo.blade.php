@props([
  'iconClass' => 'w-8 md:w-10 h-auto',
  'textClass' => 'text-lg md:text-xl font-bold font-serif text-primary tracking-tight',
  'showText' => true,
  'variant' => 'normal',
])

@php
  $brandName = $siteSettings?->site_nom ?: 'MatchJuste';
  $customLogo = $siteSettings?->logo_url;
  $defaultLogo = $variant === 'blanc'
    ? asset('img/matchjuste-logo-blanc.png')
    : asset('img/matchjuste-logo.png');
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 md:gap-2 whitespace-nowrap']) }} data-brand-logo aria-label="{{ $brandName }}">
  @if ($customLogo)
    <img
      src="{{ $customLogo }}"
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
  @else
    <img
      src="{{ $defaultLogo }}"
      alt="{{ $brandName }}"
      class="h-7 md:h-9 w-auto shrink-0 object-contain"
      loading="eager"
      decoding="async"
      fetchpriority="high"
      data-brand-logo-image
    >
  @endif
</span>
