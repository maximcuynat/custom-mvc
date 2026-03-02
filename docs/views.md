# Vues & Composants

[← Sommaire](index.md)

---

## Structure des vues

```
app/views/
├── View.php              ← moteur de templates
├── layouts/              ← mises en page globales
│   └── app.php
├── components/           ← éléments réutilisables
│   └── card.php
├── home.php
└── error.php
```

Chaque vue est un fichier PHP classique. La variable `$__view` est toujours disponible et donne accès à toutes les méthodes du moteur.

---

## Afficher une vue depuis un contrôleur

```php
// Syntaxe
$this->render('chemin/vers/vue', ['clé' => 'valeur']);

// Exemples
$this->render('home');
$this->render('article/index', ['articles' => $articles]);
$this->render('article/show',  compact('article'));
```

Le chemin est relatif à `app/views/`. Les données passées deviennent des variables PHP dans la vue.

---

## Héritage de layout

Un layout est une mise en page réutilisable (navbar, footer, balises HTML...).  
Une vue "hérite" d'un layout et remplit ses **sections**.

### 1. Déclarer le layout

```php
<?php $__view->extends('layouts/app') ?>
```

> Doit être appelé en tout premier dans la vue.

### 2. Remplir les sections

```php
<?php $__view->section('title') ?>Mon titre<?php $__view->endsection() ?>

<?php $__view->section('content') ?>
    <h1>Bonjour</h1>
    <p>Contenu de la page</p>
<?php $__view->endsection() ?>
```

### 3. Afficher les sections dans le layout

Dans `app/views/layouts/app.php` :

```php
<title><?= $__view->yield('title', 'App') ?></title>
...
<main>
    <?= $__view->yield('content') ?>
</main>
```

Le deuxième argument de `yield()` est la valeur par défaut si la section n'est pas définie.

### Sections disponibles dans le layout par défaut

| Section | Description |
|---|---|
| `title` | Titre de l'onglet |
| `content` | Corps principal de la page |
| `head` | CSS / meta supplémentaires |
| `scripts` | JS en fin de page |

```php
<?php $__view->section('head') ?>
    <link rel="stylesheet" href="/public/css/ma-page.css">
<?php $__view->endsection() ?>

<?php $__view->section('scripts') ?>
    <script src="/public/js/ma-page.js"></script>
<?php $__view->endsection() ?>
```

---

## Composants

Un composant est un fragment de vue réutilisable avec ses propres données.  
Les fichiers de composants sont dans `app/views/components/`.

### Créer un composant

```php
{{! app/views/components/alert.php !}}
<div class="alert alert-<?= $__view->e($type ?? 'info') ?>">
    <?= $__view->e($message) ?>
</div>
```

### Utiliser un composant

```php
<?= $__view->component('alert', ['type' => 'success', 'message' => 'Enregistré !']) ?>
<?= $__view->component('alert', ['type' => 'danger',  'message' => 'Une erreur.']) ?>
```

### Composant avec slot (HTML libre)

Le slot permet de passer du HTML arbitraire dans le composant :

```php
{{! app/views/components/card.php !}}
<div class="card">
    <div class="card-title"><?= $__view->e($title) ?></div>
    <?php if (!empty($slot)): ?>
        <div class="card-footer"><?= $slot ?></div>
    <?php endif; ?>
</div>
```

```php
<?= $__view->component('card', [
    'title' => 'Action requise',
    'slot'  => '<a href="/login" class="btn btn-primary">Se connecter</a>',
]) ?>
```

---

## Partials (inclusions simples)

Pour inclure un fragment sans données dynamiques ou avec des données partagées :

```php
<?= $__view->include('partials/nav') ?>
<?= $__view->include('partials/footer', ['year' => date('Y')]) ?>
```

Créer le fichier `app/views/partials/nav.php` :

```php
<nav class="...">
    <a href="/home">Accueil</a>
</nav>
```

---

## Échappement HTML

Toujours utiliser `$__view->e()` pour afficher des données utilisateur :

```php
{{! ✅ Sécurisé !}}
<?= $__view->e($user->username) ?>

{{! ❌ Dangereux si $username vient d'un utilisateur !}}
<?= $username ?>
```

---

## Exemple complet d'une vue

```php
{{! app/views/article/show.php !}}
<?php $__view->extends('layouts/app') ?>

<?php $__view->section('title') ?><?= $__view->e($article->title) ?><?php $__view->endsection() ?>

<?php $__view->section('content') ?>

    <div class="mb-3">
        <h1><?= $__view->e($article->title) ?></h1>
        <p class="text-muted"><?= $__view->e($article->created_at) ?></p>
    </div>

    <div class="card">
        <div class="card-body">
            <?= $__view->e($article->content) ?>
        </div>
    </div>

    <div class="mt-3">
        <a href="/article" class="btn btn-outline">← Retour</a>
    </div>

<?php $__view->endsection() ?>
```
