<?php

namespace App\Backend\Modules\Connexion;

use \OCFram\BackController;
use \OCFram\HTTPRequest;

class ConnexionController extends BackController {
	public function executeIndex( HTTPRequest $request ) {
		$this->_page->addVar( 'title', 'Connexion' );
		
		if ( $request->postExists( 'login' ) ) {
			$login    = $request->postData( 'login' );
			$password = $request->postData( 'password' );
			if ( $login == $this->_app->config()->get( 'login' ) && $password == $this->_app->config()->get( 'pass' ) ) {
				$this->_app->user()->setAuthenticated( true );
				$this->_app->httpResponse()->redirect( '.' );
			}
			else {
				$this->_app->user()->setFlash( 'Le pseudo ou le mot de passe est incorrect' );
			}
		}
	}
}