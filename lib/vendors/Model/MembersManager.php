<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 06/10/2016
 * Time: 10:26
 */

namespace Model;

use \OCFram\Manager;
use \Entity\Member;

abstract class MembersManager extends Manager {
	/** retourne vrai s'il existe un membre qui a le login passé en paramètre
	 * @param string $login
	 *
	 * @return bool
	 */
	abstract public function existsMemberUsingLogin( $login );
	
	/** retourne vrai s'il existe un membre avec le login et le mot de passe correspondant
	 * @param string $login
	 * @param string $password
	 *
	 * @return bool
	 */
	abstract public function getMember($login,$password);
	
	/** retourne vrai s'il existe un membre qui a l'email passé en paramètre
	 * @param string $email
	 *
	 * @return bool
	 */
	abstract public function existsMemberUsingEmail( $email );
	
	/** Ajoute un membre à la base de données
	 * @param Member $member
	 */
	abstract public function addMember( Member $member );
	
	/** appelle la méthode add si le membre est valide
	 * @param Member $member
	 */
	public function save( Member $member ) {
		if ( $member->isValid() ) {
			$this->addMember( $member );
		}
		else {
			throw new \RuntimeException( 'Le login existe déjà' );
		}
	}
}