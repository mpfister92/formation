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
	 * @param Comment $comment
	 */
	protected function add( Comment $comment ) {
		$sql = 'INSERT INTO t_new_commentc SET 
                  NCC_fk_NNC = :news,
                  NCC_auteur = :auteur,
                  NCC_contenu = :contenu,
                  NCC_date = NOW(),
                  NCC_fk_NCE = :state,
		 		  NCC_fk_NMC = :member';
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( ':news', $comment->news(), \PDO::PARAM_INT );
		if ( $comment->auteur() != null ) {
			$requete->bindValue( ':auteur', $comment->auteur() );
			$requete->bindValue( ':member', null );
		}
		if ( $comment->member() != null ) {
			$requete->bindValue( ':member', $comment->member() );
			$requete->bindValue( ':auteur', null );
		}
		$requete->bindValue( ':contenu', $comment->contenu() );
		$requete->bindValue( ':state', self::COMMENT_STATE_VALID );
		
		
		$requete->execute();
		
		$comment->setId( $this->_dao->lastInsertId() );
	}
	
	/** retourne la liste des commentaires pour une news
	 *
	 * @param $news
	 *
	 * @return Comment[]
	 */
	public function getListOf( $news ) {
		$sql = 'SELECT NCC_fk_NNC AS news,NCC_auteur AS auteur,NCC_contenu AS contenu,NCC_date AS date,NCC_id AS id, NCC_fk_NMC AS member 
                FROM t_new_commentc
                WHERE NCC_fk_NNC = :news
                AND NCC_fk_NCE = :state';
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( ':news', $news, \PDO::PARAM_INT );
		$requete->bindValue( ':state', self::COMMENT_STATE_VALID );
		
		$requete->execute();
		
		$requete->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment' );
		
		$comments = $requete->fetchAll();
		
		foreach ( $comments as $comment ) {
			$comment->setDate( new \DateTime( $comment->date() ) );
		}
		
		return $comments;
	}
	
	/** update d'un commentaire
	 *
	 * @param Comment $comment
	 */
	public function modify( Comment $comment ) {
		$sql = 'UPDATE t_new_commentc SET 
                NCC_auteur = :auteur,
                NCC_contenu = :contenu
                WHERE NCC_id = :id';
		
		$request = $this->_dao->prepare( $sql );
		$request->bindValue( ':auteur', $comment->auteur() );
		$request->bindValue( ':contenu', $comment->contenu() );
		$request->bindValue( ':id', $comment->id(), \PDO::PARAM_INT );
		
		$request->execute();
	}
	
	/** retourne le commentaire correspondant à l'id passé en paramètre
	 *
	 * @param $id
	 *
	 * @return Comment
	 */
	public function get( $id ) {
		$sql = 'SELECT NCC_id AS id,NCC_fk_NNC AS news,NCC_auteur AS auteur,NCC_contenu AS contenu, NCC_fk_NMC AS member
                FROM t_new_commentc
                WHERE NCC_id = :id';
		
		$request = $this->_dao->prepare( $sql );
		$request->bindValue( ':id', (int)$id, \PDO::PARAM_INT );
		
		$request->execute();
		
		$request->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment' );
		$comment = $request->fetch();
		
		return $comment;
	}
	
	/** supprime un commentaire
	 *
	 * @param $id
	 */
	public function delete( $id ) {
		$sql = 'UPDATE t_new_commentc SET
                	NCC_fk_NCE = :state
                WHERE NCC_id = :id';
		
		$request = $this->_dao->prepare( $sql );
		$request->bindValue( ':id', (int)$id, \PDO::PARAM_INT );
		$request->bindValue( ':state', self::COMMENT_STATE_INVALID );
		
		$request->execute();
	}
	
	/** supprime les commentaires d'une news
	 *
	 * @param $news
	 */
	public function deleteFromNews( $news ) {
		$sql = 'UPDATE t_new_commentc SET
					NCC_fk_NCE = :state
                WHERE NCC_fk_NNC = :news';
		
		$request = $this->_dao->prepare( $sql );
		$request->bindValue( ':news', (int)$news, \PDO::PARAM_INT );
		$request->bindValue( ':state', self::COMMENT_STATE_INVALID );
		
		$request->execute();
	}
	
	/** retoune le nom de l'auteur de la news pour le commentaire numéro $id
	 *
	 * @param int $id
	 *
	 * @return string
	 */
	public function getNewsAuthorFromIdComment( $id ) {
		$sql = 'SELECT NMC_login
				FROM t_new_commentc 
				INNER JOIN t_new_newsc ON NNC_id = NCC_fk_NNC
				INNER JOIN t_new_memberc ON NMC_id = NNC_fk_NMC
				WHERE NCC_id = :id';
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( ':id', $id, \PDO::PARAM_INT );
		
		$requete->execute();
		
		$result = $requete->fetchColumn();
		
		return $result;
	}
	
	/** retourne l'auteur d'un commentaire
	 *
	 * @param int $id
	 *
	 * @return string
	 */
	public function getCommentAuthorFromId( $id ) {
		$sql = 'SELECT NCC_auteur
				FROM t_new_commentc
				WHERE NCC_id = :id';
		
		$request = $this->_dao->prepare( $sql );
		
		$request->bindValue( ':id', $id, \PDO::PARAM_INT );
		
		$request->execute();
		
		$result = $request->fetchColumn();
		
		return $result;
	}
}