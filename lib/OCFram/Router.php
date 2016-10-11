<?php
namespace OCFram;

class Router extends ApplicationComponent {
	const NO_ROUTE = 1;
	protected $_routes = [];
	
	public function __construct( Application $app ) {
		parent::__construct( $app );
		$this->chargeRoute( $app->name() );
	}
	
	/** ajoute une route avec la clé unique module/action dans le tableau des routes pour l'application passée
	 * en paramètre
	 * @param Route $Route
	 * @param string $app_name
	 *
	 * @throws \Exception
	 */
	public function addRoute( Route $Route, $app_name ) {
		if ( array_key_exists( $Route->module() . '/' . $Route->action(), $this->_routes[ $app_name ] ) ) {
			throw new \Exception( 'Erreur valeur déjà présente dans le tableau' );
		}
		$this->_routes[ $app_name ][ $Route->module() . '/' . $Route->action() ] = $Route;
	}
	
	public function getRoute( $url, $app_name ) {
		//parcours de l'ensemble des routes du router
		foreach ( $this->_routes[ $app_name ] as $route ) {
			//si on a une correspondance
			if ( $route->match( $url ) ) {
				//on récupère dans $varsValue le retour de la fonction preg_match (le tableau $matches -> la chaine capturée)
				$varsValue = $route->match( $url );
				//si la route a des variables
				if ( $route->hasVars() ) {
					//on récupère dans $varsNames les noms des variables de la route
					$varsNames = $route->varsNames();
					//Création du tableau comportant les noms/variables des variables
					$arrayVars = [];
					//parcours de l'ensemble des variables de la route
					foreach ( $varsValue as $key => $value ) {
						if ( $key !== 0 ) {
							$arrayVars[ $varsNames[ $key - 1 ] ] = $value;
						}
					}
					$route->setVars( $arrayVars );
				}
				
				return $route;
			}
		}
		//si on ne capture aucune chaine on lance une exception
		throw new \RuntimeException( 'Aucune route trouvée' );
	}
	
	/** builds the url according to parameters
	 *
	 * @param        $app_name
	 * @param string $module
	 * @param string $action
	 * @param array  $vars
	 *
	 * @return string $url
	 * @throws \Exception
	 */
	public function provideRoute( $app_name, $module, $action, array $vars ) {
		$this->chargeRoute( $app_name );
		
		if ( !array_key_exists( $module . '/' . $action, $this->_routes[ $app_name ] ) ) {
			throw new \Exception( 'Le couple module/action n\'existe pas dans les routes' );
		}
		$url = $this->_routes[ $app_name ][ $module . '/' . $action ]->rewrite();
		
		if ( null !== $vars ) {
			foreach ( $vars as $key => $value ) {
				$to_replace = "(" . $key . ")";
				/*if ( strpos( $to_replace, $url ) === false ) {
					throw new \InvalidArgumentException( 'La variable n\'existe pas' );
				}*/
				$url = str_replace( $to_replace, $value, $url );
			}
		}
		
		return $url;
	}
	
	/** insere dans la variable routes les routes correspondant à l'application
	 * @param $app_name
	 */
	public function chargeRoute( $app_name ) {
		if ( !array_key_exists( $app_name, $this->_routes ) ) {
			
			$this->_routes[ $app_name ] = [];
			
			$xml = new \DOMDocument();
			$xml->load( __DIR__ . '/../../App/' . $app_name . '/Config/routes.xml' );
			
			$routes = $xml->getElementsByTagName( 'route' );
			
			foreach ( $routes as $route ) {
				$vars = [];
				
				// On regarde si des variables sont présentes dans l'URL.
				if ( $route->hasAttribute( 'vars' ) ) {
					$vars = explode( ',', $route->getAttribute( 'vars' ) );
				}
				
				// On ajoute la route au routeur.
				$this->addRoute( new Route( $route->getAttribute( 'url' ), $route->getAttribute( 'rewrite' ), $route->getAttribute( 'module' ), $route->getAttribute( 'action' ), $vars ), $app_name );
			}
		}
	}
	
	public function routes() {
		return $this->_routes;
	}
}

?>