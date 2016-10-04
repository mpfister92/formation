<?php

namespace OCFram;


abstract class Entity implements \ArrayAccess
{
    use Hydrator;

    protected $errors = [],
        $id;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    public function errors()
    {
        return $this->errors;
    }

    public function id()
    {
        return $this->id;
    }

    public function setId($id)
    {
		(int) $id;
		$this->id = (int) $id;
    }

    public function isNew()
    {
        return empty($this->id);
    }

    public function offsetExists($var)
    {
        return isset($this->$var) && is_callable([$this, $var]);
    }

    public function offsetGet($var)
    {
        if (isset($this->$var) && is_callable([$this, $var])) {
            return $this->$var();
        }
    }

    public function offsetSet($key, $value)
    {
        $method = 'set' . ucfirst($key);
        if (isset($this->$key) && is_callable([$this, $method])) {
            $this->$method($value);
        }
    }

    public function offsetUnset($var)
    {
        throw new \Exception('Impossible de supprimer une valeur');
    }
}

?>