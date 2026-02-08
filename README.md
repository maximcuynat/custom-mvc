# Projet MVC PHP avec Docker + ORM Active Record

Architecture MVC en PHP avec Docker, MySQL, Adminer et ORM Active Record custom.

## Stack Technique

- **PHP 8.2** avec Apache
- **MySQL 8.0**
- **Adminer** (interface web pour MySQL)
- **ORM Active Record** custom (style Laravel Eloquent)
- **Docker & Docker Compose**

## Prérequis

- Docker
- Docker Compose

## Structure du Projet

```
projet-mvc/
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
    │   ├── Database.php       # Singleton PDO
    │   ├── ActiveRecord.php   # ORM Base
    │   └── User.php           # Modèle User
    │
    └── views/
        ├── View.php
        ├── template.php
        ├── viewHome.php
        └── viewError.php
```

## Installation et Lancement

### 1. Cloner le projet

```bash
git clone <votre-repo>
cd <nom-projet>
```

### 2. Lancer les conteneurs

```bash
docker-compose up -d --build
```

Ou avec Make :

```bash
make build
make up
```

Cette commande :
- Build l'image PHP avec Apache + PDO MySQL
- Lance MySQL
- Lance Adminer
- Crée le réseau bridge
- Initialise la BDD avec `init.sql`

### 3. Vérifier que tout fonctionne

```bash
docker-compose ps
```

Tous les services doivent être "Up".

## Accès aux Services

| Service | URL | Identifiants |
|---------|-----|--------------|
| **Application Web** | http://localhost:8000 | - |
| **Adminer** | http://localhost:8080 | Serveur: `db`<br>User: `root`<br>Password: `root_password`<br>BDD: `internships` |
| **MySQL** (direct) | localhost:3306 | User: `root`<br>Password: `root_password` |

## Commandes Make

```bash
make build      # Build les images
make up         # Démarre les conteneurs
make down       # Arrête les conteneurs
make restart    # Redémarre
make logs       # Affiche les logs
make shell      # Shell dans le conteneur web
make db-shell   # Shell MySQL
make clean      # Supprime tout (⚠️ efface la BDD)
make rebuild    # Rebuild complet
```

## Architecture MVC

### Routing

Le fichier `.htaccess` redirige toutes les requêtes vers `index.php` qui instancie le `Router`.

**URLs** :
- `/` → redirige vers `/home`
- `/home` → `ControllerHome::home()`
- `/user/profile/123` → `ControllerUser::profile($url)`

### Contrôleur

```php
<?php
require_once('views/View.php');

class ControllerExample
{
    private $_view;

    public function __construct($url)
    {
        if (isset($url[0]) && $url[0] === "example")
            $this->example();
        else
            throw new Exception('Page introuvable');
    }

    private function example()
    {
        $this->_view = new View('Example');
        $this->_view->generate('Titre', array('key' => 'value'));
    }
}
```

### Vue

Créer `views/viewExample.php` :

```php
<div>
    <h1>Exemple</h1>
    <p><?= $key ?></p>
</div>
```

## ORM Active Record

### Créer un Modèle

```php
<?php
require_once('ActiveRecord.php');

class Post extends ActiveRecord
{
    protected static $table = 'posts';
}
```

Le nom de table est auto-déduit si non spécifié :
- `User` → `users`
- `Post` → `posts`

### Utilisation de l'ORM

#### Récupération

```php
// Tous les utilisateurs
$users = User::all();

// Par ID
$user = User::find(1);

// Par ID ou exception
$user = User::findOrFail(1);

// Premier
$user = User::first();

// WHERE
$admins = User::where('username', '=', 'admin');
$admins = User::where('username', 'admin'); // = par défaut

// Compter
$count = User::count();
```

#### Création

```php
// Méthode 1
$user = new User();
$user->username = 'john';
$user->setPassword('secret');
$user->save();

// Méthode 2
$user = User::create([
    'username' => 'john',
    'password' => password_hash('secret', PASSWORD_BCRYPT)
]);
```

#### Mise à jour

```php
$user = User::find(1);
$user->username = 'nouveau_nom';
$user->save();
```

#### Suppression

```php
// Méthode 1
$user = User::find(1);
$user->delete();

// Méthode 2
User::destroy(1);
```

### Modèle User - Méthodes Spécifiques

```php
// Hasher un mot de passe
$user->setPassword('plain_password');

// Vérifier un mot de passe
if ($user->verifyPassword('plain_password')) {
    echo "OK";
}

// Trouver par username
$user = User::findByUsername('admin');

// Authentifier
$user = User::authenticate('admin', 'password');
if ($user) {
    echo "Connecté : " . $user->username;
}
```

### Utilitaires

```php
$user = User::find(1);

// Vérifier si modifié
$user->isDirty(); // bool

// Valeurs originales
$original = $user->getOriginal();

// Rafraîchir depuis la BDD
$user->refresh();

// Convertir
$array = $user->toArray();
$json = $user->toJson();
```

## Base de Données

### Tables

La BDD est initialisée avec `init.sql` :

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Données de test

- Username: `admin` / Password: `password`
- Username: `user1` / Password: `password`

## Exemple Complet d'Utilisation

```php
<?php
// Dans un contrôleur

require_once('models/User.php');

// Lister tous les utilisateurs
$users = User::all();

// Authentifier
$user = User::authenticate('admin', 'password');
if ($user) {
    $_SESSION['user_id'] = $user->id;
    $_SESSION['username'] = $user->username;
}

// Créer un utilisateur
$newUser = User::create([
    'username' => 'john_doe',
    'password' => password_hash('secret123', PASSWORD_BCRYPT)
]);

// Modifier
$user = User::find(1);
$user->username = 'admin_updated';
$user->save();
```

## Variables d'Environnement

Définies dans `docker-compose.yml` :

```yaml
environment:
  - DB_HOST=db
  - DB_NAME=internships
  - DB_USER=root
  - DB_PASSWORD=root_password
```

## Développement

Les modifications dans `./app` sont synchronisées en temps réel.

Pas besoin de rebuild pour les changements PHP.

## Troubleshooting

### L'app ne se connecte pas à la BDD

```bash
docker-compose logs db
```

Attends `ready for connections`.

### Port déjà utilisé

Modifie dans `docker-compose.yml` :

```yaml
ports:
  - "8001:80"
```

### Réinitialiser

```bash
docker-compose down -v
docker-compose up -d --build
```

Ou :

```bash
make clean
make rebuild
```

## Sécurité

⚠️ **En production** :
- Change les mots de passe MySQL
- Utilise un fichier `.env` externe
- Active HTTPS
- Configure un reverse proxy
- Limite l'accès à Adminer
- Utilise des variables d'environnement pour les secrets

## Extensions ORM Futures

- Relations (hasMany, belongsTo, belongsToMany)
- Query Builder chainable avancé
- Scopes (local et global)
- Events (creating, created, etc.)
- Soft Deletes
- Pagination
- Eager Loading