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
	protected $news, $auteur, $contenu, $date, $state, $member;
	const AUTEUR_INVALIDE = 1;
	const TEXT_INVALIDE   = 2;
	
	/** retourne vrai si le commentaire est valide
	 *
	 * @return bool
	 */
	public function isValid() {
		return !( ( empty( $this->auteur ) && empty( $this->member ) ) || empty( $this->contenu ) );
	}
	
	/** SETTERS */
	
	public function setNews( $news ) {
		$this->news = (int)$news;
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
	
	public function setState( $state ) {
		$this->state = $state;
	}
	
	public function setMember( $member ) {
		$this->member = $member;
	}
	
	/** GETTERS */
	
	public function news() {
		return $this->news;
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
	
	public function state() {
		return $this->state;
	}
	
	public function member() {
		return $this->member;
	}
}