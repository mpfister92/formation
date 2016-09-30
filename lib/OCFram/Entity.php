<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 30/09/2016
 * Time: 16:37
 */

namespace OCFram;


abstract class Entity implements \ArrayAccess {
    protected $_erreurs = [];
    protected $_id;

    public function __construct(array $donnees = []){
        if(!empty($donnees)){
            $this->hydrate($donnees);
        }
    }

    public function hydrate(array $donnees) {
        foreach($donnees as $attribut => $valeur){
            $methode = 'set'.ucfirst($attribut);
            if (is_callable([$this,$methode])){
                $this->$methode($valeur);
            }
        }
    }

    public function erreurs() {
        return $this->_erreurs;
    }

    public function id() {
        return $this->_id;
    }

    public function setId($id){
        if(is_int($id)) {
            $this->_id = $id;
        }
    }

    public function isNew() {
        return empty($this->_id);
    }

    public function offsetExists($valeur){
        return isset($this->$valeur) && is_callable([$this,$valeur]);
    }

    public function offsetGet($valeur){
        if (isset($this->$valeur) && is_callable([$this,$valeur])){
            return $this->$valeur();
        }
    }

    public function offsetSet($key,$value){
        $method = 'set'.ucfirst($key);
        if(isset($this->$key) && is_callable([$this,$key])){
            $this->$method($value);
        }
    }

    public function offsetUnset($valeur){
        throw new Exception('Impossible de supprimer une valeur');
    }
}
?>