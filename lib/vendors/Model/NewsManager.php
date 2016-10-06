<?php

/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 03/10/2016
 * Time: 12:36
 */

namespace Model;

use OCFram\Manager;
use \Entity\News;

abstract class NewsManager extends Manager
{

    /**
     * @param int $debut
     * @param int $limite
     * @return News[]
     */
    abstract public function getList($debut = -1, $limite = -1,$name = null);
	
	/** renvoie la news liée à l'id passé en paramètre
	 * @param $id
	 *
	 * @return News
	 */
    abstract public function getNews($id);
	
	/** retourne le nombre de news dans la base
	 * @return int
	 */
    abstract public function count();
	
	/** ajoute une news dans la base
	 * @param News $news
	 */
    abstract protected function add(News $news);
	
	/** update une news de la base
	 * @param News $news
	 */
    abstract protected function modify(News $news);
	
	/** supprime une news de la base
	 * @param $id
	 */
    abstract public function delete($id);
	
	/** Retourne le nombre de news écrite apr l'auteur $name
	 * @param string $name
	 *
	 * @return int
	 */
	abstract public function countNewsForMember($name);
	
	/** détermine la méthode a appeler (update/add) selon la news passée en parametres
	 * @param News $news
	 */
    public function save(News $news)
    {
        if ($news->isValid()) {
            if ($news->isNew()) {
                $this->add($news);
            } else {
                $this->modify($news);
            }
        } else {
            throw new \RuntimeException('La news doit être validée pour être enregistrée');
        }
    }
}