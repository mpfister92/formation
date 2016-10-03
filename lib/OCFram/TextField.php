<?php

namespace OCFram;


class TextField extends Field {
    protected $_cols;
    protected $_rows;

    public function buildWidget(){
        $widget = '';

        if(!empty($this->_errorMessage)){
            $widget .= $this->_errorMessage.'<br />';
        }

        $widget .= '<label>'.$this->label().'</label>';
        $widget .= '<textarea name="'.$this->name().'"';

        if (!empty($this->_rows)){
            $widget .= ' rows="'.$this->_rows.'"';
        }

        if (!empty($this->_cols)){
            $widget .= ' cols="'.$this->_cols.'"';
        }

        $widget .= '>';

        if (!empty($this->_value)){
            $widget .= htmlspecialchars($this->value());
        }

        $widget .= '</textarea>';

        return $widget;
    }

    public function setCols($cols){
        if(is_int($cols) && $cols > 0){
            $this->_cols = $cols;
        }
        else{
            throw new \RuntimeException('Le nombre de colonne doit être un nombre positif');
        }
    }

    public function setRows($rows){
        if(is_int($rows) && $rows > 0){
            $this->_rows = $rows;
        }
        else{
            throw new \RuntimeException('Le nombre de ligne doit être un nombre positif');
        }
    }
}