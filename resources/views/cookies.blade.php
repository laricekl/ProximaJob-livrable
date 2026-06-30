@extends('layouts.guest')

@section('title', 'Cookies')

@section('content')
  <main class="flex-grow pt-32">
    <x-public-page-hero
      title="Politique cookies"
      subtitle="Dernière mise à jour : 20 juin 2026"
    />

    <section class="px-4 py-16 md:px-10 md:py-20 bg-surface-container-lowest">
      <div class="mx-auto max-w-3xl">
        <div class="prose prose-lg max-w-none space-y-8 text-on-surface-variant leading-relaxed">
          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">1. Pourquoi nous utilisons des cookies</h2>
            <p>ProximaJob utilise des cookies et technologies similaires pour assurer le bon fonctionnement du site, protéger les comptes, mémoriser certains choix et, si vous y consentez, mesurer l'audience ou activer des services marketing.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">2. Cookies nécessaires</h2>
            <p>Ils permettent notamment de maintenir la session, protéger les formulaires, conserver l'état de connexion, gérer la sécurité et mémoriser votre choix de consentement. Ils ne peuvent pas être désactivés depuis le bandeau car ils sont indispensables au service.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">3. Mesure d'audience</h2>
            <p>Ces cookies servent à comprendre comment le site est utilisé: pages consultées, parcours de navigation, performances ou points de friction. Ils ne sont activés qu'après votre accord explicite.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">4. Préférences</h2>
            <p>Ils permettent de mémoriser des réglages de confort ou d'affichage non essentiels au fonctionnement strict du site. Ils restent facultatifs et sont soumis à votre choix.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">5. Marketing et réseaux sociaux</h2>
            <p>Cette catégorie couvre les pixels publicitaires, outils de retargeting, intégrations de campagnes et certains services sociaux embarqués. Ils sont désactivés par défaut tant que vous n'avez pas consenti.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">6. Durée de conservation</h2>
            <p>Le choix de consentement est conservé pendant une durée limitée afin d'éviter de vous redemander votre décision à chaque visite. Les autres durées peuvent varier selon la catégorie et l'outil utilisé lorsqu'il est activé.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">7. Modifier vos préférences</h2>
            <p>Vous pouvez à tout moment rouvrir le panneau “Gérer les cookies” affiché sur le site pour changer vos choix. Vous pouvez aussi supprimer les cookies depuis votre navigateur.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">8. Complément d'information</h2>
            <p>Pour en savoir plus sur le traitement de vos données personnelles, consultez notre <a href="{{ route('policy') }}" class="text-secondary-container font-semibold hover:underline">politique de confidentialité</a>.</p>
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection
