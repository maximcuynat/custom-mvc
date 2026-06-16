<?php

namespace App\Controllers;

use App\Views\View;
use App\Security\Csrf;

abstract class Controller
{
    protected function render(string $viewName, array $data = []): void
    {
        $view = new View();
        $view->render($viewName, $data);
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function abort(string $message = 'Page introuvable'): void
    {
        throw new \Exception($message);
    }

    protected function csrfField(): string
    {
        return Csrf::field();
    }

    protected function verifyCsrf(): void
    {
        $token = $_POST['_csrf'] ?? '';
        if (!Csrf::validate($token)) {
            $this->abort('Token CSRF invalide');
        }
        Csrf::regenerate();
    }
}
