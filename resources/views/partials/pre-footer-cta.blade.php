<section class="w-full px-4 pb-4 md:px-10 md:pb-6">
  <div class="max-w-6xl mx-auto rounded-[24px] border border-white/50 bg-white/65 backdrop-blur-sm shadow-[0_18px_40px_rgba(15,23,42,0.06)] px-5 py-5 md:px-7 md:py-6">
    <div class="flex flex-col items-center text-center">
      <p class="text-xs font-black uppercase tracking-[0.22em] text-slate-400">Pour aller plus loin</p>
      <h2 class="mt-2 text-xl md:text-2xl font-serif font-bold text-primary">Un espace simple pour connecter talents et entreprises.</h2>
      <p class="mt-2 max-w-2xl text-sm md:text-base text-slate-500">Parcourez les offres, publiez vos besoins et avancez avec des outils pensés pour chaque étape du recrutement.</p>

      <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
        <a href="{{ route('offres') }}" class="inline-flex items-center justify-center rounded-full border border-primary/10 bg-white/80 px-5 py-3 text-sm font-semibold text-primary hover:border-secondary-container/30 hover:text-secondary-container transition-colors">
          Voir les offres
        </a>
        <a href="{{ route('register') }}" class="btn-accent-shadow inline-flex items-center justify-center rounded-full bg-secondary-container hover:bg-secondary px-6 py-3 text-sm font-bold text-white transition-all">
          Ouvrir un compte
        </a>
      </div>
    </div>
  </div>
</section>
