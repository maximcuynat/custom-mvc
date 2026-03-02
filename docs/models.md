# Modèles & ORM

[← Sommaire](index.md)

---

## Créer un modèle

Chaque modèle doit :
- se trouver dans `app/models/`
- avoir le namespace `App\Models`
- étendre `ActiveRecord`

```php
// app/models/Article.php
namespace App\Models;

class Article extends ActiveRecord
{
    // table auto-déduite : "articles"
}
```

Le nom de la table est déduit automatiquement du nom de la classe :

| Classe | Table déduite |
|---|---|
| `User` | `users` |
| `Article` | `articles` |
| `Category` | `categories` |
| `BlogPost` | `blogposts` |

Pour forcer un nom de table :

```php
class Article extends ActiveRecord
{
    protected static $table = 'mes_articles';
}
```

---

## Timestamps automatiques

Par défaut (`$timestamps = true`), `created_at` et `updated_at` sont gérés automatiquement :
- `created_at` + `updated_at` → remplis à la **création**
- `updated_at` → mis à jour à chaque **modification**

Pour désactiver sur un modèle :

```php
class Log extends ActiveRecord
{
    protected static bool $timestamps = false;
}
```

Assure-toi que la table SQL contient ces colonnes :

```sql
created_at DATETIME,
updated_at DATETIME
```

---

## Récupérer des données

```php
// Tous les enregistrements
$articles = Article::all();

// Par ID
$article = Article::find(42);         // null si inexistant
$article = Article::findOrFail(42);   // exception si inexistant

// Premier enregistrement
$article = Article::first();

// Compter
$total = Article::count();
```

---

## Requêtes avec conditions — QueryBuilder

`where()` retourne un `QueryBuilder` chaînable.

```php
// Égalité simple (opérateur '=' par défaut)
$articles = Article::where('status', 'published')->get();

// Avec opérateur explicite
$recent = Article::where('created_at', '>=', '2024-01-01')->get();

// Chaîner plusieurs conditions (AND)
$results = Article::where('status', 'published')
                  ->where('author_id', 5)
                  ->orderBy('created_at', 'DESC')
                  ->limit(10)
                  ->get();

// Premier résultat
$article = Article::where('slug', $slug)->first();

// Compter
$count = Article::where('status', 'draft')->count();
```

### Opérateurs disponibles

`=` `!=` `<>` `<` `>` `<=` `>=` `LIKE` `NOT LIKE`

```php
Article::where('title', 'LIKE', '%php%')->get();
```

### Méthodes du QueryBuilder

| Méthode | Description |
|---|---|
| `->where($col, $op, $val)` | Ajoute une condition WHERE |
| `->orderBy($col, 'ASC'|'DESC')` | Trie les résultats |
| `->limit(int)` | Limite le nombre de résultats |
| `->offset(int)` | Décalage (pagination) |
| `->get()` | Exécute et retourne un tableau d'objets |
| `->first()` | Retourne le premier résultat ou `null` |
| `->count()` | Retourne le nombre de résultats |

---

## Créer un enregistrement

```php
// Méthode statique (recommandée)
$article = Article::create([
    'title'   => 'Mon article',
    'content' => 'Contenu...',
    'status'  => 'draft',
]);

// Ou manuellement
$article = new Article();
$article->title   = 'Mon article';
$article->content = 'Contenu...';
$article->save();
```

---

## Modifier un enregistrement

```php
$article = Article::find(42);
$article->title = 'Nouveau titre';
$article->save();
```

---

## Supprimer un enregistrement

```php
// Sur une instance
$article = Article::find(42);
$article->delete();

// Statique par ID
Article::destroy(42);
```

---

## Utilitaires

```php
$article = Article::find(42);

$article->isDirty();       // true si l'objet a été modifié depuis son chargement
$article->getOriginal();   // tableau des valeurs originales chargées depuis la BDD
$article->refresh();       // recharge les données depuis la BDD
$article->toArray();       // convertit en tableau PHP
$article->toJson();        // convertit en JSON
```

---

## Ajouter des méthodes métier

Tu peux enrichir n'importe quel modèle avec tes propres méthodes :

```php
namespace App\Models;

class Article extends ActiveRecord
{
    // Scope personnalisé
    public static function published(): QueryBuilder
    {
        return static::where('status', 'published');
    }

    // Méthode d'instance
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function publish(): self
    {
        $this->status = 'published';
        return $this->save();
    }
}

// Utilisation
$articles = Article::published()->orderBy('created_at', 'DESC')->get();
```

---

## Accès à la base de données brute

Pour les requêtes complexes hors ORM, accède au PDO directement :

```php
namespace App\Models;

class Stats extends ActiveRecord
{
    public static function topAuthors(): array
    {
        $pdo  = static::db();
        $stmt = $pdo->query('SELECT author_id, COUNT(*) as total FROM articles GROUP BY author_id ORDER BY total DESC LIMIT 5');
        return $stmt->fetchAll();
    }
}
```
