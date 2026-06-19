@extends('layouts.guest')

@section('title', 'Confidentialité')

@section('content')
  <main class="flex-grow pt-32">
    <x-public-page-hero
      title="Politique de confidentialité"
      subtitle="Dernière mise à jour : 14 mai 2025"
    />

    <section class="py-16 md:py-20 px-4 md:px-10 bg-surface-container-lowest">
      <div class="max-w-3xl mx-auto">
        <div class="prose prose-lg max-w-none space-y-8 text-on-surface-variant leading-relaxed">
          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">1. Collecte des informations</h2>
            <p>Nous collectons les informations que vous nous fournissez directement, comme votre nom, prénom, e-mail, téléphone et CV, ainsi que des données techniques liées à votre navigation sur la plateforme.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">2. Utilisation des données</h2>
            <p>Vos données sont utilisées pour fournir et améliorer nos services, personnaliser votre expérience, faciliter la mise en relation avec les recruteurs, traiter vos candidatures et respecter nos obligations légales.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">3. Intelligence artificielle</h2>
            <p>Notre service de personnalisation de CV utilise l'intelligence artificielle pour analyser votre profil et les offres d'emploi. Les données de CV sont traitées de manière sécurisée et ne servent pas à entraîner des modèles tiers.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">4. Partage des données</h2>
            <p>Vos informations peuvent être partagées avec les recruteurs lorsque vous postulez à une offre. Nous ne vendons pas vos données personnelles. Des données agrégées et anonymisées peuvent être utilisées à des fins statistiques.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">5. Cookies</h2>
            <p>Nous utilisons des cookies essentiels au fonctionnement du site et des cookies analytiques pour mesurer l'audience. Vous pouvez gérer vos préférences via les paramètres de votre navigateur.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">6. Sécurité</h2>
            <p>Nous mettons en oeuvre des mesures techniques et organisationnelles appropriées afin de protéger vos données contre tout accès non autorisé, altération, divulgation ou destruction.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">7. Conservation des données</h2>
            <p>Nous conservons vos données personnelles aussi longtemps que nécessaire pour fournir nos services et respecter nos obligations légales. Vous pouvez demander la suppression de votre compte à tout moment.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">8. Vos droits</h2>
            <p>Conformément aux lois applicables, vous disposez d'un droit d'accès, de rectification, d'effacement, de limitation du traitement, de portabilité et d'opposition concernant vos données personnelles.</p>
          </div>

          <div>
            <h2 class="text-xl font-bold font-serif text-primary mb-3">9. Contact DPO</h2>
            <p>Pour toute question relative à la protection de vos données, contactez notre Délégué à la Protection des Données à l'adresse <a href="mailto:dpo@proximajob.com" class="text-secondary-container font-semibold hover:underline">dpo@proximajob.com</a>.</p>
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection
