# Bibliothèque de prêt — Symfony

Application web de gestion d'une bibliothèque de prêt développée avec **Symfony 7.2**, **Doctrine ORM** et **MySQL/MariaDB**.

Projet réalisé dans le cadre du module **POO / Framework PHP**.

---

## Fonctionnalités

### Partie publique
- Page d'accueil avec présentation + aperçu des livres
- Catalogue complet des livres
- Page de détail d'un livre
- Filtre par catégorie et par disponibilité
- Affichage des catégories

### Utilisateur connecté (`ROLE_USER`)
- Inscription + connexion
- Consultation du profil
- Liste des emprunts personnels
- Emprunt d'un livre disponible (14 jours)
- Suivi du statut des emprunts

### Administration (`ROLE_ADMIN`)
- CRUD complet des livres
- CRUD complet des catégories
- Liste des utilisateurs
- Gestion des emprunts (marquer comme rendu)

---

## Stack technique

- PHP 8.4
- Symfony 7.2
- Doctrine ORM 3
- Twig avec layout principal (`base.html.twig`)
- Bootstrap 5 (CDN)
- MySQL / MariaDB (via XAMPP)

---

## Modélisation

4 entités, 3 relations principales :

| Entité | Champs principaux | Relations |
|---|---|---|
| **User** | email, password, roles, prenom, nom | OneToMany → Emprunt |
| **Categorie** | nom, description | OneToMany → Livre |
| **Livre** | titre, auteur, resume, datePublication, disponible | ManyToOne → Categorie, OneToMany → Emprunt |
| **Emprunt** | dateEmprunt, dateRetourPrevue, dateRetourReelle, statut | ManyToOne → User, ManyToOne → Livre |

---

## Installation

### 1. Prérequis
- PHP ≥ 8.4
- Composer
- MySQL (XAMPP recommandé)

### 2. Cloner / copier le projet
```bash
cd ~/votre-dossier
# (récupérer le code source)
cd bibliotheque
```

### 3. Installer les dépendances
```bash
composer install
```

### 4. Configurer la base de données
Le fichier `.env` contient déjà la configuration pour XAMPP (root sans mot de passe).
Si besoin, modifiez `DATABASE_URL` dans `.env` :
```
DATABASE_URL="mysql://root:@127.0.0.1:3306/bibliotheque?serverVersion=10.4.28-MariaDB&charset=utf8mb4"
```

### 5. Créer la BDD, lancer les migrations et charger les fixtures
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
```

### 6. Démarrer le serveur
```bash
php -S 127.0.0.1:8000 -t public/
```

Puis ouvrir http://127.0.0.1:8000/ dans le navigateur.

---

## Comptes de test

| Rôle | Email | Mot de passe |
|---|---|---|
| **Admin** | admin@biblio.fr | admin123 |
| Utilisateur | user@biblio.fr | user123 |
| Utilisateur | marie@biblio.fr | user123 |

---

## Contraintes respectées

- ✅ 4 entités (User, Categorie, Livre, Emprunt)
- ✅ 3 relations (Livre↔Categorie, User↔Emprunt, Livre↔Emprunt)
- ✅ CRUD complet (livres + catégories)
- ✅ 2+ formulaires Symfony (LivreType, CategorieType, RegistrationFormType)
- ✅ Contraintes de validation (NotBlank, Email, Length, Choice, NotNull)
- ✅ Système de connexion (form_login)
- ✅ Zone `/admin` accessible uniquement à `ROLE_ADMIN`
- ✅ Fixtures avec données de test
- ✅ Template Twig de base (`base.html.twig`) réutilisé

## Bonus implémentés
- Messages flash (success / error)
- Filtre "disponibles uniquement"
- Inscription utilisateur
- Interface Bootstrap soignée
- Retour d'emprunt marqué par l'admin

---

## Structure du projet

```
src/
├── Controller/
│   ├── HomeController.php
│   ├── LivreController.php
│   ├── UserController.php
│   ├── SecurityController.php
│   ├── RegistrationController.php
│   └── Admin/
│       ├── AdminLivreController.php
│       ├── AdminCategorieController.php
│       └── AdminEmpruntController.php
├── Entity/
│   ├── User.php
│   ├── Categorie.php
│   ├── Livre.php
│   └── Emprunt.php
├── Form/
│   ├── LivreType.php
│   ├── CategorieType.php
│   └── RegistrationFormType.php
├── Repository/
└── DataFixtures/
    └── AppFixtures.php
templates/
├── base.html.twig
├── home/
├── livre/
├── user/
├── security/
├── registration/
└── admin/
```
