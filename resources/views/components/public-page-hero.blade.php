@props([
  'title',
  'subtitle' => null,
])

<section class="py-10 md:py-12 px-4 md:px-10 bg-primary text-white text-center">
  <div class="max-w-4xl mx-auto">
    <h1 class="text-4xl md:text-5xl font-bold font-serif leading-tight tracking-tight">{{ $title }}</h1>
    @if ($subtitle)
      <p class="text-white/70 text-base md:text-lg mt-4 max-w-2xl mx-auto">{{ $subtitle }}</p>
    @endif
  </div>
</section>
