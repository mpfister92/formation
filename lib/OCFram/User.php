<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 03/10/2016
 * Time: 10:15
 */

namespace OCFram;

session_start();

class User extends ApplicationComponent {
	public function getAttribute( $attr ) {
		if ( isset( $_SESSION[ $attr ] ) ) {
			return $_SESSION[ $attr ];
		}
		
		return null;
	}
	
	public function getFlash() {
		$flash = $_SESSION[ 'flash' ];
		unset( $_SESSION[ 'flash' ] );
		
		return $flash;
	}
	
	public function hasFlash() {
		return ( isset( $_SESSION[ 'flash' ] ) );
	}
	
	public function isAuthenticated() {
		if ( isset( $_SESSION[ 'auth' ] ) && $_SESSION[ 'auth' ] === true ) {
			return true;
		}
		
		return false;
	}
	
	public function setAttribute( $attr, $value ) {
		if ( empty( $attr ) ) {
			throw new \InvalidArgumentException( 'Attribut manquant' );
		}
		$_SESSION[ $attr ] = $value;
	}
	
	public function setAuthenticated( $authenticated = true ) {
		if ( !is_bool( $authenticated ) ) {
			throw new \InvalidArgumentException( 'Erreur : la valeur doit être un booléen' );
		}
		$_SESSION[ 'auth' ] = $authenticated;
	}
	
	public function setFlash( $value ) {
		if ( empty( $value ) ) {
			throw new \InvalidArgumentException( 'Erreur : valeur vide' );
		}
		$_SESSION[ 'flash' ] = $value;
	}
	
	/** Setter du login du user */
	public function setLogin( $login ) {
		$_SESSION[ 'name' ] = $login;
	}
	
	/** getter du login du user */
	public function getLogin() {
		if ( isset( $_SESSION[ 'name' ] ) ) {
			return $_SESSION[ 'name' ];
		}
		
		return null;
	}
	
	/** réinitialisation de la session*/
	public function unssetSession() {
		if ( isset( $_SESSION[ 'name' ] ) ) {
			unset( $_SESSION[ 'name' ] );
		}
		if ( isset( $_SESSION[ 'status' ] ) ) {
			unset( $_SESSION[ 'status' ] );
		}
		if ( isset( $_SESSION[ 'auth' ] ) ) {
			unset( $_SESSION[ 'auth' ] );
		}
	}
	
	/** set le statut de l'utilisateur (membre/admin) */
	public function setStatus( $status ) {
		$_SESSION[ 'status' ] = $status;
	}
	
	/** retourne le statut de l'utilisateur (membre/admin) */
	public function getStatus() {
		if ( isset( $_SESSION[ 'status' ] ) ) {
			return $_SESSION[ 'status' ];
		}
		
		return null;
	}
}