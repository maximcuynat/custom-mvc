<?php $__view->extends('layouts/app') ?>

<?php $__view->section('title') ?>Erreur<?php $__view->endsection() ?>

<?php $__view->section('content') ?>
    <div style="text-align: center; padding: 50px;">
        <h1>Une erreur est survenue</h1>
        <p><?= $__view->e($errorMsg) ?></p>
        <a href="/" style="color: #3498db;">Retour à l'accueil</a>
    </div>
<?php $__view->endsection() ?>
