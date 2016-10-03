<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 30/09/2016
 * Time: 16:37
 */

namespace OCFram;


abstract class Entity implements \ArrayAccess {
    protected $_errors = [];
    protected $_id;

    public function __construct(array $data = []){
        if(!empty($data)){
            $this->hydrate($data);
        }
    }

    public function hydrate(array $data) {
        foreach($data as $attribute => $value){
            $method = 'set'.ucfirst($attribute);
            if (is_callable([$this,$method])){
                $this->$method($value);
            }
        }
    }

    public function errors() {
        return $this->_errors;
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

    public function offsetExists($var){
        return isset($this->$var) && is_callable([$this,$var]);
    }

    public function offsetGet($var){
        if (isset($this->$var) && is_callable([$this,$var])){
            return $this->$var();
        }
    }

    public function offsetSet($key,$value){
        $method = 'set'.ucfirst($key);
        if(isset($this->$key) && is_callable([$this,$key])){
            $this->$method($value);
        }
    }

    public function offsetUnset($var){
        throw new \Exception('Impossible de supprimer une valeur');
    }
}
?>