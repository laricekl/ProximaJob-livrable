# CLAUDE.md — ProximaJob

## Résumé

ProximaJob est une plateforme Laravel 12 de matching emploi-candidat avec IA. Elle met en relation chercheurs d'emploi et employeurs via un système intelligent de matching automatique qui génère des CV et lettres de motivation personnalisés avec Google Gemini (via Prism PHP).

**Stack :** Laravel 12, MySQL/SQLite, Tailwind CSS, DOMPDF, Spatie Permissions, Prism (Gemini 2.5 Flash), Laravel Socialite (Google/Facebook OAuth)

**Production :** https://proximajob-main-idd3fn.laravel.cloud  
**PHP :** >= 8.2  
**Base de données :** MySQL en prod, SQLite en fallback/dev

---

## Structure du projet

```
ProximaJob-livrable/
├── app/
│   ├── Console/Commands/     # Commandes artisan
│   │   └── TestGeminiConnection.php
│   ├── Http/
│   │   ├── Controllers/      # 25+ contrôleurs
│   │   │   ├── Auth/         # Login, register, OAuth, email verification
│   │   │   ├── Admin/        # Site settings admin
│   │   │   ├── AdminController.php (1865 lignes)
│   │   │   ├── OffresController.php (1032 lignes)
│   │   │   ├── CandidatureController.php (665 lignes)
│   │   │   ├── UserController.php (684 lignes)
│   │   │   ├── CvPersonalizationController.php
│   │   │   └── EntreprisesController.php
│   │   └── Middleware/
│   │       ├── CheckUserStatus.php      # Bloque comptes inactifs/suspendus
│   │       ├── EnsureCandidateAccess.php # Redirige non-candidats
│   │       ├── EnsureEntrepriseAccess.php
│   │       └── EnsureDatabaseExists.php  # Auto-crée SQLite + migrate
│   ├── Models/               # 28 modèles Eloquent
│   │   ├── User.php          # 392 lignes — central
│   │   ├── Offre.php         # 204 lignes — offres d'emploi
│   │   ├── Postulation.php   # Candidatures
│   │   ├── CvProfile.php     # Profil CV structuré
│   │   ├── Sector.php / Skill.php  # Taxonomie SCIAN
│   │   └── Abonnement.php    # Plans d'abonnement
│   ├── Services/
│   │   └── JobMatchingService.php  # 1668 lignes — cœur IA
│   └── Providers/
├── config/
│   ├── prism.php             # Providers IA (Gemini, OpenAI, etc.)
│   └── dompdf.php            # Configuration PDF
├── database/migrations/      # 47 fichiers de migration
├── routes/
│   ├── web.php               # 647 lignes — routes principales
│   ├── api.php               # API Sanctum
│   ├── auth.php              # Routes Breeze
│   └── console.php           # Scheduler + commandes
├── resources/views/          # Vues Blade
├── lang/                     # fr.json + en.json
└── tests/                    # Tests PHPUnit + Playwright
```

---

## Modèles clés et leurs relations

### User (central)
- `hasOne(CvProfile)`, `hasOne(Entreprise)`, `hasOne(CandidateSector)`
- `hasMany(Postulation)`, `hasMany(Notification)`, `hasMany(CandidateSkill)`
- `belongsToMany(Offre via postulations)`, `belongsToMany(Abonnement via user_abonnements)`
- Rôles (Spatie) : `candidat`, `entreprise`, `admin`, `Marketing`
- Attributs notables : `status` (active/suspended), `is_active`, `salary_expectation_min`

### Offre
- `belongsTo(Entreprise)`, `belongsTo(Categorie)`, `belongsTo(Sector)`, `belongsTo(TypeOffre)`
- `hasMany(Postulation)`, `hasMany(JobOfferSkill)`, `belongsToMany(Diplome via offre_diplome)`
- Slug automatique, statut (active/inactive), filtres avancés (remote_work, salary_type, etc.)

### Postulation (candidature)
- `belongsTo(User)`, `belongsTo(Offre)`, `hasMany(AutresDoc)`
- `autopostulation` : true = suggérée par IA, false = manuelle
- Stocke CV, lettre de motivation, `match_score`, `match_details`

### CvProfile (profil CV structuré)
- Pièce maîtresse du système de CV IA
- Relations : `hasMany(CvFormation)`, `hasMany(CvCompetence)`, `hasMany(CvExperience)`, `hasMany(CvLangue)`, `hasMany(CvPerfectionnement)`, `hasMany(CvBenevolat)`, `hasMany(CvGenere)`

---

## Système IA (Gemini via Prism)

**Fichier central :** `app/Services/JobMatchingService.php` (1668 lignes)

### Matching automatique (`processAutoMatching()`)
1. Parcourt toutes les offres actives
2. Trouve les candidats éligibles (`findEligibleCandidates`)
3. Calcule un score (`calculateMatchScore`) :
   - Compétences techniques : 40%
   - Compétences générales : 40%
   - Expérience : 20%
4. Crée des candidatures automatiques si seuil dépassé

### Génération IA via Gemini
- `generateCVWithGemini($promptData)` → HTML
- `generateCoverLetter()` → HTML
- Conversion HTML → PDF via DOMPDF
- Fallback Blade si Gemini échoue

### Déclencheurs du matching
- Mise à jour du profil candidat (changements significatifs)
- Création/mise à jour du CV profil
- Création d'offre
- Scheduler : toutes les heures + quotidien 8h

---

## Routes principales

| Groupe | Préfixe | Middleware | Rôle |
|--------|---------|------------|------|
| Guest | `/` | — | Welcome, offres, contact |
| Candidat | `/user` | auth+verified+candidate+status | Dashboard, CV, candidatures |
| Entreprise | `/entreprise` | auth+verified+entreprise+status | Gestion offres, candidatures |
| Admin | `/admin` | auth+verified+role:admin | Users, stats, abonnements |
| Auth | `/` | — | Login, register, OAuth, password reset |

### Commandes artisan utiles
```bash
php artisan jobs:match        # Lance le matching (queued)
php artisan jobs:match-now    # Matching synchrone immédiat
php artisan gemini:test       # Test connexion Gemini
php artisan schedule:work     # Lance le scheduler
```

---

## Variables d'environnement critiques

```env
DB_CONNECTION=mysql           # ou sqlite
DB_DATABASE=laravel
GEMINI_API_KEY=               # Obligatoire pour l'IA
APP_LOCALE=fr
GOOGLE_CLIENT_ID=             # OAuth Google
GOOGLE_CLIENT_SECRET=
FACEBOOK_CLIENT_ID=           # OAuth Facebook
```

---

## Patterns de code

- **Langue :** Tout en français (vues, messages, emails). Support bilingue fr/en via `SetLocale` middleware.
- **Authentification :** Laravel Breeze + Socialite (Google/Facebook) + Spatie Permissions (4 rôles)
- **Matching IA :** Pattern fire-and-forget (les échecs ne bloquent pas l'action principale)
- **CV :** Double système — CV uploadé (PDF/DOC) + CV généré par IA (PDF via DOMPDF)
- **Abonnements :** Plans avec fonctionnalités (Abonnement → AbonnementFonctionnalite), souscription utilisateur avec dates début/fin

---

## Déploiement

- **Production :** Laravel Cloud (push-to-deploy sur `git push origin main`)
- **DB Prod :** SQLite (contourne les limitations MySQL du plan gratuit)
- **Middleware `EnsureDatabaseExists` :** Recrée la BDD et lance les migrations au 1er accès si nécessaire
- **Commandes build :** `composer install --no-dev` + `npm ci && npm run build`
