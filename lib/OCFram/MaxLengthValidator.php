<?php

namespace OCFram;


class MaxLengthValidator extends Validator
{
    protected $_maxLength;

    public function __construct($errorMessage, $maxLength)
    {
        parent::__construct($errorMessage);
        $this->setMaxLength($maxLength);
    }

    public function isValid($value)
    {
        return strlen($value) <= $this->_maxLength;
    }

    public function setMaxLength($maxLength)
    {
        if (is_int($maxLength) && $maxLength > 0) {
            $this->_maxLength = $maxLength;
        } else {
            throw new \RuntimeException('La longueur doit être un nombre positif');
        }
    }
}