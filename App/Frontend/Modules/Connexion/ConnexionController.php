<?php

namespace App\Frontend\Modules\Connexion;

use App\AppController;
use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\Member;
use \FormBuilder\MemberFormBuilder;
use \OCFram\FormHandler;


class ConnexionController extends BackController {
	const USER_TYPE_MEMBER = 2;
	
	use AppController;
	
	public function executeInscription( HTTPRequest $Request ) {
		
		$this->run();
		
		if ( $Request->method() == 'POST' ) {
			$member = new Member( [
				'login'                 => $Request->postData( 'login' ),
				'password'              => $Request->postData( 'password' ),
				'email'                 => $Request->postData( 'email' ),
				'password_confirmation' => $Request->postData( 'password_confirmation' ),
				'email_confirmation'    => $Request->postData( 'email_confirmation' ),
			] );
			$member->setUser_Type(self::USER_TYPE_MEMBER);
		}
		else {
			$member = new Member;
		}
		
		$formBuilder = new MemberFormBuilder( $member );
		$formBuilder->build( $this->_app->user(),$this->_managers->getManagerOf( 'Members' ) );
		
		$form = $formBuilder->form();
		
		$formHandler = new FormHandler( $form, $this->_managers->getManagerOf( 'Members' ), $Request );
		
		if ( $formHandler->process() ) {
			$this->_app->user()->setFlash( 'Vous êtes inscrit !' );
			$this->_app->httpResponse()->redirect( '.' );
		}
		
		$this->_page->addVar( 'title', 'Inscription' );
		$this->_page->addVar( 'member', $member );
		$this->_page->addVar( 'form', $form->createView() );
	}
	
}