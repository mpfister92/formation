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
    abstract protected function add(Comment $comment);

    abstract public function getListOf($news);

    abstract protected function modify(Comment $comment);

    abstract public function get($id);

    abstract public function delete($id);

    abstract public function deleteFromNews($news);

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