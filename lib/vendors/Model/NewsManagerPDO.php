<?php

namespace Model;

use \Entity\News;

class NewsManagerPDO extends NewsManager {

    /**
     * @param int $debut
     * @param int $limite
     * @return News[]
     */
    public function getList($debut = -1,$limite = -1){
        $sql = 'SELECT id,auteur,titre,contenu,dateAjout,dateModif FROM news  ORDER BY id DESC';

        if($debut != -1 || $limite != -1){
            $sql .=' LIMIT '.(int) $limite.' OFFSET '.(int) $debut;
        }

        $requete = $this->_dao->query($sql);
        $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');

        $listeNews = $requete->fetchAll();

        foreach($listeNews as $news){
            date_default_timezone_set("Europe/Paris");
            $news->setDateAjout(new \DateTime($news->dateAjout()));
            $news->setDateModif(new \DateTime($news->dateModif()));
        }

        $requete->closeCursor();

        return $listeNews;
    }

    public function getNews($id){
        $sql = 'SELECT id,auteur,titre,contenu,dateAjout,dateModif
                FROM news
                WHERE id = :id';

        $requete = $this->_dao->prepare($sql);
        $requete->bindValue(':id',(int) $id, \PDO::PARAM_INT);
        $requete->execute();

        $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');

        if($news = $requete->fetch()){
            date_default_timezone_set("Europe/Paris");
            $news->setDateAjout(new \DateTime($news->dateAjout()));
            $news->setDateModif(new \DateTime($news->dateModif()));

            return $news;
        }

        return null;
    }

    public function count(){
        $sql = 'SELECT COUNT(*)
                FROM news';

        $requete = $this->_dao->query($sql);

        $count = $requete->fetchColumn();
        return $count;
    }

    protected function add(News $news){
        $sql = 'INSERT INTO news SET 
                auteur = :auteur,
                titre = :titre,
                contenu = :contenu, 
                dateAjout = NOW(),
                dateModif = NOW()';

        $request = $this->_dao->prepare($sql);
        $request->bindValue(':auteur',$news->auteur());
        $request->bindValue(':titre',$news->titre());
        $request->bindValue(':contenu',$news->contenu());

        $request->execute();
    }

    protected function modify(News $news){
        $sql = 'UPDATE news SET 
                auteur = :auteur,
                titre = :titre,
                contenu = :contenu,
                dateModif = NOW()
                WHERE id = :id';

        $request = $this->_dao->prepare($sql);
        $request->bindValue(':auteur',$news->auteur());
        $request->bindValue(':titre',$news->titre());
        $request->bindValue(':contenu',$news->contenu());
        $request->bindValue(':id',$news->id(),\PDO::PARAM_INT);

        $request->execute();
    }

    public function delete($id){
        $sql = 'DELETE FROM news
                WHERE id = '.(int) $id;

        $request = $this->_dao->exec($sql);
    }
}