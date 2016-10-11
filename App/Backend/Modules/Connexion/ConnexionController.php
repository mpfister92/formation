<?php

namespace App\Backend\Modules\Connexion;

use App\AppController;
use \OCFram\BackController;
use \OCFram\HTTPRequest;


class ConnexionController extends BackController {
	use AppController;
	
	const USER_TYPE_MEMBER = 2;
	const USER_TYPE_ADMIN = 1;
	/** gestion de la connexion et setting de la session (admin/member)
	 *
	 * @param HTTPRequest $request
	 */
	public function executeIndex( HTTPRequest $request ) {
		$this->run();
		
		$this->_page->addVar( 'title', 'Connexion' );
		
		if ( $request->postExists( 'login' ) ) {
			$login    = $request->postData( 'login' );
			$password = $request->postData( 'password' );
			
			$Member = $this->_managers->getManagerOf( 'Members' )->getMember( $login, $password );
			
			if ( $Member ) {
				$this->_app->user()->setAuthenticated( true );
				$this->_app->user()->setLogin( $login );
				$this->app()->user()->setId($Member->id());
				if ( $Member->fk_NMY() == self::USER_TYPE_ADMIN ) {
					$this->_app->user()->setStatus( 'admin' );
				}
				else {
					$this->_app->user()->setStatus( 'member' );
				}
				$this->_app->httpResponse()->redirect( '/' );
			}
			else {
				$this->_app->user()->setFlash( 'Le pseudo ou le mot de passe est incorrect' );
			}
		}
	}
	
	/** deconnexion du visiteur et suppression des paramètres de session
	 *
	 * @param HTTPRequest $request
	 */
	public function executeDeconnexion( HTTPRequest $request ) {
		$this->run();
		
		$this->_page->addVar( 'title', 'Deconnexion' );
		
		$this->_app->user()->setAuthenticated( false );
		$this->_app->user()->unssetSession();
		$this->_app->httpResponse()->redirect( '/' );
	}
}