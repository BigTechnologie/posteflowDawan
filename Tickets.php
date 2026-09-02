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

*/