<?php

namespace OCFram;

class EmailValidator extends Validator {
	public function isValid( $value ) {
		return (filter_var($value,FILTER_VALIDATE_EMAIL));
	}
}