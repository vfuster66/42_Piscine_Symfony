# Module 05 - SQL et ORM dans Symfony

Ce module se concentre sur la manipulation de données avec SQL et l'ORM Doctrine dans Symfony, en couvrant différents aspects depuis la création basique de tables jusqu'aux injections SQL.

## SQL vs ORM dans Symfony

### SQL (Structured Query Language)

- Avantages :

    1. Contrôle total sur les requêtes
    2. Meilleures performances sur des requêtes complexes
    3. Visibilité directe sur les opérations effectuées
    4. Flexibilité maximale

- Inconvénients :

    1. Risque d'injections SQL si mal sécurisé
    2. Code moins maintenable et plus verbeux
    3. Dépendance forte à un type de base de données
    4. Gestion manuelle des relations entre tables

- Exemple SQL dans Symfony :
```php
$connection = $this->entityManager->getConnection();
$sql = "SELECT * FROM users WHERE email = :email";
$result = $connection->executeQuery($sql, ['email' => $email]);
```

### ORM (Object-Relational Mapping)

- Avantages :

    1. Code plus maintenable et orienté objet
    2. Protection native contre les injections SQL
    3. Abstraction de la base de données
    4. Gestion automatique des relations
    5. Validation intégrée
    6. Migration de schéma facilitée

- Inconvénients :

    1. Performances potentiellement moins bonnes sur des requêtes complexes
    2. Courbe d'apprentissage plus importante
    3. Peut générer des requêtes non optimisées
    4. Overhead en termes de mémoire

- Exemple ORM dans Symfony :
```php
$user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
```

### Quand utiliser quoi ?

- Utiliser SQL pour :

    1. Requêtes très complexes avec performances critiques
    2. Rapports et agrégations complexes
    3. Optimisations spécifiques
    4. Migrations de données

- Utiliser ORM pour :

    1. CRUD standard
    2. Applications avec beaucoup de relations
    3. Développement rapide
    4. Code maintenable à long terme
    5. Équipes multiples/larges

## 🎯 Objectifs du Module
- Comprendre et manipuler les bases de données avec SQL et ORM
- Gérer les relations entre entités
- Implémenter des opérations CRUD complètes
- Découvrir les vulnérabilités SQL

## 📝 Exercices

### Exercices 00-11
Ces exercices couvrent les bases de la manipulation de données, incluant :

- Création de tables (SQL et ORM)
- Insertion et lecture de données
- Gestion des relations entre entités
- Requêtes avec jointures, tri et conditions

### Exercice 12 : ORM - Requêtes avec jointures, tri et conditions
Implémentation complète d'une gestion de données avec :

- Relations OneToOne et OneToMany
- Formulaires complexes
- Filtrage et tri des données
- Validation des données

### Exercice 13 : ORM - Opérations CRUD complètes
Application de gestion des employés incluant :

- Structure d'entité complète (Employee)
- Relations hiérarchiques
- Validation des données
- Interface utilisateur responsive

### Exercice 14 : SQL - Injections fonctionnelles
Démonstration des vulnérabilités SQL :

- Pour tester :

1. Créer la table :
```
Accéder à /ex14/create-table
```

2. Tests d'injection SQL:
```
URL: /ex14/login

Test 1 - Connecté en tant qu'admin :
- Username: ' OR '1'='1
- Password: ' OR '1'='1

Test 2 - Connexion admin sans mot de passe :
- Username: admin'-- 
- Password: [n'importe quoi]

Test 3 - Suppression de la table :
- Username: '; DROP TABLE ex14_users; --
- Password: [n'importe quoi]
```

## 🚀 Lancement
```bash
symfony server:start
```

