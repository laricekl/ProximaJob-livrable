<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<body>
  <h1>{{ __('Nouveau message de contact') }}</h1>
  <p><strong>{{ __('Nom :') }}</strong> {{ $contact['firstname'] }} {{ $contact['name'] }}</p>
  <p><strong>{{ __('Courriel :') }}</strong> {{ $contact['email'] }}</p>
  <hr>
  <p>{!! nl2br(e($contact['message'])) !!}</p>
</body>
</html>
