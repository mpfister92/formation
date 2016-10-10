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
	protected $auteur, $titre, $contenu, $dateAjout, $dateModif, $state;
	const AUTEUR_INVALIDE  = 1;
	const TITRE_INVALIDE   = 2;
	const CONTENU_INVALIDE = 3;
	
	/** renvoie vrai si la news est valide
	 *
	 * @return bool
	 */
	public function isValid() {
		return !( empty( $this->auteur ) || empty( $this->titre ) || empty( $this->contenu ) );
	}
	
	/** SETTERS */
	
	public function setAuteur( $auteur ) {
		if ( empty( $auteur ) ) {
			$this->errors[] = self::AUTEUR_INVALIDE;
		}
		$this->auteur = $auteur;
	}
	
	public function setTitre( $titre ) {
		if ( !is_string( $titre ) || empty( $titre ) ) {
			$this->errors[] = self::TITRE_INVALIDE;
		}
		$this->titre = $titre;
	}
	
	public function setContenu( $contenu ) {
		if ( !is_string( $contenu ) || empty( $contenu ) ) {
			$this->errors[] = self::CONTENU_INVALIDE;
		}
		$this->contenu = $contenu;
	}
	
	public function setDateAjout( \DateTime $dateajout ) {
		$this->dateAjout = $dateajout;
	}
	
	public function setDateModif( \DateTime $datemodif ) {
		$this->dateModif = $datemodif;
	}
	
	public function setState( $state ) {
		$this->state = $state;
	}
	
	/** GETTERS */
	
	public function auteur() {
		return $this->auteur;
	}
	
	public function titre() {
		return $this->titre;
	}
	
	public function contenu() {
		return $this->contenu;
	}
	
	public function dateAjout() {
		return $this->dateAjout;
	}
	
	public function dateModif() {
		return $this->dateModif;
	}
	
	public function state() {
		return $this->state;
	}
}