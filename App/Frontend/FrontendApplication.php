<?php

namespace App\Frontend;

use \OCFram\Application;

class FrontendApplication extends Application {
	public function __construct() {
		parent::__construct();
		$this->_name = 'Frontend';
	}
	
	public function run() {
		$controller = $this->getController();
		$controller->execute();
		
		$this->_httpResponse->setPage( $controller->page() );
		$this->_httpResponse->send();
	}
}