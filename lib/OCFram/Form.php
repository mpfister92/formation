<?php

namespace OCFram;


class Form {
	protected $_entity;
	protected $_fields = [];
	
	public function __construct( Entity $entity ) {
		$this->setEntity( $entity );
	}
	
	public function add( Field $field ) {
		$attr = $field->name();
		$field->setValue( $this->_entity->$attr() );
		$this->_fields[] = $field;
		
		return $this;
	}
	
	public function createView() {
		$view = '';
		
		foreach ( $this->_fields as $field ) {
			$view .= $field->buildWidget() . '<br />';
		}
		
		return $view;
	}
	
	public function isValid() {
		$valid = true;
		
		foreach ( $this->_fields as $field ) {
			if ( $field->isValid() == false ) {
				$valid = false;
			}
		}
		
		return $valid;
	}
	
	public function entity() {
		return $this->_entity;
	}
	
	public function setEntity( $entity ) {
		$this->_entity = $entity;
	}
}