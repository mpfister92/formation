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
	protected $id,$fk_NNC, $auteur, $contenu, $date, $fk_NCE, $fk_NMC, $dateModif;
	const AUTEUR_INVALIDE = 1;
	const TEXT_INVALIDE   = 2;
	
	/** retourne vrai si le commentaire est valide
	 *
	 * @return bool
	 */
	public function isValid() {
		return !(empty( $this->contenu ));
	}
	
	/** SETTERS */
	
	public function setDateModif($dateModif){
		$this->dateModif = $dateModif;
	}
	
	public function setFk_NNC( $fk_NNC ) {
		$this->fk_NNC = (int) $fk_NNC;
	}
	
	public function setAuteur( $auteur ) {
		if ( !is_string( $auteur ) || empty( $auteur ) ) {
			$this->errors[] = self::AUTEUR_INVALIDE;
		}
		$this->auteur = $auteur;
	}
	
	public function setContenu( $contenu ) {
		if ( !is_string( $contenu ) || empty( $contenu ) ) {
			$this->errors[] = self::TEXT_INVALIDE;
		}
		$this->contenu = $contenu;
	}
	
	public function setDate( \DateTime $date ) {
		$this->date = $date;
	}
	
	public function setFk_NCE( $fk_NCE ) {
		$this->fk_NNE = $fk_NCE;
	}
	
	public function setFk_NMC( $fk_NMC ) {
		$this->fk_NMC = $fk_NMC;
	}
	
	/** GETTERS */
	
	public function fk_NNC() {
		return $this->fk_NNC;
	}
	
	public function auteur() {
		return $this->auteur;
	}
	
	public function contenu() {
		return $this->contenu;
	}
	
	public function date() {
		return $this->date;
	}
	
	public function fk_NCE() {
		return $this->fk_NCE;
	}
	
	public function fk_NMC() {
		return $this->fk_NMC;
	}
	
	public function dateModif(){
		$this->dateModif;
	}
}