# ORM Active Record - Documentation

ORM Active Record custom pour MySQL, inspiré de Laravel Eloquent.

## Architecture

```
models/
├── Database.php      # Singleton de connexion PDO
├── ActiveRecord.php  # Classe abstraite de base
└── User.php          # Modèle User (exemple)
```

## Installation

Place les fichiers dans `app/models/` :
- `Database.php`
- `ActiveRecord.php`
- `User.php`

## Créer un Modèle

```php
<?php

require_once('ActiveRecord.php');

class Post extends ActiveRecord
{
    protected static $table = 'posts'; // Optionnel, par défaut = nom de classe + 's'
    protected static $primaryKey = 'id'; // Par défaut 'id'
}
```

Le nom de table est automatiquement déduit :
- `User` → `users`
- `Post` → `posts`
- `Article` → `articles`

## Méthodes Disponibles

### Récupération

```php
// Tous les enregistrements
$users = User::all();

// Par ID
$user = User::find(1);

// Par ID ou exception
$user = User::findOrFail(1);

// Premier enregistrement
$user = User::first();

// WHERE
$users = User::where('username', '=', 'admin');
$users = User::where('username', 'admin'); // = par défaut

// Compter
$count = User::count();
```

### Création

```php
// Méthode 1
$user = new User();
$user->username = 'john';
$user->password = 'hash';
$user->save();

// Méthode 2
$user = User::create([
    'username' => 'john',
    'password' => 'hash'
]);

// Méthode 3
$user = new User();
$user->fill(['username' => 'john'])->save();
```

### Mise à Jour

```php
$user = User::find(1);
$user->username = 'new_name';
$user->save();
```

### Suppression

```php
// Méthode 1
$user = User::find(1);
$user->delete();

// Méthode 2
User::destroy(1);
```

### Utilitaires

```php
$user = User::find(1);

// Vérifier si modifié
$user->isDirty(); // true/false

// Valeurs originales
$original = $user->getOriginal();

// Rafraîchir depuis la BDD
$user->refresh();

// Convertir
$array = $user->toArray();
$json = $user->toJson();
```

## Modèle User

Le modèle `User` inclut des méthodes spécifiques :

```php
// Hasher un mot de passe
$user->setPassword('plain_password');

// Vérifier un mot de passe
$user->verifyPassword('plain_password'); // true/false

// Trouver par username
$user = User::findByUsername('admin');

// Authentifier
$user = User::authenticate('admin', 'password');
if ($user) {
    // Connexion réussie
}
```

## Timestamps Automatiques

Si ta table a un champ `created_at`, il sera rempli automatiquement lors de l'insertion.

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Créer un Nouveau Modèle

Exemple pour une table `posts` :

```php
<?php

require_once('ActiveRecord.php');

class Post extends ActiveRecord
{
    protected static $table = 'posts';
    
    // Méthodes custom
    public function getExcerpt($length = 100)
    {
        return substr($this->content, 0, $length) . '...';
    }
    
    public static function published()
    {
        return static::where('status', 'published');
    }
}
```

Utilisation :

```php
$posts = Post::published();
$post = Post::find(1);
echo $post->getExcerpt(50);
```

## Gestion des Erreurs

```php
try {
    $user = User::findOrFail(999);
} catch (Exception $e) {
    echo "Utilisateur introuvable";
}

try {
    $user = new User();
    $user->delete(); // Exception : modèle n'existe pas
} catch (Exception $e) {
    echo $e->getMessage();
}
```

## Limitations Actuelles

- Pas de relations (hasMany, belongsTo, etc.)
- Pas de Query Builder chainable avancé
- Pas de soft deletes
- WHERE basique (un seul champ)
- Pas de JOIN
- Pas de ORDER BY / GROUP BY dans les méthodes

## Extensions Futures Possibles

- Relations (hasMany, belongsTo, belongsToMany)
- Query Builder fluent chainable
- Scopes (local et global)
- Mutators et Accessors
- Events (creating, created, updating, updated, etc.)
- Soft Deletes
- Pagination
- Eager Loading (N+1 problem)

## Performance

L'ORM utilise PDO avec prepared statements pour éviter les injections SQL.
Connection en singleton pour éviter les connexions multiples.

## Exemple Complet

```php
<?php

require_once('models/User.php');

// Créer un utilisateur
$user = User::create([
    'username' => 'john_doe',
    'password' => password_hash('secret', PASSWORD_BCRYPT)
]);

echo "Utilisateur créé : " . $user->id;

// Modifier
$user->username = 'john_updated';
$user->save();

// Authentifier
$auth = User::authenticate('john_updated', 'secret');
if ($auth) {
    echo "Connecté : " . $auth->username;
}

// Lister tous
foreach (User::all() as $u) {
    echo $u->username . "\n";
}

// Supprimer
$user->delete();
```