# RESTE À FAIRE — ProximaJob

Dernière mise à jour : 2026-07-02 (après correction des bugs candidats)

---

## ✅ Bugs corrigés (session 2026-07-02)

### Critiques (7)
- ✅ Colonne `match_details` inexistante → migration ajoutée
- ✅ `match_score`, `application_date`, `algorithm_version` absents de `$fillable` → ajoutés dans Postulation
- ✅ `CvProfileController::update()` vide → validation + updateRelations appelé
- ✅ `catch (Exception $e)` sans import → `\Exception`
- ✅ `services.blade.php` statique → vue orpheline, non référencée
- ✅ `CheckUserStatus::$user->role` → `hasRole('candidat')`
- ✅ Paramètres inversés route candidatures → `$user_id, $offre_id`

### Majeurs (8)
- ✅ `MIN_MATCH_SCORE = 0` → 50
- ✅ `processAutoMatching()` ignore userId → paramètre `$candidateId` propagé
- ✅ `CvProfileController::updateRelations()` jamais appelée → appelée dans `update()`
- ✅ `Auth::user()->entreprise` null → guard ajouté
- ✅ `updateOrCreate` écrase candidature → vérification statut existant
- ✅ `createFormations()` stocke ID au lieu du nom → résolution Diplome
- ✅ `abonnement.blade.php` sans données → `UserController::abonnement()` enrichi
- ✅ `match_score` jamais calculé pour manuel → `application_date` + `autopostulation` ajoutés

### Modérés (8)
- ✅ Double extension `.pdf.pdf` → `str_ends_with()`
- ✅ Redirection `profile.edit` → `infos.cv`
- ✅ `salary_expectation` écrase `salary_expectation_min` → sauvegarde unique
- ✅ Route `user.bonnement` → `user.abonnement`
- ✅ Log "CV" au lieu de "lettre de motivation" → corrigé
- ✅ `groupBy` dans `whereHas` → sous-requête `whereIn`
- ✅ Routes dupliquées `/historique-candidature` → redirects rétrocompatibles
- ✅ `$offre->entreprise->user` null → `?->` + guard

---

## Phase 3 — Qualité de code (restant)

### 3.4 Découper AdminController (1865 lignes)

**Fichier :** `app/Http/Controllers/AdminController.php`

Créer 4 contrôleurs spécialisés :

| Nouveau contrôleur | Méthodes |
|---|---|
| `app/Http/Controllers/Admin/DashboardController.php` | `index()`, `getChartData()`, stats, graphs |
| `app/Http/Controllers/Admin/UserManagementController.php` | `users()`, `edit()`, `update()`, `store()`, `show()`, `deleteUser()`, `suspendUser()`, `reactivateUser()`, `activate()`, `updateStatus()`, `updateEntrepriseStatus()` |
| `app/Http/Controllers/Admin/AbonnementController.php` | `abonnements()`, `showAbonnement()`, `abonstore()`, `abonupdate()`, `abondestroy()`, `getFonctionnalites()`, `export()` |
| `app/Http/Controllers/Admin/SettingsController.php` | `parametres()`, `updateGeneral()`, `removeLogo()`, `removeFavicon()`, `newsletters()` |

**Effort estimé :** ~4h

---

### 3.6 Déplacer les transactions DB vers des services

**Fichiers à créer :**
- `app/Services/UserManagementService.php`
- `app/Services/AbonnementService.php`
- `app/Services/CandidatureService.php`
- `app/Services/CvProfileService.php`

**Effort estimé :** ~4h

---

## Phase 4 — Frontend

### 4.1 Découper les 3 vues monolithiques

| Vue | Lignes | Extraire en composants |
|---|---|---|
| `resources/views/layouts/connected_app.blade.php` | 1406 | `_head`, `_sidebar`, `_navbar`, `_footer`, `_mobile-menu`, `_notification-modal` |
| `resources/views/components/application-modal.blade.php` | 1376 | `_job-info`, `_cv-section`, `_cover-letter-section`, `_submission-footer` |
| `resources/views/welcome.blade.php` | 1232 | `_hero`, `_how-it-works`, `_job-counter`, `_features`, `_pricing`, `_cta` |

**Effort estimé :** ~4h

---

## Phase 6 — Performance

### 6.1 Ajouter une couche de cache

- Cacher `Sector::get()`, `Diplome::get()`, `Skill::get()` dans `ViewComposerServiceProvider`
- Invalider le cache dans les méthodes admin

**Effort estimé :** ~2h

---

## Bonus

| # | Tâche | Effort |
|---|-------|--------|
| 1 | Index fulltext `offres.poste` + `offres.description` | ~1h |
| 2 | Nettoyer doublon `cover_letter` / `lettre_motivation` dans `postulations` | ~1h |
| 3 | Tests unitaires `JobMatchingService` (scoring, génération CV) | ~3h |
| 4 | Policies Laravel (`OfferPolicy`, `UserPolicy`) | ~3h |
| 5 | Route `/candidatures/{user_id}/{offre_id}/{filename}` — ajouter middleware `auth` | ~15min |
| 6 | Vue `services.blade.php` — supprimer ou dynamiser (actuellement orpheline et statique) | ~30min |
| 7 | Limiter le nombre de candidatures par jour (utiliser `MAX_APPLICATIONS_PER_DAY = 3`) | ~1h |

**Total restant :** ~24h
