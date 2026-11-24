# CinéTech

Projet fil rouge Symfony 8 (PHP 8.4, Doctrine ORM, Twig 3, Tailwind CSS) :
gestion d'un catalogue de films avec catégories, authentification et comptes utilisateurs.

## Installation

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load -n
```

Configurer la base de données dans `.env.local` (`DATABASE_URL`), puis lancer :

```bash
symfony serve -d
```

## Comptes de test

Les fixtures créent deux comptes utilisateurs prêts à l'emploi :

| Rôle             | Email                | Mot de passe |
| ---------------- | -------------------- | ------------ |
| Administrateur   | `admin@cinetech.fr`  | `admin123`   |
| Utilisateur      | `user@cinetech.fr`   | `user123`    |

- L'administrateur possède le rôle `ROLE_ADMIN` (accès étendu).
- L'utilisateur standard possède le rôle `ROLE_USER`.

Un compte supplémentaire peut être créé librement depuis la page `/register`
(inscription sans vérification d'email). La connexion se fait sur `/login`.
