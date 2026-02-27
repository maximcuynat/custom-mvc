<?php

define('URL', str_replace("index.php", "home", (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]"));

spl_autoload_register(function ($class) {
    $dirs = ['controllers/', 'models/', 'views/'];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

$router = new Router();
$router->routeReq();
