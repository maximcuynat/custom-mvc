<?php

class ControllerHome
{
    public function index(array $params = [])
    {
        $view = new View('Home');
        $view->generate('Home', []);
    }
}
