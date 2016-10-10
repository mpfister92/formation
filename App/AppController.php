<?php

namespace App;

use \Detection\MobileDetect;
use OCFram\Application;

/**
 * Trait AppController
 *
 * @package App
 */
trait AppController {
	private $menu = [];
	
	public function run() {
		$detect      = new MobileDetect;
		$device_type = ( $detect->isMobile() ? ( $detect->isTablet() ? 'tablette' : 'téléphone' ) : 'ordinateur' );
		$this->page()->addVar( 'device_type', $device_type );
		
		$user = $this->app()->user();
		$this->page()->addVar( 'user', $user );
		
		//build du menu
		$menu[ 'Accueil' ] = "/";
		
		if ( $user->isAuthenticated() ) {
			if ( $user->getStatus() == 'admin' ) {
				$menu[ 'Admin' ] = "/admin/";
			}
			if ( $user->getStatus() == 'member' ) {
				$menu[ 'Vos news' ] = "/admin/";
			}
			$menu [ 'Ajouter une news' ] = "/admin/news-insert.html";
			$menu[ 'Deconnexion' ]       = "/admin/deconnexion.html";
		}
		else {
			$menu[ 'Connexion' ]   = "/admin/connexion.html";
			$menu[ 'S\'inscrire' ] = "/inscription.html";
		}
		
		$this->page()->addVar('menu',$menu);
	}
	
	/**
	 * Check if the connected user is an administrator
	 * @return bool
	 */
	public function loggetUserIsAdmin() {
		return 'admin' == $this->app()->user()->getStatus();
	}
	
	public function getUser() {
		return $this->app()->user();
	}
}

