<?php


namespace OCFram;


class ConfirmationValidator extends Validator {
	private $Field;
	
	public function __construct( $errorMessage, Field $field ) {
		parent::__construct( $errorMessage );
		$this->setField( $field );
	}
	
	public function isValid( $value ) {
		return !strcmp( $value, $this->Field->value() );
	}
	
	public function Field() {
		return $this->Field;
	}
	
	public function setField( $field ) {
		$this->Field = $field;
	}
}
