<?php

session_start();

define('URL', str_replace("index.php", "home", (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]"));

require_once __DIR__ . '/../vendor/autoload.php';

$router = new \App\Controllers\Router();
$router->routeReq();
