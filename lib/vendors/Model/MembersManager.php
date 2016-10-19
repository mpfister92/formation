<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 06/10/2016
 * Time: 10:26
 */

namespace Model;

use Entity\News;
use \OCFram\Manager;
use \Entity\Member;

abstract class MembersManager extends Manager {
	const MEMBERY_ADMIN = 1;
	const MEMBERY_MEMBER = 2;
	
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
	
	/**
	 * retourne l'id d'un membre a partir de son login
	 * @param $login
	 *
	 * @return int
	 */
	abstract public function getIdMemberUsingLogin($login);
	
	/**
	 * retourne le login d'un membre en fonction de son id
	 * @param int $id_member
	 *
	 * @return string
	 */
	abstract public function getLoginMemberFromId($id_member);
	
	/**
	 * retourne le statut d'un membre en fonction de son id
	 * @param int $id_member
	 *
	 * @return int
	 */
	abstract public function getStatusMemberFromId( $id_member );
	
	/**
	 * retourne un tableau de news pour un membre avec les commentaire
	 * associés écrit par ce membre
	 * @param $id_member
	 *
	 * @return News[]
	 */
	abstract public function getNewsAndCommentUsingMemberId_a($id_member);
	
	/**
	 * retourne le nombre de commentaires écrits par un membre
	 * @param int $id_member
	 *
	 * @return int
	 */
	abstract public function countNumberCommentUsingIdMember($id_member);
	
	/** retourne le nombre de news écrite par un membre
	 * @param int $id_member
	 * @return int
	 */
	abstract public function countNumberNewsUsingIdMember($id_member);
}