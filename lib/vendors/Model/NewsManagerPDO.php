<?php

/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 03/10/2016
 * Time: 12:36
 */

namespace Model;

use \Entity\News;

class NewsManagerPDO extends NewsManager {

    public function getList($debut = -1,$limite = -1){
        $sql = 'SELECT id,auteur,contenu,dateAjout,dateModif 
                FROM News 
                ORDER BY id DESC';

        if($debut > -1 || $limite > -1){
            $sql .='LIMIT '.(int) $limite.' OFFSET '.(int) $debut;
        }

        $requete = $this->_dao->query($sql);
        $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');

        $listeNews = $requete->fetchAll();

        foreach($listeNews as $news){
            $news->setDateAjout(new \DateTime($news->dateAjout()));
            $news->setDateModif(new \DateTime($news->dateModif()));
        }

        $requete->closeCursor();

        return $listeNews;
    }
}