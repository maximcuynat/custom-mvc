<?php
require_once('views/View.php');

class ControllerHome
{
    private $_view;

    public function __construct($url)
    {
        if (is_array($url) && isset($url[0]) && $url[0] === "home" && count($url) === 1)
            $this->home();
        else
            throw new Exception('Page introuvable');
    }

    // Page /home
    private function home()
    {
        $this->_view = new View('Home');
        $this->_view->generate('Home', array());
    }
}
