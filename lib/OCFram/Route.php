<?php
namespace OCFram;

class Route {
	protected $_action;
	protected $_module;
	protected $_url;
	protected $_rewrite;
	protected $_varsNames = [];
	protected $_vars      = [];
	
	public function __construct( $url, $rewrite, $module, $action, $varsName ) {
		$this->setUrl( $url );
		$this->setModule( $module );
		$this->setAction( $action );
		$this->setVarsName( $varsName );
		$this->setRewrite($rewrite);
	}
	
	public function hasVars() {
		return !empty( $this->_varsNames );
	}
	
	public function match( $url ) {
		if ( preg_match( '`^' . $this->_url . '$`', $url, $matches ) ) {
			return $matches;
		}
		
		return false;
	}
	
	public function setAction( $action ) {
		if ( is_string( $action ) ) {
			$this->_action = $action;
		}
	}
	
	public function setModule( $module ) {
		if ( is_string( $module ) ) {
			$this->_module = $module;
		}
	}
	
	public function setUrl( $url ) {
		if ( is_string( $url ) ) {
			$this->_url = $url;
		}
	}
	
	public function setRewrite( $rewrite ) {
		if ( is_string( $rewrite ) ) {
			$this->_rewrite = $rewrite;
		}
	}
	
	public function setVarsName( array $varsName ) {
		$this->_varsNames = $varsName;
	}
	
	public function setVars( array $vars ) {
		$this->_vars = $vars;
	}
	
	public function action() {
		return $this->_action;
	}
	
	public function module() {
		return $this->_module;
	}
	
	public function url() {
		return $this->_url;
	}
	
	public function rewrite() {
		return $this->_rewrite;
	}
	
	public function varsNames() {
		return $this->_varsNames;
	}
	
	public function vars() {
		return $this->_vars;
	}
}

?>