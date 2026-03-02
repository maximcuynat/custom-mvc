# Frontend CSS

[← Sommaire](index.md)

Feuille de style globale dans `app/public/css/app.css`.  
Incluse automatiquement via le layout `layouts/app.php`.

---

## Variables CSS

Toutes les couleurs, rayons et transitions sont des variables CSS modifiables dans `:root` :

```css
--color-bg         /* fond de page */
--color-surface    /* cartes, navbar */
--color-surface-2  /* inputs, surface secondaire */
--color-border     /* bordures */
--color-primary    /* violet — actions principales */
--color-accent     /* vert — succès, highlights */
--color-danger     /* rouge — erreurs */
--color-text       /* texte principal */
--color-muted      /* texte secondaire */
```

Pour changer le thème, il suffit de modifier ces variables.

---

## Grille

```html
<!-- Grille auto-responsive (min 280px par colonne) -->
<div class="grid">...</div>

<!-- 2 colonnes fixes -->
<div class="grid grid-2">...</div>

<!-- 3 colonnes fixes -->
<div class="grid grid-3">...</div>
```

> Sur mobile (< 768px), `grid-2` et `grid-3` passent automatiquement en 1 colonne.

---

## Cards

```html
<!-- Card simple -->
<div class="card">
    <div class="card-title">Titre</div>
    <div class="card-body">Contenu texte.</div>
</div>

<!-- Card avec footer -->
<div class="card">
    <div class="card-title">Titre</div>
    <div class="card-body">Contenu.</div>
    <div class="card-footer">
        <a href="#" class="btn btn-primary btn-sm">Action</a>
    </div>
</div>

<!-- Card avec effet hover -->
<div class="card card-hover">...</div>
```

Ou via le composant PHP :

```php
<?= $__view->component('card', [
    'title' => 'Titre',
    'body'  => 'Description.',
    'slot'  => '<a href="#" class="btn btn-primary btn-sm">Action</a>',
]) ?>
```

---

## Boutons

```html
<a href="#" class="btn btn-primary">Principal</a>
<a href="#" class="btn btn-accent">Accent</a>
<a href="#" class="btn btn-outline">Contour</a>
<a href="#" class="btn btn-danger">Danger</a>

<!-- Tailles -->
<a href="#" class="btn btn-primary btn-sm">Petit</a>
<a href="#" class="btn btn-primary btn-lg">Grand</a>
```

---

## Formulaires

```html
<div class="form-group">
    <label class="form-label">Email</label>
    <input type="email" class="form-control" placeholder="vous@exemple.com">
</div>

<div class="form-group">
    <label class="form-label">Message</label>
    <textarea class="form-control" rows="4"></textarea>
</div>

<button type="submit" class="btn btn-primary w-full">Envoyer</button>
```

---

## Alertes

```html
<div class="alert alert-info">Information importante.</div>
<div class="alert alert-success">Opération réussie !</div>
<div class="alert alert-danger">Une erreur est survenue.</div>
```

---

## Badges

```html
<span class="badge badge-primary">Nouveau</span>
<span class="badge badge-accent">Actif</span>
<span class="badge badge-danger">Erreur</span>
```

---

## Utilitaires

### Marges

```html
<div class="mt-1">  <!-- margin-top: .25rem -->
<div class="mt-2">  <!-- margin-top: .50rem -->
<div class="mt-3">  <!-- margin-top: 1rem   -->
<div class="mt-4">  <!-- margin-top: 1.5rem -->

<!-- Même chose avec mb- pour margin-bottom -->
```

### Flexbox

```html
<div class="flex">...</div>
<div class="flex-center">...</div>    <!-- centré horizontal + vertical -->
<div class="flex-between">...</div>  <!-- space-between -->
<div class="flex gap-2">...</div>    <!-- gap: 1rem -->
```

### Texte

```html
<p class="text-center">Centré</p>
<p class="text-muted">Discret</p>
<p class="text-accent">Mis en valeur</p>
```

### Divers

```html
<hr class="divider">           <!-- séparateur -->
<div class="w-full">...</div>  <!-- width: 100% -->
```

---

## Navbar — ajouter des liens

Dans `app/views/layouts/app.php`, ajouter des `<li>` dans la `navbar-nav` :

```html
<ul class="navbar-nav">
    <li><a href="/home" class="active">Accueil</a></li>
    <li><a href="/article">Articles</a></li>
    <li><a href="/about">À propos</a></li>
</ul>
```

La classe `active` met le lien en blanc. Le menu devient automatiquement un hamburger sur mobile.

---

## Exemple de page complète

```php
<?php $__view->extends('layouts/app') ?>

<?php $__view->section('title') ?>Articles<?php $__view->endsection() ?>

<?php $__view->section('content') ?>

<div class="flex-between mb-3">
    <h1>Articles</h1>
    <a href="/article/create" class="btn btn-primary">+ Nouvel article</a>
</div>

<?php if (empty($articles)): ?>
    <div class="alert alert-info">Aucun article pour le moment.</div>
<?php else: ?>
    <div class="grid grid-3">
        <?php foreach ($articles as $article): ?>
            <?= $__view->component('card', [
                'title' => $article->title,
                'body'  => $article->excerpt,
                'slot'  => '<a href="/article/show/' . $article->id . '" class="btn btn-outline btn-sm">Lire</a>',
            ]) ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php $__view->endsection() ?>
```
