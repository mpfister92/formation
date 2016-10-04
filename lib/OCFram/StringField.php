<?php

namespace OCFram;


class StringField extends Field
{
    protected $_maxLength;

    public function buildWidget()
    {
        $widget = '';

        if (!empty($this->_errorMessage)) {
            $widget .= $this->_errorMessage . '<br />';
        }

        $widget .= '<label>' . $this->label() . '</label><input type = "text" name="' . $this->_name . '"';

        if (!empty($this->_value)) {
            $widget .= ' value="' . htmlspecialchars($this->value()) . '"';
        }

        if (!empty($this->_maxLength)) {
            $widget .= ' maxlength="' . $this->_maxLength . '"';
        }

        $widget .= ' />';

        return $widget;
    }

    public function setMaxLength($maxLength)
    {
        if (is_int($maxLength) && $maxLength > 0) {
            $this->_maxLength = $maxLength;
        } else {
            throw new \RuntimeException('La longueur maximale doit être un nombre positif');
        }
    }
}