<?php

namespace OCFram;


class EmailField extends Field {
	
	public function buildWidget() {
		$widget = '';
		
		if ( !empty( $this->_errorMessage ) ) {
			$widget .= $this->_errorMessage . '<br />';
		}
		
		$widget .= '<label>' . $this->label() . '</label><input type = "email" name="' . $this->_name . '"';
		
		
		$widget .= ' />';
		
		return $widget;
	}
}