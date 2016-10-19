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
	const NEWS_STATE_VALID = 1;
	const NEWS_STATE_INVALID = 2;
	
	/** retourne une liste de news
	 *
	 * @param int    $debut
	 * @param int    $limite
	 * @param int $id_member
	 *
	 * @return News[]
	 */
    abstract public function getList($debut = -1, $limite = -1,$id_member = null);
	
	/** renvoie la news liée à l'id passé en paramètre
	 *
	 * @param int $id_news
	 *
	 * @return News
	 */
    abstract public function getNews($id_news);
	
	/** retourne le nombre de news dans la base (optionnel : pour un membre)
	 *
	 * @param int $id_member
	 *
	 * @return int
	 */
    abstract public function countNews($id_member = null);
	
	/** ajoute une news dans la base
	 * @param News $News
	 */
    abstract protected function add(News $News);
	
	/** update une news de la base
	 * @param News $news
	 */
    abstract protected function modify(News $news);
	
	/** supprime une news de la base
	 * @param $id_news
	 */
    abstract public function delete($id_news);
	
	
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
	
	/** supprime une news de la base
	 *
	 * @param int $id_news
	 * @return string $result
	 */
    abstract public function getLoginFromNewsId($id_news);
}