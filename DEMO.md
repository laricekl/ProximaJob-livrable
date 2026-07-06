# Plan de démonstration ProximaJob

## Avant de commencer

```bash
php artisan demo:seed                    # Remplir les données
npm run build                            # Build CSS (une fois)
php artisan serve --host=127.0.0.1       # Lancer le serveur
```

---

## Partie 1 — Zone publique (~3 min)

### 1.1 Page d'accueil
- `http://127.0.0.1:8000`
- **Montrer :** Hero "Votre recherche d'emploi propulsée par l'IA"
- **Montrer :** Barre de recherche d'offres
- **Montrer :** Catégories d'emploi
- **Montrer :** Dernières offres listées
- **Montrer :** Section Forfaits en bas
- **Montrer :** Changement de langue FR/EN

### 1.2 Liste des offres
- Cliquer "Offres" dans la nav
- **Montrer :** La liste avec les 5 offres créées
- **Montrer :** Filtres de recherche (titre, localisation, type)

### 1.3 Détail d'une offre
- Cliquer sur "Développeur Full Stack Laravel/React"
- **Montrer :** Description riche, responsabilités
- **Montrer :** Bouton "Se connecter pour postuler" (visiteur non connecté)

### 1.4 Pages publiques
- `/contact` — Formulaire de contact
- `/abonnement` — Comparaison des 3 plans

---

## Partie 2 — Parcours candidat (~5 min)

### 2.1 Connexion
```
Email : test@example.com
Mot de passe : password
```

### 2.2 Dashboard
- `http://127.0.0.1:8000/user`
- **Montrer :** Les 4 cartes de stats (candidatures envoyées, entretiens, CV IA, notifs)
- **Montrer :** Offres recommandées
- **Montrer :** Activité récente (candidatures envoyées avec statuts)

### 2.3 Historique des candidatures
- Cliquer "Candidatures" dans la nav
- **Montrer :** Tableau avec 5 candidatures et statuts variés
- **Montrer :** Filtre par statut → "Accepté" → Filtrer
- **Montrer :** Filtre par mot-clé → Réinitialiser
- Cliquer l'icône œil sur une candidature acceptée
- **Montrer :** Page détail → timeline, badge "Acceptée", CV, lettre, lien vers l'offre

### 2.4 Mon CV (point fort de la démo)
- Cliquer "Mon CV" dans la nav
- **Montrer :** Le CV principal déjà rempli avec toutes les sections
- **Montrer :** La preview PDF à droite (iframe)
- **Montrer :** La sidebar avec toutes les sections :
  - Informations personnelles
  - Compétences (8 skills)
  - Expériences (3 postes)
  - Formations (2 diplômes)
  - Langues (3 langues)
  - Perfectionnements (2 certifications)
  - Bénévolat
- Cliquer "Modifier les informations" → **Montrer le wizard**
- **Montrer :** Navigation entre les sections
- **Montrer :** Ajout dynamique d'une compétence
- Fermer sans sauvegarder

### 2.5 Personnalisation CV (IA)
- `http://127.0.0.1:8000/user/personnaliser-cv`
- **Montrer :** Formulaire avec offre pré-sélectionnée
- **Montrer :** Options de style (template, couleur, police, densité)
- **Expliquer :** L'IA génère un CV adapté à l'offre

### 2.6 Abonnement
- Cliquer "Mon abonnement" dans le menu dropdown
- **Montrer :** Abonnement Pro actif
- `/user/plan-abonnement` → **Montrer :** Les 3 plans avec toggle Mensuel/Annuel

### 2.7 Profil public
- Cliquer "Mon profil candidat" dans le dropdown
- **Montrer :** Photo, compétences, expérience, CV
- Cliquer "Prévisualiser comme employeur"
- **Montrer :** Ce que voit le recruteur

### 2.8 Notifications
- Cliquer l'icône cloche dans la nav ou `/notifications`
- **Montrer :** 5 notifications avec différents types
- **Montrer :** "Tout marquer comme lu"

---

## Partie 3 — Parcours entreprise (~3 min)

### 3.1 Connexion
```
Email : contact@techsolutions.com
Mot de passe : password
```

### 3.2 Dashboard entreprise
- **Montrer :** Stats de l'entreprise
- **Montrer :** Liste des 5 offres publiées

### 3.3 Gestion des candidatures
- Cliquer sur une offre → "Voir les candidatures"
- **Montrer :** Liste des candidats
- **Montrer :** Actions : Accepter / Rejeter
- **Montrer :** Preview du CV candidat

### 3.4 Création d'offre
- `http://127.0.0.1:8000/entreprise/offres/create`
- **Montrer :** Le formulaire riche
- **Expliquer :** Une fois publiée, le matching IA trouve les candidats

---

## Résumé : points clés à mettre en avant

| Fonctionnalité | Impact |
|----------------|--------|
| Dashboard avec stats | Vue à 360° |
| CV Builder 8 sections | Remplissage complet |
| Matching IA | Candidatures automatiques |
| Personnalisation CV par offre | IA générative |
| Système de candidature | Pipeline complet |
| Abonnement | Modèle économique |
| Notifications | Engagement utilisateur |
| Bilingue FR/EN | Marché québécois |
