@extends('layouts.guest')

@section('title', 'Conditions')

@section('content')
  <main class="flex-grow pt-32">
    <x-public-page-hero
      title="Conditions d'utilisation"
      subtitle="Dernière mise à jour : 14 mai 2025"
    />

    <section class="py-16 md:py-20 px-4 md:px-10 bg-white">
      <div class="max-w-3xl mx-auto">
        <div class="prose prose-lg max-w-none space-y-8 text-on-surface-variant leading-relaxed">
          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">1. Acceptation des conditions</h2>
            <p>En accédant et en utilisant les services de ProximaJob, vous acceptez d'être lié par les présentes conditions d'utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser nos services.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">2. Description du service</h2>
            <p>ProximaJob est une plateforme de mise en relation entre candidats et recruteurs. Nous proposons des services de recherche d'emploi, de publication d'offres, de personnalisation de CV par intelligence artificielle et de gestion de candidatures.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">3. Inscription et compte</h2>
            <p>Pour utiliser certains services, vous devez créer un compte. Vous êtes responsable de maintenir la confidentialité de vos identifiants. Vous devez fournir des informations exactes et complètes lors de votre inscription.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">4. Abonnements et paiements</h2>
            <p>Certains services sont payants. Les tarifs sont clairement indiqués avant tout achat. Les abonnements sont renouvelés automatiquement sauf annulation. Vous pouvez annuler à tout moment depuis votre compte.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">5. Protection des données</h2>
            <p>Nous collectons et traitons vos données conformément à notre <a href="{{ route('policy') }}" class="text-secondary-container font-semibold hover:underline">politique de confidentialité</a>. Nous mettons en oeuvre des mesures de sécurité appropriées pour protéger vos informations personnelles.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">6. Propriété intellectuelle</h2>
            <p>Tout le contenu de la plateforme, y compris les textes, logos, graphiques et code source, est la propriété exclusive de ProximaJob. Toute reproduction sans autorisation est interdite.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">7. Limitation de responsabilité</h2>
            <p>ProximaJob ne peut être tenue responsable des dommages indirects liés à l'utilisation de la plateforme. Nous ne garantissons pas l'exactitude des offres d'emploi publiées par les recruteurs tiers.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">8. Modification des conditions</h2>
            <p>Nous nous réservons le droit de modifier ces conditions à tout moment. Les modifications prennent effet dès leur publication. Nous vous informerons des changements importants par e-mail.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">9. Contact</h2>
            <p>Pour toute question concernant ces conditions, contactez-nous à l'adresse <a href="mailto:juridique@proximajob.com" class="text-secondary-container font-semibold hover:underline">juridique@proximajob.com</a>.</p>
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection
