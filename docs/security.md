# Sécurité

[← Sommaire](index.md)

---

## Protection CSRF

Le Cross-Site Request Forgery (CSRF) est une attaque où un site malveillant soumet un formulaire à ta place.  
La protection consiste à inclure un token secret dans chaque formulaire et à le vérifier côté serveur.

### Fonctionnement

1. Un token aléatoire est généré et stocké en session
2. Il est injecté dans le formulaire HTML (champ caché)
3. À la soumission, le serveur vérifie que le token correspond

### Ajouter le token dans un formulaire

Dans le contrôleur, passe le champ CSRF à la vue :

```php
public function create(array $params = []): void
{
    $this->render('article/create', [
        'csrfField' => $this->csrfField(),
    ]);
}
```

Dans la vue, inclure `$csrfField` dans le formulaire :

```html
<form method="POST" action="/article/store">
    <?= $csrfField ?>

    <div class="form-group">
        <label class="form-label">Titre</label>
        <input type="text" name="title" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Créer</button>
</form>
```

### Vérifier le token à la réception

```php
public function store(array $params = []): void
{
    $this->verifyCsrf(); // lève une erreur si invalide

    // traitement du formulaire...
}
```

> ⚠️ **Toujours appeler `verifyCsrf()` en premier** dans les méthodes qui traitent un `$_POST`.

---

## Classe Csrf — utilisation avancée

La classe `App\Security\Csrf` peut être utilisée directement si besoin :

```php
use App\Security\Csrf;

// Générer / récupérer le token courant
$token = Csrf::generate();

// Valider manuellement un token
if (!Csrf::validate($_POST['_csrf'])) {
    // token invalide
}

// Obtenir le champ HTML
$html = Csrf::field(); // <input type="hidden" name="_csrf" value="...">

// Régénérer un nouveau token (ex : après connexion)
Csrf::regenerate();
```

---

## Sessions

La session est démarrée automatiquement dans `index.php`.  
Elle est utilisée pour stocker le token CSRF et pour la gestion de l'authentification.

### Stocker des données de session

```php
// Connexion d'un utilisateur
$user = User::authenticate($username, $password);
if ($user) {
    $_SESSION['user_id']  = $user->id;
    $_SESSION['username'] = $user->username;
    Csrf::regenerate(); // toujours régénérer après connexion
    $this->redirect('/home');
}
```

### Vérifier une session dans un contrôleur

```php
public function index(array $params = []): void
{
    if (empty($_SESSION['user_id'])) {
        $this->redirect('/login');
    }

    $user = User::findOrFail($_SESSION['user_id']);
    $this->render('dashboard', compact('user'));
}
```

### Déconnexion

```php
public function logout(array $params = []): void
{
    session_destroy();
    $this->redirect('/home');
}
```

---

## Bonnes pratiques

| Pratique | Description |
|---|---|
| Toujours échapper les sorties | Utiliser `$__view->e($var)` dans les vues |
| Toujours vérifier le CSRF | Sur chaque formulaire POST |
| Ne jamais faire confiance à `$_POST` | Valider et filtrer chaque champ |
| Mots de passe | Toujours avec `password_hash()` / `password_verify()` |
| Requêtes SQL | Toujours via l'ORM ou PDO avec paramètres liés |
| En production | Désactiver l'affichage des erreurs PHP (`display_errors = Off`) |
