<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 30/09/2016
 * Time: 16:23
 */
namespace OCFram;

class Managers
{
    protected $_api = null;
    protected $_dao = null;
    protected $_managers = [];

    public function __construct($api, $dao)
    {
        $this->_api = $api;
        $this->_dao = $dao;
    }

    public function getManagerOf($module)
    {
        if (!is_string($module) || empty($module)) {
            throw new \InvalidArgumentException('Erreur dans le paramètre');
        }
        //si le manager n'existe pas on le crée
        if (!isset($this->_managers[$module])) {
            $manager = '\\Model\\' . $module . 'Manager' . $this->_api;
            $this->_managers[$module] = new $manager($this->_dao);
        }
        return $this->_managers[$module];
    }
}

?>