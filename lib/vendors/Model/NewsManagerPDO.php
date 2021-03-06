<?php

namespace Model;

use \Entity\News;

class NewsManagerPDO extends NewsManager {
	/** retourne une liste de news
	 *
	 * @param int    $debut
	 * @param int    $limite
	 * @param int $id_member
	 *
	 * @return News[]
	 */
	public function getList( $debut = -1, $limite = -1, $id_member = null ) {
		
		$sql = 'SELECT NNC_id AS id,NNC_fk_NMC AS fk_NMC,NNC_titre AS titre,NNC_contenu AS contenu,NNC_dateAjout AS dateAjout,NNC_dateModif AS dateModif, NNC_fk_NNE as fk_NNE
				FROM t_new_newsc
				WHERE NNC_fk_NNE = :state';
		
		if ( $id_member != null ) {
			$sql .= ' AND NNC_fk_NMC = :id';
		}
		
		if ( $debut != -1 || $limite != -1 ) {
			$sql .= ' LIMIT ' . $limite . ' OFFSET ' . $debut;
		}
		
		$requete = $this->_dao->prepare( $sql );
		if ( null !== $id_member ) {
			$requete->bindValue( ':id', $id_member );
		}
		$requete->bindValue( ':state', parent::NEWS_STATE_VALID, \PDO::PARAM_INT );
		$requete->execute();
		
		$requete->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News' );
		
		
		$listeNews = $requete->fetchAll();
		
		
		foreach ( $listeNews as $news ) {
			$news->setDateAjout( new \DateTime( $news->dateAjout() ) );
			$news->setDateModif( new \DateTime( $news->dateModif() ) );
		}
		
		$requete->closeCursor();
		
		return $listeNews;
	}
	
	/** renvoie la news liée à l'id passé en paramètre
	 *
	 * @param int $id_news
	 *
	 * @return News
	 */
	public function getNews( $id_news ) {
		$sql = 'SELECT NNC_id AS id,NNC_fk_NMC AS fk_NMC,NNC_titre AS titre,NNC_contenu AS contenu,NNC_dateAjout AS dateAjout,NNC_dateModif AS dateModif
                FROM t_new_newsc
                WHERE NNC_id = :id';
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( ':id', (int) $id_news, \PDO::PARAM_INT );
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
	
	/** retourne le nombre de news dans la base (optionnel : pour un membre)
	 *
	 * @param int $id_member
	 *
	 * @return int
	 */
	public function countNews( $id_member = null ) {
		$sql = 'SELECT COUNT(*)
                FROM t_new_newsc
                WHERE NNC_fk_NNE = :state';
		
		if ( isset($id_member ) ) {
			$sql .= ' AND NNC_fk_NMC = :id';
		}
		
		$requete = $this->_dao->prepare( $sql );
		if(null !== $id_member) {
			$requete->bindValue( ':id', $id_member );
		}
		$requete->bindValue( ':state', parent::NEWS_STATE_VALID);
		$requete->execute();
		$count = $requete->fetchColumn();
		
		return $count;
	}
	
	/** ajoute une news dans la base
	 *
	 * @param News $News
	 */
	protected function add( News $News ) {
		$sql = 'INSERT INTO t_new_newsc SET 
					NNC_fk_NMC = :id,
					NNC_titre = :titre,
					NNC_contenu = :contenu, 
					NNC_dateAjout = NOW(),
					NNC_dateModif = NOW(),
					NNC_fk_NNE = :state';
		
		$request = $this->_dao->prepare( $sql );
		$request->bindValue( ':id', $News->fk_NMC() );
		$request->bindValue( ':titre', $News->titre() );
		$request->bindValue( ':contenu', $News->contenu() );
		$request->bindValue( ':state', parent::NEWS_STATE_VALID );
		
		$request->execute();
	}
	
	/** update une news de la base
	 *
	 * @param News $News
	 */
	protected function modify( News $News ) {
		$sql = 'UPDATE t_new_newsc SET 
					NNC_fk_NMC = :auteur,
					NNC_titre = :titre,
					NNC_contenu = :contenu,
					NNC_dateModif = NOW()
					WHERE NNC_id = :id';
		
		$request = $this->_dao->prepare( $sql );
		$request->bindValue( ':auteur', $News->fk_NMC() );
		$request->bindValue( ':titre', $News->titre() );
		$request->bindValue( ':contenu', $News->contenu() );
		$request->bindValue( ':id', $News->id(), \PDO::PARAM_INT );
		
		$request->execute();
	}
	
	/** supprime une news de la base
	 *
	 * @param int $id_news
	 */
	public function delete( $id_news ) {
		$sql = 'UPDATE t_new_newsc SET
					NNC_fk_NNE = :state
				WHERE NNC_id = :id';
		
		$requete = $this->_dao->prepare($sql);
		$requete->bindValue(':state',parent::NEWS_STATE_INVALID);
		$requete->bindValue(':id',$id_news,\PDO::PARAM_INT);
		
		$requete->execute();
	}
	
	/** retourne le login de la personne qui a écrit la news
	 *
	 * @param int $id_news
	 *
	 * @return string $result
	 */
	public function getLoginFromNewsId( $id_news ) {
		$sql = 'SELECT NMC_login
				FROM t_new_newsc
				INNER JOIN t_new_memberc ON NMC_id = NNC_fk_NMC
				WHERE NNC_id = :id';
		
		$requete = $this->_dao->prepare( $sql );
		$requete->bindValue( 'id', $id_news, \PDO::PARAM_INT );
		
		$requete->execute();
		
		return ( $result = $requete->fetchColumn() );
	}
	
	
}