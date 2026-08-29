# PosteFlow final — projet Symfony complet

Application métier de formation Symfony pour une développeuse travaillant dans un contexte proche de La Poste.

## Fonctionnalités

- Authentification : `admin@posteflow.test / admin1234`
- Tableau de bord
- CRUD Agences
- CRUD Clients
- CRUD Colis
- Suivi colis avec historique de mouvements
- Services métier
- Doctrine ORM
- Repositories avec QueryBuilder
- Fixtures complètes
- Templates Twig + Bootstrap
- Migration initiale incluse

## Installation Windows / PowerShell

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
symfony serve
```

Puis ouvrir l'URL affichée par Symfony CLI.

## Important

Avant de lancer les commandes Doctrine, vérifie `.env` :

```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/posteflow_final?serverVersion=8.0.32&charset=utf8mb4"
```

Adapte `root`, mot de passe, hôte et port selon ton environnement.

## Plan de formation conseillé

1. Présenter l'architecture MVC Symfony.
2. Lire les entités `Agence`, `Client`, `Colis`, `MouvementColis`.
3. Expliquer les relations Doctrine.
4. Reproduire les CRUD Agence et Client.
5. Reproduire le CRUD Colis.
6. Étudier `TrackingService`.
7. Étudier les méthodes QueryBuilder dans `ColisRepository`.
8. Faire modifier les filtres de recherche.
9. Ajouter un nouveau statut métier.
10. Ajouter une page d'export ou de reporting.

# Pour ce projet voici le lien côté dev : https://chatgpt.com/c/6a424d5a-ea84-83ed-93f6-9f3d913687ab

