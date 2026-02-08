<?php

define('URL', str_replace("index.php", "home", (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]"));

require_once('controllers/Router.php');
$router = new Router();
$router->routeReq();
