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
		$sql = 'SELECT NMC_id AS id,NMC_login AS login,NMC_password AS password,NMC_email AS email,NMC_fk_NMY AS fk_NMY
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
	
	public function getMemberFromId($id){
		$sql = 'SELECT NMC_id AS id,NMC_login AS login,NMC_password AS password,NMC_email AS email,NMC_fk_NMY AS fk_NMY
				FROM t_new_memberc
				WHERE NMC_id = :id';
		
		$requete = $this->_dao->prepare($sql);
		
		$requete->bindValue(':id',$id,\PDO::PARAM_INT);
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
		$requete->bindValue( ':type', $member->fk_NMY() );
		
		
		$requete->execute();
	}
	
	/**
	 * retourne l'id d'un membre a partir de son login
	 * @param $login
	 *
	 * @return int
	 */
	public function getIdMemberUsingLogin( $login ) {
		$sql = 'SELECT NMC_id AS id
				FROM t_new_memberc
				WHERE NMC_login = :login';
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( ':login', $login );
		
		$requete->execute();
		
		return ( $requete->fetchColumn() );
	}
	
	/**
	 * retourne le login d'un membre en fonction de son id
	 * @param int $id_member
	 *
	 * @return string
	 */
	public function getLoginMemberFromId( $id_member ) {
		$sql = 'SELECT NMC_login AS login
				FROM t_new_memberc
				WHERE NMC_id = :id';
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( ':id', $id_member, \PDO::PARAM_INT );
		
		$requete->execute();
		
		return $requete->fetchColumn();
	}
	
	/**
	 * retourne le statut d'un membre en fonction de son id
	 * @param int $id_member
	 *
	 * @return int
	 */
	public function getStatusMemberFromId( $id_member ) {
		$sql = 'SELECT NMC_fk_NMY AS fk_NMY
				FROM t_new_memberc
				WHERE NMC_id = :id';
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( ':id', $id_member, \PDO::PARAM_INT );
		
		$requete->execute();
		
		return $requete->fetchColumn();
	}
	
	/**
	 * retourne un tableau de news pour un membre avec les commentaire
	 * associés écrit par ce membre
	 * @param $id_member
	 *
	 * @return News[]
	 */
	public function getNewsAndCommentUsingMemberId_a($id_member){
		$sql = 'SELECT *
				FROM t_new_newsc 
				LEFT OUTER JOIN t_new_commentc ON NCC_fk_NNC = NNC_id AND NCC_fk_NCE = :state_comment AND NCC_fk_NMC = :id_member
				WHERE (NCC_fk_NMC = :id_member
				OR NNC_fk_NMC = :id_member)
				AND NNC_fk_NNE = :state_news';
		
		$requete = $this->_dao->prepare($sql);
		$requete->bindValue(':id_member',$id_member,\PDO::PARAM_INT);
		$requete->bindValue(':state_comment',CommentsManager::COMMENT_STATE_VALID,\PDO::PARAM_INT);
		$requete->bindValue(':state_news',NewsManager::NEWS_STATE_VALID,\PDO::PARAM_INT);
	
		$requete->execute();
		
		$News_a = [];
		while ($result = $requete->fetch(\PDO::FETCH_ASSOC)){
			if(!key_exists($result['NNC_id'],$News_a)){
				$News_a[$result['NNC_id']] = [
					'id' => $result['NNC_id'],
					'fk_NMC' => $result['NNC_fk_NMC'],
					'titre' => $result['NNC_titre'],
					'contenu' => $result['NNC_contenu'],
					'dateAjout' => $result['NNC_dateAjout'],
					'dateModif' => $result['NNC_dateModif'],
					'comments' => []
				];
			}
			if(null != $result['NCC_id']){
				$News_a[$result['NNC_id']]['comments'][$result['NCC_id']] = [
					'id_comment' => $result['NCC_id'],
					'contenu_comment' => $result['NCC_contenu'],
					'date_comment' => $result['NCC_date'],
					'date_last_update' => $result['NCC_datemodif']
				];
			}
		}
		return $News_a;
	}
	
	/** retourne le nombre de news écrite par un membre
	 * @param int $id_member
	 * @return int
	 */
	public function countNumberNewsUsingIdMember($id_member){
		$sql = 'SELECT COUNT(*)
				FROM t_new_newsc
				WHERE NNC_fk_NMC = :id_member
				AND NNC_fk_NNE = :state';
		
		$requete = $this->_dao->prepare($sql);
		$requete->bindValue(':id_member',$id_member,\PDO::PARAM_INT);
		$requete->bindValue(':state',NewsManager::NEWS_STATE_VALID,\PDO::PARAM_INT);
		
		$requete->execute();
		
		return $requete->fetchColumn();
	}
	
	/**
	 * retourne le nombre de commentaires écrits par un membre
	 * @param int $id_member
	 *
	 * @return int
	 */
	public function countNumberCommentUsingIdMember($id_member){
		$sql = 'SELECT COUNT(*)
				FROM t_new_commentc
				WHERE NCC_fk_NMC = :id_member
				AND NCC_fk_NCE = :state';
		
		$requete = $this->_dao->prepare($sql);
		$requete->bindValue(':id_member',$id_member);
		$requete->bindValue(':state',CommentsManager::COMMENT_STATE_VALID);
		
		$requete->execute();
		
		return $requete->fetchColumn();
	}
}