<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 03/10/2016
 * Time: 14:19
 */

namespace Model;

use \Entity\News;
use \Entity\Comment;

class CommentsManagerPDO extends CommentsManager {
	/** ajoute un commentaire dans la base
	 *
	 * @param Comment $Comment
	 */
	public function add( Comment $Comment ) {
		$sql = 'INSERT INTO t_new_commentc SET 
                  NCC_fk_NNC = :news,
                  NCC_auteur = :auteur,
                  NCC_contenu = :contenu,
                  NCC_date = NOW(),
                  NCC_fk_NCE = :state,
		 		  NCC_fk_NMC = :member,
		 		  NCC_datemodif = NOW()';
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( ':news', $Comment->fk_NNC(), \PDO::PARAM_INT );
		if ( $Comment->auteur() != null ) {
			$requete->bindValue( ':auteur', $Comment->auteur() );
			$requete->bindValue( ':member', null );
		}
		if ( $Comment->fk_NMC() != null ) {
			$requete->bindValue( ':member', $Comment->fk_NMC() );
			$requete->bindValue( ':auteur', null );
		}
		$requete->bindValue( ':contenu', $Comment->contenu() );
		$requete->bindValue( ':state', self::COMMENT_STATE_VALID );
		
		
		$requete->execute();
		
		$Comment->setDate( new \DateTime() );
		$Comment->setId( $this->_dao->lastInsertId() );
	}
	
	/** retourne la liste des commentaires pour une news
	 *
	 * @param int $news_id
	 * @param      $Comment_date
	 * @param int  $state
	 *
	 * @return Comment[]
	 */
	public function getListOf( $news_id, $Comment_date = null, $state = null ) {
		$sql = 'SELECT NCC_id AS id,NCC_fk_NNC AS fk_NNC,NCC_auteur AS auteur,NCC_contenu AS contenu,NCC_date AS date, NCC_fk_NCE AS fk_NCE, NCC_fk_NMC AS fk_NMC, NCC_datemodif AS dateModif 
                FROM t_new_commentc
                WHERE NCC_fk_NNC = :news';
		
		if ( null != $state ) {
			$sql .= ' AND NCC_fk_NCE = :state';
		}
		
		if ( null != $Comment_date ) {
			$sql .= ' AND NCC_datemodif > :date AND NCC_datemodif < NOW()
					ORDER BY dateModif ASC';
		}
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( ':news', $news_id, \PDO::PARAM_INT );
		if ( null != $state ) {
			$requete->bindValue( ':state', $state );
		}
		if ( null != $Comment_date ) {
			$requete->bindValue( ':date', $Comment_date );
		}
		
		$requete->execute();
		
		$requete->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment' );
		
		$Comments_a = $requete->fetchAll();
		
		foreach ( $Comments_a as $Comment ) {
			$Comment->setDate( new \DateTime( $Comment->date() ) );
			$Comment->setDateModif( new \DateTime( $Comment->dateModif() ) );
		}
		
		return $Comments_a;
	}
	
	/** update d'un commentaire
	 *
	 * @param Comment $Comment
	 */
	public function modify( Comment $Comment ) {
		$sql = 'UPDATE t_new_commentc SET 
                NCC_contenu = :contenu,
                NCC_datemodif = NOW()
				WHERE NCC_id = :id';
		
		$request = $this->_dao->prepare( $sql );
		$request->bindValue( ':contenu', $Comment->contenu() );
		$request->bindValue( ':id', $Comment->id(), \PDO::PARAM_INT );
		
		$request->execute();
	}
	
	/** retourne le commentaire correspondant à l'id passé en paramètre
	 *
	 * @param int $id_comment
	 *
	 * @return Comment
	 */
	public function get( $id_comment ) {
		$sql = 'SELECT NCC_id AS id,NCC_fk_NNC AS fk_NNC,NCC_auteur AS auteur,NCC_contenu AS contenu, NCC_fk_NMC AS fk_NMC, NCC_datemodif AS datemodif
                FROM t_new_commentc
                WHERE NCC_id = :id';
		
		$request = $this->_dao->prepare( $sql );
		$request->bindValue( ':id', (int)$id_comment, \PDO::PARAM_INT );
		
		$request->execute();
		
		$request->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment' );
		$comment = $request->fetch();
		
		return $comment;
	}
	
	/** supprime un commentaire
	 *
	 * @param int $id_comment
	 */
	public function delete( $id_comment ) {
		$sql = 'UPDATE t_new_commentc SET
                	NCC_fk_NCE = :state,
                	NCC_datemodif = NOW()
                WHERE NCC_id = :id';
		
		$request = $this->_dao->prepare( $sql );
		$request->bindValue( ':id', (int)$id_comment, \PDO::PARAM_INT );
		$request->bindValue( ':state', self::COMMENT_STATE_INVALID );
		
		$request->execute();
	}
	
	/** supprime les commentaires d'une news
	 *
	 * @param int $news_id
	 */
	public function deleteFromNews( $news_id ) {
		$sql = 'UPDATE t_new_commentc SET
					NCC_fk_NCE = :state,
					NCC_datemodif = NOW()
                WHERE NCC_fk_NNC = :news';
		
		$request = $this->_dao->prepare( $sql );
		$request->bindValue( ':news', (int)$news_id, \PDO::PARAM_INT );
		$request->bindValue( ':state', self::COMMENT_STATE_INVALID );
		
		$request->execute();
	}
	
	/** retoune le nom de l'auteur de la news pour le commentaire numéro $id_comment
	 *
	 * @param int $id_comment
	 *
	 * @return string
	 */
	public function getNewsAuthorUsingIdComment( $id_comment ) {
		$sql = 'SELECT NMC_login
				FROM t_new_commentc 
				INNER JOIN t_new_newsc ON NNC_id = NCC_fk_NNC
				INNER JOIN t_new_memberc ON NMC_id = NNC_fk_NMC
				WHERE NCC_id = :id';
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( ':id', $id_comment, \PDO::PARAM_INT );
		
		$requete->execute();
		
		$result = $requete->fetchColumn();
		
		return $result;
	}
	
	/** retourne l'auteur d'un commentaire
	 *
	 * @param int $id_comment
	 *
	 * @return string
	 */
	public function getCommentAuthorFromId( $id_comment ) {
		$sql = 'SELECT NCC_auteur 
				FROM t_new_commentc
				WHERE NCC_id = :id';
		
		$request = $this->_dao->prepare( $sql );
		
		$request->bindValue( ':id', $id_comment, \PDO::PARAM_INT );
		
		$request->execute();
		
		$result = $request->fetchColumn();
		
		return $result;
	}
	
	/**
	 * retourne l'id du membre qui a ecrit le commentaire
	 * @param int $id_comment
	 *
	 * @return int
	 */
	public function getCommentMemberIdFromId( $id_comment ) {
		$sql = 'SELECT NCC_fk_NMC 
				FROM t_new_commentc
				WHERE NCC_id = :id';
		
		$request = $this->_dao->prepare( $sql );
		
		$request->bindValue( ':id', $id_comment, \PDO::PARAM_INT );
		
		$request->execute();
		
		$result = $request->fetchColumn();
		
		return $result;
	}
	
	/**
	 * retourne la dernière date d'edition d'un commentaire
	 * @return \DateTime
	 */
	public function getMaxEditionDate() {
		$sql = 'SELECT MAX(NCC_datemodif)
				FROM t_new_commentc';
		
		$requete = $this->_dao->prepare( $sql );
		$requete->execute();
		
		return $requete->fetchColumn();
	}
}