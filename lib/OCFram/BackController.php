<?php
namespace OCFram;

abstract class BackController extends ApplicationComponent {
	protected $_action   = '';
	protected $_module   = '';
	protected $_page     = null;
	protected $_view     = '';
	protected $_managers = null;
	
	public function __construct( $app, $module, $action, $format ) {
		parent::__construct( $app );
		$this->_page = new Page( $app, $format );
		$this->setAction( $action );
		$this->setModule( $module );
		$this->setView( $action );
		
		$this->_managers = new Managers( 'PDO', PDOFactory::getMysqlConnexion() );
	}
	
	public function execute() {
		//ucfirst(str) : retourne str avec une maj en premier char
		//nom de la méthode à appeler
		$method = 'execute' . ucfirst( $this->_action );
		//vérifie si une variable peut être appelée comme fonction
		if ( !is_callable( [
			$this,
			$method,
		] )
		) {
			throw new \Exception( 'Erreur dans l\'appel de la méthode' );
		}
		//appel de la méthode avec en parametre la request
		$this->$method( $this->_app->httpRequest() );
	}
	
	public function page() {
		return $this->_page;
	}
	
	public function setModule( $module ) {
		if ( is_string( $module ) ) {
			$this->_module = $module;
		}
	}
	
	public function setAction( $action ) {
		if ( is_string( $action ) ) {
			$this->_action = $action;
		}
	}
	
	public function setView( $view ) {
		if ( !is_string( $view ) || empty( $view ) ) {
			throw new \InvalidArgumentException( 'Erreur : nom de vue invalide' );
		}
		$this->_view = $view;
		
		$this->_page->setContentFile( __DIR__ . '/../../App/' . $this->_app->name() . '/Modules/' . $this->_module . '/Views/' . $this->_view . '.php' );
	}
}

?>