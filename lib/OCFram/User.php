<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 03/10/2016
 * Time: 10:15
 */

namespace OCFram;

session_start();

class User extends ApplicationComponent {

    public function getAttribute($attr){
        if(isset($_SESSION[$attr])){
            return $_SESSION[$attr];
        }
        return null;
    }

    public function getFlash(){
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    public function hasFlash(){
        return (isset($_SESSION['flash']));
    }

    public function isAuthenticated(){
        if (isset($_SESSION['auth']) && $_SESSION['auth'] === true){
            return true;
        }
        return false;
    }

    public function setAttribute($attr,$value){
        if(empty($attr)){
            throw new \InvalidArgumentException('Attribut manquant');
        }
        $_SESSION[$attr] = $value;
    }

    public function setAuthenticated($authenticated = true){
        if(!is_bool($authenticated)){
            throw new \InvalidArgumentException('Erreur : la valeur doit être un booléen');
        }
        $_SESSION['auth'] = $authenticated;
    }

    public function setFlash($value){
        if(empty($value)){
            throw new \InvalidArgumentException('Erreur : valeur vide');
        }
        $_SESSION['flash'] = $value;
    }
}