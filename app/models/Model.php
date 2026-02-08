<?php

abstract class Model
{
    private static $bdd;

    private static function setBdd()
    {
        self::$bdd = new PDO('mysql:host=localhost;dbname=internships;charset=utf8', 'root', '');
        self::$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    protected function getBdd()
    {
        if (self::$bdd == null)
            self::setBdd();
        return self::$bdd;
    }
}
