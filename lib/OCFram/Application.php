<?php
namespace OCFram;



abstract class Application {
	protected $_httpRequest;
	protected $_httpResponse;
	protected $_name;
	protected $_user;
	protected $_config;

	public function __construct() {
		$this->_httpRequest = new HTTPRequest($this->_httpRequest->app());
		$this->_httpResponse = new HTTPResponse($this->_httpRequest->app());
		$this->_name = '';
		$this->_user = new User($this->user()->app());
		$this->_config = new Config($this->config()->app());
	}

	public function getController() {
	    $router = new Router;

	    $xml = new \DOMDocument;
	    $xml->load(__DIR__.'/../../App/'.$this->_name.'/Config/routes.xml');

	    $routes = $xml->getElementsByTagName('route');

	    // On parcourt les routes du fichier XML.
	    foreach ($routes as $route) {
	    	$vars = [];

	      	// On regarde si des variables sont présentes dans l'URL.
	      	if ($route->hasAttribute('vars')) {
	        	$vars = explode(',', $route->getAttribute('vars'));
	      	}

	      	// On ajoute la route au routeur.
	      	$router->addRoute(new Route($route->getAttribute('url'), $route->getAttribute('module'), $route->getAttribute('action'), $vars));
	    }

	    try {
	      	// On récupère la route correspondante à l'URL.
	      	$matchedRoute = $router->getRoute($this->_httpRequest->requestURI());
	    }
	    catch (\RuntimeException $e) {
		    if ($e->getCode() == Router::NO_ROUTE) {
	        	// Si aucune route ne correspond, c'est que la page demandée n'existe pas.
	        	$this->_httpResponse->redirect404();
	      	}
	    }

	    // On ajoute les variables de l'URL au tableau $_GET.
	    $_GET = array_merge($_GET, $matchedRoute->vars());

	    // On instancie le contrôleur.
	    $controllerClass = 'App\\'.$this->_name.'\\Modules\\'.$matchedRoute->module().'\\'.$matchedRoute->module().'Controller';
	    return new $controllerClass($this, $matchedRoute->module(), $matchedRoute->action());
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

	public function user(){
		return $this->_user;
	}

	public function config(){
		return $this->_config;
	}
}
?> 