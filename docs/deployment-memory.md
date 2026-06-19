# Deployment Memory

## Contexte

- Projet deployable: `ProximaJob-livrable`
- Repo GitHub: `laricekl/ProximaJob-livrable`
- Laravel Cloud app: `ProximaJob`
- Environnement: `main`
- URL: `https://proximajob-main-idd3fn.laravel.cloud`

## Problemes reels rencontres

### 1. Variables Cloud dupliquees dans le runtime

Le `.env` runtime Cloud s'est retrouve avec deux blocs de configuration:

- un bloc MySQL interne Laravel Cloud
- un second bloc contradictoire avec `DB_CONNECTION=sqlite` et un host public

Effet:

- comportement incoherent entre runtime, commandes artisan et deploiement
- erreurs `500`
- erreurs `ProxySQL Access denied`

Commande utile pour verifier:

```bash
cloud command:run env-a20eecae-81b4-4f72-9e10-5091ea0a4f7a --cmd='nl -ba /var/www/html/.env | sed -n "1,80p"' --json
```

### 2. Seed production casse en `--no-dev`

`DatabaseSeeder` utilisait encore une factory utilisateur, ce qui finit par casser sur Cloud car `faker` n'est pas installe en prod avec `composer install --no-dev`.

Effet:

- migrations OK
- seed KO sur `UserFactory`

## Correctifs code appliques

### Pagination

- Fichier: [app/Providers/AppServiceProvider.php](/Users/laricelk/Documents/ProximaJob/ProximaJob-livrable/app/Providers/AppServiceProvider.php)
- Suppression du rendu pagination Tailwind manquant
- Bootstrap 5 conserve

### Base de donnees Cloud

- Fichier: [config/database.php](/Users/laricelk/Documents/ProximaJob/ProximaJob-livrable/config/database.php)
- Ajout d'une logique defensive pour gerer le placeholder SQLite injecte par Cloud

### Seed production

- Fichier: [database/seeders/DatabaseSeeder.php](/Users/laricelk/Documents/ProximaJob/ProximaJob-livrable/database/seeders/DatabaseSeeder.php)
- Remplacement du `User::factory()` final par un `firstOrCreate()` explicite

## Etat tests local

Commande:

```bash
php artisan test
```

Resultat valide:

```text
74 passed
```

## Commandes Cloud utiles

### Voir l'environnement

```bash
cloud environment:get env-a20eecae-81b4-4f72-9e10-5091ea0a4f7a --json --show-sensitive
```

### Voir les deployments

```bash
cloud deployment:list env-a20eecae-81b4-4f72-9e10-5091ea0a4f7a --json
```

### Suivre un deployment

```bash
cloud deploy:monitor env-a20eecae-81b4-4f72-9e10-5091ea0a4f7a
```

### Logs

```bash
cloud environment:logs ProximaJob main --tail=100 --minutes=15 --json
```

### Verifier la config DB au runtime

```bash
cloud command:run env-a20eecae-81b4-4f72-9e10-5091ea0a4f7a --cmd='php artisan tinker --execute="dump(config(\"database.default\"), env(\"DB_CONNECTION\"), env(\"DB_HOST\"), env(\"DB_DATABASE\"), config(\"cache.default\"))"' --json
```

## Deploy command a retenir

Le deploy command Cloud qui a ete prepare pour repartir proprement:

```bash
cat > /var/www/html/.env <<"EOF"
APP_NAME="ProximaJob"
APP_ENV=production
APP_DEBUG=false
APP_URL="https://proximajob-main-idd3fn.laravel.cloud"

LOG_CHANNEL=laravel-cloud-socket
LOG_STDERR_FORMATTER=Monolog\\Formatter\\JsonFormatter

DB_CONNECTION=mysql
DB_HOST=db-a20ef3fd-a8d8-478c-8c46-48485375201a.us-east-2.db.laravel.cloud
DB_PORT=3306
DB_DATABASE=main
DB_USERNAME=pnjv9venbhakipy7
DB_PASSWORD=3J7b4snTkVWR0rCVPuDq

SESSION_DRIVER=cookie
CACHE_STORE=file
SCHEDULE_CACHE_DRIVER=file

VITE_APP_NAME="${APP_NAME}"

APP_KEY=base64:pPfngkCxr0ON/0ZyH892Azxupd6Csss28pLszDwivH8=
APP_LOCALE=fr
APP_FAKER_LOCALE=fr_FR
AUTORUN_LARAVEL_MIGRATION=true
EOF
php artisan optimize:clear
php artisan migrate:fresh --seed --force
```

## Si le probleme revient

Verifier dans cet ordre:

1. `cloud deployment:list ...` pour voir si l'echec vient du build ou du deploy command
2. `cloud environment:logs ...` pour voir l'erreur exacte
3. `cloud command:run ... --cmd='nl -ba /var/www/html/.env | sed -n "1,80p"'`
4. confirmer qu'il n'y a pas de doublons `DB_*` dans `.env`
5. confirmer que `DatabaseSeeder` ne depend pas d'outils `require-dev`

## Derniers commits utiles

- `686f21e` Fix livrable pagination and frontend lockfile
- `64130fb` Fix livrable user factory faker usage
- `d520081` Fix Laravel Cloud database fallback
- `66448a2` Handle Laravel Cloud sqlite placeholder
- `3352bfe` Avoid factory in production seeding
