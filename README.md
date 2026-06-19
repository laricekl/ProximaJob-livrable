# ProximaJob

Application de recherche d'emploi et recrutement.

**Production :** https://proximajob-main-idd3fn.laravel.cloud  
**Dashboard :** https://cloud.laravel.com

---

## Déploiement Laravel Cloud — Solution qui marche

### Architecture

```
git push → Build → Deploy → App prête
                              │
                              ├── AppServiceProvider::register() crée le .sqlite
                              └── EnsureDatabaseExists middleware lance migrate
```

### 1. Base de données

**SQLite** — MySQL est bloqué par ProxySQL sur le plan gratuit (le hostname `*.public.db.laravel.cloud` n'est pas accessible depuis le réseau interne).

### 2. Variables d'environnement

```
APP_KEY=base64:...
APP_NAME=ProximaJob
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<ton-app>.laravel.cloud
APP_LOCALE=fr
APP_FAKER_LOCALE=fr_FR
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/storage/database.sqlite
DB_HOST=
```

**Critique :** `DB_HOST=` doit être vide pour éviter que le fallback MySQL ne prenne le dessus.  
**Critique :** `DB_DATABASE` doit être un chemin absolu vers `storage/`.

### 3. Commandes build/deploy

```
Build : composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts
        npm ci --audit false
        npm run build

Deploy: echo 'deployed'
```

Le deploy command est volontairement minimal. Les migrations sont gérées au runtime par le middleware `EnsureDatabaseExists`.

### 4. Push-to-deploy

Fiable à 100%. Chaque `git push origin main` déclenche build → deploy → app fonctionnelle.

### 5. Fichiers clés

| Fichier | Rôle |
|---------|------|
| `config/database.php` | `default=sqlite`, fallback MySQL désactivé |
| `app/Providers/AppServiceProvider.php` | `register()` crée le fichier SQLite s'il n'existe pas |
| `app/Http/Middleware/EnsureDatabaseExists.php` | Lance `migrate --force` au 1er accès si la BDD est vide |
| `bootstrap/app.php` | Enregistre le middleware dans le groupe `web` |

### Problèmes connus

| Problème | Cause | Solution |
|----------|-------|----------|
| MySQL inaccessible | ProxySQL bloque le hostname public | Utiliser SQLite |
| `DB_CONNECTION=mysql` auto-injecté | Laravel Cloud injecte les vars de la BDD attachée | Overrider avec `DB_CONNECTION=sqlite` |
| BDD perdue au redéploiement | Container éphémère | Middleware recrée au 1er accès |
| `route:cache` échoue | Routes en double | Corrigé (`admin.login` → `admin.login.post`) |
| `database-cluster:create` échoue | Bug régions (CLI v0.5.0) | Créer via l'interface web |

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
