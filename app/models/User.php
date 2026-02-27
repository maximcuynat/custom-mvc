<?php

require_once('ActiveRecord.php');

class User extends ActiveRecord
{
    protected static $table = 'users';

    public function setPassword($password)
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }

    public function verifyPassword($password)
    {
        return password_verify($password, $this->attributes['password']);
    }

    public static function findByUsername($username)
    {
        return static::where('username', '=', $username)->first();
    }

    public static function authenticate($username, $password)
    {
        $user = static::findByUsername($username);
        
        if ($user && $user->verifyPassword($password)) {
            return $user;
        }
        
        return null;
    }
}