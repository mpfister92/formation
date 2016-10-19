<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 03/10/2016
 * Time: 14:19
 */

namespace Model;

use \OCFram\Manager;
use \Entity\Comment;
use \Entity\News;

abstract class CommentsManager extends Manager {
	const COMMENT_STATE_VALID   = 1;
	const COMMENT_STATE_INVALID = 2;
	
	/** ajoute un commentaire dans la base
	 *
	 * @param Comment $comment
	 */
	abstract public function add( Comment $comment );
	
	/** retourne la liste des commentaires pour une news
	 *
	 * @param int  $news_id
	 * @param      $Comment_date
	 * @param int  $state
	 *
	 * @return Comment[]
	 */
	abstract public function getListOf( $news_id, $Comment_date = null, $state = null );
	
	/** update d'un commentaire
	 *
	 * @param Comment $comment
	 */
	abstract protected function modify( Comment $comment );
	
	/** retourne le commentaire correspondant à l'id passé en paramètre
	 *
	 * @param int $id_comment
	 *
	 * @return Comment
	 */
	abstract public function get( $id_comment );
	
	/** supprime un commentaire
	 *
	 * @param int $id_comment
	 */
	abstract public function delete( $id_comment );
	
	/** supprime les commentaires d'une news
	 *
	 * @param int $news_id
	 */
	abstract public function deleteFromNews( $news_id );
	
	/** determine la méthode a appeler (ajouter/modifier) selon le commentaire
	 *
	 * @param Comment $comment
	 */
	public function save( Comment $comment ) {
		if ( $comment->isValid() ) {
			if ( $comment->isNew() ) {
				$this->add( $comment );
			}
			else {
				$this->modify( $comment );
			}
		}
		else {
			throw new \RuntimeException( 'Le commentaire doit être validé pour être enregistré' );
		}
	}
	
	/** retoune le nom de l'auteur de la news pour le commentaire numéro $id_comment
	 *
	 * @param int $id_comment
	 *
	 * @return string
	 */
	abstract public function getNewsAuthorUsingIdComment( $id_comment );
	
	
	/** retourne l'auteur d'un commentaire
	 *
	 * @param int $id_comment
	 *
	 * @return string
	 */
	abstract public function getCommentAuthorFromId( $id_comment );
	
	/**
	 * retourne l'id du membre qui a ecrit le commentaire
	 *
	 * @param int $id_comment
	 *
	 * @return int
	 */
	abstract public function getCommentMemberIdFromId( $id_comment );
	
	/**
	 * retourne la dernière date d'edition d'un commentaire
	 *
	 * @return \DateTime
	 */
	abstract public function getMaxEditionDate();
}