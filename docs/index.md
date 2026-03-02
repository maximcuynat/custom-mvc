# Documentation — Custom MVC PHP

Bienvenue dans la documentation du template. Chaque section est détaillée dans son propre fichier.

---

## Sommaire

| Guide | Description |
|---|---|
| [Routing](routing.md) | Résolution des URLs vers les contrôleurs |
| [Contrôleurs](controllers.md) | Créer des contrôleurs, render, redirect, JSON |
| [Modèles & ORM](models.md) | Active Record, QueryBuilder, créer un modèle |
| [Vues & Composants](views.md) | Layouts, sections, composants, partials |
| [Frontend CSS](frontend.md) | Design system, grille, composants CSS |
| [Sécurité](security.md) | Protection CSRF, bonnes pratiques |

---

## Structure du projet

```
projet-mvc/
├── README.md
├── docs/                     ← documentation
├── docker-compose.yml
├── Dockerfile
├── Makefile
├── init.sql
│
└── app/
    ├── index.php             ← point d'entrée
    ├── .htaccess             ← réécriture d'URL
    │
    ├── controllers/          ← namespace App\Controllers
    │   ├── Router.php
    │   ├── Controller.php    ← classe abstraite de base
    │   └── ControllerHome.php
    │
    ├── models/               ← namespace App\Models
    │   ├── Database.php      ← Singleton PDO
    │   ├── ActiveRecord.php  ← ORM de base
    │   ├── QueryBuilder.php  ← requêtes chaînables
    │   └── User.php          ← exemple de modèle
    │
    ├── security/             ← namespace App\Security
    │   └── Csrf.php
    │
    ├── public/
    │   └── css/
    │       └── app.css       ← design system
    │
    └── views/                ← namespace App\Views
        ├── View.php          ← moteur de templates
        ├── layouts/
        │   └── app.php       ← layout principal
        ├── components/
        │   └── card.php      ← composant card
        ├── home.php
        └── error.php
```
