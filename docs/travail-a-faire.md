# Travail A Faire

## Priorite immediate

- identifier toutes les pages anciennes du projet original encore visibles apres la refonte
- lister les routes qui pointent encore vers des vues non harmonisees
- verifier les liens secondaires et redirections qui peuvent renvoyer vers l'ancienne interface
- auditer tout le parcours CV candidat:
  - `/user/infos-cv`
  - `/user/personnaliser-cv`
  - `/cv/create`
  - `/cv-generator/...`
- reduire les doublons entre anciens generateurs CV et parcours refondu

## Audit UI refonte

- comparer les pages publiques, candidat et entreprise avec la nouvelle direction visuelle
- noter les pages qui cassent l'effet premium ou la coherence de la refonte
- verifier les composants encore en ancien style:
  - formulaires
  - tableaux
  - dashboards
  - pages de detail
  - messages d'etat

## Nettoyage technique

- remplacer les anciennes vues encore utilisees par les vues refondues
- supprimer ou debrancher les anciennes routes/pages inutiles
- verifier les layouts partages encore herites de l'ancien projet
- centraliser ce qui peut encore l'etre dans les composants et styles communs

## Verification apres correction

- relancer les tests Playwright publics
- relancer les tests demo
- relancer la demo live complete:
  - creation compte entreprise
  - creation offre
  - creation compte candidat
  - postulation
- verifier visuellement qu'aucune ancienne page ne ressort

## Notes

- pendant la demo live, certaines pages semblaient venir du projet original avant refonte
- il faut maintenant faire un audit route par route et parcours par parcours
- generation CV:
  - un bug reel a ete trouve en production sur `/user/generer-cv-personnalise`
  - cause: le service attendait un tableau de competences mais recevait une chaine libre depuis le formulaire
  - correctif local ajoute: normalisation defensive des competences avant construction du prompt
  - test unitaire ajoute pour verrouiller ce cas
- constat structurel:
  - il existe encore plusieurs systemes CV en parallele, ce qui augmente le risque de confusion UI et de regressions
