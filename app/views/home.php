<?php $__view->extends('layouts/app') ?>

<?php $__view->section('title') ?>Accueil<?php $__view->endsection() ?>

<?php $__view->section('content') ?>

    <div class="mb-4">
        <h1>Bienvenue 👋</h1>
        <p class="mt-2">Un template MVC PHP fait maison, avec composants et layouts.</p>
    </div>

    <div class="grid grid-3 mb-4">
        <?= $__view->component('card', [
            'title' => '⚡ Rapide',
            'body'  => 'Zéro dépendance front-end, CSS custom léger et performant.',
        ]) ?>

        <?= $__view->component('card', [
            'title' => '🧩 Composants',
            'body'  => 'Système de composants et layouts PHP natif.',
            'slot'  => '<a href="#" class="btn btn-primary btn-sm">En savoir plus</a>',
        ]) ?>

        <?= $__view->component('card', [
            'title' => '📱 Responsive',
            'body'  => 'Grille fluide et navbar mobile intégrées.',
            'slot'  => '<span class="badge badge-accent">CSS only</span>',
        ]) ?>
    </div>

    <hr class="divider">

    <div class="alert alert-info">
        💡 Tu peux ajouter des sections <code>head</code> et <code>scripts</code> par page depuis n'importe quelle vue.
    </div>

<?php $__view->endsection() ?>
