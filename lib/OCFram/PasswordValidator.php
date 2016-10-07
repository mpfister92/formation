<?php

namespace OCFram;

class PasswordValidator extends Validator {
	private $Field;
	
	public function __construct( $errorMessage, Field $field) {
		parent::__construct( $errorMessage );
		$this->Field = $field;
	}
	
	public function isValid( $value ) {
		return strcmp($value, $this->Field->value());
	}
}