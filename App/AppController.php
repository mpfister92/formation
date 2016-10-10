<?php

namespace App;

use Composer\Package\Link;
use \Detection\MobileDetect;
use OCFram\Application;
use OCFram\Linking;

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
				$menu[ 'Admin' ] = Linking::provideRoute('Backend','News','index',[]);
			}
			if ( $user->getStatus() == 'member' ) {
				$menu[ 'Vos news' ] = Linking::provideRoute('Backend','News','index',[]);
			}
			$menu [ 'Ajouter une news' ] = Linking::provideRoute('Backend','News','insert',[]);
			$menu[ 'Deconnexion' ]       = Linking::provideRoute('Backend','Connexion','deconnexion',[]);
		}
		else {
			$menu[ 'Connexion' ]   = Linking::provideRoute('Backend','Connexion','index',[]);
			$menu[ 'S\'inscrire' ] = Linking::provideRoute('Frontend','Connexion','inscription',[]);
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

