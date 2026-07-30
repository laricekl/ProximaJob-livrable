<!DOCTYPE html>
<html lang="fr">
<body>
  <h1>Nouveau message de contact</h1>
  <p><strong>Nom :</strong> {{ $contact['firstname'] }} {{ $contact['name'] }}</p>
  <p><strong>Courriel :</strong> {{ $contact['email'] }}</p>
  <hr>
  <p>{!! nl2br(e($contact['message'])) !!}</p>
</body>
</html>
