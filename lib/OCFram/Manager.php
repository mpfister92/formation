<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 30/09/2016
 * Time: 16:36
 */

namespace OCFram;


abstract class Manager {
    protected $_dao;

    public function __construct($dao){
       $this->_dao = $dao;
    }
}
?>