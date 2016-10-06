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

abstract class CommentsManager extends Manager
{
	/** ajoute un commentaire dans la base
	 * @param Comment $comment
	 */
    abstract protected function add(Comment $comment);
	
	/** retourne la liste des commentaires pour une news
	 * @param $news
	 *
	 * @return Comment[]
	 */
    abstract public function getListOf($news);
	
	/** update d'un commentaire
	 * @param Comment $comment
	 */
    abstract protected function modify(Comment $comment);
	
	/** retourne le commentaire correspondant à l'id passé en paramètre
	 * @param $id
	 *
	 * @return Comment
	 */
    abstract public function get($id);
	
	/** supprime un commentaire
	 * @param $id
	 */
    abstract public function delete($id);
	
	/** supprime les commentaires d'une news
	 * @param $news
	 */
    abstract public function deleteFromNews($news);
	
	/** determine la méthode a appeler (ajouter/modifier) selon le commentaire
	 * @param Comment $comment
	 */
    public function save(Comment $comment)
    {
        if ($comment->isValid()) {
            if ($comment->isNew()) {
                $this->add($comment);
            } else {
                $this->modify($comment);
            }
        } else {
            throw new \RuntimeException('Le commentaire doit être validé pour être enregistré');
        }
    }
}