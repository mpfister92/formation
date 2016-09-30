<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 30/09/2016
 * Time: 16:33
 */

namespace OCFram;

class PDOFactory {
    public static function getMysqlConnexion(){
        $db = new \PDO('mysql:host=localhost;dbname=news','root','');
        $db->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);

        return $db;
    }
}

?>