<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 03/10/2016
 * Time: 14:19
 */

namespace Model;

use \Entity\News;

class CommentsManagerPDO extends CommentsManager {
    public function add(Comment $comment) {
        $sql = 'INSERT INTO Comments SET 
                  news = :news,
                  auteur = :auteur,
                  contenu = :contenu,
                  date = NOW()';

        $requete = $this->_dao->prepare($sql);
        $requete->bindValue(':news',$comment->news(), \PDO::PARAM_INT);
        $requete->bindValue(':auteur',$comment->auteur());
        $requete->bindValue(':contenu',$comment->contenu());

        $requete->execute();

        $comment->setId($this->_dao->lastInsertId());
    }

    public function getListOf($news){
        $sql = 'SELECT auteur,contenu,date
                FROM Comment
                WHERE news = :news';

        $requete = $this->_dao->prepare($sql);
        $requete->bindValue(':news',$news,\PDO::PARAM_INT);

        $requete->execute();

        $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');

        $comments = $requete->fetchAll();

        foreach($comments as $comment){
            $comment->setData(new \DateTime($comment->sate()));
        }

        return $comments;
    }
}