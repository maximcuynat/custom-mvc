<?php

class ControllerHome extends Controller
{
    public function index(array $params = []): void
    {
        $this->render('Home', 'Home');
    }
}
