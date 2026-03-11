# Ultras Lions Fleury — Site & Intranet

Application web pour un club/supporters avec un espace public (actus, événements, galeries, pages) et un espace membre/admin (billetterie, boutique, gestion de contenu).

**Stack**
- PHP 8.2+ / Symfony 7.3
- Twig + Asset Mapper/Importmap + Stimulus
- Doctrine ORM + migrations
- EasyAdmin (back‑office)
- VichUploader (médias)
- Docker Compose (PHP-FPM, Nginx, MariaDB, phpMyAdmin)

**Fonctionnalités (repérées dans le code)**
- Actualités/Posts, pages CMS, événements, galeries
- Billetterie (catégories, achats)
- Boutique (merch, stocks, tailles)
- Gestion membres/profils, invitations, staff
- Authentification + reset password
- Back‑office EasyAdmin

**Structure**
- `backend/` : application Symfony
- `docker/` : conf Docker (php/nginx)
- `docker-compose.yml` : stack locale MariaDB/Nginx/PHP/PhpMyAdmin

**Démarrage rapide (Docker)**
1. `docker compose up -d --build`
2. `docker compose exec php composer install`
3. `docker compose exec php bin/console doctrine:migrations:migrate`
4. Ouvrir `http://localhost:8080`
5. PhpMyAdmin: `http://localhost:8081`

**Configuration (.env)**
- L’app utilise MariaDB via `DATABASE_URL="mysql://symfony:symfony@ultras_db:3306/ultras_lions?serverVersion=11.0&charset=utf8mb4"`.
- Définir un `APP_SECRET` local.
- Éviter de committer des secrets: placer `MAILER_DSN` et autres secrets dans `.env.local`.

**Commandes utiles**
- Console Symfony: `docker compose exec php bin/console`
- Migrations: `docker compose exec php bin/console doctrine:migrations:migrate`
- Tests: `docker compose exec php bin/phpunit`

**Remarques**
- `backend/compose.yaml` est le template Symfony (PostgreSQL) et n’est pas utilisé par la stack Docker racine.
- Des scripts/exports SQL existent dans `backend/migrations/` si besoin d’importer des données.
