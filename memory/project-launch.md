---
name: project-launch
description: Procédure correcte pour lancer le projet en local (serveur + CSS + tests)
metadata:
  type: project
---

# Démarrage du projet ProximaJob en local

## Serveur Laravel

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Toujours utiliser `--host=127.0.0.1` (IPv4 explicite), jamais `localhost` seul (peut résoudre en IPv6 `[::1]` et causer des problèmes cross-origin avec le CSS).

## CSS / JS — Assets front-end

Deux modes :

### Mode 1 : Build statique (recommandé pour les tests)
```bash
npm run build
```
Génère `public/build/assets/app-*.css` et `app-*.js`. Le CSS est servi directement par Laravel, aucun processus supplémentaire nécessaire. **Fiable à 100%.** Relancer `npm run build` après chaque modification du CSS/JS.

### Mode 2 : Dev avec hot-reload (pour modifier le CSS en direct)
```bash
npm run dev
```
Vite écoute sur `http://127.0.0.1:5173`. Le `vite.config.js` a été modifié avec `server.host: '127.0.0.1'` pour forcer l'IPv4 et éviter les incompatibilités avec le serveur Laravel.

## Base de données

- SQLite : `database/database.sqlite`
- Un symlink `storage/database.sqlite → database/database.sqlite` garantit que les helpers Playwright (`runLaravelExpression`) et le serveur web utilisent la **même** base

## Lancer les tests Playwright

```bash
# Tous les tests (séquentiel recommandé pour éviter les conflits)
npx playwright test --workers=1

# Avec affichage visuel
UI_SLOW_MO=2000 npx playwright test --headed --project=desktop-chromium --workers=1

# Uniquement les tests candidat
npx playwright test tests/ui/candidate-*.spec.js --workers=1

# Le parcours complet
npx playwright test tests/ui/candidate-complete-journey.spec.js --workers=1
```

## Démarrage complet en un script

```bash
# 1. Build CSS (une fois)
npm run build

# 2. Lancer Laravel
php artisan serve --host=127.0.0.1 --port=8000

# 3. Lancer les tests
npx playwright test --workers=1
```

**Why:** Sans `npm run build`, le CSS est absent (pages sans style). Sans `--host=127.0.0.1`, Vite peut résoudre en IPv6 et le CSS ne charge pas. Le `--workers=1` évite les contaminations entre tests qui partagent les mêmes comptes.
