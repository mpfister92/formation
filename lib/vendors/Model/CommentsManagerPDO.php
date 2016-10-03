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
                FROM Comments
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

    public function modify(Comment $comment){
        $sql = 'UPDATE FROM Comments SET 
                auteur = :auteur,
                contenu = :contenu,
                WHERE id = :id';

        $request = $this->_dao->prepare($sql);
        $request->bindValue(':auteur',$comment->auteur());
        $request->bindValue(':contenu',$comment->contenu());
        $request->bindValue(':id',$comment->id());

        $request->execute();
    }

    public function get($id){
        $sql = 'SELECT id,news,auteur,contenu
                FROM Comments
                WHERE id = :id';

        $request = $this->_dao->prepare($sql);
        $request->bindValue(':id',(int) id,\PDO::PARAM_INT);

        $request->execute();

        $request->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');
        $comment = $request->fetch();

        return $comment;
    }

    public function delete($id){
        $sql = 'DELETE FROM Comments
                WHERE id = :id';

        $request = $this->_dao->prepare($sql);
        $request->bindValue(':id',(int) $id,\PDO::PARAM_INT);

        $request->execute();
    }

    public function deleteFromNews($news){
        $sql = 'DELETE FROM Comments
                WHERE news = :news';

        $request = $this->_dao->prepare($sql);
        $request->bindValue(':news',(int) $news,\PDO::PARAM_INT);

        $request->execute();
    }
}