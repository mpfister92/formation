<?php

namespace Model;

use \Entity\News;

class NewsManagerPDO extends NewsManager {
	/** retourne la liste des news pour un auteur $news
	 *
	 * @param int    $debut
	 * @param int    $limite
	 * @param string $name
	 *
	 * @return News[]
	 */
	public function getList( $debut = -1, $limite = -1, $name = null ) {
		$sql = 'SELECT NNC_id AS id,NNC_auteur AS auteur,NNC_titre AS titre,NNC_contenu AS contenu,NNC_dateAjout AS dateAjout,NNC_dateModif AS dateModif
				FROM t_new_newsc';
		
		if ( $name != null ) {
			$sql .= ' WHERE NNC_auteur = :auteur';
		}
		
		if ( $debut != -1 || $limite != -1 ) {
			$sql .= ' LIMIT ' . $limite . ' OFFSET ' . $debut;
		}
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( ':auteur', $name );
		$requete->execute();
		
		$requete->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News' );
		
		
		$listeNews = $requete->fetchAll();
		
		
		foreach ( $listeNews as $news ) {
			date_default_timezone_set( "Europe/Paris" );
			$news->setDateAjout( new \DateTime( $news->dateAjout() ) );
			$news->setDateModif( new \DateTime( $news->dateModif() ) );
		}
		
		//var_dump($listeNews);
		//die();
		
		$requete->closeCursor();
		
		return $listeNews;
	}
	
	/** renvoie la news liée à l'id passé en paramètre
	 *
	 * @param int $id
	 *
	 * @return News
	 */
	public function getNews( $id ) {
		$sql = 'SELECT NNC_id AS id,NNC_auteur AS auteur,NNC_titre AS titre,NNC_contenu AS contenu,NNC_dateAjout AS dateAjout,NNC_dateModif AS dateModif
                FROM t_new_newsc
                WHERE NNC_id = :id';
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( ':id', (int)$id, \PDO::PARAM_INT );
		$requete->execute();
		
		$requete->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News' );
		
		if ( $news = $requete->fetch() ) {
			date_default_timezone_set( "Europe/Paris" );
			$news->setDateAjout( new \DateTime( $news->dateAjout() ) );
			$news->setDateModif( new \DateTime( $news->dateModif() ) );
			
			return $news;
		}
		
		return null;
	}
	
	/** retourne le nombre de news dans la base
	 *
	 * @param string $name
	 *
	 * @return int
	 */
	public function countNews( $name = null ) {
		$sql = 'SELECT COUNT(*)
                FROM t_new_newsc';
		
		if ( $name != null ) {
			$sql .= ' WHERE NNC_auteur = :auteur';
		}
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( ':auteur', $name );
		$requete->execute();
		$count = $requete->fetchColumn();
		
		return $count;
	}
	
	/** ajoute une news dans la base
	 *
	 * @param News $news
	 */
	protected function add( News $news ) {
		$sql = 'INSERT INTO t_new_newsc SET 
					NNC_auteur = :auteur,
					NNC_titre = :titre,
					NNC_contenu = :contenu, 
					NNC_dateAjout = NOW(),
					NNC_dateModif = NOW() ';
		
		$request = $this->_dao->prepare( $sql );
		$request->bindValue( ':auteur', $news->auteur() );
		$request->bindValue( ':titre', $news->titre() );
		$request->bindValue( ':contenu', $news->contenu() );
		
		$request->execute();
	}
	
	/** update une news de la base
	 *
	 * @param News $news
	 */
	protected function modify( News $news ) {
		$sql = 'UPDATE t_new_newsc SET 
					NNC_auteur = :auteur,
					NNC_titre = :titre,
					NNC_contenu = :contenu,
					NNC_dateModif = NOW()
					WHERE NNC_id = :id';
		
		$request = $this->_dao->prepare( $sql );
		$request->bindValue( ':auteur', $news->auteur() );
		$request->bindValue( ':titre', $news->titre() );
		$request->bindValue( ':contenu', $news->contenu() );
		$request->bindValue( ':id', $news->id(), \PDO::PARAM_INT );
		
		$request->execute();
	}
	
	/** supprime une news de la base
	 *
	 * @param int $id
	 */
	public function delete( $id ) {
		$sql = 'DELETE FROM t_new_newsc
					WHERE NNC_id = ' . (int)$id;
		
		$request = $this->_dao->exec( $sql );
	}
	
	/** retourne le login de la personne qui a écrit la news
	 * @param int $id
	 * @return string $result
	 */
	public function getLoginFromNewsId($id){
		$sql = 'SELECT NNC_auteur
				FROM t_new_newsc
				WHERE NNC_id = :id';
		
		$requete = $this->_dao->prepare($sql);
		$requete->bindValue('id',$id, \PDO::PARAM_INT);
		
		$requete->execute();
		
		return ($result = $requete->fetchColumn());
	}
}