# Travail A Faire

## 1. Deja traite

- deploiement Laravel Cloud stabilise
- commande de deploy corrigee:
  - `php artisan optimize:clear`
  - `php artisan migrate --force`
  - `php artisan db:seed --force`
- base de demonstration rehydratee apres deploy
- entreprises, candidats et offres de demo ajoutes
- pages publiques principales verifiees sans erreur 500
- parcours candidat et entreprise verifies sur la version deployee
- pagination publique des offres corrigee
- pages auth legacy principales remplacees:
  - verification email
  - mot de passe oublie
- protection des boutons Google et Facebook quand OAuth n'est pas configure
- bug CV production corrige sur `/user/generer-cv-personnalise`
- anciennes routes CV redirigees vers le builder CV refondu pour eviter les pages cassees et les 500 latents
- anciennes routes publiques `/offres/detail` et `/app-form` branchees vers `/offres`
- parcours de verification email fiabilise:
  - page entreprise branchee sur la bonne route de renvoi
  - vue de renvoi de verification ajoutee pour eviter un 500
- profil public candidat branche sur les vraies donnees utilisateur/CV
- page detail candidature nettoyee pour afficher les vraies donnees du dossier au lieu de textes figes
- pages entreprise `abonnements`, `promotions` et `detail candidat` branchees sur de vraies donnees
- fallback local vers SQLite ajoute quand MySQL local n'est pas disponible afin d'eviter les 500 sur localhost
- reliquats de vues legacy non branchees supprimes du projet
- audit automatique des vues manquantes nettoye
- smoke test transversal ajoute sur les routes principales public, candidat, entreprise et admin
- bug corrige sur le formulaire de connexion admin qui postait vers la mauvaise route

## 2. A faire avant livraison

### Audit anciennes pages

- identifier toutes les pages anciennes du projet original encore visibles apres la refonte
- lister les routes qui pointent encore vers des vues non harmonisees
- verifier les liens secondaires et redirections qui peuvent renvoyer vers l'ancienne interface
- faire un controle route par route sur:
  - public
  - candidat
  - entreprise
  - admin

### Parcours CV candidat

- confirmer visuellement sur le site de deploy que le parcours officiel reste:
  - `/user/infos-cv`
  - `/user/personnaliser-cv`
- verifier le rendu final et les messages d'erreur du parcours CV

### Coherence UI refonte

- comparer les pages publiques, candidat et entreprise avec la nouvelle direction visuelle
- noter les pages qui cassent l'effet premium ou la coherence de la refonte
- verifier les composants encore en ancien style:
  - formulaires
  - tableaux
  - dashboards
  - pages de detail
  - messages d'etat

### Verification fonctionnelle finale

- relancer les tests Playwright publics
- relancer les tests demo
- relancer la demo live complete:
  - creation compte entreprise
  - creation offre
  - creation compte candidat
  - postulation
- verifier visuellement qu'aucune ancienne page ne ressort

## 3. A faire apres la livraison ou plus tard

### OAuth

- configurer Google OAuth
- configurer Facebook OAuth
- remettre les boutons sociaux actifs seulement quand la configuration est prete

### Nettoyage technique

- remplacer les anciennes vues encore utilisees par les vues refondues
- supprimer ou debrancher les anciennes routes/pages inutiles
- verifier les layouts partages encore herites de l'ancien projet
- centraliser ce qui peut encore l'etre dans les composants et styles communs

### Ameliorations structurelles

- simplifier l'architecture des parcours CV
- reduire les surfaces de regression entre ancien systeme et refonte
- poursuivre la centralisation des styles, couleurs et composants

## 4. Notes de contexte

- pendant la demo live, certaines pages semblaient venir du projet original avant refonte
- il faut maintenir un audit parcours par parcours jusqu'a disparition complete de ces pages
- generation CV:
  - un bug reel a ete trouve en production sur `/user/generer-cv-personnalise`
  - cause: le service attendait un tableau de competences mais recevait une chaine libre depuis le formulaire
  - correctif local ajoute: normalisation defensive des competences avant construction du prompt
  - test unitaire ajoute pour verrouiller ce cas
- constat structurel:
  - il existe encore plusieurs systemes CV en parallele, ce qui augmente le risque de confusion UI et de regressions
- decision de nettoyage en cours:
  - le builder CV refondu devient le parcours officiel
  - les routes historiques `/cv/create`, `/cv/{id}`, `/cv/{id}/edit` et `/cv-generator/form` redirigent maintenant vers le nouveau flux
  - les anciennes entrees publiques d'offre sans contexte detaille redirigent maintenant vers la liste des offres
- validation recente:
  - `php artisan test tests/Feature` passe entierement
  - controle visuel local confirme sur:
    - accueil
    - offres
    - login
    - contact
    - dashboard candidat
    - espace entreprise
    - dashboard admin
