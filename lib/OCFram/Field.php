<?php

namespace OCFram;


abstract class Field
{
    use Hydrator;

    protected $_errorMessage;
    protected $_label;
    protected $_name;
    protected $_value;
    protected $_validators = [];

    public function __construct(array $options = [])
    {
        if (!empty($options)) {
            $this->hydrate($options);
        }
    }

    abstract public function buildWidget();

    public function isValid()
    {
        foreach ($this->_validators as $validator) {
            if (!$validator->isValid($this->_value)) {
                $this->_errorMessage = $validator->errorMessage();
                return false;
            }
        }
        return true;
    }

    public function label()
    {
        return $this->_label;
    }

    public function name()
    {
        return $this->_name;
    }

    public function value()
    {
        return $this->_value;
    }

    public function validators()
    {
        return $this->_validators;
    }

    public function setLabel($label)
    {
        if (is_string($label)) {
            $this->_label = $label;
        }
    }

    public function setName($name)
    {
        if (is_string($name)) {
            $this->_name = $name;
        }
    }

    public function setValue($value)
    {
        if (is_string($value)) {
            $this->_value = $value;
        }
    }

    public function setValidators(array $validators)
    {
        foreach ($validators as $validator) {
            if ($validator instanceof Validator && !in_array($validator, $this->_validators)) {
                $this->_validators[] = $validator;
            }
        }
    }
}