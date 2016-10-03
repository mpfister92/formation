<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 03/10/2016
 * Time: 10:42
 */

namespace OCFram;


class Config extends ApplicationComponent {
    protected $_vars = [];

    public function get($var){
        if(!is_string($var) || empty($var)){
            throw new \InvalidArgumentException('Le type du paramètre doit être une chaine de caractères non vide');
        }

        if(empty($this->_vars)){
            $xml = new \DOMDocument();
            $xml->load(__DIR__.'/../../App/'.$this->_app->name().'/Config/app.xml');

            $elements = $xml->getElementsByTagName('define');

            foreach($elements as $element){
                $this->_vars[$element->getAttribute('var')] = $element->getAttribute('value');
            }
        }

        if(isset($this->_vars[$var])){
            return $this->_vars[$var];
        }
        return null;
    }
}