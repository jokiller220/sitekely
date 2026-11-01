# Media Spotlight

Application PHP 8.2+ conçue selon une architecture MVC légère sans framework. SQLite est utilisé en local par défaut ; PostgreSQL Supabase est pris en charge côté serveur.

## Structure

- `index.php` : front controller unique.
- `app/config.php` : configuration applicative.
- `app/database.php` : connexion, schéma et migrations.
- `app/actions.php` : traitement des formulaires et règles métier.
- `app/router.php` : résolution des routes et préparation des données.
- `app/helpers.php` : sécurité, sessions, rendu et utilitaires.
- `views/pages/` : une vue PHP par écran.
- `views/partials/` : composants visuels réutilisables.
- `public/assets/` : CSS et JavaScript publics.
- `data/` : base SQLite et fichiers privés.

## Démarrage

Placez le projet dans le dossier `htdocs` de MAMP, démarrez Apache, puis ouvrez :

`http://localhost/sitekely/`

Compte administrateur initial : `admin@mediaspotlight.local` / `ChangeMe!2026`.
Changez ce mot de passe avant toute mise en production.

## Connexion à Supabase

1. Dans votre projet Supabase, ouvrez **Connect** et copiez la chaîne de connexion **Session pooler** (adaptée à un serveur PHP/MAMP IPv4). Conservez `sslmode=require`.
2. Copiez `.env.example` vers `.env.local`, puis renseignez `SUPABASE_DB_DSN`, `SUPABASE_DB_USER` et `SUPABASE_DB_PASSWORD`. Ce fichier est ignoré par Git.
3. Activez `pdo_pgsql` dans le `php.ini` utilisé par Apache/MAMP et redémarrez Apache.
4. Dans l’éditeur SQL Supabase, exécutez une seule fois [database/supabase-schema.sql](database/supabase-schema.sql). Le schéma active RLS sur toutes les tables : l’accès passe uniquement par le serveur PHP et aucun rôle navigateur n’obtient de politique d’accès.

Sans ces trois variables, le site conserve automatiquement SQLite. Ne placez jamais une clé `service_role`, un mot de passe PostgreSQL ou `SUPABASE_DB_*` dans le JavaScript ou dans `public/`.
