<?php

namespace App\Views;

class View
{
    private string $viewsDir;

    // Layout inheritance
    private ?string $layout = null;
    private array $sections = [];
    private ?string $currentSection = null;

    public function __construct()
    {
        $this->viewsDir = __DIR__;
    }

    // ─── Rendu principal ────────────────────────────────────────────────────

    public function render(string $template, array $data = []): void
    {
        echo $this->make($template, $data);
    }

    public function make(string $template, array $data = []): string
    {
        $file = $this->resolve($template);

        // Rendu du template dans un contexte isolé
        $content = $this->evaluate($file, $data);

        // Si le template déclare un layout, on l'enveloppe
        if ($this->layout !== null) {
            $layout = $this->layout;
            $this->layout = null;
            $content = $this->make($layout, $data);
        }

        return $content;
    }

    // ─── Héritage de layout ─────────────────────────────────────────────────

    /**
     * À appeler en haut d'un template pour hériter d'un layout.
     * Ex : <?php $this->extends('layouts/app') ?>
     */
    public function extends(string $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * Démarre une section nommée.
     * Ex : <?php $this->section('content') ?>
     */
    public function section(string $name): void
    {
        $this->currentSection = $name;
        ob_start();
    }

    /**
     * Termine la section courante.
     * Ex : <?php $this->endsection() ?>
     */
    public function endsection(): void
    {
        if ($this->currentSection === null) {
            throw new \LogicException('endsection() appelé sans section() ouverte');
        }
        $this->sections[$this->currentSection] = ob_get_clean();
        $this->currentSection = null;
    }

    /**
     * Affiche le contenu d'une section dans le layout.
     * Ex : <?= $this->yield('content') ?>
     */
    public function yield(string $name, string $default = ''): string
    {
        return $this->sections[$name] ?? $default;
    }

    // ─── Composants ─────────────────────────────────────────────────────────

    /**
     * Inclut un composant avec ses données.
     * Ex : <?= $this->component('card', ['title' => 'Hello']) ?>
     */
    public function component(string $name, array $data = []): string
    {
        $file = $this->resolve('components/' . $name);
        return $this->evaluate($file, $data);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    /**
     * Échappe une valeur pour l'affichage HTML.
     * Ex : <?= $this->e($userInput) ?>
     */
    public function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Inclut un sous-template (partial).
     * Ex : <?= $this->include('partials/nav') ?>
     */
    public function include(string $template, array $data = []): string
    {
        return $this->make($template, $data);
    }

    // ─── Noyau ──────────────────────────────────────────────────────────────

    private function resolve(string $template): string
    {
        $file = $this->viewsDir . '/' . ltrim($template, '/') . '.php';
        if (!file_exists($file)) {
            throw new \Exception("Vue introuvable : {$file}");
        }
        return $file;
    }

    private function evaluate(string $file, array $data): string
    {
        $__view = $this;
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return ob_get_clean();
    }
}
