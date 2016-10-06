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
	 * @param string $login
	 * @param string $password
	 *
	 * @return bool
	 */
	public function getMember($login,$password) {
		$sql = 'SELECT COUNT(*)
				FROM members
				WHERE login = :login
				AND password = :password';
		
		$requete = $this->_dao->prepare($sql);
		
		$requete->bindValue(':login',$login);
		$requete->bindValue(':password',$password);
		
		$requete->execute();
		
		$count = $requete->fetchColumn();
		
		if ($count == 1){
			return true;
		}
		else {
			return false;
		}
	}
	
	/** retourne vrai s'il existe un membre qui a le login passé en paramètre
	 * @param string $login
	 *
	 * @return bool
	 */
	public function existsMemberUsingLogin($login) {
		$sql = 'SELECT COUNT(*)
				FROM members
				WHERE login = :login';
		
		$requete = $this->_dao->prepare($sql);
		
		$requete->bindValue(':login',$login);
		
		$requete->execute();
		
		$count = $requete->fetchColumn();
		
		if ($count == 1){
			return true;
		}
		else {
			return false;
		}
	}
	
	/** retourne vrai s'il existe un membre qui a l'email passé en paramètre
	 * @param string $email
	 *
	 * @return bool
	 */
	public function existsMemberUsingEmail($email) {
		$sql = 'SELECT COUNT(*)
				FROM members
				WHERE email = :email';
		
		$requete = $this->_dao->prepare($sql);
		
		$requete->bindValue(':email',$email);
		
		$requete->execute();
		
		$count = $requete->fetchColumn();
		
		if ($count == 1){
			return true;
		}
		else {
			return false;
		}
	}
	
	/** Ajoute un membre à la base de données
	 * @param Member $member
	 */
	public function add(Member $member){
		$sql = 'INSERT INTO members SET
				login = :login,
				password = :password,
				email = :email';
		
		$requete = $this->_dao->prepare($sql);
		$requete->bindValue(':login',$member->login());
		$requete->bindValue(':password',$member->password());
		$requete->bindValue(':email',$member->email());
		
		$requete->execute();
	}
	
}