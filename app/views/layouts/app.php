<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $__view->yield('title', 'App') ?></title>
    <link rel="stylesheet" href="/public/css/app.css">
    <?= $__view->yield('head') ?>
</head>
<body>

    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">MVC<span>.</span></a>

            <button class="navbar-toggle" aria-label="Menu" onclick="this.closest('nav').querySelector('.navbar-nav').classList.toggle('open')">
                <span></span><span></span><span></span>
            </button>

            <ul class="navbar-nav">
                <li><a href="/home" class="active">Accueil</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <div class="container">
            <?= $__view->yield('content') ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> — Custom MVC</p>
    </footer>

    <?= $__view->yield('scripts') ?>
</body>
</html>
