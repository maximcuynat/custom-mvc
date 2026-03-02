# Routing

[← Sommaire](index.md)

---

## Fonctionnement général

Toutes les requêtes HTTP arrivent dans `app/index.php` via la réécriture `.htaccess`.  
Le `Router` analyse l'URL et instancie le bon contrôleur avec la bonne méthode.

```
GET /article/show/42
      │       │    └─ params : ['42']
      │       └─── méthode  : show()
      └────────── contrôleur : ControllerArticle
```

## Convention de nommage

| URL | Contrôleur | Méthode |
|---|---|---|
| `/home` | `ControllerHome` | `index()` |
| `/article` | `ControllerArticle` | `index()` |
| `/article/show` | `ControllerArticle` | `show()` |
| `/article/show/42` | `ControllerArticle` | `show(['42'])` |
| `/user/edit/5` | `ControllerUser` | `edit(['5'])` |

> Si aucune méthode n'est précisée dans l'URL, `index()` est appelée par défaut.

## Créer une nouvelle route

Il suffit de créer un fichier contrôleur — aucun fichier de routes à modifier.

```
URL /blog  →  app/controllers/ControllerBlog.php
```

```php
// app/controllers/ControllerBlog.php
namespace App\Controllers;

class ControllerBlog extends Controller
{
    public function index(array $params = []): void
    {
        $this->render('blog/index', ['posts' => []]);
    }
}
```

La route `/blog` est automatiquement disponible.

## Paramètres d'URL

Les segments après la méthode sont passés en tableau `$params` :

```
/article/show/42/preview
                │   └── $params[1] = 'preview'
                └─────── $params[0] = '42'
```

```php
public function show(array $params = []): void
{
    $id      = $params[0] ?? null;
    $mode    = $params[1] ?? 'default';

    $article = Article::findOrFail($id);
    $this->render('article/show', compact('article', 'mode'));
}
```

## Gestion des erreurs

Si le contrôleur ou la méthode n'existe pas, le Router affiche automatiquement la vue `error` avec le message `Page introuvable`.

Pour lever manuellement une erreur depuis un contrôleur :

```php
$this->abort('Article introuvable');
// ou
$this->abort(); // message par défaut
```

## Validation de l'URL

L'URL est validée par une regex avant d'être traitée.  
Seuls les caractères `a-z A-Z 0-9 / - _` sont autorisés.  
Toute URL invalide retourne immédiatement une erreur 404.
