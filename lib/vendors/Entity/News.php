<?php

/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 03/10/2016
 * Time: 11:49
 */

namespace Entity;

use \OCFram\Entity;

class News extends Entity {
    protected $_auteur;
    protected $_titre;
    protected $_contenu;
    protected $_dateAjout;
    protected $_dateModif;

    const AUTEUR_INVALIDE = 1;
    const TITRE_INVALIDE = 2;
    const CONTENU_INVALIDE = 3;

    public function isValid(){
        return !(empty($this->_auteur) || empty($this->_titre) || empty($this->_contenu));
    }

    public function setAuteur($auteur){
        if(!is_string($auteur) || empty($auteur)){
            $this->_errors[] = self::AUTEUR_INVALIDE;
        }
        $this->_auteur = $auteur;
    }

    public function setTitre($titre){
        if(!is_string($titre) || empty($titre)){
            $this->_errors[] = self::TITRE_INVALIDE;
        }
        $this->_titre = $titre;
    }

    public function setContenu($contenu){
        if(!is_string($contenu) || empty($contenu)){
            $this->_errors[] = self::CONTENU_INVALIDE;
        }
        $this->_contenu = $contenu;
    }

    public function setDateAjout(\DateTime $dateajout){
        $this->_dateAjout = $dateajout;
    }

    public function setDateModif(\DateTime $datemodif){
        $this->_dateModif = $datemodif;
    }

    public function auteur(){
        return $this->_auteur;
    }

    public function titre(){
        return $this->_titre;
    }

    public function contenu(){
        return $this->_contenu;
    }

    public function dateAjout(){
        return $this->_dateAjout;
    }

    public function dateModif(){
        return $this->_dateModif;
    }
}