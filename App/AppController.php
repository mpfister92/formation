<?php

namespace App;

use Composer\Package\Link;
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
			if ( $user->getStatus() == self::STATUS_MEMBER_ADMIN ) {
				$menu[ 'Admin' ] = $this->app()->router()->provideRoute( 'Backend', 'News', 'index', [] );
			}
			if ( $user->getStatus() == self::STATUS_MEMBER_MEMBER ) {
				$menu[ 'Vos news' ] = $this->app()->router()->provideRoute( 'Backend', 'News', 'index', [] );
			}
			$menu[ 'Résumé' ]       = $this->app()->router()->provideRoute( 'Frontend', 'News', 'getSummaryMember', ['id' => $user->getId()] );
			$menu [ 'Ajouter une news' ] = $this->app()->router()->provideRoute( 'Backend', 'News', 'insert', [] );
			$menu[ 'Deconnexion' ]       = $this->app()->router()->provideRoute( 'Backend', 'Connexion', 'deconnexion', [] );
		}
		else {
			$menu[ 'Connexion' ] = $this->app()->router()->provideRoute( 'Backend', 'Connexion', 'index', [] );
			//var_dump($this->app()->router()->provideRoute('Backend','Connexion','index',[]));
			//die();
			
			$menu[ 'S\'inscrire' ] = $this->app()->router()->provideRoute( 'Frontend', 'Connexion', 'inscription', [] );
		}
		
		$this->page()->addVar( 'menu', $menu );
	}
	
	/**
	 * Check if the connected user is an administrator
	 *
	 * @return bool
	 */
	public function loggedUserIsAdmin() {
		return 1 == $this->app()->user()->getStatus();
	}
	
	public function getUser() {
		return $this->app()->user();
	}
}

