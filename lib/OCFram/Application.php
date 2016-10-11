<?php
namespace OCFram;


abstract class Application {
	protected $_httpRequest;
	protected $_httpResponse;
	protected $_name;
	protected $_user;
	protected $_config;
	protected $_router;
	
	public function __construct() {
		$this->_httpRequest  = new HTTPRequest( $this );
		$this->_httpResponse = new HTTPResponse( $this );
		$this->_name         = '';
		$this->_user         = new User( $this );
		$this->_config       = new Config( $this );
		$this->_router 		 = null;
	}
	
	public function getController() {
		
		try {
			// On récupère la route correspondante à l'URL.
			$matchedRoute = $this->router()->getRoute( $this->_httpRequest->requestURI(),$this->_name );
		}
		catch ( \RuntimeException $e ) {
			// Si aucune route ne correspond, c'est que la page demandée n'existe pas.
			$this->_httpResponse->redirect404();
		}
		
		
		// On ajoute les variables de l'URL au tableau $_GET.
		$_GET = array_merge( $_GET, $matchedRoute->vars() );
		
		// On instancie le contrôleur.
		$controllerClass = 'App\\' . $this->_name . '\\Modules\\' . $matchedRoute->module() . '\\' . $matchedRoute->module() . 'Controller';
		
		return new $controllerClass( $this, $matchedRoute->module(), $matchedRoute->action() );
	}
	
	abstract public function run();
	
	public function httpRequest() {
		return $this->_httpRequest;
	}
	
	public function httpResponse() {
		return $this->_httpResponse;
	}
	
	public function name() {
		return $this->_name;
	}
	
	public function user() {
		return $this->_user;
	}
	
	public function config() {
		return $this->_config;
	}
	
	public function router(){
		if ( null === $this->_router )
			$this->_router = new Router( $this );
		return $this->_router;
	}
}

?>