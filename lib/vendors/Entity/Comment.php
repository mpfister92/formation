<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 03/10/2016
 * Time: 14:19
 */

namespace Entity;

use \OCFram\Entity;

class Comment extends Entity {
    protected $_news;
    protected $_auteur;
    protected $_contenu;
    protected $_date;

    const AUTEUR_INVALIDE = 1;
    const TEXT_INVALIDE = 2;

    public function isValid(){
        return !(empty($this->_auteur) || empty($this->_contenu));
    }

    public function setNews($news){
        if(!empty($news)) {
            $this->_news = (int) $news;
        }
    }

    public function setAuteur($auteur){
        if(!is_string($auteur) || empty($auteur)){
            $this->_errors[] = self::AUTEUR_INVALIDE;
        }
        $this->_auteur = $auteur;
    }

    public function setText($contenu){
        if(!is_string($contenu) || empty($contenu)){
            $this->_errors[] = self::TEXT_INVALIDE;
        }
        $this->_contenu= $contenu;
    }

    public function setDate(\DateTime $date){
        $this->_date = $date;
    }

    public function news() {
        return $this->_news;
    }

    public function auteur(){
        return $this->_auteur;
    }

    public function contenu(){
        return $this->_contenu;
    }

    public function date(){
        return $this->_date;
    }

}