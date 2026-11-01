<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reçu — {{ $userAbonnement->abonnement->nom ?? 'Abonnement' }} — ProximaJob</title>
  <style>
    body { font-family: Arial, Helvetica, sans-serif; color: #1a1a2e; margin: 0; background: #f4f4f6; }
    .sheet { max-width: 720px; margin: 32px auto; background: #fff; padding: 40px 44px; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
    .brand { font-size: 22px; font-weight: 700; }
    h1 { font-size: 20px; margin: 24px 0 4px; }
    .muted { color: #666; font-size: 13px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid #e6e6ec; font-size: 14px; vertical-align: top; }
    th { width: 38%; color: #555; font-weight: 600; }
    .total { font-size: 18px; font-weight: 700; }
    .actions { max-width: 720px; margin: 0 auto 40px; text-align: right; }
    .actions button { background: #e8590c; color: #fff; border: 0; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; }
    @media print {
      body { background: #fff; }
      .sheet { box-shadow: none; margin: 0 auto; }
      .actions { display: none; }
    }
  </style>
</head>
<body>
  @php
    $plan = $userAbonnement->abonnement;
    $acheteur = $userAbonnement->user;
    $reference = $userAbonnement->stripe_payment_intent_id
        ?? $userAbonnement->stripe_checkout_session_id
        ?? 'Paiement validé par l’administration';
  @endphp

  <div class="sheet">
    <div class="brand">ProximaJob</div>
    <p class="muted">Reçu d’abonnement — document généré le {{ now()->translatedFormat('d F Y') }}</p>

    <h1>{{ $plan->nom ?? 'Abonnement' }}</h1>
    <p class="muted">Abonnement n° {{ $userAbonnement->id }}</p>

    <table>
      <tr>
        <th>Client</th>
        <td>
          {{ trim(($acheteur->prenom ?? '') . ' ' . ($acheteur->name ?? '')) }}<br>
          {{ $acheteur->email }}
          @if ($acheteur->entreprise)
            <br>{{ $acheteur->entreprise->company_name }}
          @endif
        </td>
      </tr>
      <tr>
        <th>Plan</th>
        <td>{{ $plan->nom ?? 'Abonnement' }} ({{ $plan->duree ?? '—' }} jours)</td>
      </tr>
      <tr>
        <th>Période</th>
        <td>Du {{ $userAbonnement->date_debut ? $userAbonnement->date_debut->translatedFormat('d F Y') : '—' }} au {{ $userAbonnement->date_fin ? \Carbon\Carbon::parse($userAbonnement->date_fin)->translatedFormat('d F Y') : '—' }}</td>
      </tr>
      <tr>
        <th>Statut</th>
        <td>{{ $userAbonnement->effective_status }}</td>
      </tr>
      <tr>
        <th>Référence du paiement</th>
        <td>{{ $reference }}</td>
      </tr>
      <tr>
        <th>Montant payé</th>
        <td class="total">{{ number_format((float) ($plan->montant ?? 0), 2, ',', ' ') }} $ CAD</td>
      </tr>
    </table>

    <p class="muted" style="margin-top:28px;">Merci de votre confiance. Pour toute question sur ce reçu, contactez le support via la page Contact de ProximaJob.</p>
  </div>

  <div class="actions">
    <button type="button" onclick="window.print()">Imprimer / enregistrer en PDF</button>
  </div>
</body>
</html>
