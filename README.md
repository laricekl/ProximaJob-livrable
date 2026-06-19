# ProximaJob

Application de recherche d'emploi et recrutement.

## Déploiement Laravel Cloud

### Ce qui marche (juin 2026)

Après plusieurs tentatives, voici la configuration qui fonctionne sur Laravel Cloud :

### 1. Création de l'application

```bash
# Installer la CLI Laravel Cloud
composer global require laravel/cloud-cli

# Se connecter
cloud login

# Créer l'app (la BDD échoue sur le plan gratuit, on la crée séparément)
cloud ship --name="ProximaJob"
```

### 2. Base de données

**⚠️ MySQL ne fonctionne PAS** — le hostname public `*.public.db.laravel.cloud` est bloqué par ProxySQL depuis le réseau interne.  
**✅ SQLite fonctionne** — c'est la solution utilisée.

Configuration dans `config/database.php` :
```php
'default' => env('DB_CONNECTION', 'sqlite'),
```

### 3. Variables d'environnement critiques

Définir ces variables dans l'environnement Laravel Cloud (elles sont **essentielles** au bon fonctionnement) :

```
APP_KEY=base64:...
APP_NAME=ProximaJob
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<ton-app>.laravel.cloud
APP_LOCALE=fr
APP_FAKER_LOCALE=fr_FR
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite
DB_HOST=
```

**Important :** `DB_HOST` doit être vide pour que SQLite fonctionne sans tenter MySQL.  
**Important :** `DB_DATABASE` doit être le chemin absolu `/var/www/html/database/database.sqlite`.

### 4. Commandes de build/deploy

```
Build : composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
        npm ci --audit false
        npm run build

Deploy: # php artisan migrate --force (commenté au début)
```

Une fois déployé, lancer les migrations manuellement :
```bash
cloud cmd:run <env-id> --cmd="touch /var/www/html/database/database.sqlite && php artisan migrate --force"
```

### 5. Push-to-deploy

Activé par défaut. Chaque `git push origin main` déclenche un déploiement.

### Problèmes connus

| Problème | Cause | Solution |
|----------|-------|----------|
| MySQL inaccessible | ProxySQL bloque le hostname public | Utiliser SQLite ou contacter le support |
| `DB_CONNECTION=mysql` auto-injecté | Laravel Cloud injecte les variables de la BDD attachée | Overrider avec `DB_CONNECTION=sqlite` |
| `replace` des variables ne fonctionne pas | Bug CLI v0.5.0 | Utiliser `--action=set` par variable |
| `database-cluster:create` échoue | Bug régions CLI v0.5.0 | Créer la BDD via l'interface web |

### URLs

- **Production :** https://proximajob-main-idd3fn.laravel.cloud
- **Dashboard :** https://cloud.laravel.com

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
