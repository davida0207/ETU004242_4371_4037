# BNGRC - Suivi Collectes / Distributions

Ce module suit le cahier des charges dans `todolist.yml`.

## Base de données
- Schéma (complet / recommandé): `sql/base.sql`
- Schéma (ultra simple, pas de FK/UNIQUE, index optionnels): `sql/base_simple.sql`
- Schéma (FK oui, pas de KEY/UNIQUE explicites): `sql/base_fk_simple.sql`
- Données de test: `sql/seed.sql`

### Convention de nommage (préfixe)
Toutes les tables BNGRC utilisent le préfixe `bngrc_` pour éviter les conflits avec d'autres modules du même `dbname` (ex: `bngrc_regions`, `bngrc_besoins`, etc.).

### Import (MySQL)
1. Créer la base (si nécessaire) puis importer:
   - `sql/base.sql`
   - `sql/seed.sql`

2. Vérifier la config DB dans `app/config/config.php` (`dbname`, `user`, `password`).

> Où est la base ?
> - La base est un serveur MySQL (pas un fichier) et elle est définie par `host` + `dbname` dans `app/config/config.php`.
> - Dans ce projet, le `docker-compose.yml` ne démarre que PHP (pas MySQL) : il faut donc un MySQL local ou ajouter un service MySQL.

## Routes
- `/dashboard` : affiche le template HTML fourni (inchangé)
- `/` : redirige vers le dashboard dynamique
- `/bngrc/dashboard` : dashboard dynamique (données DB)

Les pages CRUD et dispatch seront ajoutées ensuite.
