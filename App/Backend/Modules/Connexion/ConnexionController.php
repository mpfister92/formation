<?php

namespace App\Backend\Modules\Connexion;

use \OCFram\BackController;
use \OCFram\HTTPRequest;

class ConnexionController extends BackController {
	/** gestion de la connexion et setting de la session (admin/member)
	 * @param HTTPRequest $request
	 */
	public function executeIndex( HTTPRequest $request ) {
		$this->_page->addVar( 'title', 'Connexion' );
		
		if ( $request->postExists( 'login' ) ) {
			$login    = $request->postData( 'login' );
			$password = $request->postData( 'password' );
			if ( $login == $this->_app->config()->get( 'login' ) && $password == $this->_app->config()->get( 'pass' ) ) {
				$this->_app->user()->setAuthenticated( true );
				$this->_app->user()->setLogin($login);
				$this->_app->user()->setStatus('admin');
				$this->_app->httpResponse()->redirect( '/' );
			}
			else {
				if ( $this->_managers->getManagerOf( 'Members' )->getMember( $login, $password ) == 1 ) {
					$this->_app->user()->setAuthenticated( true );
					$this->_app->user()->setLogin($login);
					$this->_app->user()->setStatus('member');
					$this->_app->httpResponse()->redirect( '/' );
				}
				else {
					$this->_app->user()->setFlash( 'Le pseudo ou le mot de passe est incorrect' );
				}
			}
		}
	}
	
	/** deconnexion du visiteur et suppression des paramètres de session
	 * @param HTTPRequest $request
	 */
	public function executeDeconnexion(HTTPRequest $request){
		$this->_page->addVar('title','Deconnexion');
		
		$this->_app->user()->setAuthenticated(false);
		$this->_app->user()->unssetSession();
		$this->_app->httpResponse()->redirect('/');
	}
}