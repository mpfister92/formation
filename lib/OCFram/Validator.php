<?php

namespace OCFram;


abstract class Validator
{
    protected $_errorMessage;

    public function __construct($errorMessage)
    {
        $this->setErrorMessage($errorMessage);
    }

    abstract public function isValid($value);

    public function errorMessage()
    {
        return $this->_errorMessage;
    }

    public function setErrorMessage($errorMessage)
    {
        if (is_string($errorMessage)) {
            $this->_errorMessage = $errorMessage;
        }
    }
}