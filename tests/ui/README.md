# UI smoke tests

Petit projet Playwright pour valider rapidement les parcours visibles `public`, `candidat` et `entreprise`.

## Installation

```bash
npm install
npx playwright install
```

## Lancer les tests

Dans un terminal, demarrer Laravel:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Dans un autre terminal:

```bash
npm run ui:test
```

Mode visible:

```bash
npm run ui:test:headed
```

Mode demo lent:

```bash
npm run ui:test:demo
```

Suites dediees:

```bash
npm run ui:test:demo-suite
npm run ui:test:e2e-confidence
```

Choisir la lenteur du mode visible:

`UI_SLOW_MO` est en millisecondes. Par exemple, `1000` = 1 seconde entre les actions, `2000` = 2 secondes, `5000` = 5 secondes.

Rapide mais visible:

```bash
UI_SLOW_MO=500 npm run ui:test:headed -- --project=desktop-chromium --workers=1
```

Tres confortable:

```bash
UI_SLOW_MO=2000 npm run ui:test:headed -- --project=desktop-chromium --workers=1
```

Tres lent:

```bash
UI_SLOW_MO=4000 npm run ui:test:headed -- --project=desktop-chromium --workers=1
```

Rapport HTML:

```bash
npm run ui:test:report
```

## Variables utiles

```bash
UI_BASE_URL=http://127.0.0.1:8000 \
UI_CANDIDATE_EMAIL=test@example.com \
UI_CANDIDATE_PASSWORD=password \
UI_ENTERPRISE_EMAIL=contact@techsolutions.com \
UI_ENTERPRISE_PASSWORD=password \
npm run ui:test
```

Les tests couvrent:

- pages publiques principales
- page d'accueil complete du hero jusqu'au footer
- verification des grandes sections visibles et absence de debordement horizontal
- capture full-page automatique si un test public echoue
- ouverture d'une vraie offre depuis `/offres`
- 4 parcours candidat A-Z
- 4 parcours entreprise A-Z
- pages candidat principales
- redirection candidat vers `/user` si `/entreprise` est ouvert par erreur
- pages entreprise principales
- dashboard entreprise avec cartes et actions branchees
- redirection entreprise vers `/entreprise` si `/user` est ouvert par erreur
