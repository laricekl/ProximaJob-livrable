# ProximaJob

Application de recherche d'emploi et recrutement.

**Production :** https://proximajob-main-idd3fn.laravel.cloud  
**Dashboard :** https://cloud.laravel.com

---

## Déploiement de production

La production doit utiliser une base persistante, une file asynchrone et un stockage persistant. Une base SQLite placée dans le conteneur ne convient pas : les données seraient perdues lors d’un remplacement du conteneur et les écritures concurrentes seraient limitées.

### 1. Préparer l’environnement

Copier les valeurs de `.env.production.example` dans le gestionnaire de secrets de l’hébergeur. Ne jamais commiter le véritable `.env`.

Exigences principales :

- `APP_ENV=production`, `APP_DEBUG=false` et une URL HTTPS ;
- PostgreSQL ou MySQL persistant ;
- `QUEUE_CONNECTION=database` ou `redis`, avec un worker permanent ;
- sessions et cache sur `database` ou `redis` ;
- cookies HTTPS et sessions chiffrées ;
- SMTP ou fournisseur transactionnel réellement configuré ;
- volume persistant pour `storage/app/public` si les conteneurs sont éphémères.

### 2. Déployer

Le script `scripts/deploy-production.sh` installe les dépendances, compile les ressources, applique les migrations, crée le lien de stockage, construit les caches Laravel et redémarre les workers.

```bash
chmod +x scripts/deploy-production.sh
./scripts/deploy-production.sh
```

Les migrations ne sont jamais exécutées depuis une requête web en production.

### 3. Processus permanents

Configurer ces deux processus chez l’hébergeur :

```bash
php artisan queue:work --sleep=2 --tries=3 --timeout=120 --max-time=3600
php artisan schedule:work
```

Le worker de queue doit être relancé automatiquement lorsqu’il termine. Une seule instance du planificateur doit être active.

### 4. Vérification obligatoire

```bash
php artisan production:check --strict
```

Le déploiement doit être arrêté si cette commande retourne un échec. La route `/health` vérifie ensuite la base et le cache pendant l’exploitation.

---

## Développement local

```bash
git clone git@github.com:laricekl/ProximaJob-livrable.git
cd ProximaJob-livrable

composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run dev

php artisan serve
```

### Identifiants de test locaux

Apres `php artisan migrate --seed`, les comptes suivants peuvent servir aux tests manuels:

| Role | Email | Mot de passe | Usage |
|------|-------|--------------|-------|
| Candidat | `test@example.com` | `password` | Parcours utilisateur candidat |
| Admin | `admin@example.com` | `password` | Acces administration local |
| Marketing | `marketing@example.com` | `password` | Verification du role marketing |

Les donnees de demonstration ajoutent aussi des entreprises et des offres afin que les pages publiques, candidat et entreprise affichent du contenu reel.
