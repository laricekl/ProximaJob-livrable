@props([
  'iconClass' => 'w-8 md:w-10 h-auto',
  'textClass' => 'text-lg md:text-xl font-bold font-serif text-primary tracking-tight',
  'showText' => true,
])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 md:gap-2 whitespace-nowrap']) }} data-brand-logo aria-label="ProximaJob">
  <img
    src="{{ asset('img/proxi-mark.png') }}"
    alt=""
    class="{{ $iconClass }} shrink-0 object-contain"
    loading="eager"
    decoding="async"
    fetchpriority="high"
    data-brand-logo-image
  >
  @if ($showText)
    <span class="{{ $textClass }}">ProximaJob</span>
  @endif
</span>
