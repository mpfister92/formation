<?php


namespace OCFram;

class FormHandler {
	protected $_form;
	protected $_manager;
	protected $_request;
	
	public function __construct( Form $form, Manager $manager, HTTPRequest $request ) {
		$this->setForm( $form );
		$this->setManager( $manager );
		$this->setRequest( $request );
	}
	
	public function process() {
		if ( $this->_request->method() == 'POST' && $this->_form->isValid() ) {
			$this->_manager->save( $this->_form->entity() );
			
			return true;
		}
		
		return false;
	}
	
	public function setForm( Form $form ) {
		$this->_form = $form;
	}
	
	public function setManager( Manager $manager ) {
		$this->_manager = $manager;
	}
	
	public function setRequest( HTTPRequest $request ) {
		$this->_request = $request;
	}
}