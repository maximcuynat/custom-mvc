<?php

namespace App\Controllers;

class ControllerHome extends Controller
{
    public function index(array $params = []): void
    {
        $this->render('home');
    }
}
