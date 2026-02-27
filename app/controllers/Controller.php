<?php

abstract class Controller
{
    protected function render(string $viewName, string $title, array $data = []): void
    {
        $view = new View($viewName);
        $view->generate($title, $data);
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
}
