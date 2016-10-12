<?php

namespace OCFram;


class Page extends ApplicationComponent {
	protected $_contentFile;
	protected $_vars = [];
	protected $_format;
	
	public function __construct( Application $app, $format ) {
		parent::__construct( $app );
		$this->_format = $format;
	}
	
	public function addVar( $var, $value ) {
		if ( !is_string( $var ) || is_numeric( $var ) || empty( $var ) ) {
			throw new \InvalidArgumentException( 'Nom de variable incorrect' );
		}
		$this->_vars[ $var ] = $value;
	}
	
	public function getGeneratedPage() {
		var_dump($this->_contentFile);
		if ( !file_exists( $this->_contentFile ) ) {
			throw new \RuntimeException( 'Erreur : la vue n\'existe pas' );
		}
		
		$user = $this->_app->user();
		
		//créé des variables à partir d'un tableau associatif
		extract( $this->_vars );
		
		if($this->_format == 'json'){
			json_encode($this->_vars);
		}
		
		//enclanche la temporisation de sortie
		ob_start();
		//require = include mais emet une erreur fatale
		require $this->_contentFile;
		//lit le contenu du tampon puis l'efface
		$content = ob_get_clean();
		
		ob_start();
		require __DIR__ . '/../../App/' . $this->_app->name() . '/Templates/layout.php';
		
		return ob_get_clean();
	}
	
	public function setContentFile( $contentFile ) {
		if ( !is_string( $contentFile ) || empty( $contentFile ) ) {
			throw new \InvalidArgumentException( 'Erreur, vue invalide' );
		}
		$this->_contentFile = $contentFile;
	}
}