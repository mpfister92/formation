<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 06/10/2016
 * Time: 10:26
 */

namespace Model;

use \Entity\Member;

class MembersManagerPDO extends MembersManager {
	/** retourne vrai s'il existe un membre avec le login et le mot de passe correspondant
	 *
	 * @param string $login
	 * @param string $password
	 *
	 * @return bool
	 */
	public function getMember( $login, $password ) {
		$sql = 'SELECT NMC_id AS id,NMC_login AS login,NMC_password AS password,NMC_email AS email,NMC_fk_NMY AS user_type
				FROM t_new_memberc
				WHERE NMC_login = :login
				AND NMC_password = :password';
		
		$requete = $this->_dao->prepare( $sql );
		
		$requete->bindValue( ':login', $login );
		$requete->bindValue( ':password', $password );
		
		$requete->execute();
		
		$requete->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Member' );
		
		if ( $member = $requete->fetch() ) {
			return $member;
		}
	}
	
	/** retourne vrai s'il existe un membre qui a le login passé en paramètre
	 *
	 * @param string $login
	 *
	 * @return bool
	 */
	public function existsMemberUsingLogin( $login ) {
		$sql = 'SELECT COUNT(*)
				FROM t_new_memberc
				WHERE NMC_login = :login';
		
		$requete = $this->_dao->prepare( $sql );
		
		$requete->bindValue( ':login', $login );
		
		$requete->execute();
		
		$count = $requete->fetchColumn();
		
		if ( $count == 1 ) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/** retourne vrai s'il existe un membre qui a l'email passé en paramètre
	 *
	 * @param string $email
	 *
	 * @return bool
	 */
	public function existsMemberUsingEmail( $email ) {
		$sql = 'SELECT NMC_id AS id
				FROM t_new_memberc
				WHERE NMC_email = :email';
		
		$requete = $this->_dao->prepare( $sql );
		
		$requete->bindValue( ':email', $email );
		
		$requete->execute();
		
		return ( true == $requete->fetch() );
	}
	
	/** Ajoute un membre à la base de données
	 *
	 * @param Member $member
	 */
	public function addMember( Member $member ) {
		$sql = 'INSERT INTO t_new_memberc SET 
					NMC_login = :login, 
					NMC_password = :password, 
					NMC_email = :email,
					NMC_fk_NMY = :type';
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( ':login', $member->login() );
		$requete->bindValue( ':password', $member->password() );
		$requete->bindValue( ':email', $member->email() );
		$requete->bindValue( ':type', $member->user_type() );
		
		
		$requete->execute();
	}
	
	public function getIdMemberFromLogin( $login ) {
		$sql = 'SELECT NMC_id AS id
				FROM t_new_memberc
				WHERE NMC_login = :login';
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( ':login', $login );
		
		$requete->execute();
		
		return ( $requete->fetchColumn() );
	}
	
	public function getLoginMemberFromId( $id ) {
		$sql = 'SELECT NMC_login
				FROM t_new_memberc
				WHERE NMC_id = :id';
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( ':id', $id, \PDO::PARAM_INT );
		
		$requete->execute();
		
		return $requete->fetchColumn();
	}
	
	public function getStatusMemberFromId( $id ) {
		$sql = 'SELECT NMC_fk_NMY 
				FROM t_new_memberc
				WHERE NMC_id = :id';
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( ':id', $id, \PDO::PARAM_INT );
		
		return $requete->fetchColumn();
	}
}