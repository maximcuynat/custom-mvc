<?php

class Router
{
    public function routeReq()
    {
        try {
            $url = isset($_GET['url']) ? filter_var($_GET['url'], FILTER_SANITIZE_URL) : 'home';
            $segments = explode('/', trim($url, '/'));

            $controllerName = 'Controller' . ucfirst(strtolower($segments[0]));
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
            $view = new View('Error');
            $view->generate('Erreur', ['errorMsg' => $e->getMessage()]);
        }
    }
}