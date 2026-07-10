# 🧪 Plan de Test — Audit ProximaJob (10 Juillet 2026)

## Prérequis

```bash
# 1. S'assurer que la BDD est à jour
php artisan migrate:fresh --seed

# 2. Créer un admin de test si pas déjà fait
php artisan tinker --execute="
  \$u = App\Models\User::create(['name'=>'Admin Test','email'=>'admin@test.com','password'=>bcrypt('password'),'is_active'=>true]);
  \$u->assignRole('admin');
"

# 3. Lancer le serveur
php artisan serve --port=8765
```

---

## 1. Étape 1 — Ménage (vérifier que rien n'est cassé)

### 1.1 Filtres offres (A1 + A2)

| # | Test | Action | Résultat attendu |
|---|------|--------|------------------|
| 1.1a | Filtre remote_work | Aller sur `/offres`, cocher "Présentiel", cliquer "Appliquer" | Les offres avec `remote_work = Présentiel` apparaissent |
| 1.1b | Filtre remote_work | Cocher "Hybride" + "Télétravail" | Résultats combinés |
| 1.1c | Filtre expérience | Cocher "Junior", appliquer | Offres avec `experience` contenant "0-1", "1 an", "junior", "débutant"... |
| 1.1d | Filtre expérience | Cocher "Senior", appliquer | Offres avec "5 ans", "senior", "expert", "confirmé"... |
| 1.1e | Filtre expérience | Cocher "Intermédiaire" | Offres avec "2-3", "3 an", "4-5", "intermédiaire"... |

### 1.2 Fichiers supprimés (A5, A6, B1, C1)

| # | Test | Action | Résultat attendu |
|---|------|--------|------------------|
| 1.2a | Vérification fichiers | `ls resources/views/layouts/dashboard.blade.php` | Fichier inexistant ✅ |
| 1.2b | | `ls resources/views/profile/show.blade.php` | Fichier inexistant ✅ |
| 1.2c | | `ls app/Http/Controllers/Auth/ForgotPasswordController.php` | Fichier inexistant ✅ |
| 1.2d | | `ls app/Http/Controllers/Auth/ResetPasswordController.php` | Fichier inexistant ✅ |
| 1.2e | | `ls resources/views/layouts/app.blade.php` | Fichier inexistant ✅ |
| 1.2f | | `ls resources/views/layouts/connected_app.blade.php` | Fichier inexistant ✅ |
| 1.2g | | `ls resources/views/layouts/entreprise_app.blade.php` | Fichier inexistant ✅ |
| 1.2h | | `ls resources/views/auth/passwords/` | Dossier inexistant ✅ |
| 1.2i | Pas d'erreur fatale | Accéder à `/login`, `/register`, `/forgot-password` | Pages d'auth Breeze normales |
| 1.2j | Mot de passe oublié | Aller sur `/forgot-password`, entrer un email | Le mail de reset utilise le bon template (emails/password-reset.blade.php) |

---

## 2. Phase A — Admin CRUDs

Se connecter en admin : `admin@test.com` / `password`

### 2.1 Sidebar Admin

| # | Test | Résultat attendu |
|---|------|------------------|
| 2.1a | Section "Référentiel" visible dans la sidebar | 5 liens : Catégories, Types d'offres, Secteurs, Compétences, Diplômes |
| 2.1b | Lien actif surligné | Chaque page a son lien en `bg-white/10 text-white font-semibold` |
| 2.1c | Icônes Material Symbols | Chaque lien a une icône distincte |

### 2.2 Catégories CRUD

| # | Test | Résultat attendu |
|---|------|------------------|
| 2.2a | `/admin/categories` | Tableau avec colonnes : Catégorie, Offres, Créée le, Actions |
| 2.2b | Créer | Entrer "Santé" → cliquer "Créer" → apparaît dans le tableau avec 0 offres |
| 2.2c | Créer doublon | Entrer "Santé" → message d'erreur "Cette catégorie existe déjà" |
| 2.2d | Créer vide | Cliquer "Créer" sans texte → erreur de validation |
| 2.2e | Éditer | Cliquer ✏️ sur "Santé" → renommer en "Santé & Bien-être" → ✓ → ligne mise à jour |
| 2.2f | Éditer annuler | Cliquer ✏️ → modifier → cliquer ✕ → retour au nom original |
| 2.2g | Supprimer sans offres | 🗑️ sur une catégorie sans offres → confirmation → supprimée |
| 2.2h | Supprimer avec offres | 🗑️ sur une catégorie liée à des offres → message "Impossible... X offre(s) associée(s)" |
| 2.2i | Recherche | Taper un nom partiel → filtre le tableau |
| 2.2j | Pagination | Si >20 catégories → pagination affichée |

### 2.3 Types d'offres CRUD

| # | Test | Résultat attendu |
|---|------|------------------|
| 2.3a | `/admin/types-offres` | Tableau fonctionnel |
| 2.3b | Créer "CDI" | Apparaît dans le tableau |
| 2.3c | Éditer "CDI" → "CDI Temps plein" | Mise à jour |
| 2.3d | Supprimer type lié à des offres | Refusé avec message |

### 2.4 Secteurs CRUD

| # | Test | Résultat attendu |
|---|------|------------------|
| 2.4a | `/admin/secteurs` | Tableau avec colonnes : Secteur, Code SCIAN, Offres, Actions |
| 2.4b | Créer secteur racine | Nom: "TI", Code: "5415", Parent: aucun → créé |
| 2.4c | Créer sous-secteur | Nom: "Développement", Parent: "TI" → créé |
| 2.4d | Éditer un secteur | Modifier le code SCIAN → mis à jour |
| 2.4e | Supprimer secteur avec enfants | Refusé "a des sous-secteurs" |

### 2.5 Compétences (Skills) CRUD

| # | Test | Résultat attendu |
|---|------|------------------|
| 2.5a | `/admin/skills` | Colonnes : Compétence, Catégorie, Importance (étoiles), Actions |
| 2.5b | Créer | Nom: "PHP", Catégorie: Technique, Importance: 4 → 4 étoiles affichées |
| 2.5c | Créer avec catégorie | "Leadership", Transversale, Importance 5 → badge "Transversale" + 5⭐ |
| 2.5d | Éditer inline | Modifier catégorie + importance → mise à jour immédiate |
| 2.5e | Supprimer | Confirmation → supprimée |
| 2.5f | Recherche | "PHP" → filtre, "technique" → filtre par catégorie |

### 2.6 Diplômes CRUD

| # | Test | Résultat attendu |
|---|------|------------------|
| 2.6a | `/admin/diplomes` | Colonnes : Diplôme, Sigle, Niveau, Durée, Actions |
| 2.6b | Créer | "Baccalauréat en informatique", "B.Sc.", Universitaire 1er cycle, 3 ans → créé |
| 2.6c | Filtre par niveau | Sélectionner "Collégial" → ne montre que les diplômes de ce niveau |
| 2.6d | Éditer inline | Modifier sigle + niveau → mis à jour |
| 2.6e | Supprimer diplôme lié à des offres | Refusé avec message |

---

## 3. Phase B — Bugs corrigés

### 3.1 Abonnements dynamiques (A3)

| # | Test | Résultat attendu |
|---|------|------------------|
| 3.1a | `/abonnement` (page publique) | Affiche les vrais plans depuis la BDD (pas des prix en $) |
| 3.1b | Prix en € | Les prix sont en euros (€), pas en dollars ($) |
| 3.1c | Fonctionnalités dynamiques | Les features listées viennent de `abonnement_fonctionnalites` |
| 3.1d | Badge "Recommandé" | Sur le plan avec `populaire = true` |
| 3.1e | Plan gratuit | Si un plan à 0€ existe → bouton "Commencer" au lieu de prix |
| 3.1f | `/user/plan` (connecté candidat) | Plans dynamiques en € |
| 3.1g | Page `/` (welcome) | La section abonnements est dynamique |

### 3.2 Souscription dynamique (A4)

| # | Test | Résultat attendu |
|---|------|------------------|
| 3.2a | Créer un compte candidat | S'inscrire via `/register` |
| 3.2b | Aller sur `/user/plan` | Voir les plans de la BDD |
| 3.2c | Cliquer "Choisir [Plan]" | Redirigé vers `/user/abonnement` avec succès |
| 3.2d | Vérifier en BDD | `user_abonnements` a une entrée avec le bon `abonnement_id` |
| 3.2e | Réessayer de souscrire | Message "Vous avez déjà un abonnement actif" |
| 3.2f | Plan invalide | `POST /user/plan-souscrire` avec `abonnement_id: 999` → erreur 422 |

### 3.3 Catégories offres (A7)

| # | Test | Résultat attendu |
|---|------|------------------|
| 3.3a | Créer un compte entreprise | S'inscrire, créer une offre avec catégorie "Marketing" |
| 3.3b | Vérifier en BDD | `categories` a une entrée "Marketing", l'offre a le bon `categorie_id` |
| 3.3c | Créer une offre avec catégorie "Finance" | L'offre est dans la catégorie Finance (pas Technologie) |
| 3.3d | Admin : `/admin/categories` | Les catégories créées via les offres apparaissent |

### 3.4 Validation job_category (B8)

| # | Test | Résultat attendu |
|---|------|------------------|
| 3.4a | Créer une offre avec `job_category: "Nouveau Domaine"` | Accepté (plus de liste fixe) |
| 3.4b | Créer une offre sans `job_category` | Rejeté (champ requis) |
| 3.4c | Créer une offre avec `job_category` > 255 caractères | Rejeté (max:255) |

---

## 4. Tests de régression

### 4.1 Authentification

| # | Test | Résultat attendu |
|---|------|------------------|
| 4.1a | `/login` → connexion candidat | Redirigé vers dashboard candidat |
| 4.1b | `/login` → connexion entreprise | Redirigé vers dashboard entreprise |
| 4.1c | `/login` → connexion admin | Redirigé vers `/admin` |
| 4.1d | `/register` → inscription | Email de vérification envoyé |
| 4.1e | `/forgot-password` → reset | Email reçu avec lien |
| 4.1f | OAuth Google | Redirigé vers Google (si configuré) |

### 4.2 Pages publiques

| # | Test | Résultat attendu |
|---|------|------------------|
| 4.2a | `/` | Page d'accueil charge sans erreur |
| 4.2b | `/offres` | Liste des offres avec filtres |
| 4.2c | `/offres/{slug}` | Détail d'une offre |
| 4.2d | `/contact` | Page contact |
| 4.2e | `/abonnement` | Plans dynamiques |

### 4.3 Pages candidat

| # | Test | Résultat attendu |
|---|------|------------------|
| 4.3a | `/user` | Dashboard candidat |
| 4.3b | `/user/historiques` | Historique candidatures |
| 4.3c | `/user/historiques-ia` | Candidatures IA |
| 4.3d | `/user/cv` | Gestion CV |
| 4.3e | `/user/plan` | Plans dynamiques |

---

## 5. Résumé des tests

| Groupe | Tests | Criticité |
|--------|-------|-----------|
| 1. Filtres offres (A1+A2) | 5 | 🔴 Critique |
| 2. Fichiers supprimés | 10 | 🟡 Vérification |
| 3. Admin CRUDs | 25 | 🔴 Nouvelles features |
| 4. Abonnements (A3+A4) | 9 | 🔴 Monétisation |
| 5. Catégories (A7+B8) | 7 | 🔴 Critique |
| 6. Régression | 16 | 🟡 Sécurité |
| **Total** | **72** | — |

### Ordre recommandé

1. **Régression d'abord** (10 min) — vérifier que rien n'est cassé
2. **Admin CRUDs** (20 min) — créer des données de test
3. **Bugs corrigés** (20 min) — vérifier les corrections avec les données créées
4. **Filtres** (5 min) — valider A1+A2
