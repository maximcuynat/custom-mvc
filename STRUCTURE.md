# Structure Complète du Projet

```
projet-mvc/
│
├── docker-compose.yml
├── Dockerfile
├── init.sql
├── Makefile
├── .env.example
├── .gitignore
├── README.md
│
└── app/
    ├── .htaccess
    ├── index.php
    │
    ├── controllers/
    │   ├── Router.php
    │   └── ControllerHome.php
    │
    ├── models/
    │   ├── Database.php
    │   ├── ActiveRecord.php
    │   └── User.php
    │
    └── views/
        ├── View.php
        ├── template.php
        ├── viewHome.php
        └── viewError.php
```

## Fichiers à placer

### Racine du projet :
- `docker-compose.yml`
- `Dockerfile`
- `init.sql`
- `Makefile`
- `.env.example` (renommer `_env.example`)
- `.gitignore` (renommer `_gitignore`)
- `README.md`

### app/ :
- `.htaccess` (renommer `_htaccess`)
- `index.php`

### app/controllers/ :
- `Router.php`
- `ControllerHome.php`

### app/models/ :
- `Database.php`
- `ActiveRecord.php`
- `User.php`

### app/views/ :
- `View.php`
- `template.php`
- `viewHome.php` (renommer `viewAccueil.php`)
- `viewError.php`

## ⚠️ Fichiers SUPPRIMÉS :
- ❌ `Model.php` → remplacé par `Database.php` + `ActiveRecord.php`
- ❌ `ORMREADME.md` → intégré dans le README principal
- ❌ `examples_orm.php` → juste pour référence, pas à mettre dans le projet

## Changements dans les fichiers :

### ControllerHome.php
Ligne 14 : `viewAccueil.php` → `viewHome.php`
```php
private function home()
{
    $this->_view = new View('Home');
    $this->_view->generate('Home', array());
}
```