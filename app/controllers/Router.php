<?php

namespace App\Controllers;

use App\Views\View;

class Router
{
    public function routeReq()
    {
        try {
            $url = isset($_GET['url']) ? $_GET['url'] : 'home';

            if (!preg_match('/^[a-zA-Z0-9\/\-_]+$/', $url)) {
                throw new \Exception('Page introuvable');
            }

            $segments = explode('/', trim($url, '/'));

            $controllerName = 'App\\Controllers\\Controller' . ucfirst(strtolower($segments[0]));
            $method = (isset($segments[1]) && $segments[1] !== '') ? strtolower($segments[1]) : 'index';
            $params = array_slice($segments, 2);

            if (!class_exists($controllerName)) {
                throw new \Exception('Page introuvable');
            }

            $controller = new $controllerName();

            if (!method_exists($controller, $method)) {
                throw new \Exception('Page introuvable');
            }

            $controller->$method($params);
        } catch (\Exception $e) {
            $view = new View();
            $view->render('error', ['errorMsg' => $e->getMessage()]);
        }
    }
}