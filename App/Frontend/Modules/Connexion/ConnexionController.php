<?php

namespace App\Frontend\Modules\Connexion;

use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\Member;
use \FormBuilder\MemberFormBuilder;
use \OCFram\FormHandler;


class ConnexionController extends BackController {
	
	/*public function executeIndex( HTTPRequest $request ) {
		$this->_page->addVar( 'title', 'Connexion' );
		
		if ( $request->method() == 'POST' ) {
			$login    = $request->postData( 'login' );
			$password = $request->postData( 'password' );
			
			if ( $this->_managers->getManagerOf( 'Members' )->getMember( $login, $password ) == 1 ) {
				$this->_app->user()->setMemberAuthenticated( true );
				$this->_app->user()->setLogin($login);
				$this->_app->user()->setStatus('membre');
				$this->_app->httpResponse()->redirect( '/' );
			}
			else {
				$this->_app->user()->setFlash( 'Le pseudo ou le mot de passe est incorrect' );
			}
		}
	}
	
	public function executeDeconnexion( HTTPRequest $request ) {
		$this->_page->addVar( 'title', 'Deconnexion' );
		
		$this->_app->user()->setMemberAuthenticated( false );
		$this->_app->user()->unssetSession();
		$this->_app->httpResponse()->redirect( '/' );
	}*/
	
	public function executeInscription( HTTPRequest $request ) {
		if ( $request->method() == 'POST' ) {
			$member = new Member( [
				'login'                 => $request->postData( 'login' ),
				'password'              => $request->postData( 'password' ),
				'email'                 => $request->postData( 'email' ),
				'password_confirmation' => $request->postData( 'password_confirmation' ),
				'email_confirmation'    => $request->postData( 'email_confirmation' ),
			] );
		}
		else {
			$member = new Member;
		}
		
		$formBuilder = new MemberFormBuilder( $member );
		$formBuilder->build( $this->_app->user(),$this->_managers->getManagerOf( 'Members' ) );
		
		$form = $formBuilder->form();
		
		$formHandler = new FormHandler( $form, $this->_managers->getManagerOf( 'Members' ), $request );
		
		if ( $formHandler->process() ) {
			$this->_app->user()->setFlash( 'Vous êtes inscrit !' );
			$this->_app->httpResponse()->redirect( '.' );
		}
		
		$this->_page->addVar( 'title', 'Inscription' );
		$this->_page->addVar( 'member', $member );
		$this->_page->addVar( 'form', $form->createView() );
	}
}