<?php

namespace Model;

use \Entity\News;

class NewsManagerPDO extends NewsManager {

    /** retourne la liste des news pour un auteur $news
     * @param int $debut
     * @param int $limite
	 * @param string $name
     * @return News[]
     */
    public function getList($debut = -1,$limite = -1,$name = null){
        $sql = 'SELECT id,auteur,titre,contenu,dateAjout,dateModif 
				FROM news';

        if($name != null){
        	$sql .= ' WHERE auteur = \'Morgan\'';
		}
        
        if($debut != -1 || $limite != -1){
            $sql .=' LIMIT '.$limite.' OFFSET '.$debut;
        }
		
        //var_dump($sql);
        //die();
        
        $requete = $this->_dao->prepare($sql);
        $requete->bindValue(':auteur',$name);
        $requete->execute();
        
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
	
	/** renvoie la news liée à l'id passé en paramètre
	 * @param $id
	 *
	 * @return News
	 */
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
	
	/** retourne le nombre de news dans la base
	 * @return int
	 */
    public function count(){
        $sql = 'SELECT COUNT(*)
                FROM news';

        $requete = $this->_dao->query($sql);

        /** @var int $count */
        $count = $requete->fetchColumn();
        return $count;
    }
	
	/** ajoute une news dans la base
	 * @param News $news
	 */
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
	
	/** update une news de la base
	 * @param News $news
	 */
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
	
	/** supprime une news de la base
	 * @param $id
	 */
    public function delete($id){
        $sql = 'DELETE FROM news
                WHERE id = '.(int) $id;

        $request = $this->_dao->exec($sql);
    }
	
	
	/** Retourne le nombre de news écrite apr l'auteur $name
	 * @param string $name
	 *
	 * @return int
	 */
	public function countNewsForMember($name){
		$sql = 'SELECT COUNT(*)
				FROM news
				WHERE auteur = :auteur';
		
		$requete = $this->_dao->prepare($sql);
		
		$requete->bindValue(':auteur',$name);
		
		$requete->execute();
		
		$count = $requete->fetchColumn();
		
		return $count;
	}
}