<?php 
/*
PLAN DU TICKET 1 : Service avec Symfony

Pour ce ticket, créer un service nommé TrackingService dans le fichier :

src/Service/TrackingService.php

Ce service aura pour responsabilité de centraliser la logique métier liée au changement de statut d’un colis.

Créer une méthode publique changerStatut() recevant les paramètres suivants :

le colis à mettre à jour ;
le nouveau statut du colis ;
le lieu où le changement de statut a été effectué ;
un commentaire facultatif.

Cette méthode devra :

mettre à jour le statut actuel du colis ;
créer une nouvelle instance de MouvementColis ;
associer le mouvement au colis concerné ;
enregistrer dans le mouvement le nouveau statut, le lieu et le commentaire éventuel ;
persister le nouveau mouvement avec Doctrine ;
enregistrer toutes les modifications en base de données.

L’EntityManagerInterface devra être injecté dans le constructeur du service.

Le colis étant déjà géré par Doctrine lorsqu’il provient d’un repository ou d’un paramètre de contrôleur, seul le nouveau 
mouvement devra être explicitement persisté.

L’objectif de ce service est de garantir que chaque changement de statut d’un colis entraîne automatiquement la création 
d’une nouvelle entrée dans son historique.

__________________________________________________________________________________________________________________________

PLAN DU TICKET 2 : Voter

Nous allons avancer dans cet ordre :

définir les règles d’autorisation ;
créer AgenceVoter, ClientVoter et ColisVoter ;
protéger les actions du contrôleur ;
masquer les boutons interdits dans Twig ;
tester avec un administrateur et un utilisateur standard ;
appliquer ensuite le même principe à Client et Agence.

_______________________________________________________________________________________________________________________________


PLAN DU TICKET 3 : CRÉER UN DTO DE RECHERCHE

Créer :

ColisSearchDTO

Aujourd'hui, notre contrôleur reçoit :

$request->query->getString('q');
$request->query->getString('ville');
$request->query->getString('statut');

Nous pourrions remplacer cela par :

ColisSearchDTO

Le contrôleur deviendrait beaucoup plus élégant.

L'OBJECTIF

Pour le moment:

HTTP Request
|
Controller
|
Repository

__________________

HTTP Request
|
Search Form : ColisSearchType.php
|
ColisSearchDTO.php
|
Repository

Le controller ne manipule plus de chaines
Il manipule un objet

Objectif :

Dans ce ticket, nous allons améliorer la recherche des colis en regroupant tous les critères, comme le texte recherché, le statut et la ville, dans un objet appelé `ColisSearchDTO`.
Nous allons également créer un formulaire `ColisSearchType`, puis adapter le contrôleur, le repository et le template Twig.
Cette organisation permettra d’alléger le contrôleur, d’éviter de multiplier les paramètres dans le repository et de faciliter l’ajout de nouveaux critères de recherche.
Le formulaire récupérera les critères saisis par l’utilisateur, le DTO les transportera et le repository les utilisera pour construire la requête Doctrine.
Cette architecture, courante dans les projets Symfony professionnels, rend le code plus lisible, évolutif et facile à maintenir.

_______________________________________________________________________________________________________________________________


TICKET 4 : Events & Listeners

Objectif : 
    - Comprendre le pattern Event-Listener
    - Créer un Event personnalisé
    - Dispatch un Event depuis un Service
    - Créer un Listener pour réagir à l'Event
    - Journaliser les changements de statut
    - Créer des Listeners pour des actions métier

LE PLAN DU TICKET 4

Nous allons procéder très progressivement.

4.1

Créer

ColisStatusChangedEvent

4.2

Modifier

TrackingService

pour dispatcher cet Event.

4.3 => On peut créer :

ColisStatusChangedLogger : Permet de journaliser le changement de statut dans les logs Symfony.

ou

ColisStatusChangedListener : Permet de faire des traitements plus complexes quand le statut change.

4.4

Journaliser automatiquement :

Le colis XXX est passé en EN_TRANSIT

4.5

Créer un second Listener 

Notification

4.6

Créer un troisième Listener

Statistiques

4.7

Créer un quatrième Listener

Mail

(pour plus tard avec Mailer)

Le TrackingService ne changera presque plus.

On ajoutera simplement de nouveaux Listeners.


trackingService -> 13:10:00
Listener Mail -> 13:10:02
Listener Log -> 13:10:04

Le Ticket 4 est terminé.

4.8 : On peut créer une véritable entité Audit pour persister les événements en base (au lieu de les écrire uniquement dans un fichier).

_______________________________________________________________________________________________________________________________

// Vérifier et configurer Symfony Messenger

Formulaire de changement de statut
        |
TrackingService
        |
ColisStatusChangedEvent
        |
ColisLivreMessageDispatcher
        |
ColisLivreMessage
        |
transport async
        |
worker messenger:consume
        |
ColisLivreMessageHandler
        |
log "Notification asynchrone..."

Etapes :

.1 Messenger                         
.2 ColisLivreMessage                
.3 Dispatch si LIVRE                
.4 MessageHandler                   
.5 Transport async + worker         
.6 Cycle temps réel                 
.7 Gestion des échecs / failed      
.8 Mailtrap / véritable e-mail

ETAPES :

1-
composer show symfony/messenger

composer require symfony/messenger

* Configuration du fichier messenger.yaml
* configuration du fichier .env : MESSENGER_TRANSPORT_DSN=doctrine://default?queue_name=async

php bin/console cache:clear

php bin/console messenger:stats

2-
php bin/console messenger:consume async -vv

php bin/console messenger:stats

Select-String `
    -Path var\log\dev.log `
    -Pattern "Notification asynchrone"

3-
Pour arrêter le worker :

Ctrl + C

4-
Symfony Mailer + Mailtrap :
Avant de coder l’envoi réel, on commence par vérifier si Mailer est déjà installé.

composer show symfony/mailer

Si symfony/mailer n’est pas installé, on fera :

composer show symfony/mailer

configurer .env pour le DSN : MAILER_DSN="vos informations Mailtrap"

php bin/console cache:clear

php bin/console debug:container --env-vars | Select-String "MAILER_DSN"

Voir le reste côté projet

L’idée est simple : au lieu de seulement logger :

Notification asynchrone : le colis ... a été livré.

le handler va maintenant envoyer un vrai e-mail au client.






*/