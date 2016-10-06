<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 06/10/2016
 * Time: 12:00
 */

namespace OCFram;


class UniqueValidator extends Validator {
	
	private $Manager;
	private $function_name_to_check_unicity;
	
	public function __construct( $errorMessage , Manager $Manager, $function_name_to_check_unicity ) {
		parent::__construct( $errorMessage );
		$this->Manager = $Manager;
		$this->function_name_to_check_unicity = $function_name_to_check_unicity;
	}
	
	public function isValid( $value ) {
		$function_name = $this->function_name_to_check_unicity;
		return !$this->Manager->$function_name($value);
	}
}

