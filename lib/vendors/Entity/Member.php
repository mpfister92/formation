<?php

namespace Entity;

use \OCFram\Entity;

class Member extends Entity {
	protected $login, $password, $email, $password_confirmation, $email_confirmation,$fk_NMY;
	const LOGIN_INVALIDE    = 1;
	const EMAIL_INVALIDE    = 2;
	const PASSWORD_INVALIDE = 3;
	
	/** renvoie vrai si le membre est valide
	 * @return bool
	 */
	public function isValid() {
		return !( empty( $this->login ) || empty( $this->password ) || empty( $this->email ) || empty( $this->password_confirmation ) || empty( $this->email_confirmation ) );
	}
	
	/** GETTERS */
	
	public function login() {
		return $this->login;
	}
	
	public function password() {
		return $this->password;
	}
	
	public function email() {
		return $this->email;
	}
	
	public function password_confirmation() {
		return $this->password_confirmation;
	}
	
	public function email_confirmation() {
		return $this->email_confirmation;
	}
	
	public function fk_NMY() {
		return $this->fk_NMY;
	}
	
	/** SETTERS */
	
	public function setLogin( $login ) {
		if ( !is_string( $login ) || empty( $login ) ) {
			$this->errors[] = self::LOGIN_INVALIDE;
		}
		$this->login = $login;
	}
	
	public function setPassword( $password ) {
		if ( !is_string( $password ) || empty( $password ) ) {
			$this->errors[] = self::PASSWORD_INVALIDE;
		}
		$this->password = $password;
	}
	
	public function setEmail( $email ) {
		if ( is_string( $email ) || empty( $email ) ) {
			$this->errors[] = self::EMAIL_INVALIDE;
		}
		$this->email = $email;
	}
	
	public function setPassword_confirmation( $password ) {
		if ( !is_string( $password ) || empty( $password ) ) {
			$this->errors[] = self::PASSWORD_INVALIDE;
		}
		$this->password_confirmation = $password;
	}
	
	public function setEmail_confirmation( $email ) {
		if ( !is_string( $email ) || empty( $email ) ) {
			$this->errors[] = self::EMAIL_INVALIDE;
		}
		$this->email_confirmation = $email;
	}
	
	public function setFk_NMY($type){
		$this->fk_NMY = $type;
	}
}