# Contrôleurs

[← Sommaire](index.md)

---

## Créer un contrôleur

Chaque contrôleur doit :
- se trouver dans `app/controllers/`
- avoir le namespace `App\Controllers`
- être nommé `Controller` + nom de la ressource (PascalCase)
- étendre `Controller`

```php
// app/controllers/ControllerArticle.php
namespace App\Controllers;

use App\Models\Article;

class ControllerArticle extends Controller
{
    public function index(array $params = []): void
    {
        $articles = Article::all();
        $this->render('article/index', compact('articles'));
    }

    public function show(array $params = []): void
    {
        $article = Article::findOrFail($params[0] ?? null);
        $this->render('article/show', compact('article'));
    }

    public function store(array $params = []): void
    {
        $this->verifyCsrf();

        Article::create([
            'title'   => $_POST['title'],
            'content' => $_POST['content'],
        ]);

        $this->redirect('/article');
    }
}
```

---

## Méthodes disponibles

Toutes ces méthodes sont héritées de `Controller` et disponibles dans chaque contrôleur.

### `render(string $view, array $data = [])`

Affiche une vue en lui passant des données.

```php
$this->render('article/index', [
    'articles' => Article::all(),
    'title'    => 'Tous les articles',
]);
```

Les clés du tableau `$data` deviennent des variables accessibles dans la vue.

### `redirect(string $url)`

Redirige vers une URL et stoppe l'exécution.

```php
$this->redirect('/home');
$this->redirect('/article/' . $id);
```

### `json(mixed $data, int $status = 200)`

Retourne une réponse JSON (utile pour les endpoints API).

```php
$this->json(['success' => true, 'user' => $user->toArray()]);
$this->json(['error' => 'Non autorisé'], 401);
```

### `abort(string $message = 'Page introuvable')`

Lève une exception qui affiche la page d'erreur.

```php
$this->abort();
$this->abort('Article introuvable');
```

### `csrfField()`

Retourne le champ HTML hidden du token CSRF à injecter dans un formulaire.

```php
$this->render('article/create', [
    'csrfField' => $this->csrfField(),
]);
```

### `verifyCsrf()`

Vérifie que `$_POST['_csrf']` est valide. Lève une erreur sinon.  
À appeler en début de toute méthode qui traite un formulaire POST.

```php
public function store(array $params = []): void
{
    $this->verifyCsrf();
    // traitement...
}
```

---

## Exemple complet : CRUD Article

```php
namespace App\Controllers;

use App\Models\Article;

class ControllerArticle extends Controller
{
    // GET /article
    public function index(array $params = []): void
    {
        $this->render('article/index', [
            'articles' => Article::all(),
        ]);
    }

    // GET /article/show/42
    public function show(array $params = []): void
    {
        $article = Article::findOrFail($params[0] ?? null);
        $this->render('article/show', compact('article'));
    }

    // GET /article/create
    public function create(array $params = []): void
    {
        $this->render('article/create', [
            'csrfField' => $this->csrfField(),
        ]);
    }

    // POST /article/store
    public function store(array $params = []): void
    {
        $this->verifyCsrf();

        $article = Article::create([
            'title'   => htmlspecialchars($_POST['title']),
            'content' => htmlspecialchars($_POST['content']),
        ]);

        $this->redirect('/article/show/' . $article->id);
    }

    // POST /article/delete/42
    public function delete(array $params = []): void
    {
        $this->verifyCsrf();
        Article::destroy($params[0] ?? null);
        $this->redirect('/article');
    }
}
```
