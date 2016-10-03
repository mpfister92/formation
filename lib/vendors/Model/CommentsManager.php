<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 03/10/2016
 * Time: 14:19
 */

namespace Model;

use OCFram\Manager;

abstract class CommentsManager extends Manager {
    abstract public function add(Comment $comment);

    public function save(Comment $comment){
        if($comment->isValid()){
            if ($comment->isNew()){
                $this->add($comment);
            }
            else {
                $this->modify($comment);
            }
        }
        else {
            throw new \RuntimeException('Le commentoire doit être validé pour être enregistré');
        }
    }

    abstract public function getListOf($news);
}