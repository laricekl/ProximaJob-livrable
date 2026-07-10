<footer class="w-full mt-auto border-t border-primary/5 bg-white/55 backdrop-blur-sm">
  <div class="max-w-6xl mx-auto px-4 py-3 md:px-10">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 sm:gap-3 whitespace-nowrap text-[11px] sm:text-xs text-slate-500">
      <a href="{{ route('welcome') }}" class="opacity-85 hover:opacity-100 transition-opacity shrink-0">
        <x-brand-logo icon-class="w-5 h-auto" text-class="text-[11px] font-bold font-serif text-primary tracking-tight" />
      </a>

      <nav class="flex items-center gap-3 shrink min-w-0">
        <a href="{{ route('policy') }}" class="hover:text-secondary-container transition-colors">Confidentialite</a>
        <a href="{{ route('cookies.policy') }}" class="hover:text-secondary-container transition-colors">Cookies</a>
        <a href="{{ route('terms') }}" class="hover:text-secondary-container transition-colors">Conditions</a>
        <a href="{{ route('contact') }}" class="hover:text-secondary-container transition-colors">Contact</a>
      </nav>

      <span class="text-slate-400 shrink-0">© {{ date('Y') }} {{ $siteSettings?->site_nom ?? 'ProximaJob' }}.</span>
    </div>
  </div>
</footer>
