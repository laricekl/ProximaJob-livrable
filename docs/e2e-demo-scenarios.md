# Scenarios Demo et E2E de Confiance

Ce document sert de base de validation pour les gros parcours `public`, `candidat` et `entreprise`.

Objectif:
- montrer le produit a l'equipe avec des parcours lisibles
- prouver les flux critiques de bout en bout
- separer ce qui existe deja dans le produit de ce qui devra etre ajoute plus tard

## Regles de lecture

- `Demo` = parcours lent, visible, presentable a l'equipe
- `E2E confiance` = parcours strict, automatise, oriente verification metier
- `Disponible` = la fonctionnalite existe deja dans le projet
- `A construire` = la fonctionnalite n'existe pas encore ou n'est pas assez explicite dans l'interface actuelle

## Etat actuel du produit

### Disponible aujourd'hui

- navigation publique
- changement de langue FR / EN
- inscription candidat
- inscription entreprise
- verification email
- connexion candidat
- connexion entreprise
- creation d'offre entreprise
- consultation d'offres
- candidature manuelle avec CV et lettre
- historique candidat manuel
- historique candidat IA
- espace entreprise candidatures
- acceptation / refus d'une candidature
- notifications candidat / entreprise
- profil CV candidat
- personnalisation / generation de CV
- auto-matching / auto-postulation IA
- consultation cote entreprise des candidatures IA

### Pas clairement disponible aujourd'hui

- planification d'entretien avec date, heure, lieu et confirmation dans un vrai workflow dedie
- module explicite de rendez-vous type "scheduler rencontre"
- statut metier dedie `entretien programme`
- acceptance avec details de rencontre dans la notification

Conclusion honnete:
on peut couvrir des gros flux tres solides des maintenant.
Pour la partie `rencontre programmee`, il faudra soit:
- la simuler via statut + notification,
- soit construire une vraie fonctionnalite dediee.

## Comptes de demo a creer au debut du run

Chaque gros run doit creer ses propres comptes de test pour eviter la pollution:

- `entreprise_demo_<timestamp>@example.test`
- `candidat_demo_<timestamp>@example.test`
- optionnel: `admin_demo_<timestamp>@example.test`

Chaque run doit aussi creer:
- une offre principale
- une candidature manuelle
- une candidature IA quand le matching est lance

## Scenario Demo Public

### Demo Public 1 - Accueil complet

But:
montrer une page d'accueil propre du hero jusqu'au footer.

Etapes:
1. Ouvrir `/`
2. Verifier logo, navigation, langue FR/EN
3. Descendre dans les sections principales
4. Verifier la zone offres
5. Verifier la zone tarifs
6. Verifier le footer

Attendus:
- aucune rupture visuelle
- aucun debordement horizontal
- footer propre

### Demo Public 2 - Recherche et consultation d'offre

But:
montrer qu'un visiteur trouve une offre et comprend l'action suivante.

Etapes:
1. Ouvrir `/offres`
2. Utiliser recherche + localisation + categorie
3. Ouvrir une vraie offre
4. Verifier detail offre
5. Verifier CTA `postuler` ou `se connecter`

Attendus:
- filtres fonctionnels
- URL mise a jour
- detail coherent

### Demo Public 3 - Contact et pages de confiance

But:
montrer les pages de reassurance.

Etapes:
1. Ouvrir `/contact`
2. Verifier les messages d'erreur si formulaire vide
3. Ouvrir `/ressources`
4. Ouvrir `/abonnement`

Attendus:
- messages d'erreur humains
- coherence visuelle entre pages

## Scenario Demo Candidat

### Demo Candidat 1 - Inscription et verification

Statut:
Disponible

Etapes:
1. Creer un compte candidat
2. Verifier l'email
3. Se connecter
4. Arriver sur `/user`

Attendus:
- compte cree
- email verifie
- acces espace candidat

### Demo Candidat 2 - Profil et CV

Statut:
Disponible

Etapes:
1. Completer le profil candidat
2. Ajouter les informations CV principales
3. Verifier que les donnees sont enregistrees
4. Ouvrir l'espace CV

Attendus:
- donnees reellement persistantes
- affichage coherent

### Demo Candidat 3 - Recherche et candidature manuelle

Statut:
Disponible

Etapes:
1. Ouvrir les offres
2. Ouvrir l'offre creee par l'entreprise de demo
3. Postuler avec CV et lettre
4. Verifier succes
5. Verifier historique candidat

Attendus:
- candidature enregistree
- statut initial `en attente`
- historique mis a jour

### Demo Candidat 4 - Notifications et suivi

Statut:
Disponible

Etapes:
1. Ouvrir les notifications
2. Verifier qu'une action entreprise cree bien un retour visible
3. Ouvrir l'historique manuel
4. Ouvrir l'historique IA

Attendus:
- notifications visibles
- statuts coherents
- pas de donnees mockees

### Demo Candidat 5 - CV personnalise

Statut:
Disponible

Etapes:
1. Ouvrir la personnalisation CV
2. Choisir une offre cible
3. Lancer la generation / personnalisation
4. Ouvrir l'apercu

Attendus:
- generation aboutie
- fichier / apercu accessible
- contenu non vide

## Scenario Demo Entreprise

### Demo Entreprise 1 - Inscription et verification

Statut:
Disponible

Etapes:
1. Creer un compte entreprise
2. Verifier l'email
3. Tenter de se connecter
4. Verifier l'etat en attente de validation admin

Attendus:
- compte cree
- email verifie
- message d'attente clair

### Demo Entreprise 2 - Connexion et dashboard

Statut:
Disponible

Etapes:
1. Se connecter avec un compte entreprise approuve
2. Ouvrir `/entreprise`
3. Verifier cartes, recherche, actions

Attendus:
- bon dashboard
- bons liens
- pas de redirection incorrecte

### Demo Entreprise 3 - Creation d'offre

Statut:
Disponible

Etapes:
1. Ouvrir `/entreprise/offres/create`
2. Completer tous les champs
3. Choisir un secteur
4. Publier l'offre
5. Revenir sur le dashboard entreprise

Attendus:
- creation sans erreur
- message de succes
- offre visible dans la liste

### Demo Entreprise 4 - Consultation des candidatures

Statut:
Disponible

Etapes:
1. Ouvrir la candidature du candidat de demo
2. Consulter CV et lettre
3. Voir le profil public / detail candidat

Attendus:
- fichiers consultables
- candidat visible cote entreprise

## E2E Confiance 1 - Offre creee, candidature manuelle, acceptation

Statut:
Disponible presque completement

But:
prouver le cycle principal de recrutement.

Etapes:
1. Creer compte entreprise
2. Valider / utiliser un compte entreprise approuve
3. Creer compte candidat
4. Completer profil candidat minimum
5. L'entreprise cree une offre
6. Le candidat ouvre cette offre
7. Le candidat postule manuellement
8. L'entreprise ouvre la candidature
9. L'entreprise passe le statut a `accepted`
10. Le candidat ouvre ses notifications
11. Le candidat verifie son historique

Attendus:
- offre visible
- candidature creee
- statut passe de `en_attente` a `accepted`
- notification visible cote candidat

## E2E Confiance 2 - Offre creee, candidature manuelle, refus

Statut:
Disponible

Etapes:
1. Reprendre une offre de demo
2. Faire postuler un candidat
3. L'entreprise ouvre la candidature
4. L'entreprise passe le statut a `rejected`
5. Le candidat ouvre ses notifications
6. Le candidat verifie l'historique

Attendus:
- statut `rejected`
- notification claire
- historique coherent

## E2E Confiance 3 - Offre creee, matching IA, candidature automatique

Statut:
Disponible si le candidat possede un profil CV exploitable

Etapes:
1. Creer compte candidat
2. Completer son profil CV de maniere credible
3. Creer compte entreprise
4. L'entreprise cree une offre compatible
5. Lancer ou attendre l'auto-matching
6. Verifier qu'une auto-postulation apparait
7. Verifier historique IA candidat
8. Verifier candidatures IA cote entreprise

Attendus:
- `autopostulation = true`
- candidature visible des deux cotes
- notification liee au matching si prevue

## E2E Confiance 4 - CV personnalise + matching

Statut:
Disponible

Etapes:
1. Le candidat remplit son profil CV
2. Il ouvre la personnalisation CV
3. Il genere un CV cible pour une offre reelle
4. Il ouvre l'aperçu ou le fichier
5. Le matching est relance
6. On verifie l'impact sur les candidatures IA

Attendus:
- CV genere
- apercu disponible
- matching declenche ou exploitable

## E2E Confiance 5 - Cycle complet mixte

Statut:
Disponible en grande partie

Etapes:
1. Creation compte entreprise
2. Creation compte candidat
3. Verification email des deux comptes
4. Activation entreprise si necessaire
5. Creation offre par l'entreprise
6. Completion profil CV candidat
7. Generation CV personnalise
8. Candidature manuelle
9. Matching IA ou auto-postulation
10. Consultation entreprise
11. Acceptation d'une candidature
12. Refus d'une autre candidature
13. Verification notifications et historiques

Attendus:
- coherence globale multi-role
- pas de liens casses
- pas de statuts incoherents

## Scenario Rencontre / Entretien

### Version realiste aujourd'hui

Statut:
Partiellement disponible

On peut tester:
1. entreprise accepte la candidature
2. notification candidat
3. message oriente entretien dans la communication

On ne peut pas encore tester proprement comme vrai workflow produit:
1. choix d'un creneau
2. confirmation d'un rendez-vous
3. page agenda
4. statut dedie `entretien programme`

### Recommendation produit

Avant d'automatiser un vrai scenario `scheduler rencontre`, il faudrait ajouter:
- un statut `interview_scheduled`
- date / heure / mode
- message associe
- notification dediee
- affichage candidat et entreprise

## Priorite de mise en oeuvre

### Priorite 1

- Demo Public 1 a 3
- Demo Candidat 1 a 4
- Demo Entreprise 2 a 4
- E2E Confiance 1
- E2E Confiance 2

### Priorite 2

- Demo Candidat 5
- E2E Confiance 3
- E2E Confiance 4

### Priorite 3

- E2E Confiance 5
- vrai scenario rencontre si la fonctionnalite est construite

## Validation attendue

Si ce document est valide, la suite logique est:
1. creer une suite `tests/ui/demo/`
2. creer une suite `tests/ui/e2e-confidence/`
3. implementer d'abord les scenarios Priorite 1
4. completer ensuite CV, matching et postulation IA
